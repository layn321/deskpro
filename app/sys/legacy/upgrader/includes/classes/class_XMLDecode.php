<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: class_XMLDecode.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - mailer
// +-------------------------------------------------------------+

// function just returns the XML if we don't need the object.
function getXML($file) {

	$xml_parser = new class_XMLDecode();
	return $xml_parser->parse_file($file);

}

function getXMLData($data, $utf=false) {

	if ($utf) {
		$xml_parser = new class_XMLDecode('UTF-8');
	} else {
		$xml_parser = new class_XMLDecode();
	}

	return $xml_parser->parse_xml($data);
}

class class_XMLDecode {

	// container for the raw XML to pass
	var $xml;

	// the parser object
	var $parser;

	// data we return
	var $output;

	// temporary container for parsed xml
	var $tmp = array();

	// count tags
	var $tag_id = 0;

  	/**
	* constructor, creates XML parser
	*
	* @access	public
	*/
	function class_XMLDecode($encoding = 'ISO-8859-1') {

		$this->parser = xml_parser_create($encoding);

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_element_handler($this->parser, "tag_open", "tag_close");
		xml_set_character_data_handler($this->parser, "cdata");

	}

	/**
	* Parse the XML file and return the array of the following features
	* 	- the outermost tag is ignored e.g. <language></language>
	*	- data is stored as value
	*	- automatically creates an array if e.g. <word></word><word></word> otherwise uses single element
	*	- attributes and data are stored at same level
	*
	* @param	resource	XML Parser
	* @param	string		The name of opened tag
	* @param	array		The tag's attributes
	*/
	function parse() {

		if (!xml_parse($this->parser, $this->xml)) {
			printf("$this->filename :: XML error: %s at line %d", xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));

			$lines = file(ROOT . '/' . $this->filename);

			echo xml_get_current_line_number($this->parser);

			echo $lines[xml_get_current_line_number($this->parser)];

		}

		xml_parser_free($this->parser);

		// See note in tag_close()
		if (!$this->output) {
			global $_xmldecode;
			$this->output = $_xmldecode;
		}

		return $this->output;

	}

	/**
	* Parse XML based on passed XML data
	*
	* @param	string		XML
	*
	* @return		array		The parsed XML array
	*/
	function parse_xml($xml) {

		// get xml fro file
		$this->xml = $xml;

		// parse file
		$this->parse();

		// See note in tag_close()
		if (!$this->output) {
			global $_xmldecode;
			$this->output = $_xmldecode;
		}

		// return output
		return $this->output;

	}

	/**
	* Parse XML based on filename for XML data
	*
	* @param	string		filename from ROOT
	*
	* @return		array		The parsed XML array
	*/
	function parse_file($filename) {

		$this->filename = $filename;

		// get xml from file
		$this->xml = file_get_contents(ROOT . "/$filename");

		// parse file
		$this->parse();

		// See note in tag_close()
		if (!$this->output) {
			global $_xmldecode;
			$this->output = $_xmldecode;
		}

		// return output
		return $this->output;

	}

	/**
	* XML callback : Tag opened.
	*
	* @param	resource	XML Parser
	*
	* @param	string		The name of opened tag
	*
	* @param	array		The tag's attributes
	*/
	function tag_open($parser, $tag, $attributes) {

		$this->cdata = '';
		array_unshift($this->tmp, array('tag' => $tag, 'attributes' => $attributes, 'tag_id' => ++$this->tag_id));

	}

	function cdata($parser, $cdata) {
		$this->cdata .= $cdata;
	}

	/**
	* XML callback : Tag closed.
	*
	* @param	resource	XML Parser
	*
	* @param	string		The name of closed tag
	*/
	function tag_close($parser, $tag) {

		// get the row we are closing
		$row = array_shift($this->tmp);

		// get data
		$attributes = $row['attributes'];

		// if this is a childless tag (has cdata) or we are closing the outermost tag
		// add data to the attributes array to get all in same level
		if (trim($this->cdata) != '' OR $row['tag_id'] == $this->tag_id) {
			$this->add_node($attributes, 'value', $this->xml_uncontent_cdata_string($this->cdata));
		}

		// add back into main array if there is an element left
		if (isset($this->tmp[0])) {
			$this->add_node($this->tmp[0]['attributes'], $tag, $attributes);

		} else {

			// last element removed, we are finished.
			$this->output = $attributes;

			// See ticket 64584 or issue 39
			// - For some reason, the above output variable is lost sometimes. I have no idea
			// why. I suspect a PHP bug.
			// - This is a workaround. Assigning the value to a global var seems to make it work
			global $_xmldecode;
			$_xmldecode = $attributes;
		}

		// unset data
		$this->cdata = '';
	}

	/**
	* Adds tag to array
	*
	* @param	array	Reference to array tag has to be added to
	*
	* @param	string	Tag name
	*
	* @param	string	Data to be added
	*/
	function add_node(&$array, $tag, $data) {

		// not an array or the tag istn't in the array yet - add tag to array
		if (!is_array($array) OR !in_array($tag, array_keys($array))){
			$array[$tag] = $data;

		// the tag is already in the array and the tag has been made into an array. just append this value
		} elseif (is_array($array[$tag]) AND isset($array[$tag][0])) {
			$array[$tag][] = $data;

		// the tag is already in the array but the tag has not been made into an array. Recreate the tag as an array and apend.
		} else {
			$array[$tag] = array($array[$tag]);
			$array[$tag][] = $data;
		}
	}

	/**
	 * Undoes the arbitrary cdata string replacement done when encoding
	 *
	 * @param string The data to work on
	 * @return The 'safe' text
	 */
	function xml_uncontent_cdata_string($data) {

		$fr = array(
			'<![CDATA[' => '<:::###DESKRPO_CDATA_START###:::>',
			']]>' => '<:::###DESKRPO_CDATA_END###:::>',
		);


		return str_replace(array_values($fr), array_keys($fr), $data);
	}
}

?>
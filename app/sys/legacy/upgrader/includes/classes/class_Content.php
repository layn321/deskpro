<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* html generator
*
* @package DeskPRO
*/


/**
* cell
*
* Class is used for column of table row
*
* @package DeskPRO
* @version $Id: class_Content.php 6701 2010-03-11 05:39:45Z chroder $
*/
class cell {

	/**
	* data to be displayed in cell
	* @var	string
	* @access private
	*/
	var $data;

	/**
	* cell data formatting style
	* @var	string
	* @access private
	*/
	var $style;

	/**
	* Constructor of class. It accepts two parameters
	*
	* @access	Public
	*
	* @param	string	data to be displayed in cell
	* @param	string	optional cell data formatting style
	*/
	function cell($data, $style='') {

		$this->setData($data);
		$this->setStyle($style);
	}

	/**
	* set formatting style of cell data
	*
	* @access	Public
	*
	* @param	string	cell data formatting style
	*/
	function setStyle($style) {
		$this->style = $style;
	}

	/**
	* set data to be displayed in cell
	*
	* @access	Public
	*
	* @param	string	data to be displayed in cell
	*/
	function setData($data) {
		$this->data = $data;
	}

}

/**
* row
*
* Class is used for table row
*
* @package DeskPRO
* @version $Id: class_Content.php 6701 2010-03-11 05:39:45Z chroder $
*/
class row {

	/**
	* array of array cells
	* @var cell
	* @access private
	*/
	var $data;

	/**
	* name of row
	* @var string
	* @access private
	*/
	var $name;

	/**
	* any row styles
	* @var array
	* @access private
	*/
	var $style;
	
	/**
	 * A string of CSS classnames
	 */
	var $classname = '';

	/**
	* this is a key
	* @var string
	* @access private
	*/
	var $helpkey;

	/**
	* help text
	* @var	string
	* @access private
	*/
	var $helptext;

	var $tbody = false;
	var $tbody_attr = '';

	/**
	* Constructor of class. It accepts two parameters
	*
	* @access	Public
	*
	* @param	string|array	optional array of array of cells. Each array contains data and style details
	* @param	string	optional cell data formatting style
	*/
	function row($data='', $name='', $helpkey='', $helptext='') {

		$this->name = $name;
		$this->helpkey = $helpkey;
		$this->helptext = $helptext;

		if (is_array($data)) {
			foreach ($data AS $key => $var) {

				// if we have style data
				if (is_array($var)) {

					$this->data[] = new cell($var[0], $var[1]);

				} else {

					// no style data
					$this->data[] = new cell($var);
				}
			}
		}
	}

	/**
	* set help key
	*
	* @access	Public
	*
	* @param	string	 help key
	*/
	function setHelpkey($key) {
		$this->helpkey = $key;
	}

	/**
	* Adds cells to the row
	*
	* @access	Public
	*
	* @param	string|array	array of array of cells. Each array contains data and style details
	*/
	function addCells($data) {

		foreach ($data AS $key => $var) {
			$this->addCell($data);
		}
	}

	/**
	* Adds cell to the cell array
	*
	* @access	Public
	*
	* @param	string	data to be displayed in cell
	* @param	string	optional cell data formatting style
	* @param	string	optional id of cell
	*
	* @return	cell	cell class object reference
	*/
	function &addCell($value, $style='', $columnid='') {

		$cell = new cell($value, $style);
		if ($columnid) {
			$this->data[$columnid] = $cell;
		} else {
			$this->data[] = $cell;
		}
		return $cell;

	}
}

/**
* content
*
* generates actual html for page contents
* Features of class content (when combined with row and cell)
* a) Set title
* b) Set columns
* c) Help system for title
* d) Help system for rows (generally form input rows)
* e) Can create form
* f) Can create 2nd row of data
* g) Can create checkbox select
* h) Styling on a row level
* i) Styling on a column level
* j) Styling on a cell level
* g) Can fit in as part of a section of tabs or on its own
*
* @package DeskPRO
* @version $Id: class_Content.php 6701 2010-03-11 05:39:45Z chroder $
*/
class content {

	/**
	* maximum size of attachment
	* @var int
	* @access private
	*/
	var $max_attach_size;

	/**
	* the title
	* @var string
	* @access private
	*/
	var $title;

	/**
	* name of the content
	* @var string
	* @access private
	*/
	var $name;

	/**
	* columns for top row
	* @var int
	* @access private
	*/
	var $columns;

	/**
	* rows of table
	* @var row array
	* @access private
	*/
	var $rows;

	/**
	* columnstyles
	* @var string
	* @access private
	*/
	var $columnstyle;

	/**
	* secondline, opened with checkbox
	* @var array
	* @access private
	*/
	var $secondline;

	/**
	 * Separator rows
	 * @var array
	 * @access private
	 */
	var $separator;

	/**
	* flag whether second lines are showing or not
	* @var bool
	* @access private
	*/
	var $usingsecondline;

	/**
	* an open 2nd line
	* @var string array
	* @access private
	*/
	var $openline;

	/**
	* creates a checkbox and a select all checkbox
	* @var string array
	* @access private
	*/
	var $checkbox;

	var $checkbox_selected;

	/**
	* actions for checkboxes in left column
	* @var array
	* @access private
	*/
	var $checkbox_actions;

	/**
	* contains row tips
	* @var string array
	* @access private
	*/
	var $rowtips;

	/**
	* flag to set visibility of rowtips
	* @var boolean
	* @access private
	*/
	var $showrowtips;

	/**
	* do we have a titlehelp link
	* @var boolean
	* @access private
	*/
	var $titlehelp;

	/**
	* id for <tbody>
	* @var string
	* @access private
	*/
	var $tablebodyname;

	/**
	* id for <table>
	* @var string
	* @access private
	*/
	var $tablename;

	/**
	* form action file
	* @var string
	* @access private
	*/
	var $form_filename;

	/**
	* form action flag
	* @var string
	* @access private
	*/
	var $form_do;

	/**
	* hidden field used in form
	* @var string
	* @access private
	*/
	var $form_hidden;

	/**
	* name of form
	* @var string
	* @access private
	*/
	var $form_name = 'dpform';

	/**
	* submit button html
	* @var string
	* @access private
	*/
	var $form_end_text;

	/**
	* extra fields on form
	* @var array
	* @access private
	*/
	var $form_extra;

	/**
	* first row contents
	* @var string
	* @access private
	*/
	var $top_content;

	/**
	* dynamic options
	* @var string array
	* @access private
	*/
	var $dynamics;

	/**
	* pagenav
	* @var class_PageNav
	* @access private
	*/
	var $pagenav;

	/**
	*	Displays results of pagination
	*
	* @access	Public
	*
	* @param	int	Number of records
	* @param	string	name of form
	* @param	string	name of navigation element
	*/
	function pagenav($total, $word ='', $form = 'dpform', $field = 'navpage') {

		require_once(INC . 'classes/class_PageNav.php');

		$this->pagenav = new class_PageNav($total, $form, $field);

		if ($word) {
			$this->pagenav->setWord($word);
		}

	}

	/**
	* sets top content data
	*
	* @access	Public
	*
	* @param	array	data
	*/
	function setTopContent($data, $align='right') {
		$this->top_content = $data;
		$this->top_content_align = $align;
	}

	/**
	* return reference to a row
	*
	* @access	Public
	*
	* @param	string	row id
	*
	* @return	row	reference to row object
	*/
	function &getRow($id) {
		return $this->rows[$id];
	}

	/**
	* Sets maximum allowed size for file upload
	*
	* @access	Public
	*
	* @param	int	optional allowed size of upload
	*/
	function setMaxFilesize($size='') {
		$this->max_attach_size = get_max_filesize($size);
	}

	/**
	* class constructor
	*
	* @access	Public
	*
	* @param	string	name of the object
	* @param	string	optional title of the table
	* @param	bool	optional flag to show / hide help icon for table title
	* @param	bool	optional flag to show / hide tips about table
	*/
	function content($name, $title='', $titlehelp=FALSE, $showrowtips=FALSE) {

		$this->name = $name;
		$this->titlehelp = $titlehelp;
		$this->showrowtips = $showrowtips;

		if ($title) {
			$this->setTitle($title);
		}
	}

	/**
	* Adds form tag
	*
	* @access	Public
	*
	* @param	string	form action file name
	* @param	string	action flag
	* @param	string	optional Text to be displayed on Submit button. If value is set then only submit button will be displayed.
	* @param	string	optional hidden fields to be added to form
	* @param	string	optional name of form
	* @param	string	optional
	*/
	function setForm($filename, $do, $submit='', $hidden='', $name='dpform', $extra='', $js='') {

		give_default($name, 'dpform');

		$this->form_filename = $filename;
		$this->form_do = $do;
		$this->form_end_text = $submit;
		$this->form_hidden = $hidden;
		$this->form_name = $name;
		$this->form_extra = $extra;
		$this->form_end_js = $js;

	}

	/**
	* Adds token hidden field
	*
	* @access	Public
	*
	* @param	string
	*/
	function setToken($type) {

		$token = set_tech_token($type);
		$this->form_hidden['token'] = $token;

	}

	/**
	* Adds form start tag
	*
	* @access	private
	*
	* @return	string
	*/
	function buildFormStart() {
		if ($this->form_filename) {
			return new_form($this->form_filename, $this->form_do, $this->form_hidden, $this->form_name, $this->form_extra);
		}
	}

	/**
	* Adds form close tag
	*
	* @access	private
	*
	* @return	string
	*/
	function buildFormEnd() {
		if ($this->form_filename) {
		    if ($this->form_end_text AND $this->form_end_text != 'NONE') {
			    return end_form($this->form_end_text, $this->form_end_js);
		    } else {
		    	return end_form('', $this->form_end_js);
		    }
		    
		}
	}

	/**
	* Checks any rows are added are not
	*
	* @access	Public
	*
	* @return	bool	true if any row is added, false if not added
	*/
	function isContent() {

		if (is_array($this->rows)) {
			return true;
		} else {
			return false;
		}

	}

	/**
	* Adds column style
	*
	* @access	Public
	*
	* @param	string	cell data formatting style, style defination for each column in a row
	*/
	function columnStyle() {

		$this->columnstyle = func_get_args();

	}

	/**
	* Add new row to table
	* no way to add help
	*
	* @access	Public
	*
	* @return	string|array	array of array of cell parameters
	*/
	function &newRow() {

		$values = func_get_args();
		// handle it if we are sending an array
		if (count($values) == 1 AND is_array($values[0])) {

			return $this->addRow('', $values[0]);

		} else {
			// sending an array of properties
			return $this->addRow('', $values);
		}
	}

	function newRows($rows) {
		foreach ($rows AS $result) {
			$this->newRow($result);
		}
	}

	function newRowColumns($data, $columns) {

		// lower columns if we don't have enough data
		$i = count($data);
		$columns = iff($i < $columns, $i, $columns);

		if (is_array($data)) {
			foreach ($data AS $key => $var) {

				$count++;
				$row[] = $var;

				if ($count == $columns) {
					$this->newRow($row);
					unset($row);
					unset($count);
				}
			}
		}

		// extra row
		if (is_array($row)) {
			$extra = $columns - count($row);
			if ($extra) {
				while ($extra > 0) {
					$row[] = '';
					$extra = $extra - 1;
				}
			}
			$this->newRow($row);
		}
	}

	/**
	 * Add a separator to the table to separate logical chunks of the
	 * same table.
	 *
	 * @param string $text Content, if any, to place into the separator
	 * @return unknown
	 */
	function &addSeparator($text = '') {

		$row = new row(array($text));
		$this->rows[] = $row;

		$key = count($this->rows) - 1;
		$this->separator[$key] = true;

		return $row;
	}

	/**
	* Add new row to table
	*
	* @access	Private
	*
	* @param	string	optional name of row
	* @param	array	optional array of array of cell parameters
	* @param	string	optional help key for row
	* @param	string	optional help text for row
	*
	* @return	row	row object reference
	*/
	function &addRow($name='', $values='', $helpkey='', $helptext='') {

		$row = new Row($values, $name, $helpkey, $helptext);
		$this->rows[] = $row;

		return $row;

	}

	/**
	* Add multiple rows
	*
	* @access	public
	*
	* @param	string|array	array of array of row parameters
	*/
	function addRows($row) {

		if (is_array($row)) {
			foreach ($row AS $key => $array) {
				$this->addRow('', $array);
			}
		}
	}

	/**
	* creates 2 column form field row
	*
	* @access	public
	*
	* @param	string	text to be displayed in left cell
	* @param	string	name of row
	* @param	string	data (text / form input control) to be displayed in right cell
	* @param	string	optional help text for the row
	*/
	function buildRow($text, $name, $content, $helptext='') {
		return $this->addRow($name, array($text, $content), $name, $helptext);
	}

	/**
	* creates 2 column form field row and add title and form field to row cells
	*
	* @access	private
	*
	* @param	string	type of the form field
	* @param	string	text to be displayed in left cell
	* @param	string	optional name of row
	* @param	string	multiple parameters depending on field type and form function used
	*/
	function buildformelement($type, $text, $name='') {

		$args = func_get_args();
		unset($args['0'], $args['1']);
		$row = new Row('', $name, $name);
		$row->addCell($text);
		$row->addCell(call_user_func_array($type, $args));
		$this->rows[] = $row;

		return count($this->rows) - 1;

	}

	/**
	* adds a check box in row with check box in left cell and label in right cell
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in right cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_checkbox function
	*/
	function buildCheckboxReverse() {
		$args = func_get_args();

		$text = $args['0'];
		unset($args['0']);
		$args = array_values($args);

		$row = new Row('', $args['0'], $args['0']);
		$row->addCell(call_user_func_array('form_checkbox', $args));
		$row->addCell("<label for=\"$args[0]\">$text</label>");
		$this->rows[] = $row;

		return count($this->rows) - 1;

	}

	/**
	* adds a radio button in row with radio button in left cell and label in right cell
	* @access public
	* @param	string	first argument text to be displayed in right cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_radio function
	*/
	function buildRadioReverse() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_radio');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display file browse field in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_file function
	*/
	function buildFile() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_file');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display text box field in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_input function
	*/
	function &buildInput() {

		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_input');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);

	}

	/**
	* display password box field in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_password function
	*/
	function buildPassword() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_password');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display drop down box in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_select function
	*/
	function buildSelect() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_select');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display drop down box in row with order drop down box having values Asc and Desc
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_order function
	*/
	function buildOrderSelect() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_order');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display textarea in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_textarea function
	*/
	function buildTextarea() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_textarea');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display Yes and No radio button in row
	*
	* @access	public
	*
	* @param	string	first argument text to be displayed in left cell
	* @param	string	second argument name of row
	* @param	string	parameters required to pass to form_radio_yn function
	*/
	function buildRadioYN() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_radio_yn');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display check box in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_checkbox function
	*/
	function buildCheckbox() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_checkbox');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display WYSIWYG editor in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_wysiwyg_simple function
	*/
	function buildWYSIWYG() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_wysiwyg_simple');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}


	/**
	* display WYSIWYG editor in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_wysiwyg_simple function
	*/
	function buildAdvancedWYSIWYG() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_wysiwyg');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}


	/**
	* display radio button in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_radio function
	*/
	function buildRadio() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_radio');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display date entry field in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_date function
	*/
	function buildDate() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_date');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}


	function buildTime() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_time');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display multiple date selection field in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_date_multi function
	*/
	function buildMultiDate() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_date_multi');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	function buildMultiTime() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_multi_time');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* display color selection box in row
	*
	* @access	public
	*
	* @param	string	$text	first argument text to be displayed in left cell
	* @param	string	$name	second argument name of row
	* @param	string	$field_properties	parameters required to pass to form_color function
	*/
	function buildColor() {
		$args = func_get_args();
		array_unshift_assoc($args, '-1', 'form_color');
		return call_user_func_array(array(&$this, 'buildformelement'), $args);
	}

	/**
	* Set title of the table
	*
	* @access	public
	*
	* @param	string	$title	title of the table
	*/
	function setTitle($title) {
		$this->title = $title;
	}

	/**
	* Add checkbox column for each row
	*
	* @access	public
	*
	* @param	string	$checkbox	checkbox code
	*/

	function addCheckbox($checkbox, $selected = false) {

		if ($selected) {
			$count = count($this->checkbox);
			$this->checkbox_selected[$count] = $selected;
		}
		$this->checkbox[] = $checkbox;
	}

	/**
	* Add second line for each row and shows tree node sign before it
	*
	* @access	public
	*
	* @param	string	$secondline	content to be shown on second line
	*/
	function addSecondline($secondline) {
		$this->secondline[] = $secondline;
		if ($secondline != '') {
			$this->usingsecondline = TRUE;
		}
	}

	function addOpenline($openline) {
		$this->openline[] = $openline;
	}

	/**
	* add Table header columns
	*
	* @access	public
	*
	* @param	string	$text	unlimited column titles
	*/
	function addColumn() {
		$args = func_get_args();
		foreach ($args AS $key => $var) {
			if (is_array($var)) {
				foreach ($var AS $var2) {
					if (!$var2) {
						$var2 = '&nbsp;';
					}
					$this->columns[] = $var2;
				}
			} else {
				if (!$var) {
					$var = '&nbsp;';
				}
				$this->columns[] = $var;
			}
		}
	}

	/**
	* add Table header column at a particular order
	*
	* @access	public
	*
	* @param	string	$column	column title text
	* @param	int	$order	order of column
	*/
	function addColumnOrder($column, $order) {
		$this->columns[$order] = $column;
	}

	/**
	* adds a row on top and d
	*
	* @access	public
	*
	* @param	string	$column	column title text
	* @param	int	$order	order of column
	*/
	function build_toprow() {

		if (!$this->top_content) {
			return;
		}

		if (is_array($this->rows[0]->data)) {
			$count = count($this->rows[0]->data);
		} else {
			$count = count($this->columns);
		}
		if ($this->usingsecondline) {
			$count++;
		}
		if ($this->showrowtips) {
			$count++;
		}
		if (is_array($this->checkbox)) {
			$count++;
		}

		return "<tr><td colspan=\"$count\" class=\"matrix_left\" align=\"$this->top_content_align\">" . $this->top_content . "</td></tr>";

	}

	/**
	* adds title of the table
	*
	* @access	public
	*/
	function build_title() {

		global $scriptpath;

		if (is_object($this->pagenav) AND $this->pagenav->word) {
			$this->title = $this->pagenav->title() . $this->title;
		}

		preg_match("#(.*)(\/(admin|tech)\/.*php)#", $scriptpath, $matches);
		$url = $matches[2];

		$html = '<div class="content_head"><div class="p1"><div class="p2">';

		if ($this->titlehelp) {
			$html .= '<div style="float:right">';

			if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
				$html .= "<a target=\"_blank\" href=\"./../" . iff(defined('ADMINZONE'), "admincp/", "../admincp/") . "dev_inline.php?section=" . $this->name . "\">???</a>&nbsp;&nbsp;&nbsp;";
			}

			$html .= html_image('tech/content_head_help.gif', '', "onclick=\"openWindow('" . WEB . 'tech/help/' . "pophelp.php?section=" . $this->name . "',500,350);\"");

			$html .= '</div>';
		}

		$html .= $this->title;
		$html .= '</div></div></div>';

		return $html;

	}

	/**
	* generate table header columns
	*
	* @access	private
	*/
	function build_columns() {

		$html = "";

		// add the columns
		if (is_array($this->columns)) {

			// sort columns
			ksort($this->columns);

			$html .= "<tr>";

			// add checkbox
			if (is_array($this->checkbox)) {
				if ($this->rows) {
					$html .= "<td width=\"10px\"><input type=\"checkbox\" name=\"allbox\" onclick=\"checkall(this.form, /^doaction/);\" /></td>";
				} else {
					$html .= "<td width=\"10px\"></td>";
				}
			}

			// add 2nd lince
			if ($this->usingsecondline) {
				// rand is so we can more than one of these per page
				if ($this->rows) {
					$html .= "<td width=\"10px\"><img style=\"display: none\" align=\"center\" id=\"mass_vis_" . $this->name . "\" src=\"" . constant('LOC_IMAGES') . "tech/bul101.gif\" onclick=\"mass_invisible_" . $this->name . "();\"><img align=\"center\" id=\"mass_invis_" . $this->name . "\" src=\"" . constant('LOC_IMAGES') . "tech/bul100.gif\" onclick=\"mass_visible_" . $this->name . "();\"></td>";
				} else {
					$html .= "<td width=\"10px\"></td>";
				}
			}

			if ($this->showrowtips) {
				$html .= "<td width=\"10px\"></td>";
			}

			foreach ($this->columns AS $key => $var) {
				$html .= "<td nowrap=\"nowrap\" $attributes class=\"$class\">$var</td>";
			}
			$html .= "</tr>";
		}

		return $html;

	}

	/**
	* set name of table
	*
	* @access	private
	*/
	function setTableName($tablename='', $tablebodyname='') {
		$this->tablebodyname = $tablebodyname;
		$this->tablename = $tablename;
	}

	/**
	* Display action and options for each action drop down with "Process" button
	*
	* @access	public
	*
	* @param	string	$key	 Value of the action
	* @param	string	$name	Title of the action
	* @param	string	$Values	Optional options for the action
	* @param	string	$default	optional default option to be selected
	*/
	function setDynamicOption($key, $name, $values='', $default='') {

		$this->dynamics[] = array(
			'key' => $key,
			'name' => $name,
			'values' => $values,
			'default' => $default
		);

	}

	/**
	* Generates html for dynamic options set by setDynamicOption
	*
	* @access	private
	*/
	function buildDynamicOptions() {

		$dynamics = $this->dynamics;

		if (!is_array($dynamics)) {
			return;
		}

		echo get_javascript('DynamicOptionList.js');

		// build the HTML for the two select menus
		$html = "<select name=\"actiontype\" onChange=\"listB.populate();\">";

		foreach ($dynamics AS $dynamic) {
			$html .= "<option value=\"" . $dynamic['key'] . "\">" . $dynamic['name'] . "</option>";
		}

		$html .= "
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<select name=\"actionvalue\" id=\"actionvalue\">
			<script language=\"JavaScript\">listB.printOptions()</script>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=\"submit\" name=\"Process\" value=\"Process\">
			<script language=\"JavaScript\">
			init();
			</script>
		";

		// now build the elements for 2nd options

		$js = "
			<script language=\"javascript\">
				var listB = new DynamicOptionList(\"actionvalue\",\"actiontype\");
		";

		foreach ($dynamics AS $dynamic) {

			if (is_array($dynamic['values'])) {

				$bit = '';

				foreach($dynamic['values'] AS $key => $var) {
					$bit .= "\"$var\",\"$key\",";
				}
				$bit = substr($bit, 0, -1);

				$js .= "listB.addOptions(\"$dynamic[key]\", $bit);";
				if (isset($dynamic['default'])) {
					$js .= "listB.setDefaultOption(\"$dynamic[key]\",\"$dynamic[default]\");";
				}
			}
		}

		$js .= "
			function init() {
				var theform = document.forms.$this->form_name;
				listB.init(theform);
			}
			</script>
		";

		return  "<tr><td colspan=\"100\" class=\"matrix_left\" valign=\"top\">" . checkbox_option() . $js . $html . "</td></tr>";

	}

	/**
	* Sets action for checkboxes in left column
	*
	* @access	public
	*
	* @param	string	$key	 id of checkbox
	* @param	string	$var	action script
	*/
	function setCheckboxAction($key, $var) {
		$this->checkbox_actions[$key] = $var;
	}

	/**
	* Generates html for checkboxes on left
	*
	* @access	private
	*/
	function buildCheckboxAction() {

		if (is_array($this->checkbox_actions)) {

			$html = form_select('actiontype', $this->checkbox_actions);

			if ($this->form_end_text) {
				$html2 = form_submit($this->form_end_text);
				unset($this->form_end_text);
				$html3 = "</form>";
			}

			return  "<tr><td colspan=\"100\" class=\"matrix_left\" valign=\"top\">" . checkbox_option() . $html . "&nbsp;&nbsp;&nbsp;$html2</td>$html3</tr>";

		}
	}

	/**
	* builds table based on rows and cells details provided.
	*
	* @access	public
	*
	* @param	bool	$noecho	optional flag whether to display table or not
	*/
	function build($noecho=FALSE) {

		global $db, $store;

		// initialize some variables
		$html = '';
		$javascript_2 = '';
		$javascript_3 = '';
		$javascript = "<script language=\"javascript\">";

		// build a form if we have one
		$html .= $this->buildFormStart();

		if ($this->max_attach_size) {
			$html .= form_hidden('MAX_FILE_SIZE', $this->max_attach_size);
		}

		// determine if we are showing rowtips
		if ($this->showrowtips) {
			if (!is_array($this->rows)) {
				unset($this->showrowtips);
			}
		}

		// build the title (1st <tr>)
		$html .= $this->build_title();

		// start the table
		$html .= "<table " . iff($this->tablename, "id=\"$this->tablename\" ") . "cellpadding=\"0\" style=\"width:100%\" cellspacing=\"1\" class=\"content_table\">";

		$html .= $this->build_toprow();

		// build the columns (2nd <tr>)
		$cols = $this->build_columns();

		if ($cols) {
			$html .= '<thead>' . $cols . '</thead>';
		}

		// add the data
		if (is_array($this->rows)) {

			$k = 0;

			foreach ($this->rows AS $rowkey => $row) {

				$k++;

				if ($row->tbody) {
					$html .= '<tbody '.$row->tbody_attr.'>';
				}

				// Check if its a separator
				if ($this->separator[$rowkey]) {

					// No celldata means just a thick line separator
					if (!$row->data[0] OR !$row->data[0]->data) {

						$next_row_class = ' separator_line';
						continue;

					// Else a titled separator
					} else {
						$html .= "<tr id=\"" . $this->name . "_html_table_id_$k\">";
						$html .= '<td colspan="100" align="center" class="separator">'.$row->data[0]->data.'</td>';
						$html .= '</tr>';
						continue;
					}
				}

				// If for some reason we're here without data, we should continue
				if (!is_array($row->data) OR !$row->data) {
					continue;
				}


				$html .= "<tr id=\"" . $this->name . "_html_table_id_$k\" class=\"{$row->classname} {$next_row_class}\">";
				$attributes = " class=\"matrix_left\"";

				$next_row_class = '';



				// add the checkbox
				if ($this->checkbox[$rowkey]) {
					if (is_array($this->checkbox[$rowkey])) {
						$html .= "<td width=\"10px\" class=\"matrix_left matrix_checkbox\">" . form_checkbox($this->checkbox[$rowkey][0], 1, $this->checkbox[$rowkey][1], 'doaction') . "</td>";
					} elseif ($this->checkbox[$rowkey] == 'EMPTY_CHECKBOX') {
						if ($this->checkbox_selected[$rowkey]) {
							$html .= "<td width=\"10px\" class=\"matrix_left matrix_checkbox\">{$this->checkbox_selected[$rowkey]}</td>";
						} else {
							$html .= "<td width=\"10px\" class=\"matrix_left matrix_checkbox\">&nbsp;</td>";
						}
					} else {
						$html .= "<td width=\"10px\" class=\"matrix_left matrix_checkbox\">" . form_checkbox($this->checkbox[$rowkey], 1, $this->checkbox_selected[$rowkey], 'doaction') . "</td>";
					}
				}

				// add the 2nd row icon
				if ($this->secondline[$rowkey] != '') {

					// using rand so we can have multiple secondlines on the same page
					$key_image1 = $this->name . $rowkey . 'image1';
					$key_image2 = $this->name . $rowkey . 'image2';
					$keyid = $this->name . $rowkey;
					$html .= "<td $attributes><img align=\"center\" style=\"display:none\" id=\"$key_image1\" src=\"" . constant('LOC_IMAGES') . "tech/bul101.gif\" onclick=\"oc('$keyid');oc('$key_image1');oc('$key_image2');\"><img align=\"center\" id=\"$key_image2\" src=\"" . constant('LOC_IMAGES') . "tech/bul100.gif\" onclick=\"oc('$keyid');oc('$key_image1');oc('$key_image2');\"></td>";
					$javascript_2 .= "visyes('$keyid');visyes('$key_image1');visno('$key_image2');";
					$javascript_3 .= "visno('$keyid');visno('$key_image1');visyes('$key_image2');";

				} elseif ($this->usingsecondline) {
					$html .= "<td $attributes>&nbsp;</td>";
				}

				if ($this->showrowtips) {

					if ($row->helptext) {
						$html .= "<td width=\"10px\" $attributes>" . html_image('icons/help3.gif', '', "onmouseover=\"return escape('" . $db->escape($row->helptext) . "');\"") . "</td>";
					} elseif ($row->helpkey) {
						$store->addHelp($this->name, $row->helpkey);
						$html .= "<td width=\"10px\" $attributes>" . html_image('icons/help3.gif', '', "onmouseover=\"return escape(TooltipText('$this->name', '$row->helpkey', this));\"") . "</td>";
					} else {
						$html .= "<td width=\"10px\" $attributes></td>";
					}
				}

				$col = '0';

				ksort($row->data);

				foreach ($row->data AS $cellkey => $cell) {

					/*
						- column attributes		$this->columnstyle[$cellkey]
						- row attributes
						- cell attributes		$cell->style
					*/

					give_default($var2, '&nbsp;');

					// if contains class, we don't use current class, otherwise we do
					if ($row->style) {
						if (in_string('class', $row->style)) {
							$attributes = $row->style;
						} else {
							$attributes .= $row->style;
						}
					}

					$html .= "<td " . $this->columnstyle[$cellkey] . ' ' . $cell->style . "  $attributes>" . $cell->data . "</td>";
					$col++;
					unset($e_attributes);
				}

				$html .= "</tr>";

				// add the next row
				if ($this->secondline[$rowkey] != '') {
					$attributes = " class=\"matrix_left\"";
					$html .= "<tr id=\"$keyid\" style=\"display:none\"><td width=\"10px\" style=\"max-width: 10px\" $attributes>&nbsp;</td><td colspan=\"50\" width=\"100%\" $attributes>" . $this->secondline[$rowkey] . "</td></tr>";
				}

				// add open row
				if ($this->openline[$rowkey] != '') {
					$attributes = " class=\"matrix_left2\"";
					$html .= "<tr><td class=\"matrix_left2\"></td><td colspan=\"50\" width=\"100%\" $attributes>" . $this->openline[$rowkey] . "</td></tr>";
				}

				if ($row->tbody) {
					$html .= '</tbody>';
				}

			}
		} else {
			if ($data) {
				$html .= "<tr><td class=\"matrix_left\" colspan=\"100\">$data</td></tr>";
			}
		}

		$html .= $this->buildDynamicOptions();
		$html .= $this->buildCheckboxAction();

		if ($javascript_2) {
			$javascript .= "function mass_visible_" . $this->name . "() { oc('mass_vis_" . $this->name . "'); oc('mass_invis_" . $this->name . "'); $javascript_2 } function mass_invisible_" . $this->name . "() { oc('mass_vis_" . $this->name . "'); oc('mass_invis_" . $this->name . "'); $javascript_3 }";
		}

		$html .= "</table>";

		$javascript .= "</script>";

		$html .= $javascript;

		$html .=  $this->buildFormEnd();

		// pagenav
		if (is_object($this->pagenav)) {
			$html .= '<br />' . $this->pagenav->form();
		}

		if (!$noecho) {
			echo $html;
		} else {
			return $html;
		}

	}
}

?>
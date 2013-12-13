<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 */

namespace Application\DeskPRO\Twig\PreProcessor;

/**
 * Runs through the simplified syntax for email templates
 */
class EmailPreProcessor extends AbstractPreProcessor
{
	/**
	 * @param string $source
	 * @return string
	 */
	public function process($source, $name = null)
	{
		if (strpos($name, 'DeskPRO:emails_common:') === 0) {
			return $source;
		}

		if (strpos($source, '{% extends') === false && strpos($source, '<dp:subject>') !== false) {
			$source = $this->getPrepend() . $source;
		} else {
			$source = preg_replace('#<dp:subject>\s*</dp:subject>#is', '', $source);
		}

		$source = $this->processTagAsBlock($source, 'subject', 'email_subject');
		if (strpos($source, '{%- endblock email_subject -%}') !== null) {
			$source = str_replace('{%- endblock email_subject -%}', '{%- endblock email_subject -%}{%- block content -%}', $source);
		} else {
			$source = str_replace('{% import \'DeskPRO:emails_common:layout-macros.html.twig\' as layout %}', '{%- block content -%}', $source);
		}

		$source .= '{%- endblock content -%}';

		$source = $this->processSelfTagAsMacro($source, 'agent-reply', 'show_first_message');
		$source = $this->processSelfTagAsMacro($source, 'user-reply', 'show_first_message');
		$source = $this->processSelfTagAsMacro($source, 'reply-quoted', 'show_latest_message');
		$source = $this->processSelfTagAsMacro($source, 'ticket-history', 'show_rest_message');
		$source = $this->processSelfTagAsMacro($source, 'ticket-messages', 'show_all_messages');
		$source = $this->processSelfTagAsMacro($source, 'agent-ticket-history', 'show_rest_message_agent');
		$source = $this->processSelfTagAsMacro($source, 'ticket-logs', 'show_ticket_logs');
		$source = $this->processSelfTagAsMacro($source, 'ticket-rating-links', 'show_rating_links');

		$source = $this->processTagAsTpl($source, 'ticket-properties-table', 'DeskPRO:emails_common:ticket-props-table.html.twig');

		$sets = array();
		$source = preg_replace_callback('#\s*\{\{\s*set_tplvar\((.*?),\s*(.*?)\)\s*\}\}\s*#', function ($m) use (&$sets) {
			$sets[] = trim($m[0]);
		}, $source);

		if ($sets) {
			$source = '{% block email_pre %}'.implode("\n", $sets).'{% endblock %}' . $source;
		}

		return $source;
	}


	/**
	 * Parses tags into blocks
	 *
	 * @param string $source
	 * @param string $tagname
	 * @param string $blockname
	 * @return string
	 */
	public function processTagAsBlock($source, $tagname, $blockname)
	{
		$source = str_replace("<dp:$tagname>", "{%- block $blockname -%}", $source);
		$source = str_replace("</dp:$tagname>", "{%- endblock $blockname -%}", $source);
		return $source;
	}


	/**
	 * Parses tags content into a variable, and then includes a template with 'content' set to the set variable.
	 *
	 * @param string $source
	 * @param string $tagname
	 * @param string $tplname
	 * @return string
	 */
	public function processTagAsTpl($source, $tagname, $tplname)
	{
		$self = $this;
		$set_id_stack = array();
		$source = preg_replace_callback("#<dp:$tagname>#", function($m) use ($self, &$set_id_stack) {
			$set_id = $self->getSetId();
			$set_id_stack[] = $set_id;

			$new = "{%- set $set_id -%}";
			return $new;
		}, $source);

		$source = preg_replace_callback("#</dp:$tagname>#", function($m) use ($tplname, &$set_id_stack) {
			$set_id = array_shift($set_id_stack);
			$new = "{%- endset -%}{% include '$tplname' with {content: $set_id} %}";
			return $new;
		}, $source);

		$source = preg_replace_callback("#<dp:$tagname\s*/>#", function($m) use ($tplname) {
			$new = "{% include '$tplname' %}";
			return $new;
		}, $source);

		return $source;
	}


	/**
	 * Parses a self-closing tag into a macro call
	 *
	 * @param string $source
	 * @param string $tagname
	 * @param string $tplname
	 * @return string string
	 */
	public function processSelfTagAsMacro($source, $tagname, $tplname)
	{
		$source = preg_replace_callback("#<dp:$tagname\s*/>#", function($m) use ($tplname) {
			$new = "{{ layout.$tplname(_context) }}";
			return $new;
		}, $source);

		return $source;
	}


	/**
	 * Parses tags content into a variable, and then calls a macro with first parameter the content.
	 *
	 * @param string $source
	 * @param string $tagname
	 * @param string $tplname
	 * @return string string
	 */
	public function processTagAsMacro($source, $tagname, $tplname)
	{
		$self = $this;
		$set_id_stack = array();
		$source = preg_replace_callback("#<dp:$tagname>#", function($m) use ($self, &$set_id_stack) {
			$set_id = $self->getSetId();
			$set_id_stack[] = $set_id;

			$new = "{%- set $set_id -%}";
			return $new;
		}, $source);

		$source = preg_replace_callback("#</dp:$tagname>#", function($m) use ($tplname, &$set_id_stack) {
			$set_id = array_shift($set_id_stack);
			$new = "{%- endset -%}{{ layout.$tplname($set_id) }}";
			return $new;
		}, $source);

		return $source;
	}


	/**
	 * Wraps a block of code, and only shows it if a certain block has a value (usually an inner-containing block).
	 *
	 * @param string $source
	 * @return string
	 */
	public function processIfblock($source)
	{
		$self = $this;
		$set_id_stack = array();
		$tagname = '';
		$source = preg_replace_callback("#\{%\s*ifblock\s*([a-zA-Z_]+)\s*%\}#", function($m) use (&$tagname, $self, &$set_id_stack) {
			$set_id = $self->getSetId();
			$set_id_stack[] = $set_id;
			$tagname = $m[1];

			$new = "{%- set $set_id -%}";
			return $new;
		}, $source);

		$source = preg_replace_callback("#\{%\s*endifblock\s*%\}#", function($m) use ($tagname, &$set_id_stack) {
			$set_id = array_shift($set_id_stack);
			$new = "{%- endset -%}{% if block('$tagname')|trim %}{{ $set_id }}{% endif %}";
			return $new;
		}, $source);

		return $source;
	}


	/**
	 * Get a unique varname to use
	 *
	 * @return string
	 */
	public function getSetId()
	{
		static $id = 0;
		$id++;

		return 'set_' . time() . '_' . $id;
	}


	/**
	 * Get default code to prepend to the header of the template
	 *
	 * @return string
	 */
	public function getPrepend()
	{
		return <<<'SRC'
{% extends 'DeskPRO:emails_common:layout.html.twig' %}
{% import 'DeskPRO:emails_common:layout-macros.html.twig' as layout %}
SRC;
	}
}
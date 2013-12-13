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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql;

/**
 * Compiles a DPQL string statement into a statement object.
 */
class Compiler
{
	/**
	 * @var \Application\DeskPRO\Dpql\Lexer|null
	 */
	protected $_lexer;

	/**
	 * @var \Application\DeskPRO\Dpql\Parser|null
	 */
	protected $_parser;

	/**
	 * @param \Application\DeskPRO\Dpql\Lexer|null $lexer
	 * @param \Application\DeskPRO\Dpql\Parser|null $parser
	 */
	public function __construct(Lexer $lexer = null, Parser $parser = null)
	{
		if (!$lexer) $lexer = new Lexer();
		if (!$parser) $parser = new Parser();

		$this->_lexer = $lexer;
		$this->_parser = $parser;
	}

	/**
	 * Compiles the given DPQL string to a statement object
	 *
	 * @param string $input
	 * @param array $placeholders
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Display
	 */
	public function compile($input, array $placeholders = array())
	{
		$input = $this->replacePlaceholders($input, $placeholders);
		$statement = $this->lexAndParse($input);
		$statement->prepare();

		return $statement;
	}

	/**
	 * Lexes and parses a DPQL string. Only ensures that it's syntactically valid.
	 *
	 * @param string $input
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Display
	 */
	public function lexAndParse($input)
	{
		$this->_lexer->setInput($input);

		while ($this->_lexer->yylex()) {
			$this->_parser->line = $this->_lexer->line;
			$this->_parser->doParse($this->_lexer->token, $this->_lexer->value);
		}
		$this->_parser->doParse(0, 0);

		return $this->_parser->getResult();
	}

	public function replacePlaceholders($input, array $placeholders = array())
	{
		$repository = \Application\DeskPRO\App::getEntityRepository('DeskPRO:ReportBuilder');

		$groupParams = $repository->getReportGroupParams();

		$input = preg_replace_callback(
			'/%(\d+):DATE_GROUP%/',
			function ($match) use ($placeholders, $groupParams) {
				if (isset($placeholders[$match[1]])) {
					$value = strval($placeholders[$match[1]]);
					if (isset($groupParams['dates'][$value])) {
						return $groupParams['dates'][$value][1];
					}
				}

				$first = reset($groupParams['dates']);
				return $first[1];
			},
			$input
		);

		$input = preg_replace_callback(
			'/%(\d+):FIELD_GROUP:([^:%]+)(:([^%]+))?%/',
			function ($match) use ($placeholders, $groupParams) {
				$type = $match[2];
				$table = isset($match[4]) ? $match[4] : $type;

				if (isset($placeholders[$match[1]])) {
					$value = strval($placeholders[$match[1]]);
					if (isset($groupParams['fields'][$type][$value])) {
						return sprintf($groupParams['fields'][$type][$value][1], $table);
					}
				}

				if (isset($groupParams['fields'][$type])) {
					$first = reset($groupParams['fields'][$type]);
					return sprintf($first[1], $table);
				}

				return 'NULL';
			},
			$input
		);

		$input = preg_replace_callback(
			'/%(\d+):STATUS_GROUP:([^:%]+)(:([^%]+))?%/',
			function ($match) use ($placeholders, $groupParams) {
				$type = $match[2];
				$table = isset($match[4]) ? $match[4] : $type;

				if (isset($placeholders[$match[1]])) {
					$value = strval($placeholders[$match[1]]);
					if (isset($groupParams['statuses'][$type][$value])) {
						return sprintf($groupParams['statuses'][$type][$value][1], $table);
					}
				}

				if (isset($groupParams['statuses'][$type])) {
					$first = reset($groupParams['statuses'][$type]);
					return sprintf($first[1], $table);
				}

				return '1';
			},
			$input
		);

		$input = preg_replace_callback(
			'/%(\d+):ORDER_GROUP:([^:%]+)(:([^%]+))?%/',
			function ($match) use ($placeholders, $groupParams) {
				$type = $match[2];
				$table = isset($match[4]) ? $match[4] : $type;

				if (isset($placeholders[$match[1]])) {
					$value = strval($placeholders[$match[1]]);
					if (isset($groupParams['orders'][$type][$value])) {
						return sprintf($groupParams['orders'][$type][$value][1], $table);
					}
				}

				if (isset($groupParams['orders'][$type])) {
					$first = reset($groupParams['orders'][$type]);
					return sprintf($first[1], $table);
				}

				return 'NULL';
			},
			$input
		);

		return $input;
	}
}
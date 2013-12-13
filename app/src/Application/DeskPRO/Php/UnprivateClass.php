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
 * @subpackage
 */

namespace Application\DeskPRO\Php;

/**
 * This looks at a PHP source file and replaces private variables and methods with protected ones
 */
class UnprivateClass
{
	protected $code;
	protected $new_code;
	protected $strip_comments = false;

	public function __construct($code)
	{
		$this->code = $code;
	}

	/**
	 * Strip comments as well
	 */
	public function enableStripComments()
	{
		$this->strip_comments = true;
	}


	/**
	 * @return string
	 */
	public function getCode()
	{
		if ($this->new_code !== null) {
			return $this->new_code;
		}

		$tokens = token_get_all($this->code);

		$this->new_code = '';
		foreach ($tokens as $token) {
			if (!is_array($token)) {
				$this->new_code .= $token;
				continue;
			}

			$token_name = $token[0];
			$token_str = $token[1];

			if (($this->strip_comments && ($token_name == T_DOC_COMMENT || $token_name == T_COMMENT)) || $token_name == T_FINAL) {
				continue;
			}

			if ($token_name == T_PRIVATE) {
				$token_str = 'protected';
			}

			$this->new_code .= $token_str;
		}

		return $this->new_code;
	}
}
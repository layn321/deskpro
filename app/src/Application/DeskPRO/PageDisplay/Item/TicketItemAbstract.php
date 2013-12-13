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
 * @subpackage PageDisplay
 */

namespace Application\DeskPRO\PageDisplay\Item;

use Application\DeskPRO\PageDisplay\Item\ItemInterface;

abstract class TicketItemAbstract extends ItemAbstract
{
	public function compileJsCheck()
	{
		$js = array();
		$js[] = "function (t) {";

		if ($this->conds_all OR $this->conds_any) {
			if ($this->conds_all) {
				$ticket_terms = new \Application\DeskPRO\Tickets\TicketTerms($this->conds_all);
				$js[] = "var all_check = function(ticket) {";
				$js[] = $ticket_terms->compileTermsToJavascript('all');
				$js[] = "};";
			} else {
				$js[] = "var all_check = function(ticket){return true;};";
			}

			if ($this->conds_any) {
				$ticket_terms = new \Application\DeskPRO\Tickets\TicketTerms($this->conds_any);
				$js[] = "var any_check = function(ticket) {";
				$js[] = $ticket_terms->compileTermsToJavascript('any');
				$js[] = "};";
			} else {
				$js[] = "var any_check = function(ticket){return true;};";
			}

			$js[] = "if (any_check(t) && all_check(t)) return true; else return false;";
		} else {
			$js[] = 'return true';
		}

		$js[] = "}";

		$js = implode(' ', $js);

		// Very simple minify
		$js = str_replace("\n", ' ', $js);
		$js = preg_replace("# {2,}#", ' ', $js);
		$js = preg_replace("#;\w+#", ';', $js);
		$js = str_replace('if (', 'if(', $js);
		$js = str_replace('ticket.', 't.', $js);
		$js = str_replace('function(ticket)', 'function(t)', $js);

		return $js;
	}
}

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

namespace Application\DeskPRO\UI;

use Orb\Util\Arrays;

/**
 * Handling around the JS "rule builder" widget.
 *
 * So a rule array (or a terms array, they are similar) is a simple structure. Each rule/term in the array
 * is an array that has special recognized keys, and then any number of options. Options are simply
 * array keys that were supplied that aren't "special"
 *
 * We only have two kinds of special keys:
 * - type: This is used with both rules and terms array, it's just a type identifier. For example, a term type
 * might be 'id' (types are usually defined in Searcher classes as contants).
 * - op: This is used with terms arrays, it specifies the type of comparison to make. For example, with numbers
 * we have gt, lt, gte, lte etc.
 *
 * This RuleBuilder just makes it easier to read these arrays into a standard format. From the form, we get:
 * array(..all data..)
 *
 * But a more standardized array is:
 * array('type' => 'xxx', 'op' => 'xxx', 'options' => array(..formdata..))
 *
 * Where 'options' is just any other field read in that wasn't type/op.
 */
class RuleBuilder
{
	protected $special_keys = array();

	/**
	 * The "Actions" builder has a 'type' item and then an options array.
	 *
	 * A typical result might look like:
	 * <code>
	 * array(
	 *     array('type' => 'urgency', 'options' => array('num' => 22)),
	 *     array('type' => 'priority_id', 'options' => array('priority_id' => 5)),
	 * )
	 * </code>
	 *
	 * @return \Application\DeskPRO\UI\RuleBuilder
	 */
	public static function newActionsBuilder()
	{
		return new self(array('type'));
	}


	/**
	 * The "Terms" builder has a 'type' item and an 'op' item, and then an options array.
	 *
	 * A typical result might look like:
	 * <code>
	 * array(
	 *     array('type' => 'urgency', 'op' => 'ltg', 'options' => array('num' => 22)),
	 *     array('type' => 'priority_id', 'op' => 'is', 'options' => array('priority_id' => 5)),
	 * )
	 * </code>
	 *
	 * @return \Application\DeskPRO\UI\RuleBuilder
	 */
	public static function newTermsBuilder()
	{
		return new self(array('type', 'op'));
	}


	/**
	 * Special keys are keys we'll find in the form that aren't options.
	 *
	 * @param array $special_keys
	 */
	public function __construct(array $special_keys)
	{
		$this->special_keys = $special_keys;
	}


	/**
	 * Read an array of terms based from the form, using structure defined.
	 *
	 * @param array $form
	 * @return array
	 */
	public function readForm(array $form)
	{
		$data = array();

		foreach ($form as $item) {
			if (!is_array($item)) continue;

			$data_item = array();
			foreach ($this->special_keys as $k) {
				$data_item[$k] = null;
			}
			$data_item['options'] = array();

			$is_blank = true;
			foreach ($item as $k => $v) {
				// Special value means not to add the term
				// Used for things like "any" where the term shouldnt
				// be applied.
				if ($v == 'DP_DISCARD_TERM') {
					continue 2;
				}

				if ($v == 'DP_ALLOW_BLANK') {
					$is_blank = false;
					continue;
				}

				if (in_array($k, $this->special_keys)) {
					$data_item[$k] = $v;
				} else {
					if ($is_blank) {
						if (is_array($v)) {
							$v = Arrays::removeEmptyString($v);
							if ($v) {
								$is_blank = false;
							}
						} else {
							$v = trim($v);
							if ($v !== '') {
								$is_blank = false;
							}
						}
					} elseif (!is_array($v) && trim($v) !== '') {
						$is_blank = false;
					} elseif (is_array($v) && !$v) {
						$is_blank = false;
					}
					$data_item['options'][$k] = $v;
				}
			}

			if ($is_blank) {
				continue;
			}

			$data[] = $data_item;
		}

		return $data;
	}
}

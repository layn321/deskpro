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

use Application\DeskPRO\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Orb\Util\Arrays;

/**
 * Generates information useful for a tag cloud
 */
class TagCloud
{
	protected $_tag_counts = array();
	protected $_min_count = 0;
	protected $_max_count = 0;
	protected $_spread = 1;
	protected $_max_size = 10;
	protected $_class_prefix = 'tag-size';

	/**
	 * $tag_counts must be an array of tag=>count for all tags you want to include in
	 * the cloud.
	 *
	 * @param array $tag_counts
	 */
	public function __construct(array $tag_counts)
	{
		$this->_tag_counts = $tag_counts;

		if ($tag_counts) {
			$this->_min_count = min($this->_tag_counts);
			$this->_max_count = max($this->_tag_counts);
			$this->_spread = max(1, $this->_max_count - $this->_min_count);
		}
	}

	public function getCloud()
	{
		$cloud = array();

		foreach ($this->_tag_counts as $tag => $count) {
			$size = round(1 + (($count - $this->_min_count) * (($this->_max_count - 1) / $this->_spread)));
			$size_class = $this->_class_prefix . $size;

			$cloud[$tag] = $size_class;
		}

		Arrays::shuffleAssoc($cloud);

		return $cloud;
	}
}

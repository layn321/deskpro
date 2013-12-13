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
 * @subpackage Elastica
 */

namespace Application\DeskPRO\Elastica\Type;

use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Elastica\Transformer\FeedbackTransformer;

use APplication\DeskPRO\App;

/**
 * Type for the Feedback entity
 */
class FeedbackType extends AbstractType
{
	/**
	 * Transform a value into a Document
	 *
	 * @param  Feedback $feedback
	 * @return \Elastic_Document
	 */
	public function transformToDocument($feedback)
	{
		$trans = new FeedbackTransformer();
		$doc = $trans->transform($feedback);

		$doc->setIndex('content');
		$doc->setType('feedback');

		return $doc;
	}


	/**
	 * Get the document type
	 *
	 * @return string
	 */
	public function getType()
	{
		return 'feedback';
	}


	/**
	 * Get a single value from a document
	 *
	 * @return \Application\DeskPRO\Entity\Feedback
	 */
	protected function getValueFromResult(\Elastica_Result $doc)
	{
		return App::getEntityRepository('DeskPRO:Feedback')->find($doc->getId());
	}
}

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

use Application\DeskPRO\Elastica\ElasticaManager;

/**
 * A type represents a type of indexable document
 */
abstract class AbstractType
{
	/**
	 * @var \Application\DeskPRO\Elastica\ElasticaManager
	 */
	protected $manager;

	public function __construct(ElasticaManager $manager)
	{
		$this->manager = $manager;
	}


	/**
	 * Get the document type
	 *
	 * @return string
	 */
	abstract public function getType();


	/**
	 * Transform a value into a Document
	 *
	 * This is like using a Transformer, but this should also set the proper Index
	 * and Type names.
	 *
	 * @param  $value
	 * @return \Elastic_Document
	 */
	abstract public function transformToDocument($value);


	/**
	 * Transform a documents into this type.
	 *
	 * $value can be a single value, or an array of values
	 *
	 * @param \Elastica_Result|Elastica_Result[] $docs
	 * @return mixed
	 */
	public function transformToType($docs)
	{
		if (is_array($docs)) {
			if (!$docs) return array();
			return $this->getValuesFromResults($docs);
		} else {
			if (!$docs) return null;
			return $this->getValueFromResult($docs);
		}
	}


	/**
	 * Get a single value from a document
	 *
	 * @return mixed
	 */
	abstract protected function getValueFromResult(\Elastica_Result $doc);

	
	/**
	 * Get multiple values from documents
	 *
	 * @return array
	 */
	protected function getValuesFromResults(array $docs)
	{
		$values = array();
		foreach ($docs as $doc) {
			$v = $this->getValueFromDoc($doc);
			if ($v) {
				$values[] = $v;
			}
		}
		return $values;
	}
}

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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Application\DeskPRO\Translate\HasPhraseName;
use Application\DeskPRO\Translate\Translate;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * A language groups phrases and defines a locale code.
 *
 */
class Language extends \Application\DeskPRO\Domain\DomainObject implements HasPhraseName
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * The unique sys name assigned to the language
	 *
	 * @var string
	 */
	protected $sys_name;

	/**
	 * The three-letter ISO 639-2 code
	 *
	 * @var string
	 */
	protected $lang_code;

	/**
	 * Title of the language
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The base filepath for default phrases for this lang.
	 *
	 * @var string
	 */
	protected $base_filepath;

	/**
	 * The locale code
	 *
	 * @var string
	 */
	protected $locale = 'en_US';

	/**
	 * @var string
	 */
	protected $flag_image = '';

	/**
	 * True if this is a right-to-left language.
	 *
	 * @var bool
	 */
	protected $is_rtl = false;

	/**
	 * @var bool
	 */
	protected $has_user = true;

	/**
	 * @var bool
	 */
	protected $has_agent = true;

	/**
	 * @var bool
	 */
	protected $has_admin = true;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function _invalidateLanguageCache()
	{
		$orm = App::getOrm();

		if (method_exists($orm, 'delayedUpdate')) {
			$orm->delayedUpdate(function($em) {
				// defer this until after the flush to avoid a race condition and make sure it's updated after insert
				$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
				$cache->invalidateLanguageCache();
			});
		}
	}

	/**
	 * Return a unique ID that we can use to look up translations for this object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @param Translate $translate The translate object requesting
	 * @return string
	 */
	public function getPhraseName($property = null, Translate $translate)
	{
		return 'user.lang.lang_title_' . $this->sys_name;
	}

	/**
	 * Get the default value phrase for the object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @param Translate $translate The translate object requesting
	 * @return string
	 */
	public function getPhraseDefault($property = null, Translate $translate)
	{
		return $this->title;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Language';
		$metadata->setPrimaryTable(array( 'name' => 'languages', ));
		$metadata->addLifecycleCallback('_invalidateLanguageCache', 'preFlush');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'sys_name', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'sys_name', ));
		$metadata->mapField(array( 'fieldName' => 'lang_code', 'type' => 'string', 'length' => 3, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'lang_code', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'base_filepath', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'base_filepath', ));
		$metadata->mapField(array( 'fieldName' => 'locale', 'type' => 'string', 'length' => 8, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'locale', ));
		$metadata->mapField(array( 'fieldName' => 'flag_image', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'flag_image', ));
		$metadata->mapField(array( 'fieldName' => 'is_rtl', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'is_rtl', ));
		$metadata->mapField(array( 'fieldName' => 'has_user', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'has_user', ));
		$metadata->mapField(array( 'fieldName' => 'has_agent', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'has_agent', ));
		$metadata->mapField(array( 'fieldName' => 'has_admin', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'has_admin', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}

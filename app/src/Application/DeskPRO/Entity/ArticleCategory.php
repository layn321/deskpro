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

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;

/**
 * Article categories
 *
 */
class ArticleCategory extends CategoryAbstract
{
	/**
	 */
	protected $parent;

	/**
	 */
	protected $children;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $usergroups;

	/**
	 * If this is true, then all categories and articles under this one
	 * are considered agent KB articles and wont be displayed in
	 * the user interface
	 *
	 * @var bool
	 */
	protected $is_agent = false;

	/**
	 * If this is true, then all the articles and categories under this category
	 * is treated as a book (aka manual).
	 *
	 * @var bool
	 */
	protected $is_book = false;

	/**
	 * The template suffix to use when rendering the category, and articles within
	 * the category.
	 *
	 * Eg UserBundle:Articles:article.html.twig
	 * With suffix 'download' becomes
	 * UserBundle:Articles:article-download.html.twig
	 *
	 * @var string
	 */
	protected $template_suffix = '';



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\ArticleCategory';
		$metadata->setPrimaryTable(array( 'name' => 'article_categories', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'is_agent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent', ));
		$metadata->mapField(array( 'fieldName' => 'is_book', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_book', ));
		$metadata->mapField(array( 'fieldName' => 'template_suffix', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'template_suffix', ));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->mapField(array( 'fieldName' => 'depth', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'depth', ));
		$metadata->mapField(array( 'fieldName' => 'root', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'root', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ArticleCategory', 'mappedBy' => NULL, 'inversedBy' => 'children', 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'onDelete' => 'set null', ), ), 'dpApi' => true  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'children', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ArticleCategory', 'mappedBy' => 'parent',  'orderBy' => array( 'display_order' => 'ASC', ), ));
		$metadata->mapManyToMany(array( 'fieldName' => 'usergroups', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'cascade' => array('persist','merge'), 'joinTable' => array( 'name' => 'article_category2usergroup', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'dpApi' => true ));
	}
}

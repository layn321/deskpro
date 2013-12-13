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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Form\Model;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\Person;

class NewNews
{
	public $title;
	public $category_id;
	public $status;
	public $content = '';

	public $slug;
	public $labels_json;
	public $labels = array();
	public $attach = array();

	protected $_news;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $_em;

	public function __construct(Person $person_context)
	{
		$this->_person_context = $person_context;

		$this->_em = App::getOrm();
	}

	public function save()
	{
		$this->_em->beginTransaction();

		$news = new News();
		$news->person = $this->_person_context;
		$news->title = $this->title;
		$news->content = $this->content ?: '';
		$news->setStatusCode($this->status);

		if ($news->getStatusCode() == 'published' && !$this->_person_context->hasPerm('agent_publish.validate')) {
			$news->setStatusCode('hidden.validating');
		}

		$cat = $this->_em->find('DeskPRO:NewsCategory', $this->category_id);
		$news->category = $cat;

		$this->_em->persist($news);
		$this->_em->flush();

		if ($this->labels) {
			$news->getLabelManager()->setLabelsArray($this->labels, $this->_em);
			$this->_em->flush();
		}

		$this->_em->commit();

		$this->_news = $news;
	}

	public function getNews()
	{
		return $this->_news;
	}
}

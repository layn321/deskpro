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
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\ArticleAttachment;
use Application\DeskPRO\Entity\Person;

class NewArticle
{
	public $title;
	public $category_id;
	public $status;
	public $content;
	public $language_id;

	public $slug;
	public $labels = array();
	public $attach = array();

	protected $_article;

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

		$article = new Article();
		$article->person = $this->_person_context;
		$article->setStatusCode($this->status);

		if ($article->getStatusCode() == 'published' && !$this->_person_context->hasPerm('agent_publish.validate')) {
			$article->setStatusCode('hidden.validating');
		}

		$article->title = $this->title;
		$article->content = $this->content ?: '';

		$lang = null;
		if ($this->language_id) {
			$lang = App::getContainer()->getLanguageData()->get($this->language_id);
		}
		if (!$lang) {
			$lang = App::getContainer()->getLanguageData()->getDefault();
		}
		$article->language = $lang;

		$cat = $this->_em->find('DeskPRO:ArticleCategory', $this->category_id);
		$article->addToCategory($cat);

		$this->_em->persist($article);
		$this->_em->flush();

		if ($this->labels) {
			$article->getLabelManager()->setLabelsArray($this->labels, $this->_em);
		}

		// Message Attachments
		foreach ($this->attach as $blob_id) {
			$blob = App::getOrm()->getRepository('DeskPRO:Blob')->find($blob_id);
			if ($blob) {
				$attach = new ArticleAttachment();
				$attach['blob'] = $blob;
				$attach['person'] = $this->_person_context;
				$this->_em->persist($attach);
				$article->addAttachment($attach);
			}
		}

		$this->_em->flush();

		$this->_em->commit();

		$this->_article = $article;
	}

	public function getArticle()
	{
		return $this->_article;
	}
}

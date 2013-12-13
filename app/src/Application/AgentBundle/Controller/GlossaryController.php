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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\GlossaryWord;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

/**
 * Glossary listing and editing
 */
class GlossaryController extends AbstractController
{
	public function glossaryNewWordJsonAction()
	{
		$words = $this->in->getCleanValueArray('words', 'string');

		$definition = new \Application\DeskPRO\Entity\GlossaryWordDefinition();
		$definition->definition = $this->in->getString('definition');
		foreach ($words AS $word) {
			$definition->addWord($word);
		}

		if (!count($definition->words)) {
			return $this->createJsonResponse(array('error' => 'no_word'));
		}

		$this->em->persist($definition);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'definition_id' => $definition['id'],
			'words' => $words,
			'definition' => $definition['definition']
		));
	}

	public function glossarySaveWordJsonAction($word_id)
	{
		$word = $this->em->find('DeskPRO:GlossaryWord', $word_id);
		if (!$word || !$word->definition) {
			return $this->createJsonResponse(array('error' => 'not_found'));
		}

		$words = $this->in->getCleanValueArray('words', 'string');
		if (!$words) {
			return $this->createJsonResponse(array('error' => 'no_word'));
		}

		$definition = $word->definition;

		$definition['definition'] = $this->in->getString('definition');
		$definition->updateWords($words);

		if (!count($definition->words)) {
			return $this->createJsonResponse(array('error' => 'no_word'));
		}

		$this->em->persist($definition);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'definition_id' => $definition['id'],
			'words' => $words,
			'definition' => $definition['definition']
		));
	}

	public function glossaryDeleteWordJsonAction($word_id)
	{
		$word = $this->em->find('DeskPRO:GlossaryWord', $word_id);
		if (!$word || !$word->definition) {
			return $this->createJsonResponse(array('error' => 'not_found'));
		}

		$definition = $word->definition;

		$words = array();
		foreach ($definition->words AS $word) {
			$words[] = $word->word;
		}

		$this->em->remove($definition);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'definition_id' => $definition['id'],
			'words' => $words,
			'definition' => $definition['definition']
		));
	}

	public function glossaryWordJsonAction($word_id)
	{
		$word = $this->em->find('DeskPRO:GlossaryWord', $word_id);
		if (!$word || !$word->definition) {
			return $this->createJsonResponse(array('error' => 'not_found'));
		}

		$definition = $word->definition;

		$words = array();
		foreach ($definition->words AS $def_word) {
			$words[] = $def_word->word;
		}

		return $this->createJsonResponse(array(
			'id' => $word['id'],
			'definition_id' => $definition['id'],
			'words' => $words,
			'definition' => $definition['definition']
		));
	}

	public function tipAction($word)
	{
		$def = '';

		try {
			$word = $this->em->getRepository('DeskPRO:GlossaryWord')->findOneByWord($word);
			$def = $word->definition->definition;
		} catch (\Exception $e) {
			$def = '';
		}

		return $this->createResponse($def);
	}
}

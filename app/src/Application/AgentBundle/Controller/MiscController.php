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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\AgentBundle\FragmentRouter;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;
use Symfony\Component\HttpFoundation\Response;

class MiscController extends AbstractController
{
	public function requireRequestToken($action, $arguments = null)
	{
		if ($action == 'getInterfaceDataAction') {
			return false;
		}

		return parent::requireRequestToken($action, $arguments);
	}

	public function getInterfaceDataAction()
	{
		$js = array();

		// Common names
		$js[] = 'window.DESKPRO_NAME_REGISTRY = {};';
		$js[] = 'window.DESKPRO_NAME_REGISTRY.agent = ' . json_encode($this->container->getDataService('Person')->getAgentNames()) . ';';
		if ($this->container->getSetting('core.use_agent_team')) {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.agent_team = ' . json_encode($this->container->getDataService('AgentTeam')->getTeamNames()) . ';';
		} else {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.agent_team = {};';
		}
		$js[] = 'window.DESKPRO_NAME_REGISTRY.department = ' . json_encode($this->container->getDataService('Department')->getNames(null, true)) . ';';
		$js[] = 'window.DESKPRO_NAME_REGISTRY.department_full = ' . json_encode($this->container->getDataService('Department')->getFullNames(null, true)) . ';';
		$js[] = 'window.DESKPRO_NAME_REGISTRY.department_hierarchy = ' . json_encode($this->container->getDataService('Department')->getInHierarchy(null, true)) . ';';
		if ($this->container->getSetting('core.use_product')) {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.product = ' . json_encode($this->container->getDataService('Product')->getNames()) . ';';
		} else {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.product = {};';
		}
		if ($this->container->getSetting('core.use_ticket_category')) {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_category = ' . json_encode($this->container->getDataService('TicketCategory')->getNames()) . ';';
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_category_full = ' . json_encode($this->container->getDataService('TicketCategory')->getFullNames(null, true)) . ';';
		} else {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_category = {};';
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_category_full = {};';
		}
		if ($this->container->getSetting('core.use_ticket_category')) {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_priority = ' . json_encode($this->container->getDataService('TicketPriority')->getNames()) . ';';
		} else {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_priority = {};';
		}
		if ($this->container->getSetting('core.use_ticket_workflow')) {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_workflow = ' . json_encode($this->container->getDataService('TicketWorkflow')->getNames()) . ';';
		} else {
			$js[] = 'window.DESKPRO_NAME_REGISTRY.ticket_workflow = {};';
		}
		$js[] = 'window.DESKPRO_NAME_REGISTRY.language = ' . json_encode($this->container->getDataService('Language')->getTitles()) . ';';

		$lang_data = array();
		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$lang_data[$lang->id] = array(
				'id'         => $lang->id,
				'title'      => $this->container->getTranslator()->getPhraseObject($lang),
				'title_real' => $lang->title,
				'locale'     => $lang->locale
			);
		}
		$js[] = 'window.DESKPRO_NAME_REGISTRY.lang_data = ' . json_encode($lang_data) . ';';

		$js[] = 'window.DESKPRO_NAME_REGISTRY.language = ' . json_encode($this->container->getDataService('Language')->getTitles()) . ';';

		$js[] = 'window.DESKPRO_NAME_REGISTRY.status = ' . json_encode(array(
			'awaiting_agent' => App::getTranslator()->phrase('agent.tickets.status_awaiting_agent'),
			'awaiting_user' => App::getTranslator()->phrase('agent.tickets.status_awaiting_user'),
			'hidden' => App::getTranslator()->phrase('agent.tickets.status_hidden'),
			'resolved' => App::getTranslator()->phrase('agent.tickets.status_resolved'),
			'closed' => App::getTranslator()->phrase('agent.tickets.status_closed'),
		)) . ';';
		$js[] = 'window.DESKPRO_NAME_REGISTRY.hidden_status = ' . json_encode(array(
			'deleted' => App::getTranslator()->phrase('agent.tickets.hidden_status_deleted'),
			'spam' => App::getTranslator()->phrase('agent.tickets.hidden_status_spam'),
			'validating' => App::getTranslator()->phrase('agent.tickets.hidden_status_validating'),
		)) . ';';

		// Common URLs
		$js[] = 'window.DESKPRO_URL_REGISTRY = {};';
		$js[] = 'window.DESKPRO_URL_REGISTRY.serve_person_picture = ' . json_encode(str_replace(
			array('000'),
			array('$person_id'),
			$this->generateUrl('serve_person_picture_size', array('person_id' => '000'))
		)) . ';';
		$js[] = 'window.DESKPRO_URL_REGISTRY.serve_person_picture_size = ' . json_encode(str_replace(
			array('000', '111'),
			array('{person_id}', '{size}'),
			$this->generateUrl('serve_person_picture_size', array('person_id' => '000', 'size' => '111'))
		)) . ';';

		// Data
		$js[] = 'window.DESKPRO_DATA_REGISTRY = {}';

		$system_filters = $this->db->fetchAllKeyValue("SELECT id, sys_name FROM ticket_filters WHERE is_global=1 AND sys_name IS NOT NULL");
		$system_filters = Arrays::castToType($system_filters, 'string', 'int');
		$js[] = 'window.DESKPRO_DATA_REGISTRY.systemFilters = ' . json_encode($system_filters) . ';';

		// Ticket display elements
		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('create');
		$ticket_display->addPagesFromDb();
		$js[] = "window.DESKPRO_TICKET_DISPLAY = {}";
		$js[] = "window.DESKPRO_TICKET_DISPLAY.create = " . $ticket_display->compileJs() . ";";

		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('modify');
		$ticket_display->addPagesFromDb();
		$js[] = "window.DESKPRO_TICKET_DISPLAY.modify = " . $ticket_display->compileJs() . ";";

		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('view');
		$ticket_display->addPagesFromDb();
		$js[] = "window.DESKPRO_TICKET_DISPLAY.view = " . $ticket_display->compileJs() . ";";

		// Snippet short codes
		$ticket_snippets = $this->em->getRepository('DeskPRO:TextSnippet')->getSnippetsForAgent('tickets', $this->person);
		$snippet_short_codes = array();
		foreach ($ticket_snippets as $snippet_cat) {
			if ($snippet_cat['snippets']) {
				foreach ($snippet_cat['snippets'] as $snippet) {
					if ($snippet->shortcut_code) {
						$snippet_short_codes[$snippet->shortcut_code] = $snippet->id;
					}
				}
			}
		}

		if ($snippet_short_codes) {
			$js[] = "window.DESKPRO_TICKET_SNIPPET_SHORTCODES = " . json_encode($snippet_short_codes) . ";";
		} else {
			$js[] = "window.DESKPRO_TICKET_SNIPPET_SHORTCODES = {};";
		}

		// Snippet short codes
		$text_snippets = $this->em->getRepository('DeskPRO:TextSnippet')->getSnippetsForAgent('chat', $this->person);
		$snippet_short_codes = array();
		foreach ($text_snippets as $snippet_cat) {
			if ($snippet_cat['snippets']) {
				foreach ($snippet_cat['snippets'] as $snippet) {
					if ($snippet->shortcut_code) {
						$snippet_short_codes[$snippet->shortcut_code] = $snippet->id;
					}
				}
			}
		}

		if ($snippet_short_codes) {
			$js[] = "window.DESKPRO_CHAT_SNIPPET_SHORTCODES = " . json_encode($snippet_short_codes) . ";";
		} else {
			$js[] = "window.DESKPRO_CHAT_SNIPPET_SHORTCODES = {};";
		}

		// Chat display elements
		$chat_display = new \Application\DeskPRO\PageDisplay\Page\ChatPageZoneCollection('create');
		$chat_display->addPagesFromDb();
		$js[] = "window.DESKPRO_CHAT_DISPLAY = {}";
		$js[] = "window.DESKPRO_CHAT_DISPLAY.create = " . $chat_display->compileJs() . ";";

		$js[] = "window.DESKPRO_TICKET_PRI_MAP = " . json_encode($this->container->getDataService('TicketPriority')->getIdToPriorityMap()) . ';';

		$fragment_router = new FragmentRouter($this->get('router')->getGenerator());
		$js[] = $fragment_router->compile();

		$js[] = "window.DESKPRO_DATA_REGISTRY.labels = " . json_encode($this->em->getRepository('DeskPRO:LabelDef')->getAllLabelsToTyped());

		if ($this->plugins->isPluginInstalled('MicrosoftTranslator')) {
			$lang_codes = $this->plugins->getPluginService('MicrosoftTranslator.tr_api')->getLanguagesForTranslate();
			try {
				$lang_names = $this->plugins->getPluginService('MicrosoftTranslator.tr_api')->getLanguageNames(
					$this->plugins->getPluginService('MicrosoftTranslator.tr_api')->getLanguagesForTranslate(),
					$this->person->getLanguage()->getLocale()
				);
			} catch (\Exception $e) {
				$lang_names = $this->plugins->getPluginService('MicrosoftTranslator.tr_api')->getLanguageNames(
					$this->plugins->getPluginService('MicrosoftTranslator.tr_api')->getLanguagesForTranslate(),
					'en'
				);
			}

			$info = array(
				'lang_codes' => $lang_codes,
				'lang_names' => $lang_names,
				'translate_ticket_message_url' => $this->generateUrl('agent_plugins_run', array('plugin_id' => 'MicrosoftTranslator', 'action' => 'translate-ticket-message')),
				'translate_text_url'           => $this->generateUrl('agent_plugins_run', array('plugin_id' => 'MicrosoftTranslator', 'action' => 'translate-text')),
			);

			$js[] = "window.DESKPRO_TRANSLATE_SERVICE = " . json_encode($info) . ";";
		}

		$tr = $this->container->getTranslator();

		$js[] = <<<JS
function Orb_Util_TimeAgo_getPhraseFor(type, num, ago) {

	var phrasepre = 'reltime';
	if (ago) {
		phrasepre = 'reltimeago';
	}

	if ((type == 'secs' || type == 'sec') && num < 60) {
		var phrasename = 'agent.time.' + phrasepre + '_less_minute';
	} else {
		if (type == 'min') type = 'minute';
		else if (type == 'mins') type = 'minutes';
		else if (type == 'sec') type = 'second';
		else if (type == 'secs') type = 'seconds';

		var phrasename = 'agent.time.' + phrasepre + '_x_' + type;
		if (num == 1) {
			var phrasename = 'agent.time.' + phrasepre + '_1_' + type;
		}
		if (type == 'sec' && num <= 0) {
			var phrasename = 'agent.time.' + phrasepre + '_less_second';
		}
	}

	if (!window.DESKPRO_LANG || !window.DESKPRO_LANG[phrasename]) {
		console.warn("Missing phrase %s", phrasename);
	}

	return (window.DESKPRO_LANG && window.DESKPRO_LANG[phrasename] || "").replace(/\{0\}/g, num);
}
JS;

		$js = implode("\n", $js);

		$response = $this->response;
		$response->headers->set('Content-Type', 'application/javascript');
		$response->setContent($js);

		return $response;
	}

	public function ajaxSavePrefsAction()
	{
		$prefs_expire = $this->in->getCleanValueArray('prefs_expire', 'raw', 'string');

		foreach ($this->in->getCleanValueArray('prefs', 'raw', 'string') as $pref_name => $value)
		{
			$pref        = new Entity\PersonPref();
			$pref->name  = $pref_name;
			$pref->value = $value;

			if (isset($prefs_expire[$pref_name])) {
				try {
					$date = new \DateTime($prefs_expire[$pref_name]);
					$pref->date_expire = $date;
				} catch (\Exception $e) {}
			}

			App::getDb()->replace('people_prefs', array(
				'person_id'   => $this->person->getId(),
				'name'        => $pref_name,
				'date_expire' => $pref->date_expire ? $pref->date_expire->format('Y-m-d H:i:s') : null,
				'value_str'   => $pref->value_str,
				'value_array' => $pref->value_array ? serialize($pref->value_array) : null,
			));
		}

		return $this->createJsonResponse(array(
			'success' => true
		));
	}

	public function proxyAction()
	{
		$url = $this->in->getString('url');
		$urlinfo = @parse_url($url);
		if (!$url OR !$urlinfo OR empty($urlinfo['scheme']) OR !preg_match('#^https?#', $urlinfo['scheme'])) {
			return $this->createResponse('Bad url', 400);
		}

		$originalMethod = $this->request->getMethod();
		$method = $originalMethod;
		if ($originalMethod == 'GET' || $originalMethod == 'POST') {
			$newMethod = $this->in->getString('method');
			if ($newMethod) {
				$method = $newMethod;
			}

			if ($originalMethod == 'GET') {
				$passData = $_GET;
			} else {
				$passData = $_POST;
			}
			unset($passData['url'], $passData['method']);
		} else {
			$passData = file_get_contents('php://input');
		}

		switch (strtolower($method)) {
			case 'get': $method = 'GET'; break;
			case 'post': $method = 'POST'; break;
			case 'put': $method = 'PUT'; break;
			case 'delete': $method = 'DELETE'; break;
			default: $method = 'GET';
		}

		if ($method == 'GET' && is_array($passData) && $passData) {
			$url .= (strpos($url, '?') ? '&' : '?') . http_build_query($passData);
		}

		$ch = curl_init($url);
		if ($method != 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($passData) ? http_build_query($passData) : $passData);
		}

		if ($this->request->headers->get('X-DeskPRO-Proxy-Username') OR $this->request->headers->get('X-DeskPRO-Proxy-Password')) {
			curl_setopt($ch, CURLOPT_USERPWD, $this->request->headers->get('X-DeskPRO-Proxy-Username','').':'.$this->request->headers->get('X-DeskPRO-Proxy-Password',''));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}

		if (!empty($_SERVER['CONTENT_TYPE'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $_SERVER['CONTENT_TYPE']));
		}
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DeskPRO AJAX Proxy');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		$contents = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$response = $this->response;

		if ($info['content_type']) {
			$response->headers->set('Content-Type', $info['content_type']);
		}
		if ($info['http_code']) {
			$response->setStatusCode($info['http_code']);
		}

		$response->setContent($contents);

		return $response;
	}

	public function ajaxLabelsAutocompleteAction($label_type)
	{
		$search = $this->in->getString('term');
		$statement = $this->db->executeQuery("
			SELECT label
			FROM label_defs
			WHERE label_type = ? AND label LIKE ?
			ORDER BY label ASC
			LIMIT 50",
		array($label_type, '%'.$search.'%'));

		$array = array();

		while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
			$array[] = array('name' => $row['label'], 'value' => $row['label']);
		}

		return $this->createJsonResponse($array);
	}

	public function showBlobAction($blob_id)
	{
		$blob = $this->em->getRepository('DeskPRO:Blob')->find($blob_id);

		$response = $this->container->get('response');
		$response->headers->set('Content-Type', $blob['content_type'] . '; filename=' . $blob['filename']);
		$response->headers->set('Content-Length', $blob['filesize']);
		$response->headers->set('Content-Disposition', 'inline; filename=' . $blob['filename']);

		$file = $this->container->getBlobStorage()->copyBlobRecordToString($blob);
		$response->setContent($file);

		return $response;
	}

    public function acceptTempUploadAction()
    {
		$file = $this->request->files->get('file-upload');
		$accept = $this->container->getAttachmentAccepter();

		$error = $accept->getError($file, 'agent');
		if (!$error && $this->in->getBool('is_image')) {
			$set = new \Application\DeskPRO\Attachments\RestrictionSet();
			$set->setAllowedExts(array('gif', 'png', 'jpg', 'jpeg'));
			$accept->addRestrictionSet('only_images', $set);
			$error = $accept->getError($file, 'only_images');
		}
		if ($error) {
			$error['error'] = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
			return $this->createJsonResponse(array($error));
		}

		$blob = $accept->accept($file);

		if ($this->in->getString('attach_to_object')) {
			switch ($this->in->getString('attach_to_object')) {
				case 'article':
					$article = $this->em->find('DeskPRO:Article', $this->in->getUint('object_id'));

					$attach = new \Application\DeskPRO\Entity\ArticleAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $this->person;

					$article->addAttachment($attach);

					$this->em->persist($attach);
					$this->em->persist($article);
					$this->em->flush();

					break;

				case 'feedback':
					$feedback = $this->em->find('DeskPRO:Feedback', $this->in->getUint('object_id'));

					$attach = new \Application\DeskPRO\Entity\FeedbackAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $this->person;

					$feedback->addAttachment($attach);
					$this->em->persist($attach);
					$this->em->persist($feedback);
					$this->em->flush();

					break;
			}
		}

		if ($this->in->getBool('save_media')) {
			$blob->is_media_upload = true;
			$this->em->persist($blob);
			$this->em->flush();
		}

		$res = $this->createJsonResponse(array(array(
			'blob_id'           => $blob['id'],
			'blob_auth'         => $blob->authcode,
			'blob_auth_id'      => $blob->id . '-' . $blob->authcode,
			'download_url'      => $blob->getDownloadUrl(true, false),
			'filename'          => $blob['filename'],
			'filesize_readable' => $blob->getReadableFilesize(),
			'is_image'          => $blob->isImage()
		)));

		// Required for iframe transport on IE to prevent 'download' popup
		$res->headers->set('Content-Type', 'text/plain');
		return $res;
	}

	public function acceptRedactorImageUploadAction()
	{
		/** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
		$file = $this->request->files->get('file');
		$accept = $this->container->getAttachmentAccepter();

		$filename = $this->in->getString('filename');
		if ($filename) {
			// override filename
			$file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
				$file->getPathname(), $filename, $file->getClientMimeType(), $file->getClientSize(), $file->getError()
			);
		}

		$error = $accept->getError($file, 'agent');
		if (!$error) {
			$set = new \Application\DeskPRO\Attachments\RestrictionSet();
			$set->setAllowedExts(array('gif', 'png', 'jpg', 'jpeg'));
			$accept->addRestrictionSet('only_images', $set);
			$error = $accept->getError($file, 'only_images');
		}
		if ($error) {
			$error['error'] = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);

			$res = $this->createJsonResponse($error);
		} else {
			$blob = $accept->accept($file);

			$res = $this->createJsonResponse(array(
				'blob_id'           => $blob['id'],
				'blob_auth'         => $blob->authcode,
				'blob_auth_id'      => $blob->id . '-' . $blob->authcode,
				'download_url'      => $blob->getDownloadUrl(true),
				'filename'          => $blob['filename'],
				'filesize_readable' => $blob->getReadableFilesize(),
				'is_image'          => $blob->isImage(),

				// needed for Redactor
				'filelink'     => $blob->getDownloadUrl(true)
			));
		}

		return $res;
	}

	public function redactorAutosaveAction($content_type, $content_id)
	{
		$inserted = false;

		$message = $this->in->getString('message');
		$extras = $this->in->getCleanValueArray('extras');
		$draft = null;
		if ($message) {
			$message_html = Strings::trimHtml($this->in->getHtmlCore('message'));
			$message_html = Strings::prepareWysiwygHtml($message_html);

			$message_test = preg_replace('/<(p|div) class="dp-signature-start">(.*)$/s', '', $message_html);
			$message_test = Strings::trimHtml($message_test);
			if ($message_test && !Strings::compareHtml($message_test, $this->person->getSignatureHtml())) {
				$draft = $this->em->getRepository('DeskPRO:Draft')->insertDraft(
					$content_type, $content_id, $message, $message_html, $extras
				);
				if ($draft) {
					$inserted = $draft->id;
				}
			}
		}

		if (!$inserted) {
			$this->em->getRepository('DeskPRO:Draft')->deleteDraft($content_type, $content_id);
		}

		if ($inserted && $content_type == 'ticket') {
			$html = false;
			if ($draft) {
				$ticket = $this->em->getRepository('DeskPRO:Ticket')->find($content_id);
				if ($ticket) {
					$html = $this->renderView('AgentBundle:Ticket:ticket-message-draft.html.twig', array(
						'draft' => $draft,
						'ticket' => $ticket
					));
				}
			}

			App::getDb()->insert('client_messages', array(
				'channel' => 'agent.ticket-draft-updated',
				'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
				'date_created' => date('Y-m-d H:i:s'),
				'data' => serialize(array(
					'ticket_id'      => $content_id,
					'draft_html'     => $html,
					'via_person'     => $this->person->id
				)),
				'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
			));
		}

		return $this->createJsonResponse(array(
			'inserted' => $inserted
		));
	}

    public function parseVCardAction()
    {
        $file = $this->request->files->get('files');

        $content = file_get_contents($file[0]->getPathName());
        $parse = \File_IMC::parse('vCard');
        $vcard = $parse->fromText($content);
        $fields = array();

        if(isset($vcard['VCARD'])) {
            foreach($vcard['VCARD'] as $vc) {

                if(isset($vc['EMAIL'])
                && isset($vc['EMAIL'][0]['value'])) {
                    $fields['email'] = $vc['EMAIL'][0]['value'][0][0];
                }

                if(isset($vc['FN'])
                && isset($vc['FN'][0]['value'])) {
                    $fields['name'] = $vc['FN'][0]['value'][0][0];
                }
            }
        }

        $res = $this->createJsonResponse(array(array('fields' => $fields)));

        // Required for iframe transport on IE to prevent 'download' popup
        $res->headers->set('Content-Type', 'text/plain');
        return $res;
    }

	/**
	 * @param  $id
	 * @return void
	 */
	public function dismissHelpMessageAction($id)
	{
		$this->person->HelpMessages->dismiss($id);

		$this->createJsonResponse(array('success' => true));
	}

	/**
	 * Set away status
	 * @param  $status
	 */
	public function setAgentStatusAction($status)
	{
		if (!$status OR $status == 'away') {
			$status = 'away';
		} else {
			$status = 'available';
		}

		$sessionEnt = $this->session->getEntity();
		$sessionEnt['active_status'] = $status;
		$sessionEnt['is_chat_available'] = $this->in->getBool('is_chat_available');

		$this->session->set('is_chat_available', $this->in->getBool('is_chat_available'));
		$this->session->set('active_status', $status);
		$this->session->save();

		// Update status in all other active sessions
		$is_chat_avail = (int)$this->in->getBool('is_chat_available');
		$is_chat_avail_old = (int)(!$this->in->getBool('is_chat_available'));

		// using REPLACE on the session data as a quick way to toggle the status in session data
		// without actually loading up the entire record
		$this->db->executeUpdate("
			UPDATE sessions
			SET is_chat_available = ?, active_status = ?, data = REPLACE(data, '\"is_chat_available\";i:$is_chat_avail_old;', '\"is_chat_available\";i:$is_chat_avail;')
			WHERE person_id = ? AND interface = ?
		", array(
			$this->in->getBool('is_chat_available'),
			$status,
			$this->person->getId(),
			'agent')
		);

		$this->em->transactional(function($em) use ($sessionEnt) {
			$em->persist($sessionEnt);
			$em->flush();
		});

		\Application\DeskPRO\Chat\UserChat\AvailableTrigger::update();

		// Also send our status
		//agent.ui.user-chat-status
		$cm = new Entity\ClientMessage();
		$cm->channel = 'agent.ui.user-chat-status';
		$cm->for_person = $this->person;
		$cm->data = array('is_online' => $this->in->getBool('is_chat_available'));
		$this->em->persist($cm);
		$this->em->flush();


		return $this->createJsonResponse(array('success' =>true, 'status' => $status));
	}

	public function redirectExternalAction($url)
	{
		if (!$this->container->getSetting('core.agent_intercept_external_link')) {
			$res = new \Symfony\Component\HttpFoundation\RedirectResponse($url, 302);
			return $res;
		}

		$urlinfo = parse_url($url);

		return $this->render('AgentBundle:Misc:redirect-external.html.twig', array(
			'url' => $url,
			'urlinfo' => $urlinfo
		));
	}

	public function redirectExternalInfoAction($url)
	{
		$urlinfo = parse_url($url);

		$page = @file_get_contents($url);
		$info = array();

		$info['title'] = Strings::extractRegexMatch('#<title>(.*?)</title>#im', $page, 1);
		$info['ip'] = gethostbyname($urlinfo['host']);
		$info['hostname'] = gethostbyname($info['ip']);
		$info['size'] = strlen($page);
		$info['size_readable'] = Numbers::filesizeDisplay($info['size']);

		$info['num_images'] = substr_count($page, '<img');
		$info['num_scripts'] = substr_count($page, '<script');

		return $this->render('AgentBundle:Misc:redirect-external-info.html.twig', array(
			'url' => $url,
			'urlinfo' => $urlinfo,
			'info' => $info,
		));
	}

	public function getPasswordConfirmCodeAction()
	{
		$password = $this->in->getString('password');

		$invalid_res = $this->createJsonResponse(array('invalid' => true));

		$code = $this->session->getEntity()->generateSecurityToken('password_confirm' . $this->person->secret_string);
		$valid_res = $this->createJsonResponse(array('code' => $code));

		#------------------------------
		# Auth local
		#------------------------------

		$adapter = new \Application\DeskPRO\Auth\Adapter\Local(App::getOrm());
		$adapter->setCredentials($this->person->getPrimaryEmailAddress(), $password);
		$result = $adapter->authenticate();

		if ($result->isValid()) {
			return $valid_res;
		}

		#------------------------------
		# Auth usersources that accept local input
		#------------------------------

		$usersources = $this->em->getRepository('DeskPRO:Usersource')->getLocalInputUsersources();
		foreach ($usersources as $us) {
			foreach ($this->person->getEmailAddresses() as $email) {
				/** @var $us \Application\DeskPRO\Entity\Usersource */
				$adapter = $this->_initUserSourceAdapter($us);
				$adapter->setFormData(array(
					'username' => $email,
					'password' => $password
				));

				try {
					$result = $adapter->authenticate();
				} catch (\Exception $e) {
					continue;
				}

				if ($result->isValid()) {
					return $valid_res;
				}
			}
		}

		return $invalid_res;
	}

	protected function _initUserSourceAdapter($usersource, $context = null)
	{
		$adapter = $usersource->getAdapter()->getAuthAdapter();

		if ($adapter instanceof \Orb\Auth\Adapter\FormLoginInterface) {
			$adapter->setFormData($_POST);
		}

		if ($context && $adapter instanceof \Orb\Auth\Adapter\DisplayContextInterface) {
			$adapter->setDisplayContext($context);
		}

		if ($adapter instanceof \Orb\Auth\Adapter\CallbackInterface) {
			$adapter->setCallbackUrl(
				rtrim($this->container->getSetting('core.deskpro_url'), '/') .
				$this->generateUrl('user_login_callback', array('usersource_id' => $usersource['id']), false)
			);
		}

		if ($adapter instanceof \Orb\Auth\Adapter\SessionStateInterface) {
			$auth_state = new \Orb\Auth\StateHandler\ArrayAccessWrapper($this->session);
			$auth_state->setClearStateMethod('clear');

			$adapter->setStateHandler($auth_state);
		}

		return $adapter;
	}

	public function submitDeskproFeedbackAction()
	{
		\Application\DeskPRO\Service\ErrorReporter::sendFeedback($this->person, $this->in->getString('message'), $this->in->getString('email_address'));
		return $this->createJsonResponse(array('success' => true));
	}

	public function getServerTimeAction()
	{
		$d = \Orb\Util\Dates::makeUtcDateTime($this->person->getDateTime());

		return $this->createJsonResponse(array(
			'timestamp_utc' => time(),
			'timestamp' => $d->getTimestamp(),
			'time_formatted' => $d->format('g:i a'),
			'time_hour' => (int)$d->format('H'),
			'time_minute' => (int)$d->format('i'),
		));
	}

	public function saveDomAction()
	{
		$dom = $this->in->getRaw('html');
		file_put_contents(dp_get_data_dir() . '/dom.html', $dom);
		return $this->createJsonResponse(array('okay' => true));
	}
}

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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * Handles creating/editing of widgets
 */
class WidgetsController extends AbstractController
{
	public function indexAction()
	{
		$repository = $this->_getWidgetRepository();

		return $this->render('AdminBundle:Widgets:index.html.twig', array(
			'widgetsGrouped' => $repository->getPageGroupedWidgets(),
			'pages' => $repository->getPages()
		));
	}

	public function toggleAction()
	{
		$this->ensureRequestToken();

		$updates = $this->in->getArray('widgets');
		$widgets = $this->_getWidgetRepository()->getByIds(array_keys($updates));

		$this->em->beginTransaction();

		foreach ($widgets AS $widget) {
			if (isset($updates[$widget->id])) {
				$widget->enabled = (bool)$updates[$widget->id];
				$this->em->persist($widget);
			}
		}

		try {
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array(
			'ok' => 1
		));
	}

	public function deleteAction($widget_id)
	{
		$widget = $this->_getWidgetOr404($widget_id);

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$this->em->beginTransaction();

			try {
				$this->em->remove($widget);
				$this->em->flush();
				$this->em->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}

			return $this->redirectRoute('admin_widgets');
		}

		return $this->render('AdminBundle:Widgets:delete.html.twig', array(
			'widget' => $widget
		));
	}

	public function editAction($widget_id)
	{
		if ($widget_id) {
			$widget = $this->_getWidgetOr404($widget_id);
			$widgetType = ($widget->page_location ? 'full' : 'js');
		} else {
			$widget = new Entity\Widget();
			$widgetType = 'full';
		}

		$errors = array();

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$widgetType = $this->in->getString('widget_type');

			$description = $this->in->getString('description');
			$title = $this->in->getString('title');
			$page = $this->in->getString('page');

			if ($widgetType == 'js') {
				$insertPosition = '';
				$location = '';
				$html = '';
			} else {
				$insertPosition = $this->in->getString('insert_position');
				$location = $this->in->getString('page_location');
				$html = $this->in->getStrRaw('html');
			}

			$widget->description = $description;
			$widget->title = $title;
			$widget->html = $html;
			$widget->js = $this->in->getStrRaw('js');
			$widget->css = $this->in->getStrRaw('css');
			$widget->page = $page;
			$widget->page_location = $location;
			$widget->insert_position = $insertPosition;

			if ($this->_getWidgetRepository()->canEditWidgetPlugin()) {
				$pluginId = $this->in->getString('plugin_id');
				$uniqueKey = $this->in->getString('unique_key');

				$widget->plugin = ($pluginId ? $this->em->getRepository('DeskPRO:Plugin')->findOneById($pluginId) : null);
				$widget->unique_key = ($uniqueKey === '' ? null : $uniqueKey);

				if ($widget->plugin && $widget->unique_key === null) {
					$errors['unique_key'] = 'Please enter a unique key.';
				} else if (!$widget->plugin && $widget->unique_key !== null) {
					$errors['unique_key'] = 'Unique keys may only be specified when a plugin is selected.';
				}
			}

			if (!$description) {
				$errors['description'] = 'Please enter a description.';
			}

			if ($widgetType == 'js') {
				if (!$page) {
					$errors['page'] = 'Please enter a location.';
				}
			} else {
				if (!$title) {
					$errors['title'] = 'Please enter a block title.';
				}
				if (!$page || !$insertPosition || !$location) {
					$errors['page'] = 'Please enter a complete location.';
				}
			}

			if (!$errors) {
				$this->em->beginTransaction();

				try {
					$this->em->persist($widget);
					$this->em->flush();
					$this->em->commit();
				} catch (\Exception $e) {
					$this->em->rollback();
					throw $e;
				}

				return $this->redirectRoute('admin_widgets');
			}
		}

		$repository = $this->_getWidgetRepository();
		$plugins = $this->em->getRepository('DeskPRO:Plugin')->getInstalled();

		return $this->render('AdminBundle:Widgets:edit.html.twig', array(
			'widget' => $widget,
			'widgetType' => $widgetType,
			'errors' => $errors,
			'pages' => $repository->getPages(),
			'locations' => $repository->getPageLocations(),
			'canEditPlugin' => $repository->canEditWidgetPlugin(),
			'plugins' => $plugins
		));
	}



	############################################################################

	/**
	 * @param integer $id
	 *
	 * @return \Application\DeskPRO\Entity\Widget
	 */
	protected function _getWidgetOr404($id)
	{
		$data = $this->em->getRepository('DeskPRO:Widget')->find($id);
		if (!$data) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no widget with ID $id");
		}

		return $data;
	}

	/**
	 * @return \Application\DeskPRO\EntityRepository\Widget
	 */
	protected function _getWidgetRepository()
	{
		return $this->em->getRepository('DeskPRO:Widget');
	}
}

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
 * @subpackage Form
 */

namespace Application\DeskPRO\CustomFields\Handler;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Orb\Util\Util;

/**
 * A custom field handler knows how to render an HTML form field as well as
 * a render a readable value.
 */
abstract class HandlerAbstract
{
	const CONTEXT_HTML = 'html';
	const CONTEXT_TEXT = 'text';

	const CONTEXT_USER  = 'user';
	const CONTEXT_AGENT = 'agent';

	/**
	 * @var \Symfony\Component\Templating\EngineInterface
	 */
	protected $tpl = null;

	/**
	 * The form field definition
	 * @var \Application\DeskPRO\Entity\CustomDefAbstract
	 */
	protected $field_def;

	/**
	 * @var \\Application\DeskPRO\Entity\CustomDefAbstract[]
	 */
	protected $field_children;

	public function __construct(Entity\CustomDefAbstract $field_def = null)
	{
		$this->field_def = $field_def;
		$this->init();
	}

	public function init()
	{

	}

	/**
	 * @return \Application\DeskPRO\Entity\CustomDefAbstract[]
	 */
	public function getFieldChildren()
	{
		if ($this->field_children !== null) {
			return $this->field_children;
		}

		if ($this->field_def->field_manager) {
			$children = $this->field_def->field_manager->getFieldChildren($this->field_def);
		} else {
			$children = $this->field_def['children'];
		}

		// Index array
		$children = \Orb\Util\Arrays::keyFromData($children, 'id');

		uasort($children, function($a, $b) {
			if ($a->getDisplayOrder() == $b->getDisplayOrder()) {
				return 0;
			}

			return $a->getDisplayOrder() < $b->getDisplayOrder() ? -1 : 1;
		});

		$this->field_children = $children;

		return $this->field_children;
	}


	/**
	 * Get the templating engine
	 *
	 * @return \Symfony\Component\Templating\EngineInterface
	 */
	public function getTemplateEngine()
	{
		if ($this->tpl === null) {
			$this->tpl = App::getTemplating();
		}

		return $this->tpl;
	}


	/**
	 * Set the templating engine to use
	 *
	 * @param \Symfony\Component\Templating\EngineInterface $tpl
	 */
	public function setTemplateEngine($tpl)
	{
		$this->tpl = $tpl;
	}


	/**
	 * Get the standard name/ID for this element in an HTML form.
	 *
	 * @return string
	 */
	public function getFormFieldName()
	{
		return 'field_' . $this->field_def['id'];
	}


	/**
	 * @param  $context
	 * @return string
	 */
	public function getRenderTemplateName($context = 'html')
	{
		$templating = $this->getTemplateEngine();
		$tpl = null;
		if ($this->field_def['has_display_template']) {
			$tpl = 'DeskPRO:' . $this->field_def->getTableName() . ':rendered-field_' . $this->field_def['id'];
			if ($context == 'html') {
				$tpl .= '.html.twig';
			} else {
				$tpl .= '.txt.twig';
			}

			if (!$templating->exists($tpl)) {
				$tpl = null;
			}
		}

		if (!$tpl) {
			$tpl = $this->getDefaultRenderTemplateName($context);
		}

		return $tpl;
	}

	/**
	 * @param  $context
	 * @return string
	 */
	public function getFormTemplateName()
	{
		$templating = $this->getTemplateEngine();
		$tpl = null;
		if ($this->field_def['has_form_template']) {
			$tpl = 'DeskPRO:' . $this->field_def->getTableName() . ':form-field_' . $this->field_def['id'] . '.html.twig';
			if (!$templating->exists($tpl)) {
				$tpl = null;
			}
		}

		if (!$tpl) {
			$tpl = $this->getDefaultFormTemplateName();
		}

		return $tpl;
	}


	/**
	 * Get the default template name (minus suffix that defines format).
	 *
	 * @return string
	 */
	public function getDefaultRenderTemplateName($context = 'html')
	{
		$tpl = 'DeskPRO:custom_fields:rendered-value';
		if ($context == 'html') {
			$tpl .= '.html.twig';
		} else {
			$tpl .= '.txt.twig';
		}

		return $tpl;
	}



	/**
	 * Get the default template name (minus suffix that defines format).
	 *
	 * @return string
	 */
	public function getDefaultFormTemplateName()
	{
		return 'DeskPRO:custom_fields:form-input.html.twig';
	}


	/**
	 * Get additional template vars to set
	 *
	 * @var array
	 */
	public function getRenderTemplateVars($context = 'html')
	{
		return array();
	}



	/**
	 * Render the field to HTML for use in a web page.
	 */
	public function renderHtml($data = null, array $template_vars = array())
	{
		if ($data === null) $data = array();

		$templating = $this->getTemplateEngine();

		$vars = array_merge($this->getRenderTemplateVars('html'), $template_vars, array(
			'data'          => $data,
			'field_def'     => $this->field_def,
			'field_handler' => $this,
			'field_handler_name' => Util::getBaseClassname($this),
			'field_type'    => $this->field_def->getTableName()
		));

		return $templating->render($this->getRenderTemplateName('html'), $vars);
	}



	/**
	 * Render the field
	 */
	public function renderText($data = null, array $template_vars = array())
	{
		if ($data === null) $data = array();

		$templating = $this->getTemplateEngine();

		$vars = array_merge($this->getRenderTemplateVars('text'), $template_vars, array(
			'data'          => $data,
			'field_def'     => $this->field_def,
			'field_handler' => $this,
			'field_handler_name' => Util::getBaseClassname($this),
			'field_type'    => $this->field_def->getTableName()
		));

		return $templating->render($this->getRenderTemplateName('text'), $vars);
	}

	/**
	 * Render the HTML form input
	 *
	 * @param  $formView
	 * @param array $template_vars
	 * @return string
	 */
	public function renderFormHtml($formView, array $template_vars = array())
	{
		$templating = $this->getTemplateEngine();

		if (!empty($template_vars['field_group'])) {
			$field_group = App::get('form.factory')->createNamedBuilder('form', $template_vars['field_group']);
			$field_group->add($this->getFormField());
			$form = $field_group->getForm();
			$groupView = $form->createView();
			$formView = $groupView[$this->getFormFieldName()];
		}

		$vars = array_merge($this->getRenderTemplateVars(), $template_vars, array(
			'formView'      => $formView,
			'field_def'     => $this->field_def,
			'field_handler' => $this,
			'field_handler_name' => Util::getBaseClassname($this),
			'field_type'    => $this->field_def->getTableName()
		));

		return $templating->render($this->getFormTemplateName(), $vars);
	}



	/**
	 * Render a field in a given context. This is just a strategy for calling other renderX
	 * methods.
	 *
	 * $data is a data structure `array(value=>..., children=>array(...))` as returned
	 * from `Application\DeskPRO\CustomFields\Util::createDataHierarchy()`
	 *
	 * @param string $context
	 * @param array $data
	 * @return mixed
	 */
	public function renderContext($context, $data)
	{
		switch ($context) {
			case self::CONTEXT_HTML:
				$method = 'renderHtml';
				break;

			case self::CONTEXT_TEXT:
				$method = 'renderText';
				break;

			default:
				throw new \InvalidArgumentException("Unknown context `$context`");
		}

		return $this->$method($data);
	}



	/**
	 * Get the form field
	 *
	 * @return Symfony\Component\Form\Field
	 */
	abstract public function getFormField($data = null);



	/**
	 * Get data from a posted form that we'll store in the database.
	 *
	 * This must return an array of array(field_id, type, value)
	 * If no value is set, then use null.
	 *
	 * @return array
	 */
	abstract public function getDataFromForm(array $form_data);


	/**
	 * Get an array of errors from a posted form.
	 *
	 * This must return a standard array of error codes (see Orb\Validator\ValidatorInterface).
	 * If an empty array is returned, then that means the field is valid.
	 *
	 * @param array $form_data
	 * @return array
	 */
	public function validateFormData(array $form_data, $context = self::CONTEXT_USER, $context_data = null)
	{
		return array();
	}


	/**
	 * @param array $codes
	 * @return array
	 */
	public function makeErrorArray(array $codes)
	{
		foreach ($codes as &$c) {
			$c = $this->getFormFieldName() . '.' . $c;
		}

		return $codes;
	}


	/**
	 * Gets an array of search operation types we can perform against this
	 * field.
	 *
	 * @return array
	 */
	public function getSearchCapabilities()
	{
		// Not searchable by default
		return array();
	}


	public function getFilterCapabilities()
	{
		return $this->getSearchCapabilities();
	}


	/**
	 * Get the type of search this field sholud be on.
	 *
	 * - Text/input have 'input'
	 * - Dates/numric have 'value'
	 * - Fields that use an option go by 'id'
	 *
	 * @return string
	 */
	public function getSearchType()
	{
		return 'input';
	}
}

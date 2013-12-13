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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Validator;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Validator\AbstractValidator;
use Application\DeskPRO\Form\Captcha\CaptchaAbstract;

class NewFeedbackValidator extends AbstractValidator
{
	/**
	 * @var \Application\DeskPRO\Feedback\NewFeedback
	 */
	protected $newfeedback;

	/**
	 * @var \Application\DeskPRO\Form\Captcha\CaptchaAbstract
	 */
	protected $captca;

	/**
	 * @param CaptchaAbstract $captcha
	 */
	public function setCaptcha(CaptchaAbstract $captcha)
	{
		$this->captca = $captcha;
	}

	/**
	 * Check $value to see if its valid.
	 *
	 * @param \Application\DeskPRO\Feedback\NewFeedback $newfeedback
	 * @return bool
	 */
	protected function checkIsValid($newfeedback)
	{
		$this->newfeedback = $newfeedback;

		$validator = new \Orb\Validator\StringLength(array('min' => 3));
		if (!$validator->isValid($this->newfeedback->title)) {
			$this->addError('title.short');
		}

		$validator = new \Orb\Validator\StringLength(array('min' => 5));
		if (!$validator->isValid($this->newfeedback->content)) {
			$this->addError('content.short');
		}

		$cat = App::getEntityRepository('DeskPRO:FeedbackCategory')->find($this->newfeedback->category_id);
		if (!$cat) {
			$this->addError('category_id.invalid');
		}

		$cf_man = App::getSystemService('FeedbackFieldsManager');
		$newfeedback_cat_field = $cf_man->getSystemField('cat');
		if (!$newfeedback_cat_field || !$cf_man->getFieldChildren($newfeedback_cat_field)) {
			$newfeedback_cat_field = null;
		}

		if ($newfeedback_cat_field) {
			// Not specified in the form
			if (!isset($newfeedback->custom_fields['field_' . $newfeedback_cat_field->getId()])) {
				$this->addError('usercat.invalid');

			// Specifid but may be invalid option
			} else {
				$children = $cf_man->getFieldChildren($newfeedback_cat_field);
				$selected_id = $newfeedback->custom_fields['field_' . $newfeedback_cat_field->getId()];

				if (!isset($children[$selected_id])) {
					$this->addError('usercat.invalid');
				}
			}
		}

		$person_context = $this->newfeedback->getPersonContext();
		if (!$person_context || $person_context->isGuest()) {
			$validator = new \Orb\Validator\StringEmail();
			if (!$validator->isValid($this->newfeedback->person_email)) {
				$this->addError('person_email.invalid');
			}

			$validator = new \Orb\Validator\StringLength(array('min' => 2));
			if (!$validator->isValid($this->newfeedback->person_name)) {
				$this->addError('person_name.short');
			}
		}

		if ($this->captca) {
			if (!$this->captca->validate()) {
				$this->addError('captcha.invalid');
			}
		}

		if ($this->errors) {
			return false;
		}

		return true;
	}
}

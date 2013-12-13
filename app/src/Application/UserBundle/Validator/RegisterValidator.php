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

class RegisterValidator extends AbstractValidator
{
	/**
	 * @var \Application\DeskPRO\Entity\CustomDefPerson[]
	 */
	protected $_custom_fields;

	/**
	 * @var \Application\DeskPRO\Form\Captcha\CaptchaAbstract
	 */
	protected $_captcha;

	/**
	 * @var \Application\UserBundle\Form\Model\Register
	 */
	protected $register;

	protected function checkIsValid($register)
	{
		$this->register = $register;

		if ($this->_captcha && !$this->_captcha->validate()) {
			$this->addError('captcha.invalid');
		}

		$validator = new \Orb\Validator\StringLength(array('min' => 2));
		if (!$validator->isValid($this->register->name)) {
			$this->addError('name.short');
		}

		if (!App::getSystemService('email_address_validator')->isValidUserEmail($this->register->email)) {
			$this->addError('email.invalid');
		} else {
			$check_exist = App::getDb()->fetchColumn("
				SELECT person_id
				FROM people_emails
				WHERE email = ?
			", array($this->register->email));
			if ($check_exist) {
				$this->addError('email.in_use');
			}
		}

		$validator = new \Orb\Validator\StringLength(array('min' => 5));
		if (!$validator->isValid($this->register->password)) {
			$this->addError('password.short');
		} elseif ($this->register->password != $this->register->password2) {
			$this->addError('password.mismatch');
		}

		if ($this->_custom_fields) {
			foreach ($this->_custom_fields as $field) {
				$errors = $field->getHandler()->validateFormData($this->register->custom_fields ?: array());
				foreach ($errors as $code) {
					$this->addError($code);
				}
			}
		}

		if ($this->errors) {
			return false;
		}

		return true;
	}

	public function setCustomFields(array $custom_fields)
	{
		$this->_custom_fields = $custom_fields;
	}

	/**
	 * @param CaptchaAbstract $captcha
	 */
	public function setCaptcha(CaptchaAbstract $captcha)
	{
		$this->_captcha = $captcha;
	}
}

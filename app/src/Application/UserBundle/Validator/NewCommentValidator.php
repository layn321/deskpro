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

use Application\DeskPRO\Form\Captcha\CaptchaAbstract;
use Application\DeskPRO\People\PersonContextInterface;
use Orb\Util\Arrays;
use Orb\Validator\AbstractValidator;

class NewCommentValidator extends AbstractValidator implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Comments\NewComment
	 */
	protected $newcomment;

	/**
	 * @var \Application\DeskPRO\Form\Captcha\CaptchaAbstract
	 */
	protected $captcha;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function setPersonContext(Entity\Person $person)
	{
		$this->person_context = $person;
	}

	public function init()
	{

	}

	/**
	 * @param CaptchaAbstract $captcha
	 */
	public function setCaptcha(CaptchaAbstract $captcha)
	{
		$this->captcha = $captcha;
	}

	/**
	 * Check $value to see if its valid.
	 *
	 * @param \Application\DeskPRO\Comments\NewComment $newfeedback
	 * @return bool
	 */
	protected function checkIsValid($newcomment)
	{
		if (!$this->captcha) {
			if (App::getSetting('user.publish_captcha')) {
				if ($this->person_context && (!$this->person_context->getId() || App::getSetting('user.always_show_captcha'))) {
					$this->captcha = App::getSystemObject('form_captcha', array('type' => 'new_comment'));
				}
			}
		}

		$this->newcomment = $newcomment;

		$validator = new \Orb\Validator\StringLength(array('min' => 3));
		if (!$validator->isValid($this->newcomment->content)) {
			$this->addError('content.short');
		}

		if (!$this->newcomment->getPersonContext() || !$this->newcomment->getPersonContext()->getId()) {
			$validator = new \Orb\Validator\StringLength(array('min' => 2));
			if (!$validator->isValid($this->newcomment->name)) {
				$this->addError('name.short');
			}

			if (!App::getSystemService('email_address_validator')->isValidUserEmail($this->newcomment->email)) {
				$this->addError('email.invalid');
			}
		}

		if ($this->captcha) {
			if (!$this->captcha->validate()) {
				$this->addError('captcha.invalid');
			}
		}

		if ($this->errors) {
			return false;
		}

		return true;
	}


	public function checkDupe($newcomment)
	{
		$this->newcomment = $newcomment;

		$content = $this->newcomment->content;
		$person  = $this->newcomment->getPersonContext();
		$name    = $this->newcomment->name;
		$email   = $this->newcomment->email;

		return App::getOrm()->getRepository($this->newcomment->getClass())->getDuplicate(
			$content,
			$person,
			$name,
			$email
		);
	}
}

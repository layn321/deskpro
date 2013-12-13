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
 * Orb
 *
 * @package Orb
 * @subpackage Validator
 */

namespace Orb\Validator;

use \Orb\Util\Util;
/**
 * A simple adapter that lets you use any ZF validator
 */
class ZendValidator extends AbstractValidator
{
	/**
	 * @var Zend\Validator\AbstractValidator
	 */
	protected $zend_validator;

	public function init()
	{
		$this->zend_validator = $this->getOption('zend_validator');
	}



	/**
	 * Create a new ZendValidator and automatically instantiate the Zend validator.
	 *
	 * <code>
	 * $v = ZendValidator::factory('Hostname');
	 * $v = ZendValidator::factory('Between', array('min' => 10, 'max' => 20'));
	 * $v = ZendValidator::factory('Alnum', true);
	 * </code>
	 *
	 * @param string $name     The Zend validator classname
	 * @param mixed  $param... Any parameters to pass to the validator constructor
	 * @return Orb\Validator\ZendValidator
	 */
	public static function factory($name)
	{
		$classname = $name;
		if (!class_exists($classname)) {
			$classname = 'Zend\\Validator\\'.$name;
			if (!class_exists($classname)) {
				throw new \InvalidArgumentException('Could not find any validator class named `'.$name.'`');
			}
		}
		if (!($classname instanceof \Zend\Validator\AbstractValidator)) {
			throw new \InvalidArgumentException('Invalid validator `'.$name.'`: It must be of type Zend\\Validator\\AbstractValidator');
		}

		$args = func_get_args();
		array_shift($args); // get rid of $name

		$zend_validator = Util::callUserConstructorArray($classname, $args);

		return new self(array('zend_validator' => $zend_validator));
	}



	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		if ($this->zend_validator->isValid($value)) {
			return true;
		}

		$this->errors = $this->zend_validator->getErrors();
		$this->errors_info = $this->zend_validator->getMessages();

		return false;
	}



	/**
	 * @return Zend\Validator\AbstractValidator
	 */
	public function getZendValidator()
	{
		return $this->zend_validator;
	}
}

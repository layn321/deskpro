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
 * Loader file for \Orb\Doctrine\ORM\Mapping\StaticReflectionProperty
 *
 * PHP's implementation of ReflectionProperty::getValue() is overloaded
 * (http://uk.php.net/manual/en/reflectionproperty.getvalue.php)
 * in such a way that it is imposisble to create a strict compliant version
 * in PHP when we want to override it.
 *
 * This loader just changes the error reporting to skip E_STRICT so it can
 * be included without this strict notice:
 * Declaration of Orb\Doctrine\ORM\Mapping\StaticReflectionProperty::getValue() should be compatible with that of ReflectionProperty::getValue()
 */

if (version_compare(PHP_VERSION, '5.4', '>=')) {
	$__olde = error_reporting(E_ALL ^ E_STRICT);
} else {
	$__olde = error_reporting(E_ALL);
}
require __DIR__.'/StaticReflectionProperty_Real.php';
error_reporting($__olde);
unset($__olde);

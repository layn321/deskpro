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

namespace Orb\Tests\Util;

use Orb\Util\Util;

class VariableConstructorTest
{
	public $arr;
	public function __construct($a = 0, $b = 0, $c = 0, $d = 0)
	{
		$this->arr = array($a, $b, $c, $d);
	}
}
class VariableConstructor2Test
{
	public $arr;
	public function __construct($a = 0, $b = 0, $c = 0, $d = 0, $e = 0, $f = 0, $g = 0, $h = 0, $i = 0, $j = 0, $k = 0, $l = 0, $m = 0)
	{
		$this->arr = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m);
	}
}

class UtilTest extends \PHPUnit_Framework_TestCase
{
	public function testBasic()
	{
		$this->assertEquals('integer', Util::typeof(123));
		$this->assertEquals('string', Util::typeof('foo'));
		$this->assertEquals('string', Util::typeof('123'));
		$this->assertEquals('array', Util::typeof(array()));
		$this->assertEquals('object:Orb\\Tests\\Util\\UtilTest', Util::typeof($this));

		$var = 123;
		$this->assertNull(Util::ifsetor($not_exist));
		$this->assertEquals(123, Util::ifsetor($var));

		$this->assertEquals('a', Util::ifvalor(false, 'a'));
		$this->assertEquals('a', Util::ifvalor('a', 'b'));
		$this->assertNull(Util::ifvalor(false));

		$this->assertEquals('a', Util::coalesce('a', 'b', 'c'));
		$this->assertEquals('b', Util::coalesce(false, 'b', 'c'));
		$this->assertEquals(0, Util::coalesce(false, null, 0));

		$this->assertEquals('4c92', Util::baseEncode(1000000, 'base62'));
		$this->assertEquals(1000000, Util::baseDecode('4c92', 'base62'));

		$this->assertEquals('lfls', Util::baseEncode(1000000, 'base36'));
		$this->assertEquals(1000000, Util::baseDecode('lfls', 'base36'));

		$this->assertEquals(array(
			'Orb', 'Tests', 'Util', 'UtilTest'
		), Util::getClassnameParts($this));

		$this->assertEquals('UtilTest', Util::getBaseClassname($this));
	}

	public function testSerialize()
	{
		$ser = '59752de96e243ee7a2afb69eb049c2ff:YToxOntpOjA7czozOiIxMjMiO30';
		$arr = array('123');

		$this->assertEquals($ser, Util::signedSerialize($arr, 'key'));
		$this->assertEquals($arr, Util::signedUnserialize($ser, 'key'));
	}

	public function testVariableConstructor()
	{
		$arr1 = range(1, 4);
		$arr2 = range(1, 13);

		$obj1 = Util::callUserConstructor('Orb\\Tests\\Util\\VariableConstructorTest', 1, 2, 3, 4);
		$this->assertEquals($arr1, $obj1->arr);

		$obj2 = Util::callUserConstructor('Orb\\Tests\\Util\\VariableConstructor2Test', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
		$this->assertEquals($arr2, $obj2->arr);

		$obj1 = Util::callUserConstructorArray('Orb\\Tests\\Util\\VariableConstructorTest', $arr1);
		$this->assertEquals($arr1, $obj1->arr);

		$obj2 = Util::callUserConstructorArray('Orb\\Tests\\Util\\VariableConstructor2Test', $arr2);
		$this->assertEquals($arr2, $obj2->arr);
	}

	public function testSecurityToken()
	{
		$token = '0-uzvcesqhlk-a0e7ac93d5a627d35705e6418ab4a8be167b5e55';
		$this->assertTrue(Util::checkStaticSecurityToken($token, 'secret'));
	}
}
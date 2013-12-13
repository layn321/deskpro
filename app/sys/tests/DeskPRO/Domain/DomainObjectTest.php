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

namespace DeskPRO\Tests\Domain;

use Application\DeskPRO\Domain\DomainObject;

class TestDomainObject extends DomainObject
{
	protected $_var1 = 'var';
	protected $name = 'foo';
	protected $answer = 42;
	protected $complex_name = 'test';
	protected $__double = 'trouble';

	public function getName()
	{
		return strtoupper($this->name);
	}

	public function setAnswer($value)
	{
		$this->answer = 42;
	}

	public function getComplexName()
	{
		return $this->complex_name . '!';
	}
}

class DomainObjectTest extends \PHPUnit_Framework_TestCase
{
	public function testMagic()
	{
		$obj = new TestDomainObject();

		$this->assertEquals('FOO', $obj['name']);

		$obj['name'] = 'bar';
		$this->assertEquals('BAR', $obj['name']);

		$obj['answer'] = 1000;
		$this->assertEquals(42, $obj['answer']);

		$this->assertEquals('test!', $obj['complex_name']);
		
		$obj->setComplexName('testing');
		$this->assertEquals('testing!', $obj['complex_name']);
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testBadVar1()
	{
		$obj = new TestDomainObject();
		$obj['_var1'];
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testBadVar2()
	{
		$obj = new TestDomainObject();
		$obj['__double'];
	}

	public function testToArray()
	{
		$obj = new TestDomainObject();
		$arr = $obj->toArray();

		$this->assertEquals(array(
			'name' => 'FOO',
			'answer' => 42,
			'complex_name' => 'test!'
		), $arr);
	}
}
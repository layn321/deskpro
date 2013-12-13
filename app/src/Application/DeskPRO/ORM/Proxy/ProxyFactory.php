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
 * @subpackage
 */

namespace Application\DeskPRO\ORM\Proxy;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Util\ClassUtils;
use Application\DeskPRO\ORM\Unprivate\UnprivateProxyFactory;

/**
 * _generateMethods: Added  __getpropvalue__ and __setpropvalue__ to array of functions to ignore
 * and add them directly as getter/setter
 */
class ProxyFactory extends UnprivateProxyFactory
{
	protected static $has_mutated_tpl = false;

	public function generateProxyClasses(array $classes, $toDir = null)
	{
		if (!self::$has_mutated_tpl) {
			self::$has_mutated_tpl = true;
			self::$_proxyClassTemplate = str_replace(array(
				'private $_entityPersister',
				'private $_identifier',
				'$this->_entityPersister',
				'$this->_identifier'
			), array(
				'protected $__entityPersister__',
				'protected $__identifier__',
				'$this->__entityPersister__',
				'$this->__identifier__'
			), self::$_proxyClassTemplate);

			self::$_proxyClassTemplate = str_replace(
				'protected $__entityPersister__;',
				'protected $__entityPersister__;' . "\n\t" . 'public $_dp_object_translatable;',
				self::$_proxyClassTemplate
			);
		}

		parent::generateProxyClasses($classes, $toDir);
	}

	protected function _generateMethods(ClassMetadata $class)
	{
		$methods = '';
		$methodNames = array();
		foreach ($class->reflClass->getMethods() as $method) {
			if ($method->isConstructor() || in_array(strtolower($method->getName()), array("__sleep", "__clone", "__getpropvalue__", "__setpropvalue__", '__hasrunload__', 'addcustomcallable', 'getobjecttranslatable', 'ensuredefaultpropertychangedlistener', 'addpropertychangedlistener', 'removepropertychangedlistener')) || isset($methodNames[$method->getName()])) {
				continue;
			}
			$methodNames[$method->getName()] = true;
			if ($method->isPublic() && ! $method->isFinal() && ! $method->isStatic()) {
				$methods .= "\n" . '    public function ';
				if ($method->returnsReference()) {
					$methods .= '&';
				}
				$methods .= $method->getName() . '(';
				$firstParam = true;
				$parameterString = $argumentString = '';
				foreach ($method->getParameters() as $param) {
					if ($firstParam) {
						$firstParam = false;
					} else {
						$parameterString .= ', ';
						$argumentString  .= ', ';
					}
					if (($paramClass = $param->getClass()) !== null) {
						$parameterString .= '\\' . $paramClass->getName() . ' ';
					} else if ($param->isArray()) {
						$parameterString .= 'array ';
					}
					if ($param->isPassedByReference()) {
						$parameterString .= '&';
					}
					$parameterString .= '$' . $param->getName();
					$argumentString  .= '$' . $param->getName();
					if ($param->isDefaultValueAvailable()) {
						$parameterString .= ' = ' . var_export($param->getDefaultValue(), true);
					}
				}
				$methods .= $parameterString . ')';
				$methods .= "\n" . '    {' . "\n";
				if ($this->isShortIdentifierGetter($method, $class)) {
					$identifier = lcfirst(substr($method->getName(), 3));
					$cast = in_array($class->fieldMappings[$identifier]['type'], array('integer', 'smallint')) ? '(int) ' : '';
					$methods .= '        if ($this->__isInitialized__ === false) {' . "\n";
					$methods .= '            return ' . $cast . '$this->__identifier__["' . $identifier . '"];' . "\n";
					$methods .= '        }' . "\n";
				}
				$methods .= '        if ($this->__isInitialized__ === false) $this->__load();' . "\n";
				$methods .= '        return parent::' . $method->getName() . '(' . $argumentString . ');';
				$methods .= "\n" . '    }' . "\n";
			}
		}

		$methods .= <<<'CODE'

	public function __getPropValue__($k) { return $this->$k; }
	public function __setPropValue__($k, $v) { $this->$k = $v; }
	public function __hasRunLoad__() { if (isset($this->__entityPersister__)) return false; return true; }
CODE;

		return $methods;
	}
}

<?php
/* This file has been auto-generated. See build-vendors-mutate.php */
namespace Application\DeskPRO\ORM\Unprivate;
use Doctrine\ORM\Proxy\ProxyException;
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\Mapping\AssociationMapping,
    Doctrine\Common\Util\ClassUtils;
class UnprivateProxyFactory extends \Doctrine\ORM\Proxy\ProxyFactory
{
    protected $_em;
    protected $_autoGenerate;
    protected $_proxyNamespace;
    protected $_proxyDir;
    const PATTERN_MATCH_ID_METHOD = '((public\s)?(function\s{1,}%s\s?\(\)\s{1,})\s{0,}{\s{0,}return\s{0,}\$this->%s;\s{0,}})i';
    public function __construct(EntityManager $em, $proxyDir, $proxyNs, $autoGenerate = false)
    {
        if ( ! $proxyDir) {
            throw ProxyException::proxyDirectoryRequired();
        }
        if ( ! $proxyNs) {
            throw ProxyException::proxyNamespaceRequired();
        }
        $this->_em = $em;
        $this->_proxyDir = $proxyDir;
        $this->_autoGenerate = $autoGenerate;
        $this->_proxyNamespace = $proxyNs;
    }
    public function getProxy($className, $identifier)
    {
        $fqn = ClassUtils::generateProxyClassName($className, $this->_proxyNamespace);
        if (! class_exists($fqn, false)) {
            $fileName = $this->getProxyFileName($className);
            if ($this->_autoGenerate) {
                $this->_generateProxyClass($this->_em->getClassMetadata($className), $fileName, self::$_proxyClassTemplate);
            }
            require $fileName;
        }
        if ( ! $this->_em->getMetadataFactory()->hasMetadataFor($fqn)) {
            $this->_em->getMetadataFactory()->setMetadataFor($fqn, $this->_em->getClassMetadata($className));
        }
        $entityPersister = $this->_em->getUnitOfWork()->getEntityPersister($className);
        return new $fqn($entityPersister, $identifier);
    }
    protected function getProxyFileName($className, $baseDir = null)
    {
        $proxyDir = $baseDir ?: $this->_proxyDir;
        return $proxyDir . DIRECTORY_SEPARATOR . '__CG__' . str_replace('\\', '', $className) . '.php';
    }
    public function generateProxyClasses(array $classes, $toDir = null)
    {
        $proxyDir = $toDir ?: $this->_proxyDir;
        $proxyDir = rtrim($proxyDir, DIRECTORY_SEPARATOR);
        $num = 0;
        foreach ($classes as $class) {
            if ($class->isMappedSuperclass || $class->reflClass->isAbstract()) {
                continue;
            }
            $proxyFileName = $this->getProxyFileName($class->name, $proxyDir);
            $this->_generateProxyClass($class, $proxyFileName, self::$_proxyClassTemplate);
            $num++;
        }
        return $num;
    }
    protected function _generateProxyClass($class, $fileName, $file)
    {
        $methods = $this->_generateMethods($class);
        $sleepImpl = $this->_generateSleep($class);
        $cloneImpl = $class->reflClass->hasMethod('__clone') ? 'parent::__clone();' : '';
        $placeholders = array(
            '<namespace>',
            '<proxyClassName>', '<className>',
            '<methods>', '<sleepImpl>', '<cloneImpl>'
        );
        $className = ltrim($class->name, '\\');
        $proxyClassName = ClassUtils::generateProxyClassName($class->name, $this->_proxyNamespace);
        $parts = explode('\\', strrev($proxyClassName), 2);
        $proxyClassNamespace = strrev($parts[1]);
        $proxyClassName = strrev($parts[0]);
        $replacements = array(
            $proxyClassNamespace,
            $proxyClassName,
            $className,
            $methods,
            $sleepImpl,
            $cloneImpl
        );
        $file = str_replace($placeholders, $replacements, $file);
        file_put_contents($fileName, $file, LOCK_EX);
    }
    protected function _generateMethods(ClassMetadata $class)
    {
        $methods = '';
        $methodNames = array();
        foreach ($class->reflClass->getMethods() as $method) {
            if ($method->isConstructor() || in_array(strtolower($method->getName()), array("__sleep", "__clone")) || isset($methodNames[$method->getName()])) {
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
                    $methods .= '            return ' . $cast . '$this->_identifier["' . $identifier . '"];' . "\n";
                    $methods .= '        }' . "\n";
                }
                $methods .= '        $this->__load();' . "\n";
                $methods .= '        return parent::' . $method->getName() . '(' . $argumentString . ');';
                $methods .= "\n" . '    }' . "\n";
            }
        }
        return $methods;
    }
    protected function isShortIdentifierGetter($method, $class)
    {
        $identifier = lcfirst(substr($method->getName(), 3));
        $cheapCheck = (
            $method->getNumberOfParameters() == 0 &&
            substr($method->getName(), 0, 3) == "get" &&
            in_array($identifier, $class->identifier, true) &&
            $class->hasField($identifier) &&
            (($method->getEndLine() - $method->getStartLine()) <= 4)
            && in_array($class->fieldMappings[$identifier]['type'], array('integer', 'bigint', 'smallint', 'string'))
        );
        if ($cheapCheck) {
            $code = file($method->getDeclaringClass()->getFileName());
            $code = trim(implode(" ", array_slice($code, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1)));
            $pattern = sprintf(self::PATTERN_MATCH_ID_METHOD, $method->getName(), $identifier);
            if (preg_match($pattern, $code)) {
                return true;
            }
        }
        return false;
    }
    protected function _generateSleep(ClassMetadata $class)
    {
        $sleepImpl = '';
        if ($class->reflClass->hasMethod('__sleep')) {
            $sleepImpl .= "return array_merge(array('__isInitialized__'), parent::__sleep());";
        } else {
            $sleepImpl .= "return array('__isInitialized__', ";
            $first = true;
            foreach ($class->getReflectionProperties() as $name => $prop) {
                if ($first) {
                    $first = false;
                } else {
                    $sleepImpl .= ', ';
                }
                $sleepImpl .= "'" . $name . "'";
            }
            $sleepImpl .= ');';
        }
        return $sleepImpl;
    }
    protected static $_proxyClassTemplate =
'<?php
namespace <namespace>;
/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class <proxyClassName> extends \<className> implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }
    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }
    <methods>
    public function __sleep()
    {
        <sleepImpl>
    }
    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        <cloneImpl>
    }
}';
}

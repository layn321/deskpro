<?php
namespace Proxies\__CG__\Application\DeskPRO\Entity;
/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class ChatMessage extends \Application\DeskPRO\Entity\ChatMessage implements \Doctrine\ORM\Proxy\Proxy
{
    protected $__entityPersister__;
	public $_dp_object_translatable;
    protected $__identifier__;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->__entityPersister__ = $entityPersister;
        $this->__identifier__ = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->__entityPersister__) {
            $this->__isInitialized__ = true;
            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }
            if ($this->__entityPersister__->load($this->__identifier__, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->__entityPersister__, $this->__identifier__);
        }
    }
    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }
    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->__identifier__["id"];
        }
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getId();
    }

    public function setAuthor($author)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setAuthor($author);
    }

    public function getAuthorId()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getAuthorId();
    }

    public function getAuthorName()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getAuthorName();
    }

    public function getAuthorPictureUrl($size = 80, $secure = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getAuthorPictureUrl($size, $secure);
    }

    public function _setUserName()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_setUserName();
    }

    public function getInfo()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getInfo();
    }

    public function getContentHtml()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getContentHtml();
    }

    public function toApiData($primary = true, $deep = true, array $visited = array (
))
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::toApiData($primary, $deep, $visited);
    }

    public function getObjectRef()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getObjectRef();
    }

    public function setUntrackedModelField($field, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setUntrackedModelField($field, $value);
    }

    public function getScalarData()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getScalarData();
    }

    public function _setNoPersist()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_setNoPersist();
    }

    public function _isNoPersist()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_isNoPersist();
    }

    public function __toString()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__toString();
    }

    public function fromArray(array $values)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::fromArray($values);
    }

    public function toArray($mode = 1)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::toArray($mode);
    }

    public function getKeys()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getKeys();
    }

    public function getFieldKeys()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getFieldKeys();
    }

    public function propertyFieldExists($field)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::propertyFieldExists($field);
    }

    public function get($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::get($name);
    }

    public function set($name, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::set($name, $value);
    }

    public function __get($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__set($name, $value);
    }

    public function __isset($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__isset($name);
    }

    public function __unset($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__unset($name);
    }

    public function __call($name, $arguments)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__call($name, $arguments);
    }

    public function offsetExists($offset)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::offsetExists($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::offsetSet($offset, $value);
    }

    public function offsetGet($offset)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::offsetGet($offset);
    }

    public function offsetUnset($offset)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::offsetUnset($offset);
    }

	public function __getPropValue__($k) { return $this->$k; }
	public function __setPropValue__($k, $v) { $this->$k = $v; }
	public function __hasRunLoad__() { if (isset($this->__entityPersister__)) return false; return true; }
    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'tag', 'origin', 'person_name', 'content', 'is_sys', 'is_user_hidden', 'is_html', 'metadata', 'date_created', 'date_received', 'conversation', 'author');
    }
    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->__entityPersister__) {
            $this->__isInitialized__ = true;
            $class = $this->__entityPersister__->getClassMetadata();
            $original = $this->__entityPersister__->load($this->__identifier__);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->__entityPersister__, $this->__identifier__);
        }
        parent::__clone();
    }
}
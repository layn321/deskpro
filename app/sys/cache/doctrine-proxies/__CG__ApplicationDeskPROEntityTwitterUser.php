<?php
namespace Proxies\__CG__\Application\DeskPRO\Entity;
/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class TwitterUser extends \Application\DeskPRO\Entity\TwitterUser implements \Doctrine\ORM\Proxy\Proxy
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
    
    public function isProtected()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isProtected();
    }

    public function isVerified()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isVerified();
    }

    public function isGeoEnabled()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isGeoEnabled();
    }

    public function getProfileImageUrl($size = 'normal')
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getProfileImageUrl($size);
    }

    public function getStatuses($page = 1, $per_page = 25)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getStatuses($page, $per_page);
    }

    public function getMessages()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getMessages();
    }

    public function getMentions()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getMentions();
    }

    public function getFollowers($page = 1, $per_page = 25)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getFollowers($page, $per_page);
    }

    public function getFriends($page = 1, $per_page = 25)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getFriends($page, $per_page);
    }

    public function countAccountInteractions(\Application\DeskPRO\Entity\TwitterAccount $account)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::countAccountInteractions($account);
    }

    public function getVerifiedPeople()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getVerifiedPeople();
    }

    public function getPossiblePeople()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPossiblePeople();
    }

    public function getPossibleOrganizations()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPossibleOrganizations();
    }

    public function offsetGet($offset)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::offsetGet($offset);
    }

    public function updateProfile()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::updateProfile();
    }

    public function updateFollows($register_stubs = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::updateFollows($register_stubs);
    }

    public function updateFromJson($user)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::updateFromJson($user);
    }

    public function _checkStub()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_checkStub();
    }

    public function unregisterStub()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::unregisterStub();
    }

    public function registerStub()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::registerStub();
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

    public function toApiData($primary = true, $deep = true, array $visited = array (
))
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::toApiData($primary, $deep, $visited);
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
        return array('__isInitialized__', 'id', 'name', 'screen_name', 'profile_image_url', 'language', 'url', 'is_protected', 'is_verified', 'location', 'description', 'is_geo_enabled', 'is_stub', 'last_timeline_update', 'last_profile_update', 'last_follow_update', 'followers_count', 'friends_count', 'statuses_count', 'statuses', 'replies', 'mentions', 'messages', 'friends', 'followers', 'account');
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
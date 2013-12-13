<?php
namespace Proxies\__CG__\Application\DeskPRO\Entity;
/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Person extends \Application\DeskPRO\Entity\Person implements \Doctrine\ORM\Proxy\Proxy
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

    public function isGuest()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isGuest();
    }

    public function _initPersonLogger()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_initPersonLogger();
    }

    public function hasPerm($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasPerm($name);
    }

    public function getOrganizationId()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getOrganizationId();
    }

    public function setOrganizationId($org_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setOrganizationId($org_id);
    }

    public function setIsAgent($yesno)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setIsAgent($yesno);
    }

    public function getCanBilling()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getCanBilling();
    }

    public function getRealCanBilling()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getRealCanBilling();
    }

    public function loadHelper($name, array $options = array (
))
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::loadHelper($name, $options);
    }

    public function getHelper($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getHelper($name);
    }

    public function __get($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__get($name);
    }

    public function __isset($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__isset($name);
    }

    public function getDisplayName($id_fallback = true)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDisplayName($id_fallback);
    }

    public function getDisplayNameUser()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDisplayNameUser();
    }

    public function getNameWithTitle()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getNameWithTitle();
    }

    public function getDisplayContact()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDisplayContact();
    }

    public function getDisplayContactShort($max_len = 40)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDisplayContactShort($max_len);
    }

    public function setImportance($importance)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setImportance($importance);
    }

    public function getApiToken()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getApiToken();
    }

    public function checkPassword($plain_password)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::checkPassword($plain_password);
    }

    public function setPassword($plain_password)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setPassword($plain_password);
    }

    public function setRawPassword($password)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setRawPassword($password);
    }

    public function getPlaintextPassword()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPlaintextPassword();
    }

    public function getPasswordHash()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPasswordHash();
    }

    public function hashPassword($plain_password)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hashPassword($plain_password);
    }

    public function getPasswordSchemeHandler()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPasswordSchemeHandler();
    }

    public function addPreference(\Application\DeskPRO\Entity\PersonPref $pref)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addPreference($pref);
    }

    public function setPreference($pref_name, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setPreference($pref_name, $value);
    }

    public function getPref($name, $default = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPref($name, $default);
    }

    public function getNamedPrefs()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getNamedPrefs();
    }

    public function getRealLanguage()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getRealLanguage();
    }

    public function getLanguage()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getLanguage();
    }

    public function getLanguageId()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getLanguageId();
    }

    public function setLanguageId($id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setLanguageId($id);
    }

    public function getLocale()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getLocale();
    }

    public function getStartOfWeek()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getStartOfWeek();
    }

    public function loadPrefGroup($pref_group)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::loadPrefGroup($pref_group);
    }

    public function getPermission($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPermission($name);
    }

    public function getUsergroupIds()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getUsergroupIds();
    }

    public function isMemberOfUsergroup($usergroup_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isMemberOfUsergroup($usergroup_id);
    }

    public function addContactData(\Application\DeskPRO\Entity\PersonContactData $contact_data)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addContactData($contact_data);
    }

    public function getContactData($type = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getContactData($type);
    }

    public function getCustomDataForField($field_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getCustomDataForField($field_id);
    }

    public function removeCustomDataForField(\Application\DeskPRO\Entity\CustomDefPerson $field)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::removeCustomDataForField($field);
    }

    public function setCustomData($field_id, $value_type, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setCustomData($field_id, $value_type, $value);
    }

    public function addCustomData(\Application\DeskPRO\Entity\CustomDataPerson $data)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addCustomData($data);
    }

    public function renderCustomField($field_id, $context = 'html')
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::renderCustomField($field_id, $context);
    }

    public function hasCustomField($field_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasCustomField($field_id);
    }

    public function getCustomFieldDisplayArray($field_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getCustomFieldDisplayArray($field_id);
    }

    public function getPrimaryEmailAddress()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPrimaryEmailAddress();
    }

    public function getPrimaryEmail()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPrimaryEmail();
    }

    public function pickEmailAddress($search)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::pickEmailAddress($search);
    }

    public function getEmailAddress()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getEmailAddress();
    }

    public function getEmailAddresses()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getEmailAddresses();
    }

    public function hasEmailAddress($email_address)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasEmailAddress($email_address);
    }

    public function getPrimaryEmailId()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPrimaryEmailId();
    }

    public function setEmail($email_address, $validated = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setEmail($email_address, $validated);
    }

    public function getValidatedEmails()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getValidatedEmails();
    }

    public function addEmailAddress(\Application\DeskPRO\Entity\PersonEmail $email)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addEmailAddress($email);
    }

    public function addEmailAddressString($email)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addEmailAddressString($email);
    }

    public function removeEmailAddressId($email_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::removeEmailAddressId($email_id);
    }

    public function getEmailId($email_id)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getEmailId($email_id);
    }

    public function findEmailAddress($email_address)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::findEmailAddress($email_address);
    }

    public function addUsergroup(\Application\DeskPRO\Entity\Usergroup $usergroup)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addUsergroup($usergroup);
    }

    public function hasUsergroup($usergroup)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasUsergroup($usergroup);
    }

    public function addLabel(\Application\DeskPRO\Entity\LabelPerson $label)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::addLabel($label);
    }

    public function getUsergroupSetKey()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getUsergroupSetKey();
    }

    public function setPictureBlob(\Application\DeskPRO\Entity\Blob $blob = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setPictureBlob($blob);
    }

    public function setGravatarUrl($url)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setGravatarUrl($url);
    }

    public function getPictureUrl($size = 80, $secure = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPictureUrl($size, $secure);
    }

    public function getGravatarUrl($size = 80, $secure = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getGravatarUrl($size, $secure);
    }

    public function hasPicture($auto_check = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasPicture($auto_check);
    }

    public function isNewPerson()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::isNewPerson();
    }

    public function setOrganization(\Application\DeskPRO\Entity\Organization $org = NULL, $position = '', $manager = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setOrganization($org, $position, $manager);
    }

    public function getTwitterAccountIds()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getTwitterAccountIds();
    }

    public function getTwitterAccounts()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getTwitterAccounts();
    }

    public function __toString()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__toString();
    }

    public function getKeys()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getKeys();
    }

    public function setName($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setName($name);
    }

    public function setFirstName($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setFirstName($name);
    }

    public function setLastName($name)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setLastName($name);
    }

    public function setLastLoginAt(\DateTime $time = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setLastLoginAt($time);
    }

    public function setDisablePicture($yn = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setDisablePicture($yn);
    }

    public function getLabelManager()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getLabelManager();
    }

    public function getPermissionsManager()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPermissionsManager();
    }

    public function getHelperManager()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getHelperManager();
    }

    public function getPersonLogger()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getPersonLogger();
    }

    public function _savePersonLogs()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_savePersonLogs();
    }

    public function _presavePerson()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_presavePerson();
    }

    public function setTimezone($tz)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setTimezone($tz);
    }

    public function getTimezone()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getTimezone();
    }

    public function getRealTimezone()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getRealTimezone();
    }

    public function getDateTimezone()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDateTimezone();
    }

    public function getDateTime()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDateTime();
    }

    public function getDateForTime($time)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDateForTime($time);
    }

    public function getTimezoneOffset($as_string = false)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getTimezoneOffset($as_string);
    }

    public function getTimezoneOffsetSeconds()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getTimezoneOffsetSeconds();
    }

    public function setDisableAutoresponses($val, $reason = NULL)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setDisableAutoresponses($val, $reason);
    }

    public function setOrganizationPosition($organization_position)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::setOrganizationPosition($organization_position);
    }

    public function hasSla(\Application\DeskPRO\Entity\Sla $sla)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::hasSla($sla);
    }

    public function getRememberMeCookieCode()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getRememberMeCookieCode();
    }

    public function validateRememberMeCookieCode($code)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::validateRememberMeCookieCode($code);
    }

    public function _postPersist()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::_postPersist();
    }

    public function getChangeTracker()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getChangeTracker();
    }

    public function getDataForWidget()
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::getDataForWidget();
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

    public function __set($name, $value)
    {
        if ($this->__isInitialized__ === false) $this->__load();
        return parent::__set($name, $value);
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
        return array('__isInitialized__', 'id', 'gravatar_url', 'disable_picture', 'is_contact', 'is_user', 'is_agent', 'was_agent', 'can_agent', 'can_admin', 'can_billing', 'can_reports', 'is_vacation_mode', 'disable_autoresponses', 'disable_autoresponses_log', 'is_confirmed', 'is_agent_confirmed', 'is_deleted', 'is_disabled', 'importance', 'creation_system', 'name', 'first_name', 'last_name', 'title_prefix', 'override_display_name', 'summary', 'secret_string', 'organization_position', 'organization_manager', 'timezone', 'password', 'password_scheme', 'salt', 'date_created', 'date_last_login', 'date_picture_check', 'picture_blob', 'language', 'organization', 'primary_email', 'emails', 'labels', 'custom_data', 'contact_data', 'usergroups', 'preferences', 'usersource_assoc', 'twitter_users', 'slas', 'twitter_accounts');
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
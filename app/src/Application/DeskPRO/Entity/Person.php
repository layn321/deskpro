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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;
use Application\DeskPRO\ORM\Util\Util as ORM_Util;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;
use Orb\Util\Util;

use Application\DeskPRO\Entity;

/**
 * A "person" is a record in the database that stores information about a person.
 * Every person is capable of logging in, though it may be the case that many wont (ie they are just contact cards).
 *
 */
class Person extends \Application\DeskPRO\Domain\DomainObject
{
	const CREATED_WEB_PERSON = 'web.person';
	const CREATED_WEB_AGENT = 'web.agent';
	const CREATED_WEB_USERSOURCE = 'web.usersource';
	const CREATED_GATEWAT_PERSON = 'gateway.person';

	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * The users profile picture
	 *
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $picture_blob = null;

	/**
	 * @var bool
	 */
	protected $disable_picture = false;

	/**
	 * The URL to the users gravatar if any
	 *
	 * @var string
	 */
	protected $gravatar_url = '';

	/**
	 * Is this person a contact (someone we care about seeing)?
	 *
	 * @var bool
	 */
	protected $is_contact = true;

	/**
	 * Is this person a user (someone with login credentials)?
	 *
	 * @var bool
	 */
	protected $is_user = false;

	/**
	 * @var bool
	 */
	protected $is_agent = 0;

	/**
	 * @var bool
	 */
	protected $was_agent = 0;

	/**
	 * @var bool
	 */
	protected $can_agent = 0;

	/**
	 * @var bool
	 */
	protected $can_admin = 0;

	/**
	 * @var bool
	 */
	protected $can_billing = 0;

	/**
	 * @var bool
	 */
	protected $can_reports = 0;

	/**
	 * @var bool
	 */
	protected $is_vacation_mode = 0;

	/**
	 * Autoresponds
	 *
	 * @var bool
	 */
	protected $disable_autoresponses = 0;

	/**
	 * @var string
	 */
	protected $disable_autoresponses_log = '';

	/**
	 * Has this user ever confirmed themselves via email?
	 * Individual email addresses must be confirmed as well, but this
	 * is an account-wide flag that says the user is at least real.
	 *
	 * @var bool
	 */
	protected $is_confirmed = true;

	/**
	 * Has this user ever confirmed themselves via email?
	 *
	 * @var bool
	 */
	protected $is_agent_confirmed = true;

	/**
	 * Is the user deleted?
	 *
	 * @var bool
	 */
	protected $is_deleted = false;

	/**
	 * Is the user disabled?
	 *
	 * @var bool
	 */
	protected $is_disabled = false;

	/**
	 * The user importance, 0-5
	 *
	 * @var int
	 */
	protected $importance = 0;

	/**
	 * @var string
	 */
	protected $creation_system = 'web.person';

	/**
	 * The users name (best guess from other sources etc)
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The users name (best guess from other sources etc)
	 *
	 * @var string
	 */
	protected $first_name = '';

	/**
	 * The users name (best guess from other sources etc)
	 *
	 * @var string
	 */
	protected $last_name = '';

	/**
	 * The users title prefix (Mr., Mrs., etc)
	 *
	 * @var string
	 */
	protected $title_prefix = '';

	/**
	 * Overrides the display name of an person in the user interface (agents only).
	 *
	 * @var string
	 */
	protected $override_display_name = '';

	/**
	 * The summary field as filled in by agents
	 *
	 * @var string
	 */
	protected $summary = '';


	/**
	 * A secret string used in various hashing or encryption schemes.
	 *
	 * @var string
	 */
	protected $secret_string;

	/**
	 * The language associate with the user.
	 *
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $language = null;

	/**
	 * The Other Guys
	 * The user's billing department
	 * 
	 * @var \Application\DeskPRO\Entity\Department
	 */
	 protected $department = null;

	/**
	 * The users organization
	 *
	 * @var \Application\DeskPRO\Entity\Organization
	 */
	protected $organization = null;

	/**
	 * The persons position at the organization
	 *
	 * @var string
	 */
	protected $organization_position = '';

	/**
	 * True if the person is a manager of their organization
	 *
	 * @var bool
	 */
	protected $organization_manager = false;

	/**
	 * The timezone associated with this user.
	 *
	 * @var string
	 */
	protected $timezone = 'UTC';

	/**
	 * Every person has a local login capability with this password and using
	 * an email address.
	 *
	 * @var string
	 */
	protected $password = null;

	/**
	 * The hashing scheme used with checkPassword(). NULL means default, the built in scheme.
	 *
	 * @var string
	 */
	protected $password_scheme = null;

	/**
	 * A salt used to hash the password with.
	 *
	 * @var string
	 */
	protected $salt;

	/**
	 * The primary email address used by this account
	 *
	 * @var \Application\DeskPRO\Entity\PersonEmail
	 */
	protected $primary_email;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $emails;

	/**
	 */
	protected $labels;

	/**
	 */
	protected $custom_data;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $contact_data;

	/**
	 * Usergroups the user belongs to
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
     * )
	 */
	protected $usergroups;

	/**
	 * Twitter accounts this user has access to
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $twitter_accounts;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $twitter_users;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $preferences;

	/**
	 * Usersource associations
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $usersource_assoc;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $slas;

	/**
	 * The date the user was inserted into the system
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * The last time the user logged in
	 *
	 * @var \DateTime
	 */
	protected $date_last_login = null;

	/**
	 * The last time the users gravatar (or other 3rd party image) was checked.
	 *
	 * @var \DateTime
	 */
	protected $date_picture_check = null;

	/**
	 * If we have set a password for this user, then the plaintext version will be set here.
	 * @var string
	 */
	protected $_set_plain_password = null;

	/**
	 * An array of name=>value for loaded preferences. These are not obejcts.
	 * @var array
	 */
	protected $_pref_values = array();

	/**
	 * AN array of names we've loaded. This is because values can be null if they
	 * dont exist, but we dont want to keep trying ot laod them every time they're requested.
	 * @var array
	 */
	protected $_pref_loaded = array();

	/**
	 * Label manager for adding/removing labels
	 * @var \Application\DeskPRO\Labels\LabelManager
	 */
	protected $_label_manager = null;

	/**
	 * Helper manager for auto-loading functionality onto this object
	 * @var \Orb\Helper\HelperManager
	 */
	protected $_helper_manager = null;

	/**
	 * The permissions manager helper once its loaded
	 * @var \Application\DeskPRO\People\Helpers\PermissionsManager
	 */
	protected $_permissions_manager = null;

	/**
	 * True if this is a new record.
	 * @var bool
	 */
	protected $_is_new_person = false;

	/**
	 * @var \Application\DeskPRO\People\PersonChangeTracker
	 */
	protected $_person_logger = null;

	/**
	 * @var PersonEmailValidating
	 */
	public $email_validating;

	protected $_updated_org = false;

	/**
	 * A "contact person" is simply a person record. They have no login credentials, they are not
	 * a full user.
	 *
	 * (This is really just the same as calling the constructor yourself, but we may need to
	 * change this functionality in the future so its a method).
	 *
	 * @static
	 * @return Person
	 */
	public static function newContactPerson(array $info = null)
	{
		$person = new self();

		$email = null;
		if (!empty($info['email'])) {
			$email = $info['email'];
			unset($info['email']);
		}

		if ($info) {
			$person->fromArray($info);
		}

		if ($email) {
			$person->addEmailAddressString($email);
		}

		return $person;
	}

	/**
	 * A regular person is a person who can log in. They are a full user.
	 *
	 * @static
	 * @return Person
	 */
	public static function newRegularPerson()
	{
		$person = new self();
		return $person;
	}

	public function __construct()
	{
		$this->_is_new_person = true;

		$this->setModelField('date_created',    new \DateTime());
		$this->setModelField('secret_string',   Strings::random(40));
		$this->setModelField('timezone',        'UTC');
		$this->setModelField('salt',            Strings::random(40));

		// If we're loaded, then set default timezone from setting
		if (class_exists('Application\\DeskPRO\\App')) {
			try {
				$this->setTimezone(App::getSetting('core.default_timezone'));
			} catch (\Exception $e) {};
		}

		$this->emails                 = new \Doctrine\Common\Collections\ArrayCollection();
		$this->usergroups             = new \Doctrine\Common\Collections\ArrayCollection();
		$this->twitter_accounts       = new \Doctrine\Common\Collections\ArrayCollection();
		$this->twitter_users          = new \Doctrine\Common\Collections\ArrayCollection();
		$this->usersource_assoc       = new \Doctrine\Common\Collections\ArrayCollection();
		$this->personscraper_assoc    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->contact_data           = new \Doctrine\Common\Collections\ArrayCollection();
		$this->custom_data            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->preferences            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->labels                 = new \Doctrine\Common\Collections\ArrayCollection();
		$this->slas                   = new \Doctrine\Common\Collections\ArrayCollection();

		$this->_initPersonLogger();
		$this->_person_logger->recordExtra('person_created', true);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function isGuest()
	{
		return false;
	}


	/*
	 * The Other Guys
	 * #201402061205 @Layne added deppartment id getter/setter
	 */
	 
	public function getDepartmentId()
	{
		if ($this->department) {
			return $this->department['id'];
		} else {
			return 0;
		}
	}

/*	public function getDepartment()
	{
		if ($this->department) {
			return $this->department->getUserTitle();
		} else {
			return 0;
		}
	}
*/
	public function setDepartmentId($dep_id)
	{
		if ($dep_id) {
			$dep = App::getEntityRepository('DeskPRO:Department')->find($dep_id);
			$this->setModelField('department', $dep);
		} else {
			$this->setModelField('department', null);
		}
	}

	
	/**
	 * THE OTHER GUYS - Return Agents Rate - Andy
     */
	public function getRate()
	{ 
	/**
	 * 
	 * The Other Guys | 201401261049 @Frankie -- getDB() is DeskPRO object, much easier
	 * #201401271140 @Layne -- change function call to reflect Doctrine pattern
	 */
		if ($this->department) {
			return $this->department['rate'];
		} else {
			return 0;
		}
	}
	// end #201401271140

	public function _initPersonLogger()
	{
		if ($this->_person_logger) {
			return;
		}
		$person_logger = new \Application\DeskPRO\People\PersonChangeTracker($this);
		$this->_person_logger = $person_logger;
		$this->addPropertyChangedListener($person_logger);
	}

	public function hasPerm($name)
	{
		return $this->getPermissionsManager()->hasPerm($name);
	}

	public function getOrganizationId()
	{
		if ($this->organization) {
			return $this->organization['id'];
		} else {
			return 0;
		}
	}

	public function setOrganizationId($org_id)
	{
		$this->_updated_org = true;
		if ($org_id) {
			$org = App::getEntityRepository('DeskPRO:Organization')->find($org_id);
			$this->setModelField('organization', $org);
		} else {
			$this->setModelField('organization', null);
		}
	}


	/**
	 * @param bool $yesno
	 */
	public function setIsAgent($yesno)
	{
		if ($yesno) {
			$this['is_agent_confirmed'] = true;
			$this['is_confirmed'] = true;
		}

		$this->setModelField('is_agent', $yesno);
	}


	/**
	 * @return bool|int
	 */
	public function getCanBilling()
	{
		// If they are an admin, they can use billing
		// (because they could just log in to admin and set themselves as billing!)
		if ($this->can_admin) {
			return true;
		}

		return $this->can_billing;
	}



	/**
	 * @return bool|int
	 */
	public function getRealCanBilling()
	{
		return $this->can_billing;
	}


	/**
	 * Add a new helper
	 *
	 * @param string $name Name of the helper class
	 */
	public function loadHelper($name, array $options = array())
	{
		$classname = 'Application\\DeskPRO\\People\\Helpers\\' . $name;

		if (!$this->getHelperManager()->hasHelper($name)) {
			$object = new $classname($this, $options);
			$this->getHelperManager()->addHelper($object);
		}
	}

	/**
	 * Get a registered helper
	 * @param string $name
	 * @return mixed
	 */
	public function getHelper($name)
	{
		return $this->getHelperManager()->getHelper($name);
	}

	protected function _onNotCallable($name, $arguments)
	{
		if ($this->_helper_manager) {
			$name_l = strtolower($name);
			if ($this->_helper_manager->isNameCallable($name_l)) {
				return $this->_helper_manager->callName($name_l, $arguments);
			}
		}

		if (strpos($name, 'getfield') === 0 && $field_id = Strings::extractRegexMatch('#^getfield(\d+)$#', $name)) {
			return $this->renderCustomField($field_id, 'text');
		}

		return parent::_onNotCallable($name, $arguments);
	}

	public function __get($name)
	{
		if ($this->_helper_manager) {
			$name_l = strtolower($name);
			if ($this->_helper_manager->isNameCallable($name_l)) {
				return $this->_helper_manager->callName($name_l, array());
			}
		}

		return parent::__get($name);
	}

	public function __isset($name)
	{
		if ($this->_helper_manager) {
			$name_l = strtolower($name);
			if ($this->_helper_manager->isNameCallable($name_l)) {
				return true;
			}
		}

		return parent::__isset($name);
	}

	/**
	 * Get a string display name we can call this person.
	 *
	 * @param bool $id_fallback
	 *
	 * @return string|null
	 */
	public function getDisplayName($id_fallback = true)
	{
		if ($this['first_name'] AND $this['last_name']) {
			return $this['first_name'] . ' ' . $this['last_name'];
		} elseif ($this['name']) {
			return $this['name'];
		} elseif ($this['last_name']) {
			return $this['last_name'];
		} elseif ($this['first_name']) {
			return $this['first_name'];
		} elseif ($this['primary_email']) {

			// try to get a nice name from the email address
			$email = $this['primary_email']['email'];
			list ($name,) = explode('@', $email, 2);

			$name = str_replace('_', ' ', $name);
			$name = str_replace('.', ' ', $name);
			$name = preg_replace('#[ ]{2,}#', ' ', $name); //consec spaces to single space

			$name = Strings::utf8_ucwords($name);

			return $name;
		} elseif ($id_fallback) {
			return 'ID-' . $this['id'];
		}

		return null;
	}

	/**
	 * Gets the display name to be display
	 *
	 * @return null|string
	 */
	public function getDisplayNameUser()
	{
		if ($this->is_agent && $this->override_display_name) {
			return $this->override_display_name;
		}

		return $this->getDisplayName();
	}

	/**
	 * Gets this person's name with the title prefix
	 *
	 * @return string|null
	 */
	public function getNameWithTitle()
	{
		$name = $this->getDisplayName(true);
		if ($this->title_prefix) {
			$name = $this->title_prefix . ' ' . $name;
		}

		return $name;
	}



	/**
	 * Gets this persons name and their primary email address
	 *
	 * @return string
	 */
	public function getDisplayContact()
	{
		$display = $this->getDisplayName();
		if ($this->getPrimaryEmailAddress() && $display != $this->getPrimaryEmailAddress()) {
			$display .= " <{$this->getPrimaryEmailAddress()}>";
		}

		return $display;
	}


	/**
	 * Gets this persons name and primary email address. If their name is
	 * long, we'll try to initialize it or try other ways to shorten the name
	 * to the specified number of characters.
	 *
	 * @return string
	 */
	public function getDisplayContactShort($max_len = 40)
	{
		/*
		 * n = name
		 * e = email
		 * fi = first initial
		 * li = last initial
		 * fn = first name
		 * ln = last name
		 */
		$try = array(
			array('fn', ' ', 'ln', ' ', '(e)'),
			array('fi', ' ', 'ln', ' ', '(e)'),
			array('fn', ' ', 'li', ' ', '(e)'),
			array('n', ' ', '(e)'),
			array('fn', ' ', 'ln'),
			array('fn', ' ', 'li'),
			array('fi', 'ln'),
			array('fi', 'li', ' ', '(e)'),
			array('fi', 'li'),
			array('e'),
			array('n')
		);

		$shortest = null;
		$shortest_len = null;

		foreach ($try as $k => $elements) {

			$display = array();

			foreach ($elements as $el) {
				switch ($el) {
					case 'n':
						if (!$this->name) continue 2;
						$display[] = $this->name;
						break;

					case 'fi':
						if (!$this->first_name) continue 2;
						$display[] = $this->first_name[0];
						break;

					case 'fn':
						if (!$this->first_name) continue 2;
						$display[] = $this->first_name;
						break;

					case 'li':
						if (!$this->last_name) continue 2;
						$display[] = $this->last_name[0];
						break;

					case 'ln':
						if (!$this->last_name) continue 2;
						$display[] = $this->last_name;
						break;

					case '(e)':
					case 'e':
						if (!$this->getPrimaryEmailAddress()) continue 2;

						if ($el == '(e)') {
							$display[] = "({$this->getPrimaryEmailAddress()})";
						} else {
							$display[] = $this->getPrimaryEmailAddress();
						}
						break;

					default:
						$display[] = $el;
						break;
				}
			}

			$display = implode('', $display);
			$len = strlen($display);

			if ($len <= $max_len) {
				return $display;
			}

			if ($shortest === null OR $len < $shortest_len) {
				$shortest = $display;
				$shortest_len = $len;
			}
		}

		// If we got down here, we have no choice but to show
		// whatever we have
		if ($shortest !== null) {
			return $shortest;
		} else {
			return $this->getDisplayName();
		}
	}


	/**
	 * Set the importance of this user
	 *
	 * @param int $importance
	 */
	public function setImportance($importance)
	{
		$old = $this->importance;
		$this->importance = Numbers::bound($importance, 0, 5);
		$this->_onPropertyChanged('importance', $old, $this->importance);
	}

	public function getApiToken()
	{
		return App::getEntityRepository('DeskPRO:ApiToken')->getTokenForPerson($this);
	}


	/**
	 * Check to see if a password is the same one we have on record. Used with local auth.
	 *
	 * @param  $plain_password
	 * @return bool
	 */
	public function checkPassword($plain_password)
	{
		// It may be a token login from dp:login-token
		if ($this->id && strlen($plain_password) > 55) {
			$secret = sha1($this->secret_string . $this->salt);
			if (Util::checkStaticSecurityToken($plain_password, $secret)) {
				$GLOBALS['DP_LOGIN_VIA_TOKEN'] = true;
				return true;
			}
		}

		// Allows a define to be added to config to override a users password:
		// define('DP_OVERRIDE_USER_PASS', '20001:mypassword');
		if ($this->id && defined('DP_OVERRIDE_USER_PASS') && strpos(DP_OVERRIDE_USER_PASS, ':') !== false) {
			list ($id, $override_pass) = explode(':', DP_OVERRIDE_USER_PASS, 2);
			if ($this->id == $id) {
				return ($override_pass === $plain_password);
			}
		}

		return $this->getPasswordSchemeHandler()->checkPassword($this, $this->password, $plain_password);
	}



	/**
	 * Sets the hashed form of the password for this user. Used with local auth.
	 *
	 * @param  string $plain_password The password to set
	 * @return string
	 */
	public function setPassword($plain_password)
	{
		// If we're setting the password, we're now using the default
		// password scheme so remove the old one. eg an imported user just changed their password
		$this->setModelField('password_scheme', 'bcrypt');

		// When a password is set, then they're a user now
		$this->setModelField('is_user', true);

		$hash = $this->hashPassword($plain_password);

		$pass = $hash;
		$this->_set_plain_password = $plain_password;

		$this->setModelField('password', $pass);

		if ($this->id) {
			$token = App::getEntityRepository('DeskPRO:ApiToken')->getTokenForPerson($this);
			if ($token) {
				$token->regenerateToken();
				App::getOrm()->persist($token);
			}
		}

		return $this->password;
	}


	/**
	 * Set the raw password field (ie already hashed)
	 *
	 * @param $password
	 */
	public function setRawPassword($password)
	{
		$this->setModelField('password', $password);
	}


	/**
	 * If you have set a password, the plaintext version will be returned.
	 * Otherwise, null is returned.
	 *
	 * @return string
	 */
	public function getPlaintextPassword()
	{
		return $this->_set_plain_password;
	}


	/**
	 * Get the raw password hash
	 *
	 * @return string
	 */
	public function getPasswordHash()
	{
		return $this->password;
	}



	/**
	 * Create a new password hash using the salt and algorithm used with this user.
	 *
	 * @param  string $plain_password The password to hash
	 * @return string
	 */
	public function hashPassword($plain_password)
	{
		return $this->getPasswordSchemeHandler()->hashPassword($this, $plain_password);
	}


	/**
	 * @return \Application\DeskPRO\People\PasswordSchemeInterface
	 */
	public function getPasswordSchemeHandler()
	{
		if ($this->password_scheme === null) {
			$scheme = 'deskpro4original';
		} else {
			$scheme = $this->password_scheme;
		}

		return App::getSystemObject('password_scheme', array('scheme' => $scheme));
	}



	/**
	 * Add a preference value to this user.
	 *
	 * @param Entity\PersonPref $pref
	 */
	public function addPreference(PersonPref $pref)
	{
		$this->preferences->add($pref);
		$pref['person'] = $this;
	}


	/**
	 * Set the value of a preference. Will update if it exists, or create a new one
	 * if it doesnt.
	 *
	 * @param  $pref
	 * @param  $value
	 * @return PersonPref
	 */
	public function setPreference($pref_name, $value)
	{
		$pref = App::getEntityRepository('DeskPRO:PersonPref')->getForPerson($pref_name, $this);
		if (!$pref) {
			$pref = new PersonPref();
			$pref['name'] = $pref_name;
			$this->addPreference($pref);
		}

		$pref['value'] = $value;
		$this->_pref_loaded[] = $pref_name;
		$this->_pref_values[$pref_name] = $value;

		return $pref;
	}


	/**
	 * Get the value of a preference as it's currently stored.
	 *
	 * @param string $name
	 * @param mixed $default Default Value for the preference
	 *
	 * @return mixed
	 */
	public function getPref($name, $default = null)
	{
		if (!in_array($name, $this->_pref_loaded)) {
			$this->_pref_values[$name] = App::getOrm()->getRepository('DeskPRO:PersonPref')->getPrefForPersonId($name, $this->id);
		}

		if (isset($this->_pref_values[$name])) {
			return $this->_pref_values[$name];
		}

		if ($default === null && $name == 'agent.ticket_reverse_order') {
			return App::getSetting('core_tickets.default_ticket_reverse_order');
		}

		return $default;
	}



	/**
	 * Get an array of named preferences.
	 *
	 * @param string $names...
	 * @return array
	 */
	public function getNamedPrefs()
	{
		if (func_num_args() == 1) {
			$names = array();
			$names[] = func_get_arg(0);
		} else {
			$names = func_get_args();
		}

		// Filter out ones we already have
		$loaded = $this->_pref_loaded;
		$names_get = array_filter($names, function ($v) use ($loaded) {
			if (in_array($v, $loaded)) {
				return false;
			}
			return true;
		});

		if ($names_get) {
			$got = App::getOrm()->getRepository('DeskPRO:PersonPref')->getPrefForPersonId($names_get, $this->id);
			$this->_pref_values = array_merge($this->_pref_values, $got);
			$this->_pref_loaded = array_merge($this->_pref_loaded, array_keys($got));
		}

		$ret = array();
		foreach ($names as $n) {
			$ret[$n] = isset($this->_pref_values[$n]) ? $this->_pref_values[$n] : null;
		}

		return $ret;
	}


	/**
	 * Get the real language. This might be null if there is no preference for the user.
	 *
	 * @return \Application\DeskPRO\Entity\Language|null
	 */
	public function getRealLanguage()
	{
		return $this->language;
	}


	/**
	 * Get the users language
	 *
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function getLanguage()
	{
		if ($this->language) {
			return $this->language;
		}

		return App::getDataService('Language')->getDefault();
	}


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->getLanguage()->getId();
	}


	/**
	 * @param int $id
	 */
	public function setLanguageId($id)
	{
		$lang = App::getDataService('Language')->get($id);
		if (!$lang) {
			$lang = null;
		}

		$this['language'] = $lang;
	}



	/**
	 * Get the locale string
	 *
	 * Example: en_US
	 *
	 * @return string
	 */
	public function getLocale()
	{
		$lang = $this->getLanguage();
		return $lang->getLocale();
	}



	/**
	 * Returns the ISO-8601 representation of the day of the week that this
	 * person has selected as their start of the week.
	 *
	 * 1 = Monday, ..., 7 = Sunday
	 *
	 * @return int
	 */
	public function getStartOfWeek()
	{
		return 1;
	}



	/**
	 * Load a group of user prefs
	 * @param string $pref_group
	 * @return array
	 */
	public function loadPrefGroup($pref_group)
	{
		$group = App::getOrm()->getRepository('DeskPRO:PersonPref')->getPrefgroupForPersonId($pref_group, $this->id, false);
		$this->_pref_values = array_merge(
			$this->_pref_values,
			$group
		);

		return $group;
	}



	/**
	 * Get the value of a usergroup permission
	 *
	 * @param string $name The permission name
	 * @return mixed
	 */
	public function getPermission($name)
	{
		return $this->getPermissionsManager()->Usergroups->getPermission($name);
	}



	/**
	 * Get an array of usergroup ID's this user belongs to.
	 *
	 * @return array
	 */
	public function getUsergroupIds()
	{
		return $this->getPermissionsManager()->getUsergroupIds();
	}


	/**
	 * Check if a member is in a particular group
	 *
	 * @param $usergroup_id
	 * @return bool
	 */
	public function isMemberOfUsergroup($usergroup_id)
	{
		if (is_object($usergroup_id)) {
			$usergroup_id = $usergroup_id->getId();
		}

		return in_array($usergroup_id, $this->getUsergroupIds());
	}


	/**
	 * Add contact data
	 *
	 * @param PersonEmail $email
	 */
	public function addContactData(PersonContactData $contact_data)
	{
		$em = App::getOrm();

		$this['contact_data']->add($contact_data);

		$contact_data['person'] = $this;
		$em->persist($contact_data);
		$this->_onPropertyChanged('contact_data', $this->contact_data, $this->contact_data);
	}



	/**
	 * @param null $type
	 * @return array
	 */
	public function getContactData($type = null)
	{
		if (!$type) {
			return $this->contact_data;
		}

		$ret = array();

		foreach ($this->contact_data as $cd) {
			if ($cd->contact_type == $type) {
				$ret[] = $cd;
			}
		}

		return $ret;
	}


	/**
	 * Find an existing data record for a field id.
	 *
	 * @param int $field_id
	 * @return CustomDataPerson
	 */
	public function getCustomDataForField($field_id)
	{
		if ($field_id instanceof CustomDefPerson) {
			$field_id = $field_id['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id) {
				return $data;
			}
		}

		return null;
	}

	public function removeCustomDataForField(CustomDefPerson $field)
	{
		$parent_id = null;
		$field_id = $field['id'];
		if ($field->parent) {
			$parent_id = $field->parent['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id OR $data['field_id'] == $parent_id) {
				$this->custom_data->removeElement($data);
			}
		}
	}



	/**
	 * Set custom field data for a particular field.
	 *
	 * @param int $field_id
	 * @param mixed $value
	 * @return mixed
	 */
	public function setCustomData($field_id, $value_type, $value)
	{
		$custom_data = $this->getCustomDataForField($field_id);
		$is_new = false;

		if (!$custom_data) {
			if ($value === null) return null;

			$is_new = true;

			$field = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($field_id);
			if (!$field) {
				throw new \Exception("Invalid field_id `$field_id`");
			}
			$custom_data = new CustomDataPerson();
			$custom_data['field'] = $field;
		}

		if ($value === null) {
			$this['custom_data']->removeElement($custom_data);
			return null;
		}

		$custom_data[$value_type] = $value;

		if ($is_new) {
			$this->addCustomData($custom_data);
		}

		return $custom_data;
	}

	/**
	 * Add a custom data item to this ticket
	 *
	 * @param CustomDataPerson $data
	 */
	public function addCustomData(CustomDataPerson $data)
	{
		$this->custom_data->add($data);
		$data['person'] = $this;
		$this->_onPropertyChanged('custom_data', $this->custom_data, $this->custom_data);
	}



	/**
	 * Render a custom field
	 *
	 * !depreciated
	 */
	public function renderCustomField($field_id, $context = 'html')
	{
		$f_def = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($field_id);

		$data_structured = App::getApi('custom_fields.util')->createDataHierarchy($this->custom_data, array($f_def));

		$value = !empty($data_structured[$f_def['id']]) ? $data_structured[$f_def['id']] : null;
		$rendered = $value ? $f_def->getHandler()->renderContext($context, $value) : null;

		return trim($rendered);
	}


	/**
	 * Check if this ticket has a custom field.
	 *
	 * @param $field_id
	 * @return bool
	 */
	public function hasCustomField($field_id)
	{
		foreach ($this->custom_data as $data) {
			if ($data->field['id'] == $field_id) {
				return true;
			}
		}

		foreach ($this->custom_data as $data) {
			if ($data->field->parent AND $data->field->parent['id'] == $field_id) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Gets a display array for a specific field
	 * @param $field_id
	 * @return array|mixed|null
	 */
	public function getCustomFieldDisplayArray($field_id)
	{
		$data = $this->getCustomDataForField($field_id);
		if (!$data) {
			return null;
		}

		$ticket_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
		$ticket_data_structured = App::getApi('custom_fields.util')->createDataHierarchy(array($data), $ticket_field_defs);

		$custom_fields = App::getApi('custom_fields.people')->getFieldsDisplayArray(
			$ticket_field_defs,
			$ticket_data_structured
		);

		$custom_fields = array_pop($custom_fields);

		return $custom_fields;
	}


	/**
	 * Get the primary email address, or null if this person has none.
	 *
	 * @return string
	 */
	public function getPrimaryEmailAddress()
	{
		if (!$this->primary_email) {
			return null;
		}

		return $this->primary_email['email'];
	}

	public function getPrimaryEmail()
	{
		return $this->primary_email;
	}

	public function pickEmailAddress($search)
	{
		$search = strtolower($search);

		if (count($this->emails) == 1 || !trim($search)) {
			return $this->getPrimaryEmailAddress();
		}

		foreach ($this->emails as $e) {
			$email = strtolower($e->email);
			if (strpos($email, $search) !== false) {
				return $email;
			}
		}

		return $this->getPrimaryEmailAddress();
	}


	/**
	 * Alias for getPrimaryEmailAddress
	 *
	 * @return string
	 */
	public function getEmailAddress()
	{
		return $this->getPrimaryEmailAddress();
	}


	/**
	 * @return array
	 */
	public function getEmailAddresses()
	{
		$arr = array();
		foreach ($this->emails as $email) {
			if ($email->is_validated) {
				$arr[] = $email->email;
			}
		}

		return $arr;
	}


	/**
	 * Check if the user has an email address
	 *
	 * @param string $email_address
	 * @return bool
	 */
	public function hasEmailAddress($email_address)
	{
		$email_address = strtolower($email_address);
		if ($this->primary_email && strtolower($this->primary_email->email) == $email_address) {
			return true;
		}

		if ($this->emails) {
			foreach ($this->emails as $email) {
				if (strtolower($email->email) == $email_address) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Get the primary email address ID
	 *
	 * @return int
	 */
	public function getPrimaryEmailId()
	{
		if (!$this->primary_email) {
			return 0;
		}

		return $this->primary_email['id'];
	}


	/**
	 * Sets the primray email address on the account
	 *
	 * @param $email_address
	 * @return PersonEmail
	 */
	public function setEmail($email_address, $validated = false)
	{
		$email = new PersonEmail();
		$email['email'] = $email_address;

		if ($validated) {
			$email['is_validated'] = true;
		}

		$this->addEmailAddress($email);

		$this->setModelField('primary_email', $email);

		return $email;
	}



	/**
	 * Get email addresses that are validated
	 *
	 * @return array
	 */
	public function getValidatedEmails()
	{
		$ret = array();

		foreach ($this->emails as $email) {
			if ($email['is_validated']) {
				$ret[] = $email;
			}
		}

		return $ret;
	}



	/**
	 * Add an email address
	 *
	 * @param PersonEmail $email
	 */
	public function addEmailAddress(PersonEmail $email)
	{
		if (!$this->primary_email && $this->emails->count() < 1) {
			$this->setModelField('primary_email', $email);
		}
		$this->emails->add($email);
		$this->_onPropertyChanged('emails', $this->emails, $this->emails);

		$email->person = $this;

		return $email;
	}


	/**
	 * Adds an emaila ddress string. This is same as addEmailAddress except
	 * we take care of creating the PersonEmail object here.
	 *
	 * @param string $email
	 * @return PersonEmail
	 */
	public function addEmailAddressString($email)
	{
		$email_obj = new PersonEmail();
		$email_obj['email'] = $email;

		$this->addEmailAddress($email_obj);

		return $email_obj;
	}



	/**
	 * Remove an email address from this user.
	 *
	 * The old PersonEmail will be returned.
	 *
	 * If this is the primary email, the next validated email address will be made
	 * primary. If there's no validated, then the next email address. If there are none,
	 * then the primary email is made null.
	 *
	 * @param int $email_id
	 * @return PersonEmail
	 */
	public function removeEmailAddressId($email_id)
	{
		$em = App::getOrm();

		$the_email = null;
		foreach ($this->emails as $index => $email) {
			if ($email['id'] == $email_id) {
				$this->emails->remove($index);
				$em->remove($email);
				$the_email = $email;
				break;
			}
		}

		if ($the_email AND $this->primary_email['id'] == $the_email['id']) {
			$next_email = null;
			$next_valid_email = null;
			foreach ($this->emails as $index => $email) {
				if (!$next_email) $next_email = $email;
				if (!$next_valid_email AND $email['is_validated']) $next_valid_email = $email;

				if ($next_email AND $next_valid_email) break;
			}

			if ($next_valid_email) {
				$this->setModelField('primary_email', $next_valid_email);
				$em->persist($this);
			} else if ($next_email) {
				$this->setModelField('primary_email', $next_email);
				$em->persist($this);
			}
		}

		$this->_onPropertyChanged('emails', $this->emails, $this->emails);

		return $the_email;
	}


	public function getEmailId($email_id)
	{
		foreach ($this->emails as $index => $email) {
			if ($email['id'] == $email_id) {
				return $email;
			}
		}

		return null;
	}


	/**
	 * Get the email record for a specific address
	 *
	 * @return Email
	 */
	public function findEmailAddress($email_address)
	{
		$email_address = strtolower($email_address);

		if ($this->primary_email && strtolower($this->primary_email->email) == $email_address) {
			return $this->primary_email;
		}

		foreach ($this->emails as $email) {
			if (strtolower($email['email']) == $email_address) {
				return $email;
			}
		}

		return null;
	}



	/**
	 * Add a new usergroup
	 *
	 * @param Usergroup $usergroup
	 */
	public function addUsergroup(Usergroup $usergroup)
	{
		foreach ($this->usergroups as $ug) {
			if ($ug->id == $usergroup->id) {
				return false;
			}
		}
		$this['usergroups']->add($usergroup);
		$this->_onPropertyChanged('usergroups', $this->usergroups, $this->usergroups);
		return true;
	}


	/**
	 * Check if hte user belongs to a usergroup
	 *
	 * @param $usergroup
	 * @return bool
	 */
	public function hasUsergroup($usergroup)
	{
		foreach ($this->usergroups as $ug) {
			if ($ug->id == $usergroup->id) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelPerson $label
	 */
	public function addLabel(LabelPerson $label)
	{
		$label['person'] = $this;
		$this->labels->add($label);
		$this->_onPropertyChanged('labels', $this->labels, $this->labels);
	}

	public function getUsergroupSetKey()
	{
		if ($this->usergroups instanceof \Doctrine\Common\Collections\Collection) {
			$this->usergroups = $this->usergroups->toArray();
		}

		return Usergroup::generateUsergroupSetKey($this->usergroups);
	}


	/**
	 * Set the picture blob
	 *
	 * @param \Application\DeskPRO\Entity\Blob $blob
	 */
	public function setPictureBlob(Blob $blob = null)
	{
		$this->setModelField('picture_blob', $blob);
	}



	/**
	 * Sets the gravatar URL
	 *
	 * @param string $url
	 */
	public function setGravatarUrl($url)
	{
		$this->setModelField('gravatar_url', $url);
	}



	/**
	 * Gets the URL to a picture for the person. Note that this will always return
	 * a path to an image, even if it's the default. If you need to check for the
	 * existance of an image, use hasPicture.
	 *
	 * @return null|string
	 */
	public function getPictureUrl($size = 80, $secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$url = false;
		if ($this->hasPicture()) {
			if ($this->picture_blob && $this->picture_blob->isImage()) {
				$url = App::get('router')->generate('serve_blob_sizefit', array(
					'blob_auth_id' => $this->picture_blob->getAuthId(),
					'filename' => $this->picture_blob->filename,
					's' => $size,
				), true);

			} elseif (App::getSetting('core.use_gravatar') && $this->primary_email && $this->primary_email->getId()) {
				$url = $this->getGravatarUrl($size, $secure);
			}
		}

		if (!$url) {
			if ($this->organization && $this->organization->hasPicture()) {
				return $this->organization->getPictureUrl($size, $secure);
			} else {
				$url = App::get('router')->generate('serve_default_picture', array(
					's' => $size,
					'size-fit' => 1,
				), true);
			}
		}

		if ($secure) {
			$url = preg_replace('#^http:#', 'https:', $url);
		}

		return $url;
	}

	public function getGravatarUrl($size = 80, $secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$url = $this->primary_email ? $this->primary_email->getGravatarUrl($secure) : '';
		if ($size != 80) {
			$url .= '&s=' . $size;
		}

		if ($this->organization && $this->organization->hasPicture()) {
			$url .= "&d=" . urlencode($this->organization->getPictureUrl($size, $secure));
		} else {
			if ($this->is_agent) {
				$url .= '&d=mm';
			} else {
				$url .= '&d=mm';
			}
		}

		return $url;
	}



	/**
	 * Does this user have a picture associated with their account?
	 *
	 * @return bool
	 */
	public function hasPicture($auto_check = false)
	{
		if ($this->disable_picture) {
			return false;
		}

		if ($this->picture_blob || $this->gravatar_url) {
			return true;
		}

		if ($this->primary_email AND App::getSetting('core.use_gravatar')) {
			return true;
		}

		return false;
	}



	/**
	 * Is this a newly created person?
	 *
	 * It's important to realize the context of when a person is "new." This simply means
	 * that THIS object is new (was created with a constructor). If the object is persisted,
	 * then the person gains an ID etc but is still "new".
	 *
	 * As soon as the EntityManager
	 * loses the map (eg. page is refreshed, command ends, or the EM is clear()ed), then
	 * the person is no longer considered new, because they will have been hydrated and the
	 * constructor not called.
	 *
	 * @return bool
	 */
	public function isNewPerson()
	{
		return $this->_is_new_person;
	}



	/**
	 * Set this persons organization and position.
	 *
	 * @param Organization $org
	 * @param string $position
	 * @param bool $manager
	 */
	public function setOrganization(Organization $org = null, $position = '', $manager = false)
	{
		$this->_updated_org = true;
		if (!$org) {
			$this->setModelField('organization', $org);
			$this->setModelField('organization_position', '');
			$this->setModelField('organization_manager', false);
			return;
		}

		$this->setModelField('organization', $org);
		$this->setModelField('organization_position', $position);
		$this->setModelField('organization_manager', (bool)$manager);
	}

	public function getTwitterAccountIds()
	{
		$output = array();
		foreach ($this->twitter_accounts AS $account) {
			$output[] = $account['id'];
		}
		return $output;
	}

	/**
	 * @return TwitterAccount[]
	 */
	public function getTwitterAccounts()
	{
		return $this->twitter_accounts;
	}

	public function __toString()
	{
		return $this->getDisplayName();
	}



	public function getKeys()
	{
		$keys = parent::getKeys();
		$keys[] = 'display_name';

		return $keys;
	}

	public function setName($name)
	{
		$name = preg_replace('# {2,}#', ' ', $name);
		$this->setModelField('name', $name);

		$parts = Strings::rexplode(' ', $name, 2);
		$this->setModelField('first_name', $parts[0]);
		$this->setModelField('last_name', isset($parts[1]) ? $parts[1] : '');
	}

	public function setFirstName($name)
	{
		$this->setModelField('first_name', $name);
		$this->setModelField('name', $name . ' ' . $this->last_name);
	}

	public function setLastName($name)
	{
		$this->setModelField('last_name', $name);
		$this->setModelField('name', $this->first_name . ' ' . $name);
	}

	/**
	 * Set the last time this usersource was used.
	 *
	 * @param DateTime $time The time to set, or null to set now
	 */
	public function setLastLoginAt(\DateTime $time = null)
	{
		if (!$time) $time = new \DateTime();

		$this->setModelField('date_last_login', $time);
	}

	public function setDisablePicture($yn = false)
	{
		$this->setModelField('disable_picture', $yn);

		if ($yn) {
			$this['picture_blob'] = null;
		}
	}



	/**
	 * Get a Person ID from some parameter that might be a person, already a
	 * person ID, or some object that knows about a person ID.
	 *
	 * @param mixed $person
	 * @return int
	 */
	public static function smartPersonId($person)
	{
		if (is_int($person)) {
			return $person;
		} elseif (ctype_digit($person)) {
			return (int)$person;
		} elseif (\is_object($person)) {
			if ($person instanceof Person) {
				return (int)$person['id'];
			}
		} elseif (isset($person['person_id'])) {
			return (int)$person['person_id'];
		}

		return null;
	}



	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelPerson');
		}

		return $this->_label_manager;
	}



	/**
	 * @return \Application\DeskPRO\People\Helpers\PermissionsManager
	 */
	public function getPermissionsManager()
	{
		if ($this->_permissions_manager === null) {
			$this->loadHelper('PermissionsManager');
			$this->_permissions_manager = $this->getHelper('PermissionsManager');
		}

		return $this->_permissions_manager;
	}



	public function getHelperManager()
	{
		if ($this->_helper_manager === null) {
			$this->_helper_manager = new \Orb\Helper\HelperManager();
		}

		return $this->_helper_manager;
	}

	public function getPersonLogger()
	{
		return $this->_person_logger;
	}



	/**
	 */
	public function _savePersonLogs()
	{
		if ($this->_person_logger) {
			$this->_person_logger->done();
			$this->_person_logger = null;
			if ($this->_person_logger) {
				$this->removePropertyChangedListener($this->_person_logger);
			}
			$this->_initPersonLogger();
		}

		if ($this->_updated_org) {
			$new_org = $this->organization ? $this->organization->getId() : null;
			App::getDb()->update('tickets', array('organization_id' => $new_org), array('person_id' => $this->id));
			App::getDb()->update('tickets_search_active', array('organization_id' => $new_org), array('person_id' => $this->id));
		}
	}

	public function _presavePerson()
	{
		if ($this->_person_logger) {
			$this->_person_logger->preSave();
		}
	}

	public function setTimezone($tz)
	{
		$tz = trim($tz);
		if (!$tz) {
			$tz = 'UTC';
		}

		// Make sure its valid
		try {
			$dt = new \DateTimeZone($tz);
		} catch (\Exception $e) {
			$tz = 'UTC';
		}

		$this->setModelField('timezone', $tz);
	}

	public function getTimezone()
	{
		if (!$this->timezone) {
			return 'UTC';
		}

		return $this->timezone;
	}

	public function getRealTimezone()
	{
		return $this->timezone;
	}

	public function getDateTimezone()
	{
		try {
			return new \DateTimeZone($this->getTimezone());
		} catch (\Exception $e) {
			return new \DateTimeZone('UTC');
		}
	}

	public function getDateTime()
	{
		return new \DateTime("now", $this->getDateTimezone());
	}

	public function getDateForTime($time)
	{
		return new \DateTime($time, $this->getDateTimezone());
	}

	public function getTimezoneOffset($as_string = false)
	{
		$user_offset = $this->getDateTimezone()->getOffset(new \DateTime("now"));
		$user_offset /= 3600; //hours

		if ($as_string) {
			if ($user_offset >= 0) {
				$user_offset = "+$user_offset";
			} else {
				$user_offset = "$user_offset";
			}
		}

		return $user_offset;
	}

	/**
	 * @return int
	 */
	public function getTimezoneOffsetSeconds()
	{
		return $this->getTimezoneOffset() * 3600;
	}


	/**
	 * @param bool $val
	 * @param string $reason
	 */
	public function setDisableAutoresponses($val, $reason = null)
	{
		$val = (bool)$val;

		$this->setModelField('disable_autoresponses', $val);
		if (!$val) {
			$this->setModelField('disable_autoresponses_log', null);
		} else {
			if (!$reason) {
				$reason = 'Unknown';
			}
			$reason .= ' (' . date('M j Y @ H:i') . ' UTC)';
			$this->setModelField('disable_autoresponses_log', $reason);
		}
	}

	/**
	 * @param string $organization_position
	 */
	public function setOrganizationPosition($organization_position)
	{
		if (!$organization_position) {
			$organization_position = '';
		}
		$this->setModelField('organization_position', $organization_position);
	}

	public function hasSla(Sla $sla)
	{
		foreach ($this->slas AS $person_sla) {
			if ($person_sla->id == $sla->id) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @return string
	 */
	public function getRememberMeCookieCode()
	{
		return \Orb\Util\Util::generateStaticSecurityToken(sha1(App::getAppSecret() . $this->secret_string));
	}

	/**
	 * @param $code
	 * @return bool
	 */
	public function validateRememberMeCookieCode($code)
	{
		return \Orb\Util\Util::checkStaticSecurityToken($code, sha1(App::getAppSecret() . $this->secret_string));
	}

	public function _postPersist()
	{
		// Unset permissions manager so it'll be relaoded now that the user is registered
		$this->_permissions_manager = null;
	}

	/**
	 * @return \Application\DeskPRO\People\PersonChangeTracker
	 */
	public function getChangeTracker()
	{
		$this->_initPersonLogger();
		return $this->_person_logger;
	}

	public function getDataForWidget()
	{
		$data = array();
		foreach (array('id', 'name', 'first_name', 'last_name', 'title_prefix', 'creation_system', 'organization_position') AS $key) {
			$data[$key] = $this->$key;
		}
		$data['date_created'] = $this->date_created->getTimestamp();
		$data['email'] = $this->getPrimaryEmailAddress();

		if ($this->organization) {
			$data['organization'] = array('id' => $this->organization->id, 'name' => $this->organization->name);
		}
		if ($this->language) {
			$data['language'] = array('id' => $this->language->id, 'title' => $this->language->title);
		}

		if (count($this->labels)) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		$customFields = App::getSystemService('person_fields_manager')->getDisplayArrayForObject($this);
		$data['custom'] = array();
		foreach ($customFields AS $field) {
			$data['custom'][$field['id']] = array(
				'id' => $field['id'],
				'title' => $field['title'],
				'value' => isset($field['value']['value']) ? $field['value']['value'] : false
			);
		}

		return $data;
	}

	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		if ($this->organization) {
			$data['organization_usergroups'] = array();
			foreach ($this->organization->usergroups AS $group) {
				$data['organization_usergroups'][] = $group->toApiData(false, false, $visited);
			}

			if (empty($data['organization'])) {
				$data['organization'] = $this->organization->toApiData($primary, $deep, $visited);
			}
		}

		$data['display_name'] = $this->getDisplayName();
		$data['primary_email'] = $this->getPrimaryEmailAddress();
		$data['picture_url'] = $this->getPictureUrl();


		// Render custom fields to text values
		$field_manager = App::getContainer()->getSystemService('person_fields_manager');
		$field_manager->addApiData($this, $data);

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Person';
		$metadata->setPrimaryTable(array(
			'name' => 'people',
			'indexes' => array(
				'is_agent_idx' => array( 'columns' => array( 0 => 'is_agent', ), ),
				'is_confirmed_idx' => array( 'columns' => array( 0 => 'is_confirmed', ), ),
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('_initPersonLogger', 'postLoad');
		$metadata->addLifecycleCallback('_presavePerson', 'prePersist');
		$metadata->addLifecycleCallback('_postPersist', 'postPersist');
		$metadata->addLifecycleCallback('_savePersonLogs', 'postPersist');
		$metadata->addLifecycleCallback('_savePersonLogs', 'postUpdate');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'gravatar_url', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'gravatar_url', ));
		$metadata->mapField(array( 'fieldName' => 'disable_picture', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'disable_picture', ));
		$metadata->mapField(array( 'fieldName' => 'is_contact', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_contact', ));
		$metadata->mapField(array( 'fieldName' => 'is_user', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_user', ));
		$metadata->mapField(array( 'fieldName' => 'is_agent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent', ));
		$metadata->mapField(array( 'fieldName' => 'was_agent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'was_agent', ));
		$metadata->mapField(array( 'fieldName' => 'can_agent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'can_agent', ));
		$metadata->mapField(array( 'fieldName' => 'can_admin', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'can_admin', ));
		$metadata->mapField(array( 'fieldName' => 'can_billing', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'can_billing', ));
		$metadata->mapField(array( 'fieldName' => 'can_reports', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'can_reports', ));
		$metadata->mapField(array( 'fieldName' => 'is_vacation_mode', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_vacation_mode', ));
		$metadata->mapField(array( 'fieldName' => 'disable_autoresponses', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'disable_autoresponses', ));
		$metadata->mapField(array( 'fieldName' => 'disable_autoresponses_log', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'disable_autoresponses_log', ));
		$metadata->mapField(array( 'fieldName' => 'is_confirmed', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_confirmed', ));
		$metadata->mapField(array( 'fieldName' => 'is_agent_confirmed', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent_confirmed', ));
		$metadata->mapField(array( 'fieldName' => 'is_deleted', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_deleted', ));
		$metadata->mapField(array( 'fieldName' => 'is_disabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_disabled', ));
		$metadata->mapField(array( 'fieldName' => 'importance', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'importance', ));
		$metadata->mapField(array( 'fieldName' => 'creation_system', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'creation_system', ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'first_name', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'first_name', ));
		$metadata->mapField(array( 'fieldName' => 'last_name', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_name', ));
		$metadata->mapField(array( 'fieldName' => 'title_prefix', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title_prefix', ));
		$metadata->mapField(array( 'fieldName' => 'override_display_name', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'override_display_name', ));
		$metadata->mapField(array( 'fieldName' => 'summary', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'summary', ));
		$metadata->mapField(array( 'fieldName' => 'secret_string', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'secret_string', 'dpqlAccess' => false, 'dpApi' => false, ));
		$metadata->mapField(array( 'fieldName' => 'organization_position', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'organization_position', ));
		$metadata->mapField(array( 'fieldName' => 'organization_manager', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'organization_manager', ));
		$metadata->mapField(array( 'fieldName' => 'timezone', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'timezone', ));
		$metadata->mapField(array( 'fieldName' => 'password', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'password', 'dpqlAccess' => false, 'dpApi' => false, ));
		$metadata->mapField(array( 'fieldName' => 'password_scheme', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'password_scheme', 'dpqlAccess' => false, 'dpApi' => false, ));
		$metadata->mapField(array( 'fieldName' => 'salt', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'salt', 'dpqlAccess' => false, 'dpApi' => false, ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_last_login', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_last_login', ));
		$metadata->mapField(array( 'fieldName' => 'date_picture_check', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_picture_check', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		/**
		 * The Other Guys
		 * #201401221859 @ Frankie -- DPQL Field mapping people table to department.title (many to one)
		 */
		$metadata->mapManyToOne(array( 'fieldName' => 'department', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Department', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'department_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ) ), 'dpApi' => true ));

		$metadata->mapManyToOne(array( 'fieldName' => 'picture_blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'picture_blob_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), )  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'organization', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Organization', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'primary_email', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonEmail', 'mappedBy' => NULL, 'inversedBy' => NULL, 'fetch' => ClassMetadata::FETCH_EAGER, 'joinColumns' => array( 0 => array( 'name' => 'primary_email_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'emails', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonEmail', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'person', 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelPerson', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'person', 'orphanRemoval' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataPerson', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'person', 'orphanRemoval' => true, 'dpApi' => false));
		$metadata->mapOneToMany(array( 'fieldName' => 'contact_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonContactData', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'person', 'indexBy' => 'id', 'dpApi' => true, 'dpApiDeep' => true ));
		$metadata->mapManyToMany(array( 'fieldName' => 'usergroups', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'cascade' => array('persist','merge'), 'joinTable' => array( 'name' => 'person2usergroups', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'preferences', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonPref', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'person',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'usersource_assoc', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonUsersourceAssoc', 'mappedBy' => 'person',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'twitter_users', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonTwitterUser', 'mappedBy' => 'person',  ));
		$metadata->mapManyToMany(array( 'fieldName' => 'slas', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Sla', 'cascade' => array('persist','merge'), 'mappedBy' => 'people', 'dpApi' => true));
		$metadata->mapManyToMany(array( 'fieldName' => 'twitter_accounts', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccount', 'mappedBy' => 'persons' ));
	}
}

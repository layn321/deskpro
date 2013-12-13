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
use Orb\Util\Dates;
use Orb\Util\WorkHoursSet;

/**
 * Ticket triggers
 *
 */
class TicketTrigger extends \Application\DeskPRO\Domain\DomainObject
{
	const EVENT_NEW                   = 'new';
	const EVENT_NEW_EMAIL             = 'new.email';
	const EVENT_NEW_EMAIL_USER        = 'new.email.user';
	const EVENT_NEW_EMAIL_AGENT       = 'new.email.agent';
	const EVENT_NEW_WEB               = 'new.web';
	const EVENT_NEW_WEB_AGENT         = 'new.web.agent';
	const EVENT_NEW_WEB_AGENT_PORTAL  = 'new.web.agent.portal';
	const EVENT_NEW_WEB_USER          = 'new.web.user';
	const EVENT_NEW_WEB_USER_PORTAL   = 'new.web.user.portal';
	const EVENT_NEW_WEB_USER_WIDGET   = 'new.web.user.widget';
	const EVENT_NEW_WEB_USER_FORM     = 'new.web.user.embed';
	const EVENT_NEW_WEB_API           = 'new.web.api';
	const EVENT_UPDATE                = 'update';
	const EVENT_UPDATE_AGENT          = 'update.agent';
	const EVENT_UPDATE_USER           = 'update.user';
	const EVENT_UPDATE_API            = 'update.api';

	const EVENT_TIME_OPEN                  = 'time.open';
	const EVENT_TIME_USER_WAITING          = 'time.user_waiting';
	const EVENT_TIME_TOTAL_USER_WAITING    = 'time.total_user_waiting';
	const EVENT_TIME_AGENT_WAITING         = 'time.agent_waiting';
	const EVENT_TIME_RESOLVED              = 'time.resolved';

	const EVENT_SLA_WARNING = 'sla.warning';
	const EVENT_SLA_FAIL    = 'sla.fail';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $event_trigger;

	/**
	 * @var array
	 */
	protected $event_trigger_options = null;

	/**
	 * @var bool
	 */
	protected $is_enabled = true;

	/**
	 * @var bool
	 */
	protected $is_uneditable = false;

	/**
	 * @var string
	 */
	protected $terms = array();

	/**
	 * @var string
	 */
	protected $terms_any = array();

	/**
	 * @var string
	 */
	protected $actions = array();

	/**
	 * When non-null, the group is a special system trigger (hidden from most interfaces).
	 * Used prefixes: "urgency." for urgency-type triggers.
	 *
	 * @var bool
	 */
	protected $sys_name = null;

	/**
	 * @var int
	 */
	protected $run_order = 0;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketActions\ActionsCollection
	 */
	protected $_ticket_actions_coll;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketTerms
	 */
	protected $_ticket_terms;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketTerms
	 */
	protected $_ticket_terms_any;

	public function __construct()
	{
		$this->date_created = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get a searcher with the criteria terms. This is used in th
	 */
	public function getSearcher()
	{
		$searcher      = new \Application\DeskPRO\Searcher\TicketSearch();
		$user_searcher = new \Application\DeskPRO\Searcher\PersonSearch();
		$org_searcher  = new \Application\DeskPRO\Searcher\OrganizationSearch();

		$has_user_terms = false;
		$has_org_terms  = false;

		foreach ($this->terms as $term) {
			if ($term['op'] != 'ignore') {
				if (strpos($term['type'], 'person_') === 0) {
					$user_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_user_terms = true;
				} elseif (strpos($term['type'], 'org_') === 0) {
					$org_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_org_terms = true;
				} else {
					$searcher->addTerm($term['type'], $term['op'], $term['options']);
				}
			}
		}
		foreach ($this->terms_any as $term) {
			if ($term['op'] != 'ignore') {
				if (strpos($term['type'], 'person_') === 0) {
					$user_searcher->addAnyTerm($term['type'], $term['op'], $term['options']);
					$has_user_terms = true;
				} elseif (strpos($term['type'], 'org_') === 0) {
					$org_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_org_terms = true;
				} else {
					$searcher->addAnyTerm($term['type'], $term['op'], $term['options']);
				}
			}
		}

		if ($has_user_terms) {
			$searcher->setPersonSearch($user_searcher);
		}
		if ($has_org_terms) {
			$searcher->setOrganizationSearch($org_searcher);
		}

		$time_secs = $this->getOptionSeconds();

		$searcher->addRawWhere("tickets.date_created >= '" . $this->date_created->format('Y-m-d H:i:s') . "'");

		switch ($this->event_trigger) {
			case self::EVENT_TIME_OPEN:
				$searcher->addRawWhere('tickets.status IN (\'awaiting_user\', \'awaiting_agent\')');
				$date_cut = new \DateTime('-' . $time_secs . ' seconds');
				$searcher->addTerm('date_created', 'lte', array('date1' => $date_cut));

				break;

			case self::EVENT_TIME_USER_WAITING:
				$searcher->addTerm('status', 'is', array('awaiting_agent'));
				$searcher->addRawWhere('tickets.date_user_waiting IS NOT NULL');

				$date_cut = new \DateTime('-' . $time_secs . ' seconds');
				$searcher->addTerm('user_waiting', 'lte', array('date1' => $date_cut));

				break;

			case self::EVENT_TIME_TOTAL_USER_WAITING:
				$searcher->addTerm('status', 'is', array('awaiting_agent'));
				$searcher->addRawWhere('tickets.date_user_waiting IS NOT NULL');
				$searcher->addTerm('total_user_waiting', 'between', array($time_secs, $time_secs));
				break;

			case self::EVENT_TIME_AGENT_WAITING:
				$searcher->addTerm('status', 'is', array('awaiting_user'));
				$searcher->addRawWhere('tickets.date_agent_waiting IS NOT NULL');

				$date_cut = new \DateTime('-' . $time_secs . ' seconds');
				$searcher->addTerm('agent_waiting', 'lte', array('date1' => $date_cut));

				break;

			case self::EVENT_TIME_RESOLVED:
				$searcher->addTerm('status', 'is', array('resolved'));
				$searcher->addRawWhere('tickets.date_resolved IS NOT NULL');

				$date_cut = new \DateTime('-' . $time_secs . ' seconds');
				$searcher->addTerm('date_resolved', 'lte', array('date1' => $date_cut));

				break;

			default:
				$searcher->addRawWhere('0');
		}

		return $searcher;
	}

	/**
	 * Gets the relevant time field on ticket for a particular ticket trigger.
	 * For example, 'EVENT_TIME_USER_WAITING' is dependant on ticket.date_user_waiting
	 *
	 * @return string
	 */
	public function getTicketTimeField()
	{
		switch ($this->event_trigger) {
			case self::EVENT_TIME_OPEN:
				return 'date_created';

			case self::EVENT_TIME_USER_WAITING:
			case self::EVENT_TIME_TOTAL_USER_WAITING:
				return 'date_user_waiting';

			case self::EVENT_TIME_AGENT_WAITING:
				return 'date_agent_waiting';
				break;

			case self::EVENT_TIME_RESOLVED:
				return 'date_resolved';
		}

		return null;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketTerms
	 */
	public function getAllTicketTerms()
	{
		if ($this->_ticket_terms) return $this->_ticket_terms;
		$this->terms = (array)$this->terms;

		$ticket_terms = new \Application\DeskPRO\Tickets\TicketTerms($this->terms);

		$this->_ticket_terms = $ticket_terms;
		return $this->_ticket_terms;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketTerms
	 */
	public function getAnyTicketTerms()
	{
		if ($this->_ticket_terms_any) return $this->_ticket_terms_any;
		$this->terms_any = (array)$this->terms_any;

		$ticket_terms = new \Application\DeskPRO\Tickets\TicketTerms($this->terms_any);

		$this->_ticket_terms_any = $ticket_terms;
		return $this->_ticket_terms_any;
	}


	/**
	 * Check to see if a ticket matches
	 *
	 * @param Ticket $ticket
	 * @return bool
	 */
	public function isTriggerMatch(Ticket $ticket, \Application\DeskPRO\Tickets\TicketChangeTracker $tracker)
	{
		$ticket_terms = $this->getAllTicketTerms();
		$ticket_terms->setChangeTracker($tracker);

		$match = $ticket_terms->doesTicketMatch($ticket);

		if (!$match) {
			return false;
		}

		if (!$this->terms_any) {
			$any_match = true;
		} else {
			$any_ticket_terms = $this->getAnyTicketTerms();
			$any_ticket_terms->setChangeTracker($tracker);
			$any_match = $any_ticket_terms->doesTicketMatchAny($ticket);
		}

		return $match && $any_match;
	}


	/**
	 * @return array
	 */
	public function getAllTermDescriptions($as_html = false)
	{
		$descs = $this->getAllTicketTerms()->getDescriptions($as_html);
		return $descs;
	}


	/**
	 * @return array
	 */
	public function getAnyTermDescriptions($as_html = false)
	{
		$descs = $this->getAnyTicketTerms()->getDescriptions($as_html);
		return $descs;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionsCollection
	 */
	public function getTicketActionsCollection()
	{
		if ($this->_ticket_actions_coll) return $this->_ticket_actions_coll;

		$factory = new \Application\DeskPRO\Tickets\TicketActions\ActionsFactory();
		$actions_collection = new \Application\DeskPRO\Tickets\TicketActions\ActionsCollection();

		foreach ($this->actions as $action_info) {
			$action = $factory->createFromInfo($action_info);
			if ($action) {
				if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\AbstractAction) {
					$action->setTrigger($this);
				}
				$actions_collection->add($action);
			}
		}

		$this->_ticket_actions_coll = $actions_collection;
		return $this->_ticket_actions_coll;
	}


	/**
	 * @return array
	 */
	public function getActionDescriptions($as_html = true)
	{
		return $this->getTicketActionsCollection()->getDescriptions($as_html);
	}


	/**
	 * @return string
	 */
	public function getTriggerType()
	{
		if (strpos($this->event_trigger, 'time.') === 0) {
			return 'escalation';
		} else if (strpos($this->event_trigger, 'sla.') === 0) {
			return 'escalation';
		} else {
			return 'trigger';
		}
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setEventTriggerOption($name, $value)
	{
		$opt = $this->event_trigger_options;
		if (!$opt) {
			$opt = array();
		}

		if ($value === null) {
			if (isset($opt[$name])) {
				unset($opt[$name]);
				$this->setModelField('event_trigger_options', $opt);
			}
		} else {
			$opt[$name] = $value;
			$this->setModelField('event_trigger_options', $opt);
		}

		// For time fields, the run order is based off their time.
		// Admins never see this num so its perfect to keep ordering queryies
		// the same everywhere.
		if ($name == 'time') {

			$this->setModelField('run_order', $this->getOptionSeconds());
		}
	}


	/**
	 * Get an event trigger option
	 *
	 * @param string $name
	 * @param null $default
	 */
	public function getEventTriggerOption($name, $default = null)
	{
		if (!$this->event_trigger_options || !isset($this->event_trigger_options[$name])) {
			return $default;
		}

		return $this->event_trigger_options[$name];
	}

	/**
	 * @param null $idx
	 * @return array|null
	 */
	public function getEventTriggerPath($idx = null)
	{
		$parts = explode('.', $this->event_trigger);
		if ($idx === null) {
			return $parts;
		}

		return isset($parts[$idx]) ? $parts[$idx] : null;
	}


	/**
	 * @return array|null
	 */
	public function getMasterEvent()
	{
		return $this->getEventTriggerPath(0);
	}


	/**
	 * @return array|null
	 */
	public function getSubEvent()
	{
		return $this->getEventTriggerPath(1);
	}


	/**
	 * @param array $terms
	 */
	public function setTerms(array $terms)
	{
		$this->setModelField('terms', $terms);
		$this->_ticket_terms = null;
	}


	/**
	 * Is the trigger un-editable? Some system triggers are referenced elsewhere
	 * and shouldnt have additional actions appended to them.
	 *
	 * @return bool
	 */
	public function isUneditable()
	{
		if (!$this->sys_name) {
			return false;
		}

		return $this->is_uneditable;
	}


	/**
	 * Get the phrase name for the top-level type
	 *
	 * @return string
	 */
	public function getSysPhraseName()
	{
		return 'agent.general.triggers_' . str_replace('.', '_', $this->sys_name);
	}


	############################################################################
	# Escalation-related
	############################################################################

	/**
	 * Get the time portion of the time option. Eg: 1 days, returns 1
	 *
	 * @return int
	 */
	public function getOptionTime()
	{
		if (!$this->event_trigger_options || !isset($this->event_trigger_options['time'])) {
			return 0;
		}

		if (strpos($this->event_trigger_options['time'], ' ') === false) {
			return $this->event_trigger_options['time'];
		}

		list ($time, ) = explode(' ', $this->event_trigger_options['time']);
		return $time;
	}


	/**
	 * Get the scale portion of the time option. Eg: 1 days returns days
	 *
	 * @return string
	 */
	public function getOptionScale()
	{
		if (!isset($this->event_trigger_options['time']) || !$this->event_trigger_options['time'] || strpos($this->event_trigger_options['time'], ' ') === false) {
			return 'seconds';
		}

		list (, $scale) = explode(' ', $this->event_trigger_options['time']);
		return $scale;
	}


	/**
	 * get the time option in seconds.
	 *
	 * @return int
	 */
	public function getOptionSeconds(WorkHoursSet $hours_set = null)
	{
		$time = $this->getOptionTime();
		$scale = $this->getOptionScale();

		$secs = 0;

		switch ($scale) {

			case 'minutes':
				$secs = $time * Dates::SECS_MIN;
				break;

			case 'hours':
				$secs = $time * Dates::SECS_HOUR;
				break;

			case 'days':
				if ($hours_set) {
					$secs = $time * $hours_set->getSecondsPerDay();
				} else {
					$secs = $time * Dates::SECS_DAY;
				}
				break;

			case 'weeks':
				if ($hours_set) {
					$secs = $time * $hours_set->getSecondsPerWeek();
				} else {
					$secs = $time * Dates::SECS_WEEK;
				}
				break;

			case 'months':
				if ($hours_set) {
					$secs = $time * ($hours_set->getSecondsPerWeek() * 4);
				} else {
					$secs = $time * Dates::SECS_MONTH;
				}
				break;

			default:
				$secs = $time;
				break;
		}

		return $secs;
	}


	/**
	 * Given a creation_system string, get the scoped event name
	 * for the 'new ticket' event.
	 *
	 * @param string $creation_system
	 * @return string
	 */
	public static function getNewTicketEventName($creation_system)
	{
		switch ($creation_system) {
			case Ticket::CREATED_GATEWAY_PERSON:
				$event = 'new.email.user';
				break;
			case Ticket::CREATED_WEB_PERSON_PORTAL:
				$event = 'new.web.user.portal';
				break;
			case Ticket::CREATED_WEB_PERSON_EMBED:
				$event = 'new.web.user.embed';
				break;
			case Ticket::CREATED_WEB_PERSON_WIDGET:
				$event = 'new.web.user.widget';
				break;
			case Ticket::CREATED_GATEWAY_AGENT:
				$event = 'new.email.agent';
				break;
			case Ticket::CREATED_WEB_AGENT_PORTAL:
				$event = 'new.web.agent.portal';
				break;
			case Ticket::CREATED_WEB_API:
				$event = 'new.web.api';
				break;
			default:
				$event = 'new';
		}

		return $event;
	}


	public static function passActionsArray($actions_array, array &$info = null)
	{
		if ($info === null) {
			$info = array();
		}

		$actions = $actions_array;

		$redirect_to = null;
		$tpl_types = array(
			'set_user_email_template_newticket' => 1,
			'user_newticket_agent' => 1,
			'set_user_email_template_newticket_validate' => 1,
			'set_agent_email_template_newticket' => 1,
			'set_user_email_template_newticket_agent' => 1,
			'set_user_email_template_newreply_agent' => 1,
			'set_agent_email_template_newreply_agent' => 1,
			'set_user_email_template_newreply_user' => 1,
			'set_agent_email_template_newreply_user' => 1,
			'send_user_email' => 1,
			'send_agent_email' => 1,
			'send_autoclose_warn_email' => 1,
		);
		foreach ($actions as &$_info) {
			if (isset($tpl_types[$_info['type']])) {
				if (isset($_info['options']['new_option']) && !empty($_info['options']['new_option'])) {
					$new_name = $_info['options']['new_option'];
					$new_name = preg_replace('#[^a-zA-Z0-9\-_]#', '_', $new_name);
					if (!$new_name) {
						$new_name = 'custom_template';
					}

					unset($_info['options']['new_option']);

					if (strpos($_info['type'], 'set_user_') !== false || strpos($_info['type'], 'send_user_email') !== false || $_info['type'] == 'send_autoclose_warn_email') {
						$_info['options']['template_name'] = 'DeskPRO:emails_user:custom_' . $new_name . '.html.twig';
					} else {
						$_info['options']['template_name'] = 'DeskPRO:emails_agent:custom_' . $new_name . '.html.twig';
					}

					$variant = null;

					switch ($_info['type']) {
						case 'set_user_email_template_newticket': $variant = 'DeskPRO:emails_user:new-ticket.html.twig'; break;
						case 'user_newticket_agent': $variant = 'DeskPRO:emails_user:new-ticket-agent.html.twig'; break;
						case 'set_user_email_template_newticket_validate': $variant = 'DeskPRO:emails_user:new-ticket-validate.html.twig'; break;
						case 'set_agent_email_template_newticket': $variant = 'DeskPRO:emails_agent:new-ticket.html.twig'; break;
						case 'set_user_email_template_newticket_agent': $variant = 'DeskPRO:emails_user:new-ticket-agent.html.twig'; break;
						case 'set_user_email_template_newreply_agent': $variant = 'DeskPRO:emails_user:new-reply-agent.html.twig'; break;
						case 'set_agent_email_template_newreply_agent': $variant = 'DeskPRO:emails_agent:new-reply-agent.html.twig'; break;
						case 'set_user_email_template_newreply_user': $variant = 'DeskPRO:emails_user:new-reply-user.html.twig'; break;
						case 'set_agent_email_template_newreply_user': $variant = 'DeskPRO:emails_agent:new-reply-user.html.twig'; break;
						case 'send_user_email': $variant = 'DeskPRO:emails_user:blank.html.twig'; break;
						case 'send_agent_email': $variant = 'DeskPRO:emails_agent:blank.html.twig'; break;
						case 'send_autoclose_warn_email': $variant = 'DeskPRO:emails_user:ticket-autoclose-warn.html.twig'; break;
					}

					$template = App::getOrm()->getRepository('DeskPRO:Template')->findOneBy(array('name' => $new_name));

					// A new variation
					if (!$template && $variant && in_array($variant, App::getTemplating()->getVariedTemplateNames())) {
						$template = new Template();
						$template->style = App::getContainer()->getSystemService('style');
						$template->variant_of = $variant;

						$name = preg_replace('#[^a-zA-Z0-9\-_]#', '_', $new_name);
						$nameparts = explode(':', $template->variant_of);
						array_pop($nameparts);

						$name = implode(':', $nameparts) . ':custom_' . $name . '.html.twig';
						$template->name = $name;

						$code = App::getTemplating()->getSource($template->variant_of);
						$code = preg_replace('#\{%\s*include\s+(\'|")(.*?)(\'|")\s+#', '{% include \'$2\' ignore missing ', $code);

						$twig = App::getContainer()->get('twig');
						$compiled = $twig->compileSource($code, $name);
						$template->setTemplate($code, $compiled);

						App::getOrm()->persist($template);
						App::getOrm()->flush($template);

						if (!isset($info['new_templates'])) {
							$info['new_templates'][] = $name;
						}
					}
				}
			}
		}

		return $actions;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketTrigger';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_triggers', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'event_trigger', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'event_trigger', ));
		$metadata->mapField(array( 'fieldName' => 'event_trigger_options', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'event_trigger_options', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'is_uneditable', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_uneditable', ));
		$metadata->mapField(array( 'fieldName' => 'terms', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'terms', ));
		$metadata->mapField(array( 'fieldName' => 'terms_any', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'terms_any', ));
		$metadata->mapField(array( 'fieldName' => 'actions', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'actions', ));
		$metadata->mapField(array( 'fieldName' => 'sys_name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'sys_name', ));
		$metadata->mapField(array( 'fieldName' => 'run_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'run_order', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}

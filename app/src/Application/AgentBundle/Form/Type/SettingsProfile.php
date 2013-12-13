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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Form\Type;

use Application\DeskPRO\App;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SettingsProfile extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
    {
		$builder->add('name', 'text', array('required' => false));
	    $builder->add('override_display_name', 'text', array('required' => false));
		$builder->add('email', 'text', array('required' => false));
		$builder->add('timezone', 'choice', array(
			'choices' => array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers())
		));

		$lang_names = array();
		foreach (App::getDataService('Language')->getAll() as $l) {
			$lang_names[$l->getId()] = App::getTranslator()->getPhraseObject($l, 'title');
		}
		$builder->add('language_id', 'choice', array(
			'choices' => $lang_names
		));
		$builder->add('password', 'password', array('required' => false));
		$builder->add('password2', 'password', array('required' => false));

	    $builder->add('ticket_close_reply', 'checkbox', array('required' => false));
	    $builder->add('ticket_close_note', 'checkbox', array('required' => false));
	    $builder->add('hide_claimed_chat', 'checkbox', array('required' => false));
		$builder->add('ticket_go_next_reply', 'checkbox', array('required' => false));
		$builder->add('ticket_reverse_order', 'checkbox', array('required' => false));

		$builder->add('reset_api_token', 'hidden', array('required' => false));

		$builder->add('default_team_id', 'hidden', array('required' => false));

		$builder->add('new_picture_blob_id', 'hidden', array('required' => false));

		$builder->add('auto_dismiss_notifications', 'choice', array(
			'choices' => array(
				5 => '5 seconds',
				10 => '10 seconds',
				15 => '15 seconds',
				30 => '30 seconds',
				60 => '1 minute',
				120 => '2 minutes',
				300 => '5 minutes',
				900 => '15 minutes',
				1800 => '30 minutes',
				3600 => '1 hour',
				0 => 'Never'
			),
			'expanded' => false,
			'multiple' => false
		));
    }

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Application\\AgentBundle\\Form\\Model\\SettingsProfile',
		);
	}

    public function getName()
    {
        return 'settings_profile';
    }
}

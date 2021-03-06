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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Form\Usersource\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Application\AdminBundle\Form\CustomField\Type\PasswordValueType;

class LdapType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('title', 'text', array('required' => true));
		$builder->add('lost_password_url', 'text', array('required' => false));
		$builder->add('secure', 'choice', array('required' => false, 'choices' => array('useStartTls' => 'TLS', 'useSsl' => 'SSL')));
		$builder->add('port', 'text', array('required' => false));
		$builder->add('host', 'text', array('required' => true));
		$builder->add('baseDn', 'text', array('required' => true));
		$builder->add('username', 'text', array('required' => true));
		$builder->add('password', new PasswordValueType(), array('required' => false, 'always_empty' => true));
		$builder->add('field_username', 'text', array('required' => true));
		$builder->add('field_email', 'text', array('required' => true));
		$builder->add('accountFilterFormat', 'text', array('required' => false));
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Application\\AdminBundle\\Form\\Usersource\\Model\\LdapModel',
		);
	}

	public function getName()
	{
		return 'usersource';
	}
}

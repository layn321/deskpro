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

namespace Application\AdminBundle\Form;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Application\AdminBundle\Form\CustomField\Type\PasswordValueType;

class EditEmailTransport extends AbstractType
{
	public function __construct()
	{

	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('match_type', 'text');
		$builder->add('match_email', 'text', array('required' => false));
		$builder->add('match_domain', 'text', array('required' => false));
		$builder->add('match_regex', 'text', array('required' => false));

		$builder->add('transport_type', 'text');

		$options_form = $builder->create('smtp_options', 'form');
		$options_form->add('host', 'text', array('required' => false));
		$options_form->add('username', 'text', array('required' => false));
		$options_form->add('password', new PasswordValueType(), array('required' => false, 'always_empty' => false));
		$options_form->add('port', 'text', array('required' => false));
		$options_form->add('secure', 'choice', array('required' => false, 'empty_value' => false, 'choices' => array('' => 'None', 'ssl' => 'Use SSL', 'tls' => 'Use TLS')));
		$builder->add($options_form);

		$options_form = $builder->create('gmail_options', 'form');
		$options_form->add('username', 'text', array('required' => false));
		$options_form->add('password', new PasswordValueType(), array('required' => false, 'always_empty' => false));
		$builder->add($options_form);
	}

	public function getName()
	{
		return 'transport';
	}
}

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

namespace Magento\Usersource\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MagentoType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('title', 'text', array('required' => true));
		$builder->add('lost_password_url', 'text', array('required' => false));
		$builder->add('sso_cookie', 'text', array('required' => false));
		$builder->add('magento_path', 'text', array('required' => false));
		$builder->add('sso_js', 'text', array('required' => false));
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Magento\\Usersource\\Form\\MagentoModel',
		);
	}

	public function getName()
	{
		return 'usersource';
	}
}

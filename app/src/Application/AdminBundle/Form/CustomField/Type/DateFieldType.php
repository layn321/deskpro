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

namespace Application\AdminBundle\Form\CustomField\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DateFieldType extends CustomFieldTypeAbstract
{
    protected function buildCustomFieldForm(FormBuilder $builder, array $options)
    {
        $builder->add('default_value', 'text', array('required' => false));
        $builder->add('default_mode', 'text', array('required' => true));
		$builder->add('required', 'checkbox', array('required' => false));
		$builder->add('agent_required', 'checkbox', array('required' => false));

		$builder->add('date_valid_type', 'hidden');
		$builder->add('date_valid_date1', 'text', array('required' => false));
		$builder->add('date_valid_date2', 'text', array('required' => false));
		$builder->add('date_valid_range1', 'text', array('required' => false));
		$builder->add('date_valid_range2', 'text', array('required' => false));
		$builder->add('date_valid_dow', 'choice', array(
			'multiple' => true,
			'expanded' => true,
			'required' => false,
			'choices' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
		));
    }

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Application\\AdminBundle\\Form\\CustomField\\Model\\DateField',
		);
	}
}

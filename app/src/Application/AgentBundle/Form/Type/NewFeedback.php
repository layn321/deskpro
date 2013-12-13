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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NewFeedback extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
    {
		#------------------------------
		# Basic fields
		#------------------------------

		$builder->add('title', 'text');
		$builder->add('content', 'textarea');

		$builder->add('category_id', 'text');
		$builder->add('status_code', 'text');

		$builder->add('labels', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));

		$builder->add('attach_ids', 'collection', array(
			'type' => 'hidden',
			'required' => false,
			'allow_add' => true,
			'allow_delete' => true
		));
    }

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Application\\AgentBundle\\Form\\Model\\NewFeedback',
		);
	}

    public function getName()
    {
        return 'newfeedback';
    }
}

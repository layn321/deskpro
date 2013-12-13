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

class NewTask extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        #------------------------------
        # Basic fields
        #------------------------------

        $builder->add('title', 'text');
        $builder->add('date_due', 'datetime', array(
            'widget' => 'single_text',
            'empty_value' => '',
            'date_format'=>'M/d/y',
            'required' => false,
        ));

        $builder->add('visibility', 'choice', array(
            'choices' => array(0 => 'Public', 2 => 'Private'),
            'required' => true,
        ));
        $builder->add('assigned_agent_team', 'entity', array(
            'class' => 'Application\DeskPRO\Entity\AgentTeam',
            'property' => 'name',
            'required' => false,
            'empty_value'=> '--Agent Team--'
        ));

        $builder->add('assigned_agent', 'entity', array(
            'class' => 'Application\DeskPRO\Entity\Person',
			'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
				return $er->createQueryBuilder('p')
						->where('p.is_agent = true')
						->orderBy('p.name', 'ASC');
			},
            'property' => 'name',
            'required' => false,
            'empty_value'=> '--Agent--'
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Application\DeskPRO\Entity\Task',
        );
    }

    public function getName()
    {
        return 'newtask';
    }
}

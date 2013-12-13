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

class EditDepartmentType extends AbstractType
{
	protected $is_new;

	public function __construct($is_new)
	{
		$this->is_new = $is_new;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('title', 'text');
		$builder->add('user_title', 'text');

		$builder->add('is_tickets_enabled', 'checkbox', array(
			'required' => false,
		));
		$builder->add('is_chat_enabled', 'checkbox', array(
			'required' => false,
		));

		if ($this->is_new) {
			$builder->add('parent', 'entity', array(
				'class' => 'DeskPRO:Department',
				'property' => 'title',
				'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
						return $er->createQueryBuilder('p')
								->where('p.parent IS NULL')
								->orderBy('p.display_order', 'ASC');
				},
				'empty_value' => '',
				'required' => false,
			));
		}
	}

	public function getName()
	{
		return 'department';
	}
}

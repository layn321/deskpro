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
 */

namespace Application\DeskPRO\Twig;

abstract class Template extends \Twig_Template
{
	public function display(array $context, array $blocks = array())
    {
		if (!$this->env->isCustomTemplate($this->getTemplateName())) {
			parent::display($context, $blocks);
		} else {
			try {
				parent::display($context, $blocks);
			} catch (\Exception $e) {
				if (preg_match('#\.html\.twig$#', $this->getTemplateName())) {
					echo "<div style='background-color: #ccc; border: 3px solid red; color: #000; padding: 10px; border-radius: 3px; margin: 10px;'>";
					echo "There was an error rendering your custom template: <strong>" . $this->getTemplateName() . "</strong><br />";
					echo "<hr/>";
					echo $e->getMessage();
					echo "</div>";
				} else {
					echo "\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n\n";
					echo "There was an error rendering your customised template: " . $this->getTemplateName();
					echo "\nError: " . $e->getMessage();
					echo "\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n\n";
				}
			}
		}
    }
}
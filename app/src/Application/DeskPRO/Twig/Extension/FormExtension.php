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
 * @subpackage
 */

namespace Application\DeskPRO\Twig\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Util\FormUtil;

class FormExtension extends \Symfony\Bridge\Twig\Extension\FormExtension
{
	public function __construct(array $resources = array())
	{
		$set_resources = array();
		foreach ($resources as $r) {
			if ($r != 'form_div_layout.html.twig') {
				$set_resources[] = $r;
			}
		}

		parent::__construct($set_resources);
	}

	protected function render(FormView $view, $section, array $variables = array())
    {
        $mainTemplate = in_array($section, array('widget', 'row'));

        if (null === $this->template) {
            $this->template = reset($this->resources);
            if (!$this->template instanceof \Twig_Template) {
                $this->template = $this->environment->loadTemplate($this->template);
            }
        }

        $custom = '_'.$view->get('id');
        $rendering = $custom.$section;
        $blocks = $this->getBlocks($view);

        if (isset($this->varStack[$rendering])) {
            $typeIndex = $this->varStack[$rendering]['typeIndex'] - 1;
            $types = $this->varStack[$rendering]['types'];
            $this->varStack[$rendering]['variables'] = array_replace_recursive($this->varStack[$rendering]['variables'], $variables);
        } else {
            $types = $view->get('types');
            $types[] = $custom;
            $typeIndex = count($types) - 1;
            $this->varStack[$rendering] = array (
                'variables' => array_replace_recursive($view->all(), $variables),
                'types'     => $types,
            );
        }

        do {
            $types[$typeIndex] .= '_'.$section;

            if (isset($blocks[$types[$typeIndex]])) {

                $this->varStack[$rendering]['typeIndex'] = $typeIndex;

                // we do not call renderBlock here to avoid too many nested level calls (XDebug limits the level to 100 by default)
                ob_start();
                $this->template->displayBlock($types[$typeIndex], $this->varStack[$rendering]['variables'], $blocks);
                $html = ob_get_clean();

                if ($mainTemplate) {
                    $view->setRendered();
                }

                unset($this->varStack[$rendering]);

                return $html;
            }
        } while (--$typeIndex >= 0);

        throw new FormException(sprintf(
            'Unable to render the form as none of the following blocks exist: "%s".',
            implode('", "', array_reverse($types))
        ));
    }
}
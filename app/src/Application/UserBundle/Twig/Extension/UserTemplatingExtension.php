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
 * @category Templating
 */

namespace Application\UserBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Application\DeskPRO\App;

use Orb\Util\Util;

class UserTemplatingExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

	public function getFunctions()
    {
        return array(
            'portal_js'         => new \Twig_Function_Method($this, 'portalJs', array('is_safe' => array('html'))),
			'portal_css'        => new \Twig_Function_Method($this, 'portalCss', array('is_safe' => array('html'))),
            'portal_section'    => new \Twig_Function_Method($this, 'portalSection', array('is_safe' => array('html'))),
            'portal_hasblock'   => new \Twig_Function_Method($this, 'portalHasBlock', array('is_safe' => array('html'))),
			'portal_option'     => new \Twig_Function_Method($this, 'portalOption', array()),
        );
    }

	public function portalJs($section)
	{
		$html = array();

		$portal_page = $this->container->get('deskpro.user_portal_page');
		foreach ($portal_page->getJsAssets($section) as $asset) {
			$url = $this->container->get('templating.helper.assets')->getUrl($asset);
			$html[] = '<script src="' . $url . '"></script>';
		}

		return implode("\n", $html);
	}

	public function portalCss($section)
	{
		$html = array();

		$portal_page = $this->container->get('deskpro.user_portal_page');
		foreach ($portal_page->getCssAssets($section) as $asset) {
			$url = $this->container->get('templating.helper.assets')->getUrl($asset);
			$html[] = '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
		}

		return implode("\n", $html);
	}

	public function portalSection($section)
	{
		$portal_page = $this->container->get('deskpro.user_portal_page');
		return $portal_page->getSectionHtml($section);
	}

	public function portalHasBlock($section, $type)
	{
		$portal_page = $this->container->get('deskpro.user_portal_page');
		return $portal_page->hasBlock($section, $type);
	}

	public function portalOption($name)
	{
		static $options = array();
		if (isset($options[$name])) {
			return $options[$name];
		}

		switch ($name) {
			default:
				return '';
		}

		return $options[$name];
	}

	public function getFilters()
    {
        return array();
    }

	 public function getName()
    {
        return 'deskpro_user_templating';
    }
}

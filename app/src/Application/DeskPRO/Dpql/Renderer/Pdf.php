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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql\Renderer;

use Application\DeskPRO\Dpql\ResultHandler;
use Application\DeskPRO\Dpql\Results;
use Application\DeskPRO\App;

/**
 * Renders DPQL results to Pdf.
 */
class Pdf extends Html
{
	/**
	 * Gets the MIME content type for this type of output.
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return 'application/pdf';
	}

	/**
	 * Gets the file extension for this type of output.
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return 'pdf';
	}

	/**
	 * Render to the specified format and type
	 *
	 * @return string
	 */
	public function render()
	{
		$html = parent::render();

		$content_html = App::getTemplating()->render('DeskPRO:pdf_agent:report-builder.html.twig', array(
			'html' => $html,
			'title' => $this->_title
		));

		$mpdf = new \mPDF_mPDF
		(
			'utf-8', // Language/Character set
			'A4', // Size
			'8', // Default Font Size
			'', // Default Font
			20, // Margin Left
			20, // Margin Right
			20, // Margin Top
			20, // Margin Bottom
			10, // Margin Header
			10, // Margin Footer
			'P' // Orientation
		);

		$mpdf->SetBasePath(realpath(__DIR__.'/../../../../../web/images'));

		$mpdf->WriteHTML($content_html);

		return $mpdf->Output('', 'S');
	}

	/**
	 * Charts not supported in CSV. Returns false.
	 *
	 * @param string $type
	 * @param array $rows
	 *
	 * @return string|bool
	 */
	protected function _renderChart($type, array $rows)
	{
		return false;
	}
}
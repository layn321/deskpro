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

namespace Application\ReportBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Dpql\Compiler;
use Application\DeskPRO\Entity\ReportBuilder;
use Application\DeskPRO\Dpql\Exception AS DpqlException;
use Application\DeskPRO\Dpql\Statement\Display;

class BillingController extends AbstractController
{
	public function indexAction()
	{
		$_REQUEST['params'] = 'today';
		return $this->reportAction('list-charges-date');
	}

	public function reportAction($report_id)
	{
		$report = $this->getReportOr404($report_id);
		$error = false;

		$params = $this->getParamsInput('params');
		$query = $report['query'];

		$tempReport = new ReportBuilder();
		$tempReport->title = $report['title'];
		$report['title_final'] = $tempReport->getTitle('printable', $params);

		$output = $this->in->getString('output');
		if ($output) {
			try {
				return $this->_getReportResponseForType($output, $query, $report['title_final'], $params);
			} catch (DpqlException $e) {
				// fall through - an error will be triggered below
			}
		}

		$results = $this->renderQuery($query, 'html', $error, $params);

		return $this->render('ReportBundle:Billing:report.html.twig', $this->mergeBillingLayoutParams(array(
			'report' => $report,
			'query' => $query,
			'params' => $params,
			'error' => $error,
			'results' => $results
		)));
	}

	protected function _getReportResponseForType($type, $query, $title, array $params = array())
	{
		$compiler = new Compiler();
		$statement = $compiler->compile($query, $params);
		$statement->setImplicitLimit(0);
		$renderer = $statement->getRenderer($type);
		$output = $renderer->render();

		$response = $this->response;
		$response->headers->set('Content-Type', $renderer->getContentType());
		$response->headers->set('Content-Disposition', 'inline; filename=' . $renderer->getFileName($title));
		$response->setContent($output);
		return $response;
	}

	public function renderQuery($query, $renderer, &$error = false, array $params = array())
	{
		$error = false;
		try {
			$compiler = new Compiler();
			$statement = $compiler->compile($query, $params);
			return $statement->getRenderer($renderer)->render();
		} catch (DpqlException $e) {
			$error = $e->getMessage();
			return false;
		}
	}

	public function getReportOr404($id)
	{
		$reports = $this->_getBillingReports();
		if (empty($reports[$id])) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
				"There is no billing report with ID $id"
			);
		}

		return $reports[$id];
	}

	public function getParamsInput($name = 'params')
	{
		if (isset($_REQUEST[$name])) {
			$params = $_REQUEST[$name];
		} else {
			$params = $this->in->getRaw($name);
		}
		
		if (is_array($params)) {
			ksort($params);
		} else if ($params) {
			$newParams = array();
			foreach (explode(',', $params) AS $k => $v) {
				$newParams[$k + 1] = $v;
			}
			$params = $newParams;
		} else {
			$params = array();
		}

		return $params;
	}

	public function mergeBillingLayoutParams(array $params = array())
	{
		$rbRepository = $this->em->getRepository('DeskPRO:ReportBuilder');
		$groupParams = $rbRepository->getReportGroupParams();

		$groupParams['fields'] = array();
		$groupParams['orders'] = array();
		$groupParams['statuses'] = array();

		$params['billingReports'] = $this->_getBillingReports();
		$params['reportGroupParams'] = $groupParams;

		return $params;
	}

	protected function _getBillingReports()
	{
		$currency = addslashes(App::getSetting('core_tickets.billing_currency'));

		$output = array(
			'list-charges-date' => array(
				'title' => 'List of charges <1:date group, default: today>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(TIME_LENGTH(ticket_charges.charge_time)) AS 'Time', TOTAL(FORMAT(ticket_charges.amount, 'number', 2)) AS 'Amount ($currency)', ticket_charges.agent, ticket_charges.date_created, ticket_charges.comment, ticket_charges.ticket
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					ORDER BY ticket_charges.date_created
				"
			),
			'total-charges-per-day-date' => array(
				'title' => 'Total [charges] per day <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					GROUP BY DATE(ticket_charges.date_created) AS 'Date'
				"
			),
			'total-amount-charges-per-day-date' => array(
				'title' => 'Total [amount charges] per day <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.amount > 0
					GROUP BY DATE(ticket_charges.date_created) AS 'Date'
				"
			),
			'total-time-charges-per-day-date' => array(
				'title' => 'Total [time charges] per day <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.charge_time > 0
					GROUP BY DATE(ticket_charges.date_created) AS 'Date'
				"
			),
			'total-charges-person-date' => array(
				'title' => 'Total [charges] per person <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					GROUP BY ticket_charges.person
				"
			),
			'total-amount-charges-person-date' => array(
				'title' => 'Total [amount charges] per person <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.amount > 0
					GROUP BY ticket_charges.person
				"
			),
			'total-time-charges-person-date' => array(
				'title' => 'Total [time charges] per person <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.charge_time > 0
					GROUP BY ticket_charges.person
				"
			),
			'list-charges-person-date' => array(
				'title' => 'List of charges per person <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(TIME_LENGTH(ticket_charges.charge_time)) AS 'Time', TOTAL(FORMAT(ticket_charges.amount, 'number', 2)) AS 'Amount ($currency)', ticket_charges.agent, ticket_charges.date_created, ticket_charges.comment, ticket_charges.ticket
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					SPLIT BY ticket_charges.person
					ORDER BY ticket_charges.date_created
				"
			),
			'total-charges-organization-date' => array(
				'title' => 'Total [charges] per organization <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.organization_id <> NULL
					GROUP BY ticket_charges.organization
				"
			),
			'total-amount-charges-organization-date' => array(
				'title' => 'Total [amount charges] per organization <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.organization_id <> NULL AND ticket_charges.amount > 0
					GROUP BY ticket_charges.organization
				"
			),
			'total-time-charges-organization-date' => array(
				'title' => 'Total [time charges] per organization <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.organization_id <> NULL AND ticket_charges.charge_time > 0
					GROUP BY ticket_charges.organization
				"
			),
			'list-charges-organization-date' => array(
				'title' => 'List of charges per organization <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(TIME_LENGTH(ticket_charges.charge_time)) AS 'Time', TOTAL(FORMAT(ticket_charges.amount, 'number', 2)) AS 'Amount ($currency)', ticket_charges.agent, ticket_charges.date_created, ticket_charges.comment, ticket_charges.ticket
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.organization_id <> NULL
					SPLIT BY ticket_charges.organization
					ORDER BY ticket_charges.date_created
				"
			),
			'total-charges-agent-date' => array(
				'title' => 'Total [charges] per agent <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					GROUP BY ticket_charges.agent
				"
			),
			'total-amount-charges-agent-date' => array(
				'title' => 'Total [amount charges] per agent <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(FORMAT(SUM(ticket_charges.amount), 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.amount > 0
					GROUP BY ticket_charges.agent
				"
			),
			'total-time-charges-agent-date' => array(
				'title' => 'Total [time charges] per agent <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE, BAR
					SELECT TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP% AND ticket_charges.charge_time > 0
					GROUP BY ticket_charges.agent
				"
			),
			'list-charges-agent-date' => array(
				'title' => 'List of charges per agent <1:date group, default: this_month>',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(TIME_LENGTH(ticket_charges.charge_time)) AS 'Time', TOTAL(FORMAT(ticket_charges.amount, 'number', 2)) AS 'Amount ($currency)', ticket_charges.date_created, ticket_charges.comment, ticket_charges.ticket
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					SPLIT BY ticket_charges.agent
					ORDER BY ticket_charges.date_created
				"
			),
			/**
			 * The Other Guys
			 * #201401220632 @ Frankie -- Custom Billing Report by Department
			 */
			 'list-charges-dept-date' => array(
				'title' => '***Total [charges] per department <1:date group, default: this_month>***',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', FORMAT(ticket_charges.department.rate, 'number', 2) AS 'Rate ($currency)', TOTAL(FORMAT(TIME_LENGTH(SUM(ticket_charges.charge_time)) / 3600 * ticket_charges.department.rate, 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					SPLIT BY ticket_charges.department
				"
			),
			/**
			 * The Other Guys
			 * #201401222200 @ Andy, Layne, Frankie -- Custom Billing Report by Agent
			 */
			 'list-charges-agent-rate-date' => array(
				'title' => '***Total [charges] per agent with rate<1:date group, default: this_month>***',
				'query' => "
					DISPLAY TABLE
					SELECT TOTAL(COUNT()) AS 'Number of Charges', TOTAL(TIME_LENGTH(SUM(ticket_charges.charge_time))) AS 'Total Time', FORMAT(ticket_charges.agent.department.rate, 'number', 2) AS 'Rate ($currency)', TOTAL(FORMAT(TIME_LENGTH(SUM(ticket_charges.charge_time)) / 3600 * ticket_charges.department.rate, 'number', 2)) AS 'Total Amount ($currency)'
					FROM ticket_charges
					WHERE ticket_charges.date_created = %1:DATE_GROUP%
					SPLIT BY ticket_charges.agent
				"
			),
			
		);

		$tempReport = new ReportBuilder();

		foreach ($output AS $id => &$report) {
			$report['id'] = $id;

			$tempReport->title = $report['title'];
			$report['title_placeholder'] = $tempReport->getTitle('placeholder');
		}

		return $output;
	}
}

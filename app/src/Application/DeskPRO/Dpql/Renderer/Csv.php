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
 * Renders DPQL results to CSV.
 */
class Csv extends AbstractRenderer
{
	/**
	 * Get the default value renderer that should be used for this type.
	 *
	 * @return \Application\DeskPRO\Dpql\Renderer\Values\AbstractValues
	 */
	protected function _getDefaultValueRenderer()
	{
		return new \Application\DeskPRO\Dpql\Renderer\Values\Text();
	}

	/**
	 * Gets the MIME content type for this type of output.
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return 'text/csv';
	}

	/**
	 * Gets the file extension for this type of output.
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return 'csv';
	}

	/**
	 * Joins the already rendered output into one output.
	 *
	 * @param array $output
	 *
	 * @return string
	 */
	protected function _implodeSplitOutput(array $output)
	{
		return implode("\r\n\r\n", $output);
	}

	/**
	 * Finalizes the rendering of a split output by rendering the body with the header.
	 *
	 * @param string $header
	 * @param string $body
	 *
	 * @return string
	 */
	protected function _renderSplitOutputWithHeader($header, $body)
	{
		return "\"$header\"\r\n$body";
	}

	/**
	 * Renders a table with the specified rows/data.
	 *
	 * @param array $rows
	 *
	 * @return string
	 */
	protected function _renderTable(array $rows)
	{
		if (!$rows) {
			return '';
		}

		if ($this->_handler->getGroupXColumns()) {
			return $this->_renderMatrixTable($rows);
		}

		$groupXColumns = $this->_handler->getGroupXColumns();
		$groupYColumns = $this->_handler->getGroupYColumns();
		$selectColumns = $this->_handler->getSelectColumns();

		$output = array();

		$columns = array();
		foreach ($groupXColumns AS $column) {
			$columns[] = $this->wrapCell($column['title']);
		}
		foreach ($groupYColumns AS $column) {
			$columns[] = $this->wrapCell($column['title']);
		}
		foreach ($selectColumns AS $column) {
			$columns[] = $this->wrapCell($column['title']);
		}

		$output[] = implode(',', $columns);

		foreach ($rows AS $row) {
			$columns = array();
			foreach ($groupXColumns AS $column) {
				$columns[] = $this->wrapCell($this->_renderCellValue($row, $column));
			}
			foreach ($groupYColumns AS $column) {
				$columns[] = $this->wrapCell($this->_renderCellValue($row, $column));
			}
			foreach ($selectColumns AS $column) {
				$columns[] = $this->wrapCell($this->_renderCellValue($row, $column));
			}

			$output[] = implode(',', $columns);
		}

		// output a row of totals if requested - only do it with grouping, as otherwise there's
		// no real way of marking a row as the totals and that'd be confusing
		$totalColumns = $this->_handler->getTotalColumns();
		if (count($rows) > 1  && $totalColumns && $groupYColumns) {
			$cells = array();

			if ($groupYColumns) {
				foreach ($groupYColumns AS $column) {
					$cells[] = $this->wrapCell('');
				}
				array_pop($cells);
				$cells[] = $this->wrapCell('Total');
			}

			$columnTotals = array();
			foreach ($rows AS $row) {
				foreach ($totalColumns AS $id) {
					if ($this->getColumnValue($row, $id) === null) {
						continue;
					}

					if (!isset($columnTotals[$id])) {
						$columnTotals[$id] = 0;
					}
					$columnTotals[$id] += $this->getColumnValue($row, $id);
				}
			}

			$firstRow = reset($rows);
			$fakeRow = array_fill_keys(array_keys($firstRow), null);
			foreach ($columnTotals AS $id => $value) {
				$fakeRow[$id - 1] = $value;
			}

			foreach ($selectColumns AS $column) {
				if (isset($columnTotals[$column['resultId']])) {
					$cells[] = $this->wrapCell($this->_renderCellValue($fakeRow, $column));
				} else {
					$cells[] = $this->wrapCell('');
				}
			}
			$output[] = implode(',', $cells);
		}

		return implode("\r\n", $output);
	}

	/**
	 * Renders a matrix table (with X and Y grouping).
	 *
	 * @param array $rows
	 *
	 * @return string
	 */
	protected function _renderMatrixTable(array $rows)
	{
		$prepared = $this->_prepareMatrixTable($rows);
		$lookup = $prepared['lookup'];

		$rows = array();

		$rowGroups = $this->_getFinalMatrixPathsWithPrintable(array('root'), $prepared['yDistinct']);
		$headerCols = $this->_getFinalMatrixPathsWithPrintable(array('root'), $prepared['xDistinct']);

		$select = $this->_handler->getSelectColumns();
		$first = reset($select);
		if (count($select) == 1 && in_array($first['renderer'], array('number', 'numberraw'), true)) {
			$totalType = $first['renderer'];
		} else {
			$totalType = false;
		}

		$headerRow = array();
		foreach ($this->_handler->getGroupYColumns() AS $column) {
			$headerRow[] = $this->wrapCell('');
		}
		$parts = array();
		foreach ($this->_handler->getGroupXColumns() AS $column) {
			$parts[] = $column['title'];
		}
		$headerRow[] = $this->wrapCell(implode(' / ', $parts));
		foreach ($headerCols AS $headerCol) {
			$headerRow[] = $this->wrapCell('');
		}
		array_pop($headerRow);
		if ($totalType) {
			$headerRow[] = $this->wrapCell('');
		}

		$rows[] = implode(',', $headerRow);

		$headerRow = array();
		foreach ($this->_handler->getGroupYColumns() AS $column) {
			$headerRow[] = $this->wrapCell($column['title']);
		}
		foreach ($headerCols AS $headerCol) {
			$headerRow[] = $this->wrapCell(implode(' / ', $headerCol));
		}

		if ($totalType) {
			$headerRow[] = $this->wrapCell('Total');
		}

		$rows[] = implode(',', $headerRow);

		if (!$rowGroups) {
			// need to fake it so we get a row with no Y grouping
			$rowGroups = array('root' => array());
		}

		$columnTotals = array();

		foreach ($rowGroups AS $yPath => $printable) {
			$columns = array();
			$rowTotal = 0;

			foreach ($printable AS $print) {
				$columns[] = $this->wrapCell($print);
			}

			foreach ($headerCols AS $xPath => $null) {
				if (isset($lookup[$yPath][$xPath])) {
					$value = $lookup[$yPath][$xPath];
				} else {
					$value = '';
				}
				$columns[] = $this->wrapCell($value);

				if ($totalType) {
					$rowTotal += str_replace(',', '', $value);
					if (!isset($columnTotals[$xPath])) {
						$columnTotals[$xPath] = 0;
					}
					$columnTotals[$xPath] += str_replace(',', '', $value);
				}
			}

			if ($totalType) {
				$columns[] = $this->wrapCell($this->_valueRenderer->renderValue($rowTotal, $totalType));
			}

			$rows[] = implode(',', $columns);
		}

		if ($totalType && $this->_handler->getGroupYColumns()) {
			$columns = array();
			foreach ($this->_handler->getGroupYColumns() AS $rowGroupSkip) {
				$columns[] = $this->wrapCell('');
			}
			array_pop($columns);
			$columns[] = $this->wrapCell('Total');
			foreach ($columnTotals AS $value) {
				$columns[] = $this->wrapCell($this->_valueRenderer->renderValue($value, $totalType));
			}
			$columns[] = $this->wrapCell($this->_valueRenderer->renderValue(array_sum($columnTotals), $totalType));

			$rows[] = implode(',', $columns);
		}

		return implode("\r\n", $rows);
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

	/**
	 * Wraps the value as an entire cell.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function wrapCell($value)
	{
		$value = str_replace('"', '""', $value);
		return "\"$value\"";
	}
}
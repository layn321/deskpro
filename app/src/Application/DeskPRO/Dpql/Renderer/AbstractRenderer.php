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

abstract class AbstractRenderer
{
	protected static $_rendererMap = array(
		'csv' => 'Csv',
		'html' => 'Html',
		'pdf' => 'Pdf'
	);

	/**
	 * Result handler that stores all the bits that will be displayed/formatted.
	 *
	 * @var \Application\DeskPRO\Dpql\ResultHandler
	 */
	protected $_handler;

	/**
	 * Results to render.
	 *
	 * @var \Application\DeskPRO\Dpql\Results
	 */
	protected $_results;

	/**
	 * Name of the output type (html, csv, etc)
	 *
	 * @var string
	 */
	protected $_typeName = '';

	/**
	 * @var string
	 */
	protected $_title = '';

	/**
	 * Format of the output (table, bar, etc) in the specified output type.
	 * If the type doesn't support that format, it should fallback as necessary.
	 *
	 * @var string
	 */
	protected $_outputFormat = '';

	/**
	 * The value render that should be used in this output context.
	 *
	 * @var \Application\DeskPRO\Dpql\Renderer\Values\AbstractValues
	 */
	protected $_valueRenderer;

	/**
	 * Get the default value renderer that should be used for this type.
	 *
	 * @return \Application\DeskPRO\Dpql\Renderer\Values\AbstractValues
	 */
	abstract protected function _getDefaultValueRenderer();

	/**
	 * Gets the MIME content type for this type of output.
	 *
	 * @return string
	 */
	abstract public function getContentType();

	/**
	 * Gets the file extension for this type of output.
	 *
	 * @return string
	 */
	abstract public function getExtension();

	/**
	 * Joins the already rendered output into one output.
	 *
	 * @param array $output
	 *
	 * @return string
	 */
	abstract protected function _implodeSplitOutput(array $output);

	/**
	 * Finalizes the rendering of a split output by rendering the body with the header.
	 *
	 * @param string $header
	 * @param string $body
	 *
	 * @return string
	 */
	abstract protected function _renderSplitOutputWithHeader($header, $body);

	/**
	 * Renders a table with the specified rows/data.
	 *
	 * @param array $rows
	 *
	 * @return string
	 */
	abstract protected function _renderTable(array $rows);

	/**
	 * Renders a chart with the specified rows/data.
	 *
	 * @param string $type Type of chart (bar, line, pie)
	 * @param array $rows
	 *
	 * @return string|bool
	 */
	abstract protected function _renderChart($type, array $rows);

	/**
	 * @param string $typeName
	 * @param array $outputFormat
	 * @param \Application\DeskPRO\Dpql\ResultHandler $resultHandler
	 * @param \Application\DeskPRO\Dpql\Results $results
	 */
	protected function __construct($typeName, array $outputFormat, ResultHandler $resultHandler, Results $results)
	{
		if (!$outputFormat) {
			$outputFormat = array('table');
		} else {
			$outputFormat = array_unique($outputFormat);
		}

		$this->_typeName = $typeName;
		$this->_outputFormat = $outputFormat;
		$this->_handler = $resultHandler;
		$this->_results = $results;
		$this->_valueRenderer = $this->_getDefaultValueRenderer();
	}

	public function setTitle($title)
	{
		$this->_title = $title;
	}

	public function getFileName($name)
	{
		$name = preg_replace('/[^a-zA-Z0-9_ -]/', '', $name);
		$name = str_replace(' ', '-', $name);

		return strtolower($name) . '.' . $this->getExtension();
	}

	/**
	 * Render to the specified format and type
	 *
	 * @return string
	 */
	public function render()
	{
		$splitColumns = $this->_handler->getSplitColumns();

		if ($splitColumns) {
			$output = array();
			foreach ($this->_results->getSplitResults() AS $splitResult) {
				$result = $this->_renderFormatsWithFallback($this->_outputFormat, $splitResult[0]);
				if ($result) {
					$splitPrint = array();
					foreach ($this->_handler->getSplitColumns() AS $splitColumn) {
						$splitPrint[] = $this->_renderCellValue($splitResult[1], $splitColumn);
					}

					$output[] = $this->_renderSplitOutputWithHeader(implode(' / ', $splitPrint), $result);
				}
			}

			return $this->_implodeSplitOutput($output);
		} else {
			return $this->_renderFormatsWithFallback(
				$this->_outputFormat, $this->_results->getResults()
			);
		}
	}

	protected function _renderFormatsWithFallback(array $formats, array $rows)
	{
		$final = array();
		$success = false;

		foreach ($formats AS $format) {
			$result = $this->_render($format, $rows);
			if ($result !== false) {
				$success = true;
			}
			if ($result) {
				$final[] = $result;
			}
		}

		if (!$success) {
			$final[] = $this->_render('table', $rows);
		}

		return implode("\n\n", $final);
	}

	/**
	 * Renders to the specified output format.
	 *
	 * @param string $format
	 * @param array $rows
	 *
	 * @return string
	 */
	protected function _render($format, array $rows)
	{
		switch ($format) {
			case 'bar':
			case 'line':
			case 'pie':
			case 'area':
				if (count($rows) <= 1) {
					return false;
				}
				return $this->_renderChart($format, $rows);
				break;

			case 'table':
				return $this->_renderTable($rows);

			default:
				return false;
		}
	}

	/**
	 * Renders the value for a specific cell.
	 *
	 * @param mixed[int] $row
	 * @param array $column
	 *
	 * @return string
	 */
	protected function _renderCellValue(array $row, array $column)
	{
		$value = $column['resultId'] ? $row[$column['resultId'] - 1] : '';

		$renderer = $column['renderer'];
		if ($renderer instanceof \Closure) {
			/* @var $renderer \Closure */
			return $renderer($this->_valueRenderer, $value, $row, $this);
		}

		return $this->_valueRenderer->renderValue($value, $renderer);
	}

	/**
	 * Gets the value of a particular column for the given row.
	 *
	 * @param array $row
	 * @param integer|array $id
	 *
	 * @return string
	 */
	public function getColumnValue(array $row, $id)
	{
		if (is_array($id) && isset($id['resultId'])) {
			$id = $id['resultId'];
		}
		return ($id ? $row[$id - 1] : '');
	}

	/**
	 * Prepares data for a matrix table.
	 *
	 * Returns array with:
	 *  - xDistinct[pathString][renderedValue] = true -- used to find distinct values over X grouping
	 *  - yDistinct[pathString][renderedValue] = true -- used to find distinct values over Y grouping
	 *  - lookup[yPath][xPath] = cell value -- value for cell at the y/x position specified
	 *
	 * @param array $rows
	 *
	 * @return array
	 */
	protected function _prepareMatrixTable(array $rows)
	{
		$groupXColumns = $this->_handler->getGroupXColumns();
		$groupYColumns = $this->_handler->getGroupYColumns();
		$selectColumns = $this->_handler->getSelectColumns();

		$distinctXValues = array();
		$distinctXSort = array();
		$distinctYValues = array();
		$distinctYSort = array();
		$lookup = array();

		foreach ($rows AS $row) {
			$xPath = array('root');
			foreach ($groupXColumns AS $column) {
				$pathString = $this->_getGroupPathKey($xPath);
				$groupValue = $this->getColumnValue($row, $column['groupResultId']);
				$rendered = $this->_renderCellValue($row, $column);

				$distinctXValues[$pathString][$groupValue] = $rendered;
				$distinctXSort[$pathString][$groupValue] = $groupValue === null ? null : $this->getColumnValue($row, $column);

				$xPath[] = $groupValue;
			}

			$yPath = array('root');
			foreach ($groupYColumns AS $column) {
				$pathString = $this->_getGroupPathKey($yPath);
				$groupValue = $this->getColumnValue($row, $column['groupResultId']);
				$rendered = $this->_renderCellValue($row, $column);

				$distinctYValues[$pathString][$groupValue] = $rendered;
				$distinctYSort[$pathString][$groupValue] = $groupValue === null ? null : $this->getColumnValue($row, $column);

				$yPath[] = $groupValue;
			}

			$lookup[$this->_getGroupPathKey($yPath)][$this->_getGroupPathKey($xPath)] =
				$this->_renderMatrixCell($row, $selectColumns);
		}

		foreach ($distinctXSort AS $path => $sortValues) {
			uasort($sortValues, 'strnatcasecmp');

			$values = $distinctXValues[$path];
			$distinctXValues[$path] = array();
			foreach ($sortValues AS $key => $null) {
				$distinctXValues[$path][$key] = $values[$key];
			}
		}
		foreach ($distinctYSort AS $path => $sortValues) {
			uasort($sortValues, 'strnatcasecmp');

			$values = $distinctYValues[$path];
			$distinctYValues[$path] = array();
			foreach ($sortValues AS $key => $null) {
				$distinctYValues[$path][$key] = $values[$key];
			}
		}

		return array(
			'xDistinct' => $distinctXValues,
			'yDistinct' => $distinctYValues,
			'lookup' => $lookup
		);
	}

	/**
	 * Gets the final path keys to a set of distinct values in a matrix table.
	 *
	 * @param array $path Grouping paths
	 * @param array $distinct Distinct values
	 *
	 * @return array List of path keys
	 */
	protected function _getFinalMatrixPaths(array $path, array $distinct)
	{
		$pathString = $this->_getGroupPathKey($path);
		if (!isset($distinct[$pathString])) {
			return array();
		}

		$output = array();
		foreach ($distinct[$pathString] AS $value => $null) {
			$localPath = $path;
			$localPath[] = $value;

			$children = $this->_getFinalMatrixPaths($localPath, $distinct);
			if (!$children) {
				$output[] = $this->_getGroupPathKey($localPath);
			} else {
				$output = array_merge($output, $children);
			}
		}

		return $output;
	}

	/**
	 * Gets the final paths to a matrix row/column entry with the value being the printable
	 * value that lead to that entry.
	 *
	 * @param array $path
	 * @param array $distinctValues
	 * @param array $printPath
	 *
	 * @return array
	 */
	protected function _getFinalMatrixPathsWithPrintable(array $path, array $distinctValues, array $printPath = array())
	{
		$pathLookup = $this->_getGroupPathKey($path);
		if (!isset($distinctValues[$pathLookup])) {
			return array();
		}

		$output = array();

		foreach ($distinctValues[$pathLookup] AS $key => $value) {
			$localPath = $path;
			$localPath[] = $key;

			$localPrintPath = $printPath;
			$localPrintPath[] = $value;

			$childOutput = $this->_getFinalMatrixPathsWithPrintable($localPath, $distinctValues, $localPrintPath);
			if (!$childOutput) {
				// a leaf - responsible for output
				$output[$this->_getGroupPathKey($localPath)] = $localPrintPath;
			} else {
				$output = array_merge($output, $childOutput);
			}
		}

		return $output;
	}

	/**
	 * Renders a matrix cell.
	 *
	 * @param array $row
	 * @param array $selectColumns
	 *
	 * @return string
	 */
	protected function _renderMatrixCell(array $row, array $selectColumns)
	{
		$values = array();
		foreach ($selectColumns AS $column) {
			$values[] = $this->_renderCellValue($row, $column);
		}

		return implode(' / ', $values);
	}


	/**
	 * Gets the string key to identify a path to a value based on parts.
	 *
	 * @param array $groupParts
	 *
	 * @return string
	 */
	protected function _getGroupPathKey(array $groupParts)
	{
		return implode('|', $groupParts);
	}

	public static function create($type, array $outputFormat, ResultHandler $resultHandler, Results $results)
	{
		$type = strtolower($type);
		if (!isset(self::$_rendererMap[$type])) {
			throw new \Exception("Invalid DPQL renderer type $type");
		}

		$class = __NAMESPACE__ . '\\' . self::$_rendererMap[$type];
		return new $class($type, $outputFormat, $resultHandler, $results);
	}
}
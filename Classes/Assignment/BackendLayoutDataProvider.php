<?php
namespace OliverHader\Mapping\Assignment;
use OliverHader\Mapping\Domain\Object\ProcessorTask;
use TYPO3\CMS\Core\SingletonInterface;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Oliver Hader <oliver.hader@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author Oliver Hader <oliver.hader@typo3.org>
 */
class BackendLayoutDataProvider extends AbstractDataProvider implements DataProviderInterface, SingletonInterface {

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return boolean
	 */
	public function canAssign($tableName, array $record = NULL) {
		return ($tableName === 'backend_layout' && !empty($record));
	}

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return boolean
	 */
	public function canRender($tableName, array $record = NULL) {
		return (!empty($record));
	}

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return array
	 */
	public function getNodes($tableName, array $record) {
		$nodes = array();

		$typoScript = $this->parseTypoScript($record['config']);
		if (empty($typoScript['backend_layout.']['rows.'])) {
			return $nodes;
		}

		foreach ($typoScript['backend_layout.']['rows.'] as $row) {
			if (!empty($row['columns.'])) {
				foreach ($row['columns.'] as $column) {
					$colPos = $column['colPos'];
					$name = $column['name'];

					if (\TYPO3\CMS\Core\Utility\GeneralUtility::isFirstPartOfStr($name, 'LLL:')) {
						$name = $this->getLanguageService()->sL($name);
					}

					$nodes[$colPos] = array(
						'identifier' => $colPos,
						'name' => $name . ' (colPos: ' . $colPos . ')',
					);
				}
			}
		}

		return $nodes;
	}

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return array
	 */
	public function getAssignment($tableName, array $record) {
		$assignment = NULL;
		$assignmentData = NULL;

		$assignmentTableName = $this->configuration['tableName'];
		$assignmentFieldName = $this->configuration['fieldName'];

		if ($tableName === $assignmentTableName) {
			$assignmentData = $record[$assignmentFieldName];
		} elseif ($tableName === 'pages') {
			$backendLayoutRecord = $this->determineBackendLayoutRecord($record);
			if ($backendLayoutRecord !== NULL) {
				$assignmentData = $backendLayoutRecord[$assignmentFieldName];
			}
		}

		if ($assignmentData !== NULL) {
			$assignment = json_decode($assignmentData, TRUE);
		}

		return $assignment;
	}

	/**
	 * @param ProcessorTask $processorTask
	 * @return array
	 */
	public function getContentReplacement(ProcessorTask $processorTask) {
		$contentReplacement = array();

		$elements = $processorTask->getElements();
		$assignment = $processorTask->getAssignment();
		if (empty($elements) || empty($assignment['assignments'])) {
			return $contentReplacement;
		}

		if (empty($this->getFrontend()->tmpl->setup['mapping.']['contentReplacement.']['backend_layout.'])) {
			return $contentReplacement;
		}

		$contentReplacementTypoScript = $this->getFrontend()->tmpl->setup['mapping.']['contentReplacement.']['backend_layout.'];
		if (empty($contentReplacementTypoScript['default']) || empty($contentReplacementTypoScript['default.'])) {
			return $contentReplacement;
		}

		foreach ($assignment['assignments'] as $elementName => $nodeIdentifier) {
			if (empty($elements[$elementName])) {
				continue;
			}

			$renderingName = $contentReplacementTypoScript['default'];
			$renderingConfiguration = $contentReplacementTypoScript['default.'];

			if (!empty($contentReplacementTypoScript[$nodeIdentifier])) {
				$renderingName = $contentReplacementTypoScript[$nodeIdentifier];
				$renderingConfiguration = array();
			}

			if (!empty($contentReplacementTypoScript[$nodeIdentifier . '.'])) {
				$renderingConfiguration = $contentReplacementTypoScript[$nodeIdentifier . '.'];
			}

			$variableName = $processorTask->getVariableService()->substitute($elements[$elementName]);
			$processorTask->getContentObjectRenderer()->data['__mappingAssignmentColPos'] = $nodeIdentifier;
			$contentReplacement[$variableName] = $processorTask->getContentObjectRenderer()->cObjGetSingle(
				$renderingName,
				$renderingConfiguration
			);
		}

		return $contentReplacement;
	}

	/**
	 * @param array $pageRecord
	 * @return NULL|array
	 */
	protected function determineBackendLayoutRecord(array $pageRecord) {
		$backendLayoutRecord = NULL;

		if (!empty($pageRecord['backend_layout'])) {
			$backendLayoutId = $pageRecord['backend_layout'];
		} else {
			$backendLayoutId = $this->getFrontend()->cObj->getData(
				'levelfield:-1,backend_layout_next_level,slide',
				$pageRecord
			);
		}

		if (!empty($backendLayoutId)) {
			$backendLayoutRecord = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
				'*', 'backend_layout', 'uid=' . (int) $backendLayoutId . ' AND deleted=0 AND hidden=0'
			);
		}

		return $backendLayoutRecord;
	}

	/**
	 * @param string $data
	 * @return array
	 */
	protected function parseTypoScript($data) {
		/** @var $parser \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser */
		$parser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
		$parser->parse($data);
		return (array) $parser->setup;
	}

	/**
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

}
?>
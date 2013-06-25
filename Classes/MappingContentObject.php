<?php
namespace OliverHader\Mapping;
use OliverHader\Mapping\Utility\GeneralUtility;
use OliverHader\Mapping\Assignment\AbstractDataProvider;
use OliverHader\Mapping\Assignment\InvalidDataProviderException;
use OliverHader\Mapping\Service\Variable\AbstractVariableService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
class MappingContentObject {

	/**
	 * @var ContentObjectRenderer
	 */
	protected $contentObjectRenderer;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @param string $name
	 * @param array $configuration
	 * @param string $typoScriptKey
	 * @param ContentObjectRenderer $contentObjectRenderer
	 * @return null
	 */
	public function cObjGetSingleExt($name, array $configuration = NULL, $typoScriptKey, ContentObjectRenderer $contentObjectRenderer) {
		$content = NULL;

		$this->configuration = $configuration;
		$this->contentObjectRenderer = $contentObjectRenderer;

		$processorTask = $this->getProcessorTask();

		if ($processorTask !== NULL) {
			$content = $this->getProcessor()->execute($processorTask);
		}

		return $content;
	}

	/**
	 * @return NULL|Domain\Object\ProcessorTask
	 */
	protected function getProcessorTask() {
		$processorTask = $this->createProcessorTask();
		$processorTask->setContentObjectRenderer($this->contentObjectRenderer);
		$processorTask->setTableName($this->getTableName());
		$processorTask->setRecord($this->getRecord());

		$structureId = $this->getConfigurationProperty('structure', NULL);
		$context = $this->getConfigurationProperty('context', NULL);
		$renderAs = (string) $this->getConfigurationProperty('renderAs');

		if (empty($renderAs)) {
			$renderAs = AbstractVariableService::RENDER_FLUID;
		}

		$variableService = $this->getVariableService($renderAs);

		if ($structureId === NULL) {
			$assignmentDataProvider = $this->determineAssignmentDataProvider();

			if ($assignmentDataProvider !== NULL) {
				$processorTask->setAssignmentDataProvider($assignmentDataProvider);
				$assignment = $assignmentDataProvider->getAssignment(
					$this->getTableName(),
					$this->getRecord()
				);

				if ($assignment !== NULL) {
					$processorTask->setAssignment($assignment);
					$structureId = $assignment['structure'];
					$context = $assignment['context'];
				}
			}
		}

		$structure = $this->getStructureRepository()->findByUid($structureId);

		if (empty($structure)) {
			return NULL;
		}

		$processorTask->setStructure($structure);
		$processorTask->setContextName($context);
		$processorTask->setVariableService($variableService);

		return $processorTask;
	}

	/**
	 * @return NULL|Assignment\DataProviderInterface
	 */
	protected function determineAssignmentDataProvider() {
		$dataProvider = NULL;

		try {
			$dataProvider = AbstractDataProvider::createByContent(
				AbstractDataProvider::ACTION_Render,
				$this->getTableName(),
				$this->getRecord()
			);
		} catch (InvalidDataProviderException $exception) {
			return NULL;
		}

		return $dataProvider;
	}

	protected function getAssignment() {
		list($tableName, $recordId) = explode(':', $this->contentObjectRenderer->currentRecord, 2);
		$record = $this->contentObjectRenderer->data;

		if (empty($tableName) || empty($record)) {
			return NULL;
		}


		return $dataProvider->getAssignment($tableName, $record);
	}

	/**
	 * @param string $property
	 * @param NULL|string $defaultValue
	 * @return string
	 */
	protected function getConfigurationProperty($property, $defaultValue = '') {
		$value = $defaultValue;

		if (isset($this->configuration[$property])) {
			$value = $this->configuration[$property];
		}
		if (!empty($this->configuration[$property . '.'])) {
			$value = $this->contentObjectRenderer->stdWrap(
				$value,
				$this->configuration[$property . '.']
			);
		}

		return $value;
	}

	/**
	 * @return string
	 */
	protected function getTableName() {
		list($tableName, $recordId) = explode(':', $this->contentObjectRenderer->currentRecord, 2);
		return $tableName;
	}

	/**
	 * @return array
	 */
	protected function getRecord() {
		return $this->contentObjectRenderer->data;
	}

	/**
	 * @param string $renderAs
	 * @return AbstractVariableService
	 */
	protected function getVariableService($renderAs) {
		$renderAs = ucfirst(strtolower($renderAs));
		return GeneralUtility::getObjectManager()->get(
			'OliverHader\\Mapping\\Service\\Variable\\' . $renderAs . 'VariableService'
		);
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Repository\StructureRepository
	 */
	protected function getStructureRepository() {
		return GeneralUtility::getObjectManager()->get(
			'OliverHader\\Mapping\\Domain\\Repository\\StructureRepository'
		);
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Object\ProcessorTask
	 */
	protected function createProcessorTask() {
		return GeneralUtility::getObjectManager()->get(
			'OliverHader\\Mapping\\Domain\\Object\\ProcessorTask'
		);
	}

	/**
	 * @return \OliverHader\Mapping\Processor
	 */
	protected function getProcessor() {
		return GeneralUtility::getObjectManager()->get(
			'OliverHader\\Mapping\\Processor'
		);
	}

}
?>
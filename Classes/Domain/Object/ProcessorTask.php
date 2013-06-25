<?php
namespace OliverHader\Mapping\Domain\Object;

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
class ProcessorTask {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObjectRenderer;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * @var array
	 */
	protected $record;

	/**
	 * @var \OliverHader\Mapping\Domain\Model\Structure
	 */
	protected $structure;

	/**
	 * @var string
	 */
	protected $contextName;

	/**
	 * @var \OliverHader\Mapping\Service\Variable\AbstractVariableService
	 */
	protected $variableService;

	/**
	 * @var \OliverHader\Mapping\Assignment\DataProviderInterface
	 */
	protected $assignmentDataProvider;

	/**
	 * @var array
	 */
	protected $assignment;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var array|\OliverHader\Mapping\Domain\Model\Element[]
	 */
	protected $elements = array();

	/**
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer
	 */
	public function setContentObjectRenderer(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer) {
		$this->contentObjectRenderer = $contentObjectRenderer;
	}

	/**
	 * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	public function getContentObjectRenderer() {
		return $this->contentObjectRenderer;
	}

	/**
	 * @param string $tableName
	 */
	public function setTableName($tableName) {
		$this->tableName = (string) $tableName;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * @param array $record
	 */
	public function setRecord(array $record) {
		$this->record = $record;
	}

	/**
	 * @return array
	 */
	public function getRecord() {
		return $this->record;
	}

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 */
	public function setStructure(\OliverHader\Mapping\Domain\Model\Structure $structure) {
		$this->structure = $structure;
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Model\Structure
	 */
	public function getStructure() {
		return $this->structure;
	}

	/**
	 * @param string $contextName
	 */
	public function setContextName($contextName) {
		$this->contextName = (string) $contextName;
	}

	/**
	 * @return string
	 */
	public function getContextName() {
		return $this->contextName;
	}

	/**
	 * @param \OliverHader\Mapping\Service\Variable\AbstractVariableService $variableService
	 */
	public function setVariableService(\OliverHader\Mapping\Service\Variable\AbstractVariableService $variableService) {
		$this->variableService = $variableService;
	}

	/**
	 * @return \OliverHader\Mapping\Service\Variable\AbstractVariableService
	 */
	public function getVariableService() {
		return $this->variableService;
	}

	/**
	 * @param \OliverHader\Mapping\Assignment\DataProviderInterface $assignmentDataProvider
	 */
	public function setAssignmentDataProvider(\OliverHader\Mapping\Assignment\DataProviderInterface $assignmentDataProvider) {
		$this->assignmentDataProvider = $assignmentDataProvider;
	}

	/**
	 * @return \OliverHader\Mapping\Assignment\DataProviderInterface
	 */
	public function getAssignmentDataProvider() {
		return $this->assignmentDataProvider;
	}

	/**
	 * @param array $assignment
	 */
	public function setAssignment(array $assignment) {
		$this->assignment = $assignment;
	}

	/**
	 * @return array
	 */
	public function getAssignment() {
		return $this->assignment;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = (string) $content;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Element $element
	 */
	public function addElement(\OliverHader\Mapping\Domain\Model\Element $element) {
		$this->elements[$element->getName()] = $element;
	}

	/**
	 * @return array|\OliverHader\Mapping\Domain\Model\Element[]
	 */
	public function getElements() {
		return $this->elements;
	}

}
?>
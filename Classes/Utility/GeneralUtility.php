<?php
namespace OliverHader\Mapping\Utility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
class GeneralUtility {

	/**
	 * @param string $name
	 * @param string $tableName
	 * @param string $fieldName
	 * @param string $dataProvider
	 * @throws \RuntimeException
	 */
	static public function registerAssignmentHandler($name, $tableName, $fieldName, $dataProvider) {
		if (!empty($GLOBALS['TCA'][$tableName]['columns'][$fieldName])) {
			throw new \RuntimeException(
				'Field ' . $tableName . '.' . $fieldName . ' is already defined'
			);
		}

		$columns = array(
			$fieldName => array(
				'exclude' => 0,
				'label' => 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:common.assignments',
				'config' => array(
					'type' => 'user',
					'userFunc' => 'OliverHader\\Mapping\\Service\\FormEngineService->dispatch',
					'parameters' => array(
						'controllerName' => 'FormEngine',
						'actionName' => 'assignment',
					),
				),
			),
		);

		ExtensionManagementUtility::addTCAcolumns($tableName, $columns);
		ExtensionManagementUtility::addToAllTCAtypes($tableName, $fieldName);
		self::getConfigurationService()->setAssignmentHandler($name, $tableName, $fieldName, $dataProvider);
	}

	/**
	 * @return \OliverHader\Mapping\Service\ConfigurationService
	 */
	static public function getConfigurationService() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'OliverHader\\Mapping\\Service\\ConfigurationService'
		);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	static public function getObjectManager() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Extbase\\Object\\ObjectManager'
		);
	}

}
?>
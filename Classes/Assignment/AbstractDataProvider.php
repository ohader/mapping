<?php
namespace OliverHader\Mapping\Assignment;
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
abstract class AbstractDataProvider implements SingletonInterface {

	const ACTION_Assign = 'Assign';
	const ACTION_Render = 'Render';

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @param string $action
	 * @param string $tableName
	 * @param array $record
	 * @return DataProviderInterface
	 * @throws InvalidDataProviderException
	 */
	static public function createByContent($action, $tableName, array $record = NULL) {
		$dataProvider = NULL;
		$assignmentHandlers = \OliverHader\Mapping\Utility\GeneralUtility::getConfigurationService()->getAssignmentHandlers();

		foreach ($assignmentHandlers as $assignmentHandler) {
			if (empty($assignmentHandler['dataProvider'])) {
				continue;
			}

			/** @var $dataProvider DataProviderInterface */
			$possibleDataProvider = \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($assignmentHandler['dataProvider']);

			if (!$possibleDataProvider instanceof DataProviderInterface) {
				throw new InvalidDataProviderException(
					'DataProvider "' . $assignmentHandlers[$tableName]['dataProvider'] . '" does not implement DataProviderInterface.'
				);
			}

			$possibleDataProvider->setConfiguration($assignmentHandler);

			if ($action === self::ACTION_Assign && $possibleDataProvider->canAssign($tableName, $record)) {
				$dataProvider = $possibleDataProvider;
				break;
			} elseif ($action === self::ACTION_Render && $possibleDataProvider->canRender($tableName, $record)) {
				$dataProvider = $possibleDataProvider;
				break;
			}
		}

		if (empty($dataProvider)) {
			throw new InvalidDataProviderException(
				'No valid data provider for table "' . $tableName . '" found.'
			);
		}

		return $dataProvider;
	}

	/**
	 * @param array $configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return \TYPO3\CMS\Lang\LanguageService
	 */
	protected function getLanguageService() {
		return $GLOBALS['LANG'];
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

}
?>
<?php
namespace OliverHader\Mapping\Service\TypoScript;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class ContextService extends AbstractService {

	/**
	 * @param string $data
	 * @return array
	 */
	public function convertTypoScriptToArray($data) {
		$contexts = array();

		$typoScript = $this->parseTypoScript($data);
		foreach ($typoScript as $key => $value) {
			if (substr($key, -1) === '.' && !empty($typoScript[$key]['xpath'])) {
				$name = substr($key, 0, -1);
				$xpath = $typoScript[$key]['xpath'];

				if (!empty($typoScript[$key]['scope'])) {
					$scope = $typoScript[$key]['scope'];
				}

				if (empty($scope) || !GeneralUtility::inList('inner,outer', $scope)) {
					$scope = 'inner';
				}

				$contexts[$xpath] = array(
					'name' => $name,
					'scope' => $scope,
				);
			}
		}

		return $contexts;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function convertArrayToTypoScript(array $data) {
		$contexts = '';

		foreach ($data as $xpath => $value) {
			if (empty($value['name']) || empty($value['scope'])) {
				continue;
			}

			$contexts .= $value['name'] . ' {' . PHP_EOL;
			$contexts .= "\t" . 'xpath = ' . $xpath . PHP_EOL;
			$contexts .= "\t" . 'scope = ' . $value['scope'] . PHP_EOL;
			$contexts .= '}' . PHP_EOL . PHP_EOL;
		}

		return $contexts;
	}

	/**
	 * @param string $data
	 * @return array|\OliverHader\Mapping\Domain\Model\Context[]
	 */
	public function convertTypoScriptToObjects($data) {
		$contexts = array();
		$array = $this->convertTypoScriptToArray($data);

		foreach ($array as $xpath => $item) {
			$context = $this->createContext();
			$context->setXPath($xpath);
			$context->setName($item['name']);
			$context->setScope($item['scope']);
			$contexts[] = $context;
		}

		return $contexts;
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Model\Context
	 */
	protected function createContext() {
		return $this->objectManager->get(
			'OliverHader\Mapping\Domain\Model\Context'
		);
	}

}
?>
<?php
namespace OliverHader\Mapping\Service;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
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
class ElementService implements SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @param string $data
	 * @return array
	 */
	public function convertTypoScriptToArray($data) {
		$elements = array();

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

				$elements[$xpath] = array(
					'name' => $name,
					'scope' => $scope,
				);
			}
		}

		return $elements;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function convertArrayToTypoScript(array $data) {
		$elements = '';

		foreach ($data as $xpath => $value) {
			if (empty($value['name']) || empty($value['scope'])) {
				continue;
			}

			$elements .= $value['name'] . ' {' . PHP_EOL;
			$elements .= "\t" . 'xpath = ' . $xpath . PHP_EOL;
			$elements .= "\t" . 'scope = ' . $value['scope'] . PHP_EOL;
			$elements .= '}' . PHP_EOL . PHP_EOL;
		}

		return $elements;
	}

	/**
	 * @param string $data
	 * @return array|\OliverHader\Mapping\Domain\Model\Element[]
	 */
	public function convertTypoScriptToObjects($data) {
		$elements = array();
		$array = $this->convertTypoScriptToArray($data);

		foreach ($array as $xpath => $item) {
			$element = $this->createElement();
			$element->setXPath($xpath);
			$element->setName($item['name']);
			$element->setScope($item['scope']);
			$elements[] = $element;
		}

		return $elements;
	}

	/**
	 * @param string $data
	 * @return array
	 */
	protected function parseTypoScript($data) {
		/** @var $parser TypoScriptParser */
		$parser = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
		$parser->parse($data);
		return (array) $parser->setup;
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Model\Element
	 */
	protected function createElement() {
		return $this->objectManager->get(
			'OliverHader\Mapping\Domain\Model\Element'
		);
	}

}
?>
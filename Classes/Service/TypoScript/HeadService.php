<?php
namespace OliverHader\Mapping\Service\TypoScript;

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
class HeadService extends AbstractService {

	/**
	 * @param string $data
	 * @return array
	 */
	public function convertTypoScriptToArray($data) {
		$heads = array();

		$typoScript = $this->parseTypoScript($data);
		foreach ($typoScript as $key => $value) {
			if (substr($key, -1) === '.' && !empty($typoScript[$key]['xpath'])) {
				$xpath = $typoScript[$key]['xpath'];
				$heads[$xpath] = TRUE;
			}
		}

		return $heads;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function convertArrayToTypoScript(array $data) {
		$heads = '';
		$index = 0;

		foreach ($data as $xpath => $value) {
			if (empty($value)) {
				continue;
			}

			$index++;
			$name = $index * 10;

			$heads .= $name . ' {' . PHP_EOL;
			$heads .= "\t" . 'xpath = ' . $xpath . PHP_EOL;
			$heads .= '}' . PHP_EOL . PHP_EOL;
		}

		return $heads;
	}

	/**
	 * @param string $data
	 * @return array|\OliverHader\Mapping\Domain\Model\Head[]
	 */
	public function convertTypoScriptToObjects($data) {
		$heads = array();
		$array = $this->convertTypoScriptToArray($data);

		foreach ($array as $xpath => $item) {
			$head = $this->createHead();
			$head->setXPath($xpath);
			$heads[] = $head;
		}

		return $heads;
	}

	/**
	 * @return \OliverHader\Mapping\Domain\Model\Head
	 */
	protected function createHead() {
		return $this->objectManager->get(
			'OliverHader\Mapping\Domain\Model\Head'
		);
	}

}
?>
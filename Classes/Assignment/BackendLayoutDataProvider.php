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
class BackendLayoutDataProvider extends AbstractDataProvider implements DataProviderInterface {

	/**
	 * @param array $record
	 * @return array
	 */
	public function getNodes(array $record) {
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
	 * @param string $data
	 * @return array
	 */
	protected function parseTypoScript($data) {
		/** @var $parser \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser */
		$parser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
		$parser->parse($data);
		return (array) $parser->setup;
	}

}
?>
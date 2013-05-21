<?php
namespace OliverHader\Mapping;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

		$structureId = (int) $this->getConfigurationProperty('structure');
		$renderAs = (string) $this->getConfigurationProperty('renderAs');

		if (!empty($structureId)) {
			$content = $this->getProcessor()->get($structureId, $renderAs);
		}

		return $content;
	}

	/**
	 * @param string $property
	 * @return string
	 */
	protected function getConfigurationProperty($property) {
		$value = '';

		if (!empty($this->configuration[$property])) {
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
	 * @return \OliverHader\Mapping\Processor
	 */
	protected function getProcessor() {
		return $this->getObjectManager()->get(
			'OliverHader\\Mapping\\Processor'
		);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected function getObjectManager() {
		return GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Extbase\\Object\\ObjectManager'
		);
	}

}
?>
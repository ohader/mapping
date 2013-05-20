<?php
namespace OliverHader\Mapping\Controller;
use OliverHader\Mapping\Domain\Model\Structure;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Oliver Hader <oliver.hader@typo3.org>
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
class StructureController extends AbstractController {

	/**
	 * http://ip61.local/typo3/mod.php?M=user_MappingMapping&tx_mapping_user_mappingmapping%5Baction%5D=load&tx_mapping_user_mappingmapping%5Bcontroller%5D=Structure&tx_mapping_user_mappingmapping%5Bstructure%5D=1
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 * @return string
	 */
	public function loadAction(Structure $structure) {
		$result = array(
			'uid' => $structure->getUid(),
			'title' => $structure->getTitle(),
			'elements' => $this->convertElementsToArray(
				$structure->getElements()
			),
		);
		return json_encode($result);
	}

	/**
	 * @param Structure $structure
	 * @return string
	 */
	public function htmlAction(Structure $structure) {
#		$this->response->setHeader('Pragma', 'no-cache');
#		$this->response->setHeader('Cache-Control', 'no-cache, must-revalidate');
		return $this->getHtml($structure->getTemplate());
	}

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function createAction(Structure $structure) {
		$this->structureRepository->add($structure);
		$this->persistenceManager->persistAll();

		$result = array('result' => TRUE, 'uid' => $structure->getUid());
		return json_encode($result);
	}

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 * @param string $elements
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function updateAction(Structure $structure, $elements = NULL) {
		if ($elements !== NULL) {
			$structure->setElements(
				$this->convertElementsToTypoScript(json_decode($elements, TRUE))
			);
		}

		$this->structureRepository->update($structure);

		$result = array('result' => TRUE);
		return json_encode($result);
	}

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function deleteAction(Structure $structure) {
		$this->structureRepository->remove($structure);

		$result = array('result' => TRUE);
		return json_encode($result);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	protected function getHtml($file) {
		$filePath = GeneralUtility::getFileAbsFileName($file);

		$document = new \DOMDocument();
		$document->load($filePath);

		/** @var $stylesheet \DOMNode */
		foreach ($document->getElementsByTagName('link') as $stylesheet) {
			$isStylesheet = (
				$stylesheet->attributes->getNamedItem('rel') &&
				$stylesheet->attributes->getNamedItem('href') &&
				$stylesheet->attributes->getNamedItem('rel')->nodeValue === 'stylesheet'
			);

			if ($isStylesheet) {
				$href = $stylesheet->attributes->getNamedItem('href')->nodeValue;
				if (preg_match('#^([a-z]+:/)?/#i', $href)) {
					continue;
				}

				$uri = dirname($filePath) . '/' . $href;
				$uri = substr($uri, strlen(PATH_site));

				if (TYPO3_MODE === 'BE' && $uri !== FALSE) {
					$uri = '../' . $uri;
				}

				$stylesheet->attributes->getNamedItem('href')->nodeValue = $uri;
			}
		}

		$stylesheet = $document->createElement('link');
		$stylesheet->setAttribute('type', 'text/css');
		$stylesheet->setAttribute('rel', 'stylesheet');
		$stylesheet->setAttribute('href', $this->getResourceUri('Css/main.css'));

		$head = $document->getElementsByTagName('head')->item(0);
		$head->appendChild($stylesheet);

		$content = $document->saveHTML();

		return $content;
	}

	protected function convertElementsToTypoScript(array $data) {
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

	protected function convertElementsToArray($data) {
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

	protected function parseTypoScript($data) {
		/** @var $parser TypoScriptParser */
		$parser = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
		$parser->parse($data);
		return (array) $parser->setup;
	}

}
?>
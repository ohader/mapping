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
	 * @var \OliverHader\Mapping\Service\ElementService
	 * @inject
	 */
	protected $elementService;

	/**
	 * @var \OliverHader\Mapping\Service\MarkupService
	 * @inject
	 */
	protected $markupService;

	/**
	 * @param \OliverHader\Mapping\Domain\Model\Structure $structure
	 * @return string
	 */
	public function loadAction(Structure $structure) {
		$result = array(
			'uid' => $structure->getUid(),
			'title' => $structure->getTitle(),
			'elements' => $this->elementService->convertTypoScriptToArray(
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
				$this->elementService->convertArrayToTypoScript(json_decode($elements, TRUE))
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
	 * @param string $fileName
	 * @return string
	 */
	protected function getHtml($fileName) {
		/** @var $document \DOMDocument */
		$document = $this->markupService->getDomDocument($fileName);

		/** @var $stylesheet \DOMElement */
		$stylesheet = $document->createElement('link');
		$stylesheet->setAttribute('type', 'text/css');
		$stylesheet->setAttribute('rel', 'stylesheet');
		$stylesheet->setAttribute('href', $this->getResourceUri('Css/main.css'));

		$head = $document->getElementsByTagName('head')->item(0);
		$head->appendChild($stylesheet);

		$content = $document->saveHTML();

		return $content;
	}

}
?>
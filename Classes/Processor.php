<?php
namespace OliverHader\Mapping;
use OliverHader\Mapping\Domain\Model\Element;
use OliverHader\Mapping\Domain\Object\ProcessorTask;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
class Processor implements SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \OliverHader\Mapping\Service\StructureService
	 * @inject
	 */
	protected $structureService;

	/**
	 * @var \OliverHader\Mapping\Service\MarkupService
	 * @inject
	 */
	protected $markupService;

	/**
	 * @param ProcessorTask $processorTask
	 * @return NULL|string
	 */
	public function execute(ProcessorTask $processorTask) {
		$structure = $processorTask->getStructure();
		$variableService = $processorTask->getVariableService();
		$document = $this->markupService->getDomDocument($structure->getTemplate());
		$defaultNamespace = $document->documentElement->lookupNamespaceUri(NULL);
		$heads = $this->structureService->getHeads($structure);
		$elements = $this->structureService->getElements($structure);

		$xpath = new \DOMXPath($document);
		// Register default namespace to blank (default)
		if (!empty($defaultNamespace)) {
			$xpath->registerNamespace('ns', $defaultNamespace);
		}

		foreach ($heads as $head) {
			$namespaceXPath = $head->getXPath();
			if (!empty($defaultNamespace)) {
				$namespaceXPath = $this->markupService->getNamespaceXPath($namespaceXPath, 'ns');
			}
			$nodeList = $xpath->query($namespaceXPath);

			if ($nodeList === FALSE || empty($nodeList->length)) {
				continue;
			}

			$node = $nodeList->item(0);
			$this->getFrontend()->getPageRenderer()->addHeaderData($document->saveHTML($node));
		}

		foreach ($elements as $element) {
			$namespaceXPath = $element->getXPath();
			if (!empty($defaultNamespace)) {
				$namespaceXPath = $this->markupService->getNamespaceXPath($namespaceXPath, 'ns');
			}

			$nodeList = $xpath->query($namespaceXPath);

			if ($nodeList === FALSE || empty($nodeList->length)) {
				continue;
			}

			$node = $nodeList->item(0);
			$processorTask->addElement($element);

			if ($element->getScope() === Element::SCOPE_Inner) {
				foreach ($node->childNodes as $childNode) {
					$node->removeChild($childNode);
				}
				$node->nodeValue = $variableService->substitute($element);
			} elseif ($element->getScope() === Element::SCOPE_Outer) {
				$parentNode = $node->parentNode;
				$textNode = $document->createTextNode(
					$node->nodeValue = $variableService->substitute($element)
				);
				$parentNode->replaceChild($textNode, $node);
			}
		}

		$content = $this->getInnerHtml(
			$document->getElementsByTagName('body')->item(0)
		);

		if ($processorTask->getAssignmentDataProvider() !== NULL) {
			$contentReplacement = $processorTask->getAssignmentDataProvider()->getContentReplacement($processorTask);

			$content = str_replace(
				array_keys($contentReplacement),
				array_values($contentReplacement),
				$content
			);
		}

		return $content;
	}

	protected function getInnerHtml(\DOMNode $node) {
		$innerHtml = '';
		/** @var $childNode \DOMNode */
		foreach ($node->childNodes as $childNode) {
			$innerHtml .= $childNode->ownerDocument->saveHTML($childNode);
		}
		return $innerHtml;
	}

	/**
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

}
?>
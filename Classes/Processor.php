<?php
namespace OliverHader\Mapping;
use OliverHader\Mapping\Domain\Model\Element;
use OliverHader\Mapping\Domain\Model\Structure;
use OliverHader\Mapping\Service\Variable\AbstractVariableService;
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
class Processor implements SingletonInterface {

	const RENDER_FLUID = 'fluid';
	const RENDER_MARKER = 'marker';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \OliverHader\Mapping\Domain\Repository\StructureRepository
	 * @inject
	 */
	protected $structureRepository;

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
	 * @param integer $structureId
	 * @param string $renderAs
	 * @return NULL|string
	 */
	public function get($structureId, $renderAs = NULL) {
		/** @var $structure \OliverHader\Mapping\Domain\Model\Structure */
		$structure = $this->structureRepository->findByUid($structureId);

		if (empty($structure)) {
			return NULL;
		}

		if (empty($renderAs)) {
			$renderAs = self::RENDER_FLUID;
		}

		$variableService = $this->getVariableService($renderAs);
		$document = $this->markupService->getDomDocument($structure->getTemplate());
		$elements = $this->elementService->convertTypoScriptToObjects($structure->getElements());
		$xpath = new \DOMXPath($document);
		// Register default namespace to blank (default)
		$xpath->registerNamespace(
			'ns', $document->documentElement->lookupNamespaceUri(NULL)
		);

		foreach ($elements as $element) {
			$namespacePath = $this->getNamespaceXPath($element->getXPath(), 'ns');
			$nodeList = $xpath->query($namespacePath);

			if ($nodeList === FALSE) {
				continue;
			}

			$node = $nodeList->item(0);

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

		return $document->saveHTML();
	}

	/**
	 * @param string $value
	 * @param string $namespace
	 * @return string
	 */
	protected function getNamespaceXPath($value, $namespace) {
		$items = explode('/', $value);

		foreach ($items as $index => &$item) {
			if ($index === 0 || empty($item) || strpos($item, '*') === 0) {
				continue;
			}

			$item = $namespace . ':' . $item;
		}

		return implode('/', $items);
	}

	/**
	 * @param string $renderAs
	 * @return AbstractVariableService
	 */
	protected function getVariableService($renderAs) {
		$renderAs = lcfirst(strtolower($renderAs));
		return $this->objectManager->get('OliverHader\\Mapping\\Service\\Variable\\' . $renderAs . 'VariableService');
	}

}
?>
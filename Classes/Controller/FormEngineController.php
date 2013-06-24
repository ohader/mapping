<?php
namespace OliverHader\Mapping\Controller;

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
class FormEngineController extends AbstractController {

	/**
	 * @var \OliverHader\Mapping\Service\StructureService
	 * @inject
	 */
	protected $structureService;

	/**
	 * @return string
	 */
	public function indexAction() {
		return $this->errorAction();
	}

	/**
	 * @return void
	 */
	public function assignmentAction() {
		$tableName = $this->settings['FormEngine']['tableName'];
		$record = $this->settings['FormEngine']['record'];

		$dataProvider = \OliverHader\Mapping\Assignment\AbstractDataProvider::create($tableName);

		$data = array(
			'structures' => $this->getDataStructures(),
			'nodes' => $dataProvider->getNodes($record),
		);

		$this->view->assign('data', $data);
	}

	protected function getDataStructures() {
		$dataStructures = array();

		/** @var $structure \OliverHader\Mapping\Domain\Model\Structure */
		foreach ($this->structureRepository->findAll() as $structure) {
			$dataStructure = array(
				'identifier' => $structure->getUid(),
				'title' => $structure->getTitle(),
				'elements' => array(),
				'contexts' => array(
					'all' => array(
						'name' => 'all',
						'elements' => array(),
					),
					'body' => array(
						'name' => 'body',
						'elements' => array(),
					),
				),
			);

			foreach ($this->structureService->getElements($structure) as $element) {
				$dataStructure['elements'][] = $element->getName();
				$dataStructure['contexts']['all']['elements'][] = $element->getName();
				$dataStructure['contexts']['body']['elements'][] = $element->getName();
			}

			foreach ($this->structureService->getContexts($structure) as $context) {
				$dataStructure['contexts'][$context->getName()] = array(
					'name' => $context->getName(),
					'elements' => array(),
				);

				foreach ($this->structureService->getElementsPerContext($structure, $context) as $element) {
					$dataStructure['contexts'][$context->getName()]['elements'][] = $element->getName();
				}
			}

			$dataStructures[$structure->getUid()] = $dataStructure;
		}

		return $dataStructures;
	}

}
?>
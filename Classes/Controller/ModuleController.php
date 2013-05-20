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
class ModuleController extends AbstractController {
	/**
	 * @return void
	 */
	public function indexAction() {
		$settings = array(
			'urls' => array(
				'Module' => array(
					'data' => $this->uriBuilder->uriFor('data', array(), 'Module'),
				),
				'Structure' => array(
					'load' => $this->uriBuilder->uriFor('load', array(), 'Structure'),
					'html' => $this->uriBuilder->uriFor('html', array(), 'Structure'),
					'update' => $this->uriBuilder->uriFor('update', array(), 'Structure'),
				),
			),
			'arguments' => array(
				'prefix' => $this->getArgumentPrefix(),
			),
		);

		$this->view->assign('settings', $settings);
		$this->view->assign('structures', $this->structureRepository->findAll());
	}

	/**
	 * @return string
	 */
	public function dataAction() {
		return json_encode($this->getData());
	}

	/**
	 * @return array
	 */
	protected function getData() {
		$data = array(
			'structures' => $this->getDataStructures(
				$this->structureRepository->findAll()
			),
		);
		return $data;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|\OliverHader\Mapping\Domain\Model\Structure[] $structures
	 * @return array
	 */
	protected function getDataStructures(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $structures) {
		$dataStructures = array();

		foreach ($structures as $structure) {
			$identifier = $structure->getUid();
			$dataStructures[$identifier] = array(
				'identifier' => $identifier,
				'title' => $structure->getTitle(),
			);
		}

		return $dataStructures;
	}

}
?>
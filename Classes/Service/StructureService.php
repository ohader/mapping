<?php
namespace OliverHader\Mapping\Service;
use TYPO3\CMS\Core\SingletonInterface;
use OliverHader\Mapping\Domain\Model\Structure;

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
class StructureService implements SingletonInterface {

	/**
	 * @var \OliverHader\Mapping\Service\TypoScript\ContextService
	 * @inject
	 */
	protected $contextService;

	/**
	 * @var \OliverHader\Mapping\Service\TypoScript\HeadService
	 * @inject
	 */
	protected $headService;

	/**
	 * @var \OliverHader\Mapping\Service\TypoScript\ElementService
	 * @inject
	 */
	protected $elementService;

	/**
	 * @param Structure $structure
	 * @return array|\OliverHader\Mapping\Domain\Model\Context[]
	 */
	public function getContexts(Structure $structure) {
		return $this->contextService->convertTypoScriptToObjects($structure->getContexts());
	}

	/**
	 * @param Structure $structure
	 * @return array|\OliverHader\Mapping\Domain\Model\Head[]
	 */
	public function getHeads(Structure $structure) {
		return $this->headService->convertTypoScriptToObjects($structure->getHeads());
	}

	/**
	 * @param Structure $structure
	 * @return array|\OliverHader\Mapping\Domain\Model\Element[]
	 */
	public function getElements(Structure $structure) {
		return $this->elementService->convertTypoScriptToObjects($structure->getElements());
	}

}
?>
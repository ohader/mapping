<?php
namespace OliverHader\Mapping\Domain\Model;

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
class Element extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	const SCOPE_Inner = 'inner';
	const SCOPE_Outer = 'outer';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $xPath;

	/**
	 * @var string
	 */
	protected $scope;

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = (string) $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $xPath
	 */
	public function setXPath($xPath) {
		$this->xPath = (string) $xPath;
	}

	/**
	 * @return string
	 */
	public function getXPath() {
		return $this->xPath;
	}

	/**
	 * @param string $scope
	 */
	public function setScope($scope) {
		$this->scope = (string) $scope;
	}

	/**
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}
}
?>
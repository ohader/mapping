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
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
	 * @var \TYPO3\CMS\Extbase\Service\ExtensionService
	 * @inject
	 */
	protected $extensionService;

	/**
	 * @var \OliverHader\Mapping\Domain\Repository\StructureRepository
	 * @inject
	 */
	protected $structureRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
	 * @inject
	 */
	protected $persistenceManager;

	protected function getResourceUri($path, $absolute = FALSE) {
		$extensionName = $this->request->getControllerExtensionName();

		$uri = 'EXT:' . \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName) . '/Resources/Public/' . $path;
		$uri = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($uri);
		$uri = substr($uri, strlen(PATH_site));

		if (TYPO3_MODE === 'BE' && $absolute === FALSE && $uri !== FALSE) {
			$uri = '../' . $uri;
		}
		if ($absolute === TRUE) {
			$uri = $this->request->getBaseURI() . $uri;
		}

		return $uri;
	}

	/**
	 * @return string
	 */
	protected function getRelativeBaseUrl() {
		return \TYPO3\CMS\Core\Utility\PathUtility::getRelativePathTo(
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mapping')
		);
	}

	/**
	 * @return string
	 */
	protected function getArgumentPrefix() {
		return $this->extensionService->getPluginNamespace(
			$this->request->getControllerExtensionName(),
			$this->request->getPluginName()
		);
	}

}
?>
<?php
namespace OliverHader\Mapping\Service;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class MarkupService implements SingletonInterface {

	/**
	 * @param string $fileName
	 * @return string
	 */
	public function getHtml($fileName) {
		return $this->getDomDocument($fileName)->saveHTML();
	}

	/**
	 * @param string $fileName
	 * @return \DOMDocument
	 */
	public function getDomDocument($fileName) {
		$filePath = GeneralUtility::getFileAbsFileName($fileName);

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

		return $document;
	}

}
?>
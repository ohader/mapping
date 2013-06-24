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
	 * @throws \RuntimeException
	 */
	public function getDomDocument($fileName) {
		$filePath = GeneralUtility::getFileAbsFileName($fileName);
		$directoryPath = dirname($filePath);

		$document = new \DOMDocument();
		$success = $document->loadHTML(file_get_contents($filePath));

		if ($success === FALSE) {
			throw new \RuntimeException('Template cannot be loaded from ' . $filePath);
		}

		$xpath = new \DOMXPath($document);

		foreach ($xpath->query('//@href') as $attribute) {
			$this->sanitizeUri($directoryPath, $attribute);
		}

		foreach ($xpath->query('//@src') as $attribute) {
			$this->sanitizeUri($directoryPath, $attribute);
		}

		return $document;
	}

	/**
	 * @param string $directoryPath
	 * @param \DOMAttr $attribute
	 */
	protected function sanitizeUri($directoryPath, \DOMAttr $attribute) {
		$parentNode = $attribute->parentNode;

		$isValid = (
			$parentNode
			&& !empty($attribute->nodeValue)
			&& $attribute->nodeValue !== '#'
 			&& (
				$attribute->nodeName === 'src'
			||
				$parentNode->nodeName === 'link'
					&& $parentNode->attributes->getNamedItem('rel')
					&& $parentNode->attributes->getNamedItem('rel')->nodeValue === 'stylesheet'
			)
		);

		if (!$isValid) {
			return;
		}

		$uri = $attribute->nodeValue;

		if (!preg_match('#^([a-z]+:/)?/#i', $uri)) {
			$sanitizedUri = rtrim($directoryPath, '/') . '/' . $uri;
			$sanitizedUri = substr($sanitizedUri, strlen(PATH_site));

			if (TYPO3_MODE === 'BE' && $sanitizedUri !== FALSE) {
				$sanitizedUri = '../' . $sanitizedUri;
			}

			$attribute->nodeValue = $sanitizedUri;
		}
	}

}
?>
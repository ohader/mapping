<?php
namespace OliverHader\Mapping\ViewHelpers\Be;

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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class BlockViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\ContainerViewHelper {

	/**
	 * Render start page with template.php and pageTitle
	 *
	 * @param string $pageTitle title tag of the module. Not required by default, as BE modules are shown in a frame
	 * @param boolean $loadJQuery Whether to load jQuery
	 * @param array $addCssFiles Custom CSS files to be loaded
	 * @param array $addJsFiles Custom JavaScript files to be loaded
	 * @param string $requireJsModule Name of the RequireJS module to be loaded
	 * @return string
	 * @see template
	 * @see t3lib_PageRenderer
	 */
	public function render($pageTitle = '', $loadJQuery = TRUE, $addCssFiles = array(), $addJsFiles = array(), $requireJsModule = NULL) {
		$pageRenderer = $this->getDocument()->getPageRenderer();

		if ($loadJQuery) {
			$pageRenderer->loadJquery(NULL, NULL, $pageRenderer::JQUERY_NAMESPACE_NONE);
		}

		if ($requireJsModule !== NULL) {
			$pageRenderer->loadRequireJsModule($requireJsModule);
		}

		if (is_array($addCssFiles) && count($addCssFiles) > 0) {
			foreach ($addCssFiles as $addCssFile) {
				$pageRenderer->addCssFile($addCssFile);
			}
		}
		if (is_array($addJsFiles) && count($addJsFiles) > 0) {
			foreach ($addJsFiles as $addJsFile) {
				$pageRenderer->addJsFile($addJsFile);
			}
		}

		$output = $this->renderChildren();
		return $output;
	}

	/**
	 * @return \TYPO3\CMS\Backend\Template\DocumentTemplate
	 */
	protected function getDocument() {
		return $GLOBALS['SOBE']->doc;
	}

}


?>
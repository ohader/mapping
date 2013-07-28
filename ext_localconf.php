<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['cObjTypeAndClass'][] = array(
	'MAPPING',
	'OliverHader\Mapping\MappingContentObject'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
	'mapping',
	'setup',
	'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mapping/Configuration/TypoScript/setup.txt">'
);

\OliverHader\Mapping\Utility\GeneralUtility::registerAssignmentHandler(
	'backend_layout',
	'backend_layout',
	'assignments',
	'OliverHader\\Mapping\\Assignment\\BackendLayoutDataProvider'
);

\OliverHader\Mapping\Utility\GeneralUtility::registerAssignmentHandler(
	'dce',
	'tx_dce_domain_model_dce',
	'assignments',
	'OliverHader\\Mapping\\Assignment\\DceDataProvider'
);
?>
<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'OliverHader.' . $_EXTKEY,
		'tools',
		'mapping',
		'',
		array(
			'Module' => 'index,data',
			'Structure' => 'load,html,create,update,delete',
			// FormEngine.index action shows error since this controller
			// may not be used in regular module context
			'FormEngine' => 'index',
		),
		array(
			'access' => 'admin',
			'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf',
		)
	);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mapping_domain_model_structure');
$TCA['tx_mapping_domain_model_structure'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:tx_mapping_domain_model_structure',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'enablecolumns' => array(
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Structure.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mapping_domain_model_structure.png'
	),
);

\OliverHader\Mapping\Utility\GeneralUtility::applyAssignmentHandlerConfiguration();
?>
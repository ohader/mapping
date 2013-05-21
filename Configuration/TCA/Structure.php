<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_mapping_domain_model_structure'] = array(
	'ctrl' => $TCA['tx_mapping_domain_model_structure']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'title',
	),
	'types' => array(
		'1' => array('showitem' => 'title, template, heads, elements'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:tx_mapping_domain_model_structure.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'template' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:tx_mapping_domain_model_structure.template',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'txt,htm,html',
				'max_size' => 1024,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'heads' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:tx_mapping_domain_model_structure.heads',
			'config' => array(
				'type' => 'text',
				'rows' => 10,
				'cols' => 30,
				'eval' => 'trim'
			),
		),
		'elements' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mapping/Resources/Private/Language/locallang_db.xlf:tx_mapping_domain_model_structure.elements',
			'config' => array(
				'type' => 'text',
				'rows' => 10,
				'cols' => 30,
				'eval' => 'trim'
			),
		),
	),
);
?>
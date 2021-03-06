<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$enableRoles = false;

// Backend Modul einbinden
if (
	TYPO3_MODE == 'BE' && tx_rnbase_configurations::getExtensionCfgValue('t3users', 'activateBeModule')
) {
	tx_rnbase_util_Extensions::addModule('web', 'txt3usersM1', '',
			tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod/');

	/**
	 * Callcenter Panel
	 */
	tx_rnbase_util_Extensions::insertModuleFunction('web_txt3usersM1','tx_t3users_mod_FeUser',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod/class.tx_t3users_mod_FeUser.php',
		'LLL:EXT:t3users/mod/locallang.xml:tx_t3users_module_name'
	);

	// Einbindung einer PageTSConfig
	tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'.$_EXTKEY.'/mod/pageTSconfig.txt">');
}

if($enableRoles) {
	$TCA['tx_t3users_roles'] = array (
		'ctrl' => array (
			'title'     => 'LLL:EXT:t3users/locallang_db.xml:tx_t3users_roles',
			'label'     => 'name',
			'tstamp'    => 'tstamp',
			'crdate'    => 'crdate',
			'cruser_id' => 'cruser_id',
	//		'type' => 'name',
	//		'dividers2tabs' => true,
			'default_sortby' => 'ORDER BY name',
			'delete' => 'deleted',
			'enablecolumns' => array (
			),
			'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
			'iconfile'          => 'EXT:t3users/icon_tx_t3users_tables.gif',
		),
		'feInterface' => array (
			'fe_admin_fieldList' => 'name',
		)
	);

	$TCA['tx_t3users_rights'] = array(
		'ctrl' => array(
			'title'     => 'LLL:EXT:t3users/locallang_db.xml:tx_t3users_rights',
			'label' => 'sign',
			'readOnly' => 1,	// This should always be true, as it prevents the static data from being altered
			'adminOnly' => 1,
			'rootLevel' => 1,
			'is_static' => 1,
			'default_sortby' => 'ORDER BY sign',
			'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca.php',
			'iconfile'          => 'EXT:t3users/icon_tx_t3users_tables.gif',
		),
		'interface' => array(
			'showRecordFieldList' => 'sign'
		)
	);
}


if(!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
	// TCA registration for 4.5
	$TCA['tx_t3users_log'] = require tx_rnbase_util_Extensions::extPath('t3users').'Configuration/TCA/tx_t3users_log.php';
}

/* If date2cal is loaded, include it as a wizard */
$date2CalTCA = Array ();
if(tx_rnbase_util_Extensions::isLoaded('date2cal')) {
	$date2CalTCA = Array (
		'type' => 'userFunc',
		'userFunc' => 'EXT:date2cal/class.tx_date2cal_wizard.php:tx_date2cal_wizard->renderWizard',
		'evalValue' => 'date',
	);
	if(@is_dir(tx_rnbase_util_Extensions::extPath('date2cal').'/src')){
		$date2CalTCA['userFunc'] = 'EXT:date2cal/src/class.tx_date2cal_wizard.php:tx_date2cal_wizard->renderWizard';
	}
}

tx_rnbase::load('tx_rnbase_util_TCA');
tx_rnbase_util_TCA::loadTCA('fe_users');
$TCA['fe_users']['columns']['username']['config']['eval'] = 'nospace,uniqueInPid,required';

if($enableRoles) {
	tx_rnbase_util_Extensions::addTCAcolumns('fe_users', Array(
			't3usersroles' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:t3users/locallang_db.xml:fe_users_t3usersroles',
				'config' => Array (
					'type' => 'select',
					'renderType' => 'selectMultipleSideBySide',
					'allowed' => 'tx_t3users_roles',
					'size' => 10,
					'autoSizeMax' => 50,
					'minitems' => 0,
					'maxitems' => 100,
					'foreign_table' => 'tx_t3users_roles',
					'MM' => 'tx_t3users_role2owner_mm',
					'MM_foreign_select' => 1,
					'MM_opposite_field' => 'owner',
					'MM_match_fields' => Array (
						'tablenames' => 'fe_users',
					),
				)
			),
		)
	);
	$TCA['fe_users']['types']['0']['showitem'] = str_replace(', starttime', ',t3usersroles, starttime', $TCA['fe_users']['types']['0']['showitem']);
}
tx_rnbase_util_Extensions::addTCAcolumns('fe_users', Array(
		// don't display in BE, but define it in TCA so that this column is included in fe_user-Model!
		'confirmstring' => Array (),
		'confirmtimeout' => Array (),
)
);


if (intval(tx_rnbase_configurations::getExtensionCfgValue('t3users','extendTCA'))) {
	$feUsersExtendedFields = array(
		'gender' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:t3users/locallang_db.xml:fe_users.gender',
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:t3users/locallang_db.xml:fe_users_gender_mr', '0'),
					Array('LLL:EXT:t3users/locallang_db.xml:fe_users_gender_ms', '1'),
				),
			)
		),
		'birthday' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3users/locallang_db.xml:fe_users.birthday',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '10',
				'default' => '00-00-0000',
				'wizards' => Array (
					'calendar' => $date2CalTCA
				)
			)
		),
	);
	// ab TYPO3 6.2 sind die Namensfelder direkt im Core enthalten
	if (!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
		$feUsersExtendedFields = array_merge(
			$feUsersExtendedFields,
			array(
				'first_name' => Array (
					'exclude' => 0,
					'label' => 'LLL:EXT:t3users/locallang_db.xml:fe_users.first_name',
					'config' => Array (
						'type' => 'input',
						'size' => '20',
						'max' => '50',
						'eval' => 'trim',
						'default' => ''
					)
				),
				'last_name' => Array (
					'exclude' => 0,
					'label' => 'LLL:EXT:t3users/locallang_db.xml:fe_users.last_name',
					'config' => Array (
						'type' => 'input',
						'size' => '20',
						'max' => '50',
						'eval' => 'trim',
						'default' => ''
					)
		 		)
			)
		);
	}

	tx_rnbase_util_Extensions::addTCAcolumns('fe_users', $feUsersExtendedFields);

	tx_rnbase_util_Extensions::addToAllTCAtypes('fe_users', 'birthday','','before:address');

	if(!tx_rnbase_util_Extensions::isLoaded('sr_feuser_register')) {
		if (tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
			tx_rnbase_util_Extensions::addToAllTCAtypes('fe_users', 'gender,title','','before:birthday');
		}
		else {
			tx_rnbase_util_Extensions::addToAllTCAtypes('fe_users', 'first_name,last_name,gender,title','','before:birthday');
		}
	}
}
if (intval(tx_rnbase_configurations::getExtensionCfgValue('t3users','extendTCA'))) {
	tx_rnbase_util_Extensions::addTCAcolumns('fe_users', Array(
			'lastlogin' => array(
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.lastlogin',
				'config' => array(
					'type' => 'input',
					'readOnly' => '1',
					'size' => '12',
					'eval' => 'datetime',
					'default' => 0,
				)
			),
		)
	);
}


////////////////////////////////
// Plugin anmelden
////////////////////////////////
// Einige Felder ausblenden
$TCA['tt_content']['types']['list']['subtypes_excludelist']['tx_t3users_main']='layout,select_key,pages';

// Das tt_content-Feld pi_flexform einblenden
$TCA['tt_content']['types']['list']['subtypes_addlist']['tx_t3users_main']='pi_flexform';

tx_rnbase_util_Extensions::addPiFlexFormValue('tx_t3users_main','FILE:EXT:'.$_EXTKEY.'/flexform_main.xml');
tx_rnbase_util_Extensions::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:plugin.t3users.label','tx_t3users_main'));


tx_rnbase_util_Extensions::addStaticFile($_EXTKEY,'static/ts/', 'FE User Management');

if (TYPO3_MODE=="BE")	{
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_t3users_controllers_wizicon'] = tx_rnbase_util_Extensions::extPath($_EXTKEY).'controllers/class.tx_t3users_controllers_wizicon.php';

	////////////////////////////////
	// Submodul anmelden
	////////////////////////////////
	tx_rnbase_util_Extensions::insertModuleFunction(
		'web_func',
		'tx_t3users_mod_index',
		tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod/class.tx_t3users_mod_index.php',
		'LLL:EXT:t3users/mod/locallang.xml:tx_t3users_module_name'
	);
}


?>
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Wagner (dev@dmk-ebusiness.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

tx_rnbase::load('tx_rnbase_util_TYPO3');
if (tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
	/**
	 * tx_t3users_FrontendUserAuthenticationBase
	 *
	 * Wrapper für TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication seit TYPO3 6.x
	 *
	 * @package 		TYPO3
	 * @subpackage	 	t3users
	 * @author 			Hannes Bochmann <rene@system25.de>
	 * @license 		http://www.gnu.org/licenses/lgpl.html
	 * 					GNU Lesser General Public License, version 3 or later
	 */
	class tx_t3users_FrontendUserAuthenticationBase
	extends TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication {}
} else {
	/**
	 * tx_t3users_FrontendUserAuthenticationBase
	 *
	 * Wrapper für tslib_feUserAuth bis TYPO3 6.x
	 *
	 * @package 		TYPO3
	 * @subpackage	 	t3users
	 * @author 			Hannes Bochmann <rene@system25.de>
	 * @license 		http://www.gnu.org/licenses/lgpl.html
	 * 					GNU Lesser General Public License, version 3 or later
	 */
	class tx_t3users_FrontendUserAuthenticationBase extends tslib_feUserAuth {}
}

class ux_tslib_feuserauth extends tx_t3users_FrontendUserAuthenticationBase {

	var $beforelastLogin_column = 'beforelastlogin';

	/**
	 * Starts a user session
	 *
	 * @return	void
	 * @see tslib_feUserAuth::start()
	 */
	function start() {

		// Set auto timeout to lifetime, if lifetime set
		if( intval($this->lifetime) > 0 )
			$this->auth_timeout_field = $this->lifetime;

		parent::start();
	}

	/**
	 * Update additional fields for feuser
	 * Than create a user session record.
	 *
	 * @param	array		user data array
	 * @return	void
	 */
	function createUserSession ($tempuser) {
		tx_rnbase::load('tx_rnbase_configurations');
		if (
			   $this->lastLogin_column
			&& $this->beforelastLogin_column
			&& intval(tx_rnbase_configurations::getExtensionCfgValue('t3users','useBeforelastLogin'))
		    ) {
			$tempuser[$this->beforelastLogin_column] = $tempuser[$this->lastLogin_column];
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
									$this->user_table,
									$this->userid_column.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($tempuser[$this->userid_column], $this->user_table),
									array($this->beforelastLogin_column => $tempuser[$this->lastLogin_column])
								);
		}
		return parent::createUserSession($tempuser);
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/t3users/xclasses/class.ux_tslib_feuserauth.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/t3users/xclasses/class.ux_tslib_feuserauth.php']);
}

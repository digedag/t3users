<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Rene Nitzsche (rene@system25.de)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
require_once(t3lib_extMgm::extPath('dam') . 'lib/class.tx_dam_media.php');

tx_rnbase::load('tx_rnbase_view_Base');
tx_rnbase::load('tx_rnbase_util_ListBuilder');
//tx_rnbase::load('tx_dam_media');


/**
 * Viewclass to show a user.
 */
class tx_t3users_views_ShowFeUser extends tx_rnbase_view_Base {
  /**
   * Erstellen des Frontend-Outputs
   */
	function createOutput($template, &$viewData, &$configurations, &$formatter){

    // Die ViewData bereitstellen
    $feuser =& $viewData->offsetGet('user');
	  $marker = tx_rnbase::makeInstance('tx_t3users_util_FeUserMarker');
    
	  $out = $marker->parseTemplate($template, $feuser, $formatter, 'feuserdetails.feuser.');
    return $out;
  }

  function getMainSubpart() {return '###FEUSER_DETAILS###';}
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3users/views/class.tx_t3users_views_ShowFeUser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3users/views/class.tx_t3users_views_ShowFeUser.php']);
}
?>
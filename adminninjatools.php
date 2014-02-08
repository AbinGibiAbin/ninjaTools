<?php

/**
 * Generic XML Import tab for admin panel, AdminGenericimport.php
 * @category admin
 * PrestashopNinja.com 2012
 */

function printR($array)
{
        echo '<pre>';
        print_r($array);
        echo '</pre>';
}

class adminninjatools extends AdminTab
{

        public function __construct()
        {
                $this->dirname = dirname(__FILE__);
                $this->_db = Db::getInstance();
                $this->table = 'xml';
                $this->_permissionFile = $this->dirname . '/adminerPermission.php';
                $this->_SSHPermissionFile = $this->dirname . '/SSHAdminerPermission.php';
                parent::__construct();
                $token = Tools::getValue('token');
                $this->id_shop = Configuration::get('PS_SHOP_DEFAULT');
                $this->_moduleURI = Configuration::get('PS_FO_PROTOCOL') . __PS_BASE_URI__ . 'modules/ninjatools/';
                $this->context = Context::getContext();
               
        }

        public function display()
        {
                $debugProfilingStatus = (int)_PS_DEBUG_PROFILING_;
                $displayErrors = ini_get('display_errors');
                $SSLEnabled = Configuration::get('PS_SSL_ENABLED');
                $timezone = Configuration::get('PS_TIMEZONE');
                $siteLive = Configuration::get('PRESTASTORE_LIVE');
                $domain = Configuration::get('PS_SHOP_DOMAIN');
                $domainSSL = Configuration::get('PS_SHOP_DOMAIN_SSL');
                $version = Configuration::get('PS_INSTALL_VERSION');
                $smartyCache = Configuration::get('PS_SMARTY_CACHE');
                $cssThemeCache = Configuration::get('PS_CSS_THEME_CACHE');
                $jsThemeCache = Configuration::get('PS_JS_THEME_CACHE');
                $htaccessCacheControl = Configuration::get('PS_HTACCESS_CACHE_CONTROL');
                $controllerText = strstr($version , '1.5') ? 'controller' : 'tab'; 
                echo '<style>
                .tr1
                {
                        text-decoration:underline; 
                }
                a.tr1:hover
                {
                        color:#abc;
                        text-decoration:none;       
                }
                </style>';
                echo '<h2>' . $this->l('Ninja Tools') . '</h2>';
                echo '<p><table>';
                
                echo '<tr><td><b>' . $this->l('Version:') . '</b> ' . $version . ' | ';
                echo '<b>' . $this->l('Domain:') . '</b> ' . $domain . ' | ';
                echo '<b>' . $this->l('SSL Domain:') . '</b> ' . $domainSSL . ' | ';
                echo '<b>' . $this->l('Timezone:') . '</b> ' . $timezone . ' | ';
                echo '<b>' . $this->l('Site Live:') . '</b> ' . ($siteLive == 1 ? $this->l('Yes') : $this->l('No')) . ' | ';
                echo '<b>' . $this->l('SSL Enabled:') . '</b> ' . ($SSLEnabled == 1 ? $this->l('Yes') : $this->l('No')) . '<br/>';
                echo '<b>' . $this->l('Smarty Cache:') . '</b> ' . ($smartyCache == 1 ? $this->l('Yes') : $this->l('No')) . ' | ';
                echo '<b>' . $this->l('CSS Theme Cache:') . '</b> ' . ($cssThemeCache == 1 ? $this->l('Yes') : $this->l('No')) . ' | ';
                echo '<b>' . $this->l('Javascript Theme Cache:') . '</b> ' . ($jsThemeCache == 1 ? $this->l('Yes') : $this->l('No')) . ' | ';
                echo '<b>' . $this->l('.htaccess Cache Control:') . '</b> ' . ($htaccessCacheControl == 1 ? $this->l('Yes') : $this->l('No')) . '</td></tr>';
                
                echo '</table></p>';
                
                if(!$this->_handlePermissionFile())
                        return FALSE;
                echo '<p style="background:#eee;width:50%"><a class="tr1" href="index.php?controller=adminninjatools&token=' . $this->token . '">' . $this->l('Home') . '</a> | ';
                echo '<a class="tr1" href="' . $this->_moduleURI . 'adminer/?username=' . _DB_USER_ . '&db=' . _DB_NAME_ . '" target ="_new">' . $this->l('Adminer DB Manager') . '</a> | ';
                echo '<a class="tr1" href="' . $this->_moduleURI . 'b374k.php" target ="_new">' . $this->l('b374k Shell Emulator') . '</a> | ';
                if($displayErrors == 'off')
                {
                        echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=errorReporting&errorStatus=on&token=' . $this->token . '">' . $this->l('Turn Dev Mode On') . '</a> | ';
                }
                else
                {
                        echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=errorReporting&errorStatus=off&token=' . $this->token . '">' . $this->l('Turn Dev Mode Off') . '</a> | ';
                }
                if($debugProfilingStatus  == 1)
                {
                        echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=debugProfiling&errorStatus=off&token=' . $this->token . '">' . $this->l('Turn Debug Profiling Off') . '</a> | ';
                }
                else
                {
                        echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=debugProfiling&errorStatus=0&token=' . $this->token . '">' . $this->l('Turn Debug Profiling On') . '</a> | ';
                }
                echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=clearCache&token=' . $this->token . '">' . $this->l('Clear Smarty Cache') . '</a> | ';
                echo '<a class="tr1" href="index.php?' . $controllerText . '=adminninjatools&action=customstats&token=' . $this->token . '">' . $this->l('Quick Stats') . '</a></p>';
                $action = Tools::getValue('action');
                switch($action)
                {
                        case 'clearCache':
                                if(!$this->_clearCache())
                                return FALSE;
                                break;
                        
                        case 'customstats':
                                $this->_quickStats();
                                break;
                        
                        case 'errorReporting':
                                $errorStatus = Tools::getValue('errorStatus');
                                if($errorStatus != 'on')
                                        $errorStatus = 'off';
                                
                                if($this->_errorReporting($errorStatus) == FALSE)
                                return FALSE;
                        
                                echo '<script type="text/javascript">top.location="index.php?controller=adminninjatools&token=' . $this->token . '"</script>';
                                exit;
                        
                                break;
                        
                        case 'debugProfiling':
                                if($debugProfilingStatus  != TRUE)
                                        $debugProfilingStatus  = FALSE;
                                
                                if($this->_debugProfiling($debugProfilingStatus) == FALSE)
                                return FALSE;
                        
                                echo '<script type="text/javascript">top.location="index.php?controller=adminninjatools&token=' . $this->token . '"</script>';
                                exit;
                        
                                break;
                        
                        default:
                                $this->_displayHome();
                }
                
        }
        
        private function _clearCache()
        {
                Tools::clearSmartyCache();
		Media::clearCache();
              
                echo '<div class="info">' . $this->l('File cache cleared.') . '</div>';
                return TRUE; 
        }
        
        private function _handlePermissionFile()
        {
                if(!@is_writable($this->dirname))
                {
                        echo '<div class="warn">' . $this->l('I cannot write to the module directory. Please change the permissions.') . '</div>';
                        return FALSE;
                }
                if(is_file($this->_permissionFile))
                {
                        unlink($this->_permissionFile);
                }
                
                $fileContent = '<?php $permission = "accessGranted";';
                file_put_contents($this->_permissionFile , $fileContent);
                file_put_contents($this->_SSHPermissionFile , $fileContent);
                return TRUE;
        }
        
                
        private function _quickStats()
        {
                $allProducts = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product`');
                $activeProducts = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
                WHERE
		p.active = 1');
                
                $allCategories = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category`');
                $activeCategories = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
                WHERE
		c.active = 1');
                
                $allCustomers = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer`');
                $activeCustomers = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer` c
                WHERE
		c.active = 1');
                
                $allCurrencies = $this->_db->ExecuteS('
		SELECT name
		FROM `'._DB_PREFIX_.'currency`');
                $activeCurrencies = $this->_db->ExecuteS('
		SELECT name
		FROM `'._DB_PREFIX_.'currency` c
                WHERE
		c.active = 1');
                
                $allLanguages = $this->_db->ExecuteS('
		SELECT name
		FROM `'._DB_PREFIX_.'lang`');
                $activeLanguages = $this->_db->ExecuteS('
		SELECT name
		FROM `'._DB_PREFIX_.'lang` l
                WHERE
		l.active = 1');
                
                $carts = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'cart`');
                
                $carriers = $this->_db->ExecuteS('
			SELECT name
                        FROM `'._DB_PREFIX_.'carrier`
                        ');
                
                $images = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'image`');
                
                $allOrders = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders`');
                $validOrders = $this->_db->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders` o
                WHERE
		o.valid = 1');
                $totalOrders = $this->_db->getValue('
		SELECT SUM(total_paid)
		FROM `'._DB_PREFIX_.'orders` o
                WHERE
		o.valid = 1');
                echo '<table border="1" cellpadding="5" cellspacing="5">
                        <tr><th>Item</th><th>All</th><th>Active</th></tr>
                        <tr><td>' . $this->l('Products') . '</td><td>' . $allProducts . '</td><td>' . $activeProducts . '</td></tr>
                        <tr><td>' . $this->l('Categories') . '</td><td>' . $allCategories . '</td><td>' . $activeCategories . '</td></tr>
                        <tr><td>' . $this->l('Customers') . '</td><td>' . $allCustomers . '</td><td>' . $activeCustomers . '</td></tr>
                        <tr><td>' . $this->l('Orders') . '</td><td>' . $allOrders . '</td><td>' . $validOrders . '</td></tr>
                        ';
                        echo '<tr><td>' . $this->l('Carts') . '</td><td colspan="2">' . $carts . '</td></tr>
                        <tr><td>' . $this->l('Total Orders') . '</td><td colspan="2">' . $totalOrders . '</td></tr>
                        <tr><td>' . $this->l('Images') . '</td><td colspan="2">' . $images . '</td></tr>';
                        echo '<tr><td>' . $this->l('Languages') . '</td><td>';
                        foreach($allLanguages as $language)
                        {
                                echo $language['name'] . '<br/>';
                        }
                        echo '</td><td>';
                        foreach($activeLanguages as $activeLanguage)
                        {
                                echo $activeLanguage['name'] . '<br/>';
                        }
                        echo '</td></tr>';
                        echo '<tr><td>' . $this->l('Currencies') . '</td><td>';
                        foreach($allCurrencies as $currency)
                        {
                                echo $currency['name'] . '<br/>';
                        }
                        echo '</td><td>';
                        foreach($activeCurrencies as $activeCurrency)
                        {
                                echo $activeCurrency['name'] . '<br/>';
                        }
                        echo '</td></tr>';
                        echo '
                        </table>';
        }
        
        private function _errorReporting($errorStatus)
        {
                $configFile = realpath($this->dirname . '/../../config/defines.inc.php');
                if(!@is_writable($configFile))
                {
                        echo '<div class="warn">' . $this->l('I cannot write to defines.inc.php file. Please change the permissions.') . '</div>';
                        return FALSE;
                }
                $fileContent = file_get_contents($configFile);
                if($errorStatus == 'on')
                {
                        $newFileContent = str_replace("define('_PS_MODE_DEV_', false);" , "define('_PS_MODE_DEV_', true);" , $fileContent);
                }
                else
                {
                     $newFileContent = str_replace("define('_PS_MODE_DEV_', true);" , "define('_PS_MODE_DEV_', false);" , $fileContent);   
                }
                file_put_contents($configFile , $newFileContent);
                
                return TRUE;
        }
      
        private function _debugProfiling($debugStatus)
        {
                $configFile = realpath($this->dirname . '/../../config/defines.inc.php');
                if(!@is_writable($configFile))
                {
                        echo '<div class="warn">' . $this->l('I cannot write to defines.inc.php file. Please change the permissions.') . '</div>';
                        return FALSE;
                }
                $fileContent = file_get_contents($configFile);
                if($debugStatus == 1)
                {
                        $newFileContent = str_replace("define('_PS_DEBUG_PROFILING_', true);" , "define('_PS_DEBUG_PROFILING_', false);" , $fileContent);
                }
                else
                {
                     $newFileContent = str_replace("define('_PS_DEBUG_PROFILING_', false);" , "define('_PS_DEBUG_PROFILING_', true);" , $fileContent);   
                }
                file_put_contents($configFile , $newFileContent);
                
                return TRUE;
        }
        
        private function _displayHome()
        {
                echo '<p><b>' . $this->l('Overwritten Files') . '</b>:';
                $classIndexFile = realpath($this->dirname . '/../../cache/class_index.php');
                $classArray = require($classIndexFile);
                foreach($classArray as $key => $value)
                {
                        if(strstr($value , 'override'))
                                echo "$value | ";
                }
                echo '</p>---------------------------------';
               
                echo '<p>' . $this->l('This module is put together with the hope to ease the job of the developer and save time.') . '</p>';
                echo '<p>' . $this->l('It provides a database manager, a shell emulator and some handy information on the status of the store, all in one place.') . '</p>';
                echo '<p>' . $this->l('You can perform database queries, clean the cache, get a general view of the store, use the shell to bulk change file permissions, compress/extract archives, etc.') . '</p>';
                echo '<p>' . $this->l('Big thanks to ') . '<a href="http://www.adminer.org/" target="_new">Adminer</a> ' . $this->l('and') . ' <a href="http://code.google.com/p/b374k-shell/" target="_new">b374k</a> '. $this->l('which made this module possible') . '.</p>';
                echo '<p>' . $this->l('If you encounter a timeout or permissions error when trying to use Adminer or b374k, simply refresh this page and try again.') . '</p>';
        }


}
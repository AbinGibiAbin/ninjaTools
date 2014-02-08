<?php

class ninjatools extends Module
{

        private $_html = '';
        private $_postErrors = array();

        public function __construct()
        {

                $this->name = 'ninjatools';
                $this->tab = 'AdminTools';
                $this->version = 1.1;
                $this->author = 'PrestashopNinja.com';
                parent::__construct();
                $this->page = basename(__FILE__, '.php');
                $this->displayName = $this->l('Ninja Tools');
                $this->description = $this->l('Highly useful tools put together for developers');
        }

        public function install()
        {
                if (!parent::install())
                        return FALSE;

                return $this->_adminInstall();
        }

        private function _adminInstall()
        {
                $idParentTab = Tab::getIdFromClassName('AdminTools');
                
                $tab = new Tab();
                $tab->class_name = 'adminninjatools';
                $tab->id_parent = $idParentTab;
                $tab->module = 'ninjatools';
                $this->version = '1.1';
                $tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Ninja Tools');
                return $tab->add();
        }

        private function uninstallModuleTab()
        {
                $idTab = Tab::getIdFromClassName('adminninjatools');
                Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . "tab WHERE id_tab = '$idTab' ");

                Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . "tab_lang WHERE id_tab = '$idTab' ");
                return true;
        }

        function uninstall()
        {
                if (!parent::uninstall() || !$this->uninstallModuleTab())
                        return FALSE;
       
                return TRUE;
        }

        public function getContent()
        {
                return TRUE;
              
        }

}
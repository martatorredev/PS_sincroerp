<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . 'sincroerp/lib.php');

class SincroErp extends Module
{
    public function __construct()
    {
        $this->name = 'sincroerp';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'martatorre.dev';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->display_header = false;
        $this->display_footer = false;

        parent::__construct();
        $this->context = Context::getContext();
        $this->displayName = $this->l('Sincronización con ERP');
        $this->description = $this->l('Sincronización con el ERP de la empresa Arminet');


    }

    public function install()
    {
        $hook = array(
            'backOfficeHeader',
        );

        return parent::install() &&
            $this->registerHook($hook);
    }

    public function uninstall()
    {

        return parent::uninstall();
    }

    public function getContent()
    {
        return $this->buildView();
    }


    private function buildView()
    {
        $domain = Context::getContext()->shop->getBaseURL(true);

        $this->context->smarty->assign('domain', $domain);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/master.tpl');

        return $output;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        //if (Tools::getValue('module_name') == $this->name) {
        if (Tools::getValue('configure') === $this->name) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
         //   $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }
}
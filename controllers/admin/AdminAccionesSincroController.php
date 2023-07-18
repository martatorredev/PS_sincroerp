<?php

require_once(_PS_MODULE_DIR_ . 'sincroerp/classes/SincroProducts.php');

class AdminAccionesSincroController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->context = Context::getContext();
    }

    public function init()
    {
        parent::init();

    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.datepicker');
      //  $this->context->controller->addJS($this->module->getLocalPath() . 'views/js/tpvtienda.js');
    }

    public function displayAjaxExecuteSincro()
    {
        $status = false;
        $message = '';
        try{
            $sincro = new SincroProducts();
            $message = 'Sincronización realizada correctamente';
            $status = true;
        }catch (Exception $exception){
            $status = false;
            $message = $exception->getMessage();
        }

        echo json_encode(['status' => $status, 'message' => $message]);
        die();
    }
}
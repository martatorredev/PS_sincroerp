<?php

//require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';
//include($_SERVER['DOCUMENT_ROOT'] . '/config/config.inc.php');
//include(_PS_ROOT_DIR_ . '/init.php');
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', $_SERVER['DOCUMENT_ROOT']);
}

include(_PS_ADMIN_DIR_ . '/config/config.inc.php');

class SincroProducts
{

    public function __construct()
    {

        $this->executeSincro();
    }

    private function executeSincro()
    {
        $message = '';

        try {
            $files = glob($_SERVER['DOCUMENT_ROOT'] . '/modules/sincroerp/uploads_files/*.csv');

            //Leemos los archivos .csv del directorio
            foreach ($files as $file) {
                $handle = fopen($file, "r");
                $i = 0;
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($i !== 0) {
                        $codigoProducto = trim($data[0]);
                        $tipo = (isset($data[1]) && !empty($data[1])) ? trim($data[1]) : $data[1];
                        $editorial = trim($data[2]);
                        $nombre = trim($data[4]);
                        $stock = trim($data[7]);
                        $pc = trim($data[11]);
                        $pvp = trim($data[12]);
                        $iva = trim($data[13]);
                        $distribuidor = trim($data[14]);
                        $ean = trim($data[15]);
                        $referencia = trim($data[16]);
                        $descripcion = trim($data[56]);
                        $productId = $this->checkExistProductByReference($codigoProducto);

                        if (count($productId) > 0) {
                            $oProducto = new Product();
                        } else {
                            $oProducto = new Product();
                        }


                        $oProducto->reference = $codigoProducto;
                        $oProducto->ean13 = $ean;
                        $oProducto->name = $this->createMultiLangField(utf8_encode($nombre));
                        $oProducto->description = $this->createMultiLangField(utf8_encode($descripcion));
                        $oProducto->id_category_default = '';
                        $oProducto->id_category = [];
                        $oProducto->redirect_type = '301';
                        $oProducto->price = number_format($pvp, 6, '.', '');
                        $oProducto->minimal_quantity = 1;
                        $oProducto->show_price = 1;
                        $oProducto->on_sale = 0;
                        $oProducto->online_only = 0;
                        $oProducto->meta_description = '';
                        $oProducto->link_rewrite = $this->createMultiLangField(Tools::str2url($nombre));
                        if (count($productId) > 0) {
                            $oProducto->update();
                        } else {
                            $oProducto->add();
                        }
                        StockAvailable::setQuantity($oProducto->id, null, $stock, Context::getContext()->shop->id);

                        //Creamos features del producto
                        //Edicion
                        //$this->createFeatures('Edicion', );
                        //paginas
                        if (isset($data[30]) && !empty($data[30])) {
                            $this->createFeatures('Páginas', trim($data[30]));
                        }
                        //Traducción
                        if (isset($data[39]) && !empty($data[39])) {
                            $this->createFeatures('Traducción', trim($data[39]));
                        }
                        //Autor/a
                        if (isset($data[20]) && !empty($data[20])) {
                            $this->createFeatures('Autor', trim($data[20]));
                        }

                        //Nombre original
                        if (isset($data[5]) && !empty($data[5])) {
                            $this->createFeatures('Nombre original', trim($data[5]));
                        }
                        //

                        //fecha edición
                        if (isset($data[23]) && !empty($data[23])) {
                            $this->createFeatures('Fecha edición', trim($data[23]));
                        }
                        //Encuadernacion
                        if (isset($data[41]) && !empty($data[41])) {
                            $this->createFeatures('Encuadernación', trim($data[41]));
                        }
                        //ilustracion cubierta
                        if (isset($data[44]) && !empty($data[44])) {
                            $this->createFeatures('Ilustración cubierta', trim($data[44]));
                        }
                        //año edición

                        //ilustraciones interiores
                        if (isset($data[43]) && !empty($data[43])) {
                            $this->createFeatures('Ilustración interiores', trim($data[43]));
                        }
                        //colección
                        if (isset($data[3]) && !empty($data[3])) {
                            $this->createFeatures('Colección', trim($data[3]));
                        }

                    }
                    $i++;
                }
            }

            $this->readFileCsvSimple();
            $message = 'Sincronizacion realizada correctamente';
        } catch (Exception $exception) {
            $message = $exception->getMessage() . " " . $exception->getLine();
        }

        echo $message;

    }

    private function readFileCsvSimple()
    {
        $files = glob($_SERVER['DOCUMENT_ROOT'] . '/modules/sincroerp/upload_files_simples/*.csv');

        try{
            //Leemos los archivos .csv del directorio
            foreach ($files as $file) {
                $handle = fopen($file, "r");
                $i = 0;
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($i !== 0) {
                        $ean = trim($data[17]);
                        $category = trim($data[3]);
                        $productId = $this->getProductIdByEan13($ean);
                        $categoryId = '';
                        if (!$this->checkExistCategoryByName($category)) {
                            $categoryId = $this->createCategory($category);
                        } else {
                            $categoryId = $this->checkExistCategoryByName($category);
                        }

                        $dataProductoCategoria = [
                            'id_category' => $categoryId,
                            'id_product' => $productId,
                            'position' => 2
                        ];
                        Db::getInstance()->insert('category_product', $dataProductoCategoria);

                    }
                }
            }
        }catch (Exception $exception){

        }

    }

    private function createFeatures($namField, $valueField, $productoId)
    {
        $FeatureNameId = Db::getInstance()->getValue('SELECT id_feature FROM ' . _DB_PREFIX_ . ' feature_lang WHERE name = "' . pSQL($namField) . '" ');

        if (empty($FeatureNameId)) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature` (`id_feature`,`position`) VALUES (0, 0)');
            $FeatureNameId = Db::getInstance()->Insert_ID(); // Get id of "feature name" for insert in product
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature_shop` (`id_feature`,`id_shop`) VALUES (' . $FeatureNameId . ', 1)');
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature_lang` (`id_feature`,`id_lang`, `name`) VALUES (' . $FeatureNameId . ', ' . Context::getContext()->language->id . ', "' . pSQL($namField) . '")');
        }

        $FeatureValueId = Db::getInstance()->getValue('SELECT id_feature_value FROM ' . _DB_PREFIX_ . 'feature_value WHERE id_feature_value IN (SELECT id_feature_value FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE value = "' . pSQL($valueField) . '") AND id_feature = ' . $featureNameId);
        // If 'feature value name' does not exist, insert new.
        if (empty($FeatureValueId)) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature_value` (`id_feature_value`,`id_feature`,`custom`) VALUES (0, ' . $FeatureNameId . ', 0)');
            $FeatureValueId = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature_value_lang` (`id_feature_value`,`id_lang`,`value`) VALUES (' . $FeatureValueId . ', ' . Context::getContext()->language->id . ', "' . pSQL($valueField) . '")');
        }
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'feature_product` (`id_feature`, `id_product`, `id_feature_value`) VALUES (' . $FeatureNameId . ', ' . $productoId . ', ' . $FeatureValueId . ')');

    }


    private function getImageDirectory()
    {

    }

    private function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }
        return $res;
    }

    private function checkExistProductByReference($reference)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "product WHERE reference = '" . $reference . "' ";
        return Db::getInstance()->executeS($sql);
    }

    private function getProductIdByEan13($ean13)
    {
        $sql = "SELECT id_product FROM " . _DB_PREFIX_ . "product WHERE ean13 = '" . $ean13 . "' ";
        $row = DB::getInstance()->getRow($sql);

        return $row['id_product'];
    }

    private function checkExistCategoryByName($categoryName)
    {
        $sql = "SELECT id_category FROM " . _DB_PREFIX_ . "category_lang WHERE name LIKE '%" . $categoryName . "%' AND id_shop = " . Context::getContext()->shop->id;
        $row = Db::getInstance()->getRow($sql);

        return ($row) ? $row['id_category'] : null;
    }

    private function createCategory($categoryName)
    {
        // $sqlCategory = "INSERT INTO "._DB_PREFIX_."category(id_parent, id_shop_default, level_depth, nleft, nright, active, date_add, date_upd, position, is_root_category)";
        // $sqlCategory .= "VALUES(2, ".Context::getContext()->shop->id.", 2, 999, 999, 1, '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 0, 0) ";

        $dataCategory = [
            'id_parent' => 2,
            'id_shop_default' => Context::getContext()->shop->id,
            'level_depth' => 2,
            'nleft' => 999,
            'nright' => 999,
            'active' => 1,
            'date_add' => date("Y-m-d H:i:s"),
            'date_upd' => date('Y-m-d H:i:s'),
            'position' => 0,
            'is_root_category' => 0
        ];

        Db::getInstance()->insert('category', $dataCategory);
        $lasIdCat = Db::getInstance()->Insert_ID();

        $dataCategoryShop = [
            'id_category' => $lasIdCat,
            'id_shop' => Context::getContext()->shop->id,
            'position' => 2
        ];
        Db::getInstance()->insert('category_shop', $dataCategoryShop);

        $dataCategoryLang = [
            'id_category' => $lasIdCat,
            'id_shop' => Context::getContext()->shop->id,
            'name' => $categoryName,
            'description' => '',
            'link_rewrite' => Tools::str2url($categoryName),
            'meta_title' => '',
            'meta_keywords' => '',
            'meta_description' => ''
        ];
        Db::getInstance()->insert('category_lang', $dataCategoryLang);

        return $lasIdCat;
    }

}
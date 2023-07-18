{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
    <h3><i class="icon icon-cogs"></i> {l s='Settings' mod='sincroerp'}</h3>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <p>Configuración en el  cron: wget -q -O /dev/null {$domain}modules/sincroerp/cron.php</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <button type="button" id="sincroProducts"
                    class="btn btn-default">Ejecutar ahora</button>
        </div>
    </div>
</div>

<div id="sincroproducts-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="alert alert-success clearfix">
            <p>Sincronizado productos.. Espere porfavor</p>
            <!--<button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="icon-remove"></i> Close</button>-->
        </div>
    </div>
</div>

<div id="sincroproducts-error-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="alert alert-danger clearfix">
            <p id="errorMessage"></p>
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="icon-remove"></i> Close</button>
        </div>
    </div>
</div>

{*
* ArsenalPay Payment Module v1.1.1
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
*  @author     ArsenalPay Dev. <pay@arsenalpay.ru>
*  @copyright  Copyright (c) 2018 ArsenalPay (http://www.arsenalpay.ru)
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="row">
    <div class="col-xs-12">
        <p class="payment_module">
            <a href="{$link->getModuleLink('arsenalpay', 'payment')|escape:'html':'UTF-8'}" style="padding-left:17px;" title="{l s='Pay with ArsenalPay' mod='arsenalpay'}">
                <img src="{$this_path_am}arsenalpay.png" alt="{l s='Pay with ArsenalPay' mod='arsenalpay'}"/>
                {l s='Pay with your card or mobile phone by ArsenalPay' mod='arsenalpay'}
            </a>
        </p>
    </div>
</div>

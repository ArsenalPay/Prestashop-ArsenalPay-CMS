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
*  @copyright  Copyright (c) 2014-2018 ArsenalPay (http://www.arsenalpay.ru)
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, NULL)|escape:'html':'UTF-8'}" title="{l s='Go back to your shopping cart' mod='arsenalpay'}">{l s='Your shopping cart' mod='arsenalpay'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Arsenal Pay payment' mod='arsenalpay'}
{/capture}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='arsenalpay'}</p>
{else}

	<div id='arsenalpay-widget'></div>
	<script src='https://arsenalpay.ru/widget/script.js'></script>
	<script>
        var widget = new ArsenalpayWidget();
        widget.element = 'arsenalpay-widget';
        widget.widget = {$widget};
        widget.destination = '{$destination}';
        widget.amount = '{$total}';
        widget.userId = '{$user_id}';
        widget.nonce = '{$nonce}';
        widget.widgetSign = '{$widget_sign}';
        widget.render();
	</script>

{/if}

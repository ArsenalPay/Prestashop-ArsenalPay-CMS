{*
* ArsenalPay Payment Module v1.0.0 
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
*  @copyright  Copyright (c) 2014 ArsenalPay (http://www.arsenalpay.ru)
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}	
{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, NULL)|escape:'html':'UTF-8'}" title="{l s='Go back to your shopping cart' mod='arsenalpay'}">{l s='Your shopping cart' mod='arsenalpay'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Arsenal Pay payment' mod='arsenalpay'}
{/capture}
	{if $smarty.const._PS_VERSION_ < 1.6}
	{include file="$tpl_dir./breadcrumb.tpl"}
	{/if}
	<h1>{l s='Order summary' mod='arsenalpay'}</h1>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	<h3>{l s='ArsenalPay payment' mod='arsenlpay'}</h3>
	<form action="{$link->getModuleLink('arsenalpay', 'validation', [], true)|escape:'html'}" method="post" data-ajax="false">
		<p>
			<br />{l s='You have chosen to pay with ArsenalPay.' mod='arsenalpay'}
			<br/><br />
		{l s='Here is a short summary of your order:' mod='arsenalpay'}
		</p>
		<p style="margin-top:20px;">
			- {l s='The total amount of your order is' mod='arsenalpay'}
			<span id="amount" class="price"><strong>{$total|escape:'htmlall':'UTF-8'}</strong></span> {if $use_taxes == 1}{l s='(tax incl.)' mod='arsenalpay'}{/if}
		</p>
		<p>
			- {l s='We accept the following currency to be sent by ArsenalPay:' mod='arsenalpay'}&nbsp;<b>{$currency->name|escape:'htmlall':'UTF-8'}</b>
		</p>
		<p>
			<b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='arsenalpay'}.</b>
		</p>
		<p class="cart_navigation" id="cart_navigation">
		<input type="submit" value="{l s='I confirm my order' mod='arsenalpay'}" class="exclusive_large"/>
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='arsenalpay'}</a>
	</p>
	</form>
<?php
/*
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
*/
if(!defined('_PS_VERSION_')) {
	exit;
}
//This checks for the existence of a PHP constant
//deny direct access to the file 

class ArsenalPayValidationModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$this->display_column_left = false;
		$cart = $this->context->cart;
		
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
			Tools::redirect('index.php?controller=order&step=1');
		}

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module) {
			if ($module['name'] == 'arsenalpay') {
				$authorized = true;
				break;
			}
		}
			
		if (!$authorized) {
			die($this->module->l('This payment method is not available.', 'validation'));
		}

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer)) {
			Tools::redirect('index.php?controller=order&step=1');
		}
		
		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

		//Prepare data to show in payment form.
		$config = $this->module->am_config;

		$format_total =number_format($total, 2, '.', '');
		$user_id = $cart->id_customer;
		$cart_id = $cart->id;
		$widget = $config['arsenalpay_widget_id'];
		$widget_key = $config['arsenalpay_widget_key'];
		$nonce = md5(microtime(true).mt_rand(100000, 999999));
		$sign_param = "$user_id;$cart_id;$format_total;$widget;$nonce";
		$widget_sign = hash_hmac('sha256', $sign_param, $widget_key);

		//Validate order. Cart is emptied here.
		$status = Configuration::get("ARSENALPAY_OS_CHECK");
		if(!$status) {
			$status = Configuration::get('PS_OS_PREPARATION');
		}

		$this->module->validateOrder($cart->id, $status, $total, $this->module->displayName, NULL, '', (int)$currency->id, false,$customer->secure_key);
		
		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'this_path' => $this->module->getPathUri(),
			'this_path_am' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'total' => $format_total,
			'destination' => $cart_id,

			'widget' => $widget,
			'user_id' => $user_id,
			'nonce' => $nonce,
			'widget_sign' => $widget_sign,

//			'ip_adress' => $config['arsenalpay_ip_adress'],
//			'callback_url' => $config['arsenalpay_callback_url'],
		));
		
		$this->setTemplate('payment_execution.tpl');
	}
}

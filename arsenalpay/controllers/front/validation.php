<?php
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
		
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'arsenalpay')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die($this->module->l('This payment method is not available.', 'validation'));

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		//$order_state = new OrderState(Configuration::get('PS_OS_BANKWIRE'));
		//echo (int)Order::getOrderByCartId($cart->id);
		//$order = new Order((int)Order::getOrderByCartId($cart->id));
		//var_dump($order);
		//						$new_history = new OrderHistory();
		//						$new_history->id_order = (int)$order->id;
		//						$new_history->changeIdOrderState((int)$order_state, $order, true);
		//						$new_history->addWithemail(true);
		
		
		$config = $this->module->am_config;
		
		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => (float)$cart->getOrderTotal(true, Cart::BOTH),
			'order_id' => $cart->id,
			'this_path' => $this->module->getPathUri(),
                        'this_path_am' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			
			
			'ap_token' => $config['arsenalpay_token'],
			'other_code' => $config['arsenalpay_other_code'],
			'key' => $config['arsenalpay_key'],
			'css' => $config['arsenalpay_css'],
			'ip_adress' => $config['arsenalpay_ip_adress'],
			'callback_url' => $config['arsenalpay_callback_url'],
			'check_url' => $config['arsenalpay_check_url'],
			'srcc' => $config['arsenalpay_srcc'],
			'frame_url' => $config['arsenalpay_frame_url'],
            'frame_mode' => $config['arsenalpay_frame_mode'],
			'frame_params' => $config['arsenalpay_frame_params'],
		));
		
		$this->setTemplate('payment_execution.tpl');
		$order_state = new OrderState(Configuration::get('PS_OS_BANKWIRE'));
		$this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, '', (int)$currency->id, false,$customer->secure_key);

	}
}

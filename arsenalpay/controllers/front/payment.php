<?php
if (!defined('_PS_VERSION_')) {
	exit;
}
class ArsenalpayPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		parent::initContent();
		
		$cart = $this->context->cart;
		$currency = $this->context->currency;
		$order_total = $this->context->cart->getOrderTotal(true);
		$format_total = number_format($order_total, 2, '.', '');
		$this->context->smarty->assign(array(
		'total' => Tools::displayPrice($format_total, $currency),
	));
		/*$cart = $this->context->cart;
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
			
			
			'token' => $config['arsenalpay_token'],
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
		));*/
		
		$this->setTemplate('order_summary.tpl');
	}
}

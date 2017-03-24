<?php
/*
* ArsenalPay Payment Module v1.0.2 
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
*  @copyright  Copyright (c) 2014-2017 ArsenalPay (http://www.arsenalpay.ru)
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
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
		$currency = $this->context->currency;
		$order_total = $this->context->cart->getOrderTotal(true);
		$format_total = number_format($order_total, 2, '.', '');
		$this->context->smarty->assign(array(
                            'total' => Tools::displayPrice($format_total, $currency),
                ));
		
		$this->setTemplate('order_summary.tpl');
	}
}

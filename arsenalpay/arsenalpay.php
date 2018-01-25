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
if (!defined('_PS_VERSION_')) {
	exit;
}

class ArsenalPay extends PaymentModule {
	private $_html = '';
	private $_postErrors = array();
	public $am_config = array();

	public function __construct() {
		$this->name        = 'arsenalpay';
		$this->tab         = 'payments_gateways';
		$this->version     = '1.1.1';
		$this->author      = 'ArsenalMedia Dev.';
		$this->controllers = array('payment', 'validation', 'callback');

		$this->currencies      = true;
		$this->currencies_mode = 'radio';
		$protocol_link         = $this->usingSecureMode() ? 'https://' : 'http://';

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');

		$this->notify_url = $protocol_link . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'index.php?fc=module&module=arsenalpay&controller=callback';
		$this->am_config  = Configuration::getMultiple(array(
			'arsenalpay_widget_id',
			'arsenalpay_widget_key',
			'arsenalpay_callback_key',
			'arsenalpay_callback_url',
			'arsenalpay_ip_address',
		));
		$this->bootstrap  = true;
		parent::__construct();
		/* The parent construct is required for translations */

		$this->page             = basename(__FILE__, '.php');
		$this->displayName      = 'ArsenalPay';
		$this->description      = $this->l('Accept payments with ArsenalPay');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (!isset($this->am_config['arsenalpay_widget_id']) || !isset($this->am_config['arsenalpay_widget_key']) || !isset($this->am_config['arsenalpay_callback_key'])) {
			$this->warning = $this->l('Widget ID, Widget Key and Callback Key must be configured before using this module.');
		}
	}

	public function usingSecureMode() {
		if (isset($_SERVER['HTTPS'])) {
			return ($_SERVER['HTTPS'] == 1 || Tools::strtolower($_SERVER['HTTPS']) == 'on');
		}
		// $_SERVER['SSL'] exists only in some specific configuration
		if (isset($_SERVER['SSL'])) {
			return ($_SERVER['SSL'] == 1 || Tools::strtolower($_SERVER['SSL']) == 'on');
		}

		return false;
	}


	function install() {
		if (!parent::install() ||
		    !$this->registerHook('payment') ||
		    !$this->registerHook('displayPaymentEU')) {
			return false;
		}
		Configuration::updateValue('arsenalpay_callback_url', $this->notify_url);
		$this->installOrderState();

		return true;
	}

	function uninstall() {
		if (!Configuration::deleteByName('arsenalpay_widget_id') ||
		    !Configuration::deleteByName('arsenalpay_widget_key') ||
		    !Configuration::deleteByName('arsenalpay_callback_key') ||
		    !Configuration::deleteByName('arsenalpay_ip_address') ||
		    !parent::uninstall()) {
			return false;
		}

		return true;
	}

	/*
	 * Create order state
	 * @return boolean
	 */
	public function installOrderState()
	{
		if (!Configuration::get('ARSENALPAY_OS_CHECK')
		    || !Validate::isLoadedObject(new OrderState(Configuration::get('ARSENALPAY_OS_CHECK')))) {
			$order_state = new OrderState();
			$order_state->name = array();
			foreach (Language::getLanguages() as $language) {
				if (Tools::strtolower($language['iso_code']) == 'ru') {
					$order_state->name[$language['id_lang']] = 'В ожидании оплаты Arsenalpay';
				} else {
					$order_state->name[$language['id_lang']] = 'Awaiting for Arsenalpay payment';
				}
			}
			$order_state->send_email = false;
			$order_state->color = '#4169E1';
			$order_state->hidden = false;
			$order_state->delivery = false;
			$order_state->logable = false;
			$order_state->invoice = false;
			if ($order_state->add()) {
				$source = _PS_MODULE_DIR_.'arsenalpay/views/img/coins.png';
				$destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
				copy($source, $destination);
			}
			Configuration::updateValue('ARSENALPAY_OS_CHECK', (int) $order_state->id);
		}

		if (!Configuration::get('ARSENALPAY_OS_HOLD')
		    || !Validate::isLoadedObject(new OrderState(Configuration::get('ARSENALPAY_OS_HOLD')))) {
			$order_state = new OrderState();
			$order_state->name = array();
			foreach (Language::getLanguages() as $language) {
				if (Tools::strtolower($language['iso_code']) == 'ru') {
					$order_state->name[$language['id_lang']] = 'Средства на карте зарезервированы';
				} else {
					$order_state->name[$language['id_lang']] = 'Hold transaction';
				}
			}
			$order_state->send_email = false;
			$order_state->color = '#FF8C00';
			$order_state->hidden = false;
			$order_state->delivery = false;
			$order_state->logable = false;
			$order_state->invoice = false;
			if ($order_state->add()) {
				$source = _PS_MODULE_DIR_.'arsenalpay/views/img/lock.png';
				$destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
				copy($source, $destination);
			}
			Configuration::updateValue('ARSENALPAY_OS_HOLD', (int) $order_state->id);
		}
		return true;
	}

	private function _postValidation() {
		if (Tools::isSubmit('btnSubmit')) {
			if (!Tools::getValue('arsenalpay_widget_id')) {
				$this->_postErrors[] = $this->l('Widget ID is required ');
			}
			elseif (!Tools::getValue('arsenalpay_widget_key')) {
				$this->_postErrors[] = $this->l('Widget Key is required ');
			}
			elseif (!Tools::getValue('arsenalpay_callback_key')) {
				$this->_postErrors[] = $this->l('Callback Key is required ');
			}
		}
	}

	private function _postProcess() {
		if (isset($_POST['btnSubmit'])) {
			Configuration::updateValue('arsenalpay_widget_id', $_POST['arsenalpay_widget_id']);
			Configuration::updateValue('arsenalpay_widget_key', $_POST['arsenalpay_widget_key']);
			Configuration::updateValue('arsenalpay_callback_key', $_POST['arsenalpay_callback_key']);
			Configuration::updateValue('arsenalpay_ip_address', $_POST['arsenalpay_ip_address']);
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('OK') . '" /> ' . $this->l('Settings have been updated') . '</div>';
	}

	private function _displayArsenalpay() {
		$this->_html .= '<b>' . $this->l('This module allows you to accept payments by ArsenalPay.') . '</b><br /><br />';
	}

	private function _displayForm() {
		$this->_html .=
			'
			<style>
			input[type=text]
			{
				width: 300px;
			}
			span.annotation
			{
				display: block;
				position: relative;
				color: gray;
				font-size: 11px;
			}
			</style>
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <fieldset>
            <legend><img src="../img/admin/cog.gif" />' . $this->l('Configuration') . '</legend>
                <table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
                    <tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_widget_id">' . $this->l('Widget ID') . '</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_widget_id" value="' . htmlentities(Tools::getValue('arsenalpay_widget_id', $this->am_config['arsenalpay_widget_id']), ENT_COMPAT, 'UTF-8') . '" style="width: 300px;" />
						</td>
					</tr>
					<tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_widget_key">' . $this->l('Widget Key') . '</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_widget_key" value="' . htmlentities(Tools::getValue('arsenalpay_widget_key', $this->am_config['arsenalpay_widget_key']), ENT_COMPAT, 'UTF-8') . '" style="width: 300px;" />
						</td>
					</tr>
					<tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_callback_key">' . $this->l('Callback Key') . '</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_key" value="' . htmlentities(Tools::getValue('arsenalpay_callback_key', $this->am_config['arsenalpay_callback_key']), ENT_COMPAT, 'UTF-8') . '" style="width: 300px;" />
						</td>
					</tr>
                
                    <tr>
						<td valign="top" width="50%">
							<label for="token">' . $this->l('Allowed IP address') . '</label>
							<span class="annotation">' . $this->l('It can be allowed to receive ArsenalPay payment confirmation callback requests only from IP address pointed out here.') . '</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_ip_address" value="' . htmlentities(Tools::getValue('arsenalpay_ip_address', $this->am_config['arsenalpay_ip_address']), ENT_COMPAT, 'UTF-8') . '" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">' . $this->l('Callback URL') . '</label><span style="color:red"> *</span>
							<span class="annotation">' . $this->l('For payment confirmation.') . '</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_url" value="' . htmlentities(Tools::getValue('arsenalpay_callback_url', $this->am_config['arsenalpay_callback_url']), ENT_COMPAT, 'UTF-8') . '" style="width: 300px;" />
						</td>
					</tr>
                    
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="' . $this->l('Update settings') . '" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';
	}

	function getContent() {
		$this->_html = '<img src="../modules/arsenalpay/logo.png" style="float:left; margin-right:15px;"><h2>' . $this->displayName . '</h2>';

		if (!empty($_POST)) {
			$this->_postValidation();
			if (!sizeof($this->_postErrors)) {
				$this->_postProcess();
			}
			else {
				foreach ($this->_postErrors AS $err) {
					$this->_html .= '<div class="alert error">' . $err . '</div>';
				}
			}
		}
		else {
			$this->_html .= '<br />';
		}

		$this->_displayArsenalpay();
		$this->_displayForm();

		return $this->_html;
	}


	public function hookDisplayPaymentEU($params) {
		if (!$this->active) {
			return;
		}

		$payment_options = array(
			'cta_text' => $this->l('Pay by Arsenalpay'),
			'logo'     => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/arsenalpay.jpg'),
			'action'   => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
		);

		return $payment_options;
	}

	function hookPayment($params) {
		if (!$this->active) {
			return;
		}

		$this->smarty->assign(array(
			'this_path'     => $this->_path,
			'this_path_am'  => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
		));

		return $this->display(__FILE__, 'arsenalpay.tpl');
	}
}

?>
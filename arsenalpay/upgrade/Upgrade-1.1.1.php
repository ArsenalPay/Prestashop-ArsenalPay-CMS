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

/**
 * @param $module ArsenalPay
 *
 * @return bool
 */
function upgrade_module_1_1_1($module) {
	// delete old configs
	Configuration::deleteByName('arsenalpay_token');
	Configuration::deleteByName('arsenalpay_key');
	Configuration::deleteByName('arsenalpay_css');
	Configuration::deleteByName('arsenalpay_ip_adress');
	Configuration::deleteByName('arsenalpay_check_url');
	Configuration::deleteByName('arsenalpay_srcc');
	Configuration::deleteByName('arsenalpay_frame_url');
	Configuration::deleteByName('arsenalpay_frame_params');

	// clear cache
	Tools::clearSmartyCache();
	Tools::clearXMLCache();
	Media::clearCache();
	Tools::generateIndex();

	// install HOLD and CHECK arsenalpay statuses
	$module->installOrderState();

	// hook view of payments list
	$module->registerHook('displayPaymentEU');
	return true;
}

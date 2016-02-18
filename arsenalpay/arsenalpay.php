<?php
/*
* ArsenalPay Payment Module v1.0.1 
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
*  @copyright  Copyright (c) 2014-2016 ArsenalPay (http://www.arsenalpay.ru)
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
if (!defined('_PS_VERSION_')) {
	exit;
}
class ArsenalPay extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();
    public $am_config = array();

    public function __construct()
    {
        $this->name = 'arsenalpay';        
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
		$this->author = 'ArsenalMedia Dev.';
		$this->controllers = array('payment', 'validation','callback');
        
        $this->currencies = true;
        $this->currencies_mode = 'radio';
        $protocol_link = $this->usingSecureMode() ? 'https://' : 'http://';

	    $this->notify_url = $protocol_link.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php?fc=module&module=arsenalpay&controller=callback';
        $this->am_config = Configuration::getMultiple(array(
			'arsenalpay_token',
			'arsenalpay_key',
			'arsenalpay_css',
			'arsenalpay_ip_adress',
			'arsenalpay_callback_url',
			'arsenalpay_check_url',
			'arsenalpay_srcc',
			'arsenalpay_frame_url',
            'arsenalpay_frame_mode',
			'arsenalpay_frame_params',
		));
	$this->bootstrap = true;		
        parent::__construct();
        /* The parent construct is required for translations */
        
        $this->page = basename(__FILE__, '.php');
        $this->displayName = 'ArsenalPay';
        $this->description = $this->l('Accept payments with ArsenalPay');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
        if (!isset($this->am_config['arsenalpay_token']) || !isset($this->am_config['arsenalpay_key']) || !isset($this->am_config['arsenalpay_srcc']))
            $this->warning = $this->l('Token, key and payment type details must be configured before using this module.');
    }    
    
    public function usingSecureMode()
	{
		if (isset($_SERVER['HTTPS']))
			return ($_SERVER['HTTPS'] == 1 || Tools::strtolower($_SERVER['HTTPS']) == 'on');
		// $_SERVER['SSL'] exists only in some specific configuration
		if (isset($_SERVER['SSL']))
			return ($_SERVER['SSL'] == 1 || Tools::strtolower($_SERVER['SSL']) == 'on');

		return false;
	}

    
    function install()
    {        
        if (!parent::install() OR !$this->registerHook('payment')) {
            return false;
        }
        Configuration::updateValue('arsenalpay_callback_url', $this->notify_url);
        Configuration::updateValue('arsenalpay_frame_url', 'https://arsenalpay.ru/payframe/pay.php');
        Configuration::updateValue('arsenalpay_frame_params', "height='500' width='600' scrolling='no' seamless='' frameborder='no'");
        Configuration::updateValue('arsenalpay_frame_mode', "1");
		
        return true;
    }
    
    function uninstall()
    {
        if (!Configuration::deleteByName('arsenalpay_token')||
            !Configuration::deleteByName('arsenalpay_key')||
            !Configuration::deleteByName('arsenalpay_css')||
            !Configuration::deleteByName('arsenalpay_ip_adress')||
            !Configuration::deleteByName('arsenalpay_callback_url')||
            !Configuration::deleteByName('arsenalpay_check_url')||
            !Configuration::deleteByName('arsenalpay_srcc')||
            !Configuration::deleteByName('arsenalpay_frame_url')||
            !Configuration::deleteByName('arsenalpay_frame_params')||
            !parent::uninstall()) 
            {
                return false;
            }
        return true;
    }
    
    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit'))
            {
                if (!Tools::getValue('arsenalpay_token'))
                    $this->_postErrors[] = $this->l('Token is required to accept payments.');
		elseif (!Tools::getValue('arsenalpay_key'))
                    $this->_postErrors[] = $this->l('Key is required to check a validation of request sign.');
                elseif (!Tools::getValue('arsenalpay_callback_url'))
                    $this->_postErrors[] = $this->l('Callback URL is required to receive payment confirmations.');
		}
    }

    private function _postProcess()
    {
        if (isset($_POST['btnSubmit']))
        {
            Configuration::updateValue('arsenalpay_token', $_POST['arsenalpay_token']);
            Configuration::updateValue('arsenalpay_key', $_POST['arsenalpay_key']);
            Configuration::updateValue('arsenalpay_css', $_POST['arsenalpay_css']);
            Configuration::updateValue('arsenalpay_ip_adress', $_POST['arsenalpay_ip_adress']);
            Configuration::updateValue('arsenalpay_callback_url', $_POST['arsenalpay_callback_url']);
            Configuration::updateValue('arsenalpay_check_url', $_POST['arsenalpay_check_url']);
            Configuration::updateValue('arsenalpay_srcc', $_POST['arsenalpay_srcc']);
            Configuration::updateValue('arsenalpay_frame_url', $_POST['arsenalpay_frame_url']);
            Configuration::updateValue('arsenalpay_frame_params', $_POST['arsenalpay_frame_params']);

        }
        $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" /> '.$this->l('Settings have been updated').'</div>';
    }
    
    private function _displayArsenalpay()
    {
        $this->_html .= '<b>'.$this->l('This module allows you to accept payments by ArsenalPay.').'</b><br /><br />';
    }
    
    private function _displayForm()
    {
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
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>
            <legend><img src="../img/admin/cog.gif" />'.$this->l('Configuration').'</legend>
                <table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Unique token').'</label><span style="color:red"> *</span>
							<span class="annotation">'.$this->l('Assigned to merchant for access to ArsenalPay payment frame.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_token" value="'.htmlentities(Tools::getValue('arsenalpay_token', $this->am_config['arsenalpay_token']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
					 <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Frame URL').'</label><span style="color:red"> *</span>
							<span class="annotation">'.$this->l('URL-address of ArsenalPay payment frame.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_url" value="'.htmlentities(Tools::getValue('arsenalpay_frame_url', $this->am_config['arsenalpay_frame_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
					<tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('src parameter').'</label><span style="color:red"> *</span>
							<span class="annotation">'.$this->l('Payment type. Possible values:').' <b>mk</b> - '.$this->l('payment from mobile phone account (mobile-commerce).').' <b>card </b>- '.$this->l('payment from plastic card (internet acquiring).').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_srcc" value="'.htmlentities(Tools::getValue('arsenalpay_srcc', $this->am_config['arsenalpay_srcc']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('css parameter').'</label>
							<span class="annotation">'.$this->l('URL-address of CSS file if exists. Optional.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_css" value="'.htmlentities(Tools::getValue('arsenalpay_css', $this->am_config['arsenalpay_css']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Sign key').'</label><span style="color:red"> *</span>
							<span class="annotation">'.$this->l('With this key you check a validity of sign that comes with callback payment data.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_key" value="'.htmlentities(Tools::getValue('arsenalpay_key', $this->am_config['arsenalpay_key']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Allowed IP address').'</label>
							<span class="annotation">'.$this->l('It can be allowed to receive ArsenalPay payment confirmation callback requests only from IP address pointed out here.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_ip_adress" value="'.htmlentities(Tools::getValue('arsenalpay_ip_adress', $this->am_config['arsenalpay_ip_adress']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Callback URL').'</label><span style="color:red"> *</span>
							<span class="annotation">'.$this->l('For payment confirmation.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_url" value="'.htmlentities(Tools::getValue('arsenalpay_callback_url', $this->am_config['arsenalpay_callback_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Check URL').'</label>
							<span class="annotation">'.$this->l('To check an order number before payment processing. Optional.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_check_url" value="'.htmlentities(Tools::getValue('arsenalpay_check_url', $this->am_config['arsenalpay_check_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>                          
                                                <td valign="top" width="50%">
							<label for="token">'.$this->l('Frame mode').'</label>
							<span class="annotation"><b>1</b> '.$this->l('will mean that frame will be displayed inside your site, otherwise it will be displayed outside in a frame page.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_mode" value="'.htmlentities(Tools::getValue('arsenalpay_frame_mode', $this->am_config['arsenalpay_frame_mode']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">'.$this->l('Payment frame additional parameters').'</label>
							<span class="annotation">'.$this->l('Here you can set ifame parameters and so modify how ArsenalPay payment frame will be displayed.').'</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_params" value="'.htmlentities(Tools::getValue('arsenalpay_frame_params', $this->am_config['arsenalpay_frame_params']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';
    }

    function getContent()
    {
        $this->_html = '<img src="../modules/arsenalpay/logo.png" style="float:left; margin-right:15px;"><h2>'.$this->displayName.'</h2>';

        if (!empty($_POST))
        {
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= '<div class="alert error">'. $err .'</div>';
        }
        else
            $this->_html .= '<br />';

        $this->_displayArsenalpay();
        $this->_displayForm();

        return $this->_html;
    }

    function hookPayment($params)
    {
		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_am' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
        return $this->display(__FILE__, 'arsenalpay.tpl');
    }
}
?>
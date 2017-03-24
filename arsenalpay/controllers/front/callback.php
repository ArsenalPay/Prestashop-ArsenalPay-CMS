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

class ArsenalpayCallbackModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $str_log;
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $ars_callback = $_POST;
        $config = $this->module->am_config;
        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
        $this->str_log = date('Y-m-d H:i:s')." ".$REMOTE_ADDR;
        $KEY = $config['arsenalpay_key'];
        $IP_ALLOW = $config['arsenalpay_ip_adress'];
        if (strlen($IP_ALLOW) > 0 && $IP_ALLOW != $REMOTE_ADDR) {
            $this->exitf('ERR_IP');
        }

        $keyArray = array
        (
            'ID',           /* Идентификатор ТСП/ merchant identifier */
            'FUNCTION',     /* Тип запроса/ type of request to which the response is received*/
            'RRN',          /* Идентификатор транзакции/ transaction identifier */
            'PAYER',        /* Идентификатор плательщика/ payer(customer) identifier */
            'AMOUNT',       /* Сумма платежа/ payment amount */
            'ACCOUNT',      /* Номер получателя платежа (номер заказа, номер ЛС) на стороне ТСП/ order number */
            'STATUS',       /* Статус платежа - check - запрос на проверку номера получателя : payment - запрос на передачу статуса платежа
            /* Payment status. When 'check' - response for the order number checking, when 'payment' - response for status change.*/
            'DATETIME',     /* Дата и время в формате ISO-8601 (YYYY-MM-DDThh:mm:ss±hh:mm), УРЛ-кодированное */
            /* Date and time in ISO-8601 format, urlencoded.*/
            'SIGN',         /* Подпись запроса/ response sign.
             //* = md5(md5(ID).md(FUNCTION).md5(RRN).md5(PAYER).md5(AMOUNT).md5(ACCOUNT).md(STATUS).md5(PASSWORD)) */
        );
        /**
         * Checking the absence of each parameter in the post request.
         * Проверка на присутствие каждого из параметров и их значений в передаваемом запросе.
         */

        foreach ($keyArray as $key) {
            if( empty($ars_callback[$key]) || !array_key_exists($key, $ars_callback)) {
                $this->exitf('ERR_'.$key);
            }
            else {
                $this->str_log .= " $key=$ars_callback[$key]";
            }
        }

        //======================================
        /**
         * Checking validness of the request sign.
         */
        $id_order = Order::getOrderByCartId($ars_callback['ACCOUNT']);
        $objOrder = new Order($id_order);
        if (!($this->_checkSign( $ars_callback, $KEY))) {
            $objOrder->setCurrentState(_PS_OS_ERROR_);
            $this->exitf('ERR_INVALID_SIGN');
        }
        $lessAmount = false;
        $total = floatval($objOrder->total_paid);
        if ($ars_callback['MERCH_TYPE'] == 0 && $total == $ars_callback['AMOUNT']) {
            $lessAmount = false;
        }
        elseif ($ars_callback['MERCH_TYPE'] == 1 && $total >= $ars_callback['AMOUNT'] && $total == $ars_callback['AMOUNT_FULL']) {
            $lessAmount = true;
        }
        else {
            $this->exitf('ERR_AMOUNT');
        }

        if ($ars_callback['FUNCTION'] == "check" && $ars_callback['STATUS'] == "check") {
            // Check account
            /*
                    Here is account check procedure
                    Result:
                    YES - account exists
                    NO - account not exists
            */
            if (Validate::isLoadedObject($objOrder)) {
                $objOrder->setCurrentState(_PS_OS_PREPARATION_);
                $this->exitf('YES');
            }
            else {
                $objOrder->setCurrentState(_PS_OS_ERROR_);
                $this->exitf('NO');
            }
        }
        elseif ($ars_callback['FUNCTION'] == "payment" && $ars_callback['STATUS'] == "payment") {
            /**
             * Payment callback
             * Here is callback payment saving procedure
             * Result:
             * OK - success saving
             * ERR - error saving
             */

            $dbResult = Db::getInstance()->executeS('SELECT `id_order_state` FROM `'._DB_PREFIX_
                .'order_state_lang` WHERE `template` = "payment" GROUP BY `template`;');
            if ($lessAmount) {
                $logMsg = "Order #{$id_order} - payment with less amount {$ars_callback['AMOUNT']}";
            }
            else {
                $logMsg = "Order #{$id_order} - payment with full amount {$total}";
            }
            $this->log($logMsg);
            $newOrderState = (int)$dbResult[0]['id_order_state'];
            $objOrder->setCurrentState($newOrderState);
            $this->exitf('OK');
        }
        else {
            $objOrder->setCurrentState(_PS_OS_ERROR_);
            $this->exitf('ERR');
        }
    }

    private function _checkSign( $ars_callback, $pass)
    {
        $validSign = ( $ars_callback['SIGN'] === md5(md5($ars_callback['ID']).
                md5($ars_callback['FUNCTION']).md5($ars_callback['RRN']).
                md5($ars_callback['PAYER']).md5($ars_callback['AMOUNT']).md5($ars_callback['ACCOUNT']).
                md5($ars_callback['STATUS']).md5($pass) ) )? true : false;
        return $validSign;
    }

    protected function exitf($response)
    {
        // Saving income params into log file:
        $this->log($this->str_log." ".$response."\r\n");

        echo $response;
        exit;
    }

    protected function log($msg)
    {
        // Saving income params into log file:
        $fp = fopen(dirname(__FILE__).'/callback.log', 'a+');
        fwrite($fp, $msg);
        fclose($fp);
    }
}

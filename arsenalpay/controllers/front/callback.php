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
        $callback_params = $_POST;
        $config = $this->module->am_config;

        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
        $this->str_log = date('Y-m-d H:i:s')." ".$REMOTE_ADDR;

        $IP_ALLOW = $config['arsenalpay_ip_address'];

        if (strlen($IP_ALLOW) > 0 && $IP_ALLOW != $REMOTE_ADDR) {
        	$this->str_log .= " Denied IP";
            $this->exitf('ERR');
        }

        $order_id = Order::getOrderByCartId($callback_params['ACCOUNT']);
        $order = new Order($order_id);

	    if (!($this->_checkParams($callback_params))) {
		    $this->exitf('ERR');
	    }

	    $callback_key = $config['arsenalpay_callback_key'];
        if (!($this->_checkSign( $callback_params, $callback_key))) {
        	$this->str_log .= " invalid sign";
            $this->exitf('ERR');
        }

        $function = $callback_params['FUNCTION'];
	    switch ($function) {
		    case 'check': {
		    	$this->_callbackCheck($callback_params, $order);
		    	break;
		    }
		    case 'payment': {
			    $this->_callbackPayment($callback_params, $order);
			    break;
		    }
		    case 'hold': {
			    $this->_callbackHold($callback_params, $order);
			    break;
		    }
		    case 'cancel': {
			    $this->_callbackCancel($callback_params, $order);
			    break;
		    }
		    case 'cancelinit': {
			    $this->_callbackCancel($callback_params, $order);
			    break;
		    }
		    case 'refund': {
			    $this->_callbackRefund($callback_params, $order);
			    break;
		    }
		    case 'reversal': {
			    $this->_callbackReverse($callback_params, $order);
			    break;
		    }
		    case 'reverse': {
			    $this->_callbackReverse($callback_params, $order);
			    break;
		    }
		    default: {
		    	$this->str_log .= " Function {$function} is not supported";
			    $order->setCurrentState(_PS_OS_ERROR_);
		    	$this->exitf("ERR");
		    }
	    }
    }

	private function _callbackCheck($callback_params, $order) {
		if (!Validate::isLoadedObject($order)) {
			$order->setCurrentState(_PS_OS_ERROR_);
			$this->exitf('NO');
		}

		$rejected_statuses = array(
			Configuration::get('PS_OS_REFUND'),
			Configuration::get('PS_OS_CANCELED'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}

		$total             = number_format(floatval($order->total_paid), 2, '.', '');
		$is_correct_amount = ($callback_params['MERCH_TYPE'] == 0 && $total == $callback_params['AMOUNT']) ||
		                     ($callback_params['MERCH_TYPE'] == 1 && $total >= $callback_params['AMOUNT'] && $total == $callback_params['AMOUNT_FULL']);
		if (!$is_correct_amount) {
			$this->str_log .= " Wrong amount";
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('ARSENALPAY_OS_CHECK'));
		$this->exitf('YES');

	}

	private function _callbackPayment($callback_params, $order) {
		$rejected_statuses = array(
			Configuration::get('PS_OS_REFUND'),
			Configuration::get('PS_OS_CANCELED'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}

		$total = number_format(floatval($order->total_paid), 2, '.', '');
		if ($callback_params['MERCH_TYPE'] == 0 && $total == $callback_params['AMOUNT']) {
			$this->str_log .= " Order #{$order->id} - payment with full amount {$total}";
		}
		elseif ($callback_params['MERCH_TYPE'] == 1 && $total >= $callback_params['AMOUNT'] && $total == $callback_params['AMOUNT_FULL']) {
			$this->str_log .= " Order #{$order->id} - payment with less amount {$callback_params['AMOUNT']}";
		}
		else {
			$this->str_log .= " Wrong amount";
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
		$this->exitf('OK');
	}

	private function _callbackHold($callback_params, $order) {
		$rejected_statuses = array(
			Configuration::get('PS_OS_PAYMENT'),
			Configuration::get('PS_OS_REFUND'),
			Configuration::get('PS_OS_CANCELED'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}
		$total = number_format(floatval($order->total_paid), 2, '.', '');
		if ($callback_params['MERCH_TYPE'] == 0 && $total == $callback_params['AMOUNT']) {
			$this->str_log .= " Order #{$order->id} - hold with full amount {$total}";
		}
		elseif ($callback_params['MERCH_TYPE'] == 1 && $total >= $callback_params['AMOUNT'] && $total == $callback_params['AMOUNT_FULL']) {
			$this->str_log .= " Order #{$order->id} - hold with less amount {$callback_params['AMOUNT']}";
		}
		else {
			$this->str_log .= " Wrong amount";
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('ARSENALPAY_OS_HOLD'));
		$this->exitf('OK');
	}

	private function _callbackCancel($callback_params, $order) {
		$rejected_statuses = array(
			Configuration::get('PS_OS_REFUND'),
			Configuration::get('PS_OS_CANCELED'),
			Configuration::get('PS_OS_PAYMENT'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
		$this->exitf('OK');
	}

	private function _callbackRefund($callback_params, $order) {
		$rejected_statuses = array(
			Configuration::get('ARSENALPAY_OS_CHECK'),
			Configuration::get('PS_OS_CANCELED'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}

		$total             = number_format(floatval($order->total_paid), 2, '.', '');
		$is_correct_amount = ($callback_params['MERCH_TYPE'] == 0 && $total >= $callback_params['AMOUNT']) ||
		                     ($callback_params['MERCH_TYPE'] == 1 && $total >= $callback_params['AMOUNT'] && $total >= $callback_params['AMOUNT_FULL']);

		if (!$is_correct_amount) {
			$this->str_log .= " Wrong amount";
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('PS_OS_REFUND'));
		$this->str_log .= " Partition refund: {$callback_params['AMOUNT']}";
		$this->exitf('OK');
	}

	private function _callbackReverse($callback_params, $order) {
		$rejected_statuses = array(
			Configuration::get('ARSENALPAY_OS_CHECK'),
			Configuration::get('PS_OS_REFUND'),
			Configuration::get('PS_OS_CANCELED'),
		);
		if (in_array($order->getCurrentState(), $rejected_statuses)) {
			$this->str_log .= " Order has rejected status: " . $order->getCurrentState();
			$this->exitf('ERR');
		}

		$total             = number_format(floatval($order->total_paid), 2, '.', '');
		$is_correct_amount = ($callback_params['MERCH_TYPE'] == 0 && $total == $callback_params['AMOUNT']) ||
		                     ($callback_params['MERCH_TYPE'] == 1 && $total >= $callback_params['AMOUNT'] && $total == $callback_params['AMOUNT_FULL']);

		if (!$is_correct_amount) {
			$this->str_log .= " Wrong amount";
			$this->exitf('ERR');
		}

		$order->setCurrentState(Configuration::get('PS_OS_REFUND'));
		$this->str_log .= " Full refund: {$callback_params['AMOUNT']}";
		$this->exitf('OK');
	}

	private function _checkParams($callback_params) {
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
			if (empty($callback_params[$key]) || !array_key_exists($key, $callback_params)) {
				$this->str_log .= " ERROR Param {$key} is empty!";

				return false;
			}
			else {
				$this->str_log .= " $key=$callback_params[$key]";
			}
		}

		if ($callback_params['FUNCTION'] != $callback_params['STATUS']) {
			$this->str_log .= " FUNCTION != STATUS";

			return false;
		}

		return true;

	}

    private function _checkSign( $callback_params, $callback_key)
    {
        $validSign = ( $callback_params['SIGN'] === md5(md5($callback_params['ID']).
                md5($callback_params['FUNCTION']).md5($callback_params['RRN']).
                md5($callback_params['PAYER']).md5($callback_params['AMOUNT']).md5($callback_params['ACCOUNT']).
                md5($callback_params['STATUS']).md5($callback_key) ) )? true : false;
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

<?php
if (!defined('_PS_VERSION_')) 
    {
	exit;
    }
//	include_once(_PS_MODULE_DIR_.'arsenalpay/arsenalpay.php');

class ArsenalpayCallbackModuleFrontController extends ModuleFrontController
    {
	public $ssl = true;
	public $display_column_left = false;
    public $str_log;
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
            {
		parent::initContent();
	
                //$cart = Context::getContext();
				
		//if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
		
          //          {
			//$this->exitf ('ERR');
          //          }
		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		//$authorized = false;
		//foreach (Module::getPaymentModules() as $module)
       //             {
		//	if ($module['name'] == 'arsenalpay')
		//	{
		//		$authorized = true;
		//		break;
		//	}
        //            }
		//if (!$authorized)
        //            {echo 'auth</br>';
		//	$this->exitf ('ERR');
               //     }
		//$customer = new Customer($cart->id_customer);
		//if (!Validate::isLoadedObject($customer))
        //            { echo 'custom</br>';
		//	$this->exitf ('ERR');
         //           }

		//$currency = $this->context->currency;
		//$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
               
        
                $ars_callback = $_POST;
                $config = $this->module->am_config;
                $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
                $this->str_log = date('Y-m-d H:i:s')." ".$REMOTE_ADDR;
                $KEY = $config['arsenalpay_key'];
                $IP_ALLOW = $config['arsenalpay_ip_adress'];
                if( strlen( $IP_ALLOW ) > 0 && $IP_ALLOW != $REMOTE_ADDR ) 
                    {
                        $this->exitf( 'ERR_IP' );
                        
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
        
                foreach( $keyArray as $key ) 
                    {
                        if( empty( $ars_callback[$key] ) || !array_key_exists( $key, $ars_callback ) )
                            {
                                $this->exitf( 'ERR_'.$key );
                            }
                        else 
                            {
                                $this->str_log .= " $key=$ars_callback[$key]";
                            }
                    }
                    
        //======================================
          /*      if ($ars_callback['AMOUNT'] != $total)
                    {
                        $this->exitf( 'ERR_AMOUNT' );
                    }
                 */
     
                //======================================
                /**
                * Checking validness of the request sign.
                */
		if( !( $this->_checkSign( $ars_callback, $KEY) ) ) 
                    {
					//============== For testing, delete after testing =============================
      $S=md5(md5($ars_callback['ID']).
               md5($ars_callback['FUNCTION']).md5($ars_callback['RRN']).
              md5($ars_callback['PAYER']).md5($ars_callback['AMOUNT']).md5($ars_callback['ACCOUNT']).
              md5($ars_callback['STATUS']).md5($KEY) );
       echo $S.'</br>';
			$this->exitf( 'ERR_INVALID_SIGN' );
                        
                    }
					 $id_order = Order::getOrderByCartId($ars_callback['ACCOUNT']);
                     $objOrder = new Order($id_order);
                if( $ars_callback['FUNCTION'] == "check" )
                    {
                        // Check account
                        /*
                                Here is account check procedure
                                Result:
                                YES - account exists
                                NO - account not exists
                        */
						//$id_order = Order::getOrderByCartId($ars_callback['ACCOUNT']);
                        if ($id_order == NULL)
                            {
				   
                               $this->exitf( 'NO' );
                            }
							$objOrder->setCurrentState(_PS_OS_PREPARATION_);
                        $this->exitf( 'YES' );
                    }
                elseif( $ars_callback['FUNCTION']=="payment" )
                    {
                        // Payment callback
                        /*
                                Here is callback payment saving procedure
                                Result:
                                OK - success saving
                                ERR - error saving*/
                       // $arsenalpay = new ArsenalPay();
						//echo $arsenalpay->displayName;
							
                        $dbResult = Db::getInstance()->executeS('SELECT `id_order_state` FROM `'._DB_PREFIX_
                                .'order_state_lang` WHERE `template` = "payment" GROUP BY `template`;');
								
                        $newOrderState = (int)$dbResult[0]['id_order_state'];
                       // $arsenalpay->validateOrder($ars_callback['ACCOUNT'], $newOrderState, $ars_callback['AMOUNT'], $arsenalpay->displayName);
					    
					   
                       $objOrder->setCurrentState($newOrderState);
                       $this->exitf('OK');
                    }
                else 
                    { 
					//$objOrder->setCurrentState(_PS_OS_ERROR_);
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
        public function exitf($msg)
            {
                // Saving income params into log file:
                $fp = fopen(dirname(__FILE__).'/callback.log', 'a+');
                fwrite($fp, $this->str_log." ".$msg."\r\n");
                fclose($fp);

                echo $msg;
                exit;
            }
            }

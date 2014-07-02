<?php

/**
 * radCodes CoreLite Library
 * 
 * This is an extented library developed by radCodes to be used in
 * various plug-ins and customization works for Social Engine.
 * Do NOT modify this file in anyway. This file is not Open-Source.
 * 
 * @category   library
 * @package    socialEngine.library
 * @author     Vincent Van <vctvan@gmail.com>
 * @copyright  2008 radCodes <vctvan@gmail.com>
 * @version    $Id: class_radcodes_gateway.php 2008-07-26 00:45:12 $
 */
define('RC_GW_TEST_MODE', 0);

define('RADCODES_GATEWAY_LIBRARY_VERSION', 1.00);

class rc_gateway_payment
{
	var $error;
	var $debug = false;
	var $debug_file;
	var $method = 'default';
	
	var $hidden_fields = array();
	
	function rc_gateway_payment()
	{
		if (defined('RC_GW_DEBUG') and RC_GW_DEBUG) {
			$this->debug = true;
			$this->debug_file = 'include/rc-gw-'.time().'.txt';
		}
		$this->method = strtolower(str_replace('rc_gateway_', '', get_class($this)));
	}
	
	
  function data_post_back( $urls=array(), $post_back_str="", $port=80 )
  {
    $curl_used     = 0;
    $result        = "";
    
    if ( !$post_back_str ) {
      foreach ($_POST as $key => $val) {
        $post_back[] = $key . '=' . urlencode ($val);
      }
      
      $post_back_str = implode('&', $post_back);
    }
    
    //--------------------------------------
    // Attempt CURL
    //--------------------------------------
    
    if ( function_exists("curl_init") AND function_exists("curl_exec") ) {
      if ( $sock = curl_init() ) {
        curl_setopt( $sock, CURLOPT_URL            , $urls['curl_full'] );
        curl_setopt( $sock, CURLOPT_TIMEOUT        , 15 );
        curl_setopt( $sock, CURLOPT_POST           , TRUE );
        curl_setopt( $sock, CURLOPT_POSTFIELDS     , $post_back_str );
        curl_setopt( $sock, CURLOPT_POSTFIELDSIZE  , 0);
        curl_setopt( $sock, CURLOPT_RETURNTRANSFER , TRUE ); 
    
        $result = curl_exec($sock);
        
        curl_close($sock);
        
        if ($result !== FALSE) {
          $curl_used = 1;
        }
      }
    }
    
    //--------------------------------------
    // Not got a result?
    //--------------------------------------
    
    if (!$curl_used) {
    	
      $header  = "POST {$urls['sock_path']} HTTP/1.0\r\n";
      $header .= "Host: {$urls['sock_url']}\r\n";
      $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $header .= "Content-Length: " . strlen($post_back_str) . "\r\n\r\n";
      
      if ( $fp = fsockopen( $urls['sock_url'], $port, $errno, $errstr, 30 ) ) {
        socket_set_timeout($fp, 30);
        
        fwrite($fp, $header . $post_back_str);
        
        while ( ! feof($fp) ) {
          $result .= fgets($fp, 1024);
        }
        
        fclose($fp);
      }
    }
    
    if ($this->debug) {
      $to_write = $result.'\n\n'.$post_back_str.'\n\n';
      
      foreach( $_POST as $k => $v ) {
        $to_write .= "{$k}: {$v}\n";
      }
      
      $this->_write_debug_message( $to_write );
    }
    
    return $result;
  }	
	
	
  function _write_debug_message( $message )
  {
    if (!$this->debug OR !$this->debug_file) {
      return;
    }

    $bars     = '----------------------------------------------------------------------------';
    $date_now = date( 'F j, Y, g:i a' );
    
    $msg_to_write = $bars."\n"."Date: ".$date_now."\n"."Gateway: ".get_class($this)."\n".$bars."\n".$message;
    
    if ( $FH = @fopen( $this->debug_file, 'a+' ) )
    {
      @fwrite( $FH, $msg_to_write, strlen( $msg_to_write ) );
      @fclose( $FH );
    }
  }
  
  
  function add_hidden_field($field, $value)
  {
    $this->hidden_fields[$field] = $value;
  }
  
  function build_hidden_fields()
  {
    $out = '';
    foreach ($this->hidden_fields as $field => $value) {
      $out .= "\n<input type='hidden' name='$field' value='$value' />";
    }
    return $out;
  }
  
  function generate_form_action()
  {
  }

  function generate_purchase_button($options=array())
  {
    return '<input type="image" src="'.$options['src'].'" alt="'.$options['alt'].'" />';	
  }
  
  function validate_payment()
  {
  	;
  }
  
  function payment_check($balance)
  {
  	return 'PAID';
  }
  
  function get_payer_account()
  {
  	;
  }
  
}


class rc_gateway_paypal extends rc_gateway_payment {
	
  
	function generate_hidden_fields($items=array())
	{
    $this->add_hidden_field( "cmd"          , "_xclick" );
    $this->add_hidden_field( "currency_code", $items['currency_code'] );
    $this->add_hidden_field( "custom"       , $items['member_unique_id'] );
    $this->add_hidden_field( "item_number"  , $items['package_id'] );
    $this->add_hidden_field( "item_name"    , $items['package_title'] );
    $this->add_hidden_field( "amount"       , $items['package_cost'] );
    $this->add_hidden_field( "business"     , $items['company_email'] );
    $this->add_hidden_field( "no_shipping"  , 1 );
    $this->add_hidden_field( "src"          , 1 );
    $this->add_hidden_field( "notify_url"   , $items['GW_URL_VALIDATE']  );
    $this->add_hidden_field( "return"       , $items['GW_URL_PAYDONE']   );
    $this->add_hidden_field( "cancel_return", $items['GW_URL_PAYCANCEL'] );
    
    return $this->build_hidden_fields();
	}
	
	function generate_form_action()
	{
		return "https://www.paypal.com/cgi-bin/webscr";
	}
	
	function validate_payment()
	{
    if (defined('RC_GW_TEST_MODE') and RC_GW_TEST_MODE) {
      if (!is_array($_POST) or !count($_POST)) {
        $_POST = $_GET;
      }
    }
    
    $post_back[] = 'cmd=_notify-validate';
    
    foreach ($_POST as $key => $val) {
      $post_back[] = $key . '=' . urlencode (stripslashes($val));
    }
    
    $post_back_str = implode('&', $post_back);

    $urls = array( 'curl_full' => 'http://www.paypal.com/cgi-bin/webscr',
             'sock_url'  => 'www.paypal.com',
             'sock_path' => '/cgi-bin/webscr' );
             

    $state = $this->data_post_back($urls, $post_back_str, 80 );
    $state = ( stristr($state, 'VERIFIED') ) ? 'VERIFIED' : 'INVALID';
    
    if ( $state != 'VERIFIED' ) {
      $this->error = 'not_valid';
      return array( 'verified' => false );
    }
    
    //--------------------------------------
    // Second POST - we can ignore
    //--------------------------------------
    if ( ! $_POST['txn_id'] and $_POST['txn_type'] == 'subscr_signup' ) {
      exit();
    }
    

    list( $cur_sub_id, ) = explode( 'x', trim($_POST['invoice']) );
    
      $return = array( 
             'currency_code'      => $_POST['mc_currency'],
             'payment_amount'     => $_POST['mc_gross'],
             'user_id'            => intval($_POST['custom']),
             'purchase_item_id'   => intval($_POST['item_number']),
             'current_item_id'    => intval($cur_sub_id),
             'verified'           => true,
             'subscription_id'    => $_POST['subscr_id'],
             'transaction_id'     => $_POST['txn_id'] );
    

    if ( $_POST['payment_status'] == 'Refunded' ) {
      $return['payment_status'] = 'REFUND';
    }
    else if( $_POST['txn_type'] == 'subscr_cancel' || $_POST['payment_status'] == 'Cancelled_Reversal' || $_POST['payment_status'] == 'Reversed') {
      $return['payment_status'] = 'CANCEL';
    }
    else if ( strstr( $_POST['txn_type'], 'subscr_' ) ) {
      $return['payment_status'] = 'RECURRING';
    }
    else if ( $_POST['txn_type'] == 'web_accept' ) {
      $return['payment_status'] = 'ONEOFF';
    }
    else {
      $return['payment_status'] = '';
    }
    
    return $return;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param double $balance Amount to check
	 * @return  PAID, DEAD, FAILED, PENDING
	 */
	function payment_check($balance)
	{
		$this->error = false;
		
		if ($_POST['payment_status'] == 'Completed') {
			if ($_POST['mc_gross'] == $balance) {
        $result  = 'PAID';
			}
			else {
				$this->error = "Wrong payment amount. Balance {$balance}, got {$_POST['mc_gross']}";
				$result = 'FAILED';
			}
		}
		elseif ($_POST['payment_status'] == 'Pending') {
			$result = 'PENDING';
		}
		else {
			$result = 'FAILED';
		}
    
    return $result;
	}
	
	
	function get_payer_account()
	{
	  return $_POST['payer_email'];
	}
	
	
}


class rc_gateway_nocharger extends rc_gateway_payment 
{
  function generate_hidden_fields($items=array())
  {
    $this->add_hidden_field( "currency_code", $items['currency_code'] );
    $this->add_hidden_field( "user_id"       , $items['member_unique_id'] );
    $this->add_hidden_field( "purchase_item_id"  , $items['package_id'] );
    $this->add_hidden_field( "payment_amount"       , $items['package_cost'] );

    $this->add_hidden_field( "notify_url"   , $items['GW_URL_VALIDATE']  );
    $this->add_hidden_field( "return"       , $items['GW_URL_PAYDONE']   );
    $this->add_hidden_field( "cancel_return", $items['GW_URL_PAYCANCEL'] );
    
    return $this->build_hidden_fields();
  }

  function generate_form_action($items=array())
  {
    return $items['GW_URL_VALIDATE'];
  }
  
  function validate_payment()
  {
    if (defined('RC_GW_TEST_MODE') and RC_GW_TEST_MODE) {
      if (!is_array($_POST) or !count($_POST)) {
        $_POST = $_GET;
      }
    }
    
    $my_domain = $_SERVER['HTTP_HOST'];
    $my_referer = $_SERVER['HTTP_REFERER'];
    
    $referers  = array($my_domain, str_replace('www.', '', $my_domain));
    $got_match = 0;
    
    if (preg_match("#http(s)?://$r#i", $my_referer)) {
      $got_match = 1;
    }
    
    if (!$got_match) {
      $this->error = 'not_valid';
      return array( 'verified' => FALSE );
    }
    
      $return = array( 'currency_code'      => $_POST['currency_code'],
             'payment_amount'     => $_POST['payment_amount'],
             'user_id'            => intval($_POST['user_id']),
             'purchase_item_id'=> intval($_POST['purchase_item_id']),
             'current_item_id' => intval($cur_sub_id),
             'verified'           => TRUE,
             'subscription_id'    => '0-'.intval($_POST['user_id']),
             'transaction_id'     => 'Mx'.time() );
    
    $return['payment_status'] = 'ONEOFF';
    
    return $return;    
  }
  
  function payment_check($balance)
  {
    $this->error = false;
    
    if ($_POST['payment_amount'] == $balance) {
      $result  = 'PAID';
    }
    else {
      $this->error = "Wrong payment amount. Balance {$balance}, got {$_POST['payment_amount']}";
      $result = 'FAILED';
    }
    
    return $result;
  } 
  
}

class rc_gateway_2checkout extends rc_gateway_payment {
  
  
  function generate_hidden_fields($items=array())
  {
    $this->add_hidden_field( "merchant_order_id"    , $items['package_id'].'x'.$items['member_unique_id'].'x0' );
    $this->add_hidden_field( "sid"                  , $items['vendor_id'] );
    $this->add_hidden_field( "product_id"           , $items['product_id'] );
    $this->add_hidden_field( "quantity"             , 1 );  	
  	
    return $this->build_hidden_fields();
  }
  
  function generate_form_action()
  {
    return "https://www.2checkout.com/cgi-bin/sbuyers/purchase.2c";
  }
  
  function validate_payment()
  {
    if (defined('RC_GW_TEST_MODE') and RC_GW_TEST_MODE) {
      if (!is_array($_POST) or !count($_POST)) {
        $_POST = $_GET;
      }
    }
    
    $my_domain = $_SERVER['HTTP_HOST'];
    $my_referer = $_SERVER['HTTP_REFERER'];
    
    $referers  = array('www.2checkout.com', '2checkout.com', $my_domain, str_replace('www.', '', $my_domain));
    $got_match = 0;
    
    if (preg_match("#http(s)?://$r#i", $my_referer)) {
      $got_match = 1;
    }
    
    if (!$got_match) {
      $this->error = 'not_valid';
      return array( 'verified' => FALSE );
    }
    
    //--------------------------------------
    // Populate return array
    //--------------------------------------
    
    list( $purchase_package_id, $member_id, $cur_sub_id, ) = explode( 'x', trim($_REQUEST['merchant_order_id']) );
    
      $return = array( 'currency_code'      => 'USD',
             'payment_amount'     => $_REQUEST['total'],
             'user_id'            => intval($member_id),
             'purchase_package_id'=> intval($purchase_package_id),
             'current_package_id' => intval($cur_sub_id),
             'verified'           => TRUE,
             'subscription_id'    => '0-'.intval($member_id),
             'transaction_id'     => $_REQUEST['order_number'] .'x'.time() );
    
    $return['payment_status'] = 'ONEOFF';
    
    return $return;
   
  }
  
  /**
   * Enter description here...
   *
   * @param double $balance Amount to check
   * @return  PAID, DEAD, FAILED, PENDING
   */
  function payment_check($balance)
  {
    $this->error = false;
    
    $_POST['amount'] = $_POST['amount'] ? $_POST['amount'] : $_POST['total'];

    if ($_POST['amount'] == $balance) {
    	$result  = 'PAID';
    }
    else {
      $this->error = "Wrong payment amount. Balance {$balance}, got {$_POST['amount']}";
      $result = 'FAILED';
    }
    
    return $result;
  }
  
  
  function get_payer_account()
  {
    return $_POST['payer_email'];
  }
  
  
}



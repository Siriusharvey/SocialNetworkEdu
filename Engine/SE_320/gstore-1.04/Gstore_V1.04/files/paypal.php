





<?php
$page = "paypal";
include "header.php";

  $band_a = $_POST['band_a'];
  $band_b = $_POST['band_b'];
  $band_c = $_POST['band_c'];
  $band_d = $_POST['band_d'];

 $buyer_location = $_POST['buyer_location'];
 
   switch ($buyer_location)
{
case 1:
$shipping_charge = $band_a ;
 break;
case 2:
$shipping_charge = $band_b ;
 break;
case 3:
$shipping_charge = $band_c ;
 break;
 case 4:
$shipping_charge = $band_d ;
 break;
 default;
$display = 'style="display:none;"';
} 









  $stockinhand = $_POST['stockinhand'];
  $smarty->assign_by_ref('stockinhand', $stockinhand);

  $qty = $_POST['qty'];
  
  $smarty->assign_by_ref('qty', $qty);
  
  
  
  
  switch ($setting[gstore_currency])
{
case 5555187:
$country_code = "US" ;
$currency_code = "USD" ;
 break;
case 5555188:
$country_code = "GB" ;
$currency_code = "GBP" ;
 break;
case 5555190:
$country_code = "GB" ;
$currency_code = "EUR" ;
 break;
} 

 $smarty->assign_by_ref('country_code', $country_code);
 $smarty->assign_by_ref('currency_code', $currency_code); 
  
  
  
  
  $apply_shipping_charges = $_POST['apply_shipping_charges'];
  $paypal_email = $_POST['business'];
  $item_id = $_POST['item_id'];
  $item_title = $_POST['item_name'];
  $price = $_POST['amount'];
  $stock = $_POST['stock'];
  $seller_sales = $_POST['seller_sales'];
  $gstore_settings_user_id = $_POST['gstore_settings_user_id'];
  $item_sales = $_POST['item_sales'];
  
  
  
 
  
  
    
  
    switch ($apply_shipping_charges)
{
case 'Per item':
$shipping_charge = $shipping_charge * $qty ;
 break;
case 'Total order':
$shipping_charge = $shipping_charge ;
 break;
case 'No shipping':
$shipping_charge = '0.00' ;
} 
 
  
  
  $total = ($qty * $price);
  $total = ($total + $shipping_charge);
  $smarty->assign_by_ref('total', $total);




//update total item sales
  $sql = "UPDATE se_gstores SET item_sales='$item_sales' WHERE gstore_id='$item_id' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);


//update total seller sales
$user_sales = $seller_sales;
$user_sales = $user_sales+$qty;

  $sql = "UPDATE se_users SET user_sales='$user_sales' WHERE user_id='$gstore_settings_user_id' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);


//update total stock
$stock = $stock-$qty;

  $sql = "UPDATE se_gstores SET gstore_stock='$stock' WHERE gstore_id='$item_id' LIMIT 1";
  $database->database_query($sql) or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);

  
  
  
  
  
  $smarty->assign_by_ref('display', $display);
  $smarty->assign_by_ref('display_error_no_location', $display_error_no_location);
  $smarty->assign_by_ref('display_error_no_selection', $display_error_no_selection);
  $smarty->assign_by_ref('shipping_charge', $shipping_charge);
  $smarty->assign_by_ref('apply_shipping_charges', $apply_shipping_charges);
  $smarty->assign_by_ref('paypal_email', $paypal_email);
  $smarty->assign_by_ref('item_id', $item_id);  
  $smarty->assign_by_ref('item_title', $item_title);  
  $smarty->assign_by_ref('price', $price);
  $smarty->assign_by_ref('stock', $stock);
  $smarty->assign_by_ref('seller_sales', $seller_sales);
  $smarty->assign_by_ref('gstore_settings_user_id', $gstore_settings_user_id);
  $smarty->assign_by_ref('item_sales', $item_sales);
  





include "footer.php";

?> 







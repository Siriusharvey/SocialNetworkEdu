





<?php
$page = "paypal_shipping";
include "header.php";


  $stockinhand = $_POST['stockinhand'];
  $smarty->assign_by_ref('stockinhand', $stockinhand);

  $qty = $_POST['qty'];
  
  if ($qty > $stockinhand)
{
$qty = $stockinhand ;
$msg = '1' ;
$smarty->assign_by_ref('msg', $msg);
}
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
  echo "no currency code found !" ;
} 

 $smarty->assign_by_ref('country_code', $country_code);
 $smarty->assign_by_ref('currency_code', $currency_code); 
  
  
  
  $apply_shipping_charges = $_POST['apply_shipping_charges'];
  $band_a = $_POST['band_a'];
  $band_b = $_POST['band_b'];
  $band_c = $_POST['band_c'];
  $band_d = $_POST['band_d'];
  $paypal_email = $_POST['business'];
  $item_id = $_POST['item_id'];
  $item_title = $_POST['item_name'];
  $price = $_POST['amount'];
  $stock = $_POST['stock'];
  $seller_sales = $_POST['seller_sales'];
  $gstore_settings_user_id = $_POST['gstore_settings_user_id'];
  $item_sales = $_POST['item_sales'];
  
  
  $smarty->assign_by_ref('apply_shipping_charges', $apply_shipping_charges);
  $smarty->assign_by_ref('band_a', $band_a);
  $smarty->assign_by_ref('band_b', $band_b);
  $smarty->assign_by_ref('band_c', $band_c);
  $smarty->assign_by_ref('band_d', $band_d);
  $smarty->assign_by_ref('paypal_email', $paypal_email);
  $smarty->assign_by_ref('item_id', $item_id);  
  $smarty->assign_by_ref('item_title', $item_title);  
  $smarty->assign_by_ref('price', $price);
  $smarty->assign_by_ref('stock', $stock);
  $smarty->assign_by_ref('seller_sales', $seller_sales);
  $smarty->assign_by_ref('gstore_settings_user_id', $gstore_settings_user_id);
  $smarty->assign_by_ref('item_sales', $item_sales);
  
  $total = ($qty * $price);
  $smarty->assign_by_ref('total', $total);


  $item_sales = $item_sales+$qty;


include "footer.php";

?> 


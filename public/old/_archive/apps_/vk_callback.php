<?php 
require_once('../admin/myconf.php');

header("Content-Type: application/json; encoding=utf-8"); 

$secret_key = 'VpRIlT9w5tuZjtqY7JHW'; // Защищенный ключ приложения 

$input = $_POST; 
// Проверка подписи 
$sig = $input['sig']; 
unset($input['sig']); 
ksort($input); 
$str = ''; 
foreach ($input as $k => $v) { 
  $str .= $k.'='.$v; 
} 

if ($sig != md5($str.$secret_key)) { 
  $response['error'] = array( 
    'error_code' => 10, 
    'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.', 
    'critical' => true 
  ); 
} else { 
  // Подпись правильная 
  switch ($input['notification_type']) { 
    case 'get_item': 
      // Получение информации о товаре 
      $item = $input['item']; // наименование товара 

    if ($item == '10stars') { 
        $response['response'] = array( 
          'item_id' => 1, 
          'title' => '10 звезд', 
          'price' => 1 
        ); 
      } elseif ($item == '50stars') { 
        $response['response'] = array( 
          'item_id' => 2, 
          'title' => '50 звезд', 
          'price' => 4 
        ); 
      } elseif ($item == '100stars') { 
        $response['response'] = array( 
          'item_id' => 3, 
          'title' => '100 звезд', 
          'price' => 7 
        ); 
      } else { 
        $response['error'] = array( 
          'error_code' => 20, 
          'error_msg' => 'Товара не существует.', 
          'critical' => true 
        ); 
      } 
      break; 

case 'get_item_test': 
      // Получение информации о товаре в тестовом режиме 
      $item = $input['item']; 
    if ($item == '10stars') { 
        $response['response'] = array( 
          'item_id' => 11, 
          'title' => '10 звезд - тест', 
          'price' => 1 
        ); 
      } elseif ($item == '50stars') { 
        $response['response'] = array( 
          'item_id' => 12, 
          'title' => '50 звезд - тест', 
          'price' => 4 
        ); 
      } elseif ($item == '100stars') { 
        $response['response'] = array( 
          'item_id' => 13, 
          'title' => '100 звезд - тест', 
          'price' => 7 
        ); 
      } else { 
        $response['error'] = array( 
          'error_code' => 20, 
          'error_msg' => 'Товара не существует.', 
          'critical' => true 
        ); 
      } 
      break; 

case 'order_status_change': 
// Изменение статуса заказа в тестовом режиме 
      if ($input['status'] == 'chargeable') { 
        $order_id = intval($input['order_id']); 
        
        
        $vkvoice = $_POST['item_price'];
        $vkitem = $_POST['item'];
        $vkuser = $_POST['user_id'];

        $query = "SELECT * FROM _vkapps WHERE vkuser=$vkuser;";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if($exists){
            $stars = mysql_result($result, 0, 'vkstars');
            $sum = mysql_result($result, 0, 'vksum');
            $app_order_id = $order_id; // Идентификатор заказа такой же, как у ВК
            $query = "SELECT * FROM _vkorder WHERE vkorder=$app_order_id;";
            $result = mysql_query($query);
            $exists = mysql_num_rows($result);
            if($exists){
                $u = mysql_result($result, 0, 'vkuser');
                $v = mysql_result($result, 0, 'vkvoice');
                $i = mysql_result($result, 0, 'vkitem');
                if($u != $vkuser || $v != $vkvoice || $i != $vkitem){
                    $response['error'] = array( 
                      'error_code' => 111, 
                      'error_msg' => 'Error of double query of the same order. The second query does not match the first.', 
                      'critical' => true 
                    ); 
                    break;
                }
            }else{
                $query = "INSERT INTO _vkorder (vkorder, vkuser, vkvoices, vkitem, orderdate) VALUES ($app_order_id, $vkuser, $vkvoice, '$vkitem', NOW());";
                $result = mysql_query($query);
                if(!$result){
                    $response['error'] = array( 
                      'error_code' => 102, 
                      'error_msg' => 'Error of DB. Check the query to insert order. '.mysql_error(), 
                      'critical' => true 
                    ); 
                    break;
                }

                $price=Array(
                    1   => 10,
                    4   => 50,
                    7   => 100
                );

                $stars += $price[$vkvoice];
                $sum += $vkvoice;
                $query = "UPDATE _vkapps SET vkstars=$stars, vksum=$sum WHERE vkuser=$vkuser;";
                $result = mysql_query($query);
                if(!$result){
                    $response['error'] = array( 
                      'error_code' => 103,
                      'error_msg' => 'Error of DB. Check the query to update user. '.mysql_error(), 
                      'critical' => true 
                    ); 
                    break;
                }
            }
        }else{
            $response['error'] = array( 
              'error_code' => 100, 
              'error_msg' => 'Передано непонятно что вместо chargeable.', 
              'critical' => true 
            ); 
            break;
        }


        $response['response'] = array( 
          'order_id' => $order_id, 
          'app_order_id' => $app_order_id, 
        ); 
      } else { 
        $response['error'] = array( 
          'error_code' => 100, 
          'error_msg' => 'Передано непонятно что вместо chargeable.', 
          'critical' => true 
        ); 
      } 
      break; 

case 'order_status_change_test': 
      // Изменение статуса заказа в тестовом режиме 
      if ($input['status'] == 'chargeable') { 
        $order_id = intval($input['order_id']); 
        
        
        $vkvoice = $_POST['item_price'];
        $vkitem = $_POST['item'];
        $vkuser = $_POST['user_id'];

        $query = "SELECT * FROM _vkapps WHERE vkuser=$vkuser;";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if($exists){
            $stars = mysql_result($result, 0, 'vkstars');
            $sum = mysql_result($result, 0, 'vksum');
            $app_order_id = $order_id; // Идентификатор заказа такой же, как у ВК
            $query = "SELECT * FROM _vkorder WHERE vkorder=$app_order_id;";
            $result = mysql_query($query);
            $exists = mysql_num_rows($result);
            if($exists){
                $u = mysql_result($result, 0, 'vkuser');
                $v = mysql_result($result, 0, 'vkvoice');
                $i = mysql_result($result, 0, 'vkitem');
                if($u != $vkuser || $v != $vkvoice || $i != $vkitem){
                    $response['error'] = array( 
                      'error_code' => 111, 
                      'error_msg' => 'Error of double query of the same order. The second query does not match the first.', 
                      'critical' => true 
                    ); 
                    break;
                }
            }else{
                $query = "INSERT INTO _vkorder (vkorder, vkuser, vkvoices, vkitem, orderdate) VALUES ($app_order_id, $vkuser, $vkvoice, '$vkitem', NOW());";
                $result = mysql_query($query);
                if(!$result){
                    $response['error'] = array( 
                      'error_code' => 102, 
                      'error_msg' => 'Error of DB. Check the query to insert order. '.mysql_error(), 
                      'critical' => true 
                    ); 
                    break;
                }

                $price=Array(
                    1   => 10,
                    4   => 50,
                    7   => 100
                );

                $stars += $price[$vkvoice];
                $sum += $vkvoice;
                $query = "UPDATE _vkapps SET vkstars=$stars, vksum=$sum WHERE vkuser=$vkuser;";
                $result = mysql_query($query);
                if(!$result){
                    $response['error'] = array( 
                      'error_code' => 103,
                      'error_msg' => 'Error of DB. Check the query to update user. '.mysql_error(), 
                      'critical' => true 
                    ); 
                    break;
                }
            }
        }else{
            $response['error'] = array( 
              'error_code' => 100, 
              'error_msg' => 'Передано непонятно что вместо chargeable.', 
              'critical' => true 
            ); 
            break;
        }


        $response['response'] = array( 
          'order_id' => $order_id, 
          'app_order_id' => $app_order_id, 
        ); 
      } else { 
        $response['error'] = array( 
          'error_code' => 100, 
          'error_msg' => 'Передано непонятно что вместо chargeable.', 
          'critical' => true 
        ); 
      } 
      break; 
  } 
} 

echo json_encode($response); 
?>

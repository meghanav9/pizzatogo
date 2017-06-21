<?php
require('../../util/main.php');
require('../../model/database.php');
require('../../model/day_db.php');
require('../../model/initial_db.php');
require('../../model/inventory_db.php');
require('../../restclient/webservices_call.php');
require('../../restclient/curl_helper.php');


$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'list';
    }
}
 $current_day = get_current_day($db);
if ($action == 'list') {
    try {
        $todays_orders = get_todays_orders($db, $current_day);
        $cur_inventory = get_inventory($db);
        $supply = get_undelivered($db);
    } catch (PDOException $e) {
    $error_message = $e->getMessage(); 
    include('../../errors/database_error.php');
    exit();
    }
    include('day_list.php');
}    else if ($action == 'change_to_nextday')
    {
    try {
        finish_orders_for_day($db, $current_day);
        increment_day($db);
        $newday = get_current_day($db);
        try {
            client_post_day($newday, $error_message);
        } catch (Exception $e) {
            $error = $e->getMessage();
            include('errors/error.php');
            exit();
        }
        $supplybefore = get_undelivered($db);
        foreach ($supplybefore as $orderIDs) {
            try {
                $order = client_get_order($orderIDs['orderid'], $error_message);
                if ($order[0]['delivered'])
                { $f = $orderIDs['flour_qty'];
                  $c = $orderIDs['cheese_qty'];
                  $id = $orderIDs['orderid'];
                  increase_inventory_flour($db,$f);
                  increase_inventory_cheese($db,$c);
                  remove_undelivereddata($db,$id);
            } }catch (Exception $e) {
                $error = $e->getMessage();
                include('errors/error.php');
                exit();
            }
            }
        $cur_inventory = get_inventory($db);
        $supply = get_undelivered($db);
        $flour_qty = $cur_inventory[0]['quantity'];
        $cheese_qty = $cur_inventory[1]['quantity'];
        foreach ($supply as $value){
            $flour_qty += $value['flour_qty'];
            $cheese_qty += $value['cheese_qty'];
        }
        $fqty = 0;
        $cqty = 0;
        if($flour_qty < 150)
        {
            if((150-$flour_qty) % 40 == 0){
                $fqty = 150 - $flour_qty;
            }
            else{
                $fqty = (floor((150 - $flour_qty) / 40) + 1) * 40;
            }
        }
        if($cheese_qty < 150)
        {
            if((150 - $cheese_qty) % 20 == 0){
                $cqty = 150 - $cheese_qty;
            }
            else{
                $cqty = (floor((150 - $cheese_qty) / 20) + 1) * 20;
            }
        }
        if($fqty > 0 || $cqty > 0){
            $item1 = array('productID' => 11, 'quantity' => $fqty);
            $item2 = array('productID' => 12, 'quantity' => $cqty);
            $order = array('customerID' =>1, array($item1, $item2));
        
                try {
                $location = client_post_order($order, $error_message);
                $parts = explode('/', $location);
                $orderID = end($parts);
                add_orders($db,$orderID,$fqty,$cqty);
            } catch (Exception $e) {
                $error = $e->getMessage();
                include('errors/error.php');
                exit();
            }
            }
        header("Location: .");
        }
        catch (PDOException $e) {
        $error_message = $e->getMessage(); 
        include('../../errors/database_error.php');
        exit();
    }}
        
else if ($action == 'initial_db')
    {
    try {
        initial_db($db);
        header("Location: .");
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include ('../../errors/database_error.php');
        exit();
    } 
}
function handle_get_day($newday) {
    error_log('rest server in handle_get_day, day = ' . $newday);
    echo $newday;
}

?>
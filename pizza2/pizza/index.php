<?php
require('../util/main.php');
require('../model/database.php');
require('../model/order_db.php');
require('../model/topping_db.php');
require('../model/size_db.php');
require('../model/day_db.php');
require('../model/inventory_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'student_welcome';
    }
}
$room = filter_input(INPUT_POST,'room',FILTER_VALIDATE_INT);
if ($room == NULL) {
    $room = filter_input(INPUT_GET, 'room');
    if ($room == NULL || $room == FALSE) {
       $room = 1;
    }
}
if ($action == 'student_welcome'|| $action == 'select_room') {
    try {
    $sizes = get_available_sizes($db);
    $toppings = get_available_toppings($db);
    $room_preparing_orders = get_preparing_orders_of_room($db, $room);
    $room_baked_orders = get_baked_orders_of_room($db, $room);
    } catch (PDOException $e) {
      $error_message = $e->getMessage(); 
      include('../errors/database_error.php');
      exit();
    }
    include('student_welcome.php');
}    
 else if ($action == 'order_pizza') {
    try {
        $sizes = get_available_sizes($db);
        $toppings = get_available_toppings($db);
     } catch (PDOException $e) {
      $error_message = $e->getMessage(); 
      include('../errors/database_error.php');
      exit();
    }
     include ('order_pizza.php');
    }
    elseif ($action=='add_order') {
        $size_id = filter_input(INPUT_GET,'pizza_size',FILTER_VALIDATE_INT);
        $room = filter_input(INPUT_GET,'room',FILTER_VALIDATE_INT);
        $n = filter_input(INPUT_GET,'n',FILTER_VALIDATE_INT);
        $stock = get_inventory($db);
        try {
        $current_day = get_current_day($db);
        } catch (PDOException $e) {
            $error_message = $e->getMessage(); 
            include('../errors/database_error.php');
            exit();
        }
    $status = 1;
    $topping_ids = filter_input(INPUT_GET, 'pizza_topping', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    
    // exception for low inventory
    
     
    if ($size_id == NULL || $size_id == FALSE || $topping_ids == NULL) {
        $error = "Invalid size or topping data." . "$size_id" . " $room";
        include('../errors/error.php');
        } 
        try {
            if ($stock[0]['quantity'] > $n || $stock[1]['quantity'] > $n )
    {
        
            for ($i=0; $i<$n; $i++) {  
               add_order($db,$room,$size_id,$current_day,$status, $topping_ids);
             }
            reduce_inventory($db,$n);
    }
    else{
        echo "Low Inventory Please Try Later";
        exit();
    }
        } catch (PDOException $e) {
            
            $error_message = $e->getMessage(); 
            include('../errors/database_error.php');
            exit();
        }
        header("Location: .?room=$room");
    } 
    elseif ($action=='update_order_status') {
        try {
        update_to_finished($db, $room);
        } catch (PDOException $e) {
            $error_message = $e->getMessage(); 
            include('../errors/database_error.php');
             exit();
        }
        header("Location: .?room=$room");
}
?>
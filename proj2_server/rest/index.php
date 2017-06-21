<?php
$request_uri = $_SERVER['REQUEST_URI'];
$doc_root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
$dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
array_pop($dirs); // remove last element
$project_root = implode('/', $dirs) . '/';
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0'); // would mess up response
ini_set('log_errors', 1);
// the following file needs to exist, be accessible to apache
// and writable (chmod 777 php-server-errors.log)
ini_set('error_log', $project_root . 'php-server-errors.log');
set_include_path($project_root);
// app_path is the part of $project_root past $doc_root
$app_path = substr($project_root, strlen($doc_root));
// project uri is the part of $request_uri past $app_path, not counting its last /
$project_uri = substr($request_uri, strlen($app_path) - 1);
$parts = explode('/', $project_uri);
// like  /rest/product/1 ;
//     0    1     2    3    

require_once('model/database.php');
require_once('model/product_db.php');
require('model/day.php');
require('model/order_db.php');
$server = $_SERVER['HTTP_HOST'];
$method = $_SERVER['REQUEST_METHOD'];
$proto = isset($_SERVER['HTTPS']) ? 'https:' : 'http:';
$url = $proto . '//' . $server . $request_uri;
$resource = trim($parts[2]);
$id = $parts[3];
error_log('starting REST server request, method=' . $method . ', uri = ...'. $project_uri);

switch ($resource) {
    // Access the specified product
    case 'products':
        error_log('request at case product');
        switch ($method) {
            case 'GET':
                handle_get_product($id);
                break;
            case 'POST':
                handle_post_product($url);
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
    case 'orders':
        error_log('request at case orders');
        switch ($method) {
            case 'GET':
                $currentday = get_currentday($db);
                if(isset($id)){
                handle_get_orders($id,$currentday);}
                else{handle_get_all_orders();}
                break;
            case 'POST':
                $currentday = get_currentday($db);
                handle_post_orders($url,$currentday);
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
    case 'day':
        error_log('request at case day');
        switch ($method) {
            case 'GET':
                // TODO: get current day from DB
                $day2 = get_currentday($db);
                handle_get_day($day2);
                break;
            case 'POST':
                $newday = handle_post_day();
                //TODO: set new day in DB
                changeday($db,$newday);
                
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
    default:
        $error_message = 'Unknown REST resource: ' . $resource;
        include_once('errors/server_error.php');
        server_error(400, $error_message);
        break;
    
}

function handle_get_product($product_id) {
    $product = get_product($product_id);
    $data = json_encode($product);
    error_log('hi from handle_get_product');
    echo $data;
}
function handle_get_orders($order_id,$currentday) 
{   $today = $currentday;
    $order = get_order($order_id);
    $orderitem = get_order_items($order_id);
   if($order['deliveryDay'] <= $today)
       {$d = true;} 
       else{$d = false;}

    $body1 = array('customerID'=>$order['customerID'],
        'orderID' => $order['orderID'], 'delivered' => $d);
    $items = array();
    foreach ($orderitem as $value1){
        $item = array('productID' => $value1['productID'], 'quantity' => $value1['quantity']);
        array_push($items, $item);
    }
    $orderdata = array($body1,$items);
    $data = json_encode($orderdata);
    error_log('hi from handle_get_product');
    echo $data;
}
 function handle_get_all_orders() 
{
    $allorders = get_all_orders();
    $data = json_encode($allorders);
    error_log('handle_all_orders');
    echo $data;
}

function handle_post_product($url) {
    $bodyJson = file_get_contents('php://input');
    error_log('Server saw post data' . $bodyJson);
    $body = json_decode($bodyJson, true);
    try {
        $product_id = add_product($body['categoryID'], $body['productCode'], 
                $body['productName'], $body['description'], $body['listPrice'],
                $body['discountPercent']);
        // return new URI in Location header
        $locHeader = 'Location: ' . $url . $product_id;
        header($locHeader, true, 201);  // needs 3 args to set code 201 
        error_log('hi from handle_post_product, header = ' . $locHeader);
    } catch (PDOException $e) {
        $error_message = 'Insert failed: ' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(400, $error_message);
    }
}
function handle_post_orders($url,$currentday) {
    //$today = get_currentday($db);
    $today = $currentday;
    try {
        $bodyJson = file_get_contents('php://input');
        error_log('Server saw post data' . $bodyJson);
        $body = json_decode($bodyJson, true);
         
           error_log('today'.$today);
           $date = date("y-m-d H:i:s");
           $deliveryday = rand(($today+1),($today+2));
           $orderID = add_order($body['customerID'], $date, $deliveryday);
           foreach ($body[1] as $value){
               $product = get_product($value['productID']);
               add_order_item($orderID, $value['product_id'], $product['listPrice'], $product['discountPercent'], $value['quantity']);
            }
           
        // return new URI in Location header
        $locHeader = 'Location: ' . $url . $orderID;
        header($locHeader, true, 201);  // needs 3 args to set code 201 
        error_log('hi from handle_post_product, header = ' . $locHeader);
    } catch (PDOException $e) {
        $error_message = 'Insert failed: ' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(400, $error_message);
    }
}

function handle_get_day($day) {
    error_log('rest server in handle_get_day, day = ' . $day);
    echo $day;
}

function handle_post_day() {
    error_log('rest server in handle_post_day');
    $day = file_get_contents('php://input');  // just a digit string
    error_log('Server saw POSTed day = ' . $day);
    return $day;
}
// as in main.php
//function display_db_error($error_message) {
  //  include_once('errors/server_error.php');
    //server_error(400, $error_message);
    //exit;
//}

// define this error function for server use (used in product_db.php, etc.)
// The error message is put in the server log
function display_db_error($error_message) {
    include_once('errors/server_error.php');
    server_error(500, $error_message);
    exit;
}

?>
<?php

// Client-side REST, for pizza2 project
// Now have $app_path, path from doc root to parent directory
// $app_path is of form /cs637/user/proj2/pizza2
// We want URL say http://topcat.cs.umb.edu/cs637/user/proj2/proj2_server/rest for REST service
// So drop "pizza2" from $app_path, add /proj2_server/rest
$spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';

function client_get_product($url, &$error_message) {
    $productJson = curl_request($url, 'GET', null, 'application/json');

    // decode to assoc array, what is normally used for $product in this app
    // without the flag, we would get "stdObject"
    $product = json_decode($productJson, /* assoc */ true);
    return $product;
}

// POST to /product/, get back new relative URI in $location, return new full URI
function client_post_product($product, &$error_message) {
    global $base_url;
    $data_string = json_encode($product);
    $location = null;

    curl_request_get_location($base_url . '/products/', 'POST', $data_string, 'application/json', $location);

    return ($location);
}

function client_get_day($url, &$error_message) {
    // works without specifying JSON since no type check on receipt
    $day = curl_request($url, 'GET', null, 'application/json');
    $error_message = null;
    return $day;
}

// POST to /day, no new Location to report back
function client_post_day($day, &$error_message) {
    global $base_url;
    $location = null;
    curl_request_get_location($base_url . '/day/', 'POST', $day, 'application/json', $location);
    $error_message = null;
}

// POST to /orders/, get back new relative URI in $location, return new full URI
function client_post_order($order, &$error_message) {
    global $base_url;
    $url = $base_url . '/orders/';
    $data_string = json_encode($order);
    $location = null;
    curl_request_get_location($url, 'POST', $data_string, 'application/json', $location);
    // echo '<br>Location = ' . $location;
    error_log('Location: ' . $location);
    return ($location);
}

function client_get_order($orderID, &$error_message) {
    global $base_url;
    $url = $base_url . '/orders/' . $orderID;
    // works without specifying JSON since no type check on receipt
    $orderJson = curl_request($url, 'GET', null, 'application/json');
    // decode to assoc array, what is normally used for $product in this app
    // without the flag, we would get "stdObject"
    $order = json_decode($orderJson, /* assoc */ true);
    return $order;
}

function client_get_order_by_location($location) {
    error_log('Location in order get by location: ' . $location);
    // works without specifying JSON since no type check on receipt
    curl_request_get_location($location, 'POST', null, 'application/json', $orderJson);
    //$orderJson = curl_request($location, 'GET', null, 'application/json');
    // decode to assoc array, what is normally used for $product in this app
    // without the flag, we would get "stdObject"
    $order = json_decode($orderJson, /* assoc */ true);

    return $order;
}

<?php
require('../../util/main.php');
require('../../model/database.php');
require('../../model/size_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'list_sizes';
    }
}
if ($action == 'list_sizes') {
  try {
    $sizes = get_available_sizes($db);
    include('size_list.php');
  } catch (PDOException $e) {
    $error_message = $e->getMessage(); 
    include('../../errors/database_error.php');
    exit();
  }
} else if ($action == 'delete_size') {
    $size_id = filter_input(INPUT_POST, 'size_id', 
            FILTER_VALIDATE_INT);
     if ($size_id == NULL || $size_id == FALSE) {
        $error = "Missing or incorrect size.";
        include('../errors/error.php');
    } else {
        try {
            delete_size($db, $size_id);
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            include('../errors/database_error.php');
            exit();
        }
        header("Location: .");
    } 
} else if ($action == 'show_add_form') {
    include('size_add.php');    
} else if ($action == 'add_size') {
    $size_name = filter_input(INPUT_POST, 'size_name');
    if ($size_name == NULL || $size_name == FALSE) {
        $error = "Invalid product data. Check all fields and try again.";
        include('../../errors/error.php');
    } 
    else { 
      try {
        add_size($db, $size_name);
      } catch (PDOException $e) {
      $error_message = $e->getMessage(); 
      include('../../errors/database_error.php');
      exit();
    }
        header("Location: .");
    }
}    
?>
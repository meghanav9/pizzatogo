<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function get_inventory($db)
{
    $query = 'SELECT * FROM inventory';    
    $statement = $db->prepare($query);
    $statement->execute();    
    $stock = $statement->fetchAll();
    $statement->closeCursor();    
    return $stock;
}
function reduce_inventory($db,$qty) {
    $query = 'UPDATE inventory SET quantity = quantity -:qty';    
    $statement = $db->prepare($query); 
    $statement->bindValue(':qty',$qty);
    $statement->execute();    
   // $currentday = $statement->fetch();
    $statement->closeCursor();    
   }
   function increase_inventory_flour($db,$f_qty) {
    $query = 'UPDATE inventory SET quantity = quantity +:f_qty where productid = 11';    
    $statement = $db->prepare($query);
    $statement->bindValue(':f_qty',$f_qty);
    $statement->execute();    
    $statement->closeCursor();
   }
   function increase_inventory_cheese($db,$c_qty) {
    $query = 'UPDATE inventory SET quantity = quantity +:c_qty where productid = 12';    
    $statement = $db->prepare($query);
    $statement->bindValue(':c_qty',$c_qty);
    $statement->execute();    
    $statement->closeCursor();    
   }
function add_orders($db,$orderid,$f_qty,$c_qty){
    $query = 'INSERT INTO undelivered_orders(orderid,flour_qty,cheese_qty) '
           . 'VALUES (:orderid,:f_qty,:c_qty)';
    $statement = $db->prepare($query);
    $statement->bindValue(':orderid',$orderid);
    $statement->bindValue(':f_qty',$f_qty);
    $statement->bindValue(':c_qty',$c_qty);
    $statement->execute();    
    $statement->closeCursor();
}
function get_undelivered($db){

    $query = 'SELECT * FROM undelivered_orders';    
    $statement = $db->prepare($query);
    $statement->execute();    
    $un_oreder = $statement->fetchAll();
    $statement->closeCursor();    
    return $un_oreder;
}

function remove_undelivereddata($db,$orderid){
    $query = 'Delete FROM undelivered_orders where orderid = :orderid';    
    $statement = $db->prepare($query);
    $statement->bindValue(':orderid',$orderid);
    $statement->execute();    
    $statement->closeCursor();    
  }
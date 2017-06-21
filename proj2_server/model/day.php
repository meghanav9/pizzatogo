<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_currentday($db) {
    $query = 'SELECT * FROM systemday';
    $statement = $db->prepare($query);
    $statement->execute();
    $daytab = $statement->fetchAll();
    $cur_day = $daytab[0]['dayNumber'];
    return $cur_day; 
    }    
    function changeday($db,$newday) {
    $query = 'update systemday set dayNumber = :newday';
    $statement = $db->prepare($query);
    $statement->bindvalue(':newday',$newday);
    $statement->execute();
    $statement->closecursor();
    }
    ?>
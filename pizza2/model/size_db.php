<?php
// the try/catch for these actions is in the caller, index.php

function add_size($db, $size_name)  
{
    $query = 'INSERT INTO pizza_size
                 (size_name)
              VALUES
                 (:size_name)';
    $statement = $db->prepare($query);
    $statement->bindValue(':size_name', $size_name);
    $statement->execute();
    $statement->closeCursor();
}

function delete_size($db, $size_id)  
{
    $query = 'DELETE FROM pizza_size
                 WHERE id = :size_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':size_id', $size_id);
    $statement->execute();
    $statement->closeCursor();
}

function get_available_sizes($db) {
    $query = 'SELECT id, size_name FROM pizza_size';
    $statement = $db->prepare($query);
    $statement->execute();
    $sizes = $statement->fetchAll();
    return $sizes;    
}

?>
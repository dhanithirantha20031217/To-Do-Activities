<?php

$host = 'sql209.infinityfree.com'; 
$dbname = 'if0_43364463_To_Do_Activities'; 
$username = 'if0_43364463'; 
$password = 'Di20031217';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} 

catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
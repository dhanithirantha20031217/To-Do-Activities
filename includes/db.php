<?php

$host = 'sql209.infinityfree.com'; 
$dbname = 'if0_43364463_To_Do_Activities'; 
$username = 'if0_43364463'; 
$password = 'Di20031217';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("SET time_zone = '+05:30'");
} 

catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
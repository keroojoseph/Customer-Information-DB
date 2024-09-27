<?php

$connection = null;

try {
    $connection = new PDO('mysql:host=model.com;dbname=estore;port=3306;charset=utf8mb4', 'root', 'Keroo@30311152404778', array
    (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    echo('Sorry, something went wrong.');
}
<?php
$username = "postgres";
$password = "caincay";

try {
    $dbcon = new PDO("pgsql:host=localhost;port=5432;dbname=expenses_db", $username, $password);
    $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbcon->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbcon->exec("SET search_path TO expenses");

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
<?php
$host = "db_auth";
$db = "auth";
$user = "user1";
$password = "#@B5d1be";
$opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
);

try {

    $pdo = new PDO("mysql:host=${host};dbname=${db};charset=utf8mb4", "$user", "$password", $opt);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

function sqlQuery($sql, $val)
{
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->execute($val);
    return $query->fetch();
}

<?php
$host = "db_service";
$db = "polytech_absence_service";
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



function sqlInsert($sql, $val)
{
    global $pdo;
    $query = $pdo->prepare($sql);
    return $query->execute($val);
}

function sqlUpdate($sql, $val)
{
   return sqlInsert($sql, $val);
}

function sqlDelete($sql, $val)
{
   return sqlInsert($sql, $val);
}

function sqlQuery($sql, $val)
{
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->execute($val);
    return $query->fetch();
}

function sqlQueryAll($sql, $val)
{
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->execute($val);
    return $query->fetchAll();
}

function sqlStart()
{
    global $pdo;
    $pdo->beginTransaction();
}

function sqlCommit()
{
    global $pdo;
    $pdo->commit();
}

function sqlCancel()
{
    global $pdo;
    $pdo->rollBack();
}

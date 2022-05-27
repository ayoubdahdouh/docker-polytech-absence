<?php
session_start();

require_once('db_service.php');
require_once('helper.php');

$id_u = "s" . $_POST["id_u"];
$res = ["id_u" => $_POST["id_u"], "login" => $_POST["login"]];
$sql = "select email, role, etat, prenom, nom from utilisateur where id_u=?";
$res2 = sqlQuery($sql, [$_POST["id_u"]]);

$_SESSION[$id_u]["user"] = array_merge($res, $res2);

sendMessage($_SESSION[$id_u]);

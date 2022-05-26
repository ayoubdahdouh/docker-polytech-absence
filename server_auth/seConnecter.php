<?php
session_start();
require_once('connexiondb.php');


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$login = isset($_POST['login']) ? test_input($_POST['login']) : "";
$password = isset($_POST['password']) ? test_input($_POST['password']) : "";

if (empty($login) || empty($password)) {
    $_SESSION['erreurLogin'] = "Login ou mot de passe incorrecte.";
    header("location:login.php");
}

$sql = "select id_u, login, email, role, etat, prenom, nom from utilisateur where login=? and password=?";
// $statement = $pdo->prepare($query);
// $exec = $statement->execute(array());
$user = sqlQuery($sql, [$login, $password]);
if (!empty($user)) {
    if ($user['etat']) {
        $_SESSION['user'] = $user;
        header("location:${home}/index.php");
    } else {
        $_SESSION['erreurLogin'] = "Votre compte est désactivé.<br> Veuillez contacter l'administrateur";
        header("location:login.php");
    }
} else {
    $_SESSION['erreurLogin'] = "Login ou mot de passe incorrecte.";
    header("location:login.php");
}

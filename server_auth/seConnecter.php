<?php
// session_start();
require_once('db_auth.php');
require_once('helper.php');

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
    $errorMessage = "Login ou mot de passe incorrecte.";
    // header("location:login.php");
}

// $sql = "select id_u, login, email, role, etat, prenom, nom from comptes where login=? and password=?";
$sql = "select id_u, login from comptes where login=? and password=?";

$user = sqlQuery($sql, [$login, $password]);
if (!empty($user)) {
    // if ($user['etat']) {
        // $_SESSION['user'] = $user;
        $successMessage = $user;
        // header("location:${home}/index.php");
    // } else {
        // $errorMessage = "Votre compte est désactivé. Veuillez contacter l'administrateur";
        // header("location:login.php");
    // }
} else {
    $errorMessage = "Login ou mot de passe incorrecte.";
    // header("location:login.php");
}

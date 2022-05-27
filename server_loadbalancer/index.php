<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("helper.php");

if (!isset($_SESSION['user'])) {
    $out = _post("server_auth/login.php");
    if (isJson($out)) {
        $res = json_decode($out, true);
        $_POST["id_u"] = $res["data"]["id_u"];
        $_POST["login"] = $res["data"]["login"];

        $out = _post("server_service/setSession.php");
        $res2 = json_decode($out, true);
        $_SESSION = $res2["data"];
    } else {
        echo $out;
    }
}

if (isset($_SESSION["user"])) {
    $_POST["session_id"] = "s" . $_SESSION["user"]["id_u"];
    if (!empty($_GET)) {
        if (strcmp($_GET["req"], "etudiant") == 0) {
            echo _post("server_service/etudiant.php");
        } elseif (strcmp($_GET["req"], "rechercherCours") == 0) {
            echo _post("server_service/rechercherCours.php");
        } elseif (strcmp($_GET["req"], "rechercherEtudiant") == 0) {
            echo _post("server_service/rechercherEtudiant.php");
        } elseif (strcmp($_GET["req"], "gestionUtilisateurs") == 0) {
            echo _post("server_service/gestionUtilisateurs.php");
        } elseif (strcmp($_GET["req"], "notification") == 0) {
            echo _post("server_service/notification.php");
        } elseif (strcmp($_GET["req"], "profile") == 0) {
            echo _post("server_service/profile.php");
        } elseif (strcmp($_GET["req"], "seDeconnecter") == 0) {
            _post("server_service/seDeconnecter.php");
            session_destroy();
            header("Location: /");
        } else {
            echo "404 PAGE NOT FOUND";
        }
    } else {
        // page par default
        if ($_SESSION['user']['role'] == 'e') {
            echo _post("server_service/etudiant.php");
        } else {
            echo _post("server_service/rechercherCours.php");
        }
    }
}

// if (isset($_POST["req"])) {
    // $p = explode("_", $_POST["req"]);
    // if (strcmp($p[0], "login") == 0) {
    //     modify_post($p);
    //     $out = _post("auth_server/login.php");
    //     if (isJson($out)) {
    //         $res = json_decode($out, true);
    //         echo "successful login...welcome " . $res["data"]["login"];
    //         $_SESSION["login"] = $res["data"]["login"];
    //     } else {
    //         echo $out;
    //     }
    // } elseif (strcmp($p[0], "signup") == 0) {
    //     modify_post($p);
    //     echo _post("auth_server/signup.php");
    // }
// }

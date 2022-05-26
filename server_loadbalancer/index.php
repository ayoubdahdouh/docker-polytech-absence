<?php
session_start();

require_once("helper.php");

if (!isset($_SESSION['user'])) {
    echo _post("server_auth/login.php");
} else {
    if ($_SESSION['user']['role'] == 'e') {
        echo _post("server_servicee/etudiant.php");
    } else {
        echo _post("server_service/rechercher_cours.php");
    }
}

// if (isset($_POST["req"])) {
//     $p = explode("_", $_POST["req"]);
//     if (strcmp($p[0], "login") == 0) {
//         modify_post($p);
//         $out = _post("auth_server/login.php");
//         if (isJson($out)) {
//             $res = json_decode($out, true);
//             echo "successful login...welcome " . $res["data"]["login"];
//             $_SESSION["login"] = $res["data"]["login"];
//         } else {
//             echo $out;
//         }
//     } elseif (strcmp($p[0], "signup") == 0) {
//         modify_post($p);
//         echo _post("auth_server/signup.php");
//     }
// }

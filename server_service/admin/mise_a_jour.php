<?php
$absents  = isset($_POST['absents']) ? $_POST['absents'] : [];
$nbr_absents = count($absents);

$sql = "SELECT id_e FROM historique WHERE id_a=?";
$liste_etudiants = [];
foreach (sqlQueryAll($sql, [$_SESSION[$s]["rc"]["id_a"]]) as $i => $etd) {
    $liste_etudiants[$i] = $etd['id_e'];
}

try {
    sqlStart();

    $sql = "INSERT INTO historique (id_e, id_a, justificatif) VALUES (?, ?, NULL)";
    foreach ($absents as $etd) {
        if (!in_array($etd, $liste_etudiants)) {
            sqlInsert($sql, [$etd, $_SESSION[$s]["rc"]["id_a"]]);
        }
    }

    $sql = "DELETE FROM historique WHERE id_e=? and id_a=?";
    foreach ($liste_etudiants as $etd) {
        if (!in_array($etd, $absents)) {
            sqlInsert($sql, [$etd, $_SESSION[$s]["rc"]["id_a"]]);
        }
    }

    sqlCommit();
    $successMessage = "Les absents sont bien mis à jour";
} catch (Exception $e) {
    sqlCancel();
    $errorMessage = "Une erreur s'est produite, veuillez réessayer";
}


if (!empty($successMessage) || !empty($errorMessage)) {
    unset($_SESSION[$s]["rc"]["date"]);
    unset($_SESSION[$s]["rc"]["heure"]);
    unset($_SESSION[$s]["rc"]["minutes"]);
    unset($_SESSION[$s]["rc"]["matrix"]);
    unset($_SESSION[$s]["rc"]["id_a"]);
}

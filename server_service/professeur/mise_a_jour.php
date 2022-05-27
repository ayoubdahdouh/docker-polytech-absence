<?php
$warningMessage = "";
$errorMessage = "";
$successMessage = "";

$id_a = isset($_POST['id_a']) ? intval($_POST['id_a']) : "";
$absents  = isset($_POST['absents']) ? $_POST['absents'] : [];
$nbr_absents = count($absents);

$sql = "SELECT id_e FROM historique WHERE id_a=?";
$liste_etudiants = [];
foreach (sqlQueryAll($sql, [$id_a]) as $i => $etd) {
    $liste_etudiants[$i] = $etd['id_e'];
}

try {
    sqlStart();

    $sql = "INSERT INTO historique (id_e, id_a, justificatif) VALUES (?, ?, NULL)";
    foreach ($absents as $etd) {
        if (!in_array($etd, $liste_etudiants)) {
            sqlInsert($sql, [$etd, $id_a]);
        }
    }

    $sql = "DELETE FROM historique WHERE id_e=? and id_a=?";
    foreach ($liste_etudiants as $etd) {
        if (!in_array($etd, $absents)) {
            sqlInsert($sql, [$etd, $id_a]);
        }
    }

    sqlCommit();
    $successMessage = "Les absents sont bien mis à jour";
} catch (Exception $e) {
    sqlCancel();
    $errorMessage = "Une est survenue, merci de réesayer, si ce n'est pas la premiere essaye, Vueillez contacter la scolarité svp!";
}

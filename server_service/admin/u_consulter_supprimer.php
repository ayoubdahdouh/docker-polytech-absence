<?php
$id_a = intval($_POST["supprimer_absence"]);
$id_e = $_SESSION[$s]['re']["etudiant"]["id_u"];
$sql = "SELECT justificatif FROM historique WHERE id_a=? AND id_e=?";
$justificatif = sqlQuery($sql, [$id_a, $id_e]);

function delete_file($file)
{
    $files = glob("../justificatifs/$file.*");

    if (count($files) > 0) {
        return unlink($files[0]);
    }
    return false;
}

$sql = "DELETE FROM historique WHERE id_a=? AND id_e=?";
if (sqlDelete($sql, [$id_a, $id_e])) {
    $sql = "UPDATE absence SET nbr_absents = nbr_absents - 1 WHERE id_a=?";
    if (sqlUpdate($sql, [$id_a])) {
        $successMessage = "L'absence est bien supprimée";
        $id_c = 0;
        $_POST["consulter"] = $_SESSION[$s]['re']["etudiant"]["id_u"];
        unset($_POST["supprimer_absence"]);
    } else {
        $errorMessage = "Une erreur s'est produite, veuillez réessayer";
    }
} else {
    $errorMessage = "Une erreur s'est produite, veuillez réessayer";
}

if (empty($errorMessage)) {
    if (!empty($justificatif)) {
        $sql = "DELETE FROM justificatif WHERE id_j=?";
        if (sqlDelete($sql, [$justificatif["justificatif"]])) {
            // remove file from justificatifs/
            $file =  "../justificatifs/" . $id_a . "_" . $id_e;
            if (!delete_file($file)) {
                $errorMessage = "Une erreur s'est produite, veuillez réessayer";
            }
        } else {
            $errorMessage = "Une erreur s'est produite, veuillez réessayer";
        }
    }
}

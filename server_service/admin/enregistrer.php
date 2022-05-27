<?php

$date = isset($_POST['date']) ? $_POST['date'] : "";
$heure  = isset($_POST['heure']) ? trim($_POST['heure']) : "";
$minutes  = isset($_POST['minutes']) ? trim($_POST['minutes']) : "";
$absents  = isset($_POST['absents']) ? $_POST['absents'] : [];
$nbr_absents = count($absents);

if (empty($date) || $heure < 0 || $heure > 23 || $minutes < 0 || $minutes > 59) {
    $warningMessage = "Veuillez sélectionner la date, l'heure et les minutes correctement svp !";
} else {
    $sql = "select id_n from enseignement where id_c=? and type=? and groupe=?";
    $res = sqlQuery($sql, [$_SESSION[$s]["rc"]["id_c"], $_SESSION[$s]["rc"]["type"], $_SESSION[$s]["rc"]["groupe"]]);
    $en_id_n = $res['id_n'];

    $sql = "select max(id_a) as id FROM absence";
    $res = sqlQuery($sql, null);
    if ($res['id']) {
        $ab_max_id_a = $res['id'] + 1;
    } else {
        $ab_max_id_a =  1;
    }

    // insert in to absence
    $sql = "SELECT id_a FROM absence WHERE id_n=? AND date_heure=?";
    $res = sqlQueryAll($sql, [$en_id_n, "$date $heure:$minutes:00"]);

    if (count($res) == 0) {
        $sql = "INSERT INTO absence (id_a, id_n, date_heure, nbr_absents, auteur) VALUES (?, ?, ?, ?, ?)";
        $res = sqlInsert($sql, [$ab_max_id_a, $en_id_n, "$date $heure:$minutes:00", $nbr_absents, $_SESSION[$s]["user"]["id_u"]]);

        if ($res) {
            $sql = "INSERT INTO historique (id_e, id_a, justificatif) VALUES (?, ?, NULL)";
            foreach ($absents as $abs) {
                $res = sqlInsert($sql, [$abs, $ab_max_id_a]);
                if (!$res) {
                    $errorMessage = "Une erreur s'est produite, veuillez réessayer";
                }
            }
            if (empty($errorMessage)) {
                $successMessage = "Les absents sont bien enregistrés.";
            }
        } else {
            $errorMessage = "Une erreur s'est produite, veuillez réessayer";
        }
    } else {
        $errorMessage = "Un enregistrement d'absence existe déjà pour le même cours, la même date, le même type et le même groupe.";
    }
}

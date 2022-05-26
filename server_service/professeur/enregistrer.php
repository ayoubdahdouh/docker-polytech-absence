<?php
$warningMessage = "";
$errorMessage = "";
$successMessage = "";

$cours_nom = isset($_POST['cours_nom']) ? trim($_POST['cours_nom']) : "";
$cours_id_c = isset($_POST['cours_id_c']) ? trim($_POST['cours_id_c']) : "";
$type  = isset($_POST['type']) ? trim($_POST['type']) : "";
$groupe  = isset($_POST['groupe']) ? trim($_POST['groupe']) : "";
$date = isset($_POST['date']) ? $_POST['date'] : "";
$heure  = isset($_POST['heure']) ? trim($_POST['heure']) : "";
$minutes  = isset($_POST['minutes']) ? trim($_POST['minutes']) : "";
$absents  = isset($_POST['absents']) ? $_POST['absents'] : [];
$nbr_absents = count($absents);

// $timestamp = strtotime("$date $heure:$minutes:00");
// print_r(["timestamp" => $timestamp, "curre"=>time()]);


if (empty($date) || $heure < 0 || $heure > 23 || $minutes < 0 || $minutes > 59) {
    $warningMessage = "Veuillez sélectionner la date, l'heure et les minutes correctement svp !";
} else {
    $sql = "select id_n from enseignement where id_p=? and id_c=? and type=? and groupe=?";
    $res = sqlQuery($sql, [$_SESSION['user']['id_u'], $cours_id_c, $type, $groupe]);
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
        $res = sqlInsert($sql, [$ab_max_id_a, $en_id_n, "$date $heure:$minutes:00", $nbr_absents, $_SESSION["user"]["id_u"]]);

        if ($res) {
            $sql = "INSERT INTO historique (id_e, id_a, justificatif) VALUES (?, ?, NULL)";
            foreach ($absents as $abs) {
                $res = sqlInsert($sql, [$abs, $ab_max_id_a]);
                if (!$res) {
                    $errorMessage = "Une est survenue, merci de réesayer, si ce n'est pas la premiere essaye, Vueillez contacter la scolarité svp!";
                }
            }
            if (empty($errorMessage)) {
                $successMessage = "Les absents sont bien enrigistrés.";
            }
        } else {
            $errorMessage = "Une est survenue, merci de réesayer, si ce n'est pas la premiere essaye, Vueillez contacter la scolarité svp!";
        }
    } else {
        $errorMessage = "Un enrigistrement d'absence déja éxiste avec le meme (cours, professeur, date, type de cours, et groupe)!";
    }
}

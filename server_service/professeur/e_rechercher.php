<?php
$errorMessage = "";
if (!empty($identifiant)) {
    $sql = "SELECT id_u, login, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE login=? AND role='e'";
    $liste_etudiant = [sqlQuery($sql, [$identifiant])];
    if (empty($liste_etudiant)) {
        $errorMessage = "Cet identifiant n'existe pas.";
    }
} elseif (empty($prenom) && empty($nom)) {
    $errorMessage = "Veuillez sélectionner l'identifiant, le prénom ou le nom svp !";
} else {
    if (!empty($prenom) && !empty($nom)) {
        $sql = "SELECT id_u FROM utilisateur WHERE role='e' AND prenom LIKE '%" . $prenom . "%' AND nom LIKE '%" . $nom . "%'";
        $res = sqlQueryAll($sql, NULL);
    } elseif (!empty($prenom)) {
        $sql = "SELECT id_u FROM utilisateur WHERE role='e' AND prenom LIKE '%" . $prenom . "%'";
        $res = sqlQueryAll($sql, NULL);
    } else {
        $sql = "SELECT id_u FROM utilisateur WHERE role='e' AND nom LIKE '%" . $nom . "%'";
        $res = sqlQueryAll($sql, NULL);
    }
    if (empty($res)) {
        $errorMessage = "Cet identifiant n'existe pas.";
    } else {
        $liste = [];
        foreach ($res as $i => $r) {
            $liste[$i] = $r["id_u"];
        }
        if ($_SESSION['user']["role"] == 'p') {
            // professeur
            $sql = "SELECT DISTINCT t1.id_u, login, CONCAT(t1.prenom, ' ', t1.nom) AS prenom_nom FROM utilisateur t1 " .
                "JOIN ametice t2 ON t1.id_u=t2.id_e " .
                "JOIN enseignement t3 ON t2.id_c=t3.id_c " .
                "WHERE t3.id_p=? AND t2.id_e IN  (" . implode(',', $liste) . ")";
            $liste_etudiant = sqlQueryAll($sql, [$_SESSION['user']["id_u"]]);
        } else {
            // admin
            $sql = "SELECT id_u,  login, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE id_u IN (" . implode(',', $liste) . ")";
            $liste_etudiant = sqlQueryAll($sql, NULL);
        }
    }
}

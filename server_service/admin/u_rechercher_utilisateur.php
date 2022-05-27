<?php
if (!empty($identifiant)) {
    // $sql = "SELECT DISTINCT id_u, login, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE login=?";
    $sql = "SELECT DISTINCT id_u, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE login=?";
    $liste_etudiant = [sqlQuery($sql, [$identifiant])];
    if (empty($liste_etudiant)) {
        $errorMessage = "Cet identifiant n'existe pas.";
    }
} elseif (empty($prenom) && empty($nom)) {
    $errorMessage = "Veuillez sélectionner l'identifiant, le prénom ou le nom svp !";
} else {
    if (!empty($prenom) && !empty($nom)) {
        $sql = "SELECT DISTINCT id_u FROM utilisateur WHERE prenom LIKE '%" . $prenom . "%' AND nom LIKE '%" . $nom . "%'";
        $res = sqlQueryAll($sql, NULL);
    } elseif (!empty($prenom)) {
        $sql = "SELECT DISTINCT id_u FROM utilisateur WHERE prenom LIKE '%" . $prenom . "%'";
        $res = sqlQueryAll($sql, NULL);
    } else {
        $sql = "SELECT DISTINCT id_u FROM utilisateur WHERE nom LIKE '%" . $nom . "%'";
        $res = sqlQueryAll($sql, NULL);
    }
    if (empty($res)) {
        $errorMessage = "Cet identifiant n'existe pas.";
    } else {
        $liste = [];
        foreach ($res as $i => $r) {
            $liste[$i] = $r["id_u"];
        }
        // $sql = "SELECT id_u, `role`, etat, `login`, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE id_u IN (" . implode(',', $liste) . ") ORDER BY role";
        $sql = "SELECT id_u, `role`, etat, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE id_u IN (" . implode(',', $liste) . ") ORDER BY role";
        $liste_etudiant = sqlQueryAll($sql, NULL);
    }
}

<?php
require_once('identifier.php');
require_once("connexiondb.php");

error_reporting(E_ALL);
ini_set('display_errors', 'on');

if (isset($_POST["rechercher"])) {
    $identifiant = isset($_POST['identifiant']) ? trim($_POST['identifiant']) : "";
    $prenom  = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
    $nom  = isset($_POST['nom']) ? trim($_POST['nom']) : "";

    if ($_SESSION['user']['role'] == 'p') {
        require_once("professeur/e_rechercher.php");
    } elseif ($_SESSION['user']['role'] == 'a') {
        require_once("professeur/e_rechercher.php");
    } else {
        $errorMessage = "Vous n'avez pas le droit d'accéder accéder à cette page.";
    }
} elseif (isset($_POST["consulter"])) {
    $id_c = intval($_POST["index"]);
} elseif (isset($_POST["voir"])) {
    $id_e = intval($_POST['voir']);

    $sql = "SELECT id_u, login, CONCAT(prenom, ' ', nom) AS prenom_nom FROM utilisateur WHERE role='e' AND id_u=?";
    $etudiant = sqlQuery($sql, [$id_e]);

    if (empty($etudiant)) {
        $errorMessage = "Cet identifiant n'existe pas.";
    } else {
        $sql = "SELECT DISTINCT t1.id_c, t1.nom FROM cours t1 JOIN ametice t2 ON t1.id_c=t2.id_c " .
            "JOIN enseignement t3 ON t1.id_c=t3.id_c WHERE t3.id_p=? and t2.id_e=?";
        $cours = sqlQueryAll($sql, [$_SESSION["user"]["id_u"], $etudiant['id_u']]);

        if (empty($cours)) {
            $errorMessage = "L'étudiant avec cet identifiant n'est pas inscrit à aucun de votre cours.";
        } else {
            $_SESSION['re']["cours"] = $cours;
            $_SESSION['re']["etudiant"] = $etudiant;
            $id_c = $cours[0]["id_c"];
        }
    }
}

if ((isset($_POST["voir"]) || isset($_POST["consulter"])) && empty($errorMessage)) {
    $sql = "SELECT groupe FROM etudiant WHERE id_e=?";
    $groupe = sqlQuery($sql, [$_SESSION['re']["etudiant"]["id_u"]]);

    $sql = "SELECT nom FROM cours WHERE id_c=?";
    $cm = sqlQuery($sql, [$id_c]);
    // id_n
    $sql = "SELECT DISTINCT id_n, type FROM enseignement WHERE id_p=? AND id_c=? AND groupe IN (?,0)";
    $res = sqlQueryAll($sql, [$_SESSION['user']['id_u'], $id_c, $groupe["groupe"]]);

    $liste_id_n = [];
    foreach ($res as $i => $r) {
        $liste_id_n[$i] = $r["id_n"];
    }
    // recherche toutes les absences
    $sql = "SELECT T1.id_a, T1.date_heure, T1.type, T2.id_e, T2.justificatif FROM " .
        "(SELECT t1.id_a, t1.date_heure, t2.type FROM absence t1 JOIN enseignement t2 ON t1.id_n=t2.id_n WHERE t1.id_n IN (" . implode(',', $liste_id_n) . ")) AS T1 " .
        "LEFT JOIN " .
        "(SELECT t1.id_a, t1.date_heure, t2.id_e, t2.justificatif FROM absence t1 JOIN historique t2 on t1.id_a=t2.id_a WHERE t1.id_n IN (" . implode(',', $liste_id_n) . ") AND t2.id_e=?) AS T2 " .
        "ON T1.id_a=T2.id_a ORDER BY T1.date_heure;";
    $matrix_abs = sqlQueryAll($sql, [$_SESSION['re']["etudiant"]["id_u"]]);

    if (count($matrix_abs) == 0) {
        $warningMessage = "Aucun d'enregistrment trouvé";
    }
}

?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Gestion des filières</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>
    <main class="container">
        <form method="POST" action="rechercher_etudiant.php">
            <section class="py-5 text-center">
                <div class="row py-lg-2">
                    <div class="col-lg-12 col-md-8 mx-auto">
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="identifiant" name="identifiant" placeholder="Identifiant de l'étudiant">
                                    <label for="identifiant">Identifiant</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom de l'étudiant">
                                    <label for="prenom">Prénom</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom de l'étudiant">
                                    <label for="nom">Nom</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-primary" name="rechercher" type="submit">Rechercher l'étudiant</button>
                        </div>

                    </div>
                </div>
            </section>
        </form>
        
        <div class="container">
            <?php
            if (isset($_POST["rechercher"]) || isset($_POST["voir"]) || isset($_POST["consulter"])) {
                echo "<hr>";

                if (!empty($errorMessage)) {
                    echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
                } elseif (isset($_POST["rechercher"])) {
            ?>
                    <form method="POST" action="rechercher_etudiant.php">
                        <table class="table table-hover table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Identifiant</th>
                                    <th>Prénom et Nom</th>
                                    <th class="text-center">Opérations</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($liste_etudiant as $etudiant) {
                                    // Identifiant
                                    echo "<tr><td>";
                                    echo $etudiant['login'];
                                    // Nom et prénom
                                    echo "</td><td>";
                                    echo $etudiant['prenom_nom'];
                                    // consulter
                                ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="submit" name="voir" value="<?php echo $etudiant['id_u']; ?>" class="btn btn-primary btn-sm">Consulter absences</button>
                                        <!-- <button type="submit" name="voir" value="<?php echo $etudiant['id_u']; ?>" class="btn btn-primary btn-sm">Voir profile</button> -->
                                    </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </form>
                <?php
                } else {
                ?>
                    <div class="card mt-3">
                        <h5 class="card-header">Résultat de recherche</h5>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="identifiant" name="identifiant" placeholder="Identifiant" value="<?php echo $_SESSION['re']["etudiant"]["login"]; ?>" readonly="readonly">
                                        <label for="identifiant">Identifiant</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="prenom_nom" name="prenom_nom" placeholder="Prénom Nom" value="<?php echo $_SESSION['re']["etudiant"]["prenom_nom"]; ?>" readonly="readonly">
                                        <label for="prenom_nom">Prénom Nom</label>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="rechercher_etudiant.php">
                                <div class="mt-3">
                                    <label for="historique" class="form-label">Cours: </label>
                                    <div class="input-group" id="historique">
                                        <select class="form-select" id="historique" name="index">
                                            <?php

                                            foreach ($_SESSION['re']["cours"] as $cours) {
                                                if ($abs["id_c"] ==    $id_c) {
                                                    echo "<option value=\"" . $cours["id_c"] . "\" selected>" . $cours['nom'] . "</option>\n";
                                                } else {
                                                    echo "<option value=\"" . $cours["id_c"] . "\">" . $cours['nom'] . "</option>\n";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="historique"></label>
                                        <button class="btn btn-primary" name="consulter" type="submit">Consulter</button>
                                        <!-- <button class="btn btn-secondary" name="modifier" type="submit">Modifier</button> -->
                                    </div>
                                </div>
                            </form>
                            <!--  -->
                        </div>
                    </div>
                    <?php
                    if (!empty($warningMessage)) {
                        echo "<div class=\"alert alert-warning mt-4\">" . $warningMessage . "</div>";
                    } else {
                    ?>
                        <table class="table table-hover table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Justificatif</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($matrix_abs as $abs) {
                                    // Nom et prénom
                                    echo "<tr><td>";
                                    echo $abs['date_heure'];

                                    // Absent(e)
                                    echo "</td><td>";
                                    switch ($abs['type']) {
                                        case 'cm':
                                            echo "CM";
                                            break;
                                        case 'td':
                                            echo "TD";
                                            break;
                                        case 'tp':
                                            echo "TP";
                                            break;

                                        default:
                                            echo "AUTRE";
                                            break;
                                    }

                                    // Justificatif
                                    echo "</td><td>";
                                    if ($abs["id_e"]) {
                                        if ($abs["justificatif"]) {
                                            echo "OUI";
                                        } else {
                                            echo "NON";
                                        }
                                    }
                                    echo "</td></tr>\n";
                                } ?>
                            </tbody>
                        </table>
            <?php
                    }
                }
            }
            ?>

        </div>
    </main>
</body>
<?php
include_once('footer.php');
?>

</HTML>
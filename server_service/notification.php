<?php
session_start();

require_once("db_service.php");
error_reporting(E_ALL);
ini_set('display_errors', 'on');

$s = $_POST["session_id"];

$successMessage = "";
$errorMessage = "";

if (isset($_POST["accepter"])) {
    $id_j = intval($_POST["accepter"]);
    $sql = "UPDATE justificatif SET status='a' WHERE id_j=?";
    if (sqlUpdate($sql, [$id_j])) {
        $sql = "DELETE FROM notification WHERE arg1=?";
        if (sqlUpdate($sql, [$id_j])) {
            $successMessage = "Le justificatif est accepté";;
        } else {
            $errorMessage = "Une erreur s'est produite, veuillez réessayer";
        }
    } else {
        $errorMessage = "Une erreur s'est produite, veuillez réessayer";
    }
} elseif (isset($_POST["refuser"])) {
    $id_j = intval($_POST["refuser"]);
    $sql = "SELECT * FROM justificatif WHERE id_j=?";
    $res = sqlQuery($sql, [$id_j]);
    if (!empty($res)) {
        if (!file_exists($res["justificatif"]) || unlink($res["justificatif"])) {
            $sql = "UPDATE justificatif SET status='r', justificatif=NULL WHERE id_j=?";
            if (sqlUpdate($sql, [$id_j])) {
                $sql = "DELETE FROM notification WHERE arg1=?";
                if (sqlUpdate($sql, [$id_j])) {
                    $successMessage = "Le justificatif est rejeté";
                } else {
                    $errorMessage = "Une erreur s'est produite, veuillez réessayer";
                }
            } else {
                $errorMessage = "Une erreur s'est produite, veuillez réessayer";
            }
        } else {
            $errorMessage = "Une erreur s'est produite, veuillez réessayer";
        }
    } else {
        $errorMessage = "Une erreur s'est produite, veuillez réessayer";
    }
}
$sql = "SELECT * FROM notification WHERE type='justificatif'";
$res = sqlQueryAll($sql, null);

if (empty($res)) {
    $warningMessage = "Aucune notification à afficher";
}
?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Notifications</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>
    <main class="container">
        <?php
        if (isset($warningMessage) && !empty($warningMessage)) {
            echo "<div class=\"alert alert-warning\" role=\"alert\">" . $warningMessage . "</div>";
        } else {
            if (isset($errorMessage) && !empty($errorMessage)) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">" . $errorMessage . "</div>";
            } elseif (isset($successMessage) && !empty($successMessage)) {
                echo "<div class=\"alert alert-success\" role=\"alert\">" . $successMessage . "</div>";
            }

        ?>
            <div class="card">
                <h5 class="card-header">Justificatif à valider</h5>
                <div class="card-body">
                    <table class="table table-hover table-bordered mt-4">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Identifiant</th>
                                <th>Prénom et Nom</th>
                                <th>Cours</th>
                                <th>Type</th>
                                <th>Opérations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <form method="POST" action="index.php?rqe=notification">
                                <?php
                                foreach ($res as $r) {
                                    $sql = "SELECT * FROM justificatif WHERE id_j=?";
                                    $just = sqlQuery($sql, [$r["arg1"]]);
                                    $sql = "SELECT T1.login, T1.prenom_nom, T2.type, T2.nom, T2.date_heure FROM " .
                                        "(SELECT t2.login, t1.id_e, t1.groupe, CONCAT(t2.prenom,' ', t2.nom) AS prenom_nom FROM etudiant t1 JOIN utilisateur t2 ON t1.id_e=t2.id_u WHERE id_e=?) AS T1 JOIN " .
                                        "(SELECT t4.id_e, t2.type, t3.nom, t1.date_heure FROM historique t4 JOIN absence t1 on t4.id_a=t1.id_a JOIN enseignement t2 ON t1.id_n=t2.id_n JOIN cours t3 ON t2.id_c=t3.id_c WHERE t1.id_a=?) AS T2 " .
                                        "WHERE T1.id_e=T2.id_e";
                                    $abs = sqlQuery($sql, [ $just["id_e"], $just["id_a"]]);
                                    echo "<tr><td>";
                                    echo $abs["date_heure"];
                                    echo "</td><td>";
                                    echo $abs["login"];
                                    echo "</td><td>";
                                    echo $abs["prenom_nom"];
                                    echo "</td><td>";
                                    echo $abs["nom"];
                                    echo "</td><td>";
                                    echo $abs["type"];
                                    echo "</td><td  class=\"text-center\">";
                                    echo "<a href=\" " . $just["justificatif"] . "\" target=\"_blank\" class=\"btn btn-primary btn-sm\">Visualiser</a> ";
                                    // echo "<button type=\"submit\" name=\"voir\" value=\"" . $just["id_j"] . "\" class=\"btn btn-primary btn-sm\">Visualiser</button> ";
                                    echo "<button type=\"submit\" name=\"accepter\" value=\"" . $just["id_j"] . "\" class=\"btn btn-success btn-sm\">Accepter</button> ";
                                    echo "<button type=\"submit\" name=\"refuser\" value=\"" . $just["id_j"] . "\" class=\"btn btn-danger btn-sm\">Refuser</button> ";
                                    echo "</td></tr>";
                                }
                                ?>
                            </form>
                            <!-- 
                                <form method="POST" action="notification.php">
                                <?php
                                // foreach ($res as $r) {
                                // $sql = "SELECT * FROM justificatif WHERE id_j=?";
                                // $just = sqlQuery($sql, [$r["arg1"]]);
                                // // $sql = "SELECT id_f,  annee, prenom_nom, nom, date_heure FROM ".
                                // // "(SELECT t1.id_e, id_f, annee, groupe, CONCAT(prenom,' ', nom) AS prenom_nom FROM etudiant t1 JOIN utilisateur t2 ON t1.id_e=t2.id_u WHERE id_e=?) AS T1 JOIN ".
                                // // "(SELECT t4.id_e, t1.id_a, nom, date_heure FROM historique t4 JOIN absence t1 on t4.id_a=t1.id_a JOIN enseignement t2 ON t1.id_n=t2.id_n JOIN cours t3 ON t2.id_c=t3.id_c WHERE id_a=?) AS T2 ".
                                // // "WHERE T1.id_e=T2.id_e";
                                // echo "<tr><td>";
                                // echo $just["id_a"];
                                // echo "</td><td>";
                                // echo $just["id_e"];
                                // echo "</td><td  class=\"text-center\">";
                                // echo "<a href=\" " . $just["justificatif"] . "\" target=\"_blank\" class=\"btn btn-primary btn-sm\">Visualiser</a> ";
                                // // echo "<button type=\"submit\" name=\"voir\" value=\"" . $just["id_j"] . "\" class=\"btn btn-primary btn-sm\">Visualiser</button> ";
                                // echo "<button type=\"submit\" name=\"accepter\" value=\"" . $just["id_j"] . "\" class=\"btn btn-success btn-sm\">Accepter</button> ";
                                // echo "<button type=\"submit\" name=\"refuser\" value=\"" . $just["id_j"] . "\" class=\"btn btn-danger btn-sm\">Refuser</button> ";
                                // echo "</td></tr>";
                                // }
                                ?>
                            </form>
                             -->
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
        ?>
        <!-- <div class="card">
            <h5 class="card-header">Justificatif à valider</h5>
            <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
                <hr>
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
                <hr>
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
                <hr>
            </div>
        </div>
        <div class="alert alert-primary" role="alert">
            Page indisponible
        </div> -->

    </main>
</body>
<?php
include_once('footer.php');
?>

</HTML>
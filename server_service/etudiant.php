<?php
session_start();

require_once('helper.php');
require_once("db_service.php");

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$s = $_POST["session_id"];

if (
    !isset($_POST["ajouter_justificatif"]) &&
    !isset($_POST["valider_justificatif"])
) {
    $warningMessage = "";

    if (isset($_POST["suivant"])) {
        $i = $_SESSION[$s]["index"];
        if ($i >= $_SESSION[$s]['max'] - 1) {
            $i = $_SESSION[$s]["index"] = $_SESSION[$s]["max"] - 1;
        } else {
            $i++;
            $_SESSION[$s]["index"] = $i;
        }
    } elseif (isset($_POST["precedent"])) {
        $i = $_SESSION[$s]["index"];
        if ($i <= 0) {
            $i = $_SESSION[$s]["index"] = 0;
        } else {
            $i--;
            $_SESSION[$s]["index"] = $i;
        }
    } else {
        $sql = "SELECT t2.date_heure, t2.id_a, t4.nom, t3.type, t1.justificatif, t5.status FROM historique t1 " .
            "JOIN absence t2 ON t1.id_a=t2.id_a " .
            "JOIN enseignement t3 ON t2.id_n=t3.id_n " .
            "JOIN cours t4 ON t3.id_c=t4.id_c " .
            "LEFT JOIN justificatif t5 ON t5.id_j=t1.justificatif " .
            "WHERE t1.id_e=? ORDER BY t2.date_heure";
        $matrix_abs = sqlQueryAll($sql, [$_SESSION[$s]["user"]["id_u"]]);

        if (empty($matrix_abs)) {
            $warningMessage = "Vous n'avez pas d'absence à afficher.";
        } else {
            $_SESSION[$s]["taille"] = 8;
            $_SESSION[$s]["matrix_abs"] = $matrix_abs;
            $_SESSION[$s]["matrix_len"] = count($matrix_abs);
            $_SESSION[$s]["max"] = ceil($_SESSION[$s]["matrix_len"] / $_SESSION[$s]["taille"]);
            $_SESSION[$s]["index"] = 0;
            $i = 0;
        }
    }
}
?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Consulter</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>
    <main class="container">
        <?php
        if (
            isset($_POST["ajouter_justificatif"]) ||
            isset($_POST["valider_justificatif"])
        ) {
            require_once("uploadJustificatif.php");
        } else {

            if (!empty($warningMessage)) {
                echo "<div class=\"alert alert-success mt-4\">" . $warningMessage . "</div>";
            } else {
        ?>
                <table class="table table-hover table-bordered mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Cours</th>
                            <th class="text-center" colspan="2">Justificatif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="index.php?req=etudiant" method="post" enctype="multipart/form-data">
                            <?php
                            for ($j = $i * $_SESSION[$s]["taille"]; ($j < ($i + 1) * $_SESSION[$s]["taille"]) && ($j < $_SESSION[$s]["matrix_len"]); $j++) {
                                // Nom et prénom
                                echo "<tr><td>";
                                echo $_SESSION[$s]["matrix_abs"][$j]['date_heure'];

                                // Absent(e)
                                echo "</td><td>";
                                switch ($_SESSION[$s]["matrix_abs"][$j]['type']) {
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
                                echo $_SESSION[$s]["matrix_abs"][$j]['nom'];

                                // Absent(e)
                                echo "</td><td>";
                                if ($_SESSION[$s]["matrix_abs"][$j]["justificatif"]) {
                                    if ($_SESSION[$s]["matrix_abs"][$j]["status"] == "e") {
                                        echo "EN COURS";
                                    } elseif ($_SESSION[$s]["matrix_abs"][$j]["status"] == "r") {
                                        echo "REFUSE";
                                    } else {
                                        echo "OUI";
                                    }
                                } else {
                                    echo "NON";
                                }
                            ?>
                                </td>
                                <td>
                                    <?php
                                    if (!$_SESSION[$s]["matrix_abs"][$j]["justificatif"] || $_SESSION[$s]["matrix_abs"][$j]["status"] == "r") {
                                    ?>
                                        <button type="submit" name="ajouter_justificatif" value="<?php echo $_SESSION[$s]["matrix_abs"][$j]["id_a"]; ?>" class="btn btn-primary btn-sm">Ajouter justificatif</button>
                                    <?php
                                    }
                                    ?>
                                </td>
                                </tr>
                            <?php
                            } ?>
                        </form>
                    </tbody>
                </table>
                <?php
                if ($_SESSION[$s]["max"] > 1) {
                ?>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <form action="index.php?req=etudiant" method="POST">
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item <?php echo ($i == 0) ? "disabled" : ""; ?>">
                                        <button class="page-link " type="submit" name="precedent">Précédente</button>
                                    </li>
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#"><?php echo ($i + 1) . " / " . $_SESSION[$s]["max"]; ?></a>
                                    </li>
                                    <li class="page-item <?php echo ($i  == $_SESSION[$s]["max"] - 1) ? "disabled" : ""; ?>">
                                        <button class="page-link" type="submit" name="suivant">Suivante</button>
                                    </li>
                                </ul>
                            </nav>
                        </form>
                    </div>
        <?php }
            }
        }
        // }
        ?>

    </main>
</body>
<?php
include_once('footer.php');
?>

</HTML>
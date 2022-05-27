<?php
session_start();


require_once("db_service.php");

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$s = $_POST["session_id"];


$errorMessage = "";
$successMessage = "";
$warningMessage = "";

?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Gestion Utilisateurs</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>

    <main class="container">
        <form method="POST" action="index.php?req=gestionUtilisateurs">
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
                            <button class="btn btn-primary" name="rechercher" type="submit">Rechercher Utilisateur</button>
                            <!-- <button class="btn btn-success" name="creer" type="submit">Créer Nouveau</button> -->
                        </div>

                    </div>
                </div>
            </section>
        </form>

        <?php
        if (
            isset($_POST["rechercher"]) ||
            isset($_POST["change"])
        ) {
            require_once("admin/u_rechercher.php");
        } elseif (isset($_POST["creer"])) {
            require_once("admin/u_creer.php");
        } elseif (isset($_POST["voir"])) {
            require_once("admin/u_voir.php");
        } elseif (
            isset($_POST["consulter"]) ||
            isset($_POST["consulter_autre"]) ||
            isset($_POST["supprimer_absence"])
        ) {
            require_once("admin/u_consulter.php");
        } elseif (
            isset($_POST["ajouter_justificatif"]) ||
            isset($_POST["valider_justificatif"])
        ) {
            require_once("uploadJustificatif.php");
        } elseif (
            isset($_POST["modifier"]) ||
            isset($_POST["modifier_enregister"])
        ) {
            require_once("admin/u_modifier.php");
        } elseif (
            isset($_POST["supprimer"]) ||
            isset($_POST["supprimer_confirmer"])
        ) {
            require_once("admin/u_supprimer.php");
        }
        ?>
    </main>
</body>
<?php
include_once('footer.php');
?>

</HTML>
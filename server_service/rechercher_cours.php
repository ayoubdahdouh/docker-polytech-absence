<?php
require_once('identifier.php');
require_once("connexiondb.php");
error_reporting(E_ALL);
ini_set('display_errors', 'on');

?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Recherche un cours</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>
    <main class="container">

        <?php
        echo '<section class="py-5 text-center">';

        if ($_SESSION['user']['role'] == 'p') {
            require_once("professeur/professeur.php");
        } elseif ($_SESSION['user']['role'] == 'a') {
            require_once("admin/admin.php");
        } else {
            echo "Vous n'avez pas le droit d'accéder accéder à cette page.";
        }
        echo '</section>';

        if (empty($errorMessage)) {
            if ($_SESSION['user']['role'] == 'p') {
                if (isset($_POST['ajouter']) || isset($_POST['enregistrer'])) {
                    require_once('professeur/ajouter.php');
                } elseif (isset($_POST['rechercher'])  || isset($_POST['consulter'])) {
                    require_once('professeur/rechercher.php');
                } elseif (isset($_POST['modifier']) || isset($_POST['mise_a_jour'])) {
                    require_once('professeur/modifier.php');
                }
            } elseif ($_SESSION['user']['role'] == 'a') {
                if (
                    isset($_POST['ajouter']) ||
                    isset($_POST['enregistrer'])
                ) {
                    require_once('admin/ajouter.php');
                } elseif (
                    isset($_POST['rechercher']) ||
                    isset($_POST['consulter'])
                ) {
                    require_once('admin/rechercher.php');
                } elseif (
                    isset($_POST['modifier']) ||
                    isset($_POST['mise_a_jour'])
                ) {
                    require_once('admin/modifier.php');
                }
            } else {
                echo "Vous n'avez pas le droit d'accéder accéder à cette page.";
            }
        }
        ?>
    </main>
</body>
<?php
include_once('footer.php');
?>

</HTML>
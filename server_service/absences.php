<?php
require_once('identifier.php');
require_once("connexiondb.php");
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

    <div class="container">
        <?php
        if ($_SESSION['user']['role'] == 'etudiant') {
            require_once("etudiant.php");
        } elseif ($_SESSION['user']['role'] == 'professeur') {
            require_once("professeur.php");
        } elseif ($_SESSION['user']['role'] == 'admin') {
            require_once("admin.php");
        } else {
            echo "Vous n'avez pas le droit d'accéder accéder à cette page.";
        }
        ?>
    </div>
</body>
<?php
include_once('footer.php');
?>

</HTML>
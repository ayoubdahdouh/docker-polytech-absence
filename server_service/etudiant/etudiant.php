<div class="panel panel-success margetop60">

    <div class="panel-heading">Rechercher des filières</div>
    <div class="panel-body">
        $_SESSION['id'];
        <form method="get" action="filieres.php" class="form-inline">

            <div class="form-group">

                <input type="text" name="nomF" placeholder="Nom de la filière" class="form-control" value="<?php echo $nomf ?>" />

            </div>

            <label for="niveau">Niveau:</label>
            <select name="niveau" class="form-control" id="niveau" onchange="this.form.submit()">
                <option value="all" <?php if ($niveau === "all") echo "selected" ?>>Tous les niveaux</option>
                <option value="q" <?php if ($niveau === "q")   echo "selected" ?>>Qualification</option>
                <option value="t" <?php if ($niveau === "t")   echo "selected" ?>>Technicien</option>
                <option value="ts" <?php if ($niveau === "ts")  echo "selected" ?>>Technicien Spécialisé</option>
                <option value="l" <?php if ($niveau === "l")   echo "selected" ?>>Licence</option>
                <option value="m" <?php if ($niveau === "m")   echo "selected" ?>>Master</option>
            </select>

            <button type="submit" class="btn btn-success">
                <span class="glyphicon glyphicon-search"></span>
                Chercher...
            </button>

            &nbsp;&nbsp;

            <?php if ($_SESSION['user']['role'] == 'ADMIN') { ?>

                <a href="nouvelleFiliere.php">

                    <span class="glyphicon glyphicon-plus"></span>

                    Nouvelle filière

                </a>

            <?php } ?>

        </form>
    </div>
</div>
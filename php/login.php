<?php
session_start();
$_POST["goback"] = 1;
require("backround.php");
$fenetreWarnig = "
            <center>
            <div id=wrong>
            <img src=../image/wrong.png width=50 height=50><br>
            Mauvais nom d'utilisateur ou mot de passe<br><br>
            </div>
            </center>
            ";
if (isset($_POST["id"]) && isset($_POST["mdp"])) {
    require("connexion.php");
    //verifier le login et le mdp saisie
    try {
        $requete = $bdd->prepare("SELECT * FROM visiteur WHERE login = :login AND mdp = :password");
        $requete->bindValue(':login', $_POST["id"], PDO::PARAM_STR);
        $requete->bindValue(':password', $_POST["mdp"], PDO::PARAM_STR);
        $requete->execute();
        $result = $requete->fetch();
        if ($result) {
            $_SESSION['login']  = $_POST["id"];
            $_SESSION['pwd']    = $_POST["mdp"];
            $_SESSION["goback"] = 1;
            header('Location: menu.php');
        } else if (isset($_POST["id"])) {
            echo $fenetreWarnig;
        }
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE HTML>
<head>
    <title>Insert</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/standard.css">
</head>
<body>
    <div class="backWhite">
        <br>
        <center>
            Connexion
        </center>
        <center>
            <form action="login.php" method="post" class="form-exemple">
                <br>
                <label id="txt_id">Nom utilisateur :</label>
                <input type="text" name="id">
                <br>
                <br>
                <label>Mot de passe :</label>
                <input type="password" name="mdp">
                <br>
                <br>
                <input type="submit" value="Connexion">
                <br>
                <br>
            </form>
    </div>
    </center>
</body>
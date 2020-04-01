<?php
session_start();
require("backround.php");
require("connexion.php");
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}
echo "<span id=log>";
echo "<span id=txt>Connecté : " . $_SESSION['login'] . "</span>";
echo "<img src=../image/account.png width=50 height=50><br>";
echo "</span>";
//Si un utilisateur arrive sur cette page sans être connecté
if (!isset($_SESSION["goback"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
//Si on click sur deconnecter...
if (isset($_GET["deco"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE HTML>
<head>
    <title>Insert</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/standard.css">
</head>
<body>
    <div class="buttondiv">
        <center>

            <?php
//Si l'utilisateur est un Administrateur
if ($_SESSION['login'] != $admineUserName) {
    echo "<button type=button onclick=window.location.href='afficher.php'>Afficher mes fiches des frais</button><br>";
    echo "<button type=button onclick=window.location.href='insertLigne.php'>Inserer de nouveaux frais sur le mois courant</button><br>";
}
//Si l'utilisateur n'est pas est un Administrateur
else {
    echo "<button type=button onclick=window.location.href='afficher.php'>Afficher les fiches des collaborateurs</button><br>";
    echo "<button type=button disabled>Inserer de nouveaux frais (impossible vous êtes comptable)</button><br>";
}
?>
           <button type="button" onclick="window.location.href='menu.php?deco=1'">Deconnexion</button><br>
        </center>
    </div>
</body>
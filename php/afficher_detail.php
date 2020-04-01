<?php
session_start();
require("backround.php");
require("connexion.php");
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}
if (!isset($_SESSION["goback"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
echo "<span id=log>";
echo "<span id=txt>Connecté : " . $_SESSION['login'] . "</span>";
echo "<img src=../image/account.png width=50 height=50><br>";
echo "<a id=logout href=menu.php?>Retourner au Menu</a><br><br>";
echo "<a id=logout href=insertLigne.php?deco=1>Déconnexion</a>";
echo "</span>";
if (isset($_GET["idFF"])) {
    $_SESSION['idLTdelete'] = $_GET["idFF"];
    //echo $_SESSION['idLTdelete'];
}
if (isset($_SESSION['login'])) {
    try {
        $requeteID = $bdd->prepare("SELECT id FROM visiteur WHERE login=:userLogin");
        $requeteID->bindValue(':userLogin', $_SESSION['login'], PDO::PARAM_STR);
        $requeteID->execute();
        $resultID = $requeteID->fetchAll(PDO::FETCH_BOTH);
        foreach ($resultID as $ligne) {
            $CurrentUserId = $ligne[0];
        }
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
global $prixTot;
$prixTot     = 0;
$resultFICHE = false;
try {
    if (isset($_GET["idFF"])) {
        $keyDeIdentifiantFiche = "key" . $_GET["idFF"];
        $requeteFICHE          = $bdd->prepare("SELECT quantite, idFraisForfait FROM lignefraisforfait WHERE idFF = :idFF");
        $requeteFICHE->bindValue(':idFF', $_SESSION[$keyDeIdentifiantFiche], PDO::PARAM_STR);
        $requeteFICHE->execute();
        $resultFICHE = $requeteFICHE->fetchAll(PDO::FETCH_ASSOC);
    }
    if ($resultFICHE != false) {
        echo ("<table class=TabListefiche>");
        echo "
                    <tr>
                    <th class=Tabtd>Quantite</th>
                    <th class=Tabtd>Type</th>
                    <th class=Tabtd>Prix</th>
                    <th class=Tabtd>Supprimer</th>
                    <th class=Tabtd>Ajouter</th>
                    <th class=Tabtd>Retirer</th>
                    
                    </tr>";
        foreach ($resultFICHE as $key => $value) {
            echo "<tr>";
            foreach ($value as $key2 => $value2) {
                if ($key2 == 'idFraisForfait') {
                    echo "<td class=Tabtd>";
                    if ($value2 == 'ETP')
                        echo "Forfait Etape";
                    if ($value2 == 'KM')
                        echo "Frais kilomètrique";
                    if ($value2 == 'NUI')
                        echo "Nuitée Hôtel";
                    if ($value2 == 'REP')
                        echo "Repas Restaurant";
                    //else{echo "$value2";}
                    echo "</td>";
                    switch ($value2) {
                        case "ETP":
                            $prix = $qt * 110;
                            echo "<td class=Tabtd>" . $prix . " €</td>";
                            $prixTot += $prix;
                            break;
                        case "KM":
                            $prix = $qt * 0.62;
                            echo "<td class=Tabtd>" . $prix . " €</td>";
                            $prixTot += $prix;
                            break;
                        case "NUI":
                            $prix = $qt * 80;
                            echo "<td class=Tabtd>" . $prix . " €</td>";
                            $prixTot += $prix;
                            break;
                        case "REP":
                            $prix = $qt * 25;
                            echo "<td class=Tabtd>" . $prix . " €</td>";
                            $prixTot += $prix;
                            break;
                    }
                    echo "<td class=Tabtd><a id=ModifA href=delete_ligne.php?idType=" . $value2 . "><img src=../image/corbeille.png width=30 height=30 class=corbeille></a></td>";
                    echo "<td class=Tabtd><a id=ModifA href=plus.php?idType=" . $value2 . "><img src=../image/plus.png width=30 height=30 class=corbeille></a></td>";
                    echo "<td class=Tabtd><a id=ModifA href=moins.php?idType=" . $value2 . "><img src=../image/moins.png width=30 height=30 class=corbeille></a></td>";
                    $_SESSION['IdDeLaFiche'] = $value2;
                } else {
                    echo "<td class=Tabtd>";
                    $qt = $value2;
                    echo "$value2";
                    echo "</td>";
                }
            }
            echo "</tr>";
        }
        echo "<tr><td colspan=6 class=Tabtd><br>Prix Total : " . $prixTot . " €</td></tr>";
        echo ("</table><br>");
    } else {
        echo "<div class=backWhite>";
        echo "La fiche sélectionnée ne contient aucuns frais<br>Pour déclarer de nouveaux frais,<a id=ModifA href=insertLigne.php>cliquez ici</a>";
        echo "</div>";
    }
}
catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
<!DOCTYPE HTML>
<head>
    <title>Insert</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/afficher.css?<?php
echo time();
?>">
</head>
<body>

    

<div class="backWhite">

</div>


</body>
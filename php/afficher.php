<?php
session_start();
require("backround.php");
require("connexion.php");
if (isset($_GET["deco"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
if (!isset($_SESSION["goback"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
if (isset($_GET["delete"]) && $_GET["delete"] == 1 && isset($_GET["idFF"])) {
    echo $_GET["delete"];
    try {
        $requeteID = $bdd->prepare("DELETE *f FROM fichefrais WHERE idFF=:idFF");
        $requeteID->bindValue(':idFF', $_GET["idFF"], PDO::PARAM_STR);
        $requeteID->execute();
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}
echo "<span id=log>";
echo "<span id=txt>Connecté : " . $_SESSION['login'] . "</span>";
echo "<img src=../image/account.png width=50 height=50><br>";
echo "<a id=logout href=menu.php?>Retourner au Menu</a><br><br>";
echo "<a id=logout href=afficher.php?deco=1>Déconnexion</a>";
echo "</span>";
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
//SELECT * FROM `fichefrais` WHERE idVisiteur = 'adm';
global $cacheKey;
$cacheKey  = 0;
$increment = 0;
try {
    if ($_SESSION['login'] != $admineUserName) {
        $requeteFICHE = $bdd->prepare("SELECT mois,dateModif, etat, id FROM fichefrais WHERE idVisiteur = :login");
        $requeteFICHE->bindValue(':login', $CurrentUserId, PDO::PARAM_STR);
    } else {
        $requeteFICHE = $bdd->prepare("SELECT idVisiteur,mois,dateModif, etat, id FROM fichefrais");
    }
    $requeteFICHE->execute();
    $resultFICHE = $requeteFICHE->fetchAll(PDO::FETCH_ASSOC);
    if ($resultFICHE != false) {
        echo ("<table class=TabListefiche><tr class=trTab>");
        if ($_SESSION['login'] == $admineUserName) {
            echo "<tr><th class=Tabtd>Nom</th>";
        } else {
            echo "<tr>";
        }
        echo "<th class=Tabtd>Mois</th><th class=Tabtd>Date de la dernière Modification</th><th class=Tabtd>Etat</th><th class=Tabtd>Supprimer</th><th class=Tabtd>Prix Total</th><th class=Tabtd>Modifier</th></tr>";
        foreach ($resultFICHE as $idFiche => $Resfiche) {
            echo "<tr>";
            foreach ($Resfiche as $KeyLiFiche => $LiFiche) {
                if ($KeyLiFiche == 'id') {
                    $keyDeIdentifiantFiche            = "key" . $increment;
                    $_SESSION[$keyDeIdentifiantFiche] = $LiFiche;
                    $cacheKey                         = $LiFiche;
                    //$modif = "<a id=ModifA href=afficher_detail.php?idFF=".$LiFiche.">Afficher cette fiche</a>";
                    $modif                            = "<a id=ModifA href=afficher_detail.php?idFF=" . $increment . ">Afficher cette fiche</a>";
                    echo ("<td class=Tabtd>
                                  <a id=ModifA href=delete.php?idFF=" . $increment . ">Supprimer</a>
                                  </td>");
                    $increment++;
                    /////////////////////////////////////////////////////////////////////////////////        try
                    global $prixTot;
                    $prixTot = 0;
                    try {
                        $requeteFICHEP = $bdd->prepare("SELECT quantite, idFraisForfait FROM lignefraisforfait WHERE idFF = :idFF");
                        $requeteFICHEP->bindValue(':idFF', $cacheKey, PDO::PARAM_STR);
                        $requeteFICHEP->execute();
                        $resultFICHEP = $requeteFICHEP->fetchAll(PDO::FETCH_ASSOC);
                        //var_dump($resultFICHE);        
                        if ($resultFICHEP != false) {
                            //echo("<tr><td class=Tabtd>lolilo</td></tr>");
                            foreach ($resultFICHEP as $key => $value) {
                                //echo "<tr>";
                                foreach ($value as $key2 => $value2) {
                                    if ($key2 == 'idFraisForfait') {
                                        //echo "<td class=Tabtd>";
                                        switch ($value2) {
                                            case "ETP":
                                                $prix = $qt * 110;
                                                //echo "<td class=Tabtd>".$prix." €</td>";
                                                $prixTot += $prix;
                                                break;
                                            case "KM":
                                                $prix = $qt * 0.62;
                                                //echo "<td class=Tabtd>".$prix." €</td>";
                                                $prixTot += $prix;
                                                break;
                                            case "NUI":
                                                $prix = $qt * 80;
                                                //echo "<td class=Tabtd>".$prix." €</td>";
                                                $prixTot += $prix;
                                                break;
                                            case "REP":
                                                $prix = $qt * 25;
                                                //echo "<td class=Tabtd>".$prix." €</td>";
                                                $prixTot += $prix;
                                                break;
                                        }
                                    } else {
                                        $qt = $value2;
                                    }
                                }
                            }
                            /*echo("<td class=Tabtd>
                            <a id=ModifA href=delete.php?idFF=".$LiFiche.">Supprimer</a>
                            </td>");*/
                            echo "<td class=Tabtd> " . $prixTot . " €</td>";
                        } else {
                            echo "<td class=Tabtd>0 €</td>";
                        }
                    }
                    catch (Exception $e) {
                        die('Erreur : ' . $e->getMessage());
                    }
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////:
                } elseif ($KeyLiFiche == 'etat') {
                    if ($LiFiche == 'CL')
                        echo ("<td class=Tabtd>Cloturée</td>");
                    if ($LiFiche == 'CR')
                        echo ("<td class=Tabtd>Créée</td>");
                    if ($LiFiche == 'VA')
                        echo ("<td class=Tabtd>Validée</td>");
                    if ($LiFiche == 'MI')
                        echo ("<td class=Tabtd>Mise en paiement</td>");
                    if ($LiFiche == 'RE')
                        echo ("<td class=Tabtd>Remboursée</td>");
                } elseif ($KeyLiFiche == 'idVisiteur') {
                    try {
                        $requeteID = $bdd->prepare("SELECT nom FROM visiteur WHERE id=:userLoginI");
                        $requeteID->bindValue(':userLoginI', $LiFiche, PDO::PARAM_STR);
                        $requeteID->execute();
                        $resultID = $requeteID->fetchAll(PDO::FETCH_BOTH);
                        foreach ($resultID as $ligne) {
                            $CurrentUserIdR = $ligne[0];
                            echo "<td class=Tabtd>" . $CurrentUserIdR . "</td>";
                        }
                    }
                    catch (Exception $e) {
                        die('Erreur : ' . $e->getMessage());
                    }
                } else //////////////////////////////////////////////A RETIRER SI ON N'IMPLEMANTE LA MODIFIFICATION DES FICHES
                    {
                    echo "<td class=Tabtd>";
                    echo "$LiFiche";
                }
                echo "</td>";
            }
            echo "<td class=Tabtd>" . $modif . "</td>";
            echo "</tr>";
        }
        echo ("</table>");
    } else {
        echo "<div class=backWhite>";
        echo "Vous n'avez entré aucune fiche<br>Pour déclarer de nouveaux frais,<a id=ModifA href=insertLigne.php>cliquez ici</a>";
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
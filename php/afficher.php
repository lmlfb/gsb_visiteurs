<?php

session_start();
require("backround.php");
require("connexion.php");
if (isset($_GET["deco"])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

//aller à la page login si pas connecté
if (!isset($_SESSION["goback"])) {
    session_destroy();
    header('Location: login.php');
    exit();
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


//Recuperer les fiches frais de l'utilisateur
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
                    global $prixTot;
                    $prixTot = 0;
                    try {
                        $requeteFICHEP = $bdd->prepare("SELECT quantite, idFraisForfait FROM lignefraisforfait WHERE idFF = :idFF");
                        $requeteFICHEP->bindValue(':idFF', $cacheKey, PDO::PARAM_STR);
                        $requeteFICHEP->execute();
                        $resultFICHEP = $requeteFICHEP->fetchAll(PDO::FETCH_ASSOC);      
                        if ($resultFICHEP != false) {
                            foreach ($resultFICHEP as $key => $value) {
                                foreach ($value as $key2 => $value2) {
                                    if ($key2 == 'idFraisForfait') {
                                        switch ($value2) {
                                            case "ETP":
                                                $prix = $qt * 110;
                                                $prixTot += $prix;
                                                break;
                                            case "KM":
                                                $prix = $qt * 0.62;
                                                $prixTot += $prix;
                                                break;
                                            case "NUI":
                                                $prix = $qt * 80;
                                                $prixTot += $prix;
                                                break;
                                            case "REP":
                                                $prix = $qt * 25;
                                                $prixTot += $prix;
                                                break;
                                        }
                                    } else {
                                        $qt = $value2;
                                    }
                                }
                            }

                            echo "<td class=Tabtd> " . $prixTot . " €</td>";
                        } else {
                            echo "<td class=Tabtd>0 €</td>";
                        }
                    }
                    catch (Exception $e) {
                        die('Erreur : ' . $e->getMessage());
                    }

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
                } else 
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
    <link rel="stylesheet" type="text/css" href="../css/afficher.css">
</head>
<body>
<div class="backWhite">

</div>


</body>
<?php

session_start();


//Si l'utilisateur arrivant sur la page n'est pas connecté.....
if (!isset($_SESSION["goback"])) {
session_destroy();
 header('Location: login.php');
exit();}

require("backround.php");

$number = 0;

//Si l'utilisateur clic sur se deconnecter....
if (isset($_GET["deco"])) {
    session_destroy();
     header('Location: login.php');
    exit();
}

//Affichage login de l'utilisateur et du bouton deconnexion.....
echo "<span id=log>";
echo "<span id=txt>Connecté : " . $_SESSION['login'] . "</span>";
echo "<img src=../image/account.png width=50 height=50><br>";
echo "<a id=logout href=menu.php?>Retourner au Menu</a><br><br>";
echo "<a id=logout href=insertLigne.php?deco=1>Déconnexion</a>";
echo "</span>";


//Variable contenant le message d'alerte "La quantité doit être supérieur à 0"........
$fenetreWarnig = "
            <center>
            <div id=wrong>
            <img src=../image/wrong.png width=50 height=50><br>
            La quantité doit être supérieur à 0<br><br>
            </div>
            </center>
            ";

//Variable contenant la chaine de charactère pour la génération de l'id des ligne de la facture......
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

//Recuperer le mois actuel........
$arrayM = array("janv", "fev", "mars", "avr", "mai","juin", "juil", "aout", "sept","oct", "nov", "dec");
$indexMonth = date('m')*1;
$CurrentM = $arrayM[intval($indexMonth)-1];
$PrecedentM = $arrayM[intval($indexMonth)-2];
if($CurrentM == "Janvier"){
$YearOfPrecentMonth = date('Y')-1;}
else{$YearOfPrecentMonth = date('Y');}

//Ajouter les identifiants de connexion
require ("connexion.php");


//>>>>>>>>>>>>>>>>>>>>>>>>>>>>trouver le prix du forfait selectionné (ex frais au km = 0.68ct)<<<<<<<<<<<<<<<<<<<<<<<<<<<<
try
{
    if (isset($_GET["typeFrais"]))
    {
        $requete = $bdd->prepare("SELECT montant FROM fraisforfait WHERE id=:typefrais");
        $requete->bindValue(':typefrais', $_GET["typeFrais"], PDO::PARAM_STR);
        $requete->execute();
        $result = $requete->fetch(PDO::FETCH_BOTH);
    }
}
catch(Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}

//=========================================================================================================================



global $prix;

//Si l'utilisateur a saisie une quantité
if (isset($_GET["qt"])) {
$prix = $result['montant'] * $_GET["qt"];
}


///>>>>>>>>>>>>>>>>>>>>>>>>>>>>Recuper id de l'utilisateur à partir du login de l'utilisateur<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
if (isset($_SESSION['login']))
{
    try
    {
        //SELECT id FROM visiteur WHERE login='admin'
        //$prix =  $result['montant']*$_GET["qt"];
        $requeteID = $bdd->prepare("SELECT id FROM visiteur WHERE login=:userLogin");
        $requeteID->bindValue(':userLogin', $_SESSION['login'], PDO::PARAM_STR);
        $requeteID->execute();
        $resultID = $requeteID->fetchAll(PDO::FETCH_BOTH);
        foreach ($resultID as $ligne)
        {
            $CurrentUserId = $ligne[0];
        }
    }
    catch(Exception $e)
    {
        die('Erreur : ' . $e->getMessage());
    }
}
//=========================================================================================================================

///>>>>>>>>>>>>>>>>>>>>>>>>>>>>Verifier s'il y'a dejà une fiche de frais ce mois ci<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

try
{
    $requeteFI = $bdd->prepare("SELECT * FROM fichefrais WHERE mois = :CurrentMois AND dateModif LIKE :CurrentY AND idVisiteur = :idVisiteur;");
    $requeteFI->bindValue(':CurrentMois', $CurrentM, PDO::PARAM_STR);
    $requeteFI->bindValue(':CurrentY', date('Y'). '%', PDO::PARAM_STR);
    $requeteFI->bindValue(':idVisiteur', $CurrentUserId, PDO::PARAM_STR);
    $requeteFI->execute();
    $resultFI = $requeteFI->fetch(PDO::FETCH_BOTH);
    if($resultFI==true){
        $createNewFact = true;
    }
    else{
        $createNewFact = false;
    }
}
catch(Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
 //=========================================================================================================================


///>>>>>>>>>>>>>>>>>>>>>>>>>>>>Nouvelle fiche de frais si non existante<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
if($createNewFact==false){
    try
    {
        $requete = $bdd->prepare("INSERT INTO fichefrais VALUES (:id, :mois, :dateModif, :idVisiteur, :etat)");
        //substr($str, 2); 
         //$requete->bindValue(':id', substr(str_shuffle($permitted_chars) , 4, 4) , PDO::PARAM_STR);
        $requete->bindValue(':id', substr($CurrentUserId, -1) . substr($CurrentM, -1) . date('y'), PDO::PARAM_STR);
        //$requete->bindValue(':id', "OLy", PDO::PARAM_STR);
        $requete->bindValue(':mois', $CurrentM, PDO::PARAM_STR);
        $requete->bindValue(':dateModif', date('Y-m-d') , PDO::PARAM_STR);
        $requete->bindValue(':idVisiteur', $CurrentUserId, PDO::PARAM_STR);
        $requete->bindValue(':etat', 'CR', PDO::PARAM_STR);
        //$requete->bindValue(':password', $_GET["mdp"], PDO::PARAM_STR);
        $requete->execute();
    }
    catch(Exception $e) 
    {
        die('Erreur : ' . $e->getMessage());
    }
}
 //=========================================================================================================================



///>>>>>>>>>>>>>>>>>>>>>>>>>>>Verifier s'il il y'a une ligne existante pour ce type de frais ce mois ci<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
global $creerNewLigne;

if (isset($_GET["qt"]))
{
    if ($_GET["qt"] > 0)
    {
        //Verifions si il y'a une fiche de frais 
        try
        {
            $requeteELF = $bdd->prepare("SELECT * FROM lignefraisforfait WHERE idFF=:idFF AND idFraisForfait=:idFraisForfait");
            $requeteELF->bindValue(':idFraisForfait', $_GET["typeFrais"] , PDO::PARAM_STR);
            $requeteELF->bindValue(':idFF', substr($CurrentUserId, -1) . substr($CurrentM, -1) . date('y'), PDO::PARAM_STR);
            $requeteELF->execute();
            $resultELF = $requeteELF->fetch(PDO::FETCH_BOTH);
            if($resultELF==false)
            {                       
                //Inserer une nouvelle ligne de frais forfait...
                try
                {
                    
                    $prix = $result['montant'] * $_GET["qt"];
                    $requete = $bdd->prepare("INSERT INTO lignefraisforfait VALUES (:id, :quantite, :idFraisForfait, :idFF)");
                    $requete->bindValue(':id', substr(str_shuffle($permitted_chars) , 4, 4) , PDO::PARAM_STR);
                    $requete->bindValue(':quantite', $_GET["qt"], PDO::PARAM_STR);
                    $requete->bindValue(':idFraisForfait', $_GET["typeFrais"] , PDO::PARAM_STR);
                    $requete->bindValue(':idFF', substr($CurrentUserId, -1) . substr($CurrentM, -1) . date('y'), PDO::PARAM_STR);
                    $requete->execute();
                }
                catch(Exception $e)
                {
                    die('Erreur : ' . $e->getMessage());
                }
            }
            else{
                // Ajouter la quantité saisie à la ligne de frais existante 
                try
                {
                    $prix = $result['montant'] * $_GET["qt"];
                    $requete = $bdd->prepare("UPDATE lignefraisforfait SET quantite=quantite+:qtAjout WHERE idFF=:idFF AND idFraisForfait=:idFraisForfait");
                    $requete->bindValue(':qtAjout', $_GET["qt"], PDO::PARAM_STR);
                    $requete->bindValue(':idFraisForfait', $_GET["typeFrais"] , PDO::PARAM_STR);
                    $requete->bindValue(':idFF', substr($CurrentUserId, -1) . substr($CurrentM, -1) . date('y'), PDO::PARAM_STR);
                    $requete->execute();
                }
                catch(Exception $e)
                {
                    die('Erreur : ' . $e->getMessage());
                }
            }
        }
        catch(Exception $e)
        {
            die('Erreur : ' . $e->getMessage());
        }
    }
    else{
            echo $fenetreWarnig;

    }

}

 //=========================================================================================================================

//<<<<<<<<<<<<<<<<<Trouver si il y'a une fiche de frais le mois précédent et la cloturer à la création de la nouvelle>>>>>>>>>>
        try
        {
            $requetePLM = $bdd->prepare("SELECT * FROM fichefrais WHERE mois = :PrecMois AND dateModif LIKE :CurrentYe AND idVisiteur = :idVisiteur AND etat = 'CR';");
            $requetePLM->bindValue(':PrecMois', $PrecedentM, PDO::PARAM_STR);
            $requetePLM->bindValue(':CurrentYe', $YearOfPrecentMonth . '%', PDO::PARAM_STR);
            $requetePLM->bindValue(':idVisiteur', $CurrentUserId, PDO::PARAM_STR);
            $requetePLM->execute();
            $resultPLM = $requetePLM->fetch(PDO::FETCH_BOTH);
            if($resultPLM==true){
                try
                {
                    $requetePLM = $bdd->prepare("UPDATE fichefrais SET etat = 'CL' WHERE mois = :PrecMoisPLM");
                    $requetePLM->bindValue(':PrecMoisPLM', $PrecedentM, PDO::PARAM_STR);
                    $requetePLM->execute();
                }
                catch(Exception $v)
                {
                    die('Erreur : ' . $v->getMessage());
                }
            }
        }
        catch(Exception $e)
        {
            die('Erreur : ' . $e->getMessage());
        }
//=========================================================================================================================


//Calcul du prix (qui sera plus bas affiché momentanement en vert lors de la confirmation de la saisie)
if(isset($result['montant']) && isset($_GET["qt"])){
$prix = $result['montant'] * $_GET["qt"];}
?>

<!DOCTYPE HTML>
<head>
    <title>Insert</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/frais.css?<?php echo time(); ?>">
</head>
<body>
    <div class="backWhite">
        <br>
        <center>
            Ajouter des frais
        </center>
        <center>
            <form action="insertLigne.php" method="get" class="form-exemple" value="ETP">
                <br>
                Type de frais :
                <select name="typeFrais">
                    <option value="ETP">Forfait Etape</option>
                    <option value="KM">Frais kilomètrique</option>
                    <option value="NUI">Nuitée Hôtel</option>
                    <option value="REP">Repas Restaurant</option>
                </select><br><br>
                Quantité : <input type="number" name="qt" id="smallerNumber" value= 0>
                <br>
                <br>
                <input type="submit" value="Ajouter frais">

                <br>
                <br>
            </form>
            </div>
                 <?php
                    //affiche d'une fenêtre de confirmation 
                    if (isset($_GET["qt"]))
                    {
                        if ($_GET["qt"] > 0)
                        {
                        echo "<center>
                        <div id=greenOk><br>
                        <img src=../image/check.png width=35 height=35><br>
                        Ajouté " . $prix . " €<br><br>
                        </div>
                        </center>";
                        }
                    }
                ?>       
    </center>
</body>
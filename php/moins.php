<?php
//////////////////////////////////////////////////////////////////////////////////////////////
//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	session_start();

	if (!isset($_SESSION["goback"])) {
    session_destroy();
     header('Location: login.php');
    exit();}


	require("backround.php");
	require("connexion.php");

		try
	    {

			echo $_GET["idType"];echo "<br>";
			echo $_SESSION['idLTdelete'];echo "<br>";

			$keyDeIdentifiantFiche = "key".$_SESSION['idLTdelete'];

			echo $_SESSION[$keyDeIdentifiantFiche];


	    	/*                                    $requete = $bdd->prepare("UPDATE lignefraisforfait SET quantite=quantite+:qtAjout WHERE idFF=:idFF AND idFraisForfait=:idFraisForfait");
                                    $requete->bindValue(':qtAjout', $_GET["qt"], PDO::PARAM_STR);
                                    $requete->bindValue(':idFraisForfait', $_GET["typeFrais"] , PDO::PARAM_STR);
                                    $requete->bindValue(':idFF', substr($CurrentUserId, -1) . substr($CurrentM, -1) . "FI", PDO::PARAM_STR);
                                    $requete->execute();

                                    */
	        //SELECT id FROM visiteur WHERE login='admin'
	        //$prix =  $result['montant']*$_GET["qt"];
	        $requeteID = $bdd->prepare("UPDATE lignefraisforfait SET quantite = CASE WHEN quantite>0 THEN quantite-1 ELSE 0 END WHERE idFF=:idFF AND idFraisForfait=:idFraisForfait");
	        $requeteID->bindValue(':idFraisForfait', $_GET["idType"], PDO::PARAM_STR);
	        $requeteID->bindValue(':idFF', $_SESSION[$keyDeIdentifiantFiche], PDO::PARAM_STR);
	        $requeteID->execute();


	    }
	    catch(Exception $e)
	    {
	        die('Erreur : ' . $e->getMessage());
	    }

	$url = "afficher_detail.php?idFF=".$_SESSION['idLTdelete'];
	header('Location: '  . $url);
	//header("Location: afficher_detail.php?".$_SESSION['idLTdelete'].");

	
	
?>
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

	$keyDeIdentifiantFiche = "key".$_GET["idFF"];

		try
	    {
	        //SELECT id FROM visiteur WHERE login='admin'
	        //$prix =  $result['montant']*$_GET["qt"];
	        $requeteID = $bdd->prepare("DELETE FROM lignefraisforfait WHERE idFF=:idFF");
	        $requeteID->bindValue(':idFF', $_SESSION[$keyDeIdentifiantFiche], PDO::PARAM_STR);
	        $requeteID->execute();	
	    }
	    catch(Exception $e)
	    {
	        die('Erreur : ' . $e->getMessage());
	    }

	    try
	    {
	        //SELECT id FROM visiteur WHERE login='admin'
	        //$prix =  $result['montant']*$_GET["qt"];
	        $requeteID = $bdd->prepare("DELETE FROM fichefrais WHERE id=:idFF");
	        $requeteID->bindValue(':idFF', $_SESSION[$keyDeIdentifiantFiche], PDO::PARAM_STR);
	        $requeteID->execute();	
	    }
	    catch(Exception $e)
	    {
	        die('Erreur : ' . $e->getMessage());
	    }

	header("Location: afficher.php");

	
	
?>
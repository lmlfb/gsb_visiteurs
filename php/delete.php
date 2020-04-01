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
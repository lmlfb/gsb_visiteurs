<?php
	
	session_start();

	if (!isset($_SESSION["goback"])) {
    session_destroy();
     header('Location: login.php');
    exit();}


	require("backround.php");
	require("connexion.php");

		echo $_GET["idType"];echo "<br>";
		echo $_SESSION['idLTdelete'];echo "<br>";

		$keyDeIdentifiantFiche = "key".$_SESSION['idLTdelete'];

		echo $_SESSION[$keyDeIdentifiantFiche];

		try
	    {
	        $requeteID = $bdd->prepare("DELETE FROM lignefraisforfait WHERE idFraisForfait=:idFraisForfait AND idFF=:idFF");
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

	
	
?>
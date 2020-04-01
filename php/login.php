<?php

	session_start();

    $_GET["goback"]=1;

	require("backround.php");

	$fenetreWarnig = "
			<center>
			<div id=wrong>
			<img src=../image/wrong.png width=50 height=50><br>
			Mauvais nom d'utilisateur ou mot de passe<br><br>
			</div>
			</center>
			";
	
	if(isset($_GET["id"]) && isset($_GET["mdp"])){

	require("connexion.php");

		//verifier le login et le mdp saisie
		try
		{	
			$requete = $bdd->prepare("SELECT * FROM visiteur WHERE login = :login AND mdp = :password");
	
			$requete->bindValue(':login', $_GET["id"], PDO::PARAM_STR);
			$requete->bindValue(':password', $_GET["mdp"], PDO::PARAM_STR);
	
			$requete->execute();
			$result = $requete->fetch();
	
			if ($result)
			{
				$_SESSION['login'] = $_GET["id"];
				$_SESSION['pwd'] = $_GET["mdp"];
				$_SESSION["goback"]=1;
				header('Location: menu.php');
			}
			else if (isset($_GET["id"])) {
				
				echo $fenetreWarnig;
			}
			
		} catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
	
		}
	
	
	}
	?>
<!DOCTYPE HTML>
<head>
	<title>Insert</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../css/standard.css?<?php echo time(); ?>">
</head>
<body>
	<div class="backWhite">
		<br>
		<center>
			Connexion
		</center>
		<center>
			<form action="login.php" method="get" class="form-exemple">
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
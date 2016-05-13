<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="fr"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="fr"> <![endif]-->
<html lang="fr">
<!--<![endif]-->
<head>

<!-- Basic Page Needs -->
<meta charset="utf-8">
<title>Questionnaire interactif pour les couples | Find All Pleasures</title>
<meta name="description" content="FindAllPleasure est un questionnaire intéractif permettant de découvrir les fantasmes commun d'un couple">
<meta name="author" content="Léotier Nicolas">

<!-- Favicons-->
<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon"/>
<link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
<link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
<link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
<link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

<!-- Mobile Specific Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- CSS -->
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<!-- Google web font -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- HTML5 and CSS3-in older browsers-->
<script src="js/modernizr.custom.17475.js"></script>

<!-- Support media queries for IE8 -->
<script src="js/respond.min.js"></script>

<!--[if IE 7]>
  <link rel="stylesheet" href="font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->

</head>
<body>
<?php

date_default_timezone_set('Europe/Paris');

require_once '../dbCred.php';
require_once 'PHPMailer/PHPMailerAutoload.php';

/* DEBUT FONCTIONS BDD */

function ajouter_utilisateur($pdo, $pseudo, $mail, $sexe, $avance, $num) {
	$stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, mail, sexe_id, avance, num)
						VALUES (:pseudo, :mail, :sexe_id, :avance, :num)");
						
	$stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR, 100);
	$stmt->bindParam(':mail', $mail, PDO::PARAM_STR, 100);
	$stmt->bindParam(':sexe_id', $sexe, PDO::PARAM_INT);
	$stmt->bindParam(':avance', $avance, PDO::PARAM_INT);
	$stmt->bindParam(':num', $num, PDO::PARAM_INT);
	
	$stmt->execute();

	return $pdo->lastInsertId();
}

function ajouter_reponse($pdo, $num, $reponse, $utilisateur_id) {
	$stmt = $pdo->prepare("INSERT INTO reponses (num, reponse, utilisateur_id) 
						VALUES (:num, :reponse, :utilisateur_id)");
	$stmt->bindParam(':num', $num, PDO::PARAM_INT);
	$stmt->bindParam(':reponse', $reponse, PDO::PARAM_INT);
	$stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
	
	$stmt->execute();
}

function ajouter_questionnaire($pdo, $utilisateur_1_id, $utilisateur_2_id, $clef) {
	$stmt = $pdo->prepare("INSERT INTO questionnaires (utilisateur_1_id, utilisateur_2_id, clef)
						VALUES (:utilisateur_1_id, :utilisateur_2_id, :clef)");
	$stmt->bindParam(':utilisateur_1_id', $utilisateur_1_id, PDO::PARAM_INT);
	$stmt->bindParam(':utilisateur_2_id', $utilisateur_2_id, PDO::PARAM_INT);
	$stmt->bindParam(':clef', $clef, PDO::PARAM_STR, 30);
	
	$stmt->execute();
}

function obtenir_questionnaire($pdo, $clef) {
	$stmt = $pdo->prepare("SELECT * FROM questionnaires WHERE clef = :clef");
	$stmt->bindParam(':clef', $clef, PDO::PARAM_STR, 30);
	$stmt->execute();
	
	return $stmt->fetch();
}

/* FIN FONCTIONS BDD */

/* DEBUT FONCTIONS CLEF */

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
	
    if ($range < 1) return $min; // not so random...
	
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
	
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
	
    return $min + $rnd;
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet) - 1;
	
    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max)];
    }
	
    return $token;
}

/* FIN FONCTIONS CLEF */

/* DEBUT FONCTIONS MAIL */


function mail_partenaire($mail_address, $pseudo, $key) {
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';

	$mail->SMTPDebug = 3;									// Enable verbose debug output
	$mail->Debugoutput = 'html';							// Ask for HTML-friendly debug output

	
	$mail->isSMTP();										// Set mailer to use SMTP
	$mail->Host = 'mx1.rapidomaine.biz';					// Specify main SMTP servers
	$mail->SMTPAuth = true;									// Enable SMTP authentication
	$mail->Username = 'nicolas@leotier.fr';					// SMTP username
	$mail->Password = '22itt6';								// SMTP password
	$mail->SMTPSecure = 'ssl';								// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;										// TCP port to connect to - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	

	$mail->setFrom('nicolas@leotier.fr', 'Find All Pleasures');
	$mail->addAddress($mail_address);						// Add a recipient

	//$mail->isHTML(true);									// Set email format to HTML

	$mail->Subject = $pseudo . ' vous invite à remplir un questionnaire avec lui sur FindAllPleasures';
	$mail->Body    = $pseudo . " a remplie un questionnaire sur FindAllPleasures.com et vous invite à remplir le même questionnaire à votre tour.\n";
	$mail->Body    .= "FindAllPleasures.com est un site internet qui aide les couples à pimenter leur vie sexuelle en les aidant à découvrir leurs intérêts sexuels, fantasmes et fétiches commun.\n";
	$mail->Body    .= "Cliquez sur ce lien pour commencer : http://localhost/questionnaire/?type=two&clef=" . $key;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
		echo "Une erreur c'est produite lors de l'envoie de l'e-mail, merci de contacter le développeur avec le message d'erreur suivant : <br>";
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
}

/* FIN FONCTION MAIL */


$pdo = new PDO($dsn, $db_username, $db_password, $db_opt);

// Divers
$nbrQuestions = $_POST["nbrquestions"];
$avance = $_POST["avance"];
$type = $_POST["type"];

// Si c'est l'utilisateur principal, et qu'il n'y a donc pas encore de questionnaire créé dans la BDD
if(!isset($_POST["clef"]))
{
	// utilisateur principal
	$pseudo = $_POST["pseudo"];
	if (isset($_POST["mail"])) $mail = $_POST["mail"]; else $mail = null;
	$sexe = $_POST["sexe"];

	// partenaire
	$pseudo_partenaire = $_POST["pseudo_partenaire"];
	if (isset($_POST["mail_partenaire"])) $mail_partenaire = $_POST["mail_partenaire"]; else $mail_partenaire = null;
	$sexe_partenaire = $_POST["sexe_partenaire"];

	// TODO : faudrait faire une transaction ici

	// enregistrer utilisateur principal
	$partenaire_1_id = ajouter_utilisateur($pdo, $pseudo, $mail, $sexe, $avance, 1);

	// enregistrer partenaire
	$partenaire_2_id = ajouter_utilisateur($pdo, $pseudo_partenaire, $mail_partenaire, $sexe_partenaire, $avance, 2);

	// enregistrer questionnaire
	$clef = getToken(30);

	ajouter_questionnaire($pdo, $partenaire_1_id, $partenaire_2_id, $clef);
	
	// enregistrer les questions
	for($i = 0; $i < $nbrQuestions; $i++) {
		ajouter_reponse($pdo, $i, $_POST["question_" . $i], $partenaire_1_id);
	}
	
	// rediriger en fonction du nombre d'ordinateur
	if($type == "one")
	{
		?>
		
		<div class="container">
			<div class="row">
				<div class=" col-md-12" style="text-align:center; padding-top:80px;">
					<h1 style="color:#333">Merci !</h1>
					<h3 style="color: #6C3">Nous allons maintenant poursuivre avec <?php echo $pseudo_partenaire; ?>.</h3>
					<p>
						<a href="/questionnaire/?type=one&clef=<?php echo $clef; ?>" class="">Continuer</a>
					</p>
				</div>
			</div>
		</div>
		
		<?php
	}
	else if($type == "two")
	{
		mail_partenaire($mail_partenaire, $pseudo, $clef);
		
		?>
		
		<div class="container">
			<div class="row">
				<div class=" col-md-12" style="text-align:center; padding-top:80px;">
					<h1 style="color:#333">Merci !</h1>
					<h3 style="color: #6C3">Nous allons maintenant envoyer un mail à <?php echo $pseudo_partenaire; ?> pour lui demander de remplir le questionnaire à son tour.</h3>
					<p>
						Si vous voulez partager le questionnaire avec <?php echo $pseudo_partenaire; ?> d'une autre façon, voici le lien :
						<a href="/questionnaire/?clef=<?php echo $clef; ?>" class="">http://findallpleasures.com/questionnaire/?type=two&clef=<?php echo $clef; ?>"</a>
					</p>
				</div>
			</div>
		</div>
		
		<?php
	}
	else
	{
		echo "on s'arrête là ;) (coucou Luc?)"; // TODO: refaire cette partie de façon plus professionnelle
		exit();
	}
}
else // si c'est le partenaire
{
	$key = $_POST["key"];
	
	// récupérer le questionnaire
	$questionnaire = obtenir_questionnaire($pdo, $key);
	$partenaire_2_id = $questionnaire["utilisateur_2_id"];
	
	// enregistrer les réponses
	for($i = 0; $i < $nbrQuestions; $i++) {
		ajouter_reponse($pdo, $i, $_POST["question_" . $i], $partenaire_2_id);
	}
}
	
?>
</body>
</html>
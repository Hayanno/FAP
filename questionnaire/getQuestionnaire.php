<?php

require_once '../dbCred.php';

if(isset($_GET["clef"])) // A CHANGER A LA SORTIE DE DEV
	$clef = $_GET["clef"];
else
	$clef = $_POST["clef"];

$pdo = new PDO($dsn, $db_username, $db_password, $db_opt);

$stmt = $pdo->prepare("SELECT * FROM questionnaires WHERE clef = :clef");
$stmt->bindParam(':clef', $clef, PDO::PARAM_STR, 30);
$stmt->execute();

$questionnaire = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->bindParam(':id', $questionnaire['utilisateur_1_id'], PDO::PARAM_INT);
$stmt->execute();

$utilisateur_1 = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->bindParam(':id', $questionnaire['utilisateur_2_id'], PDO::PARAM_INT);
$stmt->execute();

$utilisateur_2 = $stmt->fetch();

$reponse = [];

$reponse["questionnaire"] = $questionnaire;
$reponse["utilisateur_1"] = $utilisateur_1;
$reponse["utilisateur_2"] = $utilisateur_2;

echo json_encode($reponse);

?>
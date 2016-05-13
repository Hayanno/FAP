<?php

require_once '../dbCred.php';

$clef = $_GET['clef'];

// renvoie un tableau des réponses identiques (remerciement spécial à Luc Giffon)
function comparer_reponse($pdo, $clef) {	
	$stmt = $pdo->prepare("SELECT r1.num AS num, r1.reponse AS utilisateur_1_reponse, r2.reponse AS utilisateur_2_reponse
							FROM reponses r1, reponses r2, questionnaires q , utilisateur u1, utilisateur u2
							WHERE q.clef = :clef
								AND q.utilisateur_1_id = r1.utilisateur_id
								AND q.utilisateur_2_id = r2.utilisateur_id
								AND r1.num = r2.num
								AND ((r1.reponse = 3 AND r2.reponse = 4)
									OR (r1.reponse = 4 AND r2.reponse = 3)
									OR (r1.reponse = 4 AND r2.reponse = 4)
								)");
	$stmt->bindParam(':clef', $clef, PDO::PARAM_STR, 30);
	$stmt->execute();
	
	return $stmt->fetchAll();
}

$pdo = new PDO($dsn, $db_username, $db_password, $db_opt);

$reponses = comparer_reponse($pdo, $clef);

?>

<h1>Résultats</h1>

<table class="rwd-table">
	<tr>
		<th>Activité</th>
		<th>Réponse de <?php // echo $reponse['utilisateur_1_pseudo']; ?></th>
		<th>Réponse de <?php // echo $utilisateur_2_pseudo; ?></th>
	</tr>
	<?php
	foreach($reponses as $reponse)
	{
	?>
		<tr>
			<td data-th="Activité"><?php echo $reponse['num']; ?></td>
			<td data-th="Réponse de <?php // echo $utilisateur_1_pseudo; ?>"><?php echo $reponse['utilisateur_1_reponse']; ?></td>
			<td data-th="Réponse de <?php // echo $utilisateur_2_pseudo; ?>"><?php echo $reponse['utilisateur_2_reponse']; ?></td>
		</tr>
	<?php
	}
	?>

</table>
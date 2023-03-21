<?php 

require 'common/sessie_check.php';
require 'common/global.php';


$currentpage = 'opdracht-instellingen';

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Opdrachtsetting - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

		

</head>



<?php 



require( 'common/connection.php');



// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

if ($stmt = $con->prepare('SELECT id, name FROM suppliers')) {	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	

	$stmt->execute();

	// Store the result so we can check if the account exists in the database.

	$result = $stmt->get_result();

}



?>



	<body class="app">

	
		
		<?php include 'common/navigatie.php'; ?>

		

		<div class="appcontent">

		

			<div class="titlebar">

				<div class="titlebarcontainer withbutton">

					<h2>Instellingen</h2>

					<div class="submenu">

						<a class="<?php if($currentpage == 'instellingen') { echo "actief"; } ?>" href="/instellingen/">Profiel</a>

						<a class="<?php if($currentpage == 'gebruikers') { echo "actief"; } ?>" href="/gebruikers/">Gebruikers</a>

                        <a class="<?php if($currentpage == 'leads-instellingen') { echo "actief"; } ?>" href="/leads-instellingen/">Leads</a>

						<a class="<?php if($currentpage == 'opdracht-instellingen') { echo "actief"; } ?>" href="/opdracht-instellingen/">Opdracht</a>
						
						<a class="<?php if($currentpage == 'medewerkers') { echo "actief"; } ?>" href="/medewerkers/">Medewerkers</a>
					</div>

					<div style="clear:both"></div>

				</div>

				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addSupplier()"><i class="material-icons">add</i> Toevoegen</span>

				<div style="clear:both"></div>

			</div>

			

			<table id="opdracht">

				<thead>

				 <tr>

					<th><big>Leveranciers</big></th>
					<th></th>					

				 </tr>

				</thead>

				<tbody>

				

				<?php 

				

				while ($row = $result->fetch_assoc()) { ?>

					

				<tr supplierrow="<?=$row['id'];?>">

					<td><?=$row['name'];?></td>

					<td style="width:110px">

						<div onclick="editSupplier(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>

						<div onclick="deleteSupplier(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>

					</td>

				 </tr>	

				

				<?php } ?>			 

				 

				</tbody>

			</table>

		

		</div>		



	<script src="<?=$root;?>js/settings.js" type="text/javascript"></script>			

	

<?php include 'common/footer.php'; ?>
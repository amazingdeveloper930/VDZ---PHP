<?php 

require 'common/sessie_check.php';
require 'common/global.php';


$currentpage = 'medewerkers';

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Medewerkers - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

		

</head>



<?php 



require( 'common/connection.php');



// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

if ($stmt = $con->prepare('SELECT E.*, E1.name as teamleader_name FROM employees E LEFT JOIN (SELECT id, name FROM employees) E1 ON E.teamleader = E1.id ORDER BY sort_order')) {	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	

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

				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addEmployee()"><i class="material-icons">add</i> Toevoegen</span>

				<div style="clear:both"></div>

			</div>

			


			<ul id="employee-list">
				<li class="list-header"><p class='employee-name'><big><b>Medewerkers</b></big></p><span class="employee-photo"><big><b>Afbeelding</b></big></span><span class="employee-type"><big><b>Afdeling</b></big></span><span><big><b>Teamleider</b></big></span></li>
				<?php 
					while ($row = $result->fetch_assoc()) { ?>
				<li class="employee-item" employeerow="<?=$row['id'];?>">
					<p class='employee-name'><?=$row['name'];?></p>
					<div class='employee-photo'>
						<?php
							if(isset($row['file_path']))
								echo "<img src='" . $root . "upload/" . $row['file_path'] . "'/>";
							else
							echo "<img src='" . $root . "images/users/employee.png'/>";
						?>
					</div>
					<div class='employee-type'><?=$row['type'];?><div class='employee-type-identify employee-type-i-<?=$row['type'];?>'></div></div>
					<div class='employee-teamleader'>
						<?php 
							if($row['teamleader'] == 0)
								echo 'Is teamleider';
							else{
								echo $row['teamleader_name'];
							}
						?>
					</div>
					<div class="actionbuttons">
					 	<div onclick="editEmployee(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>

						<div onclick="deleteEmployee(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
					</div>
				</li>
				<?php } ?>
			</ul>

		

		</div>		



	<script src="<?=$root;?>js/settings.js" type="text/javascript"></script>			

	

<?php include 'common/footer.php'; ?>
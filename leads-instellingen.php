<?php 

require 'common/sessie_check.php';



$currentpage = 'leads-instellingen';

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Lead Setting - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

	
		

</head>



<?php 



require( 'common/connection.php');



// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

if ($stmt = $con->prepare('SELECT id, username, email, activation_code, img FROM accounts WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	$stmt->bind_param('s', $_SESSION['id']);

	$stmt->execute();

	// Store the result so we can check if the account exists in the database.

	$stmt->store_result();

}



if ($stmt->num_rows > 0) {

	$stmt->bind_result($id, $username, $email, $activation, $img);

	$stmt->fetch();	

}



?>



	<body class="app">

	
		
		<?php include 'common/navigatie.php'; ?>

		

		<div class="appcontent">

		

			<div class="titlebar">

				<div class="titlebarcontainer">

					<h2>Instellingen</h2>

					<div class="submenu">

						<a class="<?php if($currentpage == 'instellingen') { echo "actief"; } ?>" href="/instellingen/">Profiel</a>

						<a class="<?php if($currentpage == 'gebruikers') { echo "actief"; } ?>" href="/gebruikers/">Gebruikers</a>

                        <a class="<?php if($currentpage == 'leads-instellingen') { echo "actief"; } ?>" href="/leads-instellingen/">Leads</a>		
						
					<a class="<?php if($currentpage == 'werkplanning-setting') { echo "actief"; } ?>" href="/werkplanning-instellingen/">Werkplanning</a>
					</div>

					<div style="clear:both"></div>

				</div>

				<div style="clear:both"></div>

			</div>

			

			<table >
				<thead>

					<tr>

						<th><big>Offertes</big></th>				
						<th></th>
					</tr>

				</thead>
				<tbody>
					<tr onclick="manageDefaultOfferte()" class="btn-default-quote">
						<td >
						<span>Standaard regels aanpassen voor elke nieuwe offerte</span>
						
						</td>
						<td>
							<div  class="actiebutton" data-position="top" ><i class="material-icons">chevron_right</i></div>
						</td>
					</tr>

					<tr onclick="manageDefaultOfferte(2)" class="btn-default-quote">
						<td >
						<span>Standaard regels aanpassen voor elke nieuwe offerte (STABU)</span>
						
						</td>
						<td>
							<div  class="actiebutton" data-position="top" ><i class="material-icons">chevron_right</i></div>
						</td>
					</tr>

					<tr onclick="manageDefaultQuoteIntro()" class="btn-default-quote">
						<td >
						<span>Standaard introductietekst offertes</span>
						
						</td>
						<td>
							<div  class="actiebutton" data-position="top" ><i class="material-icons">chevron_right</i></div>
						</td>
					</tr>

					<tr onclick="manageDefaultQuoteVoor()" class="btn-default-quote">
						<td >
						<span>Standaard voorwaarden offertes</span>
						
						</td>
						<td>
							<div  class="actiebutton" data-position="top" ><i class="material-icons">chevron_right</i></div>
						</td>
					</tr>

					<tr onclick="manageTagForOfferte()" class="btn-default-quote">
						<td >
						<span>Tags voor offerte regels</span>
						
						</td>
						<td>
							<div  class="actiebutton" data-position="top" ><i class="material-icons">chevron_right</i></div>
						</td>
					</tr>
				</tbody>
			</table>


			

		

		</div>			

	

	<script src="<?=$root;?>js/settings.js" type="text/javascript"></script>	

	

<?php include 'common/footer.php'; ?>
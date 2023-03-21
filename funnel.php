<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'funnel';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Funnel - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;



if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.created_date, CL.entry_type, CL.entry_date FROM contacts C LEFT JOIN (SELECT contact_id, entry_type, entry_date FROM contact_log WHERE entry_date IN ( SELECT MAX(entry_date) FROM contact_log WHERE entry_title = "contact" GROUP BY contact_id)) CL ON (C.id = CL.contact_id) WHERE C.c_status != 3')) {	

	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		<input hidden class="row_name" value="funnel"/>
		<input hidden class="row_table_id" value="<?=isset($_GET['id']) ? $_GET['id']: ''?>"/>
			<div class="titlebar">
				<div class="titlebarcontainer withbutton">
					<h2>Funnel
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Contacten worden handmatig toegevoegd aan dit scherm. Door de status van een contact aan te passen naar actief of inactief (gebruik hiervoor het potloodje), wisselt deze van scherm. Als de status ingesteld staat op 'lead', verdwijnt het contact uit dit scherm."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
									<li class="tab col"><a class="active" href="#actievecontacten">Actieve contacten</a></li>
									<li class="tab col"><a href="#inactievecontacten">Inactieve contacten</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addContact()"><i class="material-icons">add</i> Toevoegen</span>
				<div style="clear:both"></div>
			</div>			

			<div id="actievecontacten" class="tab-content active">			
				<table class="contacten-table full-w-table sort-table">
					<thead>
					<tr>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th>E-mailadres</th>
						<th>Telefoonnummer</th>
						<th>Bron</th>
						<th style="display: none;">Status</th>
						<th class="datum-column">Datum</th>
						<th>Contactwijze</th>
						<th>Timer</th>
						<th style="width:190px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php if ($result) : while ($row = $result->fetch_assoc()) : if ($row['c_status'] == 1) : ?>

						<tr contactrow="<?=$row['id'];?>" id="funnel<?=$row['id'];?>">
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['email'];?></td>
							<td><?=$row['phone'];?></td>
							<td  class='source-column'><?=getSourceType($row['source']);?>
								<?php
									if($row['source'] == 6 || $row['source'] == 7)
									{
										echo '<div onclick="report_problems(' . $row['id'] . ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Report"><i class="material-icons">report_problems</i></div>';
									}
								?>
							</td>
							<td style="display: none;"><?=$row['c_status'];?></td>
							<td><?=getFDate($row['created_date']);?></td>
							<td><?=getContactType($row['entry_type']);?></td>
							<td><?=getTimer($row['entry_date']);?></td>
							<td>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteContact(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	

					<?php endif; endwhile; endif; ?>

					</tbody>
				</table>
			</div>

			<div id="inactievecontacten" class="tab-content">
				<table class="contacten-table full-w-table sort-table">
					<thead>
					<tr>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th>E-mailadres</th>
						<th>Telefoonnummer</th>
						<th>Bron</th>
						<th style="display: none;">Status</th>
						<th class="datum-column">Datum</th>
						<th>Contactwijze</th>
						<th>Timer</th>
						<th style="width:190px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>

				<?php if ($result) : mysqli_data_seek ($result, 0); while ($row = $result->fetch_assoc()) : if ($row['c_status'] == 2) :  ?>

					<tr contactrow="<?=$row['id'];?>" id="funnel<?=$row['id'];?>">
						<td><?=$row['name'];?></td>
						<td><?=$row['city'];?></td>
						<td><?=$row['address'];?></td>
						<td><?=$row['email'];?></td>
						<td><?=$row['phone'];?></td>
						<td><?=getSourceType($row['source']);?></td>
						<td style="display: none;"><?=$row['c_status'];?></td>
						<td><?=getFDate($row['created_date']);?></td>
						<td><?=getContactType($row['entry_type']);?></td>
						<td><?=getTimer($row['entry_date']);?></td>
						<td>
							<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
							<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
							<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
							<div onclick="deleteContact(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
						</td>
					</tr>	

					<?php endif; endwhile; endif; ?>

					</tbody>
				</table>
			</div>
		</div>		

	<script src="<?=$root;?>js/funnel.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>
<?php include 'common/footer.php'; ?>
<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'leads';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Leads - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>
	

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;


function getPrioSelect($contact_id, $value)
{
	$html = "<select onChange='changedPrio(" . $contact_id . ")' class='select-prio'><option value=0>N.v.t</option>";
	for($index = 1; $index <= 20; $index ++)
	{
		$html .= "<option value=" . $index . " " . ($value == $index ? "selected" : "") .">" . $index . "</option>";
	}
	$html .= "</select>";
	return $html;
}

if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, C.prio, CL.entry_type, CL.entry_date, Q.latest_quote_date, Q.pdf_file FROM contacts C LEFT JOIN (SELECT contact_id, entry_type, entry_date FROM contact_log WHERE entry_date IN ( SELECT MAX(entry_date) FROM contact_log WHERE entry_title = "lead" GROUP BY contact_id)) CL ON (C.id = CL.contact_id) 
	LEFT JOIN (SELECT contact_id, pdf_file, MAX(quote_date) as latest_quote_date From quotes GROUP BY contact_id) Q ON (C.id = Q.contact_id)
	 WHERE C.c_status = 3 ORDER BY id')) {	

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
			<input hidden class="row_name" value="leads"/>
			<input hidden class="row_table_id" value="<?=isset($_GET['id']) ? $_GET['id']: ''?>"/>
			<div class="titlebar">
				<div class="titlebarcontainer">
					<h2>Leads
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Een contact komt in dit scherm als de status van een contact in het funnel-scherm handmatig aangepast wordt naar Lead. Door in het logboek de status aan te passen naar 'Geen deal', verschuift een contact naar 'Inactieve Leads'. Elke andere status zet het contact weer terug naar 'Actieve leads'. Door de status aan te passen naar 'Deal', verdwijnt het contact uit dit scherm."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
									<li class="tab col"><a class="active" href="#actieveleads">Actieve leads</a></li>
									<li class="tab col"><a href="#inactieveleads">Inactieve leads</a></li>
									<li class="col"><a href="/salesplanning">Salesplanning</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>				
				<div style="clear:both"></div>
			</div>			

			<div id="actieveleads" class="tab-content active">			
				<table class="contacten-table lead-table full-w-table sort-table prio-table">
					<thead>
					<tr>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th>E-mailadres</th>
						<th>Telefoonnummer</th>
						<th>Offerte</th>
						<th>Status</th>	
						<th class="no-sort">Prio</th>					
						<th>Timer</th>
						<th style="width:230px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php if ($result) : while ($row = $result->fetch_assoc()) : 

						if ((isset($row['l_status']) && ( $row['l_status'] != 2) && ( $row['l_status'] != 1))) :  // if not 'Geen deal'
							$status = ($row['l_status'] && $row['l_status'] != 5) ? LEAD_TYPE[$row['l_status']] : '';
							if($row['l_status'] == 5)
							{	$status = "Afspraak";
								$query = "SELECT * 
									FROM sales_meeting 
									WHERE date > NOW() AND contact = ?
									ORDER BY date, time_from DESC LIMIT 1
								";
								$stmt_new = $con->prepare($query);
								$stmt_new->bind_param('i', $row['id']);
								$stmt_new->execute();
								// Store the result so we can check if the account exists in the database.
								$result_new = $stmt_new->get_result();
								while ($row_new = $result_new->fetch_assoc())
								{
									$status .= ' (' . ( (new DateTime($row_new['date'])) -> format('d-m-Y')) . ')';
								}
							}
							
					?>

						<tr contactrow="<?=$row['id'];?>" id="leads<?=$row['id'];?>">
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['email'];?></td>
							<td><?=$row['phone'];?></td>							
							<td><?=isset($row['latest_quote_date']) ? (new DateTime($row['latest_quote_date']))->format('d-m-Y') . "<a class='q-pdf' target='_blank' href='" . $root . "pdf/" . $row['pdf_file'] . "'><img class='pdf-icon' src = '" . $root . "images/pdf.svg'/></a>" : 'Nog te versturen' ?></td>
							<td><?=$status?></td>
							<td><?=getPrioSelect($row['id'], $row['prio'])?></td>
							<td><?=getTimer($row['entry_date']);?></td>
							<td>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="manageQuoteList(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Offerte"><i class="material-icons">insert_drive_file</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteLead(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	

					<?php endif; endwhile; endif; ?>

					</tbody>
				</table>
			</div>

			<div id="inactieveleads" class="tab-content">
				<table class="contacten-table lead-table full-w-table sort-table prio-table">
					<thead>
					<tr>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th>E-mailadres</th>
						<th>Telefoonnummer</th>
						<th>Offerte</th>
						<th>Status</th>		
						<th  class="no-sort">Prio</th>				
						<th>Timer</th>
						<th style="width:230px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>

				<?php if ($result) : mysqli_data_seek ($result, 0); while ($row = $result->fetch_assoc()) : 
					if ((isset($row['l_status']) && ( $row['l_status'] == 2))) :  // if 'Geen deal'
				?>

					<tr contactrow="<?=$row['id'];?>" id="leads<?=$row['id'];?>">
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['email'];?></td>
							<td><?=$row['phone'];?></td>							
							<td><?=isset($row['latest_quote_date']) ? (new DateTime($row['latest_quote_date']))->format('d-m-Y') . "<a class='q-pdf'  target='_blank' href='" . $root . "pdf/" . $row['pdf_file'] . "'><img class='pdf-icon' src = '" . $root . "images/pdf.svg'/></a>" : 'Nog te versturen' ?></td>
							<td><?=LEAD_TYPE[$row['l_status']];?></td>
							<td><?=getPrioSelect($row['id'], $row['prio'])?></td>
							<td><?=getTimer($row['entry_date']);?></td>
							<td>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="manageQuoteList(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Offerte"><i class="material-icons">insert_drive_file</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteLead(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>

					<?php endif; endwhile; endif; ?>

					</tbody>
				</table>
			</div>
		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	<!-- <script src="<?=$root;?>js/funnel.js" type="text/javascript"></script> -->
	<script src="<?=$root;?>js/lead.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/offerte.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>
<?php include 'common/footer.php'; ?>
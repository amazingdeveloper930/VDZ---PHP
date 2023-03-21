<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'aftersales';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>After Sales - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>


<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;
$result_array = [];
if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, O.project_number, O.convert_date, O.sale_date, O.p_status,O.m_status4, O.m_status5, O.m_status6, O.m_status7 FROM contacts C 
    LEFT JOIN projects O ON (C.id = O.contact_id)
    WHERE C.c_status = 3 AND C.l_status = 1  AND O.as_status = 1 AND O.plaatsing = "nee" ORDER BY O.id ASC')) {	

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
    $result_array = [];
    while ($row = $result->fetch_assoc())
    {
        $row['timer_4'] = getSaleTimer($row['sale_date'], $row['m_status4']);
        $row['timer_5'] = getSaleTimer($row['sale_date'], $row['m_status5']);
		$row['timer_6'] = getSaleTimer($row['sale_date'], $row['m_status6']);
        $row['timer_7'] = getSaleTimer($row['sale_date'], $row['m_status7']);
        $difTime = dateDifferenceW( $row['sale_date']);
        $result_array []= $row;
    }
    
}

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		<input hidden class="row_name" value="aftersales"/>
		<input hidden class="row_table_id" value="<?=isset($_GET['id']) ? $_GET['id']: ''?>"/>
        <div class="titlebar">
				<div class="titlebarcontainer">
					<h2>After Sales
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Een project kom in dit scherm terecht zodra 'Geplaatst?' wordt aangepast naar 'Ja'. Zodra 'Opgeleverd?' ook ingesteld staat op 'Ja' komt het onder de tab 'Opgeleverde opdrachten' te staan."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
									<li class="tab col"><a  class="active" href="#actieveopdracbten">Actieve opdrachten</a></li>
									<li class="tab col"><a href="#inactieveopdracbten">Opgeleverde opdrachten</a></li>
									<li class="tab col"><a  href="#opentickets">Open tickets</a></li>
									<li class="tab col"><a  href="#allopentickets">Alle open tickets</a></li>
								</ul>
							</div>
						</div>
					</div>	
					<div style="clear:both"></div>
				</div>		
					
				<div style="clear:both"></div>
			</div>					

			<div id="actieveopdracbten" class="tab-content active aftersales-projects">		
                
				<table class="contacten-table full-w-table sort-table" id="project_normal">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>				
						<th>Bloemmetje en kaart</th>
                        <th>Service opmerkingen</th>
						<th>Review</th>
                        <th>Foto's op website, FB en Insta</th>
						<th style="width:350px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				
                        <?php 
                         foreach($result_array as $row){
                             ?>

						<tr contactrow="<?=$row['id'];?>" id="aftersales<?=$row['id'];?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							
                            <?php
                            for($index = 4; $index <= 7; $index ++){
                                if($row['m_status' . $index] == 'YES' || $row['m_status' . $index] == 'SKIP')
                                {
                                    echo "<td>" . $row['timer_' . $index] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_' . $index] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>done</i></span><span class='button btn btn-skip' onclick='skipProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>close</i></span></td>";
                                }
                            }
							?>
                            
							<td>
								<div onclick="manageTickets(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Tickets"><i class="material-icons">report_problem</i></div>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageProjectTask(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="manageQuoteList(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Offerte"><i class="material-icons">insert_drive_file</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteContact(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	
                        <?php 
                         }?>
					</tbody>
				</table>
            </div>


			<div id="inactieveopdracbten" class="tab-content aftersales-projects">
			<?php

				$result = null;
				$result_array = [];

				if ($stmt = $con->prepare(
					'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, O.project_number, O.convert_date, O.sale_date, O.p_status,O.m_status4, O.m_status5, O.m_status6, O.m_status7 FROM contacts C 
					LEFT JOIN projects O ON (C.id = O.contact_id)
					WHERE C.c_status = 3 AND C.l_status = 1  AND O.as_status = 1 AND O.plaatsing = "ja" ORDER BY O.id ASC')) {	

					//$stmt->bind_param('i', 3); // only lead
					$stmt->execute();
					// Store the result so we can check if the account exists in the database.
					$result = $stmt->get_result();
					
					while ($row = $result->fetch_assoc())
					{
						$row['timer_4'] = getSaleTimer($row['sale_date'], $row['m_status4']);
						$row['timer_5'] = getSaleTimer($row['sale_date'], $row['m_status5']);
						$row['timer_6'] = getSaleTimer($row['sale_date'], $row['m_status6']);
						$row['timer_7'] = getSaleTimer($row['sale_date'], $row['m_status7']);
						$difTime = dateDifferenceW( $row['sale_date']);
						$result_array []= $row;
					}
					
				}

			?>
                
				<table class="contacten-table full-w-table sort-table" id="project_inactive">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>				
						<th>Bloemmetje en kaart</th>
                        <th>Service opmerkingen</th>
						<th>Review</th>
                        <th>Foto's op website, FB en Insta</th>
						<th style="width:350px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				
                        <?php 
                         foreach($result_array as $row){
                             ?>

						<tr contactrow="<?=$row['id'];?>" id="aftersales<?=$row['id'];?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							
                            <?php
                            for($index = 4; $index <= 7; $index ++){
                                if($row['m_status' . $index] == 'YES' || $row['m_status' . $index] == 'SKIP')
                                {
                                    echo "<td>" . $row['timer_' . $index] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_' . $index] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>done</i></span><span class='button btn btn-skip' onclick='skipProject(" . $row['id'] . ", \"MODE" . $index . "\")'><i class='material-icons'>close</i></span></td>";
                                }
                            }
							?>
                            
							<td>
								<div onclick="manageTickets(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Tickets"><i class="material-icons">report_problem</i></div>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageProjectTask(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="manageQuoteList(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Offerte"><i class="material-icons">insert_drive_file</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteContact(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	
                        <?php 
                         }?>
					</tbody>
				</table>

            </div>

			<div id="opentickets" class="tab-content aftersales-projects">
			<?php

					$result = null;
					$result_array = [];

					if ($stmt = $con->prepare(
						'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, O.project_number, O.convert_date, O.sale_date, O.p_status FROM contacts C 
						LEFT JOIN projects O ON (C.id = O.contact_id)
						JOIN (SELECT count(id) AS ticket_count, contact_id FROM projects_tickets WHERE status = "OPENED" GROUP BY contact_id ) PTC ON C.id = PTC.contact_id
						WHERE C.c_status = 3 AND C.l_status = 1  AND O.as_status = 1   ORDER BY O.id ASC')) {	

						//$stmt->bind_param('i', 3); // only lead
						$stmt->execute();
						// Store the result so we can check if the account exists in the database.
						$result = $stmt->get_result();
						
						while ($row = $result->fetch_assoc())
						{
							$row['timer_ticket'] = getTicketTimer(null, 'CLOSED');
							$stmt_new = $con -> prepare('SELECT PT.datum FROM projects_tickets PT WHERE PT.contact_id = ? AND PT.status="OPENED" ORDER BY PT.datum ASC LIMIT 1');
							$stmt_new->bind_param('s', $row['id']);
							$stmt_new->execute();
							$result_new = $stmt_new->get_result();
							while($row_new = $result_new -> fetch_assoc()) {
								$row['timer_ticket'] = getTicketTimer($row_new['datum'], 'OPENED');
							}
							// $row['timer_widget'] = getTicketTimer($row['datum'], $row['status']);		
							$result_array []= $row;
						}
						
					}

?>
				<table class="contacten-table full-w-table sort-table" id="project_opened">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>				
						<th>E-mailadres</th>
                        <th>Telefoonnummer</th>
						<th>Timer</th>
						<th style="width:350px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				
                        <?php 
                         foreach($result_array as $row){
                             ?>

						<tr ticket_contact_row="<?=$row['id'];?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['email'];?></td>
							<td><?=$row['phone'];?></td>
							<td><?=$row['timer_ticket'];?></td>
                            
                            
							<td>
								<div onclick="manageTickets(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Tickets"><i class="material-icons">report_problem</i></div>
								<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="manageProjectTask(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								<div onclick="manageQuoteList(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Offerte"><i class="material-icons">insert_drive_file</i></div>
								<div onclick="editContact(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
								<div onclick="deleteContact(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	
                        <?php 
                         }?>
					</tbody>
				</table>	 
			</div>

			<div id="allopentickets" class="tab-content">
			  <?php 
				$result = null;
				$result_array = [];

				if ($stmt = $con->prepare(
					'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, O.project_number, O.convert_date, O.sale_date, O.p_status FROM contacts C 
					LEFT JOIN projects O ON (C.id = O.contact_id)
					JOIN (SELECT count(id) AS ticket_count, contact_id FROM projects_tickets WHERE status = "OPENED" GROUP BY contact_id ) PTC ON C.id = PTC.contact_id
					WHERE C.c_status = 3 AND C.l_status = 1  AND O.as_status = 1 AND PTC.ticket_count > 0  ORDER BY O.id ASC')) {	

					//$stmt->bind_param('i', 3); // only lead
					$stmt->execute();
					// Store the result so we can check if the account exists in the database.
					$result = $stmt->get_result();
					
					while ($row = $result->fetch_assoc())
					{
						$row['tickets'] = [];
						$sql = 'SELECT PT.*, E.name, A.username From projects_tickets PT LEFT JOIN employees E ON PT.employee = E.id LEFT JOIN accounts A ON PT.user = A.id WHERE PT.contact_id = ? AND PT.status = "OPENED"  ORDER BY PT.id';
						
						$stmt_new = $con -> prepare($sql);
						$stmt_new->bind_param('s', $row['id']);
						$stmt_new->execute();
						$result_new = $stmt_new->get_result();
						while($row_new = $result_new -> fetch_assoc()) {
							

							$stmt_1 = $con -> prepare('SELECT WM.employee_id, WB.datum, E.name, E.teamleader FROM werkplanning_medewerker WM LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id LEFT JOIN employees E ON WM.employee_id = E.id WHERE WM.ticket_id = ?');
							$stmt_1 -> bind_param('i', $row_new['id']);
							$stmt_1 -> execute();
							$result_1 =  $stmt_1->get_result();
							$row_1 = $result_1 -> fetch_assoc();
							if($row_1)
							{
								$row_new['name'] = $row_1['name'];
								$row_new['plan_datum'] = $row_1['datum'];
							}
							while($row_1 = $result_1 -> fetch_assoc())
							{
							   if($row_1['teamleader'] != 0){
									$row_new['name'] = $row_1['name'];
									$row_new['plan_datum'] = $row_1['datum'];
							   }
							}

							$stmt_1 = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tickets_notes WHERE ticket_id = ?');
							$stmt_1->bind_param('i', $row_new['id']);
							$stmt_1->execute();
							$result_1 =  $stmt_1->get_result();
							while($row_1 = $result_1 -> fetch_assoc())
							{
								$row_new['note_count'] = $row_1['note_count'];
							}
						
						
						
							$row_new['timer_widget'] = getTicketTimer($row_new['datum'], $row_new['status']);
							$row['tickets'] []= $row_new;
						}
						// $row['timer_widget'] = getTicketTimer($row['datum'], $row['status']);
								
						$result_array []= $row;
					}
					
				}
			  ?>
			  <table class="project-ticket-table full-w-table">
				<tbody>
				<?php 

					foreach($result_array as $row)
					{
						$html = "<tr>" . 
									"<th colspan='2'>" . $row['name'] . " - " . $row['address'] . "</th>" . 
									"<th>Datum</th>" . 
									"<th>Medewerker</th>" . 
									"<th>Ingepland</th>" .
									"<th>Inkoop besteld</th>" .
									"<th>Bedrag open</th>" .
									"<th></th>".
									"<th style='width:100px'>Timer</th>" .
									"<th style='width:200px'></th>" .
								"</tr>";
						echo $html;
						foreach($row['tickets'] as $ticket)
						{
							$html = "<tr class='project_ticket ticket_opened' ticket_row='" . $ticket['id'] . "'><td>" . $ticket['title'] . "</td>". 
									"<td>" . "<span class='button btn btn-sluten' onclick='closeTableTicket(" . $ticket['id'] . ")'><i class='material-icons'>done</i> Sluiten</span>" . "</td>".
									"<td>" . getFDate($ticket['datum']) . "</td>" . 
									"<td>" . ($ticket['name'] ?? 'N.v.t') . "</td>" . 
									"<td>" . (isset($ticket['plan_datum']) ? getFDate($ticket['plan_datum']) : 'N.v.t') . "</td>" .
									"<td>" . ($ticket['inkoop_besteld'] ? getFDate($ticket['inkoop_besteld']) : 'N.v.t') . "</td>" .
									"<td>" . ($ticket['bedrag_open'] ?? 'N.v.t') . "</td>" . 
									"<td></td>".
									"<td>" . $ticket['timer_widget'] .  "</td>".
									"<td>" .
									"<div class='actiebutton' data-position='top' onclick='gotoWeekplanning(" . $row['id'] . ", " . $ticket['id'] . ")' data-tooltip='Tickets'><i class='material-icons'>build</i></div>" . 
									"<div class='actiebutton' data-position='top' onclick='manageTickets(" . $row['id'] . ")' data-tooltip='Tickets'><i class='material-icons'>report_problem</i></div>" . 
									"<div class='actiebutton' data-position='top' onclick='editTableTicket(" . $ticket['id'] . ")'><i class='material-icons'>edit</i></div>" .
									"<div class='actiebutton  icon_ticketnote " . ($ticket['note_count'] > 0 ? 'hasnote' : '') . "' data-position='top' onclick='ticketNotes(" . $ticket['id'] . ")'><i class='material-icons'>insert_comment</i></div>" . "</td>".
							"</tr>";
							echo $html;
							$html = "<tr class='project_ticket_note' ticket_row='" . $ticket['id'] . "'><td colspan='10'></td></tr>";
							echo $html;
						}
						$html = "<tr class='blank_row'><td colspan=10></td></tr>";
						echo $html;
					}
				?>
				</tbody>
			</table>
			</div>
			
		</div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	<!-- <script src="<?=$root;?>js/funnel.js" type="text/javascript"></script> -->
	<script src="<?=$root;?>js/aftersales.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/offerte.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>
<?php include 'common/footer.php'; ?>

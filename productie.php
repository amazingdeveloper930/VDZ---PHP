<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'productie';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Productie - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;

if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, O.project_number, O.convert_date, O.startdatum, O.p_status,O.m_status1, O.m_status2, O.m_status3 FROM contacts C 
    LEFT JOIN projects O ON (C.id = O.contact_id)
    WHERE C.c_status = 3 AND C.l_status = 1  AND O.plaatsing = "nee" AND O.startdatum is not NULL ORDER BY O.startdatum ASC')) {	

	/*
SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, O.project_number, O.convert_date, O.startdatum, O.p_status,O.m_status1, O.m_status2, O.m_status3 FROM contacts C 
    LEFT JOIN projects O ON (C.id = O.contact_id)
    WHERE C.c_status = 3 AND C.l_status = 1 AND O.p_status = 1 AND O.geplaatst = "nee" ORDER BY O.startdatum ASC
	*/

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
    $result_array_urgent = [];
    $result_array_normal = [];
    while ($row = $result->fetch_assoc())
    {
        $row['timer_1'] = getProjectTimer($row['startdatum'], $row['m_status1']);
        $row['timer_2'] = getProjectTimer($row['startdatum'], $row['m_status2']);
		$row['timer_3'] = getProjectTimer($row['startdatum'], $row['m_status3']);
        $difTime = dateDifferenceW( $row['startdatum'], 1);
		$difDay = dateDifferenceD($row['startdatum'], 1);
        // if($difTime >= 2)
        //     $result_array_normal []= $row;
        // else
        //     $result_array_urgent []= $row;

		if($difDay <= 0)
            $result_array_urgent []= $row;
        else if($difTime <= 3)
            $result_array_normal []= $row;
    }
    
}

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		<input hidden class="row_name" value="productie"/>
		<input hidden class="row_table_id" value="<?=isset($_GET['id']) ? $_GET['id']: ''?>"/>
        <div class="titlebar">
				<div class="titlebarcontainer">
					<h2>Productie
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Een project komt in de onderste tabel in dit scherm terecht, 8 weken voor de 'startdatum'. Na 4 weken staat het project in de bovenste tabel. Een project verlaat dit scherm zodra 'Geplaatst?' ingesteld staat op 'Ja' in de taken popup."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
									<li class="tab col"><a  href="#projecten">Projecten</a></li>
									<li class=" col"><a href="/weekplanning">Medewerker planning</a></li>
									<li class=" col"><a href="/werkplanning">Projectplanning</a></li>									<li class=" col"><a href="/jaarplanning">Jaarplanning</a></li>
									<li class="tab col"><a href="#opentickets">Open tickets</a></li>
									
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>				
				<div style="clear:both"></div>
			</div>					

			<div id="projecten" class="tab-content active">		
                <?php 
                if(count($result_array_urgent)){
                   
                    ?>
                
				<table class="contacten-table full-w-table sort-table" id="project_normal">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th class="datum-column">Startdatum</th>					
						<th>Gevel binnen?</th>
                        <th>Betonplaat binnen?</th>
						<th>Kozijnen binnen?</th>
						<th style="width:350px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				
                        <?php 
                         foreach($result_array_urgent as $row){
                             ?>

						<tr contactrow="<?=$row['id'];?>" id="productie<?=$row['id'];?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['startdatum'] && $row['startdatum'] != '	
0000-00-00' ?(new DateTime($row['startdatum'])) -> format('d-m-Y'):'<span class="text-red">Nog niet gepland</span>'?></td>      
                            <?php
                                if($row['m_status1'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_1'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE1\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_1'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE1\")'><i class='material-icons'>done</i> Ja</span></td>";
                                }

							?>
                            
                            <?php
                                if($row['m_status2'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_2'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE2\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_2'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE2\")'><i class='material-icons'>done</i> Ja</span></td>";
                                }

							?>


							<?php	
                                if($row['m_status3'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_3'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE3\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_3'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE3\")'><i class='material-icons'>done</i> Ja</span></td>";
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
                <?php
                    
                }
                if(count($result_array_normal)){
                    ?>
                <br/>
                <h2>Komt er nog aan..</h2>
                <table class="contacten-table full-w-table sort-table" id="project_normal">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th width="200px">Adres</th>
						<th class="datum-column">Startdatum</th>					
						<th>Gevel binnen?</th>
                        <th>Betonplaat binnen?</th>
						<th>Kozijnen binnen?</th>
						<th style="width:350px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				
                        <?php 
                         foreach($result_array_normal as $row){
                             ?>

						<tr contactrow="<?=$row['id'];?>" id="productie<?=$row['id'];?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['startdatum'] && $row['startdatum'] != '	
0000-00-00' ?(new DateTime($row['startdatum'])) -> format('d-m-Y'):'<span class="text-red">Nog niet gepland</span>'?></td>
							<?php
                                if($row['m_status1'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_1'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE1\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_1'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE1\")'><i class='material-icons'>done</i> Ja</span></td>";
                                }

							?>
                            
                            <?php
                                if($row['m_status2'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_2'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE2\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_2'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE2\")'><i class='material-icons'>done</i> Ja</span></td>";
                                }

							?>

							<?php	
                                if($row['m_status3'] == 'YES')
                                {
                                    echo "<td>" . $row['timer_3'] . "<div class='actiebutton btn-refresh' onclick='restartProject(" . $row['id'] . ", \"MODE3\")'><i class='material-icons'>refresh</i></div></td>";
                                }
                                else{
                                    echo "<td>" . $row['timer_3'] . "<span class='button btn btn-complete' onclick='completeProject(" . $row['id'] . ", \"MODE3\")'><i class='material-icons'>done</i> Ja</span></td>";
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
                <?php
                }
                ?>
            </div>
            <div  id="weekplanning" class="tab-content">
            </div>
			<div id="opentickets" class="tab-content aftersales-projects">
			<?php

					$result = null;
					$result_array = [];

					if ($stmt = $con->prepare(
						'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, O.project_number, O.convert_date, O.sale_date, O.p_status FROM contacts C 
						LEFT JOIN projects O ON (C.id = O.contact_id)
						JOIN (SELECT count(id) AS ticket_count, contact_id FROM projects_tickets WHERE status = "OPENED" GROUP BY contact_id ) PTC ON C.id = PTC.contact_id
						WHERE C.c_status = 3 AND C.l_status = 1  AND O.plaatsing = "nee" AND O.startdatum is not NULL   ORDER BY O.id ASC')) {	

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
		</div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	<!-- <script src="<?=$root;?>js/funnel.js" type="text/javascript"></script> -->
	<script src="<?=$root;?>js/productie.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/offerte.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
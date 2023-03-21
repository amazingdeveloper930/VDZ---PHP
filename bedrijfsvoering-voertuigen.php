<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'bedrijfsvoering';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Bedrijfsvoering - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$stmt = $con -> prepare("SELECT V.*, E.name AS employee_name, VC.note_count FROM vehicle V LEFT JOIN employees E ON V.employee = E.id
LEFT JOIN (SELECT COUNT(id) AS note_count, vehicle_id AS v_id FROM vehicles_notes GROUP BY vehicle_id) VC
ON V.id = VC.v_id
 ORDER BY V.id");
$stmt -> execute();

$result = $stmt -> get_result();

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
			<div class="titlebar">
				<div class="titlebarcontainer withbutton">
					<h2>Bedrijfsvoering
						
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Dit scherm toont de overzichten voor het beheren van personeel, voertuigen, materiaal, leveranciers en elektronica."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
								<?php 
									if($_SESSION['ac_level'] == 1)
									{
										echo '<li class=" col"><a href="/bedrijfsvoering">Personeel</a></li>';
									}
									?>
									<li class="tab col"><a class="active" href="#">Voertuigen</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-materiaal">Materiaal</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-leveranciers">Leveranciers</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-laptops">Laptops + telefoons</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-vacatures">Vacatures</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addNewVehicle()"><i class="material-icons">add</i> Toevoegen</span>
				<div style="clear:both"></div>
			</div>			

			<div id="vehicle-panel" class="tab-content active">			
				<table class="vehicle-table full-w-table sort-table-group">
					<thead>
					<tr>
						<th>Kenteken</th>
						<th class="no-sort">Afbeelding</th>
						<th>In gebruik door</th>
						<th>Merk</th>
						<th>Uitvoering</th>
						<th>Zitplaatsen</th>
						<th>APK datum</th>
                        <th>Lease maatschappij</th>
                        <th>Lease start</th>
                        <th>Lease eind</th>
						<th style="width:200px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php if ($result) : while ($row = $result->fetch_assoc()) :
						
						if($row['employee'] == 0)
        					$row['employee_name'] = 'Algemeen';
						$img_path = "vehicle.png";
							if(isset($row['file_path']))
								$img_path = $row['file_path'];

						?>

						<tr vehiclerow="<?=$row['id'];?>" class="vehiclerow">
							<td><?=$row['kenteken'];?></td>
							<td>
							<?php
								if(isset($row['file_path']))
									echo "<img onclick='openPrev(\"" . $img_path . "\")' src='" . $root . "upload/" . $row['file_path'] . "'/>";
								else
									echo "<img src='" . $root . "images/users/vehicle.png" . "' />";
							?>
							</td>
							<td><?=$row['employee_name'];?></td>
							<td><?=$row['merk'];?></td>
							<td><?=$row['uitvoering'];?></td>
							<td><?=$row['zitplaatsen'];?></td>
							<td><?=dateFormat($row['apkdatum']);?></td>
							<td><?=$row['lease_maatschappij'];?></td>
							<td><?=dateFormat($row['lease_start']);?></td>
							<td><?=dateFormat($row['lease_eind']);?></td>
							<td>
								<div onclick="manageVehicleFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
								<div onclick="vehicleNote(<?=$row['id'];?>)" class="actiebutton tooltipped icon_vehiclenote
								<?php 

									if($row['note_count'] > 0)
									echo 'hasnote';
								?>
								" data-position="top" data-tooltip="Comment"><i class="material-icons">insert_comment</i></div>
								<div onclick="editVehicle(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
				
								<div onclick="deleteVehicle(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	
						<tr class="row_vehicle_note" vehicle_id="<?=$row['id'];?>">

						</tr>

					<?php endwhile; endif; ?>

					</tbody>
				</table>
			</div>
		</div>		

	<script src="<?=$root;?>js/bedrijfsvoering.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>

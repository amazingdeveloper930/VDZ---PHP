<?php 

require 'common/sessie_check.php';
require 'common/global.php';


$currentpage = 'bedrijfsvoering';
if($_SESSION['ac_level'] != 1)
{
	header('Location: ' . $root . "bedrijfsvoering-voertuigen");
	exit();
}

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Personeel - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

		

</head>



<?php 



require( 'common/connection.php');



// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

if ($stmt = $con->prepare('SELECT E.*, E1.name as teamleader_name, VC.note_count FROM employees E LEFT JOIN (SELECT id, name FROM employees) E1 ON E.teamleader = E1.id 
LEFT JOIN (SELECT COUNT(id) AS note_count, employee_id AS v_id FROM employees_notes GROUP BY employee_id) VC
ON E.id = VC.v_id
ORDER BY sort_order')) {	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	

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
									<li class="tab col"><a class="active" href="#">Personeel</a></li>
									<li class=" col"><a href="/bedrijfsvoering-voertuigen">Voertuigen</a></li>
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

				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addEmployee()"><i class="material-icons">add</i> Toevoegen</span>

				<div style="clear:both"></div>

			</div>

			


			<table id="employee-list" class="contacten-table full-w-table sort-table-group">
				<thead>
					<tr>
						<th class='employee-name'>Voornaam</th>
						<th class="employee-achternaam">Achternaam</th>
						<th class="employee-woonadres_nl">Woonadres NL</th>
						<th class="employee-visa">Visa</th>
						<th class="employee-photo">Afbeelding</th>
						<th class="employee-specialisme">Specialisme</th>
						<th class="employee-type">Afdeling</th>
						<th class="employee-teamleader">Teamleider</th>
						<th class="employee-aankomst_datum datum-column">Indiensttreding</th>
						<th class="employee-vertrek_datum datum-column">Einde contract</th>
						<th class="employee-weekplanning">Planning?</th>
						<th style="width: 250px" class="no-sort"></th>
					</tr>
				</thead>
				<tbody>
				<?php 
					while ($row = $result->fetch_assoc()) { 
						$img_path = "employee.png";
						if(isset($row['file_path']))
							$img_path = $row['file_path'];
						?>
				<tr class="employee-item" employeerow="<?=$row['id'];?>">
					<td class='employee-name'><?=$row['name'];?></td>
					<td class='employee-achternaam'><?=$row['achternaam'];?></td>
					<td class='employee-woonadres_nl'><?=$row['woonadres_nl'];?></td>
					<td class='employee-visa'><?=$row['visa'];?></td>
					<td class='employee-photo'>
						<?php
							if(isset($row['file_path']))
								echo "<img src='" . $root . "upload/" . $row['file_path'] . "' onclick='openPrev(\"" . $img_path . "\")'/>";
							else
							echo "<img src='" . $root . "images/users/employee.png" . "'/>";
						?>
					</td>
					<td class='employee-specialisme'><?=$row['specialisme'];?></td>
					<td class='employee-type'><?=$row['type'];?><div class='employee-type-identify employee-type-i-<?=$row['type'];?>'></div></td>
					<td class='employee-teamleader'>
						<?php 
							if($row['teamleader'] == 0)
								echo 'Is teamleider';
							else{
								echo $row['teamleader_name'];
							}
						?>
					</td>
					<td class='employee-aankomst_datum'><?=isset($row['aankomst_datum'])? dateFormat($row['aankomst_datum']) : '';?></td>
					<td class='employee-vertrek_datum'><?=isset($row['vertrek_datum'])? dateFormat($row['vertrek_datum']) : '';?></td>
					<td class="employee-weekplanning"><?=$row['inweekplanning']?></td>
					<td class="actionbuttons">

						<div onclick="manageEmployeeWorkingDate(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Beschikbaarheid"><i class="material-icons">date_range</i></div>
						<div onclick="manageEmployeeFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>

						<div onclick="employeeNote(<?=$row['id'];?>)" class="actiebutton tooltipped icon_employeenote
						<?php 

							if($row['note_count'] > 0)
							echo 'hasnote';
						?>
						" data-position="top" data-tooltip="Comment"><i class="material-icons">insert_comment</i></div>
					 	<div onclick="editEmployee(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>

						<div onclick="deleteEmployee(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
					</td>
					
				</tr>
				<tr class="row_employee_note" employee_id = "<?=$row['id'];?>"></tr>
				<?php } ?>
				</tbody>
			</table>

		

		</div>		



	<script src="<?=$root;?>js/bedrijfsvoering.js" type="text/javascript"></script>			

	

<?php include 'common/footer.php'; ?>
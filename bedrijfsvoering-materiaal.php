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
$stmt = $con -> prepare('SELECT M.*, E.name AS employee_name FROM materiaal M LEFT JOIN employees E ON M.employee = E.id ORDER BY M.id');
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
									<li class=" col"><a href="/bedrijfsvoering-voertuigen">Voertuigen</a></li>
                                    <li class="tab col"><a class="active" href="#">Materiaal</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-leveranciers">Leveranciers</a></li>
                                    <li class=" col"><a  href="/bedrijfsvoering-laptops">Laptops + telefoons</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-vacatures">Vacatures</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addNewMateriaal()"><i class="material-icons">add</i> Toevoegen</span>
				<div style="clear:both"></div>
			</div>			

			<div id="materiaal-panel" class="tab-content active">			
				<table class="materiaal-table full-w-table sort-table">
					<thead>
					<tr>
						<th>Soort</th>
						<th>Afbeelding</th>
						<th>Merk</th>
						<th>Naam</th>
						<th>Aanschaf datum</th>
						<th>Waarde</th>
						<th>In gebruik door</th>
						<th>Nummer</th>
						<th style="width:100px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php if ($result) : while ($row = $result->fetch_assoc()) : 
						if($row['employee'] == 0)
						$row['employee_name'] = 'Algemeen';
						?>

						<tr materiaalrow="<?=$row['id'];?>">
							<td><?=$row['soort'];?></td>
							<td>
							<?php
								$img_path = "vehicle.png";
								if(isset($row['file_path']))
									$img_path = $row['file_path'];

								if(isset($row['file_path']))
									echo "<img onclick='openPrev(\"" . $img_path . "\")' src='" . $root . "upload/" . $row['file_path'] . "'/>";
								else
								echo "<img src='" . $root . "images/users/vehicle.png" . "'/>";
							?>
							</td>
							<td><?=$row['merk'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=dateFormat($row['aanschaf_datum']);?></td>
                            <td>&euro;<?=$row['waarde'];?></td>
							<td><?=$row['employee_name'];?></td>
							<td><?=$row['nummer'];?></td>

							<td>
								<div onclick="editMateriaal(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>
				
								<div onclick="deleteMateriaal(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>
							</td>
						</tr>	

					<?php  endwhile; endif; ?>

					</tbody>
				</table>
			</div>
		</div>		

	<script src="<?=$root;?>js/bedrijfsvoering.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
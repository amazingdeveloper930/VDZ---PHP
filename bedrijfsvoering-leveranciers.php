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

if ($stmt = $con->prepare('SELECT * FROM suppliers  ORDER BY id')) {	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"

	

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
								<?php 
									if($_SESSION['ac_level'] == 1)
									{
										echo '<li class=" col"><a href="/bedrijfsvoering">Personeel</a></li>';
									}
									?>
									<li class=" col"><a href="/bedrijfsvoering-voertuigen">Voertuigen</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-materiaal">Materiaal</a></li>
                                    <li class="tab col"><a class="active"  href="#">Leveranciers</a></li>
                                    <li class=" col"><a  href="/bedrijfsvoering-laptops">Laptops + telefoons</a></li>
                                    <li class=" col"><a href="/bedrijfsvoering-vacatures">Vacatures</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>

				<span class="titlebarbutton button waves-effect waves-light btn" onclick="addSupplier()"><i class="material-icons">add</i> Toevoegen</span>

				<div style="clear:both"></div>

			</div>

			

			<table id="opdracht" class="contacten-table full-w-table sort-table">

				<thead>

				 <tr>
                    <th>Soort</th>
					<th>Name</th>
                    <th>Account manager</th>
                    <th>Account nummer</th>
                    <th>Email</th>
                    <th>Telefoon</th>
                    <th>Krediet</th>
                    <th>Rating</th>
                    
                    
					<th></th>					

				 </tr>

				</thead>

				<tbody>

				

				<?php 

				

				while ($row = $result->fetch_assoc()) { ?>

					

				<tr supplierrow="<?=$row['id'];?>">
                    <td><?=$row['soort']?></td>
                    <td><?=$row['name']?></td>
                    <td><?=$row['type']?></td>
                    <td><?=$row['accountnumber']?></td>
                    <td><?=$row['email']?></td>
                    <td><?=$row['phone']?></td>
                    <td><?=$row['krediet']?></td>
                    <td style="width: 180px">
                    <?php 
                        for($index = 0; $index < $row['rating']; $index ++)
                        echo "<div class='clip-star'></div>";
                    ?></td>
					<td style="width:150px">

						<div onclick="manageSupplierFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
						<div onclick="editSupplier(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>

						<div onclick="deleteSupplier(<?=$row['id'];?>)"class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>

					</td>

				 </tr>	

				

				<?php } ?>			 

				 

				</tbody>

			</table>

		

		</div>		



	<script src="<?=$root;?>js/bedrijfsvoering.js" type="text/javascript"></script>			

	

<?php include 'common/footer.php'; ?>
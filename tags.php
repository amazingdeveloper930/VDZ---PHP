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

if ($stmt = $con->prepare('SELECT * FROM tags Order By sort_order')) {

	$stmt->execute();
    
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
						
					<a class="<?php if($currentpage == 'werkplanning-setting') { echo "actief"; } ?>" href="/werkplanning-instellingen/">Werkplanning</a>
					</div>

					<div style="clear:both"></div>

				</div>
                
                <span class="titlebarbutton button waves-effect waves-light btn" onclick="addTag()"><i class="material-icons">add</i> Toevoegen</span>

				<div style="clear:both"></div>

			</div>

			

			<table id="tags" class="sort-table">

				<thead>

				 <tr>

					<th>Name</th>

					<th>Tag Type</th>

					<th class="no-sort"></th>					

				 </tr>

				</thead>

				<tbody>

				

				<?php 

				

				while ($row = $result->fetch_assoc()) { ?>

					

				<tr tagrow="<?=$row['id'];?>">

					<td><?=$row['name'];?></td>

					<td><?=($row['type'] == 'STANDARD' ? 'standaard' : 'fase');?></td>

					<td style="width:110px">

						<div onclick="editTag(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>

						<div onclick="deleteTag(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>

					</td>

				 </tr>	

				

				<?php } ?>			 

				 

				</tbody>

			</table>

			

		

		</div>			

	

	<script src="<?=$root;?>js/settings.js" type="text/javascript"></script>	

	

<?php include 'common/footer.php'; ?>
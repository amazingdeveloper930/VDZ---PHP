<?php 

require 'common/sessie_check.php';



$currentpage = 'werkplanning-setting';

?>

<!DOCTYPE html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Werkplanning Setting - Van der Zeeuw Bouw Ordersysteem</title>

		

		<?php include 'common/header.php'; ?>

	
		

</head>



<?php 



require( 'common/connection.php');





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
                <span class="titlebarbutton button waves-effect waves-light btn" onclick="addNewWerkPlan()"><i class="material-icons">add</i>Fase toevoegen</span>
				<div style="clear:both"></div>

			</div>
            <div class="content">
                <ul class="wrp_day_list">
                    <li>Maandag</li>
                    <li>Dinsdag</li>
                    <li>Woensdag</li>
                    <li>Donderdag</li>
                    <li>Vrijdag</li>
                    <li>Zaterdag</li>
                </ul>
                <div class="wrp_list">

                </div>
            </div>

		</div>			

	

	<script src="<?=$root;?>js/werkplanning-instellingen.js" type="text/javascript"></script>	

	

<?php include 'common/footer.php'; ?>
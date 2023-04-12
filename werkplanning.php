<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'werkplanning';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Werkplanning - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;
$result_pro = null;

if($stmt = $con -> prepare('SELECT * FROM employees WHERE teamleader = 0 AND inweekplanning = "Ja" ORDER BY sort_order'))
{
    //$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}
if($stmt = $con -> prepare('SELECT P.*, C.name, C.address FROM projects P Join contacts C ON P.contact_id = C.id Order By C.address'))
{
    //$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result_pro = $stmt->get_result();
}


?>


	<body class="app weekplanning">
        
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
        <div class="titlebar">
				<div class="titlebarcontainer" >
					<h2>Planning</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="">
									<li class=" col"><a href="/planning">Projecten</a></li>
									
									<li class=" col"><a href="/weekplanning">Medewerker planning</a></li>
									<li class=" col"><a class="actief" href="/werkplanning">Projectplanning</a></li>
									<li class=" col"><a href="/jaarplanning">Jaarplanning</a></li>
                                   					<li class=" col"><a href="/planning#opentickets">Open tickets</a></li>
                          
								</ul>
							</div>
						</div>
					</div>
                    <span class="titlebarbutton button waves-effect waves-light btn" id="btn_addNewWeekPlan" onclick="addNewWerkPlan()"><i class="material-icons">add</i> Toevoegen</span>
                    <div class="calender-week-header">
                        <div class="actiebutton " onclick="gotoPrevWeek()"><i class="material-icons">arrow_back</i></div>
                        <span class="calender-week-header-txt"></span>
                        <div class="actiebutton " onclick="gotoNextWeek()"><i class="material-icons">arrow_forward</i></div>
                    </div>
                    <div class="project_filter_panel">
                        <i class="material-icons">sort</i>
                        <select id='project_filter' class="browser-default" onchange='filter_changed()'>
                            <option default  selected value=''>Kies project</option>
                            <?php  
                                if ($result_pro)
                                while ($row = $result_pro->fetch_assoc())
                                {
                                    echo '<option value="' . $row['contact_id'] . '">' . $row['address'] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    
                    
								
				<div style="clear:both"></div>
			</div>					
            <div style="clear:both"></div>
			
		</div>
        <div class="content">		
            <ul class="wrp_day_list">
                <li>Maandag <span day=1></span></li>
                <li>Dinsdag <span day=2></span></li>
                <li>Woensdag <span day=3></span></li>
                <li>Donderdag <span day=4></span></li>
                <li>Vrijdag <span day=5></span></li>
                <li>Zaterdag <span day=6></span></li>
            </ul>
            <div class="wrp_list">

            </div>
        </div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	
	<script src="<?=$root;?>js/werkplanning.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>

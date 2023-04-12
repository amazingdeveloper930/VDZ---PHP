<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'jaarplanning';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Planning - Van der Zeeuw Bouw Ordersysteem</title>	

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
if($stmt = $con -> prepare('SELECT P.*, C.name, C.address FROM projects P Join contacts C ON P.contact_id = C.id Order By project_number'))
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
					<h2>Productie</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="">
									<li class=" col"><a href="/planning">Projecten</a></li>
							
									<li class=" col"><a class="actief" href="/weekplanning">Medewerker planning</a></li>
									<li class=" col"><a href="/werkplanning">Projectplanning</a></li>
									<li class=" col"><a href="/jaarplanning">Jaarplanning</a></li>
                                    <li class=" col"><a href="/planning#opentickets">Open tickets</a></li>
                     
								</ul>
							</div>
						</div>
					</div>
                    
                    <span class="titlebarbutton button waves-effect waves-light btn" id="btn_addNewWeekPlan" onclick="addNewWeekPlan()"><i class="material-icons">add</i> Toevoegen</span>
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

			<div id="weekplanning" class="tab-content active">		
                <table class="full-w-table calender-week-table scroll-table" >
                    <thead>
                        <tr >
                            <td></td>
                            <td>Maandag <span day=1></span><br/><span mday=1 class="wp-em-count"> medewerkers</span></td>
                            <td>Dinsdag <span day=2></span><br/><span mday=2 class="wp-em-count"> medewerkers</td>
                            <td>Woensdag <span day=3></span><br/><span mday=3 class="wp-em-count"> medewerkers</td>
                            <td>Donderdag <span day=4></span><br/><span mday=4 class="wp-em-count"> medewerkers</td>
                            <td>Vrijdag <span day=5></span><br/><span mday=5 class="wp-em-count"> medewerkers</td>
                            <td>Zaterdag <span day=6></span><br/><span mday=6 class="wp-em-count"> medewerkers</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($result)
                                while ($row = $result->fetch_assoc())
                                {

                                    $stmt = $con -> prepare('SELECT * FROM employees WHERE teamleader = ? AND inweekplanning = "Ja" ORDER BY sort_order');
                                    $stmt -> bind_param('i', $row['id']);
                                    $stmt->execute();
                                    $result_member = $stmt -> get_result();
                                    $member_count = $result_member -> num_rows;
                                    if($member_count > 0)
                                    echo "<tr class='teamleader' employeerow='" . $row['id'] . "' start_date = '" . $row['aankomst_datum'] . "' end_date = '" . $row['vertrek_datum'] . "' start_date_2 = '" . $row['aankomst_datum2'] . "' end_date_2 = '" . $row['vertrek_datum2'] . "'>";
                                    else{
                                        echo "<tr class='' employeerow='" . $row['id'] . "' start_date = '" . $row['aankomst_datum'] . "' end_date = '" . $row['vertrek_datum'] . "' start_date_2 = '" . $row['aankomst_datum2'] . "' end_date_2 = '" . $row['vertrek_datum2'] . "'>";
                                    }
                                    echo "<td class='employee'>" . $row['name'] . " " . $row['achternaam'];
                                    echo "<p style='font-weight:normal;'>" . $row['telefoonnummer1'] . "</p>";
                                    if($row['type'] == 'Productie')
                                        echo '<i class="material-icons" style="color: #2c3256">circle</i>';                                    
                                    if($row['type'] == 'Plaatsing')
                                        echo '<i class="material-icons" style="color: #f8b617">circle</i>';
                                    if($row['type'] == 'Service')
                                        echo '<i class="material-icons" style="color: #5fab5c">circle</i>';
                                    echo "</td>";
                        ?>
                            <td day_offset='1' class='project-panel'></td>
                            <td day_offset='2' class='project-panel'></td>
                            <td day_offset='3' class='project-panel'></td>
                            <td day_offset='4' class='project-panel'></td>
                            <td day_offset='5' class='project-panel'></td>
                            <td day_offset='6' class='project-panel'></td>
                            
                        <?php
                                    echo "</tr>";
                                    
                                    while ($row_member = $result_member->fetch_assoc())
                                    {
                                        echo "<tr class='teammember' employeerow='" . $row_member['id'] . "' start_date = '" . $row_member['aankomst_datum'] . "' end_date = '" . $row_member['vertrek_datum'] . "' start_date_2 = '" . $row_member['aankomst_datum2'] . "' end_date_2 = '" . $row_member['vertrek_datum2'] . "'>";
                                        echo "<td class='employee'><span>" . $row_member['name'] . " " . $row_member['achternaam'] . "</span>";
                                        echo "<p style='font-weight:normal;'>" . $row_member['telefoonnummer1'] . "</p>";
                                        if($row_member['type'] == 'Productie')
                                            echo '<i class="material-icons" style="color: #2c3256">circle</i>';                                        
                                        if($row_member['type'] == 'Plaatsing')
                                            echo '<i class="material-icons" style="color: #f8b617">circle</i>';
                                        if($row_member['type'] == 'Service')
                                            echo '<i class="material-icons" style="color: #5fab5c">circle</i>';
                                        echo "</td>";
                                        ?>
                                            <td day_offset='1' class='project-panel'></td>
                                            <td day_offset='2' class='project-panel'></td>
                                            <td day_offset='3' class='project-panel'></td>
                                            <td day_offset='4' class='project-panel'></td>
                                            <td day_offset='5' class='project-panel'></td>
                                            <td day_offset='6' class='project-panel'></td>
                                            
                                        <?php
                                    }
                                }
                        ?>
                    </tbody>
                </table>
            </div>
		</div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	
	<script src="<?=$root;?>js/weekplanning.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
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



?>

	<body class="app jaarplanning">
        
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
        <div class="titlebar">
				<div class="titlebarcontainer">
					<h2>Planning</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="">
									<li class=" col"><a href="/planning">Projecten</a></li>
									
									<li class=" col"><a href="/weekplanning">Medewerker planning</a></li>
									<li class=" col"><a href="/werkplanning">Projectplanning</a></li>
									<li class=" col"><a class="actief" href="/jaarplanning">Jaarplanning</a></li>
									<li class=" col"><a href="/planning#opentickets">Open tickets</a></li>
									
								</ul>
							</div>
						</div>
					</div>
                   
                    <div class="calender-quarter-header">
					<div class="actiebutton " onclick="print_jaarplanning()"><i class="material-icons">print</i></div>
                        <div class="actiebutton " onclick="gotoPrevQuarter()"><i class="material-icons">arrow_back</i></div>
                        <span class="calender-quarter-header-txt"></span>
                        <div class="actiebutton " onclick="gotoNextQuarter()"><i class="material-icons">arrow_forward</i></div>
                    </div>
					<div style="clear:both"></div>
				</div>				
				<div style="clear:both"></div>
			</div>					

			<div id="jaarplanning" class="tab-content active">		
                <table class="full-w-table calender-quarter-table">

                    <tbody>
                        <?php
                            for($jdex = 0; $jdex < 60; $jdex ++){
                                ?>
                                <tr>
                                <td class="td-jaarplanning-text" id="jaarplanning-text-<?=$jdex?>">
                                </td>
                                    <?php
                                
                                for($index = 1; $index <= 13; $index ++){
                                    
                        ?>
                                <td class="<?=$jdex!=0?'td-week':'td-week-header'?>" id="week-<?=$jdex.'-'.$index?>">
									<span class='week-number'></span>
									<br/>
									<span class='week-employee-info'></span>
								</td>
                        <?php
                                }
                                ?>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
		</div>

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	
	<script src="<?=$root;?>js/jaarplanning.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
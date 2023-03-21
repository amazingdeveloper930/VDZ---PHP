<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'salesplanning';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Leads - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>

<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;

if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, CL.entry_type, CL.entry_date, Q.latest_quote_date, Q.pdf_file FROM contacts C LEFT JOIN (SELECT contact_id, entry_type, entry_date FROM contact_log WHERE id IN ( SELECT MAX(id) FROM contact_log WHERE contact_type = "lead" GROUP BY contact_id)) CL ON (C.id = CL.contact_id) 
	LEFT JOIN (SELECT contact_id, pdf_file, MAX(quote_date) as latest_quote_date From quotes GROUP BY contact_id) Q ON (C.id = Q.contact_id)
	 WHERE C.c_status = 3')) {	

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}

?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
			<div class="titlebar">
				<div class="titlebarcontainer">
					<h2>Leads</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul>
									<li class=" col"><a href="/leads#actieveleads">Actieve leads</a></li>
									<li class=" col"><a href="/leads#inactieveleads">Inactieve leads</a></li>
									<li class="col"><a href="/salesplanning" class="actief">Salesplanning</a></li>
								</ul>
							</div>
						</div>
					</div>
                    <div class="calender-header">
                        <div class="actiebutton " onclick="gotoBeforeMonth()"><i class="material-icons">arrow_back</i></div>
                        <span class="calender-header-txt"></span>
                        <div class="actiebutton " onclick="gotoNextMonth()"><i class="material-icons">arrow_forward</i></div>
                    </div>
					<div style="clear:both"></div>
				</div>				
				<div style="clear:both"></div>
			</div>			

            <div class="content">
                <div class="sales-planning-panel">
                    <div class="sales-agent-panel">
                        
                    </div>
                    <div class="sales-meeting-panel">
                        <table class="full-w-table calender-table">
                            <tbody>
                                <tr>
                                    <td>Maandag <span></span></td>
                                    <td>Dinsdag <span></span></td>
                                    <td>Woensdag <span></span></td>
                                    <td>Donderdag <span></span></td>
                                    <td>Vrijdag <span></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	<!-- <script src="<?=$root;?>js/funnel.js" type="text/javascript"></script> -->
	<script src="<?=$root;?>js/salesplanning.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
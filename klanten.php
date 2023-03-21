<?php 
require 'common/global.php';

$currentpage = 'Klanten';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Klanten - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>
<body class="app">
    <div class="preloader-container">
        
    </div>
    
<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;
$project_code = $_GET['id'];
$employee_result = [];
$ticket_array = [];
$contact_type = ['', 'E-mail', 'Telefoon', 'Gesprek', 'Whatsapp'];
$lead_type = ['', 'Deal', 'Geen deal', 'Wijzigen offerte', 'Wacht op antwoord', 'Afspraak'];

if ($stmt = $con->prepare('SELECT P.*, C.name, C.email, C.phone FROM projects P JOIN contacts C ON P.contact_id = C.id WHERE P.project_code = ?')){
    $stmt -> bind_param('s', $project_code);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
    $row = $result->fetch_assoc();
}
if(isset($row)){
    $stmt = $con -> prepare('SELECT WM.*, WB.datum, E.* FROM werkplanning_medewerker WM JOIN employees E ON WM.employee_id = E.id LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id WHERE WM.contact_id = ? ORDER BY WB.datum');
    $stmt -> bind_param('i', $row['contact_id']);
    $stmt -> execute();
    $employee_result = $stmt->get_result();



    ////////
    
    if ($stmt = $con->prepare('SELECT PT.*, E.name, A.username From projects_tickets PT LEFT JOIN employees E ON PT.employee = E.id LEFT JOIN accounts A ON PT.user = A.id WHERE PT.contact_id = ? ORDER BY PT.id')) {

        $stmt->bind_param('s', $row['contact_id']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result_ticket = $stmt->get_result();

        while ($row_ticket = $result_ticket->fetch_assoc()) {


            $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tickets_notes WHERE ticket_id = ?');
            $stmt_new->bind_param('i', $row_ticket['id']);
            $stmt_new->execute();
            $result_new =  $stmt_new->get_result();
            while($row_new = $result_new -> fetch_assoc())
            {
                $row_ticket['note_count'] = $row_new['note_count'];
            }



            $row_ticket['timer_widget'] = getTicketTimer($row_ticket['datum'], $row_ticket['status']);
            $ticket_array []= $row_ticket;

        }
    }

    /////
    $stmt = $con -> prepare("SELECT * FROM projects_file WHERE contact_id = ? AND klantportaal = 1 ORDER BY uploaded_date DESC");
    $stmt -> bind_param("i", $row['contact_id']);
    $stmt->execute();
    $result_file = $stmt->get_result();

    $stmt = $con -> prepare('SELECT Temp.*, accounts.username
    From (SELECT C.cid, C.name, C.city, C.address, C.email, C.phone, CL.klanten, CL.file_path, CL.file_exe,CL.clid, CL.entry_type, CL.entry_title, CL.entry_date, CL.entry_description, CL.account_id
          From ( SELECT *, id as cid FROM contacts WHERE contacts.id = ?) C
          LEFT JOIN ( SELECT *, id as clid FROM contact_log Where contact_log.contact_id = ?) CL 
          ON (C.id = CL.contact_id)) Temp
    LEFT JOIN accounts
    ON Temp.account_id = accounts.id
    WHERE Temp.klanten = 1
    ORDER BY Temp.entry_date DESC');
    $stmt->bind_param('ii', $row['contact_id'], $row['contact_id']);
    $stmt->execute();
    $result_log = $stmt->get_result();
    
    $stmt_1 = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ? ORDER BY sort_order');
    $stmt_1 -> bind_param('i', $row['contact_id']);
    $stmt_1 -> execute();
    $result_1 = $stmt_1->get_result();
    $plans = [];
    while($row1 = $result_1->fetch_assoc())
    {
      $plans []= $row1;
    }
}


?>


    <div class="menubalk_klanten">
        <input hidden id="contact_id" value="<?=$row['contact_id']?>" />
        <img src="<?=$root;?>images/app-logo.png" class="app-logo">
        <img src="<?=$root;?>images/app-logo-mob.png" class="app-logo mob">
        <div class="contactcontainer">
            <div class="item-contact">
            <img src="<?=$root . '/images/phone.png'?>"/>
                <a href="tel:0235551551">023 - 555 15 51</a>
            </div>
            <div class="item-contact">
                <img src="<?=$root . '/images/mail.png'?>"/>
                <a href="mailto:info@vanderzeeuwbouw.nl">info@vanderzeeuwbouw.nl</a>
            </div>
        </div>
    

    </div>		

		<div class="appcontent">
		
			<div class="titlebar">
				<div class="titlebarcontainer" style="padding-bottom: 11px;">
					<h2>Goedenavond <?=$row['name']?>,
						
					</h2>
					
					<div style="clear:both"></div>
				</div>
				
				<div style="clear:both"></div>
			</div>			
            <div class="row" id="klanten-container">
                <div class="col s12 m12 l8 row">
                    <div class="col s12 m12 l6">
                        <h3 class="klanten-planning-header">Planning</h3>
                        <table id="klanten-planning">
                            <tr>
                                <td>Startdatum</td>
                                <td><?=dateFormat($row['startdatum'], 'd F Y')?></td>
                            </tr>
                            <tr>
                                <td>Geplande opleverdatum</td>
                                <td><?=dateFormat($row['opleverdatum'], 'd F Y')?></td>
                            </tr>
                        </table>
                        <?php 

                            $week = (int)getWeekNumberFromDate($row['startdatum']);
                            


                            $this_year = getFDate($row['startdatum'], 'Y');
                            $this_year = (int)$this_year;
                            $this_year_week_count = (int)getWeekNumberFromDate($this_year . "-12-31");
                           
                    

                        ?>
                        <table id="klanten-phase">
                            <?php 

                            foreach($plans as $plan)
                            {
                                echo "<tr><td>" . $plan['name'] . "</td><td>Week " . $week . "</td></tr>";
                                $week +=  $plan['week'];
                                if($week > $this_year_week_count)
                                    $week -= $this_year_week_count;
                            }
                            ?>
                            
                        </table>
                    </div>
                    <div class="col s12 m12 l6">
                        <h3 class="klanten-employee-header">Wij komen bij u langs</h3>
                        <div id="klanten-employee-panel">
                            <table id="klanten-employee">
                                <?php 
                                $employee_result_count = 0;
                                while($employee_row = $employee_result -> fetch_assoc()){
                                    $employee_result_count ++;
                                    if($employee_row['daypart'] == 'Heledag')
                                        $employee_row['daypart'] = 'Hele dag';
                                    ?>
                                <tr>
                                    <td class="klanten-employee-avatar"><?php 
                                        if($employee_row['file_path'])
                                            echo "<img src='" . $root. "upload/" . $employee_row['file_path'] . "'/>";
                                        else 
                                        echo "<img src='" . $root . "images/users/employee.png" . "'/>";
                                    
                                    ?></td>
                                    <td><?=$employee_row['name']?></td>
                                    <td><?=$employee_row['projecttype']?></td>
                                    <td><?=dateFormat($employee_row['datum'], 'd-m-Y') . ", " . $employee_row['daypart']?></td>
                                </tr>
                                <?php 
                                }?>
                            </table>
                        </div>
                        
                        <?php 
                        if($employee_result_count > 0){ ?>
                        <div class="alert alert-danger">
                            <i class="material-icons">warning</i>
                            Deze planning is slechts indicatief en <b>sterk</b> onderhevig aan verandering. 
                        </div>
                        <?php 
                        }
                        ?>
                    </div>
                    
                
                <div class="col s12 m12 12" >
                        <h3 class="klanten-ticket-header">Tickets</h3>
                        <div class="scroll-table-container">
                        <table id="klanten-tickets" class="project-ticket-table">
                            <tr>
                                <th>Open tickets</th>
                                <th></th>
                                <th>Datum</th>
                                <th>Medewerker</th>
                            </tr>
                            <?php 
                                foreach($ticket_array as $ticket){
                                    if($ticket['status'] == 'OPENED'){
                            ?>
                                <tr>
                                    <td><?=$ticket['title']?></td>
                                    <td></td>
                                    <td><?=dateFormat($ticket['datum'], 'd-m-Y')?></td>
                                    <td><?=$ticket['name']?$ticket['name']: 'N.v.t'?></td>
                                </tr>
                            <?php    
                                    }    
                                }
                            ?>
                            <tr class="blank_row">
                                <td colspan="7"></td>
                            </tr>
                            <tr>
                                <th>Gesloten tickets</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php 
                                foreach($ticket_array as $ticket){
                                    if($ticket['status'] == 'CLOSED'){
                            ?>
                                <tr>
                                    <td><?=$ticket['title']?></td>
                                    <td><?=$ticket['timer_widget']?></td>
                                    <td><?=dateFormat($ticket['datum'], 'd-m-Y')?></td>
                                    <td><?=$ticket['name']?$ticket['name']: 'N.v.t'?></td>
                                </tr>
                            <?php    
                                    }    
                                }
                            ?>
                            
                        </table>
                        </div>
                    </div>
		</div>
		<div class="col s12 m12 l4">
                    <h3 class="klanten-bestanden-header">Bestanden</h3>
                    <span class="titlebarbutton button waves-effect waves-light btn" onclick="addFile()" id="klanten-addfile"><i class="material-icons">add</i> Bestand uploaden</span>
                    <div style="clear:both"></div>
                    <div class="klanten-flog-panel logs">
                        <?php
                           while($row_file = $result_file -> fetch_assoc()){ 
                        ?>
                        <div class="log-container"><div class="log-wrapper"><div class="flog-prev">
                            <?php 
                                if(strtolower($row_file['file_exe']) == 'pdf'){

                            ?>
                            <a class="img-pdf" href="<?=$root ."upload/". $row_file['file_path']?>" target="_blank"><img src="<?=$root . 'images/pdf.png'?>"></a>
                            <?php 
                                }
                                else{
                            ?>
                            <a class="img-preview" onclick="openPrev('<?=$row_file['file_path']?>')">
                            <img src="<?=$root ."upload/". $row_file['file_path']?>"></a>
                            <?php 
                                }
                                ?>
                            

                        
                            </div><div class="flog-header"><span class="fc-name"><?=$row_file['name']?></span><a href="<?=$root ."upload/". $row_file['file_path']?>" download="" class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a></div><div class="flog-container"><span class="fc-date"><?=dateFormat($row_file['uploaded_date'], 'd-m-Y H:i')?></span></div></div></div>
                            <?php 
                            }
                            ?>
                        </div>
                        <h3 class="klanten-logboek-header">Logboek</h3><br/><br/>
                        <div class="klanten-log-panel logs">
                        <?php 
                            while($row_log = $result_log -> fetch_assoc())
                            {
                                
                                $contactloghtml = '<div id="clog-' . $row_log['clid'] . '" class="log-container">';
                                $type_Str = "";
                                    if ($row_log['entry_title'] == 'lead')
                                        $type_Str = $lead_type[$row_log['entry_type']];
                                    else 
                                        $type_Str = $contact_type[$row_log['entry_type']];
                                    $date = $row_log['entry_date'];
                                if(empty($row_log['file_path']) || $row_log['file_path'] == ""){
                                    

                                    $contactloghtml .='<div class="log-wrapper"><div class="log-header"><span class="c-type">' . $type_Str .  '</span><span class="c-date">' . getFDate($date) . '</span><span class="c-user"><i class="material-icons">person</i> ' . $row_log['username'] . '</span><div onclick="showInnerConfirm(' . $row_log['clid'] . ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div></div>' . '<div class="log-content">' . $row_log['entry_description'] . '</div><div id="ipo-' . $row_log['clid'] . '" class="inner-popup-overlay"></div><div id="ipc-' . $row_log['clid'] . '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerConfirm(' . $row_log['clid'] . ')">Annuleren</span><span class="button red" onclick="deleteCLogConfirm(' . $row_log['cid'] . ',' . $row_log['clid'] . ')">Verwijderen</span></div></div></div></div>';
                                    }
                                else
                                    {
                                        $contactloghtml .= '<div class="log-wrapper"><div class="flog-prev">';
                                
                                
                                        if($row_log['file_exe'] == 'pdf') 
                                        {
                                            $contactloghtml .= '<a class="img-pdf" href="upload/' . $row_log['file_path'] . '" target="_blank"><img  src="images/pdf.png"></a>';
                                        }
                                        else{
                                            $contactloghtml .= '<a class="img-preview" onclick="openPrev(\'' . $row_log['file_path'] . '\')"><img src="upload/' . $row_log['file_path'] . '"></a>';
                                        }
                                
                                        $contactloghtml .= '</div>' . 
                                        '<div class="flog-header">' .
                                        '<span class="fc-name">' . $type_Str . '</span>' .
                                        
                                        '<div onclick="showInnerConfirm(' . $row_log['clid'] . ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' . 
                                        '<a href="upload/' . $row_log['file_path'] . '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' . 
                                        '</div>' .
                                        '<div class="flog-container">' . 
                                        '<span class="fc-date">' . getFDate($date) . '</span>' . 
                                        '<span class="fc-user"><i class="material-icons">person</i>' . $row_log['username'] . '</span>' .
                                        '<p class="fc-desc">' . $row_log['entry_description'] . '</p>' 
                                        . 
                                        '</div>' ;
                                        $contactloghtml .= '<div id="ipo-' . $row_log['clid'] . '" class="inner-popup-overlay"></div><div id="ipc-' . $row_log['clid'] . '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerConfirm(' . $row_log['clid'] . ')">Annuleren</span><span class="button red" onclick="deleteCLogConfirm(' . $row_log['cid'] . ',' . $row_log['clid'] . ')">Verwijderen</span></div></div></div></div>';
                                
                                    }
                                echo  $contactloghtml;
                            }
                        ?>
                        </div>
                    </div>
                
            </div>
			
		</div>		
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/klanten.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>
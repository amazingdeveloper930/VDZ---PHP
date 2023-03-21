<?php

require( '../../common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    $result = null;

    if ($stmt = $con->prepare(
        'SELECT P.*, C.name, C.address FROM projects P JOIN contacts C ON P.contact_id = C.id ORDER BY C.address')) {	

        //$stmt->bind_param('i', 3); // only lead
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
    }
   
    $stmt = $con -> prepare("SELECT WA.* FROM werkplanning_activity WA GROUP BY text");
    $stmt -> execute();
    $result_activity = $stmt -> get_result();

    
?>
<div id="weekplaninfo" class="col s12">					
    <input type="hidden" name="userid" class="userid" value="">
    <input type="hidden" name="plan_id" id="plan_id" value="">
    <div class="row">
        <div class="col s3">
            <label for="wp_project">Project</label>
            <select id="wp_project" name="wp_project"  onchange="changedProject()">
                <?php                 
                     while ($row = $result->fetch_assoc()) {
                        echo "<option value=" . $row['contact_id']  . ">#" . $row['project_number'] . " / " . $row['address'] . "</option>";
                    }

                ?>
            </select>
            
        </div>
		
        <div class="col s3">
            <label for="wp_daypart">Dagdeel</label>
            <select id="wp_daypart" name="wp_daypart" onchange="changedDayPart()">
                <option value='Heledag'>Hele dag</option>
                <option value='Ochtend'>Ochtend</option>
                <option value='Middag'>Middag</option>
                <option value='Meerdere dagen'>Meerdere dagen</option>
            </select>
            
        </div>
        <div class="input-field col s2" id="wp_datum_panel">
			<input id="wp_datum" type="date" value="" class="wp_datum " name="wp_datum"/>
			<label for="wp_datum">Datum</label>
		</div>
        <div class="input-field col s2" id="wp_datum_end_panel">
			<input id="wp_datum_end" type="date" value="" class="wp_datum_end " name="wp_datum_end"/>
			<label for="wp_datum_end">Datum t/m</label>
		</div>
        <div class="col s2">
	    <label for="wp_project_plan">Fases</label>
            <select id="wp_project_plan" name="wp_project_plan"> 
                
            </select>
            
        </div>
        
    </div>	

    <div class="row">
        <div class="col s5">
            
            <label for="wp_text">Werkzaamheden</label>
            <input id='wp_text' name='wp_text' type='text' hidden/>
            <select id="wp_text_select" name="wp_text_select" onchange="changedWerkzaamheden()">
                <?php
                    while($row = $result_activity -> fetch_assoc())
                    {
                        echo "<option value='" . $row['text'] . "'>" . $row['text'] . "</option>";
                    }
                ?>
                <option value='' default>Anders...</option>
            </select>
            
        </div>
        <div class="col s2 wp_ticket_panel">
            <label for="wp_ticket">Ticket</label>
            <select id="wp_ticket" name="wp_ticket">
            </select>
            
        </div>
        <div class="bottombuttons input-field col s5">
            <span class="button  btn" onclick="deleteWeekPlan()" id='btn-delete'><i class="material-icons">delete</i> Verwijderen</span>
            <span class="button waves-effect waves-light btn" onclick="saveWeekPlan()">Wijzigingen opslaan</span>
        </div>
    </div>


  
						
</div>

<div id="workorder" class="col s12">
    <div class="row">
        <div class="input-field col s12">
            <h2>Werkorder</h2>
            <span class="workorder_identify"></span>
            <div class="workorder_tool_panel">
                <div class="workorder_children_list">
                    
                </div>
                <div style="float: left;">
                    <input type="text" placeholder="Werkorder nr." class="workorder_parent"/>
                </div>
                
                <div class="clear_parent" onclick="clearParent()" style="display: none;"><i class="material-icons">clear</i></div>
                <div onclick="linkToParent()" class="actiebutton tooltipped btn_link" data-position="top" data-tooltip="Bestanden" ><i class="material-icons">link</i></div>
                <div onclick="addNewWorkOrder()" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">add</i></div>
                <div onclick="showWorkOrderTable()" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">print</i></div>
            </div>
        </div>
        <div class="col s12">
            <table class="full-w-table" id="table-workorder">
                <thead>
                    <th  style="width: 40%;">Omschrijving</th>
                    <th>Materiaal</th>
                    <th>Gereedschap</th>
                    <th style="width: 50px;"></th>
                    <th style="width: 50px;"></th>
                    <th style="width: 50px;"></th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
       
    </div>

</div>

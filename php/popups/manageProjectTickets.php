<?php 
    require( '../../common/connection.php');    
    require '../../common/sessie_check.php';
	require '../../common/global.php';

    $result = null;

    if ($stmt = $con->prepare(
        'SELECT E.* FROM employees E ORDER BY E.id')) {	

        //$stmt->bind_param('i', 3); // only lead
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
    }


?>
	<form id="ticketinfo">
		<input type="hidden" name="contactid" class="contactid" value="">
        <input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
        <div class="row ticket-info-panel">
            <div class="col s10 row">
                <div class="input-field col s4">
                    <input id="ticket_title" type="text" name="ticket_title" >
                    <label for="ticket_title">Ticket titel</label>
                </div>
                <div class="input-field col s2">
                    <input id="ticket_datum" type="date" class="datepicker" name="ticket_datum" >
                    <label for="ticket_datum">Datum ticket</label>
                </div>
                <div class="col s2" style="margin-top: -14px;">
                    <label for="ticket_employee">Medewerker</label>
                    <select id="ticket_employee" name="type">
                        <option value='' default>N.v.t.</option>
                        <?php
                            while($row = $result->fetch_assoc())
                            {
                                echo '<option value=' . $row['id'] . '>' . $row['name'] . '</option>';
                            } 
                        ?>
                    </select>
                    
                </div>
                <div class="input-field col s2">
                    <input id="inkoop_besteld" type="date" class="datepicker" name="inkoop_besteld" >
                    <label for="inkoop_besteld">Inkoop besteld</label>
                </div>
                <div class="input-field col s2">
                &euro;<input type="text"   id="bedrag_open" name="bedrag_open" onchange='convertNumber(this)' placeholder="Bedrag open">
                </div>
                
            </div>
            <div class="input-field col s2">
                    <span class="button waves-effect waves-light btn" onclick="saveTicket()">Ticket toevoegen</span>
            </div>
            
        </div>

        <div class="row project-ticket-panel">
            <table class="project-ticket-table full-w-table">
                <tr>
                    <th>Open tickets</th>
                    <th></th>
                    <th>Datum</th>
                    <th>Medewerker</th>
                    <th>Ingepland</th>
                    <th style="width: 120px">Inkoop Besteld</th>
                    <th style="width: 120px">Bedrag Open</th>
                    <th></th>
                    <th>Timer</th>
                    <th style="width: 100px"></th>
                </tr>
                <tr class='blank_row'>
                     <td colspan="10" ></td>
                </tr>
                <tr>    
                    <th>Gesloten tickets</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </table>
        </div>
        

	</form>

    

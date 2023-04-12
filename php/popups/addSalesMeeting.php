<?php 

	require '../../common/global.php';
    require '../../common/sessie_check.php';
    require( '../../common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    $result = null;

    if ($stmt = $con->prepare(
        'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status FROM contacts C
        WHERE C.l_status != 1')) {	

        //$stmt->bind_param('i', 3); // only lead
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
    }
?>
	<form id="sales" class="col s12">
		<input type="hidden" name="salesmeetingid" class="salesmeetingid" value="">
        <input type="hidden" name="agentid" class="agentid" value="">
        <input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
		<div class="row">
			<div class="col s12">
                <label for="leadid">Lead</label>
                <select id="leadid" name="leadid" placeholder="Lead">      
                    <?php
                        if ($result) : while ($row = $result->fetch_assoc()) : 
                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                        endwhile; endif; 
                    ?>   
                </select>
                
			</div>
		</div>

        <div class="row">
			<div class="input-field col s12">
                <input type="date" value="" class="meetingdate " name="meetingdate"/>
                <label for="date">Datum</label>
			</div>
		</div>
        <div class="row">
            <div class="col s6">
                <label for="fromTime">Tijd van</label>
                <select id="fromTime" name="fromTime">
                    <?php
                        for($hour = 7; $hour < 20 ; $hour ++ )
                        { ?>
                            <option value="<?=$hour?>"><?=$hour . ":00 uur"?></option>
                            <option value="<?=$hour + 0.5?>"><?=$hour . ":30 uur"?></option>
                        <?php        
                        }
                    ?>
                </select>
                
            </div>
            <div class="col s6">
                <label for="toTime">Tijd tot</label>
                <select id="toTime" name="toTime">
                    <?php
                        for($hour = 7; $hour < 20 ; $hour ++ )
                        { ?>
                            <option value="<?=$hour+0.5?>"><?=$hour . ":30 uur"?></option>
                            <option value="<?=$hour+ 1?>"><?=($hour + 1) . ":00 uur"?></option>
                            
                        <?php        
                        }
                    ?>
                </select>
                
            </div>
        </div>
		
		<div class="bottombuttons" id="addNewPanel"> <span class="button waves-effect waves-light btn full" onclick="saveSalesMeeting()">Afspraak toevoegen</span> </div>
        <div class="bottombuttons" id="editExistingPanel"> 
            <span class="button waves-effect waves-light btn red" onclick="deleteSalesMeeting()">Verwijder afspraak</span>
            <span class="button waves-effect waves-light btn " onclick="saveSalesMeeting()">Afspraak opslaan</span> </div>

	</form>
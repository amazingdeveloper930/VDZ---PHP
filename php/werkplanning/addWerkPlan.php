<?php
require( '../../common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    $result = null;

    if ($stmt = $con->prepare(
        'SELECT P.*, C.name, C.address FROM projects P JOIN contacts C ON P.contact_id = C.id WHERE P.startdatum is not NULL ORDER BY C.address')) {	

        //$stmt->bind_param('i', 3); // only lead
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
    }

 ?>
 
 
 <div id="werkplaninfo" class="col s12">
    <div class="row">
        <div class="select-box-field col s12">
            <label for="wrp_p_project">Kies project</label>
            <select id='wrp_p_project' class='browser-default'>
                <?php                 
                        while ($row = $result->fetch_assoc()) {
                        echo "<option value=" . $row['contact_id']  . ">" . $row['address'] . "</option>";
                    }

                ?>
            </select>
        </div>
    </div>
    <div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveNewWerkplanning()">Toevoegen</span> </div>
</div>


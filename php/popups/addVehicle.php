<?php
require '../../common/global.php';
require( '../../common/connection.php');

    $stmt = $con -> prepare('SELECT * FROM employees ORDER BY name');
    $stmt -> execute();
    $result = $stmt -> get_result();

?>

<form id="vehicleinfo" class="col s12">
    <input type="hidden" name="vehicleid" class="vehicleid" value="">
    <div class="row">
        <div class="input-field col s5">
            <input id="kenteken" type="text" name="kenteken" value="">
            <label for="kenteken">Kenteken</label>
        </div>
        <div class="col s6">
            <label for="employee">In gebruik door</label>
            <select id="employee" style="width: calc(100% - 60px);">
                <option value='0'>Algemeen</option>
                <?php 
                    while($row = $result -> fetch_assoc()){
                        echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                    }
                ?>
            </select>
            <img src="<?=$root;?>images/users/vehicle.png" class="img-user-avatar" style="margin-top: -50px"> 
        </div>
        <div class="file-field input-field col s1 btn-filelog">
				
				<div class="preloader-wrapper small active file-loading-icon">
					<div class="spinner-layer spinner-blue-only">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div><div class="gap-patch">
						<div class="circle"></div>
					</div><div class="circle-clipper right">
						<div class="circle"></div>
					</div>
					</div>
				</div>
				<div class="file-icon">
					<i class="material-icons">attach_file</i>
					<input type="file" id="file" onchange="fileselected()">
				</div>
				<div class="file-path-wrapper">
					<input class="file-path validate" type="text" hidden>
				</div>
			</div>
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="merk" type="text" name="merk" value="">
            <label for="merk">Merk</label>
        </div>
        <div class="input-field col s6">
            <input id="uitvoering" type="text" name="uitvoering" value="">
            <label for="uitvoering">Uitvoering</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s6">
            <input id="zitplaatsen" type="text" name="zitplaatsen" value="">
            <label for="zitplaatsen">Zitplaatsen</label>
        </div>
        <div class="input-field col s6">
            <input id="apkdatum" type="date" name="apkdatum" value="">
            <label for="apkdatum">APK datum</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6 popup-sp-number">
            &euro;<input id="wegenbelasting_bedrag" type="text" name="wegenbelasting_bedrag" value="" onchange="convertNumber(this)">
            <label for="wegenbelasting_bedrag">Wegenbelasting bedrag</label>
        </div>
        <div class="input-field col s6 popup-sp-number">
            &euro;<input id="verzekering_bedrag" type="text" name="verzekering_bedrag" value="" onchange="convertNumber(this)">
            <label for="verzekering_bedrag">Verzekering bedrag</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6 popup-sp-number">
            &euro;<input id="lease_bedrag" type="text" name="lease_bedrag" value="" onchange="convertNumber(this)">
            <label for="lease_bedrag">Lease bedrag</label>
        </div>
        <div class="input-field col s6">
            <input id="lease_maatschappij" type="text" name="lease_maatschappij" value="">
            <label for="lease_maatschappij">Lease maatschappij</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="lease_start" type="date" name="lease_start" value="">
            <label for="lease_start">Lease start</label>
        </div>
        <div class="input-field col s6">
            <input id="lease_eind" type="date" name="lease_eind" value="">
            <label for="lease_eind">Lease eind</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6 popup-sp-number">
            &euro;<input id="restant_bedrag_slottijd" type="text" name="restant_bedrag_slottijd" value="" onchange="convertNumber(this)">
            <label for="restant_bedrag_slottijd">Restant bedrag slottijd</label>
        </div>
        <div class="input-field col s6">
            <input id="tankpas_nummer" type="text" name="tankpas_nummer" value="">
            <label for="tankpas_nummer">Tankpas nummer</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="pincode" type="text" name="pincode" value="">
            <label for="pincode">Pincode</label>
        </div>
        <div class="col s6">
            <label for="track_jack">Track jack</label>
            <select id="track_jack" name="track_jack">
                <option value='ja'>Ja</option>
                <option value='nee'>Nee</option>
            </select>
            
        </div>
    </div>
    <div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveVehicleInfo()">Opslaan</span> </div>
</form>
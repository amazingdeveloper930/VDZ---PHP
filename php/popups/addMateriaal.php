<?php

require( '../../common/connection.php');
require '../../common/global.php';
    $stmt = $con -> prepare('SELECT * FROM employees ORDER BY name');
    $stmt -> execute();
    $result = $stmt -> get_result();

?>

<form id="materiaalinfo" class="col s12">
    <input type="hidden" name="materiaalid" class="materiaalid" value="">
    <div class="row">
        <div class="col s11">
            <label for="soort">Soort</label>
            <select id="soort" name="soort" style="width: calc(100% - 60px);">
                <option value="Handgereedschap">Handgereedschap</option>
                <option value="Elektrisch gereedschap">Elektrisch gereedschap</option>
                <option value="Hulpmiddel">Hulpmiddel</option>
                <option value="Groot materiaal">Groot materiaal</option>
                <option value="Loods installatie">Loods installatie</option>
                <option value="Container">Container</option>
            </select>
            <img src="<?=$root;?>images/users/vehicle.png" class="img-user-avatar" style="margin-top: -50px;">
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
            <input type="text" name="merk" id="merk"/>
            <label for="merk">Merk</label>
        </div>
        <div class="input-field col s6">
            <input id="name" type="text" name="name" value="">
            <label for="name">Naam</label>
        </div>
        
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="aanschaf_datum" type="date" name="aanschaf_datum" value="">
            <label for="aanschaf_datum">Aanschaf datum</label>
        </div>
        <div class="input-field col s6 popup-sp-number">
            &euro;<input id="waarde" type="text" name="waarde" value="" onchange="convertNumber(this)">
            <label for="waarde">Waarde</label>
        </div>
    </div>
    <div class="row">
       
        
        <div class="col s6">
            <label for="employee">In gebruik door</label>
            <select id="employee">
                <option value='0'>Algemeen</option>
                <?php 
                    while($row = $result -> fetch_assoc()){
                        echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                    }
                ?>
            </select>
            
        </div>
        <div class="input-field col s6">
            <input id="nummer" type="text" name="nummer" value="">
            <label for="nummer">Nummer</label>
        </div>
    </div>
    <div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveMateriaalInfo()">Opslaan</span> </div>
</form>
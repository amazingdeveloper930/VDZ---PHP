<?php

require( '../../common/connection.php');

    $stmt = $con -> prepare('SELECT * FROM employees ORDER BY name');
    $stmt -> execute();
    $result = $stmt -> get_result();

?>

<form id="laptopinfo" class="col s12">
    <input type="hidden" name="laptopid" class="laptopid" value="">
    <div class="row">
        <div class="select-box-field col s12">
            <label for="soort">Soort</label>
            <select id="soort" name="soort" class='browser-default'>
                <option value="iPad">iPad</option>
                <option value="Telefoon">Telefoon</option>
                <option value="Computer">Computer</option>
            </select>
            
        </div>
        
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input type="text" name="merk" id="merk"/>
            <label for="merk">Merk</label>
        </div>
        <div class="input-field col s6">
            <input id="type" type="text" name="merk" value="">
            <label for="type">Type</label>
        </div>
        
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="aanschafdatum" type="date" name="aanschafdatum" value="">
            <label for="aanschafdatum">Aanschafdatum</label>
        </div>
        <div class="col s6">
            <label for="employee">In gebruik door</label>
            <select id="employee" class='browser-default'>
                <option value='0'>Algemeen</option>
                <?php 
                    while($row = $result -> fetch_assoc()){
                        echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                    }
                ?>
            </select>
            
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
            <input id="abonnement_tot" type="date" name="abonnement_tot" value="">
            <label for="abonnement_tot">Abonnement tot</label>
        </div>
        <div class="input-field col s6">
            <input id="abonnement_provider" type="text" name="abonnement_provider" value="">
            <label for="abonnement_provider">Abonnement provider</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12 popup-sp-number">
            &euro;<input id="maandprijs" type="text" name="maandprijs" value="" onchange="convertNumber(this)">
            <label for="maandprijs">Maandprijs</label>
        </div>
        
    </div>
    <div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveLaptopInfo()">Opslaan</span> </div>
</form>
<?php 
  require '../../common/sessie_check.php';
  require '../../common/global.php';
?>

<form id="vehiclefilelog" class="col s12">
  <input type="hidden" name="vehicleid" class="vehicleid" value="">
  <input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
  <input type="hidden" name="date" class="date" value="">
  <div class="row">
    <div class="input-field col s11">
        <select id="type" name="type" >
            <option value="" disabled selected>Type bestand</option>
            <option value = 1>Constructieberekening</option>
            <option value = 2>Architect tekening</option>
            <option value = 3>Kozijn tekening</option>
            <option value = 4>Foto van situatie</option>
            <option value = 5>Overig</option>
        </select>
        
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

  <div class="row row-name">
	<div class="input-field col s12">
	  <input id="name" type="text" name="name" value="">
	  <label for="name">Bestandsnaam</label>
	</div>

  <div class="bottombuttons">
	<span class="button waves-effect waves-light btn full" onclick="saveVehicleFile()"><i class="material-icons">add</i> Bestand toevoegen</span>
  </div>
  
</form>
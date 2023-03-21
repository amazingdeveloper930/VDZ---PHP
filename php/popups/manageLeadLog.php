<?php 
  require '../../common/sessie_check.php';
  require '../../common/global.php';
?>

<form id="contactlog" class="col s12">
  <input type="hidden" name="contactid" class="contactid" value="">
  <input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
  <input type="hidden" name="date" class="date" value="">
  <div class="row">
    <div class="input-field col s11">
      <select id="type" name="type">
        <option value="" disabled selected>Kies een status</option>
      <?php 
        foreach (LEAD_TYPE as $key => $value) {
          echo '<option value="'.$key.'">'.$value.'</option>';
        }
      ?>
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
  </div>
  
  <div class="row">
		<div class="input-field col s6">
      <?php 
        $date = date_create();
        $date_d = $date->format("Y-m-d");
        $date_t = $date->format("H:i:s");
      ?>
			<input id="entrydate" type="date" value="<?=$date_d?>" class="meetingdate " name="entrydate"/>
			<label for="date">Datum</label>
		</div>
    <div class="input-field col s6">
			<input id="entrytime" type="time" value="<?=$date_t?>" class="meetingtime " name="entrytime"/>
			<label for="entrytime">Time</label>
		</div>
	</div>

  <div class="row">
	<div class="input-field col s12">
	  <input id="desc" type="text" name="desc" value="">
	  <label for="desc">Typ hier een notitie</label>
	</div>
  </div>

  <div class="bottombuttons">
	<span class="button waves-effect waves-light btn full" onclick="saveContactLog()"><i class="material-icons">add</i> Notitie toevoegen</span>
  </div>
  
</form>
<?php 
  require '../../common/global.php';
?>

<form id="projectfilelog" class="col s12">
  <input type="hidden" name="date" class="date" value="">
  <div class="row" style="display: none;">
    <div class="input-field col s11" >
        <select id="type" name="type" onchange="filetypeChanged()" class='browser-default'>
            <option value = 4 selected>Foto van situatie</option>
        </select>
        
    </div>

  </div>

  <div class="row row-name">
    <div class="input-field col s11">
      <input id="name" type="text" name="name" value="">
      <label for="name">Bestandsnaam</label>
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
                <input type="file" id="file" onchange="fileselected()" accept=".png,.jpg,.jpeg">
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" hidden>
            </div>
        </div>
</div>

  <div class="bottombuttons">
	<span class="button waves-effect waves-light btn full" onclick="saveProjectFile()"><i class="material-icons">add</i> Bestand toevoegen</span>
  </div>
  
</form>

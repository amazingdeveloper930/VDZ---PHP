<?php 

	require '../../common/global.php';

?>
<form id="vacatureinfo" class="col s12">
    <input hidden id="vacature_id" name="vacature_id" value=""/>
    <div class="row">
        <div class="input-field col s12">
            <input id="name" type="text" name="name" value="">
            <label for="name" class="active">Naam</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="email" type="text" name="email" value="">
            <label for="email">E-mailadres</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="phone" type="text" name="phone" value="">
            <label for="phone">Telefoonnummer</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <textarea id="description" class='text-description materialize-textarea' name="message"></textarea>
            <label for="description">Typ hier je notitie.</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field file-field-panel col s6">
        
            <div class="file-field input-field btn-filelog">
                <div class="file-icon">
                    <i class="material-icons">attach_file</i>
                    <input type="file" id="cv_file" onchange="fileselectedInFileLog(this)">
                </div>
            </div>
            <div class="file-field input-field btn-download">
                <a href="" download="" class="actiebutton tooltipped" data-position="top" data-tooltip="Download" id="cv_link">
                    <i class="material-icons">file_download</i>
                </a>
            </div>

            <label for="description">CV</label>
            <a href="javascript:void(0)" class="btn-remove-file" onclick="removeVacatureFile(this)">verwijder bestand</a>
        </div>
        <div class="input-field file-field-panel col s6">
        
            <div class="file-field input-field btn-filelog">
                <div class="file-icon">
                    <i class="material-icons">attach_file</i>
                    <input type="file" id="mot_file" onchange="fileselectedInFileLog(this)">
                </div>
            </div>
            <div class="file-field input-field btn-download">
                <a href="" download="" class="actiebutton tooltipped" data-position="top" data-tooltip="Download"  id="mot_link">
                    <i class="material-icons">file_download</i>
                </a>
            </div>

            


            <label for="description">Motivatie brief</label>
            <a href="javascript:void(0)" class="btn-remove-file" onclick="removeVacatureFile(this)">verwijder bestand</a>
        </div>
    </div>
    <div class="bottombuttons"> <span class="button waves-effect waves-light btn full" onclick="saveVacature()">Opslaan</span> </div>
</form>
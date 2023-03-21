<?php 
    require '../../common/sessie_check.php';
	require '../../common/global.php';

?>
	<form id="projectinfo">
		<input type="hidden" name="contactid" class="contactid" value="">
        <input type="hidden" name="quoteid" class="quoteid" value="">
        <div class="row project-info-panel">
            <div class="col s10 row">
                <div class="input-field col s3">
                    <input id="offerte-akkoord" type="date" name="offerte-akkoord" >
                    <label for="offerte-akkoord">Offerte akkoord</label>
                </div>
				<!--
                <div class="input-field col s2">
                    <input id="gewenste-plaatsingsdatum" type="date" name="gewenste-plaatsingsdatum" >
                    <label for="gewenste-plaatsingsdatum">Gewenste plaatsingsdatum</label>
                </div>
				-->
                <div class="input-field col s3">
                    <input id="startdatum" type="date" name="startdatum" onchange="projectStartDatumChanged()">
                    <label for="startdatum">Startdatum</label>
                </div>
                <div class="input-field col s3">
                    <input id="opleverdatum" type="date" name="opleverdatum" onchange="projectEndDatumChanged()">
                    <label for="opleverdatum">Opleverdatum</label>
                </div>
                <div class="col s3" style="    margin-top: -14px;">
 			<label for="plaatsing">Opgeleverd?</label>
                    <select id="plaatsing" name="type">
                        <option value="nee" default>Nee</option>
                        <option value="ja">Ja</option>
                    </select>
                   
                </div>
               
                
            </div>
            <div class="input-field col s2">
                    <span class="button waves-effect waves-light btn" onclick="saveProject()">Data opslaan</span>
            </div>
            
        </div>

        <div class="row project-task-panel">
            <div class="submenu">					
                <div class="row">
                    <div class="col s12">
                        <ul class="tabs">
                            
                        </ul>
                    </div>
                </div>
            </div>	
            <div style="clear:both"></div>
            <div class="project-task-table-panel col s12">

            </div>
            
        </div>
        

	</form>

    
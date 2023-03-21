<?php 
  require '../../common/sessie_check.php';
  require '../../common/global.php';
?>

<div id="ews_log" class="col s12">
    <input type="hidden" name="userid" class="userid" value="<?=$_SESSION['id'];?>">
    <input type="hidden" name="employeeid" class="employeeid" />
    <label>Werkdagen</label>
    <div class="row">
        
        <label class="col s4">
            <input type="checkbox" class="filled-in" id="ews_mo" onchange="checkDay(this, 0)"/>
            <span>Maandag</span>
        </label>
        <label class="col s4">
            <input type="checkbox" class="filled-in " id="ews_tu" onchange="checkDay(this, 1)"/>
            <span>Dinsdag</span>
        </label>
        <label class="col s4">
            <input type="checkbox" class="filled-in  " id="ews_we" onchange="checkDay(this, 2)"/>
            <span>Woensdag</span>
        </label>
        <label class="col s4">
            <input type="checkbox" class="filled-in  " id="ews_th" onchange="checkDay(this, 3)"/>
            <span>Donderdag</span>
        </label>
        <label class="col s4">
            <input type="checkbox" class="filled-in  " id="ews_fr" onchange="checkDay(this, 4)"/>
            <span>Vrijdag</span>
        </label>
        <label class="col s4">
            <input type="checkbox" class="filled-in  " id="ews_sa" onchange="checkDay(this, 5)"/>
            <span>Zaterdag</span>
        </label>
    </div>
    <div class="col s12" style="margin-top: 10px">
        <select id="ews_option" name="ews_option">
          <option value=''>Kies een type afwezigheid</option>
          <option value="Ziek">Ziek</option>
          <option value="Vrije dag(en)">Vrije dag(en)</option>
        </select>
    </div>
    <div class="row" style="margin-top:10px;">
      <div class="input-field col s6">
        <input type="date" id="ews_date_from"/>
        <label for="ews_date_from">Datum van</label>
      </div>
      <div class="input-field col s6">
        <input type="date" id="ews_date_to"/>
        <label for="ews_date_to">Datum van</label>
      </div>
    </div>
    <div class="row">
      <div class="input-field col s12">
        <input type="text" id="ews_text"/>
        <label for="ews_text">Toelichting afwezigheid</label>
      </div>
    </div>
    <div class="bottombuttons"> 
      <span class="button waves-effect waves-light btn full" onclick="saveEmployeeSchedule()"><i class="material-icons">add</i> Afwezigheid toevoegen</span> </div>
      
</div>
<form id="taginfo" class="col s12">
    <div class="row">
        <input hidden id="tag_id">
        <div class="input-field col s12">
            <input id="tag_name" type="text" name="tag_name" value="">
            <label for="tag_name" class="active">Tag Naam</label>
        </div>
        <div class="col s12">
            <label for="tag_type">Tag Type</label>
            <select id="tag_type" name="tag_type">      
                <option value="STANDARD">Standaard</option>
                <option value="FASE">Fase</option>      
            </select>
            
        </div>
    </div>
    <div class="bottombuttons">
        <span class="button waves-effect waves-light btn" onclick="saveTagInfo()">Opslaan</span>
    </div>
</form>	
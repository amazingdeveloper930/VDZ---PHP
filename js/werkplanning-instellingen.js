function getWeekText(num)
{
    if(num == 1)
        return "1 week";
    else 
        return num + " weken";
}

function addNewWerkPlan()
{
    if($(".wrp_list .wrp_new_chapter").length > 0)
        return;
    buildWrpChapter(null, 'EDIT');
}


function buildWrpChapterHeader(id, data, mode)
{
    var container = "#wrp_chapter_" + id + " .wrp_chapter_header";
    var rawHtml = "<div class='wrp_c_h_name_panel'><input placeholder='Naam fase' class='wrp_c_h_name'/></div>";
    
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_length_panel'><label>Duur</label></div>";
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_start_panel'><label>Start</label></div>";
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_hour_panel'><label>Beschikbare uren</label><input class='wrp_c_h_hour' />   <label>uren</label></div>";
    rawHtml += "<div class='wrp_c_h_empty_col'></div>";
    rawHtml += "<div class='icon-list'>";
    rawHtml += "<div class='actiebutton btn-edit' onclick='editWrpChapter(\"" + id + "\")'><i class='material-icons'>edit</i></div>";
    if(data == null)
        rawHtml += "<div class='actiebutton btn-save' onclick='saveWrpChapter(\"" + id + "\", true)'><i class='material-icons'>save</i></div>";
    else
        rawHtml += "<div class='actiebutton btn-save' onclick='saveWrpChapter(\"" + id + "\")'><i class='material-icons'>save</i></div>";
    if(data != null)
        rawHtml += "<div class='actiebutton btn-delete' onclick='deleteWrpChapter(\"" + id + "\")'><i class='material-icons'>delete</i></div>";
    rawHtml += "</div>";
    $(container).html(rawHtml);

    rawHtml = "<select class='wrp_c_h_length'><option value='1'>1 week</option><option value='2'>2 weken</option><option value='3'>3 weken</option><option value='4'>4 weken</option></select><span class='wrp_c_h_length_label'></span>";
    $(container + " .wrp_c_h_length_panel").append(rawHtml);

    rawHtml = "<select class='wrp_c_h_start'><option value='0'>0 weken</option><option value='1'>1 week</option><option value='2'>2 weken</option><option value='3'>3 weken</option><option value='4'>4 weken</option><option value='5'>5 weken</option><option value='6'>6 weken</option></select><span class='wrp_c_h_start_label'></span>";
    $(container + " .wrp_c_h_start_panel").append(rawHtml);

    rawHtml = "<select class='wrp_c_h_mode'><option value='voor'>voor</option><option value='na'>na</option></select><span class='wrp_c_h_mode_label'></span><label>plaatsingsdatum</label>";
    $(container + " .wrp_c_h_start_panel").append(rawHtml);
    $(container + " .wrp_c_h_length").attr('original_length', 0);
    if(data != null)
    {
        $(container + " .wrp_c_h_name").val(data['name']);
        $(container + " .wrp_c_h_length").val(data['length']);
        $(container + " .wrp_c_h_length").attr('original_length', data['length']);
        $(container + " .wrp_c_h_length_label").text(getWeekText(data['length']));
        $(container + " .wrp_c_h_start").val(data['start']);
        $(container + " .wrp_c_h_start_label").text(getWeekText(data['start']));
        $(container + " .wrp_c_h_mode").val(data['mode']);
        $(container + " .wrp_c_h_mode_label").text(data['mode']);
        $(container + " .wrp_c_h_hour").val(data['hour']);
    }
    
    if(mode == 'EDIT')
    {
        
    }
    else{
        $(container + " input").prop('readonly', true);
    }

}

function buildWrpChapterContent(id, data)
{
    var rawHtml = "";
    for(var index = 1; index <= data['length']; index ++)
    {
        rawHtml += "<div class='wrp_c_c_week' id='wrp_c_c_week_" + id + "_" + index + "'>";
        rawHtml += "<div class='wrp_c_h_week_label'>Week #" + index + "</div>";
        for(var jdex = 1; jdex <= 6; jdex ++)
        {
            rawHtml += "<div class='wrp_c_h_day wrp_c_h_day_" + jdex + "'>";
            rawHtml += "<div class='wrp_c_h_plus' onclick='addNewBlock(\"" + id + "\", " + index + "," + jdex + ")'></div>";
            rawHtml += "</div>";
        }
        rawHtml += "</div>";
    }
    $("#wrp_chapter_" + id + " .wrp_chapter_content").html(rawHtml);
    for(var index = 0; index < data['block'].length; index ++)
        {
            var week = data['block'][index]['week'];
            var day = data['block'][index]['day'];
            buildBlock(id, week, day, data['block'][index]);
        }
}

function addNewBlock(werkplanning_id, week, day)
{
    var data = {
        werkplanning_id : werkplanning_id,
        week : week,
        day : day
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/create_block.php",
        data: data,
        dataType: "json",
        success: function(result) {
            buildBlock(werkplanning_id, week,day, result['data']);
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}
function buildBlock(werkplanning_id, week, day, data = [])
{
    var block_id = data['id'];
    var rawHtml = "";
    rawHtml += "<div class='wrp_c_c_block' id='wrp_c_c_block_" + block_id +"'>";
    rawHtml += '<i class="material-icons wrp_c_c_block_delete" onclick="deleteBlock(' + block_id + ')">delete</i>';
    rawHtml += "<div class='wrp_c_c_innerblock'>";
    rawHtml += "<div class='wrp_c_c_block_header'>Activiteiten</div>";
    rawHtml += '<i class="material-icons wrp_btn_newactivity" onclick="addNewActivity(' + werkplanning_id + ", " + block_id + ')">add</i>';
    for(var index = 0; index < data['activity'].length; index ++)
    {
        var activity = data['activity'][index];
        rawHtml += "<div class='wrp_c_c_activity' id='wrp_c_c_activity_" + activity['id'] + "'><div class='wrp_activity_label'>" + activity['text'] + "</div><i class='material-icons wrp_btn_deleteactivity' onclick='deleteActivity(" + activity['id'] + ")'>delete</i>" + "</div>";
    }
    rawHtml += "</div></div>";
    var container = "#wrp_c_c_week_" + werkplanning_id + "_" + week + " .wrp_c_h_day_" + day;
    $(container + " .wrp_c_h_plus").before(rawHtml);

}

function deleteBlock(block_id)
{
    showConfirm('Weet je zeker dat je dit hele blok wilt verwijderen?', 'OK', 'red', "deleteBlockConfirm('" + block_id + "')");
}

function deleteBlockConfirm(block_id)
{
    closeConfirm();
    var data = {
        block_id : block_id
    }
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_block.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                $("#wrp_c_c_block_" + block_id).remove();
            }
        }
    });
}

function addNewActivity(werkplanning_id, block_id)
{
    var container = "#wrp_c_c_block_" + block_id + " .wrp_c_c_innerblock";
    if($(container + " .wrp_c_c_new_activity").length > 0)
    {
        return;
    }
    else{
        var rawHtml = "<div class='wrp_c_c_activity wrp_c_c_new_activity'><input class='wrp_c_c_a_text'/><i class='material-icons wrp_btn_saveactivity' onclick='saveActivity(" + werkplanning_id + "," + block_id + ")'>arrow_upward</i></div>";
        $(container).append(rawHtml);
    }
}

function saveActivity(werkplanning_id, block_id)
{
    var container = "#wrp_c_c_block_" + block_id;
    var text = $(container + " .wrp_c_c_a_text").val();
    var data = {
        werkplanning_id : werkplanning_id,
        block_id : block_id,
        text : text
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/save_activity.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                var data = result['data'];
                var rawHtml = "<div class='wrp_c_c_activity' id='wrp_c_c_activity_" + data['id'] + "'><div class='wrp_activity_label'>" + data['text'] + "</div><i class='material-icons wrp_btn_deleteactivity' onclick='deleteActivity(" + data['id'] + ")'>delete</i>" + "</div>";
                $(container + " .wrp_c_c_innerblock").append(rawHtml);
                $(container + " .wrp_c_c_innerblock .wrp_c_c_new_activity").remove();
            }
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}

function deleteActivity(activity_id)
{
    var data = {
        id : activity_id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_activity.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                $("#wrp_c_c_activity_" + activity_id).remove();
            }
        }
    });
}

function buildWrpChapter(data = null, mode = 'EDIT', original_id = null)
{
    var id = makeid(15);
    var chapter_class =  "";
    if(mode == 'EDIT')
        chapter_class = "wrp_chapter_edit";
    else 
        chapter_class = "wrp_chapter_normal";
    if(data == null)
        chapter_class += " wrp_new_chapter";

    if(data != null)
    {
        id = data['id'];
    }
    var rawHtml = "";
    if(original_id == null)
        rawHtml = "<div class='wrp_chapter " + chapter_class + "' id='wrp_chapter_" + id + "'>";
    rawHtml += "<div class='wrp_chapter_header'>";
    rawHtml += "</div>";
    rawHtml += "<div class='wrp_chapter_content'>";
    rawHtml += "</div>";
    if(original_id == null)
        rawHtml += "</div>";

    if(original_id == null)
        $(".wrp_list").append(rawHtml);
    else{
        $("#wrp_chapter_" + original_id).removeClass('wrp_chapter_edit');
        $("#wrp_chapter_" + original_id).removeClass('wrp_chapter_normal');
        $("#wrp_chapter_" + original_id).removeClass('wrp_new_chapter');
        $("#wrp_chapter_" + original_id).addClass(chapter_class);
        $("#wrp_chapter_" + original_id).html(rawHtml);
        $("#wrp_chapter_" + original_id).attr('id', "wrp_chapter_" + id);
    }

    buildWrpChapterHeader(id, data, mode);
    
    if(data != null)
        buildWrpChapterContent(id, data);
}

function editWrpChapter(id)
{
    var data = {
        id : id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/get_phase.php",
        data: data,
        dataType: "json",
        success: function(result) {
            buildWrpChapter(result['data'][0], 'EDIT', id);
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}

function saveWrpChapter(id, isNew = false)
{

    var container = "#wrp_chapter_" + id;
    var original_length = $(container + " .wrp_c_h_length").attr('original_length');
    var length = $(container + " .wrp_c_h_length").val();
    if(original_length > length)
    {
        showConfirm('Alle activiteiten gaan verloren die gepland staan in de weken die je nu verwijderd.', 'OK', 'red', "saveWrpChapterConfirm('" + id + "')");
    }
    else
        saveWrpChapterConfirm(id, isNew);

}

function saveWrpChapterConfirm(id, isNew = false)
{
    closeConfirm();
    var container = "#wrp_chapter_" + id;
    var name = $(container + " .wrp_c_h_name").val();
    var length = $(container + " .wrp_c_h_length").val();
    var start = $(container + " .wrp_c_h_start").val();
    var mode = $(container + " .wrp_c_h_mode").val(); 
    var hour = $(container + " .wrp_c_h_hour").val();
    var errors = '';
    if(name == "")
    {
        errors = "error";
		melding('Vul een naam in', 'Rood');
        return;
    }
    if(hour == "")
    {
        errors = "error";
		melding('Vul een uren in', 'Rood');
        return;
    }

    
    var data = {
        name : name,
        length : length,
        start : start,
        mode : mode,
        hour : hour
    };

    if(isNew ==  false)
        data['id'] = id;
    else
        data['id'] = '';

    $.ajax({
        type: "POST",
        url: "../php/werkplanning/save_phase.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                melding('Werkplanning opgeslagen.', 'groen');
                var werkplanning_id = result['werkplanning_id'];
                var original_id = id;
                var data = {
                    id : werkplanning_id
                };
                $.ajax({
                    type: "POST",
                    url: "../php/werkplanning/get_phase.php",
                    data: data,
                    dataType: "json",
                    success: function(result) {
                        buildWrpChapter(result['data'][0], 'NORMAL', original_id);
                    },
                    error: function(e1, e2, e3) {
                        debugger;
                    }
                });
            }
            else{
                melding(result['message'], 'rood');
            }

        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
    });

}


function deleteWrpChapter(id)
{
    showConfirm('Alle activiteiten gaan verloren die gepland staan in de weken die je nu verwijderd.', 'OK', 'red', "deleteWrpChapterConfirm('" + id + "')");
}

function deleteWrpChapterConfirm(id)
{
    closeConfirm();
    var data = {
        id : id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_phase.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                $("#wrp_chapter_" + id).remove();
            }
        }
    });
}


$(document).ready(function() {  
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/get_phase.php",
        dataType: "json",
        success: function(result) {
            var data = result['data'];
            if(data.length > 0)
            {
                for(var index = 0; index < data.length; index ++)
                    buildWrpChapter(data[index], 'NORMAL');
            }
            else
                addNewWerkPlan();
        }
    });
    
});

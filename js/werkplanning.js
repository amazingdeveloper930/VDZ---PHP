var short_month_list = ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'];

var currentDate = new Date();
var displayed_Monday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay() + 1));
// displayed_Monday = new Date('2020-12-28');
displayData(displayed_Monday);


function allowDrop(ev) {

    $(ev.target).addClass('hover');
    ev.preventDefault();
    
}

function drop(ev, day) {
    ev.preventDefault();
    $(ev.target).removeClass('hover');
    var item = ev.dataTransfer.getData("text");
    var contact_id = $("#" + item).attr("contact_id");
    var werkplanning_id = $("#" + item).attr("werkplanning_id");
    var block_id = $("#" + item).attr("block_id");

    if($("#" + item).hasClass('wrp_c_c_block_multiday'))
    {
        var daycount = $("#" + item).attr("daycount");
        if(parseInt(day) + parseInt(daycount) > 7)
            {
                melding('Dat gaat niet, je verplaatst de kaart deels naar volgende week.', 'Rood');
                return;
            }
    }

    ev.target.before(document.getElementById(item));
    
    
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        werkplanning_id = getNewWerkplanningID(data);
        block_id = getNewBlockID(data, block_id);
    }
    var weekday = new Date();
    weekday.setMonth(displayed_Monday.getMonth());
    weekday.setDate(displayed_Monday.getDate() + (day - 1));


    var postdata = {
        block_id : block_id,
        day : day,
        datum : formatDate(weekday)
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/move_block.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            updateCertainWerkplanning(contact_id, werkplanning_id);
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
    });
  }

function dragstart(ev)
{
    $(".wrp_c_h_plus").attr("ondragover", "");
    
    ev.dataTransfer.setData("text", ev.target.id);
    var contact_id = $(ev.target).attr("contact_id");
    var werkplanning_id = $(ev.target).attr("werkplanning_id");
    $("#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_c_h_plus").attr("ondragover", "allowDrop(event)");
}

function leaveDropZone(ev) {
    $(ev.target).removeClass('hover');
    ev.preventDefault();
}



$.extend({
    xResponse: function(url, data) {
        // local var
        var theResponse = null;
        // jQuery ajax
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: "json",
            async: false,
            success: function(respText) {
                theResponse = respText;
            },
            error: function(e1, e2, e3) {
                theResponse = e1;
            }
        });
        // Return the response text
        return theResponse;
    }
});



function getWeekNumber(d) {
    
    var onejan  = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    // if(d.getMonth() == 12 && d.)
    // Calculate full weeks to nearest Thursday
    var weekNo = Math.ceil((((d - onejan) / 86400000) + onejan.getDay() + 1) / 7);
    // Return array of year and week number
    if(onejan.getDay() > 4)
        weekNo = weekNo - 1;
    if(d.getMonth() == 11 && d.getDate() >= 29)
        weekNo = 1;
    


    return weekNo;
}

function gotoPrevWeek()
{
    displayed_Monday.setDate(displayed_Monday.getDate() - 7);
    displayData(displayed_Monday);
}

function gotoNextWeek()
{
    displayed_Monday.setDate(displayed_Monday.getDate() + 7);
    displayData(displayed_Monday);
}

function displayData(monday)
{
    var displayed_week = getWeekNumber(monday);
    var displayed_year = monday.getFullYear();
    var temp_year = displayed_year;
    if(monday.getMonth() == 11 && monday.getDate() >= 29)
    temp_year = temp_year + 1;

    $(".calender-week-header-txt").text('Week ' + displayed_week + ", " + temp_year);
    for(var index = 0; index < 7; index ++)
    {
        var weekday = new Date();
        weekday.setMonth(monday.getMonth());
        weekday.setDate(monday.getDate() + index);
        $(".wrp_day_list span[day=" + (index+1) + "]").text(weekday.getDate() + " " + short_month_list[weekday.getMonth()]);        
    }
    loadWerkPlan(displayed_year, displayed_week);
}

function updateCertainWerkplanning(contact_id, werkplanning_id)
{
    var displayed_year = displayed_Monday.getFullYear();
    var displayed_week = getWeekNumber(displayed_Monday);
    loadWerkPlan(displayed_year, displayed_week, contact_id, werkplanning_id);
}


function loadWerkPlan(year, week, contact_id = null, werkplanning_id = null)
{
    var postdata = {
        year: year,
        week: week
    };

    if(contact_id != null)
        postdata['contact_id'] = contact_id;
    if(werkplanning_id != null)
        postdata['werkplanning_id'] = werkplanning_id;

    $.ajax({
        type: "POST",
        url: "../php/werkplanning/get_werkplanning.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            var data = result['data'];
            if(contact_id == null)
                $(".wrp_list").empty();
            for(var index = 0; index < data.length; index ++)
            {
                var item = data[index];
                if(item['werkplanning'].length > 0)
                {
                    for(var jdex = 0; jdex < item['werkplanning'].length; jdex ++)
                    {
                        var tempData = [];
                        tempData['address'] = item['address'];
                        tempData['werkplanning'] = item['werkplanning'][jdex];
                        tempData['startdatum'] = item['startdatum'];
                        tempData['project_number'] = item['project_number'];
                        tempData['employee_list'] = item['employee_list'];
                        if(werkplanning_id != null)
                            buildWrpChapter(item['contact_id'], item['werkplanning'][jdex]['werkplanning_id'], tempData, true);
                        else
                            buildWrpChapter(item['contact_id'], item['werkplanning'][jdex]['werkplanning_id'], tempData);
                    }
                    
                }
            }
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
    });
}


function buildWrpChapter(contact_id, werkplanning_id, data, update = false)
{
    if(update == false)
    {
        var rawHtml = "<div class='wrp_chapter' id='wrp_chapter_" + contact_id + "_" + werkplanning_id + "' contact_id=" + contact_id + " default=" + data['werkplanning']['is_default'] + ">";
        rawHtml += "<div class='wrp_chapter_header'>";
        rawHtml += "</div>";
        rawHtml += "<div class='wrp_chapter_content'>";
        rawHtml += "</div>";
        rawHtml += "</div>";
        $(".wrp_list").append(rawHtml);
    }
    else{
        var item = ".wrp_list #wrp_chapter_" + contact_id + "_" + werkplanning_id;
        $(item).attr('default', data['werkplanning']['is_default']);
        $(item + " .wrp_chapter_header").empty();
        $(item + " .wrp_chapter_content").empty();
    }
    
    buildWrpChapterHeader(contact_id, werkplanning_id, data);
    buildWrpChapterContent(contact_id, werkplanning_id, data);
}
function buildWrpChapterHeader(contact_id, werkplanning_id, data)
{
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_chapter_header";
    var rawHtml = "<div class='wrp_c_h_name_panel'><input placeholder='Naam fase' class='wrp_c_h_name' readonly/></div>";
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_number_panel'><label>#" + data['project_number'] + "</label> / " + data['address'] + "</div>";
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_pdatum_panel'><i class='material-icons'>date_range</i><label>Plaatsingsdatum: </label>" + convertDateFormat(data['startdatum']) + "</div>";
    if(data['employee_list'] != '')
        rawHtml += "<div class='wrp_c_h_col wrp_c_h_pemployee_panel'><i class='material-icons'>person</i>" + data['employee_list'] + "</div>";
    rawHtml += '<div class="wrp_c_h_empty_col"></div>';
    rawHtml += "<div class='wrp_c_h_col wrp_c_h_timer_panel'><span class='badge green wrp_total_hour'></span><span class='badge red wrp_default_hour'></span>";
    rawHtml += "<div class='icon-list'>";
    rawHtml += "<div class='actiebutton btn-edit' onclick='editWrpChapterHeader(" + contact_id + ", " + werkplanning_id + ")'><i class='material-icons'>edit</i></div>";
    rawHtml += "<div class='actiebutton btn-edit' onclick='deleteWrpChapter(" + contact_id + "," + werkplanning_id + ")'><i class='material-icons'>delete</i></div>";
    rawHtml += "</div>";
    rawHtml += "</div>";
    $(container).empty();
    $(container).append(rawHtml);

    $(container + " .wrp_c_h_name").val(data['werkplanning']['name']);
    $(container + " .wrp_total_hour").text(data['werkplanning']['total_hour'] + "h");
    $(container + " .wrp_total_hour").attr('except_current_week', data['werkplanning']['total_hour_except_current_week']);
    $(container + " .wrp_default_hour").text(data['werkplanning']['hour'] + "h");
}
function buildWrpChapterContent(contact_id, werkplanning_id, data)
{
    var rawHtml = "";
    for(var index = 1; index <= 6; index ++)
    {
        rawHtml += "<div class='wrp_c_h_day wrp_c_h_day_" + index + "'>";
        rawHtml += "<div class='wrp_c_h_plus' onclick='addNewBlock(\"" + contact_id + "\", " + werkplanning_id + "," + index  + ")'  ondragleave='leaveDropZone(event)' ondrop='drop(event," + index + ")'></div>";
        rawHtml += "</div>";
    }
    $("#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_chapter_content").html(rawHtml);
    
    for(var index = 0; index < data['werkplanning']['block'].length; index ++)
    {
        var item = data['werkplanning']['block'][index];
        buildBlock(contact_id, werkplanning_id, item['id'], item['week'], item['day'], item['activity'], item['medewerker'], item['day_count']);
    }
}

function buildBlock(contact_id, werkplanning_id, block_id, week, day, data_activity = [], data_medewerker = [], day_count = 0)
{
    var rawHtml = "";
    var class_list = "wrp_c_c_block";
    var day_count_str = "";
    if(day_count > 0)
    {
        class_list += " wrp_c_c_block_multiday";
        day_count_str = " daycount=" + day_count + " ";
    }
    rawHtml += "<div class='" + class_list + "' id='wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" + block_id +"' draggable='true' ondragstart='dragstart(event)' contact_id=" + contact_id + " werkplanning_id=" + werkplanning_id + " block_id=" + block_id + day_count_str + ">";
    rawHtml += '<i class="material-icons wrp_c_c_block_delete" onclick="deleteBlock(' + contact_id + ", " + werkplanning_id + ", " + block_id + ')">delete</i>';
    rawHtml += "<div class='wrp_c_c_innerblock'>";
    rawHtml += "<div class='wrp_c_c_block_header'>Activiteiten</div>";
    rawHtml += '<i class="material-icons wrp_btn_newactivity" onclick="addNewActivity(' + contact_id + ", " + werkplanning_id + ", " + block_id + ", " + week + ')">add</i>';
    for(var index = 0; index < data_activity.length; index ++)
    {
        var activity = data_activity[index];
        rawHtml += "<div class='wrp_c_c_activity' id='wrp_c_c_activity_" + activity['id'] + "'><div class='wrp_activity_label'>" + activity['text'] + "</div><i class='material-icons wrp_btn_deleteactivity' onclick='deleteActivity(" + contact_id + ", " + werkplanning_id + ", "  + activity['id'] + ")'>delete</i>" + "</div>";
    }

    
    rawHtml += "</div>";
    rawHtml += "<div class='wrp_c_m_innerblock'>";
    rawHtml += "<div class='wrp_c_m_block_header'>Medewerkers</div>";
    rawHtml += '<i class="material-icons wrp_btn_newmedewerker" onclick="addNewMedewerker(' + contact_id + ", " + werkplanning_id + ", " + block_id + "," + day + ')">add</i>';

    for(var index = 0; index < data_medewerker.length; index ++)
    {
        var medewerker = data_medewerker[index];
        var txt_hour = medewerker['hour'];
        if(medewerker['daypart'] == "Ochtend")
            txt_hour += "(O)";
        if(medewerker['daypart'] == "Middag")
            txt_hour += "(M)";
                    
        rawHtml += "<div class='wrp_c_m_medewerker' id='wrp_c_m_medewerker_" + medewerker['id'] + "'><div class='wrp_medewerker_name_label'>" + medewerker['name'] + " " + medewerker['achternaam'] + "</div><div class='wrp_medewerker_hour_label' hour=" + medewerker['hour'] + ">" + txt_hour + "</div><i class='material-icons wrp_btn_deletemedewerker' onclick='deleteMedewerker(" + contact_id + ", " + werkplanning_id + ", "  + medewerker['id'] + ")'>delete</i>" + "</div>";
    }

    rawHtml += "</div>";
    rawHtml += "</div>";
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_c_h_day_" + day;
    $(container + " .wrp_c_h_plus").before(rawHtml);
}

function getNewWerkplanningID(data)
{
    return data['new_id'];
}
function getNewBlockID(data, block_id)
{
    var block = data['block'];
    for(var index = 0; index < block.length; index ++)
    {
        if(block[index]['original_id'] == block_id)
            return block[index]['new_id'];
    }
    return false;
}
function getNewActivityID(data, activity_id)
{
    var block = data['block'];
    for(var index = 0; index < block.length; index ++)
    {
        for(var jdex = 0; jdex < block[index]['activity'].length; jdex ++)
        if(block[index]['activity'][jdex]['original_id'] == activity_id)
            return block[index]['activity'][jdex]['new_id'];
    }
    return false;
}
function addNewBlock(contact_id, werkplanning_id, day)
{
    var year = displayed_Monday.getFullYear();
    var week = getWeekNumber(displayed_Monday);
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        var new_id = getNewWerkplanningID(data);
        werkplanning_id = new_id;
    }

    var weekday = new Date();
    weekday.setMonth(displayed_Monday.getMonth());
    weekday.setDate(displayed_Monday.getDate() + (day - 1));


    var postdata = {
        werkplanning_id : werkplanning_id,
        week : week,
        year : year,
        day : day,
        datum : formatDate(weekday),
        contact_id : contact_id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/create_block.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(data == false)
                buildBlock(contact_id, werkplanning_id, result['data']['id'], week,day);
            else{
                updateCertainWerkplanning(contact_id, werkplanning_id);
            }
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}


function addNewActivity(contact_id, werkplanning_id, block_id, week)
{
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id +  " #wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" + block_id + " .wrp_c_c_innerblock";
    if($(container + " .wrp_c_c_new_activity").length > 0)
    {
        return;
    }
    else{//<input />
        var rawHtml = "<div class='wrp_c_c_activity wrp_c_c_new_activity'><select class='wrp_c_c_a_text ' onchange='activityChanged(this)'></select><input hidden/><i class='material-icons wrp_btn_saveactivity' onclick='saveActivity(" + contact_id + "," + werkplanning_id + "," + block_id + ")'>arrow_upward</i></div>";
        $(container).append(rawHtml);
    }
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/get_activity.php",
        data: {
            werkplanning_id: werkplanning_id,
            week: week
        },
        dataType: "json",
        success: function(result)
        {
            var rawHtml = "";
            for(var index = 0; index < result['activity'].length; index ++)
            {
                rawHtml += "<option value='" + result['activity'][index] + "'>" + result['activity'][index] + "</option>";
            }
            rawHtml += "<option value='Anders..'>Anders..</option>";
            $(container + " .wrp_c_c_a_text").html(rawHtml);
            activityChanged(container + " select.wrp_c_c_a_text");
        },
        error: function(e1, e2, e3)
        {
        }
    });
}

function addNewMedewerker(contact_id, werkplanning_id, block_id, day)
{
    var block_container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id +  " #wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" +  block_id;

    var container = block_container + " .wrp_c_m_innerblock";
    if($(container + " .wrp_c_m_new_medewerker").length > 0)
    {
        return;
    }
    var rawHtml = "<div class='wrp_c_m_medewerker wrp_c_m_new_medewerker'><select class='wrp_c_m_select' ></select>" +
    "<select class='wrp_c_h_select'><option value='8' daypart='Heledag' default>8</option><option value='4' daypart='Ochtend'>4(O)</option><option value='4' daypart='Middag'>4(M)</option></select>" +
    "<i class='material-icons wrp_btn_savemedewerker' onclick='saveMedewerker(" + contact_id + "," + werkplanning_id + "," + block_id + ")'>arrow_upward</i></div>";
   
    if($(block_container) . hasClass("wrp_c_c_block_multiday"))
    {
        rawHtml = "<div class='wrp_c_m_medewerker wrp_c_m_new_medewerker'><select class='wrp_c_m_select' ></select>" +
    "<select class='wrp_c_h_select'><option value='" + (8 * $(block_container).attr("daycount")) + "' daypart='Meerdere dagen' default>" + (8 * $(block_container).attr("daycount")) + "</option></select>" +
    "<i class='material-icons wrp_btn_savemedewerker' onclick='saveMedewerker(" + contact_id + "," + werkplanning_id + "," + block_id + ")'>arrow_upward</i></div>"
    }
    
    $(container).append(rawHtml);

    $.ajax({
        type: "POST",
        url: "../php/settings/get_employee_list.php",
        dataType: "json",
        success: function(result) {
            var employee_list = result['data'];
            rawHtml = "";
            for(var jdex = 0; jdex < employee_list.length; jdex ++)
            {
                if(employee_list[jdex]['inweekplanning'] == 'Ja')
                {
                    var start_date = employee_list[jdex]['aankomst_datum'];
                    var start_date_2 = employee_list[jdex]['aankomst_datum2'];
                    var end_date = employee_list[jdex]['vertrek_datum'];
                    var end_date_2 = employee_list[jdex]['vertrek_datum2'];
                    var weekday = new Date();
                    weekday.setMonth(displayed_Monday.getMonth());
                    weekday.setDate(displayed_Monday.getDate() + (day - 1));

                    var str_date = formatDate(weekday);

                    if((str_date >= start_date && str_date <= end_date) || (start_date_2 != "" && end_date_2 != "" && str_date >= start_date_2 && str_date <= end_date_2))
                        rawHtml += "<option value='" + employee_list[jdex]['id'] + "'>" + employee_list[jdex]['name'] +" " +  employee_list[jdex]['achternaam'] + "</option>";
                }
                
            }
            $(container + " .wrp_c_m_select").html(rawHtml);
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
    });
}

function saveActivity(contact_id, werkplanning_id, block_id)
{
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    var container = "#wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" + block_id;
    var text = $(container + " .wrp_c_c_a_text").val();

    if(data != false)
    {
        werkplanning_id = getNewWerkplanningID(data);
        block_id = getNewBlockID(data, block_id);
    }

    var postdata = {
        werkplanning_id : werkplanning_id,
        contact_id : contact_id,
        block_id : block_id,
        text : text
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/save_activity.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                if(data == false){
                    data = result['data'];
                    var rawHtml = "<div class='wrp_c_c_activity' id='wrp_c_c_activity_" + data['id'] + "'><div class='wrp_activity_label'>" + data['text'] + "</div><i class='material-icons wrp_btn_deleteactivity' onclick='deleteActivity(" + contact_id + "," + werkplanning_id + "," + data['id'] + ")'>delete</i>" + "</div>";
                    $(container + " .wrp_c_c_innerblock").append(rawHtml);
                    $(container + " .wrp_c_c_innerblock .wrp_c_c_new_activity").remove();
                }
                else{
                    updateCertainWerkplanning(contact_id, werkplanning_id);
                }
                
            }
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}

function saveMedewerker(contact_id, werkplanning_id, block_id)
{
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    var container = "#wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" + block_id;
    var employee_id = $(container + " .wrp_c_m_select").val();
    var hour = $(container + " .wrp_c_h_select").val();
    var daypart = $(container + " .wrp_c_h_select option:selected").attr("daypart");
    if(data != false)
    {
        werkplanning_id = getNewWerkplanningID(data);
        block_id = getNewBlockID(data, block_id);
    }
    var postdata = {
        contact_id : contact_id,
        werkplanning_id : werkplanning_id,
        block_id : block_id,
        employee_id : employee_id,
        hour : hour,
        daypart : daypart
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/save_medewerker.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                if(data == false){
                    data = result['data'];
                    var txt_hour = data['hour'];
                    if(data['daypart'] == "Ochtend")
                        txt_hour += "(O)";
                    if(data['daypart'] == "Middag")
                        txt_hour += "(M)";
                    var rawHtml = "<div class='wrp_c_m_medewerker' id='wrp_c_m_medewerker_" + data['id'] + "'><div class='wrp_medewerker_name_label'>" + data['name'] + " " +  data['achternaam'] + "</div><div class='wrp_medewerker_hour_label' hour=" + data['hour'] + ">" + txt_hour + "</div><i class='material-icons wrp_btn_deletemedewerker' onclick='deleteMedewerker(" + contact_id + "," + werkplanning_id + "," + data['id'] + ")'>delete</i>" + "</div>";
                    $(container + " .wrp_c_m_innerblock").append(rawHtml);
                    $(container + " .wrp_c_m_innerblock .wrp_c_m_new_medewerker").remove();
                    updateTotalHour(contact_id, werkplanning_id);
                }
                else{
                    updateCertainWerkplanning(contact_id, werkplanning_id);
                }
                
            }
        },
        error: function(e1, e2, e3) {
            debugger;
        }
    });
}

function activityChanged(item)
{
    var text = $(item).val();
    if(text == 'Anders..')
    {
        $(item).parent().find('input').addClass("wrp_c_c_a_text");
        $(item).parent().find('input').show();
        $(item).remove();
    }
    
}

function cloneDefaultWerkplanning(contact_id, werkplanning_id)
{
    var data = false;
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id;
    if($(container).attr('default') == 1)
        {
            data = $.xResponse("../php/werkplanning/clone_werkplanning.php", {contact_id : contact_id, werkplanning_id : werkplanning_id});
            var new_id = getNewWerkplanningID(data);
            $("#wrp_chapter_" + contact_id + "_" + werkplanning_id).attr('id', "wrp_chapter_" + contact_id + "_" + new_id);
        }

    return data;
}

function deleteBlock(contact_id, werkplanning_id, block_id)
{
    showConfirm('Weet je zeker dat je dit hele blok wilt verwijderen?', 'OK', 'red', "deleteBlockConfirm(" + contact_id + ", " + werkplanning_id + ", " + block_id + ")");
}

function deleteBlockConfirm(contact_id, werkplanning_id, block_id)
{
    closeConfirm();
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        var new_id = getNewWerkplanningID(data);
        werkplanning_id = new_id;
        block_id = getNewBlockID(data, block_id);
    }
    var postdata = {
        block_id : block_id
    }
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_block.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                if(data == false)
                    {
                        $("#wrp_c_c_block_" + contact_id + "_" + werkplanning_id + "_" + block_id).remove();
                        updateTotalHour(contact_id, werkplanning_id);
                    }
                else 
                    updateCertainWerkplanning(contact_id, werkplanning_id);
            }
        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function deleteActivity(contact_id, werkplanning_id, activity_id)
{
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        werkplanning_id = getNewWerkplanningID(data);
        activity_id = getNewActivityID(data, activity_id);
    }

    var postdata = {
        id : activity_id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_activity.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                if(data == false)
                    $("#wrp_c_c_activity_" + activity_id).remove();
                else 
                    updateCertainWerkplanning(contact_id, werkplanning_id);
            }
        }
    });
}

function deleteMedewerker(contact_id, werkplanning_id, medewerker_id)
{
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        werkplanning_id = getNewWerkplanningID(data);
    }

    var postdata = {
        id : medewerker_id
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_medewerker.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                if(data == false)
                {
                    $("#wrp_c_m_medewerker_" + medewerker_id).remove();
                    updateTotalHour(contact_id, werkplanning_id);
                } 
                    
                else 
                    updateCertainWerkplanning(contact_id, werkplanning_id);
            }
        }
    });
}

function updateTotalHour(contact_id, werkplanning_id)
{
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id;
    var total_hour = 0;
    $(container + " .wrp_c_m_medewerker .wrp_medewerker_hour_label").each(function(){
        total_hour += parseInt($(this).attr("hour"));
    });
    $(container + " .wrp_c_h_timer_panel .wrp_total_hour").text((total_hour + parseInt($(container + " .wrp_c_h_timer_panel .wrp_total_hour").attr('except_current_week'))) +  "h");
}

function filter_changed()
{
    var filter = $("#project_filter").val();
    if(filter == '')
    {
        $(".wrp_list .wrp_chapter").show();
    }
    else{
        $(".wrp_list .wrp_chapter").hide();
        $(".wrp_list .wrp_chapter[contact_id=" + filter + "] ").show();
    }
    
}

function editWrpChapterHeader(contact_id, werkplanning_id)
{
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_chapter_header";
    // var hour = $(container + " .wrp_default_hour").text().replace('h', '');
    $( "#wrp_chapter_" + contact_id + "_" + werkplanning_id).addClass("wrp_chapter_edit");
    // $(container + " .wrp_default_hour").html('<input class="wrp_default_hour_input" type="text"/>h');
    // $(container + " .wrp_default_hour_input").val(hour);
    $(container + " .wrp_c_h_name").prop('readonly', false);
    var rawHtml = "<div class='actiebutton btn-save' onclick='saveWrpChapterHeader(" + contact_id + "," + werkplanning_id + ")'><i class='material-icons'>save</i></div>";
    rawHtml += "<div class='actiebutton btn-delete' onclick='deleteWrpChapter(" + contact_id + "," + werkplanning_id + ")'><i class='material-icons'>delete</i></div>";
    $(container + " .wrp_c_h_timer_panel .icon-list").html(rawHtml);

}

function saveWrpChapterHeader(contact_id, werkplanning_id)
{
    var data = cloneDefaultWerkplanning(contact_id, werkplanning_id);
    if(data != false)
    {
        var new_id = getNewWerkplanningID(data);
        werkplanning_id = new_id;
    }
    var container = "#wrp_chapter_" + contact_id + "_" + werkplanning_id + " .wrp_chapter_header";
    var name = $(container + " .wrp_c_h_name").val();
    var postdata = {
        contact_id : contact_id,
        werkplanning_id : werkplanning_id,
        name : name
    }

    $.ajax({
        type: "POST",
        url: "../php/werkplanning/update_werkplanning.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                melding('Werkplanning opgeslagen.', 'groen');
               if(data == false)
               {
                $(container + " .wrp_c_h_name").prop('readonly', true);
                var rawHtml = "<div class='actiebutton btn-edit' onclick='editWrpChapterHeader(" + contact_id + "," + werkplanning_id + ")'><i class='material-icons'>edit</i></div>";
                rawHtml += "<div class='actiebutton btn-edit' onclick='deleteWrpChapter(" + contact_id + "," + werkplanning_id + ")'><i class='material-icons'>delete</i></div>";
                $(container + " .wrp_c_h_timer_panel .icon-list").html(rawHtml);
                $( "#wrp_chapter_" + contact_id + "_" + werkplanning_id).removeClass("wrp_chapter_edit");
               }
               else{
                    updateCertainWerkplanning(contact_id, werkplanning_id);
               }
            }
        }
    });


}

function addNewWerkPlan()
{
    prefillModal('Project toevoegen', 'addWerkPlan.php').then(function() {
        showPrefilledModal();
    });
}

function saveNewWerkplanning()
{
    var contact_id = $("#wrp_p_project").val();
    var errors = '';

    if($(".wrp_list .wrp_chapter[contact_id = " + contact_id + "]").length > 0)
    {
        errors = "error";
		melding('Project bestaat al in deze week', 'Rood');
        return;
    }
    var displayed_week = getWeekNumber(displayed_Monday);
    var displayed_year = displayed_Monday.getFullYear();

    var postdata = {
        contact_id : contact_id,
        certain_week : displayed_week,
        certain_year : displayed_year
    };
    
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/save_manual_werkplanning.php",
        data: postdata,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                melding('Werkplanning opgeslagen.', 'groen');
                closeModal();
                var werkplanning_id = result['werkplanning_id'];
                var rawHtml = "<div class='wrp_chapter' id='wrp_chapter_" + contact_id + "_" + werkplanning_id + "' contact_id=" + contact_id + " default=0>";
                rawHtml += "<div class='wrp_chapter_header'>";
                rawHtml += "</div>";
                rawHtml += "<div class='wrp_chapter_content'>";
                rawHtml += "</div>";
                rawHtml += "</div>";
                $(".wrp_list").prepend(rawHtml);
                updateCertainWerkplanning(contact_id, werkplanning_id);

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

function deleteWrpChapter(contact_id, werkplanning_id)
{
    showConfirm('Project verwijderen van deze en toekomstige weken?', 'Verwijderen', 'red', "deleteWrpChapterConfirm(" + contact_id + "," + werkplanning_id + ")");
}

function deleteWrpChapterConfirm(contact_id, werkplanning_id)
{
    closeConfirm();
    var displayed_week = getWeekNumber(displayed_Monday);
    var displayed_year = displayed_Monday.getFullYear();
    var data = {
        contact_id : contact_id,
        id : werkplanning_id,
        end_year : displayed_year,
        end_week : displayed_week
    };
    $.ajax({
        type: "POST",
        url: "../php/werkplanning/delete_phase.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['status'] == 'success')
            {
                $("#wrp_chapter_" + contact_id + "_" + werkplanning_id).remove();
            }
        },
        error: function(e1, e2, e3)
        {
            
        }
    });
}

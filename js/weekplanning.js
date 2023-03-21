var short_month_list = ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'];

var currentDate = new Date();
var displayed_Monday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay() + 1));

var copied_plan_id = null;
displayData(displayed_Monday);


function leaveDropZone(ev) {
    $(ev.target).removeClass('hover');
    ev.preventDefault();
}

function allowDrop(ev) {

    $(ev.target).addClass('hover');
    ev.preventDefault();
    
}

function dragstart(ev)
{
    ev.dataTransfer.setData("text", ev.target.id);
    $(".calender-week-table .project-panel").attr("ondragover", "allowDrop(event)");
}
function drop(ev) {
    ev.preventDefault();
    $(ev.target).removeClass('hover');
    var year = $(ev.target).attr('p-year');
    var month = $(ev.target).attr('p-month');
    var day = $(ev.target).attr('p-day');
    var item = $("#" + ev.dataTransfer.getData("text"));
    var employee_id = $(ev.target).parent().attr('employeerow');
    var plan_id = item.attr("plan");
    var contact_id = item.attr("contact");
    $(ev.target).append(item);


    var datum = year + "-" + month + "-" + day;

    var data = {
        contact_id : contact_id,
        employee_id : employee_id,
        datum : datum,
        plan_id : plan_id,
        werkplanning_id : plan_id
    };
    if($("#weekplaninfo #wp_ticket").is(':visible')){
        var ticket_id = $("#weekplaninfo #wp_ticket").val();
        data['ticket_id'] = ticket_id;
    }

   
    $.ajax({
        type: "POST",
        url: "../php/productie/move_weekplan.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Weekplan opgeslagen.')
            {
                melding(result['message'], 'groen');
                displayData(displayed_Monday);
            }
            else{
                melding(result['message'], 'rood');
            }

            $("#workorder").show();
            $(".popup .workorder_identify").html("#" + result['plan_id']);
            $("#weekplaninfo #plan_id").val(result['plan_id']);
        },
        error: function(e1, e2, e3)
        {

        }
    });

}


function changedDayPart()
{
    var daypart = $("#wp_daypart").val();
    if(daypart == 'Meerdere dagen')
      {
        $("#wp_datum_panel label").html("Datum van");
        $("#wp_datum_end_panel").show();
      }
      else{
        $("#wp_datum_panel label").html("Datum");
        $("#wp_datum_end_panel").hide();
      }
}

function changedProject()
{
    showFasesOfContact($("#wp_project").val());
    setTicketOfPopup($("#wp_project").val());
}

function disableEmployee(employee_id, year, month, day)
{
    var container = "tr[employeerow=" + employee_id + "] [p-year=" + year + "][p-month=" + month + "][p-day=" + day + "]";
    $(container).addClass('wp_disabled_date');
    $(container).attr('onclick', '');
}
function getWeekNumber(d) {
    // // Copy date so don't modify original
    // d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    // // Set to nearest Thursday: current date + 4 - current day number
    // // Make Sunday's day number 7
    // d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
    // Get first day of year
    var onejan  = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    // Calculate full weeks to nearest Thursday
    var weekNo = Math.ceil((((d - onejan) / 86400000) + onejan.getDay() + 1) / 7);
    // Return array of year and week number
    if(onejan.getDay() > 4)
        weekNo = weekNo - 1;
    if(d.getMonth() == 11 && d.getDate() >= 29)
        weekNo = 1;
    


    return weekNo;
}

function getWeekNumberFromDateString(d)
{

    var datestr = new Date(d);
    return getWeekNumber(datestr);
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
        weekday.setFullYear(monday.getFullYear());
        weekday.setDate(monday.getDate() + index);
        $(".calender-week-table thead span[day=" + (index+1) + "]").text(weekday.getDate() + " " + short_month_list[weekday.getMonth()]);
        $('.calender-week-table tbody tr').each(function(jdex,item)
        {
            var parent_item = $(".calender-week-table tbody tr[employeerow=" + $(item).attr('employeerow') + "] td[day_offset=" + (index+1) + "]");
            parent_item.attr('onclick','addNewWeekPlan(' + $(item).attr('employeerow') + ', ' + weekday.getFullYear() + ", " + (weekday.getMonth() + 1) + ", " + weekday.getDate()+ ')' );
            parent_item.attr('p-year', weekday.getFullYear());
            parent_item.attr('p-month', weekday.getMonth() + 1);
            parent_item.attr('p-day', weekday.getDate());
            parent_item.attr('ondragleave', 'leaveDropZone(event)'),
            parent_item.attr('ondragover', '');
            parent_item.attr('ondrop', 'drop(event)');
            parent_item.removeClass('wp_disabled_date');
            parent_item.empty();
        });


        
        
    }

    loadWeekPlan();
}

function editWeekPlan(plan_id)
{
    if(event != undefined)
    event.stopPropagation();
    $.ajax({
        type: "POST",
        url: "../php/productie/get_weekplan.php",
        data: {
            plan_id: plan_id,
            date: ''
        },
        dataType: "json",
        success: function(result_item) {
            var plan_item = result_item['week_plan_item'];
            var workorder_item = result_item['workorder_item'];
            var children_item = result_item['children_item'];
            if(plan_item.length > 0)
            {
                plan_item = plan_item[0];
                prefillVeryLargeModal('', 'addWeekPlan.php').then(function() {
                    $.ajax({
                        type: "POST",
                        url: "../php/productie/get_employees.php",
                        dataType: "json",
                        success: function(result) {
                            var html = "<div id='employee_select_panel'><select id='employee_select'>";
                            for(var index = 0; index < result.length; index ++){
                                if(result[index]['inweekplanning'] == 'Ja')
                                    html += "<option value=" + result[index]['id'] + " >" + result[index]['name'] + "</option>";
                            }
                                
                            html +="</select>";
                            html += "<i onclick='copyWeekplanning()' class='material-icons btn-modal-weekplanning-copy'>content_copy</i>";
                            html += "<i onclick='pasteWeekplanning()' class='material-icons btn-modal-weekplanning-paste'>content_paste</i></div>";
                            $(".popup.very-large .title").html(html);
                            $(".popup .btn-modal-weekplanning-paste").hide();
                            showFasesOfContact(plan_item['contact_id'], plan_item['project_planning_id']);


                            $("#wp_datum").val(plan_item['datum']);
                            $("#employee_select_panel #employee_select").val(plan_item['employee_id']);
                            $("#weekplaninfo #wp_project").val(plan_item['contact_id']);
                            $("#weekplaninfo #wp_daypart").val(plan_item['daypart']);
                            setTicketOfPopup(plan_item['contact_id'], plan_item['ticket_id']);
                            if(plan_item['datum_end'] == '' || plan_item['datum_end'] == null || plan_item['datum_end'] == undefined)
                            {
                                $("#weekplaninfo #wp_datum_end").val(plan_item['datum']);
                            }
                            else{
                                $("#weekplaninfo #wp_datum_end").val(plan_item['datum_end']);
                            }

                            changedDayPart();
                            
                            $("#weekplaninfo #plan_id").val(plan_item['id']);
                            $("#weekplaninfo #wp_text").val(plan_item['text']);
                            $("#weekplaninfo #wp_text_select").val(plan_item['text']);
                            $("#weekplaninfo #wp_text_select").hide();
                            $("#weekplaninfo #wp_text").show();
                            $('select').formSelect();
                            $(".popup .workorder_identify").html("#" + plan_item['id']);
                            for(var index = 0; index < workorder_item.length; index ++)
                            {
                                var html = getRawWorkorderHtml(workorder_item[index], true);
                                $("#table-workorder tbody").append(html);
                            }
                            $(".clear_parent").hide();
                            if(plan_item['parent_plan'] != null)
                            {
                                $(".workorder_parent").val(plan_item['parent_plan']);
                                $(".clear_parent").show();
                            }
                            if(children_item.length > 0)
                            {
                                $(".workorder_parent").hide();
                                
                                var html = '';
                                for(var index = 0; index < children_item.length; index ++)
                                {
                                    html += "<span class='workorder_child_item' onclick='editChildWeekPlan(" + children_item[index] + ")'>" + children_item[index]  + "</span>";
                                }
                                $(".workorder_children_list").html(html);
                            }
                            if(children_item.length > 0 || plan_item['parent_plan'] != null)
                            {
                                $(".workorder_tool_panel .btn_link").attr("onclick", '');
                                $(".workorder_tool_panel .btn_link").addClass("disabled");
                            }
                            
                            
                            showPrefilledVeryLargeModal('normal');
                        }
                    });
                    
                });
            }
        },
        error: function(e1, e2, e2)
        {

        }
    });
}
function setTicketOfPopup(contact_id, ticket_id = null)
{
    $(".wp_ticket_panel #wp_ticket").empty();
    $.ajax({
        type: "POST",
        url: "../php/aftersales/get_tickets.php",
        data: {contactid: contact_id, mode:'OPENED'},
        dataType: "json",
        success: function(result) {
            if(result)
            {
                $(".wp_ticket_panel #wp_ticket").append("<option></option>");
                for(var index=0; index < result.length; index ++)
                {
                    $(".wp_ticket_panel #wp_ticket").append('<option value="' + result[index].id + '">' + result[index].title + '</option>');
                }
                if(ticket_id)
                {
                    $(".wp_ticket_panel #wp_ticket").val(ticket_id);
                }
            }
        },
        error: function(e1, e2, e3){

        }
    });
}
function addNewWeekPlan(employee = '', year = '', month = '', date = '', contact_id = '', ticket_id = '')
{

    if(year == '')
    {
        year = displayed_Monday.getFullYear();
    }
    if(month == '')
    {
        month = displayed_Monday.getMonth() + 1;
       
    }
    if(date == '')
    {
        date = displayed_Monday.getDate();
        
    }
    if(month < 10)
        month = "0" + month;
    if(date < 10)
        date = "0" + date;
    prefillVeryLargeModal('', 'addWeekPlan.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/productie/get_employees.php",
            dataType: "json",
            success: function(result) {
                var html = "<div id='employee_select_panel'><select id='employee_select'>";
                for(var index = 0; index < result.length; index ++){
                    if(result[index]['inweekplanning'] == 'Ja')
                        html += "<option value=" + result[index]['id'] + ">" + result[index]['name'] + "</option>";
                }
                   
                html +="</select>";
                html += "<i onclick='copyWeekplanning()' class='material-icons btn-modal-weekplanning-copy'>content_copy</i>";
                html += "<i onclick='pasteWeekplanning()' class='material-icons btn-modal-weekplanning-paste'>content_paste</i></div>";
                $(".popup.very-large .title").html(html);
                $(".popup .btn-modal-weekplanning-copy").hide();
                if(copied_plan_id == null)
                    $(".popup .btn-modal-weekplanning-paste").hide();
                $("#wp_datum").val(year + '-' + month + '-' + date);
                $("#wp_datum_end").val(year + '-' + month + '-' + date);
                if(employee != '')
                    $("#employee_select_panel #employee_select").val(employee);
                if(contact_id != '')
                {
                    $("#wp_project").val(contact_id);
                }
                $('select').formSelect();
                changedWerkzaamheden();
                $("#weekplaninfo #plan_id").val('');
                $("#weekplaninfo #btn-delete").hide();
                $("#workorder").hide();
                
                showFasesOfContact(contact_id);
                 if(ticket_id != '')
                {
                    setTicketOfPopup($("#wp_project").val(), ticket_id);
                }
                else{
                    setTicketOfPopup($("#wp_project").val());
                }
                showPrefilledVeryLargeModal('normal');
            }
        });
        
    });
}

function showFasesOfContact(contact_id, phase_id = null)
{
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_contact_planning.php",
        data: {
            contact_id : contact_id
        },
        dataType: "json",
        success: function(result)
        {
            $("#wp_project_plan").empty();
            html = '';
            if(result)
            {
                for(var i = 0; i < result.length; i ++)
                    html += "<option value=" + result[i]['id'] + ">" + result[i]['name'] + "</option>";
                $("#wp_project_plan").append(html);
                if(phase_id != null)
                    $("#wp_project_plan").val(phase_id);
            }
            $("#wp_project_plan").formSelect();
        }
    });
}

function getDateStr(year, month, date)
{
    var str = "";
    if(month < 10)
        month = "0" + month;
    if(date < 10)
        date = "0" + date;
    str = year + "-" + month + "-" + date;
    return str;
}
function loadWeekPlan(plan_id = '')
{
    var date = displayed_Monday.getFullYear() + "-" + (displayed_Monday.getMonth() + 1) + "-" + displayed_Monday.getDate();
    $.ajax({
        type: "POST",
        url: "../php/productie/get_weekplan.php",
        data: {
            plan_id: plan_id,
            date: date
        },
        dataType: "json",
        success: function(result) {

            

            if(result['week_plan_item'].length > 0)
            {
                for(var index = 0; index < result['week_plan_item'].length; index ++)
                    insertWeekPlanHtml(result['week_plan_item'][index]);
            }


            $(".calender-week-table tbody tr").each(function(){
               
                var start_date = $(this).attr('start_date');
                var end_date = $(this).attr('end_date');
                var start_date_2 = $(this).attr('start_date_2');
                var end_date_2 = $(this).attr('end_date_2');

                $(this).removeClass('wp_disabled_employee');

                if(start_date == '' || end_date == '' || start_date == '0000-00-00' || end_date == '0000-00-00')
                {
                    $(this).find('.project-panel').addClass('wp_disabled_date');
                    $(this).addClass('wp_disabled_employee');
                    
                }
                else{
                    var self = this;
                    $(self).find('.project-panel').each(function(){
                        var str_date = getDateStr($(this).attr('p-year'), $(this).attr('p-month'), $(this).attr('p-day'));
                        $(this).addClass('wp_disabled_date');
    
                        if(str_date >= start_date && str_date <= end_date)
                            $(this).removeClass('wp_disabled_date');
                        if(start_date_2 != "" && end_date_2 != "" && str_date >= start_date_2 && str_date <= end_date_2)
                            $(this).removeClass('wp_disabled_date');
    
                    });
    
                    if($(this).find('.project-panel.wp_disabled_date').length == 6)
                        $(this).addClass('wp_disabled_employee');
                }
                


            });

            for(var index = 0; index <  6; index ++)
            {
                var employee_count = $(".calender-week-table  tbody .project-panel[day_offset=" + (index + 1) + "]").length - $(".calender-week-table  tbody .project-panel[day_offset=" + (index + 1) + "].wp_disabled_date").length;

                if(employee_count < 10)
                    employee_count = "0" + employee_count;
                $(".calender-week-table thead span[mday=" + (index+1) + "]").text(employee_count + " medewerkers");
            }
        },
        error: function(e1, e2, e2)
        {

        }
    });
}
function insertWeekPlanHtml(item)
{
    var panel = $(".calender-week-table tr[employeerow=" + item.employee_id + "] td[day_offset=" + item.w + "]");
    var existing = $(".wp-card[plan=" + item.id + "]").length;
    $(".wp-card[plan=" + item.id + "]").remove();
    //item['datum'], item['datum_end'], displayed_Monday;
    var datum = item['datum'];
    var datum_end = item['datum_end'];
    var class_name = '';
    if(item['daypart'] == "Meerdere dagen")
    {
        datum = new Date(datum);
        datum_end = new Date(datum_end);
        var displayed_Saturday = new Date(displayed_Monday.getTime() + 1000 *  3600 * 24 * 5);
        datum_end = datum_end.getTime() > displayed_Saturday.getTime() ? displayed_Saturday : datum_end;
        var start_day = datum.getTime() > displayed_Monday.getTime() ? datum:displayed_Monday;
        var diff = (datum_end.getTime() - start_day.getTime()) / (1000 * 3600 * 24);
        diff = Math.round(diff);
        if(diff > 0)
        {
            diff = diff > 5 ? 5: diff; 
            class_name = "card-d-" + diff;
        }
    }
    if(item['ticket_status'])
        class_name += " has_ticket";
    var html = "<div class='wp-card " + class_name + "' plan=" + item.id + " contact=" + item.contact_id + " onclick='editWeekPlan(" + item.id + ")' draggable='true' ondragstart='dragstart(event)' id='wp-card-" + makeid(10)+ "'>";
    if(item['ticket_status'])
        html += "<i class='material-icons wp-card-ticket-status'>report_problem</i>";
    html += "<i class='material-icons wp-card-type wp-card-" + item['color'] + "' >circle</i>";
        

    html += "<div class='wp-card-header'>" + "<span class='wp-card-pid'>#" + item.project_number + "</span>" +
    "<span class='wp-card-pname'>" + item['address'] + "</span></div>"+
    "<div class='wp-card-body'><div class='wp-card-content'>" + item['text'] + "</div></div>" + 
    "</div>";
    if(existing && item.daypart == 'Middag')
    {
        panel.append(html);
    }
    else{
        panel.prepend(html);
    }
    
}
function saveWeekPlan()
{
    var contact_id = $("#weekplaninfo #wp_project").val();
    var employee_id = $("#employee_select_panel #employee_select").val();
    var datum = $("#weekplaninfo #wp_datum").val();
    var datum_end = $("#weekplaninfo #wp_datum_end").val();
    var daypart = $("#weekplaninfo #wp_daypart").val();
    var planning = $("#wp_project_plan").val();
    var text = $("#weekplaninfo #wp_text").val();
    var plan_id = $("#weekplaninfo #plan_id").val();
    var displayed_week = getWeekNumber(displayed_Monday);
    var displayed_year = displayed_Monday.getFullYear();

    if(daypart == "Meerdere dagen")
    {
        var start_week = getWeekNumberFromDateString(datum);
        var end_week = getWeekNumberFromDateString(datum_end);
        if((start_week != end_week) || (new Date(datum).getDay() == 0) || (new Date(datum_end).getDay() == 0))
        {
            melding("Sorry, maar op zondag werken we niet?", 'rood');
            return;
        }
    }

    var data = {
        contact_id : contact_id,
        employee_id : employee_id,
        datum : datum,
        datum_end : datum_end,
        daypart : daypart,
        planning : planning,
        text : text,
        plan_id : plan_id,
        year : displayed_year,
        week : displayed_week
    };
    if($("#weekplaninfo #wp_ticket").is(':visible')){
        var ticket_id = $("#weekplaninfo #wp_ticket").val();
        data['ticket_id'] = ticket_id;
    }

   
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
                data['werkplanning_id'] = result['werkplanning_id'];
                $.ajax({
                    type: "POST",
                    url: "../php/productie/save_weekplan.php",
                    data: data,
                    dataType: "json",
                    success: function(result) {
                        if(result['message'] == 'Weekplan opgeslagen.')
                        {
                            melding(result['message'], 'groen');
                            displayData(displayed_Monday);
                        }
                        else{
                            melding(result['message'], 'rood');
                        }
            
                        $("#workorder").show();
                        $(".popup .workorder_identify").html("#" + result['plan_id']);
                        $("#weekplaninfo #plan_id").val(result['plan_id']);
                    },
                    error: function(e1, e2, e3)
                    {
            
                    }
                });

            }
            else{
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function deleteWeekPlan()
{
    var plan_id = $("#weekplaninfo #plan_id").val();
    $.ajax({
        type: "POST",
        url: "../php/productie/delete_weekplan.php",
        data: {
            plan_id: plan_id
        },
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Notitie item verwijderd.')
            {
                melding(result['message'], 'groen');
                $(".wp-card[plan=" + plan_id + "]").remove();
                if(copied_plan_id == plan_id)
                {
                    copied_plan_id = null;
                }
            }
            else{
                melding(result['message'], 'rood');
            }

            
            closeVeryLargeModal();
        }
    });
}

function filter_changed()
{
    var filter = $("#project_filter").val();
    if(filter == '')
    {
        $(".calender-week-table .wp-card").show();
    }
    else{
        $(".calender-week-table .wp-card").hide();
        $(".calender-week-table .wp-card[contact=" + filter + "] ").show();
    }
    
}


function  addNewWorkOrder()
{
    var id = makeid(15);
    var html = "<tr randid='" + id + "'><td><input type='text' class='wo_description' placeholder='Omschrijving'/></td>" + 
            "<td><input type='text' class='wo_material' placeholder='Materiaal'/></td>" + 
            "<td><input type='text' class='wo_tool' placeholder='Gereeschap'/></td>" +
            "<td></td><td></td>" + 
            "<td><div onclick='saveWorkOrder(\"" + id + "\")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>save</i></div></td>" +
            "</tr>";
    $("#table-workorder").append(html);
}

function endWorkOrder(workorder_id)
{
    $.ajax({
        type: "POST",
        url: "../php/productie/end_workorder.php",
        data: {
            workorder_id: workorder_id
        },
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Werkorder ended.')
            {
                melding(result['message'], 'groen');
                var item = result['item'][0];
                var html = getRawWorkorderHtml(item);
                $("#table-workorder tr[order_id=" + item.id + "]").html(html);
            }
            else{
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}

function restartWorkOrder(workorder_id)
{
    $.ajax({
        type: "POST",
        url: "../php/productie/restart_workorder.php",
        data: {
            workorder_id: workorder_id
        },
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Werkorder restarted.')
            {
                melding(result['message'], 'groen');
                var item = result['item'][0];
                var html = getRawWorkorderHtml(item);
                $("#table-workorder tr[order_id=" + item.id + "]").html(html);
            }
            else{
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}


function getRawWorkorderHtml(item, includinghead = false)
{
    var html = "";
    if(includinghead)
        html += "<tr order_id='" + item.id + "'>";
    html += "<td>" + item.description + "</td>" + 
            "<td>" + item.material + "</td>" + 
            "<td>" + item.tool + "</td>";
    if(item.status == 'PROGRESSING')
    {
        html += "<td></td><td><span class='button btn-end' onclick='endWorkOrder(" + item.id + ")'><i class='material-icons'>close</i></span></td>";
    }
    else if(item.status == 'ENDED')
    {
        html += "<td><div class='actiebutton' onclick='restartWorkOrder(" + item.id + ")'><i class='material-icons'>refresh</i></div></td>";
        html += "<td>" + item.timer_widget + "</td>";
    }

    html += "<td><div class='actiebutton' onclick='deleteWorkOrder(" + item.id + ")'><i class='material-icons'>delete</i></div></td>"
        
    if(includinghead)
        html += "</tr>";
    return html;
}

function deleteWorkOrder(workorder_id)
{
    $.ajax({
        type: "POST",
        url: "../php/productie/delete_workorder.php",
        data: {
            workorder_id : workorder_id
        },
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Werkorder regel verwijderd')
            {
                melding(result['message'], 'groen');
                
                $("#table-workorder tr[order_id=" + workorder_id + "]").slideUp();

            }
            else{
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}

function saveWorkOrder(randid)
{
    var desc = $("#table-workorder tr[randid=" + randid + "] .wo_description").val();
    var matr = $("#table-workorder tr[randid=" + randid + "] .wo_material").val();
    var tool = $("#table-workorder tr[randid=" + randid + "] .wo_tool").val();
    var plan_id = $("#weekplaninfo #plan_id").val();
    let errors = "";
    if(desc == '')
    {
        errors = "error";
        melding('Vul een omschrijving in', 'rood');
    }
    else if(matr == '')
    {
        errors = "error";
        melding('Vul een materiaal in', 'rood');
    }
    else if(tool == '')
    {
        errors = "error";
        melding('Vul een gereeschap in', 'rood');
    }

    var data = {
        description: desc,
        material: matr,
        tool: tool,
        plan_id: plan_id
    };
    if(errors != 'error')
    {
        $.ajax({
            type: "POST",
            url: "../php/productie/save_workorder.php",
            data: data,
            dataType: "json",
            success: function(result) {
                if(result['message'] == 'Werkorder opgeslagen.')
                {
                    melding(result['message'], 'groen');
                    var item = result['item'][0];
                    var html = getRawWorkorderHtml(item);
                    $("#table-workorder tr[randid=" + randid + "]").html(html);
                    $("#table-workorder tr[randid=" + randid + "]").attr('order_id', item.id);
                    $("#table-workorder tr[randid=" + randid + "]").removeAttr('randid');
                }
                else{
                    melding(result['message'], 'rood');
                }
            },
            error: function(e1, e2, e3) {
    
            }
        });
    }
    
}

function showWorkOrderTable()
{
    var plan_id = $("#weekplaninfo #plan_id").val();
    var url = $("#root_path").val() + "workorder?plan_id=" + plan_id;
    window.open(url, '_blank').focus();

}
function editChildWeekPlan(plan_id)
{
    closeVeryLargeModal();
    setTimeout(function(){ 

        editWeekPlan(plan_id);
        
    }, 400);
}


var error_timer = null;
function showError(){
    $(".workorder_parent").addClass('error');
    clearTimeout(error_timer);
    error_timer = setTimeout(function(){
        $(".workorder_parent").removeClass('error');
    }, 3000);
}
function linkToParent()
{
    var parent = $(".workorder_parent").val();
    var plan_id = $("#weekplaninfo #plan_id").val();
    if($.isNumeric(parent) == false || parent == plan_id)
    {
        showError();
        return;
    }
    var data = {
        parent: parent,
        plan_id: plan_id
    };
    $.ajax({
        type: "POST",
        url: "../php/productie/link_workorder.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Werkorder linked.')
            {
                $("#table-workorder tbody").html("");
                for(var index = 0; index < result['items'].length; index ++)
                {
                    var html = getRawWorkorderHtml(result['items'][index], true);
                    $("#table-workorder tbody").append(html);
                    
                }
                $(".workorder_tool_panel .btn_link").addClass('disabled');
                $(".workorder_tool_panel .btn_link").attr('onclick', '');
                $(".workorder_tool_panel .clear_parent").show();
            }
            else{
                showError();
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}

function clearParent(){
    var plan_id = $("#weekplaninfo #plan_id").val();
    data = {
        plan_id : plan_id
    };
    $.ajax({
        type: "POST",
        url: "../php/productie/delink_workorder.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Werkorder delinked.')
            {
                $("#table-workorder tbody").html("");
                for(var index = 0; index < result['items'].length; index ++)
                {
                    var html = getRawWorkorderHtml(result['items'][index], true);
                    $("#table-workorder tbody").append(html);
                }
                $(".clear_parent").hide();
                $(".workorder_parent").val("");
                $(".workorder_tool_panel .btn_link").removeClass('disabled');
                $(".workorder_tool_panel .btn_link").attr('onclick', 'linkToParent()');
            }
           
        },
        error: function(e1, e2, e3) {

        }
    });
}


function changedWerkzaamheden()
{
    $("#wp_text").val($("#wp_text_select").val());
    if($("#wp_text_select").val() == '')
    {
        $("#wp_text_select").hide();
        $("#wp_text").show();
    }
}


function copyWeekplanning()
{
    copied_plan_id = $("#weekplaninfo #plan_id").val();
    melding('Weekplan was copied to clipboard', 'groen');

}

function pasteWeekplanning()
{
    var employee_id = $("#employee_select_panel #employee_select").val();
    var data = {
        plan_id : copied_plan_id,
        employee_id : employee_id
    };
    $.ajax({
        type: "POST",
        url: "../php/productie/clone_weekplan.php",
        data: data,
        dataType: "json",
        success: function(result) {
            displayData(displayed_Monday);
            melding(result['message'], 'groen');
            editWeekPlan(result['weekplanning_id']);
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
        
    });
}

$(document).ready(function() {
    displayData(displayed_Monday);
    if(getCookie('page') == 'WERKPLANNING')
    {
        addNewWeekPlan('', '', '','', getCookie('contact_id'), getCookie('ticket_id'));
        setCookie("contact_id", '');
        setCookie("ticket_id", '');
        setCookie("page", "");
    }
    
});

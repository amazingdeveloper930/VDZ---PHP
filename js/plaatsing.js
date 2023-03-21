var status_list = ['', 'Active', 'Inactive', 'Lead'];
var source_list = ['', 'Facebook', 'Website', 'Adwords'];
var contact_type = ['', 'E-mail', 'Telefoon', 'Gesprek', 'Whatsapp'];
var lead_type = ['', 'Deal', 'Geen deal', 'Wijzigen offerte', 'Wacht op antwoord', 'Afspraak'];

function getFormatedDate(date) {
    let fdate = new Date(date);
    return ("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear();
}

function getFormatedDateTime(date) {
    let fdate = new Date(date);
    return ("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear() + ' ' + ("0" + fdate.getHours()).slice(-2) + ':' + ("0" + fdate.getMinutes()).slice(-2);
}

function deleteContact(contactid) {
    showConfirm('Dit contact verwijderen?', 'Verwijderen', 'red', 'deleteContactConfirm(' + contactid + ')');
}

function deleteContactConfirm(contactid) {
    closeConfirm();
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_contact.php",
        data: {
            'contact': contactid
        },
        dataType: "html",
        success: function(result) {
            //alert(result);
            if (result == 'Contact verwijderd.') {
                //Alles ging goed
                melding(result, 'groen');
                $("tr[contactrow=" + contactid + "]").slideUp();
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}


function saveContactInfo() {
    var contactid = $("#contactinfo .contactid").val();
    let user = $("#contactinfo #name").val();
    let city = $("#contactinfo #city").val();
    let address = $("#contactinfo #address").val();
    let email = $("#contactinfo #email").val();
    let phone = $("#contactinfo #phone").val();
    //let source = $("#contactinfo #source").val();
    //let c_status = $("#contactinfo #c_status").val();
    let errors = "";
    if (user == '') {
        errors = "error";
        melding('Vul een gebruikersnaam in', 'Rood');
    } else if (city == '') {
        errors = "error";
        melding('Vul een stad in', 'Rood');
    } else if (address == '') {
        errors = "error";
        melding('Vul een adres in', 'Rood');
    } else if (email == '') {
        errors = "error";
        melding('Vul een e-mailadres in', 'Rood');
    } else if (phone == '') {
        errors = "error";
        melding('Vul een telefoonummer in', 'Rood');
    }
    if (errors == '') {
        var data = $("#contactinfo").serializeArray();
        $.ajax({
            type: "POST",
            url: "../php/opdracht/save_contact.php",
            data: data,
            dataType: "html",
            success: function(result) {
                //alert(result);
                if (result == 'Contact opgeslagen.') {
                    melding(result, 'groen');
                    if (contactid != '') {
                        updateContactTableRow(contactid);
                    } else {
                        addNewContactTableRow();
                    }
                    closeModal();
                } else {
                    melding(result, 'rood');
                }
            }
        });
    }
};



function editContact(contactid) {
    prefillModal('Contact wijzigen', 'addLead.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/opdracht/get_contact.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {
                for (var i = 0; i < result.length; i++) {
                    var name = result[i].name;
                    var city = result[i].city;
                    var address = result[i].address;
                    var email = result[i].email;
                    var phone = result[i].phone;

                    $("#contactinfo .contactid").val(contactid);
                    $("#contactinfo #name").val(name);
                    $("#contactinfo #city").val(city);
                    $("#contactinfo #address").val(address);
                    $("#contactinfo #email").val(email);
                    $("#contactinfo #phone").val(phone);
                    updateTimerWiget(contactid);
                    $(".popup.large .logs").html('');
                    showPrefilledModal();
                }
            }
        });
    });
}

function updateTimerWiget(contactid)
{
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_closest_task.php",
        data: {
            contactid: contactid
        },
        dataType: "json",
        success: function(result) {
            if(result){
                var name = result.name;
                var email = result.email;
                var phone = result.phone;
                var timer_widget = result.timer_widget;
                var address = result.address;
                var title = '<span class="name">' + name + ' </span>' + ((timer_widget) ? timer_widget : '') + '<span class="city">' + email + " - " + phone + " - " + address +  '</span>';
                $(".popup.large .title").html(title);
                $(".popup.very-large .title").html(title);
                $(".contacten-table tr[contactrow=" + contactid + "] td:nth-child(7)").html(((timer_widget) ? timer_widget : ''));
            }
        },
        error: function(e1, e2, e3)
        {

        }
    });
}
function manageContactLog(contactid) {
    prefillModal('Logboek', 'manageContactLog.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/opdracht/get_contact_log.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {
                if (result) {
                    var contactloghtml = '';
                    $("#contactlog .contactid").val(contactid);

                    updateTimerWiget(contactid);
                    for (var i = 0; i < result.length; i++) {
                        var clid = result[i].clid;
                        var type = result[i].entry_type;

                        
                        if (clid) {
                            var type_Str = contact_type[type];
                            if (result[i].entry_title == 'lead')
                                type_Str = lead_type[type];
                            if (type != 0 && !(type == 1 && result[i].entry_title == 'lead'))
                            {

                                contactloghtml += getContactlogStr(contactid,clid, result[i]);


                            }
                                
                        }
                    }
                    $(".popup.large .logs").html(contactloghtml);
                    $('.tooltipped').tooltip();
                    showPrefilledModal();
                }
            },
            error: function(e1, e2, e3) {

            }
        });
    });
}

function saveContactLog() {
    let date = new Date().toISOString().slice(0, 19).replace("T", " "); //convert js datetime format to mysql one.
    $("#contactlog .date").val(date);
    let contactid = $("#contactlog .contactid").val();
    let userid = $("#contactlog .userid").val();
    let type = $("#contactlog #type").val();
    let desc = $("#contactlog #desc").val();
	let entrydate = $("#contactlog #entrydate").val();
    let entrytime = $("#contactlog #entrytime").val();
    var files = $('#contactlog #file')[0].files;
    let errors = "";
    if (contactid == '') {
        errors = "error";
        melding('Fatale fout', 'Rood');
    } else if (userid == '') {
        errors = "error";
        melding('Fatale fout', 'Rood');
    } else if (type == '') {
        errors = "error";
        melding('kies een contactwijze', 'Rood');
    } else if (desc == '') {
        errors = "error";
        melding('Vul een notitie in', 'Rood');
    } else if(entrydate == '') {
		errors = "error";
		melding('Vul een datum in', 'Rood');
	} else if(entrytime == '') {
        errors = "error";
		melding('Vul een tijd in', 'Rood');
	}

    
    if (errors == '') {
        var data_array = $("#contactlog").serializeArray();
        
        var data = new FormData();

        for(var index = 0; index < data_array.length; index ++)
        {
            data.append(data_array[index]['name'],data_array[index]['value']);
        }
        if(files.length > 0)
            data.append('file',files[0]);
        else 
            data.append('file', '');
        
        $(".file-loading-icon").show();
        $(".file-icon").hide();

        $.ajax({
            type: "POST",
            url: "../php/opdracht/save_contact_log.php",
            data: data,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                if (result['message'] == 'Logboek opgeslagen.') {
                    //Alles ging goed
                    melding(result['message'], 'groen');
                    if (contactid != '') {

                       
                        addNewContactLog(result['new_one'], result['next_one']);
                        
                        $("#contactlog #type").val('');
						$("#contactlog #type").formSelect();
						$("#contactlog #desc").val('');
						$("#contactlog #entrydate").val('');
						$("#contactlog #entrytime").val('');
                        $("#contactlog #file").val("");
                        $("#contactlog .btn-filelog").removeClass('file_selected');
                        $("#contactlog .file-loading-icon").hide();
                        $("#contactlog .file-icon").show();
                    }
                } else {
                    //Er ging iets mis
                    melding(result['message'], 'rood');
                }
            }
        });
    }
};

function addNewContactLog(new_one, next_one) {
    var id = new_one['id'];

	var contactid = new_one['contact_id'];

	var contactloghtml = getContactlogStr(contactid, id, new_one, true);
    var file_path = new_one['file_path'];
    
	var item = $(".popup.large .logs");
	if(next_one != ''){
		item = $("#clog-" + next_one['id']);
		item.after(contactloghtml);
	}
		
	else{
		item.prepend(contactloghtml);
	}
	$('.tooltipped').tooltip();
	$('#clog-' + id).slideDown();
}

function showInnerConfirm(clID) {
    $("#ipc-" + clID).show();
    $("#ipo-" + clID).show();
    $("#ipc-" + clID).addClass('visible');
    $("#ipo-" + clID).addClass('visible');
}

function closeInnerConfirm(clID) {
    $("#ipc-" + clID).removeClass('visible');
    $("#ipo-" + clID).removeClass('visible');
    setTimeout(function() {
        $("#ipo-" + clID).hide();
        $("#ipc-" + clID).hide();
    }, 300);
}

function deleteCLogConfirm(contactid, clID) {
    //closeConfirm();
    closeInnerConfirm(clID)
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_contact_log.php",
        data: {
            'clog': clID
        },
        dataType: "html",
        success: function(result) {
            if (result == 'Logboek item verwijderd.') {
                //Alles ging goed
                melding(result, 'groen');
                $("#clog-" + clID).slideUp();
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}

function updateContactTableRow(contactid) {
    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_contact_log.php",
        data: {
            contactid: contactid
        },
        dataType: "html",
        success: function(result) {
            if (result) {
                if (!Array.isArray(result)) result = $.parseJSON(result);
                var id = result[0].cid;
                var name = result[0].name;
                var city = result[0].city;
                var address = result[0].address;
                
                $("tr[contactrow=" + id + "] td:nth-child(2)").html(name);
                $("tr[contactrow=" + id + "] td:nth-child(3)").html(city);
                $("tr[contactrow=" + id + "] td:nth-child(4)").html(address);
            }
        }
    });
}





function getTaskRowFromRawData(contactid, task_line, suppliers)
{
    if(task_line['factuurnummer'] == null)
        task_line['factuurnummer'] = '';
    var html = "<td class='task_name'>" + task_line['name'] + "</td>";

    if(task_line['name'].endsWith("betaald") && task_line['isspecial_task'])
    html += "<td><span class='button btn' onclick='completeTask(" + contactid + ", " + task_line['id'] + ")'><i class='material-icons'>done</i> Opslaan</span>";
    else
    html += "<td><span class='button btn' onclick='completeTask(" + contactid + ", " + task_line['id'] + ")'><i class='material-icons'>done</i> Gedaan</span>";
    if (task_line['not_necessary'] == 'true')
        html += "<span class='button btn' onclick='skipTask(" + contactid + ", " + task_line['id'] + ")'>Niet nodig</span>";
    if(task_line['status'] == 'COMPLETED' || task_line['status'] == 'SKIPPED')
        html += "<span class='user-info'><i class='material-icons'>person</i>" + task_line['username'] + "</span>";
    html += "</td>";
    // if (task_line['supplier'] == 'true')
    if (task_line['status'] == 'PROCESSING' || task_line['status'] == 'COMPLETED' || task_line['status'] == 'SKIPPED') {
        if(task_line['chapter'] == 1){
            if(task_line['isspecial_task'] == 1 && task_line['is_invoice'] == 1)
            html += "<td class='td_factuurnummer'><input type='text' value='" + task_line['factuurnummer'] + "' class='line_factuurnummer' onchange='saveTaskSpecialValues(" + contactid + ", " + task_line['id'] + ")'/></td>";
            else 
                    html += "<td></td>";
        }
        
        if(task_line['isspecial_task']){
            html += "<td class='td_price_inc'>";
            var price = task_line['price'];
            if(price == null)
            price = '';
            var price_inc = task_line['price_inc'];
            if(price_inc == null)
            price_inc = '';

            html += "&euro;<input type='text' value='" + price_inc + "' class='line_price_inc special_number' placeholder='0.00' onchange='saveTaskSpecialValues(" + contactid + ", " + task_line['id'] + ")'/>";
            html += "</td>";
            
            html += "<td class='td_price'>";
            html += "&euro;<input type='text' value='" + price + "' class='line_price special_number' placeholder='0.00' onchange='saveTaskSpecialValues(" + contactid + ", " + task_line['id'] + ")'/>";
            html += "</td>";

            html += "<td class='td_betaaldatum'>";
            html += "<input type='date' value='" + task_line['betaaldatum'] + "' class='line_betaaldatum ' onchange='saveTaskSpecialValues(" + contactid + ", " + task_line['id'] + ")'/>";
            html += "</td>";
            html += "<td></td>";
        }
        else
        {
        
        html += "<td class='td_supplier'>";
        if (task_line['supplier'] == 'false')
            html += "<p class='text-null'>N.v.t.</p>";
        else {
            html += "<select class='line_supplier' onchange='saveTask(" + contactid + ", " + task_line['id'] + ")'><option></option>";
            for (var k = 0; k < suppliers.length; k++) {
                if (suppliers[k]['id'] == task_line['supplier_id'])
                    html += "<option value=" + suppliers[k]['id'] + " selected>" + suppliers[k]['name'] + "</option>";
                else
                    html += "<option value=" + suppliers[k]['id'] + ">" + suppliers[k]['name'] + "</option>";
            }
            html += "</select>";
        }

        html += "</td><td class='td_bdate'>";
        if (task_line['order_date'] == 'false')
            html += "<p class='text-null'>N.v.t.</p>";
        else {
            html += "<input type='date' value='" + task_line['besteldatum'] + "' class='line_bdate' onchange='saveTask(" + contactid + ", " + task_line['id'] + ")'/>";
        }

        html += "</td><td class='td_sdate'>";
        if (task_line['supply_date'] == 'false')
            html += "<p class='text-null'>N.v.t.</p>";
        else {
            html += "<input type='date' value='" + task_line['leverdatum'] + "' class='line_sdate' onchange='saveTask(" + contactid + ", " + task_line['id'] + ")'/>";
        }

        html += "</td>";
        if (task_line['supply_date'] == 'false')
            html += "<td></td>";
        else {
            html += "<td><label><input type='checkbox' class='filled-in cb-special-jaarplanning'  onchange='saveTask(" + contactid + ", " + task_line['id'] + ")' ";
            if(task_line['special_jaarplanning'] == 'YES')
                html += "checked='checked'";
            html += "><span></span></label></td>";
        }
    }
    } else {
        if(task_line['chapter'] == 1){
            if(task_line['isspecial_task'] == 1 && task_line['is_invoice'] == 1)
                html += "<td class='td_factuurnummer'><p class='text-null'>Invullen</p></td>";
             else 
                html += "<td></td>";
        }
        if(task_line['isspecial_task']){
            html += "<td class='td_price'><p class='text-null'>Invullen</p></td>";
            html += "<td class='td_price_inc'><p class='text-null'>Invullen</p></td>";
            html += "<td class='td_betaaldatum'><p class='text-null'>Invullen</p></td>";
            html += "<td></td>";
        }
        else
        {
        if (task_line['supplier'] == 'true')
            html += "<td class='td_supplier'><p class='text-null'>Invullen</p></td>";
        else if (task_line['order_date'] == 'false')
            html += "<td class='td_supplier'><p class='text-null'>N.v.t.</p></td>";


        if (task_line['order_date'] == 'true')
            html += "<td class='td_bdate'><p class='text-null'>Invullen</p></td>";
        else if (task_line['order_date'] == 'false')
            html += "<td class='td_bdate'><p class='text-null'>N.v.t.</p></td>";

        if (task_line['supply_date'] == 'true'){
            html += "<td class='td_sdate'><p class='text-null'>Invullen</p></td>";
            html += "<td><label><input type='checkbox' class='filled-in cb-special-jaarplanning' disabled><span></span></label></td>";
        }
        else if (task_line['supply_date'] == 'false')
            html += "<td class='td_sdate'><p class='text-null'>N.v.t.</p></td><td></td>";
        }
        
    }

    
    html += "<td><div class='actiebutton btn-refresh' onclick='restartTask(" + contactid + ", " + task_line['id'] + ")'><i class='material-icons'>refresh</i></div></td>" +
        "<td>" + task_line['timer_widget'] + "</td>" +
        "<td><div  class='actiebutton tooltipped icon_tasknote ";
        if(task_line['note_count'] > 0)
        html += " hasnote ";
        html += "' data-position='top'  onclick='taskNotes(" + contactid + ", " + task_line['id'] + ")'><i class='material-icons'>insert_comment</i></div>";
        if(task_line['custom_contact_id'] != null)
        html += "<div class='actiebutton tooltipped' data-position='top' onclick='deleteProjectTask(" + contactid + ", " + task_line['id'] + ")'><i class='material-icons'>delete</i></div><div class='inner-popup-overlay'>" + 
        '<div class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeTaskInnerConfirm(' + contactid + ', ' + task_line['id'] + ')">Annuleren</span><span class="button red" onclick="deleteTaskConfirm(' + contactid + ', ' + task_line['id'] + ')">Verwijderen</span></div></div>'
        + "</div>"
        html += "</td>";
    return html;
}


function deleteProjectTask(contact_id, task_id){
    $("tr.project_task[task_id=" + task_id + "] .inner-popup-overlay").show();
    $("tr.project_task[task_id=" + task_id + "] .inner-popup-overlay").addClass('visible');
    $("tr.project_task[task_id=" + task_id + "] .inner-confirm").show();
    $("tr.project_task[task_id=" + task_id + "] .inner-confirm").addClass('visible');
}

function deleteTaskConfirm(contact_id, task_id){
    closeTaskInnerConfirm(contact_id, task_id);
    $.ajax({
		type: "POST",
		url: "../php/opdracht/delete_project_task.php",
		data: {
			'contact_id': contact_id,
            'task_id' : task_id
		},
        dataType: 'json',
        success: function(result){
            if(result['message'] == 'Notitie item verwijderd.')
                {
                    melding(result['message'], 'groen');
                    var items = result['items'];
                    for(var index = 0; index < items.length; index ++)
                        $("#projectinfo .project_task[task_id=" + items[index] + "]").slideUp();
                }
        },
        error: function(e1, e2, e3){

        }
    })
}

function closeTaskInnerConfirm(contact_id, task_id) {
	$("tr.project_task[task_id=" + task_id + "] .inner-popup-overlay").removeClass('visible');
	$("tr.project_task[task_id=" + task_id + "] .inner-confirm").removeClass('visible');
	setTimeout(function() {
		$("tr.project_task[task_id=" + task_id + "] .inner-popup-overlay").hide();
		$("tr.project_task[task_id=" + task_id + "] .inner-confirm").hide();
	}, 300);
}

function initialProjectTaskPopup(contactid) {

    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_project_task.php",
        data: {
            contactid: contactid
        },
        dataType: "json",
        success: function(result) {
            $(".project-task-table").empty();
            $(".project-task-panel .tabs").empty();
            $(".project-task-table-panel").empty();
            var klantenhtml = result['klanten_widget'];
            if(klantenhtml != '')
                $(".popup.very-large .city").append(klantenhtml);
            for (var index = 0; index < result['task_chapter'].length; index++) {
                var chapter = result['task_chapter'][index];

                var html = "<li class='tab col'><a  href='#tab_chapter" + chapter['id'] + "'>" + chapter['name'] + "</a></li>";
                $(".project-task-panel .tabs").append(html);
                if(index != 0)
                html = "<div class='tab-content' id='tab_chapter" + chapter['id'] + "'>" +
                       "<table class='project-task-table full-w-table'>" + 
                       "<tr class='task_chapter' id='chapter_" + chapter['id'] + "'>" +
                        "<td>" + chapter['name'] + "</td>" +
                        "<td></td>" + "<td>Leverancier</td><td>Besteldatum</td><td>Leverdatum</td><td><i class='material-icons'>date_range</i></td><td></td><td width=80px>Timer</td><td>" +
                        "</td></tr>" +
                       "</table>" +
                "</div>";
                else{
                    html = "<div class='tab-content' id='tab_chapter" + chapter['id'] + "'>" +
                        "<table class='project-task-table full-w-table'>" + 
                        "<tr class='task_chapter' id='chapter_" + chapter['id'] + "'>" +
                            "<td>" + chapter['name'] + "</td>" +
                            "<td></td><td>Factuurnummer</td>" + "<td>Bedrag incl. BTW</td><td>Bedrag excl. BTW</td><td>Datum</td><td><i class='material-icons'>date_range</i></td><td></td><td width=80px>Timer</td><td>" +
                            "</td></tr>" +
                        "</table>" +
                    "</div>";
                }
                $(".project-task-table-panel").append(html);
            }


            // This is for special chapter - meer-/minderwerk
            var chapter = 'Meer-/minderwerk';
            var html = "<li class='tab col'><a  href='#tab_chapter_meer'>" + 'Meer-/minderwerk <span>(Jochem & Henry)</span>' + "</a></li>";
            $(".project-task-panel .tabs").append(html);
            html = "<div class='tab-content' id='tab_chapter_meer'>" +
                       "<table class='project-task-table full-w-table'>" + 
                       "<tr class='task_chapter' id='chapter_meer'>" +
                        "<td width='45%'>" + chapter + "</td>" +
                        "<td>Prijs ex.BTW</td>" + "<td width='75px;'>BTW</td><td>Prijs inc.BTW</td><td>Wijze akkoord</td><td>Datum akkoord</td><td style='width:60px;'></td></tr>";
            

            //var html = "<tr st_row=" + result['id'] + ">" + getRawHtmlFromSpeicalTask(result) + "</tr>";
            for(var index = 0; index < result['task_special'].length; index ++)
            {
                html += '<tr st_row='  + result['task_special'][index]['id'] + '>' + getRawHtmlFromSpeicalTask(result['task_special'][index]) + '</tr>'; 
            }

            html += '<tr class="project_s_task_input_field st_newrow">' +
                    '<td><input placeholder="Omschrijving" class="st_text" type="text"/></td>' + 
                    '<td>&euro;<input placeholder="0.00" class="special_number st_price" type="text" onchange="convertNumber(this)" oninput="calcPriceInc(this)"/></td>' + 
                    '<td><select class="st_vat" onchange="calcPriceIncFromSTID(0)"><option value="0.09">9%</option><option value="0.21" selected>21%</option></select></td>' +
                    '<td>&euro;<input placeholder="0.00" class="special_number st_price_inc" type="text" readonly/></td>' + 
                    '<td><input placeholder="Wijze akkoord" class="st_option" type="text"/></td>' + 
                    
                    '<td><input placeholder="dd-mm-jjjj" class="st_datum" type="date"/></td>' + 
                    '<td><div onclick="saveSpecialTask(' + contactid + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Save"><i class="material-icons">save</i></div></td>'
            '</tr>';
            html += "</table></div>";
            $(".project-task-table-panel").append(html);

            //end
            // this is for projectplanning
            var chapter = 'Projectplanning';
            var html = "<li class='tab col'><a  href='#tab_chapter_planning'>" + 'Projectplanning <span>(werkvoorbereiding)</span>' + "</a></li>";
            $(".project-task-panel .tabs").append(html);
            html = "<div class='tab-content' id='tab_chapter_planning'>" +
            "<table class='project-task-table full-w-table'>" + 
            "<tr class='task_chapter' id='chapter_planning'>" +
             "<td width='40%'>Fase</td>" +
              "<td>Duur</td>" + "<td>Volgorde</td>" +
            "<td style='width: 100px'>Medewerkers</td>" +
            "<td style='width: 100px'>Uitbesteed</td>" +
             "<td style='width:30%;'>Kleur</td><td style='width:100px;'></td></tr>";
            var color = '';
            var color_index = 0;
             for(var index = 0; index < result['task_planning'].length; index ++)
             {
                 html += '<tr pt_row='  + result['task_planning'][index]['id'] + '>' + getRawHtmlFromTaskPlanning(result['task_planning'][index]) + '</tr>'; 
                 color = result['task_planning'][index]['color'];
                 color_index = color_list.indexOf(color);
             }//
             
             html += "<tr class='pt_new'>" + getRawHtmlFromTaskPlanningEdition(contactid, null, color_list[(color_index + 1) % (color_list.length)]) + "</tr>";


            html += "</table></div>";
            $(".project-task-table-panel").append(html);
            //end
            $(".project-task-panel .tabs li.tab:first-child a").addClass("active");
            $("#tab_chapter1").addClass("active");
            for (var index = 0; index < result['task_chapter'].length; index++) {
                var chapter = result['task_chapter'][index];
                var html = '';
                if(chapter['id'] == 1)
                {
                    html = "<tr class='tr_new_task' chapter_id = " + chapter['id'] + "><td><input type='text' placeholder='Naam taak' class='new_task_name'/></td>" + 
                    "<td><select class='new_task_isspecial_task' onchange='changedTaskOption(" + contactid + ")'><option value=0>Dit is geen factuur</option><option value=1>Dit is een factuur</option></select></td><td></td>" + 
                    "<td><select class='new_task_leverancier'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" +
                    "<td><select class='new_task_besteldatum'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" + 
                    "<td><select class='new_task_leverdatum'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" + 
                    "<td colspan='2'></td>" + 
                    "<td><input class='new_task_timer' type='text' placeholder='' value='48'/>h</td>" + 
                    "<td><div class='actiebutton tooltipped icon_tasknote ' data-position='top' onclick='saveNewTask(" + contactid + ", " + chapter['id'] + ")'><i class='material-icons'>save</i></div></td>" +
                    "</tr>";
                }
                else{
                    html = "<tr class='tr_new_task' chapter_id = " + chapter['id'] + "><td><input type='text' placeholder='Naam taak' class='new_task_name'/></td>" + 
                    "<td></td>" + 
                    "<td><select class='new_task_leverancier'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" +
                    "<td><select class='new_task_besteldatum'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" + 
                    "<td><select class='new_task_leverdatum'><option value='true'>AAN</option><option value='false'>UIT</option></select></td>" + 
                    "<td colspan='2'></td>" + 
                    "<td><input class='new_task_timer' type='text' placeholder='' value='48'/>h</td>" + 
                    "<td><div class='actiebutton tooltipped icon_tasknote ' data-position='top' onclick='saveNewTask(" + contactid + ", " + chapter['id'] + ")'><i class='material-icons'>save</i></div></td>" +
                    "</tr>";
                }
                $(".project-task-table #chapter_" + chapter['id']).after(html);
            }


            var suppliers = result['task_suppliers'];
            for (var jdex = result['task_list'].length - 1; jdex >= 0; jdex--) {
                var task_line = result['task_list'][jdex];
                var html = "<tr task_id='" + task_line['id'] + "' class='project_task ";
                if (task_line['status'] == 'PROCESSING')
                    html += "line_processing";
                else if (task_line['status'] == 'COMPLETED')
                    html += "line_completed";
                else if (task_line['status'] == 'SKIPPED')
                    html += "line_skipped";
                else
                    html += "line_init";

                html += "'>";

                html += getTaskRowFromRawData(contactid, task_line, suppliers);
                html += "</tr>";
                html += "<tr task_id='" + task_line['id'] + "' class='project_task_note'></tr>";
                $(".project-task-table #chapter_" + task_line['chapter']).after(html);

            }

            $('.tr_new_task select').formSelect();
            $('.line_supplier').formSelect();
            $('.line_completed .button').attr('disabled', true);
            $('.line_completed input').attr('disabled', true);
            $('.line_skipped .button').attr('disabled', true);
            $('.line_skipped input').attr('disabled', true);
            $("#tab_chapter_meer .st_newrow .st_vat").formSelect();
            setTimeout(function(){
                $('.project-task-panel .submenu .tabs').tabs();
            }, 100);
           

        },
        error(e1, e2, e3) {

        }
    });
}

function manageProjectTask(contactid) {
    prefillVeryLargeModal('Project Task', 'manageProjectTask.php').then(function() {
        
        $.ajax({
            type: "POST",
            url: "../php/opdracht/get_contact.php",
            data: {
                contactid: contactid
            },
            dataType: "json",
            success: function(result) {
                updateTimerWiget(contactid);
                for (var i = 0; i < result.length; i++) {
                   
                    $("#projectinfo .contactid").val(contactid);

                    $("#projectinfo #offerte-akkoord").val(result[i].convert_date);                    
                    $("#projectinfo #startdatum").val(result[i].startdatum);
                    $("#projectinfo #opleverdatum").val(result[i].opleverdatum);                    
                    $("#projectinfo #plaatsing").val(result[i].plaatsing);
                    initialProjectTaskPopup(contactid);



                    showPrefilledVeryLargeModal('max');

            
                }
            }
        });

    });
}

function saveProject() {
    var contactid = $("#projectinfo .contactid").val();
    var convert_date = $("#projectinfo #offerte-akkoord").val();    
    var startdatum = $("#projectinfo #startdatum").val();
    var opleverdatum = $("#projectinfo #opleverdatum").val();    
    var plaatsing = $("#projectinfo #plaatsing").val();   
    if(startdatum == '')
        startdatum = null;  
    if(opleverdatum == '')
        opleverdatum = null;   

    var data = {
        contactid: contactid,
        convert_date: convert_date,        
        startdatum: startdatum,
        opleverdatum: opleverdatum,        
        plaatsing:plaatsing
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/save_project.php",
        data: data,
        dataType: "html",
        success: function(result) {
            if (result == 'Opdracht opgeslagen.') {
                //Alles ging goed
                melding(result, 'groen');
                updateTimerWiget(contactid);
                $('.project-task-panel .submenu .tabs').tabs('destroy');
                initialProjectTaskPopup(contactid);

                if(startdatum == null || startdatum == '0000-00-00' || startdatum =='')
                {
                    $(".contacten-table tr[contactrow=" + contactid + "] td:nth-child(5)").html("<span class='text-red'>Nog niet gepland</span>");
                }
                else
                {
                    $(".contacten-table tr[contactrow=" + contactid + "] td:nth-child(5)").html(convertDateFormat(startdatum));
                }
                var item = $("tr[contactrow=" + contactid + "]");


                if(plaatsing == 'ja')
                {
                    item.remove();
                }


            } else {
                melding(result, 'rood');
            }
        },
        error(e1, e2, e3) {

        }
    });
}


function saveTask(contactid, taskid)
{
    var supplier_id = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_supplier").val();
    var besteldatum = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_bdate").val();
    var leverdatum = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_sdate").val();
    var special_jaarplanning = $(".project-task-table tr[task_id=" + taskid + "].project_task .cb-special-jaarplanning").prop('checked');
    if(supplier_id == undefined)
        supplier_id = null;
    if(besteldatum == undefined)
        besteldatum = null;
    if(leverdatum == undefined)
        leverdatum = null;
    if(special_jaarplanning == undefined)
        special_jaarplanning = '';
    else 
        if(special_jaarplanning == true)
            special_jaarplanning = 'YES';
        else 
            special_jaarplanning = 'NO';

    var data = {
        contact_id: contactid,
        projects_tasks_id: taskid,
        supplier_id: supplier_id,
        besteldatum: besteldatum,
        leverdatum: leverdatum,
        special_jaarplanning:special_jaarplanning
    };

    $.ajax({
        type: "POST",
        url: "../php/opdracht/save_task.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Opdracht opgeslagen.') {
                //melding(result['message'], 'groen');
                

            } else {
                melding(result['message'], 'rood');
            }
        },
        error(e1, e2, e3) {

        }
    });
}

function completeTask(contactid, taskid)
{
    var user_id = $(".userid").text();
    var data = {
        contact_id: contactid,
        projects_tasks_id: taskid,
        user_id: user_id
    };
    var sid = $("tr[task_id=" + taskid + "].project_task .line_supplier").val();
    var bdate = $("tr[task_id=" + taskid + "].project_task .line_bdate").val();
    var sdate = $("tr[task_id=" + taskid + "].project_task .line_sdate").val();
    if(sid == '' || bdate == '' || sdate == '')
    {
        melding('Vul een leverancier, bestel- en leverdatum in. Indien niet bekend, maak een schatting.', 'rood');
        return;
    }

    var price = $("tr[task_id=" + taskid + "].project_task .line_price").val();
    var price_inc = $("tr[task_id=" + taskid + "].project_task .line_price_inc").val();
    var betaaldatum = $("tr[task_id=" + taskid + "].project_task .line_betaaldatum").val();
    if(price == '' || price_inc == '' || betaaldatum == '')
    {
        melding('Vul een Bedrag incl.BTW, bestel- en Datum in. Indien niet bekend, maak een schatting.', 'rood');
        return;
    }
    
    $.ajax({
        type: "POST",
        url: "../php/opdracht/complete_task.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Taak afgerond!')
            {
                melding(result['message'], 'groen');
                updateTimerWiget(contactid);
                if(result['completed_task'])
                {

                    var item = $(".project-task-table tr[task_id=" + result['completed_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_processing");
                    var html = getTaskRowFromRawData(contactid, result['completed_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_completed");
                    $(".contacten-table tr[contactrow=" + contactid + "] td:nth-child(6)").text(result['completed_task']['name']);
                }
                if(result['created_task'])
                {
                    var item = $(".project-task-table tr[task_id=" + result['created_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_init");
                    var html = getTaskRowFromRawData(contactid, result['created_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_processing");

                }
                if(result['created_special_task'])
                {
                    var item = $(".project-task-table tr[task_id=" + result['created_special_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_init");
                    var html = getTaskRowFromRawData(contactid, result['created_special_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_processing");
                }
                $(' .line_supplier').formSelect();
                $('.line_completed .button').attr('disabled', true);
                $('.line_completed input').attr('disabled', true);
                $('.line_skipped .button').attr('disabled', true);
                $('.line_skipped input').attr('disabled', true);
            }
            else{
                melding(result['message'], 'rood');
            }
            
            
        },
        error(e1, e2, e3) {

        }
    });
}

function skipTask(contactid, taskid)
{
    var user_id = $(".userid").text();
    var data = {
        contact_id: contactid,
        projects_tasks_id: taskid,
        user_id: user_id
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/skip_task.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Taak overgeslagen!') {
                melding(result['message'], 'groen');
                updateTimerWiget(contactid);
                if(result['skipped_task'])
                {
                    var item = $(".project-task-table tr[task_id=" + result['skipped_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_processing");
                    var html = getTaskRowFromRawData(contactid, result['skipped_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_skipped");
                }
                if(result['created_task'])
                {
                    var item = $(".project-task-table tr[task_id=" + result['created_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_init");
                    var html = getTaskRowFromRawData(contactid, result['created_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_processing");
                }


                $('.line_supplier').formSelect();
                $('.line_completed .button').attr('disabled', true);
                $('.line_completed input').attr('disabled', true);
                $('.line_skipped .button').attr('disabled', true);
                $('.line_skipped input').attr('disabled', true);
            }
            else{
                melding(result['message'], 'rood');
            }
            
            
        },
        error(e1, e2, e3) {

        }
    });
}


function restartTask(contactid, taskid)
{
    var user_id = $(".userid").text();
    var data = {
        contact_id: contactid,
        projects_tasks_id: taskid,
        user_id: user_id
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/restart_task.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if(result['message'] == 'Taak gereset!')
            {
                melding(result['message'], 'groen');
                updateTimerWiget(contactid);
                if(result['restarted_task'])
                {
                    var item = $(".project-task-table tr[task_id=" + result['restarted_task']['id'] + "].project_task");
                    item.empty();
                    item.removeClass("line_completed");
                    item.removeClass("line_skipped");
                    var html = getTaskRowFromRawData(contactid, result['restarted_task'], result['suppliers']);
                    item.append(html);
                    item.addClass("line_processing");
                }

                
                $('.line_supplier').formSelect();
                $('.line_completed .button').attr('disabled', true);
                $('.line_completed input').attr('disabled', true);
                $('.line_skipped .button').attr('disabled', true);
                $('.line_skipped input').attr('disabled', true);
            }
            else{
                melding(result['message'], 'rood');
            }
            
            
        },
        error(e1, e2, e3) {

        }
    });
}

function getTaskNoteRowFromRawData(result, display = true)
{
    var html = "<div class='task_note' id='task_note_" + result['id'] + "' ";
    if(!display)
        html += "style='display: none;'";
    html += ">";
    var file_path = result['file_path'];
    if(file_path != undefined && file_path != null && file_path != '')
    {
        html += "<div class='note_prev'>";
        if(result['file_exe'] == 'pdf')
        {
            html += '<a class="img-pdf" href="../upload/' + result['file_path'] + '" target="_blank"><img  src="images/pdf.png"></a>';
        }
        else
        {
            html += '<a class="img-preview" onclick="openPrev(\'' + result['file_path'] + '\')"><img src="upload/' + result['file_path'] + '"></a>';
        }
        html += "</div>";
    }
    html += "<div><div class='note_header'>"
        + "<span class='note_date'>" + result['created_at'] + "</span>" +
            "<span class='note-user'><i class='material-icons'>person</i> " + result['username'] + "</span>" + 
            "<div onclick='showNoteInnerConfirm(" + result['id'] + ")' class='actiebutton tooltipped' data-position='top' data-tooltip='Verwijderen'><i class='material-icons'>delete</i></div></div>" + 
            "<div class='note_content'>" + result['data'] + "</div></div>" + 
            '<div id="nipo-' + result['id'] + '" class="inner-popup-overlay"></div><div id="nipc-' + result['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeNoteInnerConfirm(' + result['id'] + ')">Annuleren</span><span class="button red" onclick="deleteNoteConfirm(' + result['id'] + ')">Verwijderen</span></div></div>' + 
            "</div>";
    return html;
}
function taskNotes(contact_id, task_id)
{
    var item = $(".project-task-table tr[task_id=" + task_id + "].project_task_note");
    if(item.hasClass('display-notes'))
    {
        
        $(".project-task-table tr[task_id=" + task_id + "].project_task_note .task_note_container").slideUp();
        item.removeClass('display-notes');
    }
    else{
        
        var data = {
            contact_id:contact_id,
            task_id, task_id
        };
        $.ajax({
            type: "POST",
            url: "../php/opdracht/get_task_notes.php",
            data: data,
            dataType: "json",
            success: function(result) {
                item.addClass('display-notes');
                item.empty();
                var html = '';
                html = "<td colspan=10><div class='row task_note_container'><div class='col s5 row'><div  class='col s11'><textarea type='text' class='task-note-text materialize-textarea' placeholder='Typ hier je notitie.'></textarea></div><div class='col s1'><div class='file-field input-field col s1 btn-filelog'><div class='preloader-wrapper small active file-loading-icon'><div class='spinner-layer spinner-blue-only'><div class='circle-clipper left'><div class='circle'></div></div><div class='gap-patch'><div class='circle'></div></div><div class='circle-clipper right'><div class='circle'></div></div></div></div><div class='file-icon'><i class='material-icons'>attach_file</i><input type='file' class='note_file' onchange='fileselected_note(" + task_id + ")'></div><div class='file-path-wrapper'><input class='file-path validate' type='text' hidden></div></div></div><div><span class='button waves-effect waves-light btn' onclick='saveTaskNote(" + contact_id + ", " + task_id + ")'><i class='material-icons'>add</i> Notitie toevoegen</span></div></div><div class='col s6 task_note_panel'>";
                for(var index = 0; index <result.length; index ++)
                {
                    html += getTaskNoteRowFromRawData(result[index]);
                }
                html += "</div></div></td>";
                
                item.append(html);
                $(".project-task-table tr[task_id=" + task_id + "].project_task_note .task_note_container").show('fast');
                
                
            },
            error(e1, e2, e3) {
    
            }
        });   
        
    }
}

function saveTaskNote(contactid, task_id)
{
    var user_id = $(".userid").text();
    var text = $("tr[task_id=" + task_id + "].project_task_note .task-note-text").val();
    var files = $('tr[task_id=' + task_id + '].project_task_note .note_file').prop('files');
    var data = new FormData();
    data.append('contact_id',contactid );
    data.append('project_tasks_id',task_id );
    data.append('user_id',user_id );
    data.append('data',text );
    if(files.length > 0)
        data.append('file',files[0]);
    else 
        data.append('file', '');
    $("tr[task_id=" + task_id + "].project_task_note .file-loading-icon").show();
    $("tr[task_id=" + task_id + "].project_task_note .file-icon").hide();

    $.ajax({
        type: "POST",
        url: "../php/opdracht/save_task_note.php",
        data: data,
        dataType: "json",
        contentType: false,
        processData: false,
        
        success: function(result) {
            if(result['message'] == 'Notitie opgeslagen.')
            {
                var html = getTaskNoteRowFromRawData(result['data'], false);
                $("tr[task_id=" + task_id + "].project_task_note .task_note_panel").prepend(html);
                $("#task_note_" + result['data']['id']).slideDown();
                $("tr[task_id=" + task_id + "].project_task .icon_tasknote").addClass("hasnote");
                melding(result['message'], 'groen');

            }
            else{
                
                melding(result['message'], 'rood');
            }



            $("tr[task_id=" + task_id + "].project_task_note .task-note-text").val("");
            $("tr[task_id=" + task_id + "].project_task_note .file-loading-icon").hide();
            $("tr[task_id=" + task_id + "].project_task_note .file-icon").show();
            $("tr[task_id=" + task_id + "].project_task_note .note_file").val("");
            $("tr[task_id=" + task_id + "].project_task_note .btn-filelog").removeClass('file_selected');

        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function showNoteInnerConfirm(note_id)
{
    $("#nipc-" + note_id).show();
    $("#nipo-" + note_id).show();
    $("#nipc-" + note_id).addClass('visible');
    $("#nipo-" + note_id).addClass('visible');
}

function closeNoteInnerConfirm(note_id)
{
    $("#nipc-" + note_id).removeClass('visible');
    $("#nipo-" + note_id).removeClass('visible');
    setTimeout(function() {
        $("#nipo-" + note_id).hide();
        $("#nipc-" + note_id).hide();
    }, 300);
}

function deleteNoteConfirm(note_id)
{
    closeNoteInnerConfirm(note_id)
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_task_note.php",
        data: {
            'note_id': note_id
        },
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Notitie item verwijderd.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                
                if(result['note_count'] > 0)
                {
                    $("tr[task_id=" + result['task_id'] + "].project_task .icon_tasknote").addClass("hasnote");
                }
                else{
                    $("tr[task_id=" + result['task_id'] + "].project_task .icon_tasknote").removeClass("hasnote");
                }

                $("#task_note_" + note_id).slideUp();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        }
    });
}



function fileselected_note(task_id)
{
    var files = $('.project-task-table tr[task_id=' + task_id + '] .note_file')[0].files;
    if(files.length > 0)
    {
        $(".project-task-table tr[task_id=" + task_id + "] .btn-filelog").addClass('file_selected');
    }
    else{
        $(".project-task-table tr[task_id=" + task_id + "] .btn-filelog").removeClass('file_selected');
    }
}



function saveTaskSpecialValues(contactid, taskid)
{

    
    var price = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_price").val();

    var price_inc = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_price_inc").val();
    if(price != ''){
	price = price.replace(/\./g, '');
	price = price.replace(/\,/g, '.');
    price = addCommas(parseFloat(price).toFixed(2));
    $(".project-task-table tr[task_id=" + taskid + "].project_task .line_price").val(price);
    }

    if(price_inc != ''){
    price_inc = price_inc.replace(/\./g, '');
	price_inc = price_inc.replace(/\,/g, '.');
    price_inc = addCommas(parseFloat(price_inc).toFixed(2));
    $(".project-task-table tr[task_id=" + taskid + "].project_task .line_price_inc").val(price_inc);
    }

    var betaaldatum = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_betaaldatum").val();

    var factuurnummer = $(".project-task-table tr[task_id=" + taskid + "].project_task .line_factuurnummer").val();


    var data = {
        contact_id: contactid,
        projects_tasks_id: taskid,
        price: price,
        price_inc: price_inc,
        betaaldatum: betaaldatum
    };
if(factuurnummer != undefined)
        data['factuurnummer'] = factuurnummer;

    $.ajax({
        type: "POST",
        url: "../php/opdracht/save_task_special_values.php",
        data: data,
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Opdracht opgeslagen.') {
                //melding(result['message'], 'groen');
                

            } else {
                melding(result['message'], 'rood');
            }
        },
        error(e1, e2, e3) {

        }
    });
}


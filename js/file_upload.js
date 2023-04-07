var selected_files = [];
function saveProjectFile()
{
    var contact_id = $(".contactid").val();
    var folder_id = $(".folderid").val();
    var file_type = $("#projectfilelog #type").val();
    var files = $('#projectfilelog #file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file',files[0]);
        fd.append('file_type', file_type);
        fd.append('file_name', $("#name").val());
        fd.append('contact_id', contact_id);
        fd.append('folder_id', folder_id);
        fd.append('user_id', $("#projectfilelog .userid").val());
        fd.append('klantportaal', $("#klantportaal").prop('checked'));
        $("#projectfilelog .file-loading-icon").show();
        $("#projectfilelog .file-icon").hide();
    $.ajax({
        url: "../php/opdracht/file_upload.php",
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result){
            if (result['message'] == 'Bestand opgeslagen') {
                //Alles ging goed
                
                var fileloghtml = getFileLogHtml(result['inserted_file'], true);
                $(".file-loading-icon").hide();
                $(".file-icon").show();
                $(".popup.large .logs .file-log-panel").prepend(fileloghtml);
				$('.tooltipped').tooltip();
				$('#flog-' + result['inserted_file']['id']).slideDown();
                melding(result['message'], 'groen');
                $("#projectfilelog #file").val("");
                $("#projectfilelog #type").val("");
                $("#name").val("");
                $("#type").formSelect();
                $("#klantportaal").prop('checked', false);
                $("#projectfilelog .btn-filelog").removeClass('file_selected');
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
     });
    }else{
        melding('Selecteer eerst een bestand', 'rood');
    }
}

function showInnerFConfirm(clID) {
    $("#fipc-" + clID).show();
    $("#fipo-" + clID).show();
    $("#fipc-" + clID).addClass('visible');
    $("#fipo-" + clID).addClass('visible');
}

function closeInnerFConfirm(clID) {
    $("#fipc-" + clID).removeClass('visible');
    $("#fipo-" + clID).removeClass('visible');
    setTimeout(function() {
        $("#fipo-" + clID).hide();
        $("#fipc-" + clID).hide();
    }, 300);
}

function filetypeChanged()
{
    // if($("#type").val() == 5)
    // {
    //     $(".row-name").show();
    // }
    // else{
    //     $(".row-name").hide();
    // }
}
function deleteCFConfirm(fID) {
    //closeConfirm();
    closeInnerFConfirm(fID)
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_file_log.php",
        data: {
            'file_id': fID
        },
        dataType: "html",
        success: function(result) {
            if (result == 'Bestand verwijderd') {
                //Alles ging goed
                melding(result, 'groen');
                $("#flog-" + fID).slideUp();
            } else {
                //Er ging iets mis
                melding(result, 'rood');
            }
        }
    });
}

function manageProjectFileLog(contact_id)
{
    
    prefillModal('Contact wijzigen', 'manageProjectFile.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/opdracht/get_project_file.php",
            data: {
                contact_id: contact_id
            },
            dataType: "json",
            success: function(result) {
                var projectfilehtml = "";
                var pathlinkhtml = "";
                selected_files = [];
                if(result)
                {
                    $("#projectfilelog .folderid").val(result['folder_id']);
                    let file_list = result['file_list'];
                    let folder_list = result['folder_list'];
                    let path_list = result['path_list'];
                    projectfilehtml += "<div class='folder-log-panel'>";
                    for(var index = 0; index < folder_list.length; index ++)
                   {
                        projectfilehtml += getFolderLogHtml(folder_list[index]);
                   }
                   projectfilehtml += "</div>";
                   projectfilehtml += "<div class='file-log-panel'>";
                   for(index = 0; index < file_list.length; index ++)
                   {
                        projectfilehtml += getFileLogHtml(file_list[index]);
                   }
                   projectfilehtml += "</div>";
                   for(index = 0; index < path_list.length ; index ++)
                   {
                    pathlinkhtml += '<a href="javascript:void(0)" onclick="gotoFileFolder(\'' + path_list[index]['id'] + '\')">' + path_list[index]['name'] + "</a>";
                   }

                }
                pathlinkhtml = "<div class='folder-path-panel'><div class='folder-path-list'>" + pathlinkhtml + "</div><div class='folder-path-action'><div onclick='cutSelectedFiles()' class='actiebutton tooltipped cutfolderbutton' data-position='top' data-tooltip='Knippen'><i class='material-icons'>content_cut</i></div><div onclick='pasteSelectedFiles()' class='actiebutton tooltipped pastefolderbutton' data-position='top' data-tooltip='Plakken'><i class='material-icons'>content_paste</i></div><div onclick='deleteSelectedFiles()' class='actiebutton tooltipped deletefolderbutton' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>delete</i></div></div></div>";
                
                $(".popup.large .logs").html(projectfilehtml);
                $('.tooltipped').tooltip();
                
                $(".contactid").val(contact_id);
                updateTimerWiget(contact_id);
                showPrefilledModal('wide', true);
                $(".popup.large .middle-section").html(pathlinkhtml);
                setTimeout(function(){
                    $("#projectfilelog .tabs").tabs();
                    $('.tooltipped').tooltip();
                }, 1000);
                

            },
            error: function(e1, e2, e3)
            {

            }
        });
    });
}




function saveProjectFolder()
{
    var folder_id = $("#projectfilelog .folderid").val();
    var title = $("#projectfilelog #folder-name").val();
    var contact_id = $(".contactid").val();
    var user_id = $("#projectfilelog .userid").val();
    var data = {
        folder_id : folder_id,
        title : title,
        contact_id : contact_id,
        user_id : user_id
    }
    if(title == "")
    {
        melding('Vul een Naam in', 'Rood');
        return;
    }
    $.ajax({
        url: "../php/opdracht/save_project_folder.php",
        type: 'post',
        data: data,
        dataType: "json",
        success: function(result){
            if (result['message'] == 'Bestand opgeslagen') {
                melding(result['message'], 'groen');
                $("#projectfilelog #folder-name").val("");
                var folderloghtml = getFolderLogHtml(result['item'], true);
                $(".popup.large .logs .folder-log-panel").prepend(folderloghtml);
                $('.tooltipped').tooltip();
				$('#folderlog-' + result['item']['id']).slideDown();
            }
            else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }

        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function gotoFileFolder(folder_id)
{
    var contact_id = $(".contactid").val();
    var data = {
        folder_id : folder_id,
        contact_id : contact_id
    }
    showLoading();

    $.ajax({
        type: "POST",
        url: "../php/opdracht/get_project_file.php",
        data: data,
        dataType: "json",
        success: function(result) {
            var projectfilehtml = "";
            var pathlinkhtml = "";
            hideLoading();
            if(result)
            {
                $("#projectfilelog .folderid").val(result['folder_id']);
                let file_list = result['file_list'];
                let folder_list = result['folder_list'];
                let path_list = result['path_list'];
                projectfilehtml += "<div class='folder-log-panel'>";
                for(var index = 0; index < folder_list.length; index ++)
               {
                    projectfilehtml += getFolderLogHtml(folder_list[index]);
               }
               projectfilehtml += "</div>";
               projectfilehtml += "<div class='file-log-panel'>";
               for(index = 0; index < file_list.length; index ++)
               {
                    projectfilehtml += getFileLogHtml(file_list[index]);
               }
               projectfilehtml += "</div>";
               for(index = 0; index < path_list.length ; index ++)
                {
                    pathlinkhtml += '<a href="javascript:void(0)" onclick="gotoFileFolder(\'' + path_list[index]['id'] + '\')">' + path_list[index]['name'] + "</a>";
                }
                
            }
            $(".popup.large .logs").html(projectfilehtml);

            pathlinkhtml = "<div class='folder-path-panel'><div class='folder-path-list'>" + pathlinkhtml + "</div><div class='folder-path-action'><div onclick='cutSelectedFiles()' class='actiebutton tooltipped cutfolderbutton' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>content_cut</i></div><div onclick='pasteSelectedFiles()' class='actiebutton tooltipped pastefolderbutton' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>content_paste</i></div><div onclick='deleteSelectedFiles()' class='actiebutton tooltipped deletefolderbutton' data-position='top' data-tooltip='Wijzigen'><i class='material-icons'>delete</i></div></div></div>";
            
            $(".popup.large .middle-section").html(pathlinkhtml);
            if(selected_files.length == 0)
            {
                $(".popup .pastefolderbutton").hide();
                $(".popup .cutfolderbutton").css('display', 'inline-block');
            }
            else{
                $(".popup .pastefolderbutton").css('display', 'inline-block');
                $(".popup .cutfolderbutton").hide();
            }
            $('.tooltipped').tooltip();

        },
        error: function(e1, e2, e3)
        {

        }
    });

}


function getFileLogHtml(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="flog-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper">'
    if(item['id'] != '')
    {
        html += '<div class="flog-selector"><label><input type="checkbox" class="filled-in cb-flog-selector cb-flog-selector-file" data-id="' + item['id'] + '"><span></span></label></div>';
    }
    
    html += '<div class="flog-prev">';
    if(item['file_exe'] == 'pdf') 
    {
        html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
    }
    else{
        html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
    }
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name">' + item['name'] + '</span>';
    if(item['id'] != '')
    {
       
        html += '<div onclick="showInnerFConfirm(' + item['id'] + ')" class="actiebutton tooltipped" data-position="top" data-tooltip="Verwijderen"><i class="material-icons">delete</i></div>' ;
    }
    

    html += '<a href="' + root_path + 'upload/' + item['file_path'] + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>';
    if(item['id'] != '')
    {
        html += '<div onclick="editFileTitle(' + item['id'] + ')" class="actiebutton tooltipped editTitleButton" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>' ;
    }
    html += '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['uploaded_date']) + '</span>';

    if(item['username'] != null)
    html += '<span class="fc-user"><i class="material-icons">person</i>' + item['username'] + '</span>';

    html += '<span class="fc-check">';
	if(item['klantportaal'])
	{
		html += '<span class="fc-check"><i class="material-icons">check</i>Klantportaal</span>';
	}
    html += "</span>";
    
    html +='</div>' + 
    '<div id="fipo-' + item['id'] + '" class="inner-popup-overlay"></div>' + 
    '<div id="fipc-' + item['id'] + '" class="popup inner-confirm"><div class="buttons"><span class="button white" onclick="closeInnerFConfirm(' + item['id'] + ')">Annuleren</span><span class="button red" onclick="deleteCFConfirm(' + item['id'] + ')">Verwijderen</span></div></div>' + 
    '</div></div>';
    return html;
}

function getFolderLogHtml(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="folderlog-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper"><div class="flog-selector"><label><input type="checkbox" class="filled-in cb-flog-selector cb-flog-selector-folder" data-id="' + item['id'] + '"><span></span></label></div><div class="flog-prev">';
    html += '<a class="folder-preview" onclick="gotoFileFolder(\'' + item['id'] + '\')"><img  src="' + root_path + 'images/folder.png"></a>';
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name"><a href="javascript:void(0)" class="image-folder-link" onclick="gotoFileFolder(\'' + item['id'] + '\')">' + item['name'] + '</a></span>';
    
    html += '<div onclick="editFolderTitle(' + item['id'] + ')" class="actiebutton tooltipped editTitleButton" data-position="top" data-tooltip="Wijzigen"><i class="material-icons">edit</i></div>' ;
    
    html += '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['created_at']) + '</span>' + 
    '<span class="fc-user"><i class="material-icons">person</i>' + item['username'] + '</span>';
    
    html +='</div>' + 
    '</div></div>';
    return html;
}

function editFileTitle(id)
{
    var item = ".popup .logs #flog-" + id + " .fc-name";
    var file_type = [
        'Constructieberekening',
        'Architect tekening',
        'Kozijn tekening',
        'Foto van situatie'
    ];
    var str_type = '';
    var str_content = $(item).text();
    for(var index = 0; index < file_type.length; index ++)
    {
        if(str_content.startsWith(file_type[index] + " - "))
        {
            str_type = file_type[index] + " - ";
            str_content = str_content.replace(str_type, '');
            break;
        }
    }
    str_content = str_type + "<input class='fc-name-edit' value='" + str_content + "'/>";
    $(item).html(str_content);
    $(".popup .logs #flog-" + id + " .editTitleButton i.material-icons").html("save");
    $(".popup .logs #flog-" + id + " .editTitleButton").attr('onclick', "saveFileTitle(" + id + ")");
}

function saveFileTitle(id)
{
    var item = ".popup .logs #flog-" + id + " .fc-name";
    var data = {
        id : id,
        title : $(item).text() + $(".popup .logs #flog-" + id + " .fc-name .fc-name-edit").val()
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/set_project_file.php",
        dataType: "json",
        data: data,
        success: function(result) {
            if(result['message'] == 'Bestanden opgeslagen')
            {
                melding(result['message'],'groen');
                var text = $(item).text() + $(".popup .logs #flog-" + id + " .fc-name .fc-name-edit").val();
                $(".popup .logs #flog-" + id + " .fc-name").html(text);
                $(".popup .logs #flog-" + id + " .editTitleButton i.material-icons").html("edit");
                $(".popup .logs #flog-" + id + " .editTitleButton").attr('onclick', "editFileTitle(" + id + ")");
            }
            else
                melding(result['message'], 'Rood');
        },
        error: function(e1, e2, e3)
        {

        }
    });
}

function editFolderTitle(id)
{
    var item = ".popup .logs #folderlog-" + id + " .fc-name";

    var str_content = $(item).text();

    str_content = "<input class='fc-name-edit' value='" + str_content + "'/>";
    $(item).append(str_content);
    $(item + " a").hide();
    $(".popup .logs #folderlog-" + id + " .editTitleButton i.material-icons").html("save");
    $(".popup .logs #folderlog-" + id + " .editTitleButton").attr('onclick', "saveFolderTitle(" + id + ")");
}

function saveFolderTitle(id)
{
    var item = ".popup .logs #flog-" + id + " .fc-name";
    var data = {
        id : id,
        title : $(".popup .logs #folderlog-" + id + " .fc-name .fc-name-edit").val()
    };
    $.ajax({
        type: "POST",
        url: "../php/opdracht/set_project_folder.php",
        dataType: "json",
        data: data,
        success: function(result) {
            if(result['message'] == 'Bestanden opgeslagen')
            {
                melding(result['message'],'groen');
                var text = $(".popup .logs #folderlog-" + id + " .fc-name .fc-name-edit").val();
                $(".popup .logs #folderlog-" + id + " .fc-name a").html(text);
                $(".popup .logs #folderlog-" + id + " .fc-name .fc-name-edit").remove();
                $(".popup .logs #folderlog-" + id + " .fc-name a").show();
                $(".popup .logs #folderlog-" + id + " .editTitleButton i.material-icons").html("edit");
                $(".popup .logs #folderlog-" + id + " .editTitleButton").attr('onclick', "editFolderTitle(" + id + ")");
            }
            else
                melding(result['message'], 'Rood');
        },
        error: function(e1, e2, e3)
        {

        }
    });
}


function cutSelectedFiles()
{
    selected_files = [];
    $(".popup .cb-flog-selector").each((index, item) => {
        if($(item).prop('checked'))
        {
            selected_files.push({
                type : $(item).hasClass('cb-flog-selector-folder') ? 'folder' : 'file',
                id : $(item).attr('data-id')
            });
        }
    });
    if(selected_files .length == 0)
    {
        melding('Please select at least one item', 'rood');
        return;
    }
    $(".popup .pastefolderbutton").css('display', 'inline-block');
    $(".popup .cutfolderbutton").hide();
}

function deleteSelectedFiles()
{

    if($(".popup .cb-flog-selector:checked").length == 0)
    {
        melding('Please select at least one item', 'rood');
        return;
    }
    var folder_count = $(".popup .cb-flog-selector.cb-flog-selector-folder:checked").length;
    var file_count = $(".popup .cb-flog-selector:checked").length - $(".popup .cb-flog-selector.cb-flog-selector-folder:checked").length;
    var alert_str = "Weet je zeker dat je de geselecteerde bestanden en mappen wilt verwijderen?";
    if(folder_count == 0)
        alert_str = "Weet je zeker dat je dit bestand wil verwijderen?";
    if(file_count == 0)
        alert_str = "Weet je zeker dat je deze map en alle bestanden daarin wilt verwijderen?";
    showConfirm(alert_str, 'Verwijderen', 'red', "deleteSelectedFilesConfirm()");
}

function deleteSelectedFilesConfirm()
{
    closeConfirm();
    var choosed_files = [];
    $(".popup .cb-flog-selector").each((index, item) => {
        if($(item).prop('checked'))
        {
            choosed_files.push({
                type : $(item).hasClass('cb-flog-selector-folder') ? 'folder' : 'file',
                id : $(item).attr('data-id')
            });
        }
    });
    showLoading();
    $.ajax({
        type: "POST",
        url: "../php/opdracht/delete_file_lists.php",
        data: {
            'choosed_files': choosed_files
        },
        dataType: "json",
        success: function(result) {
            if (result['message'] == 'Bestand verwijderd') {
                //Alles ging goed
                melding(result['message'], 'groen');
                var folder_id = $(".folderid").val();
                gotoFileFolder(folder_id);
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        }
        ,
        error: function(e1, e2, e3)
        {
            hideLoading();
        }
    });

}

function pasteSelectedFiles()
{
    var folder_id = $("#projectfilelog .folderid").val();
    var data = {
        folder_id : folder_id,
        selected_items : selected_files
    };

    $.ajax({
        type: "POST",
        url: "../php/opdracht/paste_files.php",
        data: data,
        dataType: "json",
        success: function(result) {
            selected_files = [];
            if(result['status'] == 'success')
                gotoFileFolder(folder_id);
            else{
                $(".popup .pastefolderbutton").hide();
                $(".popup .cutfolderbutton").css('display', 'inline-block');
            }
        },
        error: function(e1, e2, e3) {

        }
    });
}
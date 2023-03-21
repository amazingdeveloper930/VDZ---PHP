function fileselected()
{
    var files = $('#file')[0].files;
    if(files.length > 0)
    {
        $(".btn-filelog").addClass('file_selected');
    }
    else{
        $(".btn-filelog").removeClass('file_selected');
    }
}

function addFile()
{
    prefillModal('Bestand uploaden', 'addFile.php').then(function() {
        $("#projectfilelog #file").attr("accept", "");
        showPrefilledModal();
    });
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

function getFileLogHtmlKlanten(item, isNew = false)
{
    var html = '';
    var root_path = $("#root_path").val();
    html += '<div id="flogitem-' + item['id'] + '" class="log-container" ' + (isNew?' style="display: none;" ':'') + '><div class="log-wrapper"><div class="flog-prev">';
    if(item['file_exe'] == 'pdf') 
    {
        html += '<a class="img-pdf" href="' + root_path + 'upload/' + item['file_path'] + '" target="_blank"><img  src="' + root_path + 'images/pdf.png"></a>';
    }
    else{
        html += '<a class="img-preview" onclick="openPrev(\'' + item['file_path'] + '\')"><img src="' + root_path + 'upload/' + item['file_path'] + '"></a>';
    }
    
    html += '</div>' + 
    '<div class="flog-header">' +
    '<span class="fc-name">' + item['name'] + '</span>' +
    
    '<a href="' + root_path + 'upload/' + item['file_path'] + '" download class="actiebutton tooltipped" data-position="top" data-tooltip="Download"><i class="material-icons">file_download</i></a>' + 
    '</div>' +
    '<div class="flog-container">' + 
    '<span class="fc-date">' + getFormatedDateTime(item['uploaded_date']) + '</span>' ;

	
    html +='</div>' + 
    '</div></div>';
    return html;
}



function saveProjectFile()
{
    var contact_id = $("#contact_id").val();
    var file_type = $("#type").val();
    var files = $('#file')[0].files;
    var fd = new FormData();
    if(files.length > 0 ){
        fd.append('file',files[0]);
        fd.append('file_type', file_type);
        fd.append('file_name', $("#name").val());
        fd.append('contact_id', contact_id);
        fd.append('user_id', null);
        fd.append('klantportaal', true);
        $(".file-loading-icon").show();
        $(".file-icon").hide();
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
                
                var fileloghtml = getFileLogHtmlKlanten(result['inserted_file'], true);
                melding(result['message'], 'groen');
                $(".klanten-flog-panel").prepend(fileloghtml);
                $("#flogitem-" + result['inserted_file']['id']).slideDown();
                closeModal();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
     });
    }else{
        melding('Selecteer eerst een bestand', 'rood');
    }
}
function getFormatedDateTime(date) {
	let fdate = new Date(date);
	return("0" + fdate.getDate()).slice(-2) + '-' + ("0" + (fdate.getMonth() + 1)).slice(-2) + '-' + fdate.getFullYear() + ' ' + ("0" + fdate.getHours()).slice(-2) + ':' + ("0" + fdate.getMinutes()).slice(-2);
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
$(window).load(function(){
    // PAGE IS FULLY LOADED  
    // FADE OUT YOUR OVERLAYING DIV
    setTimeout(function(){ 

        $('.preloader-container').hide();
        
         }, 500);
    
 });
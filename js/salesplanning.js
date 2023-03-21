var day_list = ['', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', ''];
var month_list = ['', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
var short_month_list = ['', 'Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'];
var displayed_year = new Date().getFullYear();
var displayed_month = new Date().getMonth() + 1;

function gotoBeforeMonth() {
    displayed_month--;
    if (displayed_month == 0) {
        displayed_year--;
        displayed_month = 12;
    }
    showCalender(displayed_year, displayed_month);
}

function gotoNextMonth() {
    displayed_month++;
    if (displayed_month == 13) {
        displayed_year++;
        displayed_month = 1;
    }
    showCalender(displayed_year, displayed_month);
}

function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

function makeTwoDigital(number) {
    if (number >= 10)
        return number;
    return "0" + number;
}

function addNewSalesMeeting(year, month, date, sales_agent) {
    prefillModal('Afspraak toevoegen', 'addSalesMeeting.php').then(function() {



        // $("#date").datepicker({ format: 'mmm dd, yyyy' });
        // $("#date").setDate(new Date(year + "-" + month + "-" + date));
        $(".agentid").val(sales_agent);
        $(".salesmeetingid").val("");
        $("#editExistingPanel").hide();
        $("#addNewPanel").show();
        showPrefilledModal();

        $(".meetingdate").val(year + "-" + makeTwoDigital(month) + "-" + makeTwoDigital(date));
        // $(".datepicker").datepicker({
        //     defaultDate: new Date(year, month, date),
        //     // setDefaultDate: new Date(2000,01,31),
        //     // maxDate: new Date(currYear - 5, 12, 31),
        //     // yearRange: [1928, currYear - 5],
        //     format: "yyyy/mm/dd"
        // });

    });
}

function confirmDeleteSalesMeeting() {
    showConfirm('Delete Sales Meeting?', 'Verwijderen', 'red', 'deleteSalesMeeting()');
}

function editSalesMeeting(meeting_id, e) {
    e.stopPropagation();
    prefillModal('Afspraak wijzigen', 'addSalesMeeting.php').then(function() {
        $.ajax({
            type: "POST",
            url: "../php/leads/get_sales_meeting.php",
            data: { meeting_id: meeting_id },
            dataType: "json",
            success: function(result) {
                $(".agentid").val(result[0]['sales_agent']);
                $(".salesmeetingid").val(result[0]['id']);
                $("#leadid").val(result[0]['contact']);
                $("#fromTime").val(result[0]['time_from']);
                $("#toTime").val(result[0]['time_untill']);
                $("#addNewPanel").hide();
                $("#editExistingPanel").show();

                $(".meetingdate").val(result[0]['date'].split(' ')[0]);
                showPrefilledModal();
                // $(".meetingdate").val(year + "-" + month + "-" + date);
                // // $(".meetingdate").datepicker();
                // $(".meetingdate").val("05/06/2021");
            }
        });


    });
}

function saveSalesMeeting() {
    var meeting_data = {
        leadid: $('#leadid').val(),
        agentid: $('.agentid').val(),
        fromTime: $('#fromTime').val(),
        toTime: $('#toTime').val(),
        meetingdate: $('.meetingdate').val(),
        salesmeetingid: $('.salesmeetingid').val(),
        userid: $(".userid").text()
    };
    $.ajax({
        type: "POST",
        url: "../php/leads/save_sales_meeting.php",
        data: meeting_data,
        dataType: "json",
        success: function(result) {

            if (result['message'] == 'Afspraak opgeslagen.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                var sm = result['salesmeeting'][0];
                addSalesMeetingComponent(sm['id'], sm['month'], sm['day'], sm['time_from'], sm['time_untill'], sm['sales_agent'], sm['color']);
                closeModal();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3) {
            // debugger;
        }
    });
}

function deleteSalesMeeting() {
    closeConfirm();
    // salesmeetingid: $('.salesmeetingid').val()
    var salesmeetingid = $('.salesmeetingid').val();
    var meeting_data = {
        salesmeetingid: salesmeetingid
    };

    $.ajax({
        type: "POST",
        url: "../php/leads/delete_sales_meeting.php",
        data: meeting_data,
        dataType: "json",
        success: function(result) {

            if (result['message'] == 'Afspraak verwijderd.') {
                //Alles ging goed
                melding(result['message'], 'groen');
                $("#mw-" + salesmeetingid).remove();
                closeModal();
            } else {
                //Er ging iets mis
                melding(result['message'], 'rood');
            }
        },
        error: function(e1, e2, e3) {
            // debugger;
        }
    });

}



$.fn.meetingcomponent = function() {
    this.each(function() {
        var backcolor = $(this).attr('color');
        var until = $(this).attr('until');
        var from = $(this).attr('from');

        $(this).css('background-color', backcolor);
        $(this).css('width', (until - from) * 100 / 13 + "%");
        $(this).css('left', (from - 7) * 100 / 13 + "%");
    });
};



function addSalesMeetingComponent(id, month, date, from, until, agent_id, color) {
    var html = "<div class='meeting-wizard' id='mw-" + id + "' agent = '" + agent_id + "' from='" + from + "' until='" + until + "' color='" + color + "' onclick='editSalesMeeting(" + id + ", event)'></div>";
    $("#mw-" + id).remove();
    $("#mc-" + month + "-" + date + "-" + agent_id).append(html);
    $("#mw-" + id).meetingcomponent();
}

function showCalender(year, month) {

    $.ajax({
        type: "POST",
        url: "../php/leads/get_sales_agent.php",
        dataType: "json",
        success: function(result) {
            if (result) {


                $(".calender-header-txt").text(short_month_list[month] + ", " + year);
                var d = new Date(month + '/01/' + year);

                var day_count = daysInMonth(month, year);
                var row_count = 0;
                switch (d.getDay()) {
                    case 0: //sunday
                        row_count = Math.floor(day_count / 7) + ((day_count) % 7 >= 2 ? 1 : 0);
                        break;
                    case 1: //monday
                        row_count = Math.floor((day_count + 1) / 7) + ((day_count + 1) % 7 >= 2 ? 1 : 0);
                        break;
                    case 2:
                        row_count = Math.floor((day_count + 2) / 7) + ((day_count + 2) % 7 >= 2 ? 1 : 0);
                        break;
                    case 3:
                        row_count = Math.floor((day_count + 3) / 7) + ((day_count + 3) % 7 >= 2 ? 1 : 0);
                        break;
                    case 4:
                        row_count = Math.floor((day_count + 4) / 7) + ((day_count + 4) % 7 >= 2 ? 1 : 0);
                        break;
                    case 5:
                        row_count = Math.floor((day_count + 5) / 7) + ((day_count + 5) % 7 >= 2 ? 1 : 0);
                        break;
                    case 6:
                        row_count = Math.floor((day_count - 1) / 7) + ((day_count - 1) % 7 >= 2 ? 1 : 0);
                        break;
                }
                // (day_count + d.getDay()) / 7
                $(".calender-table tbody").empty();
                var temp = d.getDay();
                if (temp == 6)
                    temp = -1;
                for (var index = 0; index < row_count; index++) {
                    var header_html = "";
                    var meeting_html = new Array(result.length).fill('');

                    header_html += "<tr class='calender-date'>";

                    for (var jdex = 1; jdex < 6; jdex++) {
                        //d.getDay();
                        var date = 1 - temp + jdex + 7 * index;
                        if (date > 0 && date <= day_count) {
                            header_html += "<td><span class='calender-month'>" + day_list[jdex] + "</span> " + date + " " + month_list[month] + "</td>";

                        } else {
                            header_html += "<td>" + "</td>";

                        }
                        for (k = 0; k < result.length; k++) {
                            if (date > 0 && date <= day_count)
                                meeting_html[k] += "<td onclick='addNewSalesMeeting(" + year + "," + month + "," + date + "," + result[k]['id'] + ")' class='meeting-container' id='mc-" + month + "-" + date + "-" + result[k]['id'] + "'></td>";
                            else
                                meeting_html[k] += "<td></td>";
                        }

                    }
                    header_html += "</tr>";
                    $(".calender-table tbody").append(header_html);
                    for (k = 0; k < result.length; k++) {
                        meeting_html[k] = "<tr>" + meeting_html[k] + "</tr>";
                        $(".calender-table tbody").append(meeting_html[k]);
                    }

                }

                //////////////////////////////
                // display left side bar
                $(".sales-agent-panel").empty();
                var unit_agents_list_html = "";
                for (index = 0; index < result.length; index++) {
                    unit_agents_list_html += "<li><i class='material-icons ' style='color: " + result[index]['color'] + "'>circle</i>" + result[index]['name'] + "</li>"
                }
                unit_agents_list_html = "<ul class='sales-agents'>" + unit_agents_list_html + "</ul>"
                for (index = 0; index < row_count; index++) {
                    $(".sales-agent-panel").append(unit_agents_list_html);
                }
                //////////////////////////////////
                // display sales meeting
                // addSalesMeetingComponent(id, month, date, from, until, agent_id, color) 

                $.ajax({
                    type: "POST",
                    url: "../php/leads/get_sales_meeting_list.php",
                    dataType: "json",
                    success: function(result) {
                        if (result) {
                            for (var index = 0; index < result.length; index++) {
                                addSalesMeetingComponent(result[index]['id'], result[index]['month'], result[index]['day'], result[index]['time_from'], result[index]['time_untill'], result[index]['sales_agent'], result[index]['color']);
                            }
                        }
                    },
                    error: function(e1, e2, e3) {

                    }
                });



            }
        }
    });


}
showCalender(displayed_year, displayed_month);
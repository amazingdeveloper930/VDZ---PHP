<?php

require('../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

$plan_id = NULL;
$block_id = NULL;
$hour = 8;
$day = date("w", strtotime($_POST['datum']));
if($_POST['daypart'] == 'Ochtend' || $_POST['daypart'] == 'Middag')
    $hour = 4;
if($_POST['daypart'] == 'Meerdere dagen')
    {
        $date1=date_create($_POST['datum_end']);
        $date2=date_create($_POST['datum']);
        $diff=date_diff($date2, $date1);
        $day_count = $diff->format("%R%a");
        $day_count = (int)($day_count);
        $day_count ++;
        $hour = 8 * $day_count;
    }
if(!empty($_POST['plan_id']))
{
    $stmt = $con -> prepare('SELECT * FROM werkplanning_medewerker WHERE id = ?');
    $stmt -> bind_param('i', $_POST['plan_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result -> fetch_assoc())
    {
        if($row['contact_id'] != $_POST['contact_id'])
        {
            
            $stmt = $con -> prepare('DELETE FROM werkplanning_medewerker WHERE id = ?');
            $stmt -> bind_param('i', $_POST['plan_id']);
            $stmt -> execute();
            $_POST['plan_id'] = '';
        }
    }
    
}
if($_POST['plan_id'] == '') { //New plan
    $stmt = $con -> prepare('INSERT INTO werkplanning_block (werkplanning_id, contact_id, year, week, day, datum, datum_end) VALUES (?, ?, ?, ?, ?, ?, ?)');

    $stmt-> bind_param('iiiiiss', $_POST['werkplanning_id'], $_POST['contact_id'], $_POST['year'], $_POST['week'], $day, $_POST['datum'],  $_POST['datum_end']);
    $stmt -> execute();
    $block_id = $stmt -> insert_id;

    $stmt = $con -> prepare('INSERT INTO werkplanning_medewerker (employee_id, contact_id, 	project_planning_id, block_id, werkplanning_id, hour, daypart) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt -> bind_param('sssssss', $_POST['employee_id'], $_POST['contact_id'], $_POST['planning'], $block_id, $_POST['werkplanning_id'], $hour, $_POST['daypart']);
    $stmt -> execute();
    $plan_id = $stmt -> insert_id;
    if(isset($_POST['ticket_id']))
        {
            $stmt = $con -> prepare('UPDATE werkplanning_medewerker SET ticket_id = ? WHERE id = ?');
            $stmt -> bind_param('ii', $_POST['ticket_id'], $plan_id);
            $stmt -> execute();
        }
      
    
}
else{
    $stmt = $con -> prepare('UPDATE werkplanning_medewerker SET employee_id = ?, contact_id = ?, daypart = ?, 	project_planning_id = ?, ticket_id = ?, hour = ? WHERE id = ?');
    $stmt -> bind_param('sssssii', $_POST['employee_id'], $_POST['contact_id'], $_POST['daypart'], $_POST['planning'], $_POST['ticket_id'], $hour, $_POST['plan_id']);
    $stmt -> execute();
    $plan_id = $_POST['plan_id'];
    $stmt = $con -> prepare('SELECT * FROM werkplanning_medewerker WHERE id = ?');
    $stmt -> bind_param('i', $plan_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $block_id = NULL;
    while($row = $result->fetch_assoc())
    {
        $stmt_1 = $con -> prepare('DELETE FROM werkplanning_activity WHERE block_id = ?');
        $stmt_1 -> bind_param('i', $row['block_id']);
        $stmt_1 -> execute();
        $block_id = $row['block_id'];

        $stmt_1 = $con -> prepare('UPDATE werkplanning_block SET datum = ?, datum_end = ?, day = ? WHERE id = ?');
        $stmt_1 -> bind_param('ssii', $_POST['datum'], $_POST['datum_end'], $day, $block_id);
        $stmt_1 -> execute();

        
    }


    if(($_POST['daypart'] == 'Meerdere dagen') && isset($block_id))
        {
            $stmt_1 = $con -> prepare('UPDATE werkplanning_medewerker SET hour = ?, daypart = ? WHERE block_id = ?');
            $stmt_1 -> bind_param('isi', $hour, $_POST['daypart'], $block_id);
            $stmt_1 -> execute();
        }

        //here should be daypart is normal and original daypart is meerdere

}
if($block_id && !empty($_POST['text']))
{
    
    $stmt = $con -> prepare('SELECT * FROM werkplanning_block WHERE id = ?');
    $stmt -> bind_param('i', $block_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result->fetch_assoc())
    {
        $text_arr = explode(",", $_POST['text']);
        foreach($text_arr as $text)
        {
            $stmt_1 = $con -> prepare('INSERT INTO werkplanning_activity (werkplanning_id, contact_id, block_id, text) VALUES (?, ?, ?, ?)');
            $stmt_1 -> bind_param('iiis', $row['werkplanning_id'], $row['contact_id'], $row['id'], $text);
            $stmt_1 -> execute();
        }
    }
    if($_POST['plan_id'] == '')
    {
        $title = 'Medewerker ingepland';
        $text = $_POST['text'];
        addNewLog($_POST['contact_id'], $_SESSION['id'], $title, $text);
    }
}


echo json_encode(['message' => 'Weekplan opgeslagen.', 'plan_id' => $plan_id]);
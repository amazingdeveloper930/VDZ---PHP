<?php 
require( '../../common/connection.php');
require '../../common/global.php';

$stmt = null;
if(isset($_POST['contact_id']))
{
    $stmt = $con -> prepare("SELECT P.project_number, P.startdatum, C.address, P.contact_id FROM projects P LEFT JOIN contacts C ON P.contact_id = C.id WHERE P.contact_id = ? ORDER BY P.project_number");
    $stmt -> bind_param('i', $_POST['contact_id']);
}
else{
    $stmt = $con -> prepare("SELECT P.project_number, P.startdatum, C.address, P.contact_id FROM projects P LEFT JOIN contacts C ON P.contact_id = C.id WHERE P.startdatum is not NULL ORDER BY P.project_number");
}


$stmt -> execute();

$result = $stmt -> get_result();

$data = [];
$displayed_week = $_POST['week'];
$displayed_year = $_POST['year'];

while($row = $result -> fetch_assoc())
{

    $stmt_employee = $con -> prepare("SELECT E.name FROM employees E WHERE E.contact_id = ? AND E.teamleader = 0 ORDER BY E.sort_order");
    $stmt_employee -> bind_param("i", $row['contact_id']);
    $stmt_employee -> execute();

    $result_employee = $stmt_employee -> get_result();
    $employees = [];
    while($row_employee = $result_employee -> fetch_assoc())
    {
        $employees []= $row_employee['name'];
    }

    $row['employee_list'] = join(", ", $employees);
    $row['werkplanning'] = [];
    $week = getWeekNumberFromDate($row['startdatum']);
    $year = getYearFromDate($row['startdatum']);
    $stmt_1 = null;
    if(isset($_POST['werkplanning_id']))
    {
        $stmt_1 = $con -> prepare('SELECT * FROM werkplanning WHERE contact_id = ? AND id = ?  ORDER BY sort_order ');
        $stmt_1 -> bind_param('ii',$_POST['contact_id'], $_POST['werkplanning_id']);
    }
    else{
        // $stmt_1 = $con -> prepare('SELECT * FROM werkplanning WHERE ((contact_id is Null OR contact_id = ?) AND id NOT IN (SELECT default_werkplanning_id FROM werkplanning WHERE default_werkplanning_id IS NOT NULL AND contact_id = ?)) OR (contact_id = ? AND certain_week = ? AND certain_year = ?) ORDER BY sort_order ');
        $stmt_1 = $con -> prepare("SELECT * FROM werkplanning WHERE contact_id = ? AND certain_week IS NOT NULL AND certain_year IS NOT NULL ORDER BY contact_id, sort_order");
        $stmt_1 -> bind_param('i', $row['contact_id']);
    }
    
    $stmt_1 -> execute();
    $result_1 = $stmt_1 -> get_result();
    while($row_1 = $result_1 -> fetch_assoc())
    {


        if(((
            ($row_1['certain_week'] <= $displayed_week) && ($row_1['certain_year'] == $displayed_year)) || ($row_1['certain_year'] < $displayed_year)) && (empty($row_1['end_year']) || ((($row_1['end_week'] > $displayed_week) && ($row_1['end_year'] == $displayed_year)) || ($row_1['end_year'] > $displayed_year))))
        {
            $item = [];
            $item['werkplanning_id'] = $row_1['id'];
            $item['is_default'] = 1;
            if($row_1['contact_id'] != NULL && $row_1['contact_id'] != "")
                $item['is_default'] = 0;
            $item['week'] = $displayed_week;
            $item['year'] = $displayed_year;
            $item['name'] = $row_1['name'];
            // $item['hour'] = $row_1['hour'];
            $item['total_hour'] = 0;
            $item['total_hour_except_current_week'] = 0;
            $item['block'] = [];
            $stmt_2 = $con -> prepare('SELECT * FROM werkplanning_block WHERE werkplanning_id = ? AND week = ? AND year = ? ORDER BY id');
            $stmt_2 -> bind_param('iii', $row_1['id'], $displayed_week, $displayed_year);
            $stmt_2 -> execute();

            $result_2 = $stmt_2 -> get_result();
            while($row_2 = $result_2 -> fetch_assoc())
            {
                $row_2['day_count'] = 0;
                if(isset($row_2['datum_end']))
                {
                    $date1=date_create($row_2['datum_end']);
                    $date2=date_create($row_2['datum']);
                    $diff=date_diff($date2, $date1);
                    $row_2['day_count'] = $diff->format("%R%a");
                    $row_2['day_count'] = (int)($row_2['day_count']);
                    if($row_2['day_count'] > 0)
                        $row_2['day_count'] ++;
                }
                $row_2['activity'] = [];
                $row_2['medewerker'] = [];
                $stmt_3 = $con -> prepare('SELECT * FROM werkplanning_activity WHERE block_id = ? ORDER BY id');
                $stmt_3 -> bind_param('i', $row_2['id']);
                
                $stmt_3 -> execute();
                $result_3 = $stmt_3 -> get_result();
                while($row_3 = $result_3 -> fetch_assoc())
                {
                    $row_2['activity'] []= $row_3;
                }
                $stmt_3 = $con -> prepare('SELECT WM.*, E.name, E.achternaam FROM werkplanning_medewerker WM LEFT JOIN  employees E ON WM.employee_id = E.id WHERE WM.block_id = ? ORDER BY WM.id');
                $stmt_3 -> bind_param('i', $row_2['id']);
                
                $stmt_3 -> execute();
                $result_3 = $stmt_3 -> get_result();
                while($row_3 = $result_3 -> fetch_assoc())
                {
                    $row_2['medewerker'] []= $row_3;
                    
                    // $item['total_hour'] += $row_3['hour'];
                }
                $item['block'] []= $row_2;

            }

            $stmt_3 = $con -> prepare('SELECT WM.hour, WP.*, WB.year, WB.week FROM werkplanning_medewerker WM LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id LEFT JOIN werkplanning WP ON WM.werkplanning_id = WP.id WHERE WP.contact_id = ? ORDER BY WM.id');
            $stmt_3 -> bind_param('i', $row['contact_id']);

            $stmt_3 -> execute();
            $result_3 = $stmt_3 -> get_result();
            while($row_3 = $result_3 -> fetch_assoc())
            {
                $flag = 0;
                if(($row_3['year'] > $row_3['certain_year']) || (($row_3['year'] == $row_3['certain_year']) && ($row_3['week'] >= $row_3['certain_week'])))
                {
                    if(empty($row_3['end_year']))
                    {
                        $flag = 1;
                    }
                    else{
                        if(($row_3['end_year'] > $row_3['year']) || (($row_3['end_year'] == $row_3['year']) && ($row_3['end_week'] > $row_3['week'])))
                        {
                            $flag = 1;
                        }
                    }
                }
                
                if($flag == 1)
                 $item['total_hour'] += $row_3['hour'];
                 if(($flag == 1) && (($row_3['year'] != $displayed_year) || ($row_3['week'] != $displayed_week)))
                 $item['total_hour_except_current_week'] += $row_3['hour'];
            }



            $hours = 0;
            $stmt_4 = $con -> prepare('SELECT * FROM quotes WHERE contact_id = ? AND version = 2 ORDER BY quote_date DESC LIMIT 1');
            $stmt_4 -> bind_param('i', $row['contact_id']);
            $stmt_4 -> execute();
            $result_4 = $stmt_4 -> get_result();
            while($row_4 = $result_4 -> fetch_assoc())
            {
                $stmt_5 = $con -> prepare('SELECT QL.quanitty, QL.arbeid FROM quote_lines QL WHERE QL.quote_id = ? AND QL.line_am_option = "ja"');
                $stmt_5 -> bind_param('i', $row_4['id']);
                $stmt_5 -> execute();
                $result_5 = $stmt_5 -> get_result();
                while($row_5 = $result_5 -> fetch_assoc())
                {
                    $hours += convertNumberType($row_5['arbeid']) * convertNumberType($row_5['quanitty']);
                }


            }
            $item['hour'] = round($hours);

            
            $row['werkplanning'] []= $item;
        }
    }
    
    $data []= $row;
}

echo json_encode(['data' => $data]);
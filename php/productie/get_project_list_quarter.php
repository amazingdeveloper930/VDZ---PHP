<?php

require( '../../common/connection.php');
require('../../common/global.php');

/*


$_POST['convert_date'] != null &&
        $_POST['gewenste_plaatsingsdatum'] != null &&
        $_POST['startdatum'] != null &&
        $_POST['opleverdatum'] != null &&
        $_POST['portaal'] != null &&
        $_POST['plaatsing'] != null &&
        $_POST['geplaatst'] != null

*/
if ($stmt = $con->prepare('SELECT P.contact_id, P.m_status1, P.m_status2, P.m_status3, P.startdatum, C.name, C.city, C.address, C.email, C.phone FROM projects P LEFT JOIN contacts C ON P.contact_id = C.id  Where 

P.convert_date IS NOT NULL AND P.convert_date != "0000-00-00" AND
P.startdatum IS NOT NULL AND P.startdatum != "0000-00-00"
ORDER BY P.startdatum
  ')) {
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();




while ($row = $result->fetch_assoc()) {

//   $date = $row['entry_date'];
//   $timer = getTimer($date);
//   $row += ['timer' => $timer];

  $row['timer_1'] = getProjectTimer($row['startdatum'], $row['m_status1']);
  $row['timer_2'] = getProjectTimer($row['startdatum'], $row['m_status2']);
  $row['timer_3'] = getProjectTimer($row['startdatum'], $row['m_status3']);
  
  $stmt_1 = $con -> prepare('SELECT PL.leverdatum, PL.projects_tasks_id, PT.name FROM project_tasks_lines PL LEFT JOIN projects_tasks PT ON PL.projects_tasks_id = PT.id WHERE PL.special_jaarplanning = "YES" AND PL.status ="COMPLETED" AND PL.contact_id = ?');
  $stmt_1 -> bind_param('i', $row['contact_id']);
  $stmt_1 -> execute();
  $result_1 = $stmt_1->get_result();
  $special_jaar = [];
  while($row1 = $result_1->fetch_assoc())
  {
    $special_jaar []= $row1;
  }
  $row['special_jaars'] = $special_jaar;

  $stmt_1 = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ? ORDER BY sort_order');
  $stmt_1 -> bind_param('i', $row['contact_id']);
  $stmt_1 -> execute();
  $result_1 = $stmt_1->get_result();
  $plan = [];
  while($row1 = $result_1->fetch_assoc())
  {
    $plan []= $row1;
  }

  $row['plan'] = $plan;

  $result_array[] = $row;

}
$employee_rows = [];

$stmt = $con -> prepare('SELECT * FROM employees WHERE aankomst_datum is not NULL AND vertrek_datum is NOT NULL AND  aankomst_datum != "0000-00-00" AND vertrek_datum != "0000-00-00" AND inweekplanning = "Ja"');
$stmt -> execute();
$result_em = $stmt -> get_result();
$quarter = $_POST['quarter'];
$year = $_POST['year'];

while($row = $result_em -> fetch_assoc())
{
  $employee_rows []= $row;
}




for($index = 0 ; $index < 13 ; $index ++)
{
  $week = ($quarter - 1) * 13 + 1 + $index;
  $employee_count = 0;
  for($jdex = 0; $jdex < 6; $jdex ++)
  {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $dto->modify('+' . $jdex . ' days');
    $date = $dto->format('Y-m-d');
    $count = 0;
    foreach($employee_rows as $employee)
    {
      if((($date >= $employee['aankomst_datum']) && ($date <= $employee['vertrek_datum'])) || (($date >= $employee['aankomst_datum2']) && ($date <= $employee['vertrek_datum2'])))
        $count ++;
    }
    if($count > $employee_count)
      $employee_count = $count;
  }
  $employees []= $employee_count;
}

echo json_encode(['result' => $result_array, 'employees' => $employees]);
}

?>
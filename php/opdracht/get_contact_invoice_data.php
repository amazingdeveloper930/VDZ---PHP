<?php

require( '../../common/connection.php');
require('../../common/global.php');


$invoice_data_array =  array();
$invoice_data_array[0]['name'] = 'Aanbetaling 25%';
$invoice_data_array[1]['name'] = 'Voor plaasing 65%';
$invoice_data_array[2]['name'] = 'Slotsom 10%';
$invoice_data_array[3]['name'] = 'Meerwerk';

for($index = 0; $index < count($invoice_data_array); $index ++)
{
    $invoice_data_array[$index]['invoice_date'] = null;
    $invoice_data_array[$index]['paid_date'] = null;
    $invoice_data_array[$index]['invoice_price'] = null;
    $invoice_data_array[$index]['paid_price'] = null;


    $stmt = $con -> prepare('SELECT PTSL.*, PTL.status FROM project_tasks_special_lines PTSL JOIN project_tasks_lines PTL ON PTL.contact_id = PTSL.contact_id AND PTL.projects_tasks_id = PTSL.projects_tasks_id WHERE PTSL.contact_id = ? AND (PTSL.projects_tasks_id = ? OR PTSL.projects_tasks_id = ?)');
    $invoice_task_id = $index + 1;
    $invoice_payment_task_id = $index + 45;
    $stmt -> bind_param('iii', $_POST['contact_id'], $invoice_task_id, $invoice_payment_task_id);
    $stmt -> execute();
    $invoice_result = $stmt -> get_result();
    while($row_invoice = $invoice_result->fetch_assoc())
    {
        if($row_invoice['projects_tasks_id'] >= 45)
        {
            if($row_invoice['status'] == 'COMPLETED')
            {
                $invoice_data_array[$index]['paid_date'] = $row_invoice['betaaldatum'];
                $invoice_data_array[$index]['paid_price'] = '€' . $row_invoice['price_inc'];
            }
            
        }
        else{
            if($row_invoice['status'] == 'COMPLETED')
            {
                $invoice_data_array[$index]['invoice_date'] = $row_invoice['betaaldatum'];
                $invoice_data_array[$index]['invoice_price'] = '€' . $row_invoice['price_inc'];
            }
            
        }
    }

    $invoice_data_array[$index]['timer_widget'] = null;
    $mode = 0;//checkbox
    if($invoice_payment_task_id == 48)
    {
        if($invoice_data_array[$index]['paid_price'] == null)
            $mode = 1;//gray
    }
    else{
        if($invoice_data_array[$index]['paid_price'] == null)
        $mode = 1;//gray
        else if($invoice_data_array[$index]['paid_price'] != $invoice_data_array[$index]['invoice_price'])
            $mode = 2;//red
    }
    $invoice_data_array[$index]['timer_widget'] = getInvoiceTimerWidget($invoice_data_array[$index]['invoice_date'], $mode);
    $invoice_data_array[$index]['invoice_payment_task_id'] = $invoice_payment_task_id;
}

echo json_encode($invoice_data_array);
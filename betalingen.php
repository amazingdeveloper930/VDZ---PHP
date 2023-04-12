
<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'betalingen';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Opdracht voorbereiding - Van der Zeeuw Bouw Ordersysteem</title>	

		<?php include 'common/header.php'; ?>		

</head>
<?php 

require( 'common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.

$result = null;
if ($stmt = $con->prepare(
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, CL.entry_type, CL.entry_date, O.project_number, O.convert_date, O.startdatum, O.plaatsing FROM contacts C LEFT JOIN (SELECT contact_id, entry_type, entry_date FROM contact_log WHERE id IN ( SELECT MAX(id) FROM contact_log GROUP BY contact_id)) CL ON (C.id = CL.contact_id) 
    LEFT JOIN projects O ON (C.id = O.contact_id)
    WHERE C.l_status = 1')) {	

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}


?>

	<body class="app">
        
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
			<div class="titlebar">
				<div class="titlebarcontainer" >
					<h2>Opdrachten
						<div class="page-info tooltipped" data-position="top" data-tooltip="Meer informatie"
							data-page-info="Een project komt in dit scherm als de status van het contact in het vorige scherm wordt aangepast naar 'Deal'. Een project verlaat dit scherm zodra 'Geplaatst?' ingesteld staat op 'Ja' in de taken popup."
						>
							<img src="<?=IMG_DIR_PATH . 'question_mark.svg'?>" />
						</div>
					</h2>
					<div class="submenu">					
						<div class="row">
							<div class="col s12">
								<ul class="tabs">
									<li class="col"><a class="active" href="/opdracht">Opdrachten</a></li>
									<li class="tab col"><a href="#">Betalingen</a></li>
                                    					<li class="col"><a href="/alle_taken">Alle taken</a></li>
				    					<li class="col"><a href="/opdracht#opentickets">Open tickets</a></li>
								</ul>
							</div>
						</div>
					</div>
					<span class="titlebarbutton button waves-effect waves-light btn" onclick="toggleHold()" id="btn_hold"><i class="material-icons">arrow_upward</i> <span>Inklappen</span></span>

                    <div class="opdrachten-option">
						<label>
							<input type="checkbox" class="filled-in" id="cb-opgeleverd"/>
							<span>Toon ook opgeleverd</span>
						</label>
					</div>
					<div class="opdrachten-option">
						<label>
							<input type="checkbox" class="filled-in"  id="cb-gestart"/>
							<span>Verberg niet gestart</span>
						</label>
					</div>

					<div style="clear:both"></div>
				</div>				
				<div style="clear:both"></div>
			</div>			

			<div id="projecten" class="tab-content active">			
				<table class="contacten-table full-w-table sort-table-group" id="table-betalingen">
					<thead>
					<tr>
                        <th class="" >Project</th>
                        <td></td>
						<th class="" style="width:200px">Naam</th>
						<th class="" >Stad</th>
						<th class="" style="width:200px">Adres</th>					
						<th class="datum-column">Startdatum</th>
						<th class="no-sort"></th>
                        <th class="no-sort">Status</th>
						<th style="width:150px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php
					
					if ($result) : while ($row = $result->fetch_assoc()) : 


							//////////////////////////////
                            $row['timer_widget'] = getInvoiceTimerWidget(null, 1);
							$min_date_gap = -1;
                            $all_task_done = true;
					?>

						
                        <?php 
                            $invoice_data_array =  array();
                            $unpaid_invoice_count = 0;
                            $stmt = $con -> prepare('SELECT PT2.*, PTL.started_at, PTL.status, PTL.facturenren, PTSL.price, PTSL.price_inc, PTSL.betaaldatum FROM (SELECT * FROM projects_tasks PT WHERE (PT.custom_contact_id is null or PT.custom_contact_id = ?) AND PT.is_invoice = 0 AND PT.invoice_id is not null ORDER BY PT.sort_order) PT2 LEFT JOIN project_tasks_lines PTL ON PT2.id = PTL.projects_tasks_id AND PTL.contact_id = ?
                            LEFT JOIN project_tasks_special_lines PTSL ON  PT2.id = PTSL.projects_tasks_id AND PTSL.contact_id = ?');
                            $stmt -> bind_param('iii', $row['id'], $row['id'], $row['id']);
                            $stmt -> execute();
                            $result_paid = $stmt -> get_result();
                            while($row_paid = $result_paid -> fetch_assoc())
                            {
                                $item = [];
                                $item['invoice_id'] = $row_paid['invoice_id'];
				$item['facturenren'] = -1;
                                $item['invoice_date'] = null;
                                $item['paid_date'] = null;
                                $item['invoice_price'] = null;
                                $item['paid_price'] = null;
                                $item['name'] = str_replace(' betaald', '', $row_paid['name']);
                                if($row_paid['status'] == 'COMPLETED')
                                {
                                    $item['paid_price'] = '€' . $row_paid['price_inc'];
                                    $item['paid_date'] = $row_paid['betaaldatum'];
                                }
                                $stmt = $con -> prepare('SELECT PTL.*, PTSL.factuurnummer, PTSL.price, PTSL.price_inc, PTSL.betaaldatum FROM project_tasks_lines PTL LEFT JOIN project_tasks_special_lines PTSL ON PTL.projects_tasks_id = PTSL.projects_tasks_id AND PTL.contact_id = PTSL.contact_id WHERE PTL.contact_id = ? AND PTL.projects_tasks_id = ?');
                                $stmt -> bind_param('ii', $row['id'], $row_paid['invoice_id']);
                                $stmt -> execute();
                                $result_invoice = $stmt -> get_result();
                                while($row_invoice = $result_invoice -> fetch_assoc())
                                {
                                    if($row_invoice['status'] == 'COMPLETED')
                                    {
                                        $item['invoice_price'] = '€' . $row_invoice['price_inc'];
                                        $item['invoice_date'] = $row_invoice['betaaldatum'];
                                    }
                                    $item['facturenren'] = $row_invoice['facturenren'];
                                    $item['factuurnummer'] = $row_invoice['factuurnummer'];
                                }

                                
                                $item['timer_widget'] = null;
                                $mode = 0;//checkbox
                                if($item['invoice_price'] == null)
                                    $mode = 1;//gray
                                else if($item['paid_price'] != $item['invoice_price'])
                                    $mode = 2;//red
                                if($mode == 2)
                                    $unpaid_invoice_count++;
                                $item['timer_widget'] = getInvoiceTimerWidget($item['invoice_date'], $mode);

                                $item['mode'] = $mode;
                                $invoice_data_array []= $item;
                                $gap = dateDifferenceD($item['invoice_date'], 1);
                                if($gap < 0)
                                    $gap = 0;
                                if($mode == 2 && ($min_date_gap > $gap || $min_date_gap < 0))
                                    {
                                        $min_date_gap = $gap;
                                        $row['timer_widget'] = getInvoiceTimerWidget($item['invoice_date'], $mode);
                                    }
                                if($mode != 0)
                                    $all_task_done = false;
                              

                            }

                            

							
					?>

						
                        <?php 
                             
                            if($all_task_done)
                                $row['timer_widget'] = getInvoiceTimerWidget(null, 0);
                        ?>
                    <tr contactrow="<?=$row['id'];?>" class="contactrow <?php echo($unpaid_invoice_count > 0)?'contract-unpaid':'contract-paid'; ?>"  data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                            <td><?=$row['project_number'];?><i class="material-icons identify-invoice">circle</i></td>
                            <td></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
						
							<td><?=$row['startdatum'] && $row['startdatum'] != '' && $row['startdatum'] != '0000-00-00' ?(new DateTime($row['startdatum'])) -> format('d-m-Y'):'<span class="text-red">Nog niet gepland</span>'?></td>
							
                            <td></td>
                            <td><?=$row['timer_widget'];?></td>
                            
							<td>
							<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
                                <div onclick="manageProjectTask(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
								
							</td>
						</tr>	
                        <tr class="row-gray row-invoice-header row-hold" contactid="<?=$row['id']?>"  data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                            <td>Factuur</td>
                            <td>Factuurnummer</td>
                            <td>Datum verzonden</td>
                            <td>Datum betaald</td>
                            <td>Bedrag factuur</td>
                            <td>Bedrag betaald</td>
                            <td>Factureren</td>
                            <td>Status</td>
                            
                            <td></td>
                        </tr>
                        <?php 
                        for($index = 0; $index < count($invoice_data_array); $index ++)
                        {
                        ?>
                            <tr class="row-gray row-hold" contactid="<?=$row['id']?>" invoice_payment_task_id="<?=$invoice_data_array[$index]['invoice_id']?>">
                            <td><?=$invoice_data_array[$index]['name']?></td>
                            <td><?=$invoice_data_array[$index]['factuurnummer']?></td>
                            <td><?=getFDate($invoice_data_array[$index]['invoice_date'])?></td>
                            <td><?=getFDate($invoice_data_array[$index]['paid_date'])?></td>
                            <td><?=$invoice_data_array[$index]['invoice_price']?></td>
                            <td><?=$invoice_data_array[$index]['paid_price']?></td>
                            
                            
                            <td>
                                <?php 
                                    if($invoice_data_array[$index]['facturenren'] != -1)
                                {
                                ?>
                                <label><input type="checkbox" class="filled-in cb-facturenren" <?=$invoice_data_array[$index]['facturenren']? 'checked':''?> onchange="savefactureren(<?=$row['id'] . ',' . $invoice_data_array[$index]['invoice_id']?>)" <?=$invoice_data_array[$index]['mode'] == 0 ?'disabled':'';?>><span></span></label>
                                <?php 
                                }?>
                            </td>
                            <td><?=$invoice_data_array[$index]['timer_widget']?></td>
                            <td></td>
                            </tr>
                            <?php
                            }
                        ?>
					<?php endwhile; endif; ?>

					</tbody>
				</table>
			</div>
			

		</div>		
	<input type="hidden" value="<?=$root?>" id="root_path"/>
	
	<script src="<?=$root;?>js/betalingen.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>

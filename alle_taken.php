<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'opdracht';
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
	'SELECT C.id, C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, C.l_status, O.project_number, O.convert_date, O.startdatum, O.plaatsing  FROM contacts C
    LEFT JOIN projects O ON (C.id = O.contact_id)
    WHERE C.l_status = 1')) {	

	//$stmt->bind_param('i', 3); // only lead
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
}

$array_taskchapter = [];
if ($stmt = $con->prepare("SELECT * FROM projects_tasks_chapters ORDER BY sort_order")){
    $stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result_taskchapter = $stmt->get_result();
    while ($row = $result_taskchapter->fetch_assoc()) {
        
        $array_taskchapter []= $row;
    }
};


?>

	<body class="app">
		
		<?php include 'common/navigatie.php'; ?>		

		<div class="appcontent">
		
			<div class="titlebar">
				<div class="titlebarcontainer"  >
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
									<li class="col"><a href="/opdracht">Opdrachten</a></li>
									<li class="col"><a href="/betalingen">Betalingen</a></li>
                                    					<li class="tab col"><a class="active" href="#">Alle taken</a></li>
									<li class="col"><a href="/opdracht#opentickets">Open tickets</a></li>
								</ul>
							</div>
						</div>
					</div>
					<span class="titlebarbutton button waves-effect waves-light btn" onclick="toggleHold()" id="btn_hold"><i class="material-icons">arrow_upward</i> <span>Inklappen</span></span>
					<select class="browser-default" id="s_showoption" onchange="showOption()">
                    <!-- please set value as db's id -->
                        <option value=0>Toon alles</option>
                        <option value=1>Administratie (Nathalie)</option>
                        <option value=2>Verkoop (Henry)</option>
                        <option value=3>Werkvoorbereiding (Sabine & Nathalie)</option>
                        <option value=4>Productie (Ivan/Jochem)</option>
                        <option value=5>Meer-/minderwerk (Rob)</option>
                    </select>
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
				<table class="contacten-table full-w-table sort-table-group" id="table-alltaken">
					<thead>
					<tr>
                        <th>Project</th>
						<th>Naam</th>
						<th>Stad</th>
						<th>Adres</th>
						<th class="datum-column">Startdatum</th>
						<th class="no-sort"></th>	
                        <th class="no-sort"></th>					
						<th style="width:150px" class="no-sort"></th>
					</tr>
					</thead>
					<tbody>				

					<?php
					
					if ($result) : while ($row = $result->fetch_assoc()) : 

							$temp = [];
                            if(dateDifferenceD($row['startdatum'], 1) > 0)
                                $row['gestart'] = 'nee';
                            else 
                                $row['gestart'] = 'ja';
					?>

						<tr class="contactrow" contactrow="<?=$row['id'];?>" chaptershown="true" data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                            <td><?=$row['project_number'];?></td>
							<td><?=$row['name'];?></td>
							<td><?=$row['city'];?></td>
							<td><?=$row['address'];?></td>
							<td><?=$row['startdatum'] && $row['startdatum'] != '' && $row['startdatum'] != '0000-00-00' ?(new DateTime($row['startdatum'])) -> format('d-m-Y'):'<span class="text-red">Nog niet gepland</span>'?></td>							
							<td></td>
                            <td></td>
							<td>
							<div onclick="manageProjectFileLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Bestanden"><i class="material-icons">attach_file</i></div>
                                <div onclick="manageProjectTask(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Taken"><i class="material-icons">list</i></div>
								<div onclick="manageContactLog(<?=$row['id'];?>)" class="actiebutton tooltipped" data-position="top" data-tooltip="Logboek"><i class="material-icons">assignment</i></div>
							</td>
						</tr>
                        <?php 
                            foreach($array_taskchapter as $taskchapter){
                        ?>
                        <tr class="chapterrow tr-darkgray" contactrow="<?=$row['id'];?>" chapterrow="<?=$taskchapter['id'];?>" data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                            
                                <td><?=$taskchapter['name']?></td>
                                <td>Afgerond door</td>
                                <?php 
                                if($taskchapter['id'] == 1){ ?>
                                <td>Bedrag incl. BTW</td>
                                <td>Bedrag excl. BTW</td>
                                <td>Datum</td>
                                
                                <?php 

                                }
                                else{
                                    ?>
                                    
                                <td>Leverancier</td>
                                <td>Besteldatum</td>
                                <td>Leverdatum</td>
                                    <?php 
                                }
                                ?>
                                <td><i class="material-icons">date_range</i></td>
                                <td>Timer</td>
                                <td></td>
                        </tr>
                        <?php 
                            $stmt = $con -> prepare('SELECT PT.*,  PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, S.name AS suppliername, PTL.user_id,  PTL.special_jaarplanning, A.username, PTSL.price, PTSL.price_inc, PTSL.betaaldatum FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id 
                            LEFT JOIN suppliers S ON PTL.supplier_id = S.id
                            LEFT JOIN project_tasks_special_lines PTSL ON PTL.contact_id = PTSL.contact_id AND PT.id = PTSL.projects_tasks_id
                            WHERE PT.chapter = ? AND (PT.custom_contact_id is NULL OR PT.custom_contact_id = ?)
                            ORDER BY PT.sort_order ASC');
                            $stmt->bind_param('iii', $row['id'], $taskchapter['id'], $row['id']);
                            $stmt->execute();
                            $result_tasks = $stmt->get_result();
                            while($row_task = $result_tasks -> fetch_array())
                            {        
                                $mode = null;
                                if($row_task['status'] == 'COMPLETED')
                                    $mode = 2;
                                if($row_task['status'] == 'PROCESSING')
                                    $mode = 1;
                                if($row_task['status'] == 'SKIPPED')
                                    $mode = 0;
                                if(isset($row_task['status']) && $row_task['isspecial_task'] == 1 && $row_task['id'] == 48)
                                    $mode = 3;
                                if($row_task['id'] <= 3)
                                    $temp[$row_task['id']] = $row_task['price_inc'];

                                ?>
                                    <tr class="taskrow tr-gray" contactrow="<?=$row['id'];?>" chapterrow="<?=$taskchapter['id'];?>" taskrow="<?=$row_task['id']?>" data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                                        <td><?=$row_task['name']?></td>
                                        
                                        <?php
                                        if(!isset($row_task['status']))
                                        {
                                            echo "<td></td>";
                                            echo "<td></td>";
                                            echo "<td></td>";
                                            echo "<td></td>";
                                            echo "<td></td>";
                                        }
                                        else if($row_task['status'] == 'PROCESSING'){         
                                            ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php } 
                                        else{
                                        ?>
                                        <td><i class="material-icons">person</i><?=$row_task['username']?></td>
                                        <?php 

                                            if($row_task['status'] == 'SKIPPED'){
                                                echo "<td></td><td></td><td></td><td></td>";}
                                            if($row_task['status'] == 'COMPLETED'){
                                                if($row_task['isspecial_task'] == 1){
                                                    echo "<td>&euro;" . $row_task['price_inc'] . "</td>";
                                                    echo "<td>&euro;" . $row_task['price'] . "</td>";
                                                    echo "<td>" . dateFormat($row_task['betaaldatum']) . "</td>";
                                                    echo "<td></td>";
                                                    // if($row_task['id'] >= 45 && $row_task['id'] < 48 && $row_task['price_inc'] != $temp[$row_task['id'] - 44])
                                                    //     $mode = 1;
                                                }
                                                else{
                                                    echo "<td>" . (($row_task['supplier'] == 'true') ? $row_task['suppliername'] : '') . "</td>";
                                                    echo "<td>" . (($row_task['order_date'] == 'true') ? dateFormat($row_task['besteldatum']) : '') . "</td>";
                                                    echo "<td>" . (($row_task['supply_date'] == 'true') ? dateFormat($row_task['leverdatum']) : '') . "</td>";
                                                    if($row_task['special_jaarplanning'] == 'YES')
                                                    echo "<td><i class='material-icons'>check_box</i></td>";
                                                    else
                                                    echo "<td></td>";
                                                }

                                        
                                            }
                                        }
                                        $timer_widget = getTaskTimer($row_task['started_at'],  $row_task['timer'], $mode);;
                                        ?>
                                        <td><?=$timer_widget?></td>
                                        <td></td>
                                    </tr>
                                <?php  
                                }
                            } ?>
                            
                            <?php 
                                $stmt = $con -> prepare("SELECT ST.* FROM special_tasks ST WHERE contact_id = ? ORDER BY ST.id");
                                $stmt -> bind_param("i", $row['id']);
                                $stmt -> execute();
                                $result_specialtasks = $stmt -> get_result();
                                ?>
                                <tr class="chapterrow tr-darkgray <?=$result_specialtasks-> num_rows == 0 ? 'tr-hidden':''?>" contactrow="<?=$row['id'];?>" chapterrow="special_tasks" data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                                <td>Meer-/minderwerk (Rob)</td>
                                <td>Prijs ex.BTW</td>
                                <td>BTW</td>
                                <td>Prijs inc.BTW</td>
                                <td>Wijze akkoord</td>
                                <td colspan="2">Datum akkoord</td>
                                <td ></td>                                
                                </tr>
                                <?php
                                while($row_st = $result_specialtasks -> fetch_array())
                                {
                                    ?>
                                    <tr class="taskrow staskrow tr-gray" contactrow="<?=$row['id'];?>" staskrow="<?=$row_st['id']?>" data-opgeleverd = "<?=$row['plaatsing']?>" data-gestart = "<?=$row['gestart']?>">
                                        <td><?=$row_st['text']?></td>
                                        <td>&euro;<?=$row_st['price']?></td>
                                        <td><?=$row_st['vat'] * 100;?>%</td>
                                        <td>&euro;<?=$row_st['price_inc']?></td>
                                        <td><?=$row_st['option']?></td>
                                        <td colspan="2"><?=dateFormat($row_st['date']);?></td>
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
	
	<script src="<?=$root;?>js/alle_taken.js" type="text/javascript"></script>
	<script src="<?=$root;?>js/file_upload.js" type="text/javascript"></script>

<?php include 'common/footer.php'; ?>

<?php 

	require '../../common/global.php';
    require( '../../common/connection.php');

    $sql = "SELECT C.*, P.project_number,P.plaatsing, P.startdatum, P.as_status FROM contacts C LEFT JOIN projects P ON C.id = P.contact_id ORDER BY C.id";
    $stmt = $con -> prepare($sql);
    $stmt -> execute();
    $result = $stmt -> get_result();
?>
	<form id="searchContacts" class="col s12">
        <div class="row">
        <div class="input-field col s12">
            <input id="keyword" type="text" name="username" value="" onInput="keyEntered()">
            <i class="material-icons subfix">search</i>
            <label for="keyword" class="active">Zoeken</label>
        </div>
        </div>
	</form>
    <div class="searchResult-panel">
        <table class="full-w-table">
            <thead>
                <tr>
                    <th width="70%">Project</th>
                    <th>Fase</th>
                    <th width="75px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) 
                 {
                    $type = '';
                    $link = '';
                    
                    if($row['c_status'] == 1 || $row['c_status'] == 2)
                    {
                        $type =  'Funnel';
                        $link = '/funnel/?id=' . $row['id'];
                        if($row['c_status'] == 1)
                            $link .="#actievecontacten";
                        if($row['c_status'] == 2)
                            $link .="#inactievecontacten";
                        
                    }
                    else{
                        if($row['l_status'] == 1)
                        {//O.geplaatst = "nee" AND O.startdatum is not NULL
                            //O.plaatsing = "nee" AND O.geplaatst = "nee"
                            //O.as_status = 1  AND O.geplaatst = "ja"
                            if($row['as_status'] == 1)
                            {
                                $type = 'After Sales';
                                $link = '/aftersales/?id=' . $row['id'];
                                if($row['plaatsing'] == 'nee')
                                    $link .="#project_normal";
                                if($row['plaatsing'] == 'ja')
                                    $link .="#project_inactive";
                            }
                                
                            if(($row['as_status'] == 0 || $row['as_status'] == null) && isset($row['startdatum']))
                            {
                                if($row['plaatsing'] == "nee"){
                                    $type =  'Plaatsing';
                                    $link = '/plaatsing/?id=' . $row['id'];
                                }
                                    
                                else {
                                    $type = 'Productie';
                                    $link = '/productie/?id=' . $row['id'];
                                }
                                    
                            }
                            if($type == ''){
                                $type = 'Opdracht';
                                $link = '/opdracht/?id=' . $row['id'];
                            }
                                
                                
                        }
                        else
                        {
                            $type =  'Leads';
                            $link = '/leads/?id=' . $row['id'];
                            if($row['l_status'] != 2)
                                $link .="#actieveleads";
                            if($row['c_status'] == 2)
                                $link .="#inactieveleads";
                        }
                    }
                ?>
                <tr c-name="<?=strtolower($row['name'])?>" c-address="<?=strtolower($row['address'])?>"  c-city="<?=strtolower($row['city'])?>" c-email="<?=strtolower($row['email'])?>"  p-number="<?=$row['project_number']?>" onclick='gotoPage("<?=$link?>")'>
                    <td><?php 
                    echo $row['address'] . ", " . $row['name'];
                    ?></td>
                    <td>
                        <?php 
                            echo $type;
                        ?>
                    </td>
                    <td><div class="actiebutton" data-position="top"><i class="material-icons">chevron_right</i></div></td>
                </tr>
                <?php 
                 }
                ?>
            </tbody>
        </table>
    </div>

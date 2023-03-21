<?php 
require 'common/sessie_check.php';
require 'common/global.php';

$currentpage = 'jaarplanning';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Werkorder</title>	

		<?php include 'common/header.php'; ?>		
        <?php 

            require( 'common/connection.php');
            $plan_id = $_REQUEST['plan_id'];
            $stmt = $con -> prepare('SELECT * FROM werkplanning_medewerker WHERE id = ? ORDER BY id');
            $stmt -> bind_param('i', $_REQUEST['plan_id']);
            $stmt -> execute();
            $result = $stmt -> get_result();
            while($row = $result->fetch_assoc())
            {
                if($row['parent_plan'] != null)
                {
                    $plan_id = $row['parent_plan'];
                }
            }
            $stmt = $con -> prepare('SELECT * FROM work_orders WHERE plan_id = ? ORDER BY id');
            $stmt -> bind_param('i', $plan_id);
            $stmt -> execute();
            $result = $stmt -> get_result();
            $result_array = [];

        ?>
    </head>
    <body>
    
        <table class="full-w-table" style="box-shadow: none;">
            <thead>
                <tr>
                    <th width='50%'>Omschrijving</th>
                    <th width='20%'>Materiaal</th>
                    <th width='30%'>Gereedschap</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while($row = $result->fetch_assoc())
                {
                    $html = "<tr><td>" . $row['description'] . "</td>" .
                            "<td>" . $row['material'] . "</td>" . 
                            "<td>" . $row['tool'] . "</td>" . 
                    "</tr>";
                    echo $html;
                }
                ?>
            </tbody>
        </table>
    </body>
</html>

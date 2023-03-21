<?php

require( '../../common/connection.php');

$woord = "coördinatie";

                    if($stmt = $con -> prepare('INSERT INTO quote_lines (line_title, line_descr) VALUES (?, ?)')){                   
                        $stmt->bind_param('ss', $woord, $woord );
                        $stmt->execute();
                        
                    }
                    else{
                        echo json_encode(['message' => $con -> error]);
                        return;
                    }
                    

echo "klaar!";
            
?>
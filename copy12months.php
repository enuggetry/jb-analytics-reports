<?php

/* 
 * copies last 12 months of data to last12months table
 * some reports depend on last 12 months of data only
 */

    include "db.php";
    include "util.php";

    echo "Report Active Hosts by Month\n";
    
    $starttime = microtime(true);
    
    
    /*
     * emptying last12months
     */
    echo "emptying last12months\n";
    
    $myquery = "
        TRUNCATE TABLE last12months        
    ";
    
    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error()).'\n';
    }
    
    /*
     * copying...
     */
    echo "copying....\n";
    //we add an additional 30 days
    $myquery2 = "
        INSERT INTO last12months 
        SELECT * 
        FROM jbrowse_client_log
        WHERE FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 395 DAY AND NOW()
    ";
    
    $result2 = mysql_query( $myquery2 );
    if (!$result2) {
            die('Invalid query: ' . mysql_error()).'\n';
    }



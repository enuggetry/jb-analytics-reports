<?php
    include "util.php";
    include "db.php";

    $starttime = microtime(true);

    //$myfile = fopen("active-hosts-by-month.csv", "w") or die("Unable to open file!");

    //fwrite($myfile,"Running Active Hosts by Month\n");

    $myquery = "
SELECT FROM_UNIXTIME(reportTime),host,plugins
FROM  `jbrowse_client_log` 
WHERE  `plugins` LIKE  '%WhatThePlugin%'
LIMIT 30
    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    while ($row = mysql_fetch_array( $result, MYSQL_NUM)) {
            $txt = "";
            for($i = 0;$i < sizeof($row);$i++) {
                    if ($i > 0) $txt .= ",";
                    $txt .= $row[$i];
            }
            $txt .= "\n";
            //fwrite($myfile, $txt);
            echo $txt;
            $count++;
    }

    echo("$count total rows\n");

    //fclose($myfile);

    mysql_close( $dbh );

    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

?>

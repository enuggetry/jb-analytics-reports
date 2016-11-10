<?php

    include "db.php";

    echo "Report Active Hosts by Month\n";

    $myfile = fopen("unique-clients-12-mon.csv", "w") or die("Unable to open file!");

    $myquery = "
            select host, clientAddr, count(*) as t_count
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()
            group by host, clientAddr having t_count > 4
            order by t_count DESC


    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    while ($row = mysql_fetch_array( $result, MYSQL_NUM)) {
            //echo count($row)."\n";
            //if ($row[0]=="") continue;
            //if ($row[0]=="jbrowse.org") continue;
            //if (strstr($row[0],"localhost")) continue;
            //if (strstr($row[0],"127.0.0.1")) continue;
            $txt = "";
            for($i = 0;$i < sizeof($row);$i++) {
                    if ($i!= 0) $txt .= ",";
                    $txt .= $row[$i];
            }
            $txt .= "\n";
      fwrite($myfile, $txt);
      $count++;
    }

    echo("$count total rows\n");

    fclose($myfile);

    mysql_close( $dbh );

?>

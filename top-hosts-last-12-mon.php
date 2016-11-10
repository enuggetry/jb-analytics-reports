<?php

    include "db.php";

    echo "Report Active Hosts by Month\n";

    $myfile = fopen("top-hosts-12-months.csv", "w") or die("Unable to open file!");

    $myquery = "
            select host, count(*) as t_count 
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()
            group by host
            order by t_count DESC
            limit 100
    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    while ($row = mysql_fetch_array( $result, MYSQL_NUM)) {
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

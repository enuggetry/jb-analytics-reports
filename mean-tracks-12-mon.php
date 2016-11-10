<?php
    include "util.php";
    include "db.php";

    echo "Report Active Hosts by Month\n";
    
    $myfile = fopen("mean-tracks-12-months.csv", "w") or die("Unable to open file!");

    //fwrite($myfile,"Running Active Hosts by Month\n");
    //select REPLACE(host,'www.','') as t_host, SUM(trackCount) / COUNT(*) as t_mean, count(*) t_count 

    fwrite($myfile,"host,mean,count\n");    // headers
    
    $myquery = "
            select host as t_host, SUM(trackCount) / COUNT(*) as t_mean, count(*) t_count 
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()
            group by t_host having t_count > 100
            order by t_mean DESC

            ";
    $starttime = microtime(true);

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
            //$txt = "track sum = ".$row[0]."\n";
            //$txt .= "record count = ".$row[1]."\n";
            //$txt .= "mean (sum/count) = ".$row[0] / $row[1]."\n";
            //fwrite($myfile, $txt);
            //echo $txt;
            $count++;
    }

    echo("$count total rows\n");
    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    fclose($myfile);
    mysql_close( $dbh );

?>

<?php
    include "util.php";
    include "db.php";

    echo "Report - Trackcount Counts, last 12 months\n";
    
    $myfile = fopen("trackcount-counts-12mon.csv", "w") or die("Unable to open file!");

    
    $myquery = "
            select trackCount as t_count, COUNT(*) as n 
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()
            group by t_count
            order by t_count ASC

            ";
    $starttime = microtime(true);

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    fwrite($myfile,"How many times each trackCount value was seen (unfiltered) \n");    // headers
    fwrite($myfile,"trackCount,count\n");    // headers

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

<?php
    include "util.php";
    include "db.php";

    echo "Report - large trackcounts (> 2000, last 12 months\n";
    
    $myfile = fopen("track-large-referer.csv", "w") or die("Unable to open file!");

    // by month
    $myquery = "
            select host, trackCount, 
                CONCAT(YEAR(FROM_UNIXTIME(reportTime)),'-', LPAD(MONTH(FROM_UNIXTIME(reportTime)),2,'0')) as t_month,
                referer
            from last12months 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW() and trackCount > 5000
            order by trackCount DESC

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
                    $txt .= '"'.$row[$i].'"';
            }
            $txt .= "\n";
            fwrite($myfile, $txt);
            $count++;
    }

    echo("$count total rows\n");
    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    fclose($myfile);
    mysql_close( $dbh );

?>

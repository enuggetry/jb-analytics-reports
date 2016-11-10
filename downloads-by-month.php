<?php
    include "db_wordpress.php";

    echo "Report Downloads by month\n";
    
    $myfile = fopen("downloads-by-month.csv", "w") or die("Unable to open file!");

    fwrite($myfile,"Downloads by month\n");

    $myquery = "
            select CONCAT(YEAR(date),'-', LPAD(MONTH(date),2,'0')) as t_date, SUM(hits)
            from wp_download_monitor_stats
            where date >= '2010'
            group by t_date
        order by date DESC
    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    while ($row = mysql_fetch_array( $result, MYSQL_NUM)) {
            //echo $row[0].",".$row[1]."\n";
            $txt = "";
            for($i = 0;$i < count($row);$i++) {
                    if ($i > 0) $txt .= ",";
                    $txt .= $row[$i];
            }
            $txt .= "\n";
            echo $txt;
            fwrite($myfile, $txt);
            $count++;
    }

    echo("$count total rows\n");

    fclose($myfile);

    mysql_close( $dbh );

?>

<?php

    include "db.php";

    echo "Report Active Hosts by Month\n";
    
    $myfile = fopen("active-hosts-by-month.csv", "w") or die("Unable to open file!");
    

    $myquery = "
            select 
                    CONCAT(YEAR(FROM_UNIXTIME(reportTime)),'-', LPAD(MONTH(FROM_UNIXTIME(reportTime)),2,'0')) as t_date, 
                    count(*) as Hosts 
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) >= '2000' and FROM_UNIXTIME(reportTime) < NOW()
            group by t_date
            order by t_date DESC
    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    
    fwrite($myfile,"Date,Host Count\n");
    
    while ($row = mysql_fetch_array( $result, MYSQL_NUM)) {
            $txt = "";
            for($i = 0;$i < sizeof($row);$i++) {
                    if ($i > 0) $txt .= ",";
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

<?php

    include "db.php";
    include "util.php";

    echo "Report - Unique clients 12 months\n";

    $starttime = microtime(true);

    $myquery = "

select 
    host, 
    clientAddr,
    CONCAT(YEAR(FROM_UNIXTIME(reportTime)),'-', LPAD(MONTH(FROM_UNIXTIME(reportTime)),2,'0')) as t_month,

from last12months 
where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()

";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    $count = 0;
    $hosts = array();
    while ($row = mysql_fetch_assoc( $result)) {

        $host = $row['host'];
        $clientAddr = $row['clientAddr'];
            
        // initialize if necessary
        if (!isset($hosts[$host])) {
            $hosts[$host] = array(
                'count' => 0,
                'clients' => array(
                )
            );
        }
        if (!isset($hosts[$host]['clients'][$clientAddr])) {
            $hosts[$host]['clients'][$clientAddr] = 0;
        }
        
        $hosts[$host]['count']++;
        $hosts[$host]['clients'][$clientAddr]++;

        $count ++;
    }

    echo("$count total rows, ".sizeof($hosts)." unique hosts\n");
    
    echo("writing output\n");
    
    $myfile = fopen("unique-clients-12-mon.csv", "w") or die("Unable to open file!");
    fwrite($myfile,"host,accesses, distinct client count\n");

    foreach ($hosts as $host => $data) {
        $client_count = 0;
        foreach ($data['clients'] as $clientcount) $client_count += $clientcount;
        
        $txt = "";
        $txt .= "$host, ";
        $txt .= $data['count'].', ';            // host count
        $txt .= sizeof($data['clients']).', ';  // distinct client count
        $txt .= "\n";
        fwrite($myfile,$txt);
        
    }
    
    fclose($myfile);

    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    mysql_close( $dbh );

?>

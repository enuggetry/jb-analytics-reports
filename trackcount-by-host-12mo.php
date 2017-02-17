<?php
    include "util.php";
    include "db.php";

    echo "Report - Trackcount by host, last 12 months\n";
    

/*    
 * 
 * Ughhh!  why don't this work???!
                sum(case when trackCount between 101 and 500 then 1 else 0 end) as range2,
                sum(case when trackCount between 501 and 1000 then 1 else 0 end) as range3,
                sum(case when trackCount between 1001 and 2000 then 1 else 0 end) as range4,
                sum(case when trackCount between 2001 and 5000 then 1 else 0 end) as range5,
                sum(case when trackCount between 5001 and 10000 then 1 else 0 end) as range6,
                sum(case when trackCount between 10001 and 15000 then 1 else 0 end) as range7,
                sum(case when trackCount between 15001 and 20000 then 1 else 0 end) as range9,
                sum(case when trackCount >= 20001 then 1 else 0 end) as range10,
                COUNT(case when trackCount between 0 and 100 then trackCount else 0 end) as range1,
                COUNT(case when trackCount between 101 and 200 then trackCount else 0 end) as range2,
                COUNT(case when trackCount between 201 and 500 then trackCount else 0 end) as range3,
                COUNT(case when trackCount between 501 and 1000 then trackCount else 0 end) as range4,
                SUM(trackCount) / COUNT(*) as t_mean, 
                count(*) as t_count
            group by host having t_count > 100
*/
      $myquery = "
 
            select host,trackCount
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()
            order by trackCount DESC

            ";
    $starttime = microtime(true);

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }

    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time query: ".convertElapsedTime($timediff)."\n";
    echo "starting sort to bins\n";

    $count = 0;
    
    // hlist of hosts with bins.bins for tallies
    $hlist = array();
    
    while ($row = mysql_fetch_assoc( $result)) {
            $host = $row['host'];
            
            // init if not exist
            if (!isset($hlist[$host])) {
                $hlist[$host] = array(
                    "-100"=>0,
                    "-200"=>0,
                    "-500"=>0,
                    "-1000"=>0,
                    "-2000"=>0,
                    "-5000"=>0,
                    "-10000"=>0,
                    "-20000"=>0,
                    "+20000"=>0,
                    "hits"=>0
                );
            }

            $hlist[$host]['hits'] += 1;
            
            $tcount = $row['trackCount'];
            if      ($tcount <= 100)    { $hlist[$host]['-100'] += 1; }
            else if ($tcount <= 200)    { $hlist[$host]['-200'] += 1; }
            else if ($tcount <= 500)    { $hlist[$host]['-500'] += 1; }
            else if ($tcount <= 1000)   { $hlist[$host]['-1000'] += 1; }
            else if ($tcount <= 2000)   { $hlist[$host]['-2000'] += 1; }
            else if ($tcount <= 5000)   { $hlist[$host]['-5000'] += 1; }
            else if ($tcount <= 10000)  { $hlist[$host]['-10000'] += 1; }
            else if ($tcount <= 20000)  { $hlist[$host]['-20000'] += 1; }
            else if ($tcount > 20000)   { $hlist[$host]['+20000'] += 1; }

            $count++;
    }
    echo("$count total rows, "+sizeof($hlist)+" host count\n");
    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    
    /*
     * write the file
     */
    echo "outputting...\n";
    
    
    $myfile = fopen("trackcount-by-host-12mon.csv", "w") or die("Unable to open file!");
    fwrite($myfile,"trackCount separated into bins (last 12 months, > 100 hits)\n");
    fwrite($myfile,"host,"
            . "'0-100,"
            . "'100-200,"
            . "'200-500,"
            . "'500-1000,"
            . "'1000-2000,"
            . "'2000-5000,"
            . "'5000-10000,"
            . "'10000-20000,"
            . "'>20000,"
            . "trackCount,"
            . "hits\n");    // headers
    
    foreach ($hlist as $host => $bins) {
        
        //echo "host = $host\n";
        
        if ($hlist[$host]['hits'] < 100) continue;      // ignore hosts with less than 100 hits
        
        $txt = "";
        $txt .= "$host,";
        foreach ($bins as $bin => $value) {
            $txt .= "$value,";
        }
        $txt .= "\n";
        
        fwrite($myfile, $txt);
    }

    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    fclose($myfile);
    mysql_close( $dbh );

?>

<?php
    include "util.php";
    include "db.php";

    echo "Report - Plugins by host, last 12 months\n";
    

    $myquery = "
 
            select host,plugins
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) BETWEEN NOW() - INTERVAL 365 DAY AND NOW()

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
    $hlist = array(
            'TOTAL'=>array('hits'=>0)
        );
    
    while ($row = mysql_fetch_assoc( $result)) {
            $host = $row['host'];
            
            // init if not exist
            if (!isset($hlist[$host])) {
                $hlist[$host] = array(
                    "hits"=>0
                );
            }
            $hlist[$host]['hits'] += 1;
            $hlist['TOTAL']['hits'] += 1;
            
            $plugins = $row['plugins'];
            $plist = split(',',$plugins);
            
            foreach ($plist as $plugin) {
                if (!isset($hlist[$host][$plugin])) {
                    $hlist[$host][$plugin] = 0;
                }
                if (!isset($hlist['TOTAL'][$plugin])) {
                    $hlist['TOTAL'][$plugin] = 0;
                }
                $hlist[$host][$plugin] += 1;
                $hlist['TOTAL'][$plugin] += 1;
            }
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
    
    
    $myfile = fopen("plugins-by-host-12mon.csv", "w") or die("Unable to open file!");
    fwrite($myfile,"plugis separated into bins (last 12 months, > 100 hits)\n");
    
    fwrite($myfile,"host,");
    foreach($hlist['TOTAL'] as $plugin => $value) {
        fwrite($myfile,"$plugin,");
    }
    fwrite($myfile,"\n");

    foreach($hlist['TOTAL'] as $plugin => $value) {
        if (isset($hlist['TOTAL']))
        if ($hlist['TOTAL'] < 100) continue;
        
    }
    
    foreach ($hlist as $host => $plugins) {
        
        if ($host=='TOTAL') continue;
        if ($hlist['TOTAL']['hits'] < 100) continue;

        $txt = "$host,";
        foreach($hlist['TOTAL'] as $plugin => $value) {
            if (isset($hlist[$host][$plugin]))
                $txt .= $hlist[$host][$plugin].", ";
            else
                $txt .= ", ";
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

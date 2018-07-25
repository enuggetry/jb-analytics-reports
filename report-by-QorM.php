<?php
/*
 * Monthy or Quarterly report of the following:
 * accesses,unique hosts, unique clients,tracks-mean, tracks-hi, tracks-mode,plugins-mean, plugins-hi, plugins-mode
 */
    ini_set('memory_limit', '2048M');
    include "db.php";
    include "util.php";

    echo "Report - Reports by: ";
    
    $starttime = microtime(true);

    $orderby = "QUARTER";
    $prefix = 'Q';
    
    if (isset($argv[1])) {
        $p = strtoupper($argv[1]);
        if ($p == 'QUARTER') {
            $orderby = 'QUARTER';
            $prefix = 'Q';
        }
        elseif ($p == 'MONTH') {
            $orderby = 'MONTH';
            $prefix = '';
        }
		echo $orderby."\n";
    }
    
    $myquery = "
            select 
                host,plugins,trackCount,clientAddr,plugins, 
                CONCAT(YEAR(FROM_UNIXTIME(reportTime)),'-".$prefix."', LPAD(".$orderby."(FROM_UNIXTIME(reportTime)),2,'0')) as t_month
            from jbrowse_client_log 
            where FROM_UNIXTIME(reportTime) >= '2000' and FROM_UNIXTIME(reportTime) < NOW()
            order by t_month DESC
            
    ";

    $result = mysql_query( $myquery );
    if (!$result) {
            die('Invalid query: ' . mysql_error());
    }
    echo "query complete\n";

    $myfile = fopen("reports-by-".$orderby.".csv", "w") or die("Unable to open file!");
    fwrite($myfile,$orderby.",accesses,unique hosts, unique clients,tracks-mean, tracks-hi, tracks-mode,plugins-mean, plugins-hi, plugins-mode\n");
    
    $count = 0;
    
    $months = array();
    $last_month = "";
    
    while ($row = mysql_fetch_assoc( $result)) {
        
      
        $month = $row['t_month'];
        
        // we process per month and then toss months data, otherwise we'd run out of memory.
        if ($last_month != $month) {
            if ($last_month != "") {
                echo "month $last_month completed\n";
                
                CSV_out($months,$myfile);
                $months = array();
            }
            $last_month = $month;
        }
        
        
        $host = $row['host'];
        $trackCount = $row['trackCount'];
        $clientAddr = $row['clientAddr'];
        $plugins = $row['plugins'];
        $plist = split(',',$plugins);

        
        // init if necessary
        if (!isset($months[$month])) {
            $months[$month] = array (
                'hit-count' => 0,
                'hosts' => array(
                    'list' => array()           // sizeof() = unique hosts
                ),
                'tracks' => array(
                    'count' => 0,
                    'hi' => $trackCount,
                    'lo' => $trackCount,
                    'for-mode' => array()
                ),
                'clients' => array (
                    'list' => array()           // sizeof() = unique clients
                ),
                'plugins' => array (
                    'list' => array(),           // sizeof() = unique plugins
                    'hi' => sizeof($plist),
                    'lo' => sizeof($plist),
                    'for-mode' => array()
                ),
            );
        }
        if (!isset($months[$month]['hosts']['list'][$host])) {
            $months[$month]['hosts']['list'][$host] = 0;
        }
        if (!isset($months[$month]['clients']['list'][$clientAddr])) {
            $months[$month]['clients']['list'][$clientAddr] = 0;
        }
        // hit count or number of accesses
        $months[$month]['hit-count']++;
        
        // hosts
        $months[$month]['hosts']['list'][$host]++;
        
        // track stuff
        if (!isset($months[$month]['tracks']['for-mode'][$trackCount])) $months[$month]['tracks']['for-mode'][$trackCount] = 0; 
        $months[$month]['tracks']['for-mode'][$trackCount]++;
        
        if ($trackCount > $months[$month]['tracks']['hi']) $months[$month]['tracks']['hi'] = $trackCount ;
        if ($trackCount < $months[$month]['tracks']['lo']) $months[$month]['tracks']['lo'] = $trackCount ;

        $months[$month]['tracks']['count'] += $trackCount;

        // clients
        $months[$month]['clients']['list'][$clientAddr]++;

        // plugins
        if (!isset($months[$month]['plugins']['for-mode'][sizeof($plist)])) $months[$month]['plugins']['for-mode'][sizeof($plist)] = 0; 
        $months[$month]['plugins']['for-mode'][sizeof($plist)]++;

        if (sizeof($plist) > $months[$month]['plugins']['hi']) $months[$month]['plugins']['hi'] = sizeof($plist) ;
        if (sizeof($plist) < $months[$month]['plugins']['lo']) $months[$month]['plugins']['lo'] = sizeof($plist) ;

        foreach ($plist as $plugin) {
            if (!isset($months[$month]['plugins']['list'][$plugin])) {
                $months[$month]['plugins']['list'][$plugin] = 0;
            }
            if (!isset($months[$month]['plugins']['count'])) {
                $months[$month]['plugins']['count'] = 0;
            }
            $months[$month]['plugins']['count'] ++;
            $months[$month]['plugins']['list'][$plugin] ++;
        }
        
        $count++;
    }
    echo("$count total rows\n");

/*
 * todo: combine downloads into this report
 */
    
    
/*
 * close up 
 */    
    fclose($myfile);
    
    $endtime = microtime(true);
    $timediff = $endtime - $starttime;
    echo "elapsed time: ".convertElapsedTime($timediff)."\n";

    mysql_close( $dbh );

/*
 * output one row
 */    
function CSV_out($months,$myfile) {
    
    foreach($months as $month => $d) {
        
        // figure track statistical mode
        $trackmode = 0; $maxtrack = 0; 
        foreach ($d['tracks']['for-mode'] as $trackCount => $val) {
            if ($val > $maxtrack) {
                $maxtrack = $val;
                $trackmode = $trackCount;
            }
        }
        // figure plugins statistical mode
        $pluginmode = 0; $maxplugin = 0; 
        unset($d['plugins']['for-mode'][1]);    // remove 1 list b/c it is by default always there.
        
        foreach ($d['plugins']['for-mode'] as $pluginCount => $val) {
            if ($val > $maxplugin) {
                $maxplugin = $val;
                $pluginmode = $pluginCount;
            }
        }

        //todo: we could also get the most frequently occuring host.
        
        $txt = "";
        $txt .= $month.', ';
        $txt .= $d['hit-count'].', ';
        $txt .= sizeof($d['hosts']['list']).', ';
        $txt .= sizeof($d['clients']['list']).', ';
        $txt .= $d['tracks']['count'] / $d['hit-count'].', ';
        $txt .= $d['tracks']['hi'].', ';
        $txt .= $trackmode.', ';
        $txt .= $d['plugins']['count'] / $d['hit-count'].', ';
        $txt .= $d['plugins']['hi'].', ';
        $txt .= $pluginmode.', ';
        $txt .= "\n";

        fwrite($myfile, $txt);
    }
}

?>

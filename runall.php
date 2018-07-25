<?php

// by month/quarter
run_exec('php report-by-QorM.php MONTH');         
run_exec('php report-by-QorM.php QUARTER');         
run_exec('php downloads-by-month.php');

// last 12 months 
run_exec('php accesses-by-host-12mon.php');         
run_exec('php top-hosts-last-12-mon.php');
run_exec('php unique-clients-12-mon.php');
run_exec('php trackcount-by-host-12mo.php');
run_exec('php trackcount-counts-12-mon.php');
run_exec('php mean-tracks-12-mon.php');
run_exec('php plugins-by-host-12mo.php');

// other
run_exec('php accesses-by-host-all.php');

function run_exec($cmd) {
    
    echo $cmd."\n";
    
    exec($cmd,$results);
    
    foreach ($results as $key => $val) {
       echo $val."\n";
    }    
};

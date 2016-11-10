<?php


run_exec('php accesses-by-host-12mon-apollo.php');
run_exec('php accesses-by-host-12mon.php');         
run_exec('php downloads-by-month.php');
run_exec('php accesses-by-host-all.php');
run_exec('php get-last-few.php');
run_exec('php accesses-by-month-apollo.php');
run_exec('php mean-tracks-12-mon.php');
run_exec('php accesses-by-month.php');
run_exec('php top-hosts-last-12-mon.php');
run_exec('php unique-clients-12-mon.php');
run_exec('php active-hosts-by-month.php');

function run_exec($cmd) {
    
    echo $cmd."\n";
    
    exec($cmd,$results);
    
    foreach ($results as $key => $val) {
       echo $val."\n";
    }    
};

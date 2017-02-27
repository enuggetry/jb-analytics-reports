all: monthly yearly other

monthly:
	php report-by-QorM.php MONTH
	php report-by-QorM.php QUARTER
	php downloads-by-month.php

yearly:
	php accesses-by-host-12mon.php
	php top-hosts-last-12-mon.php
	php unique-clients-12-mon.php
	php trackcount-by-host-12mo.php
	php trackcount-counts-12-mon.php
	php mean-tracks-12-mon.php
	php plugins-by-host-12mo.php

other:
	php accesses-by-host-all.php

charts:
	Rscript charts.R

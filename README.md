# JBrowse Analytics Reports

![](archive-reports-2017-02-22/Accesses by Month.png)
![](archive-reports-2017-02-22/Plugins per Month.png)
![](archive-reports-2017-02-22/Tracks per Month.png)
![](archive-reports-2017-02-22/Unique Clients per Month.png)
![](archive-reports-2017-02-22/Unique Hosts per Month.png)

## Fetching Data

Assuming you have configured db.php according to db.sample.php:

```
make
```

## Building Charts

First, update date in charts.R and then run:

```
make charts
```

or

```
Rscript charts.R
```

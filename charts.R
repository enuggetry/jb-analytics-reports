library(ggplot2)
library(reshape2)


# Define some functions, one is a monthly plot type
monthlyPlot <- function(data, data_col, palette="Set2", title="Unknown", y_lab="Unknown", output_directory="."){
	# First, we subset the data by filtering the variable column for data_col
	data = data[data$variable==data_col,]
	# Now we can plot
	filename = paste(output_directory, paste(title, 'png', sep="."), sep="/")

	g = ggplot(data, aes_string(x="date", y="value")) +
		geom_bar(stat='identity') +
		theme(axis.text.x = element_text(angle = -90, hjust = 1)) +
		scale_fill_brewer(palette=palette) +
		labs(title=title, x="Date", y=y_lab)

	ggsave(filename, plot=g, width=5, height=4)
}

# The other plots multiple series together.
monthlyPlotMulti <- function(data, data_cols, palette="Set2", title="Unknown", y_lab="Unknown", log=FALSE, output_directory="."){
	# First, we subset the data by filtering the variable column for data_col
	data = data[data$variable %in% data_cols,]
	if(log){
		data$value = log10(data$value)
	}
	# Now we can plot
	filename = paste(output_directory, paste(title, 'png', sep="."), sep="/")

	g = ggplot(data, aes_string(x="date", y="value", colour="variable")) +
		geom_point(stat='identity') +
		theme(axis.text.x = element_text(angle = -90, hjust = 1)) +
		scale_fill_brewer(palette=palette) +
		labs(title=title, x="Date", y=y_lab)

	ggsave(filename, plot=g, width=5, height=4)
}

directory = "archive-reports-2017-02-22"
# Read in the latest reports file.
reportsByMonth = read.csv(paste(directory, "reports-by-month.csv", sep="/"))
# R cannot handle year-month dates, so we append a 1 to the end and parse a y-m-d
reportsByMonth$date = as.Date(paste(reportsByMonth$month, "1"), "%Y-%m %d")
# We re-shape the data into a better format for R to handle.
moltenReportsByMonth = melt(reportsByMonth, "date")
moltenReportsByMonth$value = as.numeric(moltenReportsByMonth$value)
# And plot a specific column
monthlyPlot(moltenReportsByMonth, "accesses", title="Accesses by Month", y_lab="Users", output_dir=directory)
monthlyPlot(moltenReportsByMonth, "unique.hosts", title="Unique Hosts per Month", y_lab="Hosts", output_dir=directory)
monthlyPlot(moltenReportsByMonth, "unique.clients", title="Unique Clients per Month", y_lab="Clients", output_dir=directory)
# Now some multi-plots
monthlyPlotMulti(moltenReportsByMonth, c("tracks.mean", "tracks.mode", "tracks.hi"), title="Tracks per Month", y_lab="log10(Tracks)", log=TRUE, output_dir=directory)
monthlyPlotMulti(moltenReportsByMonth, c("plugins.mean", "plugins.mode", "plugins.hi"), title="Plugins per Month", y_lab="log10(Plugins)", log=FALSE, output_dir=directory)

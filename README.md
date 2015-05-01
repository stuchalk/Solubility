# NIST Solubility Data

This is a CakePHP REST style website developed to show how to take existing chemical property data and re-publish it so that it can be more easily searched and the downloaded via an API.  Data from the NIST-IUPAC Solubility database (link below) were scraped form the website using PHP scripts and ingested into a MySQL database.

You can download this project, put it up on a server (Apache and PHP) and load the data in solubility.sql.zip into a MySQL database to get your own working copy. Be sure to change the base URL of the website in the sol.php config file and turn on mod_rewrite and modify the database.php config file to authenticate to MySQL.

## Links
- Access a working version of this project at [here](http://chalk.coas.unf.edu/solubility)
- Go to the orgiinal source of the data [available from NIST](http://srdata.nist.gov/solubility/)
- Access the paper published on this site (coming soon...)


> Stuart Chalk

> Department of Chemistry

> University of North Florida

> Jacksonville, FL 32224 USA

> Email: schalk@unf.edu
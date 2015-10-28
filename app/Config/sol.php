<?php

// The base URL for the REST based website
$config['host']['base']="https://chalk.coas.unf.edu/solubility/";

// The base URL of the NIST Solubility website
$config['url']['base']="http://srdata.nist.gov/solubility/";

// Generic URLs pointing to different parts of the NIST Solubility website
$config['url']['systype']=$config['url']['base']."sol_sys.aspx?nm_dataSeries=*volID*";
$config['url']['system']['list']=$config['url']['base']."sol_sys_lst.aspx?sysID=*systypeID*&FROM=SSN";
$config['url']['system']['detail']=$config['url']['base']."sol_detail.aspx?sysID=*sysID*";
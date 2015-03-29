<?php

$config['host']['base']="https://eureka.coas.unf.edu/solubility/";
$config['url']['base']="http://srdata.nist.gov/solubility/";
$config['url']['systype']=$config['url']['base']."sol_sys.aspx?nm_dataSeries=*volID*";
//$config['url']['system']['list']=$config['url']['base']."sol_sys_lst.aspx?sysID=*systypeID*&SerialID=*volID*"; Not used due to js page access
$config['url']['system']['list']=$config['url']['base']."sol_sys_lst.aspx?sysID=*systypeID*&FROM=SSN";
$config['url']['system']['detail']=$config['url']['base']."sol_detail.aspx?sysID=*sysID*";
?>
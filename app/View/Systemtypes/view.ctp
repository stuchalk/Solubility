<?php
$systems=$data['System'];
$type=$data['Systemtype'];
?>
<h2><?php echo $type['title']; ?></h2>
<h3>Data About This System</h3>
<?php
echo "<ul style='list-style-type: square;font-size: 12px;'>";
foreach($systems as $system) {
	echo "<li>".$this->Html->link($system['title'],'/systems/view/'.$system['sysID']);
	echo "<div class='abstract' style='color: gray;width: 900px'>".substr($system['method'],0,100)."... (".$system['preparer'].")</div>";
	echo "</li>";
}
echo "</ul>";
?>
<p>&nbsp;<br />
	<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/sol_sys_lst.aspx?sysID='.$system['sysID'].'&FROM=SSN',['target'=>'_blank']);?>
</p>
<?php $volume=$data['Volume'];$systems=$data['System']; ?>
<h2><?php echo $volume['title']; ?></h2>
<?php
if($volume['url']!=""): echo "<p>".$this->Html->link($volume['reference'],$volume['url'],array('target'=>'_blank'))."</p>";
else:					echo "<p>".$volume['reference']."</p>";
endif;
?>
<h3>System Types (Click name for systems)</h3>
<?php
//pr($systems);
$grouped=array();
foreach($systems as $system)
{
	if(isset($grouped[$system['systemtype_id']])):	$grouped[$system['systemtype_id']][]=$system;
	else:											$grouped[$system['systemtype_id']][0]=$system;
	endif;
}
?>
<div id="left" style="float: left;width: 450px;">

<?php
//pr($grouped);
echo "<ul style='font-size: 12px;'>";
foreach($grouped as $id=>$group)
{
	$type=$this->requestAction('/systemtypes/view/'.$id);
	//pr($type);
	echo "<li onclick=\"copydiv('left".$id."','right')\" style='cursor: pointer;'>".$type['Systemtype']['title']."</li>";
	echo "<div id='left".$id."' style='display: none;'>";
	echo "<ul style='list-style-type: square;font-size: 12px;'>";
	foreach($group as $sys)
	{
		echo "<li>".$this->Html->link($sys['title'],'/systems/view/'.$sys['sysID'])."</li>";
	}
	echo "</ul>";
	echo "</div>";
}
echo "</ul>";
?>
</div>
<div id="right"></div>
<div class="floatreset"></div>
<br />
<p><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/sol_sys.aspx?nm_dataSeries='.$volume['nistid'],array('target'=>'_blank'));?></p>
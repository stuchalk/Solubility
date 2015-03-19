<h2>Volumes</h2>
<?php //pr($data); ?>
<?php //pr($formula); ?>
<?php
echo "<div>";
echo "<ul>";
foreach($data as $volume)
{
	$vol=$volume['Volume'];
	echo '<li>'.html_entity_decode($this->Html->link($vol['title'],'/volumes/view/'.$vol['vol'])).'</li>';
}
echo "</ul></div>";
?>
<p>&nbsp;<br /><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/dataSeries.aspx',array('target'=>'_blank'));?></p>
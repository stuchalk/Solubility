<?php
$author=$data['Author'];
if(isset($data['Citation']))	{ $citations=$data['Citation']; }
?>
<h2><?php echo $author['name']; ?></h2>
<?php
echo "<ul style='font-size: 12px;'>";
foreach($citations as $citation)
{
	echo '<li>'.html_entity_decode($this->Html->link($citation['journal'],'/citations/view/'.$citation['id'])).'</li>';
}
echo "</ul></div>";
?>
<p>&nbsp;<br />
<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/citation_detail.aspx?REF_NO='.$citation['refno'],array('target'=>'_blank'));?></p>
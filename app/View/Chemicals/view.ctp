<?php
if(!is_array($data))
{
	echo "<h3>".$data."</h3>";
}
else
{
	$chemical=$data['Chemical'];
	$tables=array();
	if(isset($data['System']))
	{
		$systems=$data['System'];
		foreach($systems as $system)
		{
			if(isset($system['Table']))
			{
				foreach($system['Table'] as $table) { $tables[]=$table; }
			}
		}
	}
	?>
	<h2><?php echo $chemical['name']; ?></h2>
	<?php
	if(isset($chemical['formula']))		{ echo "<h4>Chemical Formula: ".$chemical['formula']."</h4>"; }
	if(isset($chemical['casrn']))		{ echo "<h4>CAS Registry Number: ".$chemical['casrn']."</h4>"; }
	if(isset($chemical['inchi']))		{ echo "<h4>InChI String: ".$chemical['inchi']."</h4>"; }
	echo "<div id='chemical' class='chemical' style='float: left'>";
	echo "<script type='text/javascript'>\n";
	echo "  var Info = { color: '#000000', height: 290, width: 300, use: 'HTML5', defaultModel: '$".$chemical['name']."', j2sPath: '/sol/js/jsmol/j2s' };\n";
	echo "  Jmol.getTMApplet('chem', Info);\n";
	echo "</script>\n";
	echo "</div>";
	echo "<div class='floatreset'></div>";
	echo "<p style='margin-top: 10px;'>View this molecule @ ";
	echo $this->Html->link('NIST','http://webbook.nist.gov/cgi/cbook.cgi?ID='.$chemical['casrn'],array('target'=>'_blank')).", ";
	echo $this->Html->link('ChemSpider','http://www.chemspider.com/Search.aspx?q='.$chemical['inchi'],array('target'=>'_blank'));
	echo "</p>";
	// Systems
	if(isset($systems))
	{
		echo "<div style='margin-bottom: 10px;'>";
		echo "<h3>Solubility Data Sources</h3>\n";
		echo '<ul>';
		foreach($systems as $system)
		{
			echo "<li>".$this->Html->link($system['title'],'/systems/view/'.$system['sysID'])."</li>";
		}
		echo "</ul></div>";
	}
	// Tables
	if(!empty($tables)) { echo $this->element('tables',array('tables'=>$tables,'links'=>true)); }
	// Download
	echo "<h3>Download This Data As</h3>\n";
	echo "<p>";
	echo $this->Html->link($this->Html->image('xml.png',array('height'=>'50','alt'=>'XML')),'https://chalk.coas.unf.edu/sol/chemicals/id/'.$chemical['id'].'/xml',array('escape'=>false,'target'=>'_blank'))."&nbsp;&nbsp;&nbsp;";
	echo $this->Html->link($this->Html->image('json.png',array('height'=>'50','alt'=>'JSON')),'https://chalk.coas.unf.edu/sol/chemicals/id/'.$chemical['id'].'/json',array('escape'=>false,'target'=>'_blank'))."&nbsp;&nbsp;&nbsp;";
	echo $this->Html->link($this->Html->image('jsonld.png',array('height'=>'50','alt'=>'JSON-LD')),'https://chalk.coas.unf.edu/sol/chemicals/id/'.$chemical['id'].'/jsonld',array('escape'=>false,'target'=>'_blank'));
	echo "</p>";
	?>
	<p><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/casNO.aspx',array('target'=>'_blank'));?></p>
<?php
}
?>
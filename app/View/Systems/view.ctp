<?php
//pr($data);
$system=$data['System'];
if(isset($data['Citation']))	{ $citation=$data['Citation']; }
if(isset($data['Variable']))	{ $variables=$data['Variable']; }
if(isset($data['Table']))		{ $tables=$data['Table']; }
if(isset($data['Chemical']))	{ $chemicals=$data['Chemical']; }
?>
<h2><?php echo $system['title']; ?></h2>
<?php
// Citation
if(isset($citation)&&($system['citation_id']!='00000'))
{
	echo "<div id='citation'>\n";
		echo "<div style='float: left;'><h4>Original Citation:&nbsp;</h4></div>\n";
		echo "<div style='float: left;'>";
			if(isset($citation['title'])) { echo "<h4 style='margin-bottom: 0px;'>".$citation['title']."</h4>\n"; }
			$authors=$data['Author'];$bib="";
			foreach($authors as $au) { $bib.=$au['lastname'].", ".$au['firstname']."; "; }
			$bib.=$citation['journal']." ".$citation['year'].", ".$citation['volume'];
			($citation['issue']!="") ? $bib.="(".$citation['issue']."), " : $bib.=", ";
			$bib.=$citation['firstpage'];
			($citation['lastpage']!="") ? $bib.="-".$citation['lastpage']."." : $bib.=".";
			if($citation['url']!=""):	echo "<h4>".$this->Html->link($bib,$citation['url'],array('target'=>'_blank'))."</h4>";
			else:						echo "<h4>".$bib."</h4>";
			endif;
		echo "</div>";
	echo "<div class='floatreset'></div></div>";
}
if($system['preparer']!="") { echo "<h4>Prepared by: ".$system['preparer']."</h4>\n"; }
if($system['remarks']!="") { echo "<h4 style='width: 900px;'>Remarks: ".$system['remarks']."</h4>"; }
// Variables
echo "<div id='varschems' style='width: 900px;margin-bottom: 15px;margin-top: 10px;'>\n";
if(isset($variables))
{
	echo "<div id='variables' class='variables'>";
	echo "<h3>Variables</h3>\n";
	if(!empty($variables))
	{
		foreach($variables as $var)
		{
			echo "<p>".$var['name']." = ".$var['bounds']."</p>";
		}
	}
	else
	{
		echo "<p>"."None"."</p>";
	}
	echo "</div>";
}
// Chemicals
if(isset($chemicals))
{
	echo "<div id='chemicals'>";
	echo "<h3 style='text-align: right;'>Chemicals</h3>\n";
	for($x=0;$x<count($chemicals);$x++)
	{
		$chem=$chemicals[$x];
		echo "<div id='chemical".$x."' class='chemical'>";
		//echo $this->Html->image('http://cactus.nci.nih.gov/chemical/structure/'.$chem['inchi'].'/image?format=png&linewidth=2',array('alt'=>$chem['name']));
		echo "<script type='text/javascript'>\n";
		echo "  var Info".$x." = { color: '#000000', height: 190, width: 200, use: 'HTML5', defaultModel: '$".$chem['name']."', j2sPath: '/sol/js/jsmol/j2s' };\n";
		echo "  Jmol.getTMApplet('chem".$x."', Info".$x.");\n";
		echo "</script>\n";
		echo "<p>".$chem['name']."<br />\n";
		echo "View @ ";
		echo $this->Html->link('NIST','http://webbook.nist.gov/cgi/cbook.cgi?ID='.$chem['casrn'],array('target'=>'_blank')).", ";
		echo $this->Html->link('ChemSpider','http://www.chemspider.com/Search.aspx?q='.$chem['inchi'],array('target'=>'_blank'));
		echo "</p>";
		echo "</div>";
	}
	echo "<div class='floatreset'></div></div>";
}
echo "<div class='floatreset'></div></div>";
// Data analysis (text)
if($system['data']!="") { echo "<h3>Data Analysis</h3><p style='width: 900px;font-size: 12px;text-align: justify;'>".$system['data']."</p>"; }
// Tables
echo $this->element('tables',array('tables'=>$tables));
// Data table notes
if($system['datanotes']!="")
{
	$notes=json_decode($system['datanotes']);
	for($x=0;$x<count($notes);$x++)
	{
		$letter=$notes[$x][0];
		$notes[$x]="(".$letter.") ".substr($notes[$x],1);
	}
	echo "<p id='note'>Notes: ".implode(", ",$notes)."</p>";
}
// Method
if($system['method']!="")
{
	echo "<h3>Method/Apparatus/Procedure</h3>\n";
	echo "<p style='width: 900px'>".$system['method']."</p>";
}
// Sources
if($system['source']!="")
{
	$sources=json_decode($system['source']);
	echo "<h3>Source and Purity of Materials</h3>\n";
	echo "<p style='width: 900px;'>";
	for($x=0;$x<count($sources);$x++)
	{
		echo "(".($x+1).") ".$sources[$x]."<br />";
	}
	echo "</p>";
}
// Errors
if($system['errors']!="")
{
	echo "<h3>Errors</h3>\n";
	echo "<p style='width: 900px'>".$system['errors']."</p>";
}
// References
if($system['refs']!="")
{
	$refs=json_decode($system['refs']);
	echo "<h3>References</h3>\n";
	echo "<p style='width: 900px'>";
	for($x=0;$x<count($refs);$x++)
	{
		echo "(".($x+1).") ".trim(substr($refs[$x],1))."<br />";
	}
	echo "</p>";
}
?>
<?php //pr($system); ?>
<p><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database','http://srdata.nist.gov/solubility/sol_detail.aspx?sysID='.$system['sysID'],array('target'=>'_blank'));?></p>
<?php
$system=$data['System'];
if(isset($data['Citation']))	{ $citation=$data['Citation']; }
if(isset($data['Variable']))	{ $variables=$data['Variable']; }
if(isset($data['Table']))		{ $tables=$data['Table']; }
if(isset($data['Chemical']))	{ $chemicals=$data['Chemical']; }
?>
<h2><?php echo $system['title']; ?></h2>
<?php
echo "<div id='varschems' style='width: 900px;margin-bottom: 15px;margin-top: 10px;'>\n";

// Citation
if(isset($citation)&&($system['citation_id']!='00000')) {
	echo "<div id='citation'>\n";
		echo "<div style='float: left;'><h4>Original Citation:&nbsp;</h4></div>\n";
		echo "<div style='float: left;'>";
			if(isset($citation['title'])) { echo "<h4 style='margin-bottom: 0;'>".$citation['title']."</h4>\n"; }
			$authors=$data['Author'];$bib="";
			foreach($authors as $au) { $bib.=$au['lastname'].", ".$au['firstname']."; "; }
			$bib.=$citation['journal']." ".$citation['year'].", ".$citation['volume'];
			($citation['issue']!="") ? $bib.="(".$citation['issue']."), " : $bib.=", ";
			$bib.=$citation['firstpage'];
			($citation['lastpage']!="") ? $bib.="-".$citation['lastpage']."." : $bib.=".";
			if($citation['url']!=""):	echo "<h4>".$this->Html->link($bib,$citation['url'],['target'=>'_blank'])."</h4>";
			else:						echo "<h4>".$bib."</h4>";
			endif;
		echo "</div>";
	echo "<div class='floatreset'></div></div>";
}
// Preparer
if($system['preparer']!="") {
	echo "<h4>Prepared by: ".$system['preparer']."</h4>\n";
}

// Remarks
if($system['remarks']!="") {
	echo "<h4 style='width: 900px;'>Remarks: ".$system['remarks']."</h4>";
}

// Variables
if(isset($variables)) {
	echo "<div id='variables' class='variables' style='width: 290px;'>";
	echo "<h3>Variables</h3>\n";
	if(!empty($variables)) {
        $dups=[];
		foreach($variables as $var) {
            $unique=$var['name'].$var['bounds'];
            if(!in_array($unique,$dups)) {
                echo "<p>".$var['name']." = ".$var['bounds']."</p>";
                $dups[]=$unique;
            }
		}
	} else {
		echo "<p>"."None"."</p>";
	}
	echo "</div>";
}

// Chemicals
if(isset($chemicals)) {
	echo "<div id='chemicals'>";
	echo "<h3 style='text-align: right;'>Chemicals</h3>\n";
	for($x=0;$x<count($chemicals);$x++) {
		$chem=$chemicals[$x];
		if($chem['inchi']==''&&$chem['inchikey']==''&&($chem['casrn']==''||stristr($chem['casrn'],","))) { continue; }
		if($chem['inchikey']!='') {
			$id=$chem['inchikey'];
		} elseif($chem['inchi']!='') {
			$id=$chem['inchi'];
		} else {
			$id=$chem['casrn'];
		}
		echo "<div id='chemical".$x."' class='chemical'>";
		//echo $this->Html->image('http://cactus.nci.nih.gov/chemical/structure/'.$chem['inchi'].'/image?format=png&linewidth=2',array('alt'=>$chem['name']));
		echo "<script type='text/javascript'>\n";
		$url="https://chalk.coas.unf.edu/sol/admin/proxy?url=http://cactus.nci.nih.gov/chemical/structure/".$id."/file?format=sdf&get3d=True";
		echo "  var Info".$x." = { color: '#000000', height: 190, width: 190, src: '".$url."', use: 'HTML5', j2sPath: '/sol/js/jsmol/j2s' };\n";
		echo "  Jmol.getTMApplet('chem".$x."', Info".$x.");\n";
		echo "</script>\n";
		list($name,)=explode(" (",$chem['name']); // remove stuff in parens
		echo "<p>".$name."<br />\n";
		echo "View @ ";
		echo $this->Html->link('NIST','http://webbook.nist.gov/cgi/cbook.cgi?ID='.$chem['casrn'],['target'=>'_blank']).", ";
		echo $this->Html->link('ChemSpider','http://www.chemspider.com/Search.aspx?q='.$chem['inchi'],['target'=>'_blank']);
		echo "</p>";
		echo "</div>";
	}
	echo "<div class='floatreset'></div></div>";
}
echo "<div class='floatreset'></div></div>";

// Data analysis (text)
if($system['data']!="") {
	echo "<h3>Data Analysis</h3><p style='width: 900px;font-size: 12px;text-align: justify;'>".$system['data']."</p>";
}

// Tables
if(!empty($tables)) {
	echo $this->element('tables',['tables'=>$tables]);
}

// Data table notes
if($system['datanotes']!="") {
	$notes=json_decode($system['datanotes']);
	for($x=0;$x<count($notes);$x++) {
		$letter=$notes[$x][0];
		$notes[$x]="(".$letter.") ".substr($notes[$x],1);
	}
	echo "<p id='note'>Notes: ".implode(", ",$notes)."</p>";
}

// Method
if($system['method']!="") {
	echo "<h3>Method/Apparatus/Procedure</h3>\n";
	echo "<p style='width: 900px;'>".$system['method']."</p>";
}

// Sources
if($system['source']!="[]") {
	$sources=json_decode($system['source']);
	echo "<h3>Source and Purity of Materials</h3>\n";
	echo "<p style='width: 900px;'>";
	for($x=0;$x<count($sources);$x++) {
		echo "(".($x+1).") ".$sources[$x]."<br />";
	}
	echo "</p>";
}

// Errors
if($system['errors']!="") {
	echo "<h3>Errors</h3>\n";
	echo "<p style='width: 900px;'>".$system['errors']."</p>";
}

// References
if($system['refs']!="") {
	$refs=json_decode($system['refs']);
	echo "<h3>References</h3>\n";
	echo "<p style='width: 900px;'>";
	for($x=0;$x<count($refs);$x++) {
		echo "(".($x+1).") ".trim(substr($refs[$x],1))."<br />";
	}
	echo "</p>";
}
// Download
echo "<h3>Download This Data As</h3>\n";
echo "<p>";
echo $this->Html->link($this->Html->image('xml.png', ['height' => '50', 'alt' => 'XML']), $base . 'systems/view/' . $sysID . '/xml', ['escape' => false, 'target' => '_blank']) . "&nbsp;&nbsp;&nbsp;";
echo $this->Html->link($this->Html->image('json.png', ['height' => '50', 'alt' => 'JSON']), $base . 'systems/view/' . $sysID . '/json', ['escape' => false, 'target' => '_blank']) . "&nbsp;&nbsp;&nbsp;";
echo $this->Html->link($this->Html->image('jsonld.png', ['height' => '50', 'alt' => 'JSON-LD']), $base . 'systems/view/' . $sysID . '/jsonld', ['escape' => false, 'target' => '_blank']) . "&nbsp;&nbsp;&nbsp;";
echo "</p>";
?>
<p><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$nist.'sol_detail.aspx?sysID='.$system['sysID'],['target'=>'_blank']);?></p>
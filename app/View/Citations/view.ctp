<?php
$citation=$data['Citation'];
if(isset($data['System']))		{ $systems=$data['System']; }
if(isset($data['Author']))		{ $authors=$data['Author']; }
?>
<h2><?php echo ($citation['title']!="") ? $citation['title'] : "No Title"; ?></h2>
<?php
// Citation
if(isset($citation)) {
	echo "<div id='citation' style='margin-bottom: 10px;'>\n";
	$bib="";
	if(isset($authors)) {
		foreach($authors as $au) { $bib.=$au['lastname'].", ".$au['firstname']."; "; }
		$bib.=$citation['journal']." ".$citation['year'].", ".$citation['volume'];
		($citation['issue']!="") ? $bib.="(".$citation['issue']."), " : $bib.=", ";
		$bib.=$citation['firstpage'];
		($citation['lastpage']!="") ? $bib.="-".$citation['lastpage']."." : $bib.=".";
		if($citation['url']!=""):	echo "<h4>".$this->Html->link($bib,$citation['url'],['target'=>'_blank'])."</h4>";
		else:						echo "<h4>".$bib."</h4>";
		endif;
	}
	echo "</div>";
}

// Systems
if(isset($systems)) {
	echo "<h3>Solubility Data</h3>\n";
	echo '<ul>';
	foreach($systems as $system) {
		echo "<li>".$this->Html->link($system['title'],'/systems/view/'.$system['sysID'])."</li>";
	}
	echo "</ul>";
}
?>
<p>&nbsp;<br />
<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$nist.'citation.aspx',['target'=>'_blank']);?>
</p>
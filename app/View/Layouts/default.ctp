<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<?php echo $this->Html->charset(); ?>
	<title>NIST Solubility Database: <?php echo $title_for_layout; ?></title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('solubility');
		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery.validate');
		echo $this->Html->script('jqcake');
		//echo $this->Html->script('recline');
		echo $this->Html->script('JSmol.lite.nojq');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>IUPAC-NIST Solubility Data Series @ UNF</h1>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo "Scraped by Stuart Chalk @ ".$this->Html->link("University of North Florida",'http://www.unf.edu/',['target' =>'_blank'])." © 2014-2015"; ?>
		</div>
	</div>
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>

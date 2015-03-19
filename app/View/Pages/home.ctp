<h2>Scraping the IUPAC-NIST Solubility Data Series</h2>
<p>This website was developed as a proof of concept to take an existing website of data (extracted from papers)
 and 'scraping' the pages for the metadata, cleaning it, and putting it in a relational database.  Subsequently,
 users can search the data more effectively and access it via a REST style API</p>
<p>The REST style API has the following formats<p>
<h3>For Volumes</h3>
<ul>
	<li>Index of Volumes: <?php echo $this->Html->link('/volumes','/volumes'); ?></li>
	<li>View a Volume: /volumes/view/&lt;volume#&gt;</li>
</ul>
<h3>For Chemicals</h3>
<ul>
	<li>Index of Chemicals: <?php echo $this->Html->link('/chemicals','/chemicals'); ?></li>
	<li>Find/view a Chemical: 
		<ul>
			<li>Via name /chemicals/name/&lt;chemical_id&gt;/&lt;format&gt;</li>
			<li>Via CAS /chemicals/casrn/&lt;chemical_id&gt;/&lt;format&gt;</li>
			<li>Via formula /chemicals/formula/&lt;chemical_id&gt;/&lt;format&gt;</li>
		</ul>
		(format can be xml, json, or jsonld)
	</li>
</ul>
<h3>For Systems</h3>
<ul>
	<li>Index of Systems: <?php echo $this->Html->link('/systems','/systems'); ?></li>
	<li>View a System: /systems/view/&lt;sysID&gt;</li>
</ul>
<h3>For Citations</h3>
<ul>
	<li>Index of Citations: <?php echo $this->Html->link('/citations','/citations'); ?></li>
	<li>View a Citation: /citations/view/&lt;id&gt;</li>
</ul>
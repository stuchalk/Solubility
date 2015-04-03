<h2>Scraping the IUPAC-NIST Solubility Data Series</h2>
<p>This website was developed as a proof of concept to take an existing website of data -
    <?php $url=Configure::read('url.base');echo $this->Html->link('The NIST Solubility Database',$url,['target'=>'_blank']); ?>
    (extracted from papers)
 and 'data scraping' the pages for the data and metadata, cleaning it, and putting it in a relational database.  Subsequently,
 users (humans and computers) can search the data more effectively and access it via a REST style API as follows</p>
<h3>For Volumes</h3>
<ul>
	<li>Index of Volumes: <?php echo $this->Html->link('/volumes','/volumes'); ?></li>
	<li>View a Volume: /volumes/view/&lt;volume#&gt;<br />&nbsp;</li>
</ul>
<h3>For Chemicals</h3>
<ul>
	<li>Index of Chemicals: <?php echo $this->Html->link('/chemicals','/chemicals'); ?></li>
	<li>Find/view a Chemical: /chemicals/view/&lt;chemicalID&gt;/&lt;format&gt;<br/>
        (chemicalID can be name, CASRN, InChI Key, or formula)<br/>
        (format can be xml, json, or jsonld (none=html))<br />&nbsp;
	</li>
</ul>
<h3>For System Types</h3>
<ul>
    <li>Index of Systems: <?php echo $this->Html->link('/systemtypes','/systemtypes'); ?></li>
    <li>View a System: /systemtypes/view/&lt;systypeID&gt;<br />&nbsp;</li>
</ul>
<h3>For Experimental System Data</h3>
<ul>
	<li>Index of Systems: <?php echo $this->Html->link('/systems','/systems'); ?></li>
	<li>View a System: /systems/view/&lt;sysID&gt;<br />&nbsp;</li>
</ul>
<h3>For Citations</h3>
<ul>
	<li>Index of Citations: <?php echo $this->Html->link('/citations','/citations'); ?></li>
	<li>View a Citation: /citations/view/&lt;id&gt;</li>
</ul>
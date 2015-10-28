<?php $url=Configure::read('url.base'); ?>
<h2>Scraping the IUPAC-NIST Solubility Data Series</h2>
<div style="width: 900px;">
    <p style="text-align: justify;">This website was developed as a proof of concept to take an existing website of data,
        <?php echo $this->Html->link('The NIST Solubility Database',$url,['target'=>'_blank']); ?>
        (extracted from papers), and 'scrape' the pages for the data and metadata, clean it, and put it in a
        relational database (see this project on GitHub).  Subsequently, users (humans and computers) can search the data more effectively
        and access it via a REST style API as follows</p>
    <h3>For Volumes</h3>
    <ul>
        <li>Index of Volumes: <?php echo $this->Html->link('/volumes','/volumes'); ?></li>
        <li>View a Volume: /volumes/view/&lt;volume#&gt;/&lt;format&gt;<br/>
            (format can be xml, json, or jsonld (none=html))<br />&nbsp;</li>
    </ul>
    <h3>For Chemicals</h3>
    <ul>
        <li>Index of Chemicals: <?php echo $this->Html->link('/chemicals','/chemicals'); ?></li>
        <li>View a Chemical: /chemicals/view/&lt;chemicalID&gt;/&lt;format&gt;<br/>
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
    <p style="text-align: justify;">
        &nbsp;<br />NOTE: The focus of this site was getting the data from the web pages and into a database.  Fields of data
        were only cleaned/organized if they were necesary to afford the correct functioning of this site.  Thus, there
        are many fields of data that have not been cleaned.
    </p>
</div>
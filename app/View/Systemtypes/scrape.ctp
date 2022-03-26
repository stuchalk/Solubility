<h2>System Types Found</h2>
<div>
    <ul>
        <?php
        foreach($data as $sys=>$title) {
            echo '<li>'.html_entity_decode($this->Html->link($sys.") ".$title,'/systemtypes/view/'.$sys)).'</li>';
        }
        ?>
    </ul>
</div>
<p>&nbsp;</p>
<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$base.'dataSeries.aspx',['target'=>'_blank']);?>
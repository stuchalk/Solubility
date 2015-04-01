<h2>Volumes Found</h2>
<div>
    <ul>
        <?php
        foreach($data as $vol=>$title) {
            echo '<li>'.html_entity_decode($this->Html->link($title,'/volumes/view/'.$vol)).'</li>';
        }
        ?>
    </ul>
</div>
<p>&nbsp;</p>
<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$base.'dataSeries.aspx',['target'=>'_blank']);?>
<h2>Volumes Found</h2>
<div>
    <ul>
        <?php
        foreach($data as $v) {
            echo '<li>'.$v['volume'].') '.$v['title'].' ('.$v['nistid'].')</li>';
        }
        ?>
    </ul>
</div>
<p>&nbsp;</p>
<?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$base.'dataSeries.aspx',['target'=>'_blank']);?>
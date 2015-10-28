<?php
$volume=$data['Volume'];
$systems=$data['System'];
//pr($systems);
?>

<h2><?php echo $volume['title']; ?></h2>
<div id="left" style="width: 900px;">
<?php
if ($volume['url'] != "") {
    echo "<p>" . $this->Html->link($volume['reference'], $volume['url'], ['target' => '_blank']) . "</p>";
} else {
    echo "<p>" . $volume['reference'] . "</p>";
}
?>

<h3>System Types (Click name to show a list of systems(datasets))</h3>
<?php
$grouped = [];
foreach ($systems as $system) {
    if (isset($grouped[$system['systemtype_id']])): $grouped[$system['systemtype_id']][] = $system;
    else: $grouped[$system['systemtype_id']][0] = $system;
    endif;
}

echo "<ul style='font-size: 12px;'>";
foreach ($grouped as $id => $group) {
    $type = $this->requestAction('/systemtypes/view/' . $id);
    echo "<li onclick=\"multitoggle('st" . $id . "','types')\" style='cursor: pointer;'>" . $type['Systemtype']['title'] . " (" . count($group) . ").</li>";
    echo "<div id='st" . $id . "' class='types' hidden>";
    echo "<ul style='list-style-type: square;font-size: 12px;'>";
    foreach ($group as $sys) {
        if(isset($sys['Citation']['cite'])) {
            echo "<li>".$this->Html->link($sys['title'],'/systems/view/'.$sys['sysID'])." (".$sys['Citation']['cite'].")</li>";
        } else {
            echo "<li>".$this->Html->link($sys['title'],'/systems/view/'.$sys['sysID'])."</li>";
        }
    }
    echo "</ul>";
    echo "</div>";
}
echo "</ul>";
?>
</div>
<p>
    &nbsp;<br /><?php echo "Information obtained from ".$this->Html->link('The NIST Solubility Database',$base.'sol_sys.aspx?nm_dataSeries='.$volume['nistid'],['target'=>'_blank']);?>
</p>
<?php
// Display table data
// Element Variables: $tables, $links
if(!isset($links)) { $links=false; }
if(isset($tables)&&!empty($tables))
{
	echo "<h3>Data Tables</h3>";
	foreach($tables as $table)
	{
		$t=json_decode($table['content']);
        //echo "<p>".$table['content']."</p>";
		//pr($table);pr($t);
		echo "<table>";
		$header=$t[0];
		echo "<tr>";
		foreach($header as $cell) { echo "<th>".$cell."</th>"; }
		for($x=1;$x<count($t);$x++)
		{
			echo "<tr>";
			foreach($t[$x] as $cell) { echo "<td>".$cell."</td>"; }
			echo "</tr>";
		}
		echo "</tr>";
		echo "</table>";
		if($links) { echo "<p id='note'>".$this->Html->link('See the original source for notes','/system/view/'.$table['system_id'])."</p>"; }
	}
}
?>
<?   // Copyright (c) Isaac Gouy 2004, 2005 ?>

<?

// FILTER & SORT DATA ////////////////////////////////////////

$p = array($P1,$P2,$P3,$P4);
list($NData,$Selected,$TestValues) = ComparisonData($Langs,$Data,$Sort,$p,$Excl);

$cols = sizeof($TestValues) + 1;
$TestName = $Tests[$SelectedTest][TEST_NAME];
$TestTag = $Tests[$SelectedTest][TEST_TAG];
?>

<!-- // MENU /////////////////////////////////////////////////// -->
<div>
<p><strong>Choose</strong> programs for side-by-side comparison. </p>
<? MkComparisonMenuForm($Langs,$Tests,$SelectedTest,$NData,$P1,$P2,$P3,$P4,$Sort); ?>
</div>

<!-- // SELECTED FULLCPU TAG /////////////////////////////////////////////////// -->

<table class="div">
<tr><td>
<h4 class="rev">&nbsp;<?=$TestName;?> side-by-side - Full CPU Time</h4>
</td></tr>
</table>


<!-- // SELECTED FULLCPU CHART //////////////////////////////////////////////////// -->

<table class="div">
<tr><td>

<img src="chartcpu.php?test=<?=$SelectedTest;?>&amp;p1=<?=$P1;?>&amp;p2=<?=$P2;?>&amp;p3=<?=$P3;?>&amp;p4=<?=$P4;?>&amp;sort=<?=$Sort;?>" 
   width="160" height="240" alt="CPU Time comparison chart" />
</td>


<!-- // SELECTED FULLCPU TABLE //////////////////////////////////////// -->

<td>
<table>
<tr><th colspan="<?=$cols;?>">Full CPU Time as N increases</th></tr>

<tr>
<th>N</th>
<? foreach($TestValues as $v){ printf('<th>%s</th>',$v); echo "\n"; } ?>
</tr>

<? 
usort($Selected,'CompareMaxCpu');
$RowClass = 'c';
foreach($Selected as $row){
   printf('<tr class="%s">', $RowClass); echo "\n";
   printf('<td>%s</td>', $row[N_NAME]); echo "\n";
   foreach($row[N_FULLCPU] as $v){ 
      printf('<td class="r">%0.2f</td>', $v); echo "\n"; 
   }   
   echo "</tr>\n";                          
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>
</table>
</td></tr></table>


<!-- // SELECTED MEMORY TAG /////////////////////////////////////////////////// -->

<table class="div">
<tr><td>
<h4 class="rev">&nbsp;<?=$TestName;?> side-by-side - Memory use</h4>
</td></tr>
</table>


<!-- // SELECTED MEMORY CHART //////////////////////////////////////////////////// -->

<table class="div">
<tr><td>
<img src="chartmem.php?test=<?=$SelectedTest;?>&amp;p1=<?=$P1;?>&amp;p2=<?=$P2;?>&amp;p3=<?=$P3;?>&amp;p4=<?=$P4;?>&amp;sort=<?=$Sort;?>" 
   width="160" height="240" alt="Memory use comparison chart" />
</td>


<!-- // SELECTED MEMORY TABLE //////////////////////////////////////// -->

<td>
<table>
<tr><th colspan="<?=$cols;?>">Memory use as N increases</th></tr>
<tr>
<th>N</th>
<? foreach($TestValues as $v){ printf('<th>%s</th>',$v); echo "\n"; } ?>
</tr>

<? 
usort($Selected,'CompareMaxMemory');
$RowClass = 'c';
foreach($Selected as $row){
   printf('<tr class="%s">', $RowClass); echo "\n";
   printf('<td>%s</td>', $row[N_NAME]); echo "\n";
   foreach($row[N_MEMORY] as $v){ 
      if ($v==0){ $kb = '?'; } else { $kb = number_format((double)$v); }
      printf('<td class="r">%s</td>', $kb); echo "\n"; 
   }   
   echo "</tr>\n";                           
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>
</table>
</td></tr></table>


<!-- // FULLCPU TAG /////////////////////////////////////////////////// -->

<table class="div">
<tr><td>
<h4 class="rev">&nbsp;<?=$TestName;?> benchmark - Full CPU Time</h4>
</td></tr>
</table>


<!-- // FULLCPU TABLE //////////////////////////////////////// -->

<table class="div">
<tr><th colspan="<?=$cols;?>">Full CPU Time as N increases</th></tr>
<tr>
<th>Program &amp; Logs</th>
<? foreach($TestValues as $v){ printf('<th>%s</th>',$v); echo "\n"; } ?>
</tr>

<? 
$RowClass = 'c';
foreach($NData as $row){
   printf('<tr class="%s">', $RowClass); echo "\n";
   printf('<td><a href="benchmark.php?test=%s&amp;lang=%s&amp;id=%d&amp;sort=%s" title="%s program and logs for the %s benchmark">%s</a></td>', 
      $SelectedTest,$row[N_LANG],$row[N_ID],$Sort,$row[N_FULL],$TestName,$row[N_HTML]); echo "\n";

   foreach($row[N_FULLCPU] as $v){ 
      printf('<td class="r">%0.2f</td>', $v); echo "\n"; 
   }   
   echo "</tr>\n";                           
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>
</table>


<!-- // MEMORY TAG /////////////////////////////////////////////////// -->

<table class="div">
<tr><td>
<h4 class="rev">&nbsp;<?=$TestName;?> benchmark - Memory use</h4>
</td></tr>
</table>


<!-- // MEMORY TABLE //////////////////////////////////////// -->

<table class="div">
<tr><th colspan="<?=$cols;?>">Memory use as N increases</th></tr>
<tr>
<th>Program &amp; Logs</th>
<? foreach($TestValues as $v){ printf('<th>%s</th>',$v); echo "\n"; } ?>
</tr>

<? 
$RowClass = 'c';
foreach($NData as $row){
   printf('<tr class="%s">', $RowClass); echo "\n";
   printf('<td><a href="benchmark.php?test=%s&amp;lang=%s&amp;id=%d&amp;sort=%s" title="%s program and logs for the %s benchmark">%s</a></td>', 
      $SelectedTest,$row[N_LANG],$row[N_ID],$Sort,$row[N_FULL],$TestName,$row[N_HTML]); echo "\n";

   foreach($row[N_MEMORY] as $v){ 
      if ($v==0){ $kb = '?'; } else { $kb = number_format((double)$v); }
      printf('<td class="r">%s</td>', $kb); echo "\n"; 
   }   
   echo "</tr>\n";                          
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>

</table>


<!-- // ABOUT /////////////////////////////////////////////////// -->

<table class="div">
<tr><td><h4 class="rev">&nbsp;about side-by-side comparison</h4></td></tr>
<tr><td><?=$About;?></td></tr>  

</table>




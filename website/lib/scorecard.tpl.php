<?   // Copyright (c) Isaac Gouy 2004, 2005 ?>

<!-- // MENU /////////////////////////////////////////////////// -->

<div>
<? MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang,$Sort); ?>
</div>


<!-- // TAG /////////////////////////////////////////////////// -->

<table class="div">
<tr><td colspan="2">
<h4 class="rev"><a class="arev" href="#faster" name="faster">&nbsp;Imaginatively-Weighted Overall Scores</a></h4>
</td></tr>
<tr><td colspan="2">
<p>What fun! <a href="http://en.wikipedia.org/wiki/April_Fool's_Day" title="April Fool's Day defined on Wikipedia"><strong>April Fool's Day</strong></a> all year long! Can you manipulate the multipliers and weights to make your favourite language the fastest programming language in the Shootout?</p>

<p>And remember, languages that implement more benchmarks will move to the top of the list, and those with <strong>many missing benchmarks</strong> will stay at the bottom!</p>

<p>And remember, "For every complex problem, there is a solution that is simple, neat, <strong>and wrong</strong>."</p>
</td></tr> 


<?  // SET WEIGHTS /////////////////////////////////////

if (sizeof($W)==0){ // populate weights

// should define these in config or somewhere
//   $W['xcpu'] = 0; 
   $W['xfullcpu'] = 1;
   $W['xmem'] = 0;
   $W['xloc'] = 0;

   foreach($Tests as $t){
      $W[ $t[TEST_LINK] ] = $t[TEST_WEIGHT];
   }
}


$minWeight = 0;    // normalize weights
$maxWeight = 5;

foreach($W as $k => $v){
   if ($v > $maxWeight){ $W[$k] = $maxWeight; }
   elseif ($v < $minWeight){ $W[$k] = $minWeight; }
}

// CALCULATE WEIGHTED SCORES /////////////////////////////////////

foreach($Data as $k => $test){
   $s = 0.0;
   foreach($test as $t => $v){
      $s += $v[DATA_FULLCPU] * $W[$t] * $W['xfullcpu'];
      $s += $v[DATA_MEMORY] * $W[$t] * $W['xmem'];      
      $s += $v[DATA_LINES] * $W[$t] * $W['xloc'];
   }
   $score[$k] = array($s,sizeof($Tests) - sizeof($test));
}
unset($Data);

function CompareScore($a, $b){
   if ($a[0] == $b[0]) return 0;
   return  ($a[0] > $b[0]) ? -1 : 1;
}
uasort($score, 'CompareScore');

?>

<tr>

<!-- // SCORECARD LANGUAGE TABLE ///////////////////////////////////// -->

<td>
<table>

<tr>
<th>language</th>
<th>score</th>
<th>missing</th>
</tr>

<?  

$RowClass = 'c';
foreach($score as $k => $v){   
   $Name = $Langs[$k][LANG_FULL];
   $HtmlName = $Langs[$k][LANG_HTML];
   printf('<tr class="%s">',$RowClass); echo "\n";
   printf('<td><a href="benchmark.php?test=all&amp;lang=%s&amp;sort=%s" title="%s benchmark rankings and information">%s</a></td>', 
      $k,$Sort,$Name,$HtmlName); echo "\n";
   printf('<td class="r">%0.2f</td><td class="r">%d</td>', $v[0], $v[1]); echo "\n";
   echo "</tr>\n";
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>

</table>
</td>


<!-- // SCORECARD FORM /////////////////////////////////////////// -->

<td>
<form method="get" action="benchmark.php">
<div>
<input type="hidden" name="test" value="all" />
<input type="hidden" name="lang" value="all" />
<input type="hidden" name="sort" value="<?=$Sort;?>" />

<table>
<tr class="c">
<td><p><input type="submit" value="Calculate" /></p></td>
<td><p><input type="reset" value="Reset" /></p></td>
</tr>

<tr><th class="b" colspan="2">multipliers</th></tr>

<!--
<tr class="a">
<td><a href="faq.php?sort=<?=$Sort;?>#cpu">CPU Time</a></td>
<td><p><input type="text" size="2" name="xcpu" value="<?=$W['xcpu'];?>" /></p></td>
</tr>
-->

<tr class="a">
<td><a href="faq.php?sort=<?=$Sort;?>#fullcpu">Full CPU Time</a></td>
<td><p><input type="text" size="2" name="xfullcpu" value="<?=$W['xfullcpu'];?>" /></p></td>
</tr>
<tr class="a">
<td><a href="faq.php?sort=<?=$Sort;?>#memory">Memory Use</a></td>
<td><p><input type="text" size="2" name="xmem" value="<?=$W['xmem'];?>" /></p></td>
</tr>
<tr class="a">
<td><a href="faq.php?sort=<?=$Sort;?>#codelines">Code Lines</a></td>
<td><p><input type="text" size="2" name="xloc" value="<?=$W['xloc'];?>" /></p></td>
</tr>

<!-- THIS IS JUST FOR SPACING! SHOULD USE CSS! -->
<tr class="c"><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><th>benchmark</th><th>weight</th></tr>

<?  
$RowClass = 'c';
foreach($Tests as $t){
   $Link = $t[TEST_LINK];
   $Name = $t[TEST_NAME];
   $weight = $W[ $t[TEST_LINK] ];

   printf('<tr class="%s">',$RowClass); echo "\n";
   printf('<td><a href="benchmark.php?test=%s&amp;lang=all&amp;sort=%s" title="%s performance measurements">%s</a></td>', $Link,$Sort,$Name,$Name); echo "\n";
   printf('<td><p><input type="text" size="2" name="%s" value="%d" /></p></td>', $Link, $weight); echo "\n";
   echo "</tr>\n";
   if ($RowClass=='a'){ $RowClass='c'; } else { $RowClass='a'; } 
}
?>

<tr class="c">
<td><p><input type="submit" value="Calculate" /></p></td>
<td><p><input type="reset" value="Reset" /></p></td>
</tr>

</table>
</div>
</form>
</td>
</tr>


<!-- // ABOUT /////////////////////////////////////////////////// -->

<tr><td colspan="2"><h4 class="rev"><a class="arev" href="#about" name="about">&nbsp;about CRAPS!&#8482;</a></h4></td></tr>
<tr><td colspan="2"><?=$About;?></td></tr>  
</table>
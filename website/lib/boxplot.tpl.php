<?   // Copyright (c) Isaac Gouy 2009 ?>

<? MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang,"fullcpu"); ?>
<h2><a href="#boxplot" name="boxplot">&nbsp;<?=$Title;?></a></h2>

<?
   list($score,$labels,$stats) = $Data;
   unset($Data);
?>

<p>You choose the language implementations for robust <a href="#about">Box and Whiskers statistics</a> of <em>program time</em> measurements on the default benchmarks!</p>

<p><img src="chartbox.php?
      <?='d='.HttpVarsEncodeStats($stats);?>&amp;<?='a='.HttpVarsEncodeLabels($labels);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>

<form method="get" action="benchmark.php">
<input type="hidden" name="test" value="all" />
<input type="hidden" name="lang" value="all" />

<table>
<colgroup span="3" class="txt"></colgroup>
<colgroup span="7" class="num"></colgroup>
<tr class="score">
<td colspan="3"><? MkDataSetMenu($DataSet); ?></td>
<td colspan="7" class="num">
<input type="submit" name="calc" value="calculate" />
<input type="submit" name="calc" value="reset" />
</td>
</tr>

<tr>
<th>&nbsp;&nbsp;x&nbsp;&nbsp;</th>
<th>&nbsp;</th>
<th>language</th>
<th>&nbsp;|-</th>
<th>&nbsp;|---</th>
<th>&nbsp;25%</th>
<th>median</th>
<th>&nbsp;75%</th>
<th>&nbsp;---|</th>
<th>&nbsp;-|</th>
</tr>


<?
foreach($score as $k => $v){
   $Name = $Langs[$k][LANG_FULL];
   $HtmlName = $Langs[$k][LANG_HTML];

   $checked = "";
   if (isset($Plot[$k])){ $checked = 'checked="checked"'; }

   printf('<tr>');
   printf('<td>%s</td>', PFx(pow(10.0,$v[STAT_MEDIAN]/2.0))); echo "\n";
   printf('<td class="score"><p><input type="checkbox" name="%s" "%s" /></p></td>', $k, $checked); echo "\n";
   printf('<td><a href="benchmark.php?test=all&amp;lang=%s&amp;lang2=%s">%s</a></td>',
      $k,$k,$HtmlName); echo "\n";
   printf('<td>%0.2f</td><td>%0.2f</td><td>%0.2f</td><td class="sort">%0.2f</td><td>%0.2f</td><td>%0.2f</td><td>%0.2f</td>',
      $v[STAT_MIN], $v[STAT_XLOWER], $v[STAT_LOWER], $v[STAT_MEDIAN],
      $v[STAT_UPPER], $v[STAT_XUPPER], $v[STAT_MAX]); echo "\n";
   echo "</tr>\n";
}
?>

<tr class="score">
<td colspan="10" class="num">
<input type="submit" name="calc" value="calculate" />
<input type="submit" name="calc" value="reset" />
</td>
</tr>
</table>
<input type="hidden" name="box" value="1" />
</form>


<h3><a href="#about" name="about">&nbsp;about the Boxplot Summary</a></h3>
<?=$About;?>


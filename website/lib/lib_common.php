<?php
// Copyright (c) Isaac Gouy 2004-2009

// DATA LAYOUT ///////////////////////////////////////////////////

define('TEST_LINK',0);
define('TEST_NAME',1);
define('TEST_TAG',2);
define('TEST_WEIGHT',3);
define('TEST_DATE',4);         

define('LANG_LINK',0);
define('LANG_FAMILY',1);
define('LANG_NAME',2);
define('LANG_FULL',3);
define('LANG_HTML',4);
define('LANG_TAG',5);
define('LANG_DATE',6);  
define('LANG_SELECT',7);
define('LANG_COMPARE',8);
define('LANG_SPECIALURL',9);

define('DATA_TEST',0);
define('DATA_LANG',1);
define('DATA_ID',2);
define('DATA_TESTVALUE',3);
define('DATA_GZ',4);
define('DATA_FULLCPU',5);
define('DATA_MEMORY',6);
define('DATA_STATUS',7);
define('DATA_LOAD',8);
define('DATA_ELAPSED',9);

// With quad-core we changed from CPU Time to Elapsed Time
// but we still want to show the old stuff
if (SITE_NAME == 'debian' || SITE_NAME == 'gp4'){
   define('DATA_TIME',DATA_FULLCPU);
} else {
   define('DATA_TIME',DATA_ELAPSED);
}


define('INCL_LINK',0);
define('INCL_NAME',1);

define('EXCL_USE',0);
define('EXCL_TEST',1);
define('EXCL_LANG',2);
define('EXCL_ID',3);


// CONSTANTS ///////////////////////////////////////////////////

define('EXCLUDED','X');
define('PROGRAM_TIMEOUT',-1);
define('PROGRAM_ERROR',-2);
define('PROGRAM_SPECIAL','-3');
define('PROGRAM_EXCLUDED',-4);
define('LANGUAGE_EXCLUDED',-5);
define('NO_COMPARISON',-6);
define('NO_PROGRAM_OUTPUT',-7);

define('N_TEST',0);
define('N_LANG',1);
define('N_ID',2);
define('N_NAME',3);
define('N_FULL',4);
define('N_HTML',5);
define('N_FULLCPU',6);
define('N_MEMORY',7);
define('N_CPU_MAX',8);
define('N_MEMORY_MAX',9);
define('N_COLOR',10);

define('N_N',3);
define('N_LINES',8);
define('N_GZ',9);
define('N_EXCLUDE',10);


define('CPU_MIN',0);
define('MEM_MIN',1);
define('GZ_MIN',2);

define('SCORE_RATIO',0);
define('SCORE_MEAN',1);
define('SCORE_TESTS',2);

define('NAME_LEN',16);
define('PRG_ID_LEN',NAME_LEN+2);

define('STAT_MIN',0);
define('STAT_XLOWER',1);
define('STAT_LOWER',2);
define('STAT_MEDIAN',3);
define('STAT_UPPER',4);
define('STAT_XUPPER',5);
define('STAT_MAX',6);
define('STAT_N',7);

// FUNCTIONS ///////////////////////////////////////////////////

function GetMicroTime(){
   $t = explode(" ", microtime());
   return doubleval($t[1]) + doubleval($t[0]);
}

function ReadIncludeExclude(){
   $incl = array();
   $f = @fopen('./include.csv','r') or die('Cannot open ./include.csv');
   $row = @fgetcsv($f,1024,','); // heading row
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }
      if (isset($row[INCL_LINK]{0})){ $incl[ $row[INCL_LINK] ] = 0; }                   
   }
   @fclose($f);
   
   $excl = array();
   $f = @fopen(DESC_PATH.'/exclude.csv','r') or die('Cannot open '.DESC_PATH.'/exclude.csv');
   $row = @fgetcsv($f,1024,','); // heading row

   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }
      if (isset($row[EXCL_TEST]{0})){           
         if (!isset($row[EXCL_ID])){ $row[EXCL_ID] = 1; }
         $key = $row[EXCL_TEST].$row[EXCL_LANG].strval($row[EXCL_ID]);
         $excl[$key] = $row;
      }
   }
   @fclose($f);

   return array($incl,$excl);
}



function ReadUniqueArrays($FileName,$Incl,$HasHeading=TRUE){
   if (file_exists('./'.$FileName)){
      $f = @fopen('./'.$FileName,'r') or die('Cannot open '.$FileName);
   } else {
      $f = @fopen(DESC_PATH.$FileName,'r') or die('Cannot open '.$FileName);
   }

   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }

   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }
     
//######## Hardcoded assumption that $row[0] is a link name
      if (isset( $Incl[$row[0]] )){ $rows[ $row[0] ] = $row; }      
   }
   @fclose($f);
   return $rows;
}


function ReadSelectedDataArrays($FileName,$Value,$Incl,$HasHeading=TRUE){
   $f = @fopen($FileName,'r') or die ('Cannot open $FileName');
   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }
   $rows = array();
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }
      if ( isset($row[DATA_LANG]) && ($row[DATA_TEST]==$Value) ){                  
         settype($row[DATA_ID],'integer');
         if (isset($rows[$row[DATA_LANG]])){
            array_push( $rows[$row[DATA_LANG]], $row);
         } else {
            $rows[$row[DATA_LANG]] = array($row);
         }
      }
   }
   @fclose($f);      
   return $rows;
}


function SplitByTestValue(&$data){
   $splitdata = array();
   foreach($data as $d){
      if (isset($splitdata[ $d[DATA_TESTVALUE] ])){
         $splitdata[ $d[DATA_TESTVALUE] ][] = $d;
      } else {
         $splitdata[ $d[DATA_TESTVALUE] ] = array($d);
      }
   }
   krsort($splitdata);
   $ks = array_keys($splitdata);
   $k = array_shift($ks);
   $largest = $splitdata[ $k ];
   unset( $splitdata[ $k ] );
   $others = array();
   foreach ($splitdata as $k => $v){
      $others = array_merge($others,$v);
   }
   return array($largest,$others);
}


function TestData($FileName,&$Tests,&$Langs,&$Incl,&$Excl,$HasHeading=TRUE){
   // expect NOT to encounter more than one DATA_TESTVALUE for each test
   $f = @fopen($FileName,'r') or die ('Cannot open $FileName');
   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }

   $data = array();
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }

      $test = $row[DATA_TEST];
      $lang = $row[DATA_LANG];
      settype($row[DATA_ID],'integer');
      $id = $row[DATA_ID];
      $key = $test.$lang.strval($id);

      // accumulate all acceptable datarows, exclude duplicates

      if (isset($Incl[$test]) && isset($Incl[$lang]) && isset($Langs[$lang]) &&
               ($Tests[$test][TEST_WEIGHT]>0) &&
                  !isset($Excl[$key])){

            if ($row[DATA_STATUS] == 0 && (
                  ($row[DATA_FULLCPU] > 0 && (!isset($data[$lang][$test]) ||
                     $row[DATA_FULLCPU] < $data[$lang][$test])))){

               $data[$lang][$test] = $row[DATA_FULLCPU];
            }
      }
   }
   @fclose($f);

   /*
   When there are multiple N values this doesn't seem quite right - we might
   have taken times from different programs for different N values for the
   same language implementation - but it's completely negligible.
   */

   $a = array();
   // preserve ordering of $Tests
   foreach($Tests as $k => $v){
      if (($v[TEST_WEIGHT]<=0)){ continue; }
      $a[$k] = array();
   }
   foreach($data as $lang => $testvalues){
      foreach($testvalues as $t => $v){
         if ($v > 0.0){
            $a[$t][] = log10($v)*2.0;
         }
      }
   }

   foreach($Tests as $k => $v){
      if (($v[TEST_WEIGHT]<=0)){ continue; }
      $a[$k] = Percentiles($a[$k]);
   }

   $labels = array();
   $stats = array();
   foreach($a as $k => $v){
      $labels[] = ' '.$Tests[$k][TEST_NAME];
      $stats[] = $v;
   }
   return array($labels,$stats);
}



function Percentiles($a){
   sort($a);
   $n = sizeof($a);
   $mid = floor($n / 2);
   if ($n % 2 != 0){
      $median = $a[$mid];
      $lower = Median( array_slice($a,0,$mid+1) ); // include median in both quartiles
      $upper = Median( array_slice($a,$mid) );
   } else {
      $median = ($a[$mid-1] + $a[$mid]) / 2.0;
      $lower = Median( array_slice($a,0,$mid) );
      $upper = Median( array_slice($a,$mid) );
   }
   $maxwhisker = ($upper - $lower) * 1.5;
   $xlower = ($lower - $maxwhisker < $a[0]) ? $a[0]: $lower - $maxwhisker;
   $xupper = ($upper + $maxwhisker > $a[$n-1]) ? $a[$n-1] : $upper + $maxwhisker;

   return array($a[0],$xlower,$lower,$median,$upper,$xupper,$a[$n-1],$n);
}


function Median($a){
   $n = sizeof($a);
   $mid = floor($n / 2);
   return ($n % 2 != 0) ? $a[$mid] : ($a[$mid-1] + $a[$mid]) / 2.0;
}


function CompareTestCpuTime($a, $b){
   if ($a[DATA_TEST] == $b[DATA_TEST]){
      if ($a[DATA_FULLCPU] == $b[DATA_FULLCPU]){
         if ($a[DATA_MEMORY] == $b[DATA_MEMORY]){ 
            if ($a[DATA_GZ] == $b[DATA_GZ]){ return 0; }
            else { return ($a[DATA_GZ] < $b[DATA_GZ]) ? -1 : 1; }
         }
         else { return ($a[DATA_MEMORY] < $b[DATA_MEMORY]) ? -1 : 1; }
      }
      else { return ($a[DATA_FULLCPU] < $b[DATA_FULLCPU]) ? -1 : 1; }
   }
   return  ($a[DATA_TEST] < $b[DATA_TEST]) ? -1 : 1;
}


function CompareFullCpuTime($a, $b){
   if ($a[DATA_FULLCPU] == $b[DATA_FULLCPU]){
      if ($a[DATA_MEMORY] == $b[DATA_MEMORY]){
         if ($a[DATA_GZ] == $b[DATA_GZ]){ return 0; }
         else { return ($a[DATA_GZ] < $b[DATA_GZ]) ? -1 : 1; }
      }
      else { return ($a[DATA_MEMORY] < $b[DATA_MEMORY]) ? -1 : 1; }
   }
   return  ($a[DATA_FULLCPU] < $b[DATA_FULLCPU]) ? -1 : 1;
}

function CompareMemoryUse($a, $b){
   if ($a[DATA_MEMORY] == $b[DATA_MEMORY]){
      if ($a[DATA_FULLCPU] == $b[DATA_FULLCPU]){
         if ($a[DATA_GZ] == $b[DATA_GZ]){ return 0; }
         else { return ($a[DATA_GZ] < $b[DATA_GZ]) ? -1 : 1; }
      }
      else { return ($a[DATA_FULLCPU] < $b[DATA_FULLCPU]) ? -1 : 1; }
   }
   return  ($a[DATA_MEMORY] < $b[DATA_MEMORY]) ? -1 : 1;
}

function CompareGz($a, $b){
   if ($a[DATA_GZ] == $b[DATA_GZ]){
      if ($a[DATA_FULLCPU] == $b[DATA_FULLCPU]){
         if ($a[DATA_MEMORY] == $b[DATA_MEMORY]){ return 0; }
         else { return ($a[DATA_MEMORY] < $b[DATA_MEMORY]) ? -1 : 1; }            
      }
      else { return ($a[DATA_FULLCPU] < $b[DATA_FULLCPU]) ? -1 : 1; }
   }
   return  ($a[DATA_GZ] < $b[DATA_GZ]) ? -1 : 1;
}

function CompareElapsed($a, $b){
   if ($a[DATA_ELAPSED] == $b[DATA_ELAPSED]){
      if ($a[DATA_MEMORY] == $b[DATA_MEMORY]){
         if ($a[DATA_GZ] == $b[DATA_GZ]){ return 0; }
         else { return ($a[DATA_GZ] < $b[DATA_GZ]) ? -1 : 1; }
      }
      else { return ($a[DATA_MEMORY] < $b[DATA_MEMORY]) ? -1 : 1; }
   }
   return  ($a[DATA_ELAPSED] < $b[DATA_ELAPSED]) ? -1 : 1;
}


function CompareLangName($a, $b){
   return strcasecmp($a[LANG_FULL],$b[LANG_FULL]);
}

function CompareTestName($a, $b){
   return strcasecmp($a[TEST_NAME],$b[TEST_NAME]);
}

function CompareTestValue($a, $b){
   if ($a[DATA_LANG] == $b[DATA_LANG]){
      if ($a[DATA_ID] == $b[DATA_ID]){
         if ($a[DATA_TESTVALUE] == $b[DATA_TESTVALUE]) return 0;
         return ($a[DATA_TESTVALUE] < $b[DATA_TESTVALUE]) ? -1 : 1;
      }
      else {
         return ($a[DATA_ID] < $b[DATA_ID]) ? -1 : 1;      
      }
   }
   else {
      return ($a[DATA_LANG] < $b[DATA_LANG]) ? -1 : 1;      
   }   
}

function CompareTestValue2($a, $b){
   if ($a[DATA_TEST] == $b[DATA_TEST]){
         if ($a[DATA_TESTVALUE] == $b[DATA_TESTVALUE]) return 0;
         return ($a[DATA_TESTVALUE] > $b[DATA_TESTVALUE]) ? -1 : 1;
   }
   else {
      return ($a[DATA_TEST] < $b[DATA_TEST]) ? -1 : 1;      
   }
}


function CompareNName($a, $b){
   return strcasecmp($a[N_FULL],$b[N_FULL]);
}

function SortName($sort){
   if ($sort=='fullcpu'){ return 'Full CPU Time'; }
   elseif ($sort=='gz'){ return 'GZ Compressed Source'; }
   elseif ($sort=='elapsed'){ return 'Elapsed Time'; }
   else { return 'Memory use'; }
}

function IdName($id){
   if ($id>1){ return ' #'.$id; } else { return ''; }
}


function ExcludeData(&$d,&$langs,&$Excl){
   if( !isset($langs[$d[DATA_LANG]]) ) { return LANGUAGE_EXCLUDED; }

   $key = $d[DATA_TEST].$d[DATA_LANG].strval($d[DATA_ID]);
   if (isset($Excl[$key])){
      if ($Excl[$key][EXCL_USE]==EXCLUDED){ return PROGRAM_EXCLUDED; }
      else { return PROGRAM_SPECIAL; }
   }
   return $d[DATA_STATUS];
}


function FilterAndSortData($langs,$data,&$sort,&$Excl){
   $Accepted = array();
   $Rejected = array();   
   $Special = array();

   // $data is an associative array keyed by language
   // Each value is itself an array of one or more data records

   foreach($data as $ar){
      foreach($ar as $d){  
         $x = ExcludeData($d,$langs,$Excl);         
         if ($x==PROGRAM_SPECIAL){ 
            $Special[] = $d;
         } elseif ($x==PROGRAM_EXCLUDED) { 

         } elseif ($x) {
            $Rejected[] = $d;
         } else {
            $Accepted[] = $d;
         }
      }         
   }
   
   // hack for old data which doesn't have Elapsed times only CPU times
   if ($sort=='elapsed' && sizeof($Accepted)>0 && $Accepted[0][DATA_ELAPSED]== 0.0){
      $sort = 'fullcpu';
   }

   if ($sort=='fullcpu'){
      usort($Accepted, 'CompareFullCpuTime');
      usort($Special, 'CompareFullCpuTime');
   } elseif ($sort=='kb'){
      usort($Accepted, 'CompareMemoryUse'); 
      usort($Special, 'CompareMemoryUse');
   } elseif ($sort=='gz'){
      usort($Accepted, 'CompareGz');
      usort($Special, 'CompareGz');
   } elseif ($sort=='elapsed'){
      usort($Accepted, 'CompareElapsed');
      usort($Special, 'CompareElapsed');
   }

   return array($Accepted,$Rejected,$Special);
}


function TimeMemoryRatios(&$Accepted,$sort){
   if ($sort=='fullcpu'){ $DTIME = DATA_FULLCPU; }
   else { $DTIME = DATA_TIME; }

   foreach($Accepted as $d){
      if (!isset($mintime)||$d[$DTIME] < $mintime){
         $mintime = $d[$DTIME];
      }
      if (!isset($minmem)||$d[DATA_MEMORY] < $minmem){
         $minmem = $d[DATA_MEMORY];
      }
   }
   
   // memory use measurement can fail, so accomodate strange data
   $lowest = 200.0;
   if ($minmem<$lowest){ $minmem = $lowest; }

   $timeratio = array();
   $memratio = array();
   foreach($Accepted as $d){
     if ($mintime==0){ $timeratio[] = 1.0; }
     else { $timeratio[] = $d[$DTIME]/$mintime; }
     
     // memory use measurement can fail, so accomodate strange data
     if ($d[DATA_MEMORY]<$lowest){ $memratio[] = 1.0; }
     else { $memratio[] = $d[DATA_MEMORY]/$minmem; }
   }
   return array($timeratio,$memratio);
}


function ProgramData($FileName,$T,$L,$I,&$Langs,&$Incl,&$Excl,$HasHeading=TRUE){
   $f = @fopen($FileName,'r') or die ('Cannot open $FileName');
   if ($HasHeading){ $row = @fgetcsv($f,1024,','); }

   $data = array();
   while (!@feof ($f)){
      $row = @fgetcsv($f,1024,',');
      if (!is_array($row)){ continue; }

      if (($row[DATA_TEST]==$T)&&($row[DATA_LANG]==$L)){
         settype($row[DATA_ID],'integer');
         if (($I > -1)&&($row[DATA_ID]==$I)){ return $row; }
         else { $data[] = $row; }
      }
   }
   @fclose($f);
   usort($data, 'CompareFullCpuTime');

   if ($I == -1){
      foreach($data as $ar){
         if (isset($Incl[$ar[DATA_TEST]]) && isset($Incl[$ar[DATA_LANG]])
               && !ExcludeData($ar,$Langs,$Excl)){
            return $ar;
         }
      }
      foreach($data as $ar){
         if (isset($Incl[$ar[DATA_TEST]]) && isset($Incl[$ar[DATA_LANG]])){
           $ex = ExcludeData($ar,$Langs,$Excl);
           //if (($ex == PROGRAM_TIMEOUT)||($ex == PROGRAM_ERROR)){
           if ($ex != PROGRAM_EXCLUDED && $ex != LANGUAGE_EXCLUDED){
              return $ar;
           }
         }
      }
   }
   return array();
}



// CONTENT ///////////////////////////////////////////////////

function MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang,$Sort){
   echo '<form method="get" action="benchmark.php">', "\n";
   echo '<p><select name="test">', "\n";
   echo '<option value="all">- all ', TESTS_PHRASE, 's -</option>', "\n";

   foreach($Tests as $Row){
      $Link = $Row[TEST_LINK];
      $Name = $Row[TEST_NAME];
      if ($Link==$SelectedTest){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";

   echo '<select name="lang">', "\n";
   echo '<option value="all">- all ', LANGS_PHRASE, 's -</option>', "\n";
   foreach($Langs as $Row){
      $Link = $Row[LANG_LINK];
      $Name = $Row[LANG_FULL];
      if ($Link==$SelectedLang){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";
   echo '<input type="submit" value="Show" />', "\n";
   echo '</p><input type="hidden" name="box" value="1" /></form>', "\n";
}


function HtmlFragment($FileName){
   $html = '<p>&nbsp;</p>';
   if (is_readable($FileName)){
      $f = fopen($FileName,'r');
      $fs = filesize($FileName);
      if ($fs > 0){ $html = fread($f,$fs); }
      fclose($f);
   }
   return $html;
}


function PFx($d){
   if ($d>9.9){ return number_format($d); }
   elseif ($d>0.0){ return number_format($d,1); }
   else { return "&nbsp;"; }
}



function PTime($d){
   if ($d <= 0.0){ return ''; }
   if ($d<300.0){ return number_format($d,2); }
   elseif ($d<3600.0){
     $m = floor($d/60); $s = $d-($m*60); $ss = number_format($s,0);
     if (strlen($ss)<2) { $ss = "0".$ss; }
     return number_format($m,0)." min"; }
   else {
     $h = floor($d/3600); $m = floor(($d-($h*3600))/60);
     $mm = number_format($m,0); if (strlen($mm)<2) { $mm = "0".$mm; }
     return number_format($h,0)."h ".$mm." min";
   }
}



function HttpVarsEncodeArray($a){
   foreach($a as $v){ $d[] = intval(sprintf('%d',$v*10)); }
   $s = implode('o',$d);
   return $s;
}



function HttpVarsEncodeStats($aa){
   $a = array(); $d = array();
   foreach($aa as $stats){ $a = array_merge($a,$stats); }
   foreach($a as $each){ $d[] = intval(sprintf('%d',($each+1)*100)); } // avoid negatives
   $s = implode('o',$d);
   return $s;
}



function HttpVarsEncodeLabels($a){
   $s = implode(',',$a);
   return rawurlencode($s);
}


function HttpVarsEncodeHeadToHead(&$Tests,&$Data){
   $a = array();
   foreach($Tests as $Row){
      if (($Row[TEST_WEIGHT]<=0)){ continue; }
      if (isset($Data[$Row[TEST_LINK]])){
         $v = $Data[$Row[TEST_LINK]];

         $a[] = intval(sprintf('%d',$v[N_FULLCPU]*100000.0));
         
         if ($Row[TEST_NAME]=='startup'){ $kb = 1.0; } else { $kb = $v[N_MEMORY]; }
         $a[] = intval(sprintf('%d',$kb*100000.0));
         
         $a[] = intval(sprintf('%d',$v[N_GZ]*100000.0));

      } else {
         $a[] = 100000.0; $a[] = 100000.0; $a[] = 100000.0;
      }
   }
   $s = implode('o',$a);
   return $s;
}


function StatusMessage($i){
   if ($i==0){ $m = ''; }
   elseif ($i==PROGRAM_TIMEOUT){ $m = 'Timed&nbsp;Out'; }
   elseif ($i==PROGRAM_ERROR){ $m = 'Failed'; }
   elseif ($i==NO_COMPARISON){ $m = 'No&nbsp;Comparison'; }
   elseif ($i==-10){ $m = 'Bad&nbsp;Output'; }
   elseif ($i==-11){ $m = 'Make&nbsp;Error'; }
   elseif ($i==-12){ $m = 'Empty'; }
   else { $m = ''; }
   return $m;
}


function ElapsedTime($d){
   if ($d[DATA_ELAPSED] > 0.0){
      return number_format($d[DATA_ELAPSED],2);
   } else {
      return '';
   }
}


function CpuLoad($d){
   if (strlen($d[DATA_LOAD])>1){
      return str_replace(' ','&nbsp;',$d[DATA_LOAD]);
   } else {
      return '';
   }
}


?>
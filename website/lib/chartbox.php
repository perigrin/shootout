<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2009

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');

list($in,$ex) = WhiteListInEx();
$WhiteListLangs = WhiteListUnique('lang.csv',$in);


// DATA ////////////////////////////////////////////////////

$D = array();
if (isset($HTTP_GET_VARS['d'])
      && (strlen($HTTP_GET_VARS['d']) && (strlen($HTTP_GET_VARS['d']) <= 512))){
   $X = $HTTP_GET_VARS['d'];
   if (ereg("^[0-9o]+$",$X)){
      foreach(explode('o',$X) as $v){
         if (strlen($v) && (strlen($v) <= 5)){ $D[] = doubleval($v)/100.0 -1.0; }
      }
   }
}

$A = array();
if (isset($HTTP_GET_VARS['a'])
      && (strlen($HTTP_GET_VARS['a']) && (strlen($HTTP_GET_VARS['a']) <= 512))){
   $X = $HTTP_GET_VARS['a'];
   if (ereg("^[a-z0-9O]+$",$X)){
      foreach(explode('O',$X) as $v){
         if (strlen($v) && (strlen($v) <= 24) &&
            (isset($WhiteListLangs[$v]))){ $A[] = ' '.$WhiteListLangs[$v][LANG_FULL]; }
      }
   }
}

$Mark = '';
if (isset($HTTP_GET_VARS['mark'])
      && (strlen($HTTP_GET_VARS['mark']) && (strlen($HTTP_GET_VARS['mark']) <= 24))){
   $X = rawurldecode($HTTP_GET_VARS['mark']);
   if (ereg("^[ a-zA-Z0-9]+$",$X)){
      $Mark = $X;
   }
}


// CHART /////////////////////////////////////////////////////
   $w = 480;
   $h = 300;

   $boxspace = 8;
   $xo = 65;
   $yo = $boxspace;

   $yscale = 38;

   $boxw = 20;
   $boxo = 10;
   $whisk = floor(($boxw - $boxo)/2);
   $outlier = 5;
   $maxboxes= 15;

$im = ImageCreate($w,$h);
ImageColorAllocate($im,204,204,204);

$white = ImageColorAllocate($im,255,255,255);
$black = ImageColorAllocate($im,0,0,0);
$bgray = ImageColorAllocate($im,204,204,204);
$mgray = ImageColorAllocate($im,165,165,165);
$gray = ImageColorAllocate($im,221,221,221);
$charwidth2 = 6.0; // for size 2
$charwidth3 = 7.0; // for size 2

// BACKGROUND TEXT LAYER
$x = $xo-$charwidth2;
$count = 0;
foreach($A as $k){
   $watermark = str_repeat($k,15);
   ImageStringUp($im, 3, $x, $h+17, $watermark, $mgray);
   $x = $x + $boxw + $boxspace;
   if ($count == $maxboxes){ break; } else { $count++; }
}

// BOXES
$n = sizeof($D);
if ($n%STATS_SIZE == 0){
   $x = $xo-4;
   $count = 0;
   for ($i=0; $i<$n; $i+=STATS_SIZE){
      $xlower = $h-($yo+$D[$i+STAT_XLOWER]*$yscale);
      $lower = $h-($yo+$D[$i+STAT_LOWER]*$yscale);
      $upper = $h-($yo+$D[$i+STAT_UPPER]*$yscale);
      $xupper = $h-($yo+$D[$i+STAT_XUPPER]*$yscale);

      ImageLine($im, $x+$boxo, $xlower, $x+$boxo, $xupper, $white);
      ImageLine($im, $x+$boxo+1, $xlower, $x+$boxo+1, $xupper, $white);
      ImageFilledRectangle($im, $x, $upper, $x+$boxw, $lower, $white);

      $x = $x + $boxw + $boxspace;
      if ($count == $maxboxes){ break; } else { $count++;  }
   }
}

// GRID
for ($i=0; $i<9; $i++){
   $y = $h-($yo+$i*$yscale);
//   ImageLine($im, $xo-15, $y, $w, $y, $gray);

   if ($i>0){ ImageString($im, 2, $xo-15, $y, strval($i), $black); }
   $label = strval( floor(pow(10.0,$i/2.0)) ).'x';
   $x = strlen($label)*$charwidth3;
   ImageString($im, 2, $xo-$x-6, $y-13, $label, $white);
}
for ($i=0; $i<20; $i++){
   if ($i==1||$i==5||$i==9||$i==13||$i==17){ continue; }
   $y = $h-($yo+($i/2.0)*$yscale);
   ImageLine($im, $xo-15, $y, $w, $y, $gray);
}

// AXIS LEGEND
$label = '"hemibels" 2 * log10 time/fastest by language';
$y = ($h-strlen($label)*$charwidth2)/2;
ImageStringUp($im, 2, 5, $h-$y, $label, $black);

// MEDIAN and WHISKERS
$n = sizeof($D);
if ($n%STATS_SIZE == 0){
   $x = $xo-4;
   $count = 0;
   for ($i=0; $i<$n; $i+=STATS_SIZE){
      $xlower = $h-($yo+$D[$i+STAT_XLOWER]*$yscale);
      $median = $h-($yo+$D[$i+STAT_MEDIAN]*$yscale);
      $xupper = $h-($yo+$D[$i+STAT_XUPPER]*$yscale);

      ImageLine($im, $x+$whisk, $xlower, $x+$boxw-$whisk, $xlower, $white);
      ImageLine($im, $x+$whisk, $xupper, $x+$boxw-$whisk, $xupper, $white);
      ImageFilledRectangle($im, $x, $median-1, $x+$boxw, $median, $black);

      if ($D[$i+STAT_MIN] < $D[$i+STAT_XLOWER]){
         $y = $h-($yo+$D[$i+STAT_MIN]*$yscale);
         ImageLine($im, $x+$whisk, $y, $x+$boxw-$whisk, $y, $white);
         ImageLine($im, $x+$boxo, $y-5, $x+$boxo, $y, $white);
         ImageLine($im, $x+$boxo+1, $y-5, $x+$boxo+1, $y, $white);
      }
      if ($D[$i+STAT_MAX] > $D[$i+STAT_XUPPER]){
         $y = $h-($yo+$D[$i+STAT_MAX]*$yscale);
         ImageLine($im, $x+$whisk, $y, $x+$boxw-$whisk, $y, $white);
         ImageLine($im, $x+$boxo, $y, $x+$boxo, $y+5, $white);
         ImageLine($im, $x+$boxo+1, $y, $x+$boxo+1, $y+5, $white);
      }

      $x = $x + $boxw + $boxspace;
      if ($count == $maxboxes){ break; } else { $count++; }
   }
}

// NOTICE
$x = $w-5-strlen($Mark)*$charwidth2;
ImageString($im, 2, $x, $h-29, $Mark, $white);
$label = 'The Computer Language Benchmarks Game';
$x = $w-5-strlen($label)*$charwidth2;
ImageString($im, 2, $x, $h-15, $label, $white);

ImageInterlace($im,1);
ImagePNG($im);
ImageDestroy($im);
?> 

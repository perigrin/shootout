<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2004-2009

// DATA ////////////////////////////////////////////////////

$D = array();
if (isset($HTTP_GET_VARS['d'])
      && (strlen($HTTP_GET_VARS['d']) && (strlen($HTTP_GET_VARS['d']) <= 512))){
   $X = $HTTP_GET_VARS['d'];
   if (ereg("^[0-9o]+$",$X)){
      foreach(explode('o',$X) as $v){
         if (strlen($v) && (strlen($v) <= 8)){ $D[] = doubleval($v)/10.0; }
      }
   }
}

$M = array();
if (isset($HTTP_GET_VARS['m'])
      && (strlen($HTTP_GET_VARS['m']) && (strlen($HTTP_GET_VARS['m']) <= 512))){
   $X = $HTTP_GET_VARS['m'];
   if (ereg("^[0-9o]+$",$X)){
      foreach(explode('o',$X) as $v){
         if (strlen($v) && (strlen($v) <= 8)){ $M[] = doubleval($v)/10.0; }
      }
   }
}

// CHART //////////////////////////////////////////////////

   $barspace = 3;
   $w = 480;
   $h = 225;

   $xo = 65;
   $yo = 8;

   $yscale = 64;
   $barw = 3;
   $barmw = 0;
   $charwidth2 = 6.0; // for size 2


$im = ImageCreate($w,$h);
ImageColorAllocate($im,204,204,204);
$white = ImageColorAllocate($im,255,255,255);
$black = ImageColorAllocate($im,0,0,0);
$bgray = ImageColorAllocate($im,204,204,204);
$gray = ImageColorAllocate($im,221,221,221);

// BARS
$x = $xo;
foreach($D as $v){
   $y = $h-($yo+ log10($v)*$yscale);
   ImageFilledRectangle($im, $x, $y, $x+$barw, $h-$yo, $white);
   $x = $x + $barw + $barspace;
}
$x = $xo;
foreach($M as $v){
   $y = $h-($yo+ log10($v)*$yscale);
   ImageFilledRectangle($im, $x, $y, $x+$barmw, $h-$yo, $black);
   $x = $x + $barmw + $barw + $barspace;
}

// GRID
for ($i=0; $i<13; $i++){
   if ($i==1||$i==5||$i==9){ continue; }
   $y = $h-($yo+($i/4.0)*$yscale);
   ImageLine($im, $xo-15, $y, $w, $y, $gray);

   $label = strval( floor(pow(10.0,$i/4.0)) ).'x';
   $x = strlen($label)*$charwidth2;
   ImageString($im, 2, $xo-$x-6, $y-13, $label, $white);
}

// AXIS LEGEND
ImageStringUp($im, 2, 5, $h-20, 'log10 secs ratio', $black);
ImageFilledRectangle($im, 11, $h-16, 11+$barw, $h-6, $white);

ImageStringUp($im, 2, 5, $h-138, 'log10 KB ratio', $black);
ImageFilledRectangle($im, 12, $h-134, 12+$barmw, $h-124, $black);

ImageInterlace($im,1);
ImagePng($im);
ImageDestroy($im);
?>
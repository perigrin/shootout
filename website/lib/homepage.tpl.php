<?   // Copyright (c) Isaac Gouy 2004 ?>

<?php      echo '<?xml version="1.0" encoding="iso-8859-1" ?>';      ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="robots" content="all" />
<meta name="revisit" content="5 days" />
<meta name="keywords" content="fast programming language performance benchmark shootout" />
<meta name="description" content="Programming languages compared on more than 25 performance benchmarks, for more than 50 languages." />

<title><?=$PageTitle;?></title>
<link rel="stylesheet" type="text/css" href="<?=CORE_SITE;?>benchmark.css" />
<style type="text/css" media="all">
   .hf, .rev, .arev, .arev:visited { background-color: <?=REV_COLOR;?>; }
   .arevCore { background-color: <?=REV_COLOR_CORE;?>; }
   .arevGreat { background-color: <?=REV_COLOR_GREAT;?>; }
   .arevSandbox { background-color: <?=REV_COLOR_SANDBOX;?>; }
</style>
</head>


<body>
<table class="hf"><tr>
<td><h2><?=$BannerTitle;?></h2></td>
<td class="hftag"><a class="arev" href="faq.php?sort=<?=$Sort;?>" ><?=$FaqTitle;?></a></td>
</tr></table>

<?=$PageBody;?>


<form><input type="hidden" name="sort" value="<?=$Sort;?>" /></form>
<div class="center"><p><a href="miscfile.php?sort=<?=$Sort;?>&file=license&title=revised BSD license" title="Software contributed to the Shootout is published under this revised BSD license">revised&nbsp;BSD&nbsp;license</a> | <b>Send</b>&nbsp;<a href="http://alioth.debian.org/sendmessage.php?touser=1230" title="Send your suggestion or comment without subscribing to the mailing list">suggestions&nbsp;and&nbsp;comments</a></p></div>
</body>
</html>
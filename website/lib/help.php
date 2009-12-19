<?php
// Copyright (c) Isaac Gouy 2004-2009

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_common.php');  
require_once(LIB); 


// TEMPLATE VARS ////////////////////////////////////////////////

$Page = & new Template(LIB_PATH);
$Page->set('PageTitle', 'Help'.BAR.'Computer&nbsp;Language&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', 'The&nbsp;Computer&nbsp;Language&nbsp; <br/>Benchmarks&nbsp;Game');
$Page->set('FaqTitle', 'Help');
$Page->set('PageBody', BLANK);

$Body = & new Template(LIB_PATH);
$Body->set('Download', DOWNLOAD_PATH);
$Body->set('Changed', filemtime(LIB_PATH.'help.tpl.php'));

$Page->set('PageBody', $Body->fetch('help.tpl.php'));


$metaRobots = '<meta name="robots" content="all" /><meta name="revisit" content="10 days" />';
$bannerUrl = CORE_SITE;
$bannerTitleTag = 'title="Go to Computer Language Benchmarks Game Home"';

$faqUrl = CORE_SITE.'help.php';
$MetaKeywords = '<meta name="description" content="How to compare programming languages in the benchmarks game. How to contribute programs. How programs were measured." />';



$Page->set('Robots', $metaRobots);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('BannerTitleTag', $bannerTitleTag);
$Page->set('FaqUrl', $faqUrl);
$Page->set('MetaKeywords', $MetaKeywords);
$Page->set('PageId', 'faq');

echo $Page->fetch('page.tpl.php');
?>


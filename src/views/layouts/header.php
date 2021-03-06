﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bid & Sell</title>
<link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
<link rel="stylesheet" type="text/css" href="/assets/css/flash.css" />
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="/assets/css/iecss.css" />
<![endif]-->
<script type="text/javascript" src="/assets/js/formularios.js"></script>
</head>
<body>
<div id="main_container">
    <div class="top_bar">
        <div class="top_search">
            <form action="index.php" method="get" accept-charset="utf-8">
                <input type="hidden" name="controller" value="product" />
                <input type="hidden" name="action" value="search" />

                <div class="search_text"><?php print $lang["general"]["search"]; ?></div>
                <input type="text" class="search_input" name="search" />
                <input alt="search" type="image" src="/assets/img/search.gif" class="search_bt" />
            </form>
        </div>
        <div class="languages">
            <div class="lang_text"><?php print $lang["general"]["languages"]; ?></div>
            <a href="/index.php?action=changeLanguage&lang=en&redirect=<?php print $this->getCurrentUrl(); ?>" class="lang"><img src="/assets/img/en.gif" alt="" title="" border="0" /></a>
            <a href="/index.php?action=changeLanguage&lang=es&redirect=<?php print $this->getCurrentUrl(); ?>" class="lang"><img src="/assets/img/es.gif" alt="" title="" border="0" /></a>
            <a href="/index.php?action=changeLanguage&lang=gl&redirect=<?php print $this->getCurrentUrl(); ?>" class="lang"><img src="/assets/img/gl.gif" alt="" title="" border="0" /></a>
        </div>
    </div>
    <div id="header">
        <div id="logo">
            <a href="index.php"><img src="/assets/img/logo.png" alt="" title="" border="0" width="237" height="140" /></a>
        </div>
    </div>
   <div id="main_content">

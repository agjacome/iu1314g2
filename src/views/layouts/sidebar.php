<div class="left_content">
    <div class="title_box"><?php print $lang ["head"]["menu"]; ?></div>
    <ul class="left_menu">
<?php if ($this->isLoggedIn()) { ?>
        <li class="odd"><a href="/index.php?controller=user&action=logout"><?php print $lang["user"]["logout"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=user&action=get&login=<?php print $username; ?>"><?php print $lang ["user"]["panel"]; ?></a></li>
<?php } else { ?>
        <li class="odd"><a href="/index.php?controller=user&action=login"><?php print $lang["user"]["login"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=user&action=create"><?php print $lang ["user"]["register"]; ?></a></li>
<?php } ?>
        <li class="even"><a href="/index.php?controller=product&action=available"><?php print $lang ["head"]["productos"]; ?></a></li>
<?php if ($this->isAdmin()) { ?>
        <li class="odd"><a href="/index.php?controller=store"><?php print $lang["store"]["panel"]; ?></a></li>
<?php } ?>
    </ul>
</div><!-- end of left content -->

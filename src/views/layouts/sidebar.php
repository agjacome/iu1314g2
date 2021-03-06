<div class="left_content">
    <div class="title_box"><?php print $lang ["general"]["menu"]; ?></div>
    <ul class="left_menu">
<?php if ($this->isLoggedIn()) { ?>
        <li class="even"><a href="/index.php?controller=user&action=logout"><?php print $lang["user"]["logout"]; ?></a></li>
        <li class="odd"><a href="/index.php?controller=user&action=get&login=<?php print $username; ?>"><?php print $lang ["user"]["panel"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=product"><?php print $lang["product"]["panel"]; ?></a>
<?php } else { ?>
        <li class="odd"><a href="/index.php?controller=user&action=login"><?php print $lang["user"]["login"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=user&action=create"><?php print $lang ["user"]["register"]; ?></a></li>
<?php } ?>
        <li class="odd"><a href="/index.php?controller=product&action=available"><?php print $lang["product"]["list"]; ?></a></li>
        <li class="even"><a href="/index.php?controller=sale&action=listing"><?php print $lang ["sale"]["panel"]; ?></a></li>
        <li class="odd"><a href="/index.php?controller=bidding&action=listing"><?php print $lang["bidding"]["panel"]; ?></a></li>
<?php if ($this->isAdmin()) { ?>
        <li class="even"><a href="/index.php?controller=store"><?php print $lang["store"]["panel"]; ?></a></li>
<?php } ?>
    </ul>
</div><!-- end of left content -->

        <div>
<?php if ($logged) { ?>
            <h3><?php print $username; ?></h3>
            <a href="index.php?controller=user&action=logout"><?php print $lang["user"]["logout"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=get&login=<?php print $username; ?>"><?php print $lang["user"]["details"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=update&login=<?php print $username; ?>"><?php print $lang["user"]["update"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=delete&login=<?php print $username; ?>"><?php print $lang["user"]["delete"]; ?></a>
<?php if ($userrole === "admin") { ?>
            <br>
            <a href="index.php?controller=user&action=listing"><?php print $lang["user"]["list"]; ?></a>
<?php } } else { ?>
            <a href="index.php?controller=user&action=login"><?php print $lang["user"]["login"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=create"><?php print $lang["user"]["register"]; ?></a>
<?php } ?>
        </div>

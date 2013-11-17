<?php

require "header.php";
require "sidebar.php";

?>

        <div>
            <a href="index.php?action=changeLanguage&lang=es">ES</a> | <a href="index.php?action=changeLanguage&lang=en">EN</a>
        </div>

        <div>
<?php if ($logged) { ?>
            <a href="index.php?controller=user&action=logout"><?php print $lang["user"]["logout"]; ?></a>
<?php } else { ?>
            <a href="index.php?controller=user&action=login"><?php print $lang["user"]["login"]; ?></a>
<?php } ?>
            <br>
            <a href="index.php?controller=user&action=create"><?php print $lang["user"]["register"]; ?></a>
        </div>
<?php

require "footer.php";

?>

<?php

require "header.php";
require "sidebar.php";

?>

        <div>
            <a href="index.php?action=changeLanguage&lang=es">ES</a> | <a href="index.php?action=changeLanguage&lang=en">EN</a>
        </div>

        <div>
<?php if ($logged) { ?>
            <h3><?php print $username; ?></h3>
            <a href="index.php?controller=user&action=logout"><?php print $lang["user"]["logout"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=delete&login=<?php print $username; ?>"><?php print $lang["user"]["delete"]; ?></a>
<?php } else { ?>
            <a href="index.php?controller=user&action=login"><?php print $lang["user"]["login"]; ?></a>
            <br>
            <a href="index.php?controller=user&action=create"><?php print $lang["user"]["register"]; ?></a>
<?php } ?>
        </div>
<?php

require "footer.php";

?>

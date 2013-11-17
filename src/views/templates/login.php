<?php

require "header.php";
require "sidebar.php";

?>
        <div class="login">
        <form action="index.php" method="post" accept-charset="utf-8">
            <input type="hidden" name="controller" value="user" />
            <input type="hidden" name="action" value="login" />

            <label for="login"><?php print $lang["user"]["username"]; ?></label>
            <input type="text" name="login" id="login"><br>

            <label for="password"><?php print $lang["user"]["password"]; ?></label>
            <input type="password" name="password" id="password"><br>

            <input type="submit" value="<?php print $lang["user"]["login"]; ?>">
        </form>
        </div>
<?php

require "footer.php";

?>

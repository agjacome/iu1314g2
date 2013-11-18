<?php

require "header.php";
require "sidebar.php";

?>
        <div class="register">
        <form action="index.php" method="post" accept-charset="utf-8">
            <input type="hidden" name="controller" value="user" />
            <input type="hidden" name="action" value="create" />

            <label for="login"><?php print $lang["user"]["username"]; ?></label>
            <input type="text" name="login" id="login"><br>

            <label for="password"><?php print $lang["user"]["password"]; ?></label>
            <input type="password" name="password" id="password"><br>

            <label for="verifyPassword"><?php print $lang["user"]["repeat_pass"]; ?></label>
            <input type="password" name="verifyPassword" id="verifyPassword"><br>

            <label for="email"><?php print $lang["user"]["email"]; ?></label>
            <input type="text" name="email" id="email"><br>

            <label for="name"><?php print $lang["user"]["name"]; ?></label>
            <input type="text" name="name" id="name"><br>

            <label for="address"><?php print $lang["user"]["address"]; ?></label>
            <input type="text" name="address" id="address"><br>

            <label for="telephone"><?php print $lang["user"]["telephone"]; ?></label>
            <input type="text" name="telephone" id="telephone"><br>

            <input type="submit" value="<?php print $lang["user"]["register"]; ?>">
        </form>
        </div>
<?php

require "footer.php";

?>

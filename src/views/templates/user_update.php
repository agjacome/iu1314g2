        <div class="register">
        <form action="index.php" method="post" accept-charset="utf-8">
            <input type="hidden" name="controller" value="user" />
            <input type="hidden" name="action" value="update" />

            <label for="login"><?php print $lang["user"]["username"]; ?></label>
            <input type="text" name="login" id="login" value="<?php print $login; ?>" readonly><br>

<?php if ($userrole === "admin") { ?>
            <label for="role"><?php print $lang["user"]["role"]; ?></label>
            <select name="role">
                <option value="admin" <?php if ($role === "admin") print "selected"; ?>>Administrador</option>
                <option value="usuario" <?php if ($role === "usuario") print "selected"; ?>>Usuario</option>
            </select><br>
<?php } ?>

            <label for="password"><?php print $lang["user"]["password"]; ?></label>
            <input type="password" name="password" id="password"><br>

            <label for="verifyPassword"><?php print $lang["user"]["repeat_pass"]; ?></label>
            <input type="password" name="verifyPassword" id="verifyPassword"><br>

            <label for="email"><?php print $lang["user"]["email"]; ?></label>
            <input type="text" name="email" id="email" value="<?php print $email; ?>"><br>

            <label for="name"><?php print $lang["user"]["name"]; ?></label>
            <input type="text" name="name" id="name" value="<?php print $name; ?>"><br>

            <label for="address"><?php print $lang["user"]["address"]; ?></label>
            <input type="text" name="address" id="address" value="<?php print $address; ?>"><br>

            <label for="telephone"><?php print $lang["user"]["telephone"]; ?></label>
            <input type="text" name="telephone" id="telephone" value="<?php print $telephone; ?>"><br>

            <input type="submit" value="<?php print $lang["user"]["update"]; ?>">
        </form>
        </div>

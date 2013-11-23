<div class="contact_form">
    <div class="center_title_bar"><?php print $lang ["user"]["update"]; ?></div>

    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">

            <form action="index.php" name="modifyuser" method="post" accept-charset="utf-8">
                <div class="contact_form">

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["username"]; ?></strong></label>
                        <input type="text" name="login" class="contact_input" value="<?php print $user->getLogin() ?>" readonly />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["new_pass"]; ?></strong></label>
                        <input type="password" name="password" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["repeat_pass"]; ?></strong></label>
                        <input type="password" name="verifyPassword" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["email"]; ?></strong></label>
                        <input type="text" name="email" class="contact_input" value="<?php print $user->email; ?>" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["name"]; ?></strong></label>
                        <input type="text" name="name" class="contact_input" value="<?php print $user->name; ?>" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["address"]; ?></strong></label>
                        <input type="text" name="address" class="contact_input" value="<?php print $user->address; ?>" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["telephone"]; ?></strong></label>
                        <input type="text" name="telephone" class="contact_input" value="<?php print $user->telephone; ?>" />
                    </div>

                    <?php if ($this->isAdmin()) { ?>
                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["role"]; ?></strong></label>
                        <select name="role" class="contact_input" />
                            <option <?php if ($user->role === "usuario") print "selected=\"selected\""; ?> value="usuario"><?php print $lang["user"]["role_user"]; ?></option>
                            <option <?php if ($user->role === "admin")   print "selected=\"selected\""; ?> value="admin"><?php print $lang["user"]["role_admin"]; ?></option>
                        </select>
                    </div>
                    <?php } ?>

                </div>
                <div class="form_row">
                    <input type="button" onClick="modifyuserr();" value="<?php print $lang["general"]["accept"]; ?>" class="contact">
                </div>
                <input type="hidden" name="controller" value="user" />
                <input type="hidden" name="action" value="update" />
            </form>
        </div>
    <div class="bottom_prod_box_big"></div>
</div> <!-- end of center content -->

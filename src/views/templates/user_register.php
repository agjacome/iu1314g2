<div class="contact_form">
<div class="center_title_bar"><?php print $lang["user"]["register"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <form action="index.php" method="post" accept-charset="utf-8">
                <div class="contact_form">
                    <input type="hidden" name="controller" value="user" />
                    <input type="hidden" name="action" value="create" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["username"]; ?></strong></label>
                        <input type="text" name="login" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["password"]; ?></strong></label>
                        <input type="password" name="password" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["repeat_pass"]; ?></strong></label>
                        <input type="password" name="verifyPassword" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["email"]; ?></strong></label>
                        <input type="text" name="email" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["name"]; ?></strong></label>
                        <input type="text" name="name" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["address"]; ?></strong></label>
                        <input type="text" name="address" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["telephone"]; ?></strong></label>
                        <input type="text" name="telephone" class="contact_input" />
                    </div>

                </div>
                <div class="form_row">
                    <input type="submit" value="<?php print $lang["general"]["accept"]; ?>" class="contact">
                </div>
            </form>
        </div>
        <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->

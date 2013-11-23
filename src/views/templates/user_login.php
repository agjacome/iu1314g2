<div class="contact_form">
<div class="center_title_bar"><?php print $lang["user"]["login"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form method="post" action="index.php" accept-charset="utf-8">
                    
                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["username"]; ?></strong></label>
                        <input type="text" name="login" class="contact_input" />
                    </div>
                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["user"]["password"]; ?></strong></label>
                        <input type="password" name="password" class="contact_input" />
                    </div>
                    <input type=submit value=<?php print $lang["general"]["accept"]?>>
                    <input type="hidden" name="controller" value="user" />
                    <input type="hidden" name="action" value="login" />
                </form>
            </div>
        </div>
    <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->

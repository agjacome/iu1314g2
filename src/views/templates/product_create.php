<div class="contact_form">
<div class="center_title_bar"><?php print $lang["product"]["new"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <form action="index.php" method="post" accept-charset="utf-8">
                <div class="contact_form">
                    <input type="hidden" name="controller" value="product" />
                    <input type="hidden" name="action" value="create" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["product"]["name"]; ?></strong></label>
                        <input type="text" name="name" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["product"]["descr"]; ?></strong></label>
                        <textarea rows="8" cols="50" name="description"></textarea>
                    </div>

                </div>
                <div class="form_row">
                    <input type="submit" value="<?php print $lang["general"]["accept"]; ?>" class="contact">
                </div>
            </form>
        </div>
        <div class="bottom_prod_box_big"></div>
    </div>
</div><!-- end of center content -->

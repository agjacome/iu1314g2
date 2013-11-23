<div class="contact_form">
<div class="center_title_bar"><?php print $lang["store"]["changecomm"]; ?></div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <div class="contact_form">
                <form method="post" action="index.php" accept-charset="utf-8" name="com">
                    <input type="hidden" name="controller" value="store" />
                    <input type="hidden" name="action" value="changeCommission" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang["store"]["commission"]; ?></strong></label>
                        <input type="text" name="commission" class="contact_input" value="<?php print $commission; ?>" />
                    </div>

                    <input type="button" onClick="changeComm()" value="Aceptar">
                </form>
            </div>
        </div>
    <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->

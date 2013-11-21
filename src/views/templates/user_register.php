<div class="contact_form">
<div class="center_title_bar"><?php print $lang ["user"]["modificar_datos_usuario"]; ?>Modificar datos usuario</div>
    <div class="prod_box_big">
        <div class="top_prod_box_big"></div>
        <div class="center_prod_box_big">
            <form action="index.php" method="post" accept-charset="utf-8">
                <div class="contact_form">
                    <input type="hidden" name="controller" value="user" />
                    <input type="hidden" name="action" value="create" />

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["usuario"]; ?></strong></label>
                        <input type="text" name="login" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["pass"]; ?></strong></label>
                        <input type="password" name="password" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["repetir_pass"]; ?></strong></label>
                        <input type="password" name="verifyPassword" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["correo"]; ?></strong></label>
                        <input type="text" name="email" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["nombre"]; ?></strong></label>
                        <input type="text" name="name" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["direccion"]; ?></strong></label>
                        <input type="text" name="address" class="contact_input" />
                    </div>

                    <div class="form_row">
                        <label class="contact"><strong><?php print $lang ["user"]["telefono"]; ?></strong></label>
                        <input type="text" name="telephone" class="contact_input" />
                    </div>

                </div>
                <div class="form_row">
                    <input type="submit" value="<?php print $lang ["botones"]["aceptar"]; ?>" class="contact">
                </div>
            </form>
        </div>
        <div class="bottom_prod_box_big"></div>
</div><!-- end of center content -->

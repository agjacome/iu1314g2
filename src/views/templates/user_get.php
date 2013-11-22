<div class="center_content">
    <div class="center_title_bar"><?php print $lang ["user"]["details"]; ?></div>

    <ul class="list">
        <li class="even"><?php print "<strong>" . $lang["user"]["username"]  . ":</strong> " . $user->getLogin(); ?></li>
        <li class="even"><?php print "<strong>" . $lang["user"]["email"]     . ":</strong> " . $user->email; ?></li>
<?php if ($this->isAdmin()) { ?>
        <li class="even"><?php print "<strong>" . $lang["user"]["role"]      . ":</strong> " . $user->role; ?></li>
<?php } ?>
        <li class="even"><?php print "<strong>" . $lang["user"]["name"]      . ":</strong> " . $user->name; ?></li>
        <li class="even"><?php print "<strong>" . $lang["user"]["address"]   . ":</strong> " . $user->address; ?></li>
        <li class="even"><?php print "<strong>" . $lang["user"]["telephone"] . ":</strong> " . $user->telephone; ?></li>
        <li class="odd">
            <a href="/index.php?controller=user&action=update&login=<?php print $user->getLogin(); ?>">
                <?php print $lang["user"]["update"]; ?>
            </a>
        </li>
    </ul>


</div> <!-- end of center content -->

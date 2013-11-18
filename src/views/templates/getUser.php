<?php
require "header.php";
require "sidebar.php";
?>

<div id="user">
    <?php print $lang["user"]["username"]  . ": " . $login;     ?><br>
    <?php print $lang["user"]["role"]      . ": " . $role;      ?><br>
    <?php print $lang["user"]["email"]     . ": " . $email;     ?><br>
    <?php print $lang["user"]["name"]      . ": " . $name;      ?><br>
    <?php print $lang["user"]["address"]   . ": " . $address;   ?><br>
    <?php print $lang["user"]["telephone"] . ": " . $telephone; ?><br>
</div>

<?php
require "footer.php";
?>

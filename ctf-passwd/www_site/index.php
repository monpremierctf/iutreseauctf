<!DOCTYPE html>
<html lang="fr">
<head>
  <title>CTF: Passw0rds</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <link rel="stylesheet" href="/yoloctf/style.css">
</head>
<body>

<!--- Page Header  -->

<div class="col-sm-4 text-center">
    <?php
    include "passwd_utils.php";

    if (($_POST['login']=="admin") && ($_POST['password']=="admin")) {
        passwd_access_ok("img/ok_1.gif", "Flag_C3st_0ouv3rt");
    } else {
        passwd_login("Authentification V1.0", "img/guard_1.jpg");
    }
    ?>
</div>
</body>
<?php
if (isset($_POST['pass'])){
    if($_POST['pass'] == "Mirculcol"){
        ?>BRAVO LE PASS EST flag{Mirculcol}
<?php
    }
    else{
        echo '<body onLoad="alert(\'MAUVAIS PASS\')">';
    }
}
?>


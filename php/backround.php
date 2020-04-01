<?php
//pas besoin de demarrarer un session car toujours appelé après lancement dans autres php
if (!isset($_SESSION['NbWal'])) {
    $_SESSION['NbWal'] = random_int(0, 2);
}


echo "<style type=text/css>body {background-image: url(../image/insert_backround".$_SESSION['NbWal'].".jpg);}</style>";

?>
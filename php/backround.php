<?php
//pas besoin de demarrarer un session car toujours appelé après lancement dans autres php
if (!isset($_SESSION['NbWal'])) {
    $_SESSION['NbWal'] = random_int(0, 2);
}
switch ($_SESSION['NbWal']) {
    case 0:
        echo "<style type=text/css>body {background-image: url(../image/insert_backround0.jpg);}</style>";
        break;
    case 1:
        echo "<style type=text/css>body {background-image: url(../image/insert_backround1.jpg);}</style>";
        break;
    case 2:
        echo "<style type=text/css>body {background-image: url(../image/insert_backround2.jpg);}</style>";
        break;
}
?>
<?php
    require_once 'inc/admin.php';

    $deleteQuery=$db->prepare('DELETE FROM events WHERE event_id=?');
    $deleteQuery->execute(array($_GET['id']));

    header('Location: index.php');
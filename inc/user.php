<?php
session_start();

require_once 'db.php';

#region Check if user is valid
if(!empty($_SESSION['user_id'])) {
    $userQuery = $db->prepare('SELECT * FROM users WHERE user_id=? AND active=1 LIMIT 1');
    $userQuery->execute([
        $_SESSION['user_id']
    ]);
    $currentUser=$userQuery->fetch(PDO::FETCH_ASSOC);
    if ($userQuery->rowCount() != 1) {
        session_destroy();
        header('Location: index.php');
        die();
    }
}
#endregion Check if user is valid
<?php
    require_once 'inc/user.php';

    $registeredQuery=$db->prepare('SELECT * FROM attend WHERE user_id=:id AND event_id=:event_id');
    $registeredQuery->execute([
        ':id'=>$_SESSION['user_id'],
        ':event_id'=>$_GET['id']
    ]);
    $_SESSION['alreadyRegistered'] = $registeredQuery->fetch(PDO::FETCH_ASSOC);
    if($registeredQuery->rowCount()>0){
        header('refresh: 3; url=./index.php');
        die('Already registered to this event');
    }else{
        $attendQuery=$db->prepare('INSERT INTO attend (user_id, event_id) VALUES (:user_id,:event_id)');
        $attendQuery->execute([
            ':user_id'=>$_SESSION['user_id'],
            ':event_id'=>$_GET['id']
        ]);
        header('Location: index.php');
    }
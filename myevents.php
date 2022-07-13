<?php
    require_once 'inc/user.php';

    $myEventsQuery = $db->prepare('SELECT * FROM attend JOIN events USING (event_id) WHERE user_id=:user_id');
    $myEventsQuery->execute([
        ':user_id'=>$_SESSION['user_id']
    ]);

    $events = $myEventsQuery->fetchAll(PDO::FETCH_ASSOC);

    include 'inc/header.php';

    if (!empty($events)){
        echo '<h2>You\'re registered for these events:</h2>';
        echo '<table class="table table-striped">';
        echo '<tr>';
        echo '<td>Name</td>';
        echo '<td>Description</td>';
        echo '<td>Date</td>';
        echo '</tr>';
        foreach ($events as $event){
            echo '<tr>';
            echo '<td>'.htmlspecialchars($event['name']).'</td>';
            echo '<td>'.nl2br(htmlspecialchars($event['description'])).'</td>';
            echo '<td>'.date('d.m.Y' ,strtotime($event['date'])).'</td>';
            echo '<tr>';
        }
        echo '</table>';
    } else {
        echo '<h1>You\'re not registered to any event! <a href="index.php">Choose and register now!</a></h1>';
    }
    echo '<a href="index.php" class="btn btn-secondary my-3">Go to the main page</a>';


    include 'inc/footer.php';

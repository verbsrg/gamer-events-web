<?php
    include 'path/to/autoload.php';


    $fb = new Facebook\Facebook([
        'app_id' => 'APP_ID_HERE',
        'app_secret' => 'APP_SECRET_HERE',
        'default_graph_session' => 'v4.0',
    ]);
<?php
    require 'user.php';

    if(empty($currentUser) || $currentUser['role']!='admin'){
        header('refresh: 3; url=./index.php');
        die('This page is available only for administrators. Redirecting to homepage...');
    }
<?php
    $db = new PDO('mysql:host=HOSTNAME;dbname=DBNAME;charset=utf8', 'login', 'password');

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
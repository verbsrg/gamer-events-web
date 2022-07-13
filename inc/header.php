<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events for gamers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>
<body>
<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom bg-light">
    <h1 class="text-black text-start m-1 p-1">Events for gamers</h1>
    <div class="col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <?php
            if(!empty($_SESSION['user_id'])){
                echo '<strong>'.htmlspecialchars($_SESSION['user_name']).'</strong>';
                echo ' ';
                echo '<a href="logout.php" class="btn btn-danger">Logout</a>';
                echo '<a href="myevents.php" class="btn btn-info mx-2">My events</a>';
            }else{
                echo '<a href="login.php" class="m-1 btn btn-dark">Login</a>';
                echo ' <a href="registration.php" class="m-2 btn btn-dark">Register</a>';
            }
        ?>
    </div>
</header>
<main class="container">
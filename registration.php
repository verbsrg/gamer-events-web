<?php
    require_once 'inc/user.php';

    if(!empty($_SESSION['user_id'])){
        header('Location: index.php');
        exit();
    }


    $errors=[];
    if(!empty($_POST)){
        $name=trim(@$_POST['name']);
        if(empty($name)){
            $errors['name']='Please enter your name';
        }
        $surname=trim(@$_POST['surname']);
        if(empty($surname)){
            $errors['surname']='Please enter your surname';
        }
        $email=trim(@$_POST['email']);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email']='Please enter valid e-mail address';
        }else{
            $mailQuery=$db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1;');
            $mailQuery->execute([
               ':email'=>$email
            ]);
            if($mailQuery->rowCount()>0){
                $errors['email']='E-mail address you entered is already assigned to an existing account';
            }
        }
        $password=@$_POST['password'];
        if (strlen($password) < 8){
            $errors['password']='Password should be at least 8 characters long';
        }
        if ($password !== @$_POST['password2']){
            $errors['password2']='Passwords don\'t match!';
        }
        if(empty($errors)){
            $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

            $query=$db->prepare('INSERT INTO users (name,surname,email,password,active) VALUES (:name,:surname,:email,:password,1);');
            $query->execute([
                    ':name'=>$name,
                    ':surname'=>$surname,
                    ':email'=>$email,
                    ':password'=>$password
            ]);
            // login after registration complete
            $_SESSION['user_id']=$db->lastInsertId();
            $_SESSION['user_name']=$name;

            header('Location: index.php');
            exit();
        }
    }

    include 'inc/header.php';

    ?>

    <form method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?' is-invalid':'')?>" />
            <?php
                echo (!empty($errors['name'])?'<div class="invalid-feedback">'.$errors['name'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="surname">Surname:</label>
            <input type="text" name="surname" id="surname" required class="form-control <?php echo (!empty($errors['surname'])?' is-invalid':'')?>"/>
            <?php
                echo (!empty($errors['surname'])?'<div class="invalid-feedback">'.$errors['surname'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo (!empty($errors['email'])?' is-invalid':'')?>"/>
            <?php
                echo (!empty($errors['email'])?'<div class="invalid-feedback">'.$errors['email'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo (!empty($errors['password'])?' is-invalid':'')?>"/>
            <?php
                echo (!empty($errors['password'])?'<div class="invalid-feedback">'.$errors['password'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password2">Repeat password:</label>
            <input type="password" name="password2" id="password2" required class="form-control <?php echo (!empty($errors['password2'])?' is-invalid':'')?>"/>
            <?php
                echo (!empty($errors['password2'])?'<div class="invalid-feedback">'.$errors['password2'].'</div>':'');
            ?>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-light">Login</a>
        <a href="index.php" class="btn btn-light">Go back</a>
    </form>

<?php
    include 'inc/footer.php';
?>

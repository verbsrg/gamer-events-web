<?php
    require_once 'inc/user.php';
    require_once 'inc/facebook.php';

    if(!empty($_SESSION['user_id'])){
        header('Location: index.php');
        exit();
    }

    $errors=false;
    if(!empty($_POST)){
        #region validate form
        $userQuery=$db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $userQuery->execute([
            ':email'=>trim($_POST['email'])
        ]);
        if($user=$userQuery->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($_POST['password'],$user['password'])){
                $_SESSION['user_id']=$user['user_id'];
                $_SESSION['user_name']=$user['name'];
                header ('Location: index.php');
                exit();
            }else{
                $errors=true;
            }
        }else{
            $errors=true;
        }
        #endregion validate form
    }
    #region login with fb
    $fbHelper = $fb->getRedirectLoginHelper();

    $permissions = ['email'];
    $callbackUrl = htmlspecialchars('https://eso.vse.cz/~vers01/events/fb-callback.php');

    $fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permissions);
    #endregion login with fb

    include 'inc/header.php';
    ?>
<h2>Login</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control<?php echo ($errors?' is-invalid':''); ?>" value="<?php echo htmlspecialchars(@$_POST['email'])?>"/>
            <?php
                echo (!empty($errors)?'<div class="invalid-feedback">Wrong e-mail and password combination.</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required class="form-control<?php echo ($errors?' is-invalid':''); ?>" />
        </div>
        <br>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="<?php echo $fbLoginUrl?>" class="btn btn-danger">Login With Facebook!</a>
            <a href="registration.php" class="btn btn-light">Register</a>
            <a href="index.php" class="btn btn-light">Cancel</a>
    </form>
<?php
    include 'inc/footer.php';

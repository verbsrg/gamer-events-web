<?php
    require_once 'inc/user.php';

    use PHPMailer\PHPMailer\PHPMailer;

    if(!empty($_SESSION['user_id'])){
        header('Location: index.php');
        exit();
    }
    $errors=false;
    if (!empty($_POST) && !empty($_POST['email'])){
        $userQuery=$db->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $userQuery->execute([
           ':email'=>trim($_POST['email'])
        ]);
        if ($user=$userQuery->fetch(PDO::FETCH_ASSOC)){

            $code='xx'.rand(100000,993952);

            $mailer = new PHPMailer(false);
            $mailer->isSendmail();

            $mailer->addAddress($user['email'],$user['name']);
            $mailer->setFrom('');

            $mailer->isHTML(true);
            $mailer->Body='<html><head><meta charset="utf-8"/></head><body>In order to renew your password, please click this link: <a href="'.$link.'">'.$link.'</a></body></html>';

            $mailer->send();

            header('Location: forgottenpassword.php?mailed=ok');
        }
    }
    include 'inc/header.php';
    ?>

    <h2>Forgotten password change</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo ($errors?'is-invalid':''); ?>"
                   value="<?php echo htmlspecialchars(@$_POST['email']) ?>"/>
            <?php
                echo ($errors?'<div class="invalid-feedback">Invalid e-mail.</div>':'');
            ?>
        </div>
        <button type="submit" class="btn btn-primary">Send code</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>

<?php
    include 'inc/footer.php';

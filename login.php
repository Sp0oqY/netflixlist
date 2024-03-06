<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require './app/controllers/Db.php';

if (isset($_SESSION['id'])) {
    header('Location: /rocnikovy/profile');
    exit();
}

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');
$chyba = '';
if (isset($_POST['login'])) {
    if ($_POST['username'] && $_POST['password']) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $user = Db::queryOne('
                    SELECT password, username, id
                    FROM users
                    WHERE `username`=?', $username);

        if (!$user || !password_verify($password, $user['password'])) {
            $chyba = 'Your e-mail or password is incorrect';
        } else {
            $_SESSION['id'] = $user['id'];
            header('Location: /rocnikovy/profile');
            exit();
        }
    } else {
        $chyba = 'All fields must be filled out';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./assets/includes/head.php" ?>
</head>

<body>
    <section id="pic">
        <?php require_once "./assets/includes/header.php" ?>
        <div class="row h-75" >
            <div class="col-12 d-flex flex-column justify-content-center align-items-center">
                <div class="loginbox">
                    <a href="/rocnikovy/index"><img class="avatar" src="https://yt3.ggpht.com/ytc/AKedOLQoagIeVndJduQpYCRRxmbxbd_plCRTj6l0Rd-2=s900-c-k-c0x00ffffff-no-rj" alt=""></a>
                    <h1>Login Here</h1>
                    <form action="./login" method="POST">
                        <span class="vypis"><?php echo ($chyba); ?></span>
                        <p>Username</p>
                        <input type="text" name="username" placeholder="Enter Username">
                        <p>Password</p>
                        <input type="password" name="password" placeholder="Enter Password">

                        <a href="#">Lost your password?</a> <br>
                        <a href="SignUp.html">Don't you have an account?</a>

                        <input type="submit" name="login" value="Login">
                    </form>
                </div>
            </div>
        </div>
        <?php require_once "./assets/includes/footer.php" ?>
    </section>
</body>

</html>
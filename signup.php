<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
error_reporting(E_ALL);

require './app/controllers/Db.php';

if (isset($_SESSION['id'])) {
    header('Location: /rocnikovy/profile');
    exit();
}

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');



$chyba = '';
$chyba_username = '';
$chyba_email = '';
$chyba_password = '';


if (isset($_POST['signup'])) {
    if (!$_POST['username']) {
        $chyba_username = 'Username cannot be blank';
    }
    $username = trim($_POST['username']);
    if (!$_POST['email']) {
        $chyba_email = 'Email cannot be blank';
    } else {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $chyba_email = 'Email is not valid';
        }
    }
    if (!$_POST['password']) {
        $chyba_password = 'Password cannot be blank';
    } else {
        if ($_POST['password2']) {
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if ($password == $password2) {
                $number = preg_match('@[0-9]@', $password);
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);

                if (strlen($password) < 8 || !$number || !$uppercase || !$lowercase) {
                    $chyba_password = 'Heslo musí obsahovať aspoň 1 čislo, malé a veľké písmeno';
                }
            } else {
                $chyba_password = 'Password does not match';
            }
        } else {
            $chyba_password = 'Check password';
        }
    }

    if ($chyba_username == '' && $chyba_email == '' && $chyba_password == '') {
        $existujeMeno = DB::querySingle(
            'SELECT COUNT(*) 
                FROM users
                WHERE username=?
                LIMIT 1',
            $_POST['username']
        );
        $existujeEmail = DB::querySingle(
            'SELECT COUNT(*) 
                FROM users
                WHERE `email`=?
                LIMIT 1',
            $_POST['email']
        );

        if ($existujeMeno) {
            $chyba = 'Zadané meno už existuje';
        } else if ($existujeEmail) {
            $chyba = 'Pre zadaný e-mail už je vytvorený účet';
        } else {
            $heslo = password_hash($password, PASSWORD_DEFAULT);
            Db::query('INSERT INTO users (username, email, password) 
            VALUES (?, ?, ?)', $username, $email, $heslo);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./assets/includes/head.php" ?>
</head>

<body>
    <div id="pic">
        <?php require_once "./assets/includes/header.php" ?>
        <div class="loginbox">
            <a href="home.html"><img class="avatar" src="https://yt3.ggpht.com/ytc/AKedOLQoagIeVndJduQpYCRRxmbxbd_plCRTj6l0Rd-2=s900-c-k-c0x00ffffff-no-rj" alt=""></a>
            <h1>SignUp Here</h1>
            <span><?= $chyba ?></span>
            <form action="./signup" class="form" id="form" method="POST">

                <p>Username</p>
                <small class="error"><?= $chyba_username ?></small>
                <input name="username" type="text" placeholder="Enter Username" id="username">

                <p>E-mail</p>
                <small class="error"><?= $chyba_email ?></small>
                <input name="email" type="text" placeholder="Enter E-mail" id="email">
                
                <p>Password</p>
                <small class="error"><?= $chyba_password ?></small>
                <input name="password" type="password" placeholder="Enter Password" id="password">
                
                <p>Password check</p>
                <input name="password2" type="password" placeholder="Enter Password" id="password2">

                <p id="acc">Do you have an account? <a href="/rocnikovy/login">(Click here)</a></p>
                <input type="submit" name="signup" value="Sign Up">
            </form>
        </div>
        <?php require_once "./assets/includes/footer.php" ?>
    </div>
</body>

</html>
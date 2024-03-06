<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
error_reporting(E_ALL);

require './app/controllers/Db.php';

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');

if (isset($_SESSION['id'])) {
    $userid = $_SESSION['id'];
    $user = Db::queryOne("SELECT * FROM users WHERE id = ${userid};");
} else {
    header('Location: /rocnikovy/profile');
    exit();
}

$chyba = '';
$chyba_username = '';
$chyba_email = '';
$chyba_password = '';
$chyba_oldpassword = '';

if (isset($_SESSION['edit_success'])) {
    $edit_success = $_SESSION['edit_success'];
    unset($_SESSION['edit_success']);
}
if (isset($_SESSION['change_success'])) {
    $change_success = $_SESSION['change_success'];
    unset($_SESSION['change_success']);
}


if (isset($_POST['edit'])) {
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
    }

    if ($chyba_username == '' && $chyba_email == '' && $chyba_password == '') {
        if (password_verify($_POST['password'], $user['password'])) {
            Db::query("UPDATE `users` SET `username`='${username}',`email`='${email}' WHERE `id`='${userid}';");
            $_SESSION['edit_success'] = "Data has been successfully changed";
            header("Location: /rocnikovy/settings");
        } else {
            $chyba_password = 'Password is incorrect';
            var_dump("TEDT");
        }
    }
}

if (isset($_POST['change'])) {
    if (!$_POST['old_password']) {
        $chyba_oldpassword = 'Old password cannot be blank';
    }
    if (!$_POST['password']) {
        $chyba_password = 'Password cannot be blank';
    }
    if (!$_POST['password_confirm']) {
        $chyba_password = 'Password confirmation cannot be blank';
    }

    $password = $_POST['password'];
    $password2 = $_POST['password_confirm'];

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

    if ($chyba_oldpassword == '' && $chyba_password == '') {
        if (password_verify($_POST['old_password'], $user['password'])) {
            $heslo = password_hash($password, PASSWORD_DEFAULT);
            Db::query("UPDATE `users` SET `password`='${heslo}' WHERE `id`='${userid}';");
            $_SESSION['change_success'] = "Heslo bolo uspešne zmenené";
            header("Location: /rocnikovy/settings");
        } else {
            $chyba_oldpassword = 'Password is incorrect';
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
        <h1>Settings</h1>
        <span><?= $chyba ?></span>
        <div class="row">
            <div class="col-6">
                <section class="loginbox">
                    <h3>Change data form</h3>

                    <form action="./settings" class="form" id="form" method="POST">

                        <p>Username</p>
                        <input name="username" type="text" value="<?= $user['username'] ?>" placeholder="Enter Username" id="username">
                        <small class="error"><?= $chyba_username ?></small>

                        <p>E-mail</p>
                        <input name="email" type="text" value="<?= $user['email'] ?>" placeholder="Enter E-mail" id="email">
                        <small class="error"><?= $chyba_email ?></small>

                        <p>Password</p>
                        <input name="password" type="password" placeholder="Enter Password" id="password">
                        <small class="error"><?= $chyba_password ?></small>
                        <?php if (isset($edit_success)) : ?>
                            <small class="success"><?= $edit_success ?></small>
                        <?php endif ?>

                        <input type="submit" name="edit" value="Change data">
                    </form>
                </section>
            </div>

            <div class="col-6">
                <section class="loginbox">
                    <h3>Change password form</h3>
                    <form action="./settings" class="form" id="passwordform" method="POST">

                        <p>Old password</p>
                        <input name="old_password" type="password" placeholder="Enter Password" id="password">
                        <small class="error"><?= $chyba_oldpassword ?></small>

                        <p>New password</p>
                        <input name="password" type="password" placeholder="Enter Password" id="password">
                        <small class="error"><?= $chyba_password ?></small>

                        <p>Password Confirm</p>
                        <input name="password_confirm" type="password" placeholder="Enter Password" id="password">

                        <?php if (isset($change_success)) : ?>
                            <small class="success"><?= $change_success ?></small>
                        <?php endif ?>
                        <input type="submit" name="change" value="Change password">
                    </form>
                </section>
            </div>
        </div>
        <?php require_once "./assets/includes/footer.php" ?>
    </div>
</body>

</html>
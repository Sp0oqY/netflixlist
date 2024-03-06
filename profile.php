<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: /rocnikovy/login');
    exit();
}

require './app/controllers/Db.php';

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');

$user = Db::queryOne('
                SELECT password, username, id, image
                FROM users
                WHERE `id`=?', $_SESSION['id']);

$userid = $user['id'];
if (isset($_GET['user'])) {
    $search = $_GET['user'];
    if ($search == $user['username']) {
        header("Location: /rocnikovy/profile");
    }
    $user = Db::queryOne("SELECT username, id, image FROM `users` WHERE username LIKE '%${search}%'");
    $userid = $user['id'];
}
if (isset($_FILES['image'])) {
    $errors = array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));


    $newname = $user['username'] . '-' . time() . '.' . $file_ext;

    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size must be excately 2 MB';
    }

    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "./assets/images/users/" . $newname);
        Db::query("UPDATE `users` SET `image`='${newname}' WHERE `id`='${userid}';");
    }
}
if (isset($_POST['follow'])) {
    $follower = $_POST['user'];
    if ($follower != $userid) {
        $followed = Db::queryOne("SELECT * FROM `follows` WHERE `users_id` = '${userid}' AND `follower_id` = '${follower}'");
        if (!$followed) {
            Db::query("INSERT INTO `follows` (users_id, follower_id) VALUES ('${userid}','${follower}')");
        }
    }
}

$count = Db::queryGroup("SELECT category,COUNT(*) AS count FROM `watchlist` WHERE `users_id`='${userid}' GROUP BY category;");
$following = Db::queryAll("SELECT users.image, users.username FROM `follows` LEFT JOIN `users` ON follows.follower_id=users.id WHERE follows.users_id='${userid}'");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./assets/includes/head.php" ?>
</head>

<body>
    <section id="pic">
        <?php require_once "./assets/includes/header.php" ?>

        <div class="row">
            <div class="col-12">
                <section class="statistics">
                    <div class="row">
                        <div class="col-4">

                        </div>

                        <div class="col-4">
                            <h2>Statistics</h2>
                        </div>

                        <div class="col-4">
                            <form action="/rocnikovy/profile" method="get" class="search-box">
                                <button class="btn-search"><i class="fas fa-search"></i></button>
                                <input type="search" class="input-search" name="user" required placeholder="Type to Search...">
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row cont">
            <div class="col-4">
                <section class="content">
                    <h3><?= ucfirst($user['username']) ?>'s Profile</h3>
                    <form action="/rocnikovy/profile" method="post" enctype="multipart/form-data" class="center">
                        <?php if (!isset($_GET['user'])) : ?>
                            <label for="file-ip-1">Upload Image</label>
                            <button type="submit" name="profile">Set Up Image</button>
                        <?php endif ?>

                        <div class="form-input">
                            <input type="file" id="file-ip-1" accept="image/*" name="image" onchange="showPreview(event);">
                            <div class="preview">
                                <img id="file-ip-1-preview" src="./assets/images/users/<?= $user['image'] ?>">
                            </div>
                            <?php if (isset($_GET['user'])) : ?>
                                <form action="/rocnikovy/profile" method="post">
                                    <input type="hidden" name="user" value="<?= $user['id'] ?>">
                                    <button type="submit" name="follow">Follow</button>
                                </form>
                            <?php endif ?>
                        </div>
                    </form>
                </section>
            </div>

            <div class="col-4">
                <section class="content">
                    <h3>Movie Stats</h3>
                    <div class="row ">
                        <div class="col-6 category">
                            <p> <i id="W" class="fas fa-arrow-circle-right"></i> &nbsp; Watching</p>
                            <p> <i id="C" class="fas fa-arrow-circle-right"></i> &nbsp; Completed</p>
                            <p> <i id="D" class="fas fa-arrow-circle-right"></i> &nbsp; Dropped</p>
                            <p> <i id="P" class="fas fa-arrow-circle-right"></i> &nbsp; Plan To Watch</p>
                        </div>

                        <div class="col-6  category">
                            <p><i id="W" class="fas fa-arrow-circle-right"></i> &nbsp; <?= isset($count['watching']) ? $count['watching'][0]['count'] : '0' ?></p>
                            <p><i id="C" class="fas fa-arrow-circle-right"></i> &nbsp; <?= isset($count['completed']) ? $count['completed'][0]['count'] : '0' ?></p>
                            <p><i id="D" class="fas fa-arrow-circle-right"></i> &nbsp; <?= isset($count['dropped']) ? $count['dropped'][0]['count'] : '0' ?></p>
                            <p><i id="P" class="fas fa-arrow-circle-right"></i> &nbsp; <?= isset($count['planned']) ? $count['planned'][0]['count'] : '0' ?></p>
                        </div>
                    </div>

                    <a class="mylistBtn" href="/rocnikovy/mylist<?= isset($_GET['user']) ? '?user=' . $user['username'] : '' ?>"><button><?= isset($_GET['user']) ? ucfirst($user['username']) . "'s LIST" : 'MY LIST' ?></button></a>
                </section>
            </div>

            <div class="col-4">
                <section class="content">
                    <h3>Friends</h3>
                    <?php foreach ($following as $follow) : ?>
                        <div class="friends">
                            <a href="/rocnikovy/profile?user=<?= $follow['username'] ?>">
                                <img src="./assets/images/users/<?= $follow['image'] ?>" alt="">
                                <p><?= ucfirst($follow['username']) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </section>
            </div>
        </div>

        <?php require_once "./assets/includes/footer.php" ?>
    </section>

    <script src="./assets/js/profile.js?verison=2"></script>
</body>

</html>
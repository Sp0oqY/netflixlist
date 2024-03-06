<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: /rocnikovy/login');
    exit();
}

require './app/controllers/Db.php';

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');

$userid = $_SESSION['id'];
if(isset($_GET['user'])){
    $search = $_GET['user'];
    $user = Db::queryOne("SELECT username, id, image FROM `users` WHERE username LIKE '%${search}%'");
    $userid = $user['id'];
}

if(isset($_GET['category'])){
    $category = $_GET['category'];
    $movies = Db::queryAll("SELECT * FROM `watchlist` INNER JOIN `movies` ON watchlist.movies_id = movies.id WHERE `users_id`='${userid}' AND `category`='${category}'");
} else {
    $movies = Db::queryAll("SELECT * FROM `watchlist` INNER JOIN `movies` ON watchlist.movies_id = movies.id WHERE `users_id`='${userid}'");
}

if(isset($_POST['rating'])){
    $rating = $_POST['rating'];
    $movie = $_POST['movie'];
    DB::query("UPDATE `watchlist` SET `rating`='${rating}' WHERE `movies_id`='${movie}'");
}
if(isset($_POST['category'])){
    $category = $_POST['category'];
    $movie = $_POST['movie'];
    DB::query("UPDATE `watchlist` SET `category`='${category}' WHERE `movies_id`='${movie}'");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once "./assets/includes/head.php" ?>
</head>
<body>
<?php require_once "./assets/includes/header.php" ?>

    <div class="row">
        <div class="col-12">
            <div class="backgr">
                <div class="main2">
                    <h1><?= isset($_GET['user']) ? ucfirst($user['username']) . "'s List" : 'MY LIST' ?></h1>
                    <div class="row">
                        <div class="col-12">
                            <img id="titObr" src="https://www.mojandroid.sk/wp-content/uploads/2018/10/Netflix-logo-and-screen.jpg" alt="">
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-12">
                            <nav id="kategorie">
                                <a id="all" class="<?= !isset($_GET['category']) ? 'active' : '' ?>" href="/rocnikovy/mylist<?= isset($_GET['user']) ? '?user=' . $user['username'] : '' ?>">All</a>
                                <a id="watch" class="<?= (isset($_GET['category']) && $_GET['category'] == 'watching') ? 'active' : '' ?>" href="/rocnikovy/mylist?category=watching<?= isset($_GET['user']) ? '&user=' . $user['username'] : '' ?>">Watching</a>
                                <a id="comp"  class="<?= (isset($_GET['category']) && $_GET['category'] == 'completed') ? 'active' : '' ?>" href="/rocnikovy/mylist?category=completed<?= isset($_GET['user']) ? '&user=' . $user['username'] : '' ?>" href="/rocnikovy/mylist?category=completed">Completed</a>
                                <a id="drop"  class="<?= (isset($_GET['category']) && $_GET['category'] == 'dropped') ? 'active' : '' ?>" href="/rocnikovy/mylist?category=dropped<?= isset($_GET['user']) ? '&user=' . $user['username'] : '' ?>" href="/rocnikovy/mylist?category=dropped">Dropped</a>
                                <a id="plan"  class="<?= (isset($_GET['category']) && $_GET['category'] == 'planned') ? 'active' : '' ?>" href="/rocnikovy/mylist?category=planned<?= isset($_GET['user']) ? '&user=' . $user['username'] : '' ?>" href="/rocnikovy/mylist?category=planned">Plan To Watch</a>
                            </nav>
                    
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="main-header">
                                <h3><?= isset($_GET['category']) ? ucfirst($_GET['category']) : 'ALL' ?></h3>
                            </div>
                        </div>
                    </div>
                    <table class="table table-dark movie-box ">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>                            
                                <th scope="col">Title</th>
                                <th scope="col">Rating</th>
                                <th scope="col">Stauts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($movies as $key => $item): ?>
                                <tr>
                                    <th scope="row"><?= $key+1 ?></th>
                                    <td><img class="myListImg" src="<?= $item['image']?>" alt=""></td>
                                    
                                    <td><a class="title" href="/rocnikovy/detail?movie=<?= $item['id'] ?>"><?= $item['name'] ?></a></td>
                                    <td>
                                        <?php if(isset($_GET['user'])): ?>
                                            <p><?= is_null($item['rating']) ? '1' : $item['rating'] ?></p>
                                        <?php else: ?>
                                        <form action="/rocnikovy/mylist" method="post">
                                            <input type="hidden" name="movie" value="<?= $item['id'] ?>">
                                            <input type="range" name="rating" min="1" max="10" step="1" value="<?= is_null($item['rating']) ? '1' : $item['rating'] ?>" id="" onchange="this.form.submit()">
                                        </form>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                    <?php if(isset($_GET['user'])): ?>
                                            <p><?= ucfirst($item['category']) ?></p>
                                        <?php else: ?>
                                            <form action="/rocnikovy/mylist" method="post">
                                            <input type="hidden" name="movie" value="<?= $item['id'] ?>">
                                            <select name="category" id="" onchange="this.form.submit()">
                                                <option <?= $item['category'] == 'watching' ? 'selected' : ''?> value="watching">Watching</option>
                                                <option <?= $item['category'] == 'completed' ? 'selected' : ''?> value="completed">Completed</option>
                                                <option <?= $item['category'] == 'dropped' ? 'selected' : ''?> value="dropped">Dropped</option>
                                                <option <?= $item['category'] == 'planned' ? 'selected' : ''?> value="planned">Plan to watch</option>
                                            </select>
                                        </form>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
            
        </div>
    </div>
    <?php require_once "./assets/includes/footer.php" ?>
</body>
</html>
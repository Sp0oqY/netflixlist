<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require './app/controllers/Db.php';
require './app/controllers/DataController.php';

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');
$genres = Db::queryAll("SELECT * FROM `genres`;");

if (isset($_GET['search']) || isset($_GET['category']) || isset($_GET['genre']) || isset($_GET['all'])) {
  switch ($_GET) {
    case !empty($_GET['search']):
      $search = $_GET['search'];
      $movies = Db::queryAll("SELECT movies.id,movies.name,movies.description,movies.image,GROUP_CONCAT(genres.name SEPARATOR ' / ') AS allGenres FROM `movies` LEFT JOIN `movie_genres` ON movies.id = movie_genres.movie_id LEFT JOIN `genres` ON movie_genres.genres_skr = genres.skr WHERE movies.name LIKE '%${search}%' GROUP BY movies.name  ORDER BY RAND();");
      break;
    case !empty($_GET['category']):
      $category = $_GET['category'];
      $movies = Db::queryAll("SELECT movies.id,movies.name,movies.description,movies.image,GROUP_CONCAT(genres.name SEPARATOR ' / ') AS allGenres FROM `movies` LEFT JOIN `movie_genres` ON movies.id = movie_genres.movie_id LEFT JOIN `genres` ON movie_genres.genres_skr = genres.skr WHERE `type` = '${category}'  GROUP BY movies.name ORDER BY RAND();");
      break;
    case !empty($_GET['genre']):
      $genre = $_GET['genre'];
      $movies = Db::queryAll("SELECT movies.id,movies.name,movies.description,movies.image,GROUP_CONCAT(genres.name SEPARATOR ' / ') AS allGenres FROM `movies` LEFT JOIN `movie_genres` ON movies.id = movie_genres.movie_id LEFT JOIN `genres` ON movie_genres.genres_skr = genres.skr WHERE movie_genres.genres_skr = '${genre}' GROUP BY movies.name ORDER BY RAND();");
      break;
    case isset($_GET['all']):
      $movies = Db::queryAll("SELECT movies.id,movies.name,movies.description,movies.image,GROUP_CONCAT(genres.name SEPARATOR ' / ') AS allGenres FROM `movies` LEFT JOIN `movie_genres` ON movies.id = movie_genres.movie_id LEFT JOIN `genres` ON movie_genres.genres_skr = genres.skr GROUP BY movies.name ORDER BY RAND();");
      break;
  }
} else {
  $movies = Db::queryAll("SELECT movies.id,movies.name,movies.description,movies.image,GROUP_CONCAT(genres.name SEPARATOR ' / ') AS allGenres FROM `movies`LEFT JOIN `movie_genres` ON movies.id = movie_genres.movie_id LEFT JOIN `genres` ON movie_genres.genres_skr = genres.skr GROUP BY movies.name ORDER BY RAND() LIMIT 30;");
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
      <section class="filter">
        <a href="/rocnikovy/list?category=s">Series</a>
        <a href="/rocnikovy/list?category=m">Movies</a>
        <form action="/rocnikovy/list" method="get" class="search-box">
          <button class="btn-search"><i class="fas fa-search"></i></button>
          <input type="search" class="input-search" name="search" placeholder="Type to Search...">
        </form>

        <form action="/rocnikovy/list">
          <select name="genre" id="genre" onchange="this.form.submit()">
            <option default selected disabled>Genres</option>
            <?php foreach ($genres as $item) : ?>
              <option <?= (!empty($genre) && $item['skr'] == $genre) ? 'selected' : '' ?> value="<?= $item['skr'] ?>"><?= $item['name'] ?></option>
            <?php endforeach ?>
          </select>
        </form>

        <a href="/rocnikovy/list?all">All</a>

      </section>
    </div>
  </div>

  <!--MOVIE-->
  <div class="movies">
    <?php foreach ($movies as $movie) : ?>
      <div class="movie" data-movie="<?= $movie['id'] ?>" method="POST">
        <img class="movie-img" src="<?= $movie['image'] ?>" alt="">
        <h3 class="movie-title"><?= $movie['name'] ?></h3>
      </div>
    <?php endforeach ?>
  </div>

  <?php require_once "./assets/includes/footer.php" ?>

  <script src="./assets/js/list.js?ver=<?= time() ?>"></script>

</body>

</html>
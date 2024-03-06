<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['id'])){
    header('Location: /rocnikovy/login');
    exit();
}

require './app/controllers/Db.php';

Db::connect('127.0.0.1', 'mkas_rocnikovy', 'softlukas.sk', 'data23Lk03');

if(isset($POST['send']))
{
    $name = $_POST['name'];
    $description = $_POST['descriptio'];
    $image = $_POST['image'];
    $type = $_POST['type'];

    Db::query('INSERT INTO `movies` (id, name, description, image, type) VALUES (?, ?, ?, ?, ?)', $id, $name, $description, $image, $type);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once "./assets/includes/head.php" ?>
</head>
<body>
<?php require_once "./assets/includes/header.php" ?>

<form class="container" action="./admin" method="POST">
          <section class="contact-box">
            <div class="right">
              <h1>Admin form</h1>

              <label for="name">Name</label>
              <input type="text" name="name" id="name" class="field" placeholder="Enter the name of movie">
              <small class="error"></small>
  
              <label for="email">Description</label>
              <textarea id="message" name="message" class="field" placeholder="Enter description of the movie"></textarea>
              <small class="error"></small>
  
              <label for="subject">Image</label>
              <input type="text" name="subject" id="subject" class="field" placeholder="Enter the image of the movie">
              <small class="error"></small>
  
              <label for="message">Type</label>
              <input type="text" name="type" id="email" class="field" placeholder="Enter the type of the movie">
              <small class="error"></small>
  
              <div class="center">  
                <input id="btn" type="submit" name="send" value="SEND MESSAGE"> 
              </div>
            </div>
          </section>
        </form>
<?php require_once "./assets/includes/footer.php" ?>
</body>
</html>
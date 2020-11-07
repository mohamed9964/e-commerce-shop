<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title><?php getTitle(); ?></title>
  <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $css; ?>font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css">
  <link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css">
  <link rel="stylesheet" href="<?php echo $css; ?>frontend.css">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;900&display=swap" rel="stylesheet">
</head>

<body>
  <div class="upper-bar">
    <div class="container">
      <?php if (isset($_SESSION['user'])) {
        $getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
        $getUser->execute(array($sessionUser));
        $info = $getUser->fetch();
        $pic = $info['profile'];
        if (empty($pic)) {
          echo "<img class='my-image img-thumbnail img-circle' src='admin/uploads/profile_pic/avatar3.jpg' alt='Profile Picture'>";
        } else {
          echo "<img class='my-image img-thumbnail img-circle' src='admin/uploads/profile_pic/" . $info['profile'] . "' alt='Profile Picture'>";
        }
      ?>
        <div class="btn-group my-info">
          <span class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <?php echo $sessionUser ?>
            <span class="caret"></span>
          </span>
          <ul class="dropdown-menu">
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="newad.php">New Item</a></li>
            <li><a href="profile.php#my-ads">My Items</a></li>
            <li><a href="profile.php#my-fav">My Favorite Items</a></li>
            <li><a href="logout.php">logout</a></li>
          </ul>
        </div>
        <a class="pull-right btn btn-warning" href="mycart.php?do=shop" role="button"><i class="fa fa-shopping-cart"></i> My Cart</a>
        <a class="pull-right btn btn-info" href="contact.php" role="button" style="margin-right: 10px;"><i class="fa fa-phone"></i> Contact Us</a>
      <?php
      } else {
      ?>
        <a class="pull-right btn btn-success" href="login.php" role="button"><i class="fa fa-sign-in"></i> Login/Signup</a>
        <a class="pull-left btn btn-info" href="contact.php" role="button"><i class="fa fa-phone"></i> Contact Us</a>
      <?php } ?>
    </div>
  </div>
  <nav class="navbar navbar-inverse">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">HomePage</a>
      </div>
      <div class="nav navbar-nav navbar-right search">
        <form class="form-inline" action="search.php" method="POST">
          <input class="form-control mr-sm-2" name="name" type="search" placeholder="Search about Item" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
      <div class="collapse navbar-collapse" id="app-nav">
        <ul class="nav navbar-nav navbar-right">
          <?php
          $allCats = getAllFrom("*", "categories", "where Parent = 0", "", "ID", "ASC");
          foreach ($allCats as $cat) {
            echo '<li>
                      <a href="categories.php?pageid=' . $cat['ID'] . '">
                      ' . $cat['Name'] . '
                      </a>
                    </i>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
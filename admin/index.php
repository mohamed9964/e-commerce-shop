<?php
$noNavbar = '';
$pageTitle = 'Login';
session_start();
if (isset($_SESSION['Username'])) {
  header('Location: dashboard.php'); // Redirect To Dashboard Page
}
 include 'init.php';

// Check If User Coming HTTP Request

 if($_SERVER['REQUEST_METHOD'] == 'POST') {
   $username = $_POST['user'];
   $password = $_POST['pass'];
   $hashedPass = sha1($password);

   // Check If THe User Exit In SQLiteDatabase

   $stmt = $con->prepare("SELECT
                                UserID,Username, Password
                           FROM
                                users
                           WHERE
                                Username = ?
                           AND
                                Password = ?
                           AND
                                GroupID = 1
                           LIMIT 1");
   $stmt->execute(array($username, $hashedPass));
   $row =$stmt->fetch();
   $count  = $stmt->rowCount();
   // IF Count > 0 This Mean The Databas Contain Record About This Username
   if($count > 0){
     $_SESSION['Username'] = $username;
     $_SESSION['ID'] = $row['UserID'];
     header('Location: dashboard.php');
     exit();
   }

 }
?>
<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <h4 class="text-center">Admin Login</h4>
  <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off">
  <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password">
  <input class="btn btn-primary btn-block" type="submit" value="login">
</form>
<?php include $tpl . 'footer.php';?>

<?php
ob_start();
session_start();
$pageTitle = 'Login';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
}
include 'init.php';

// Check If User Coming HTTP Request

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPass = sha1($password);
        $stmt = $con->prepare("SELECT
                                    UserID, Username, Password
                            FROM
                                    users
                            WHERE
                                    Username = ?
                            AND
                                    Password = ?");
        $stmt->execute(array($username, $hashedPass));
        $user = $stmt->fetch();
        $count  = $stmt->rowCount();
        // IF Count > 0 This Mean The Databas Contain Record About This Username
        if ($count > 0) {
            $_SESSION['user'] = $username;
            $_SESSION['userid'] = $user['UserID'];
            header('Location: index.php');
            exit();
        }
    } else {
        $formErrors = array();
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];
        if (isset($username)){
            $filterUser = filter_var($username, FILTER_SANITIZE_STRING);
            if (strlen($filterUser) < 4) {
                $formErrors[] = 'Username Must Be Larger Than 4 Characters';
            }
        }
        if (isset($password) && isset($password2)) {
            if (empty($password)) {
                $formErrors[] = 'Sorry Password Cant Be Empty';
            }
            if (sha1($password) !== sha1($password2)) {
                $formErrors[] = 'Sorry Password Is Not Match';
            }
        }
        if (isset($email)) {
            $filterdEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true) {
                $formErrors[] = 'This Email Is Not Valid';
            }
        }
        if (empty($formErrors)) {
            // Check If User Exist In Database
            $check = checkItem("Username", "users", $username);
            if ($check == 1) {
                $formErrors[] = 'Sorry This User Is Exits';
            } else {
                // Insert Userinfo In Database
                $stmt = $con->prepare("INSERT INTO
                                    users(Username, Password, Email, RegStatus,Date)
                                    VALUES(:zuser, :zpass, :zmail, 0,now())");
                $stmt->execute(array(
                    'zuser' => $username,
                    'zpass' => sha1($password),
                    'zmail' => $email
                ));
                // Echo Success Message
                $succesMsg = 'Congrats You Are Now Registerd User';
            }
        }
    }
}
?>
<div class="container login-page">
    <h1 class="text-center">
        <span class="blue selected" data-class="login">Login</span> |
        <span data-class="signup">SignUp</span>
    </h1>
    <!-- Start Login Form -->
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
            <input class="form-control" type="text" name="username" autocomplete="off" placeholder="Enter your username" required>
        </div>
        <div class="input-container">
            <input class="form-control" type="password" name="password" autocomplete="new-password" placeholder="Enter your password" required>
        </div>
        <input class="btn btn-primary btn-block" name="login" type="submit" value="Login">
    </form>
    <!-- End Login Form -->
    <!-- Start Signup Form -->
    <form class="signup" action="" method="POST">
        <div class="input-container">
            <input pattern=".{4,}" title="Username Must Be 4 Chars" class="form-control" type="text" name="username" autocomplete="off" placeholder="Enter your username" required>
        </div>
        <div class="input-container">
            <input minlength="4" class="form-control" type="password" name="password" autocomplete="new-password" placeholder="Type a Complex password" required>
        </div>
        <div class="input-container">
            <input minlength="4" class="form-control" type="password" name="password2" autocomplete="new-password" placeholder="Type a password again" required>
        </div>
        <div class="input-container">
            <input class="form-control" type="email" name="email" placeholder="Enter a Valid email">
        </div>
        <input class="btn btn-success btn-block" name="signup" type="submit" value="Signup">
    </form>
    <!-- End Signup Form -->
    <div class="the-errors text-center">
        <?php
            if (!empty($formErrors)) {
                foreach ($formErrors as $error) {
                    echo '<div class="msg error">' . $error . '</div>';
                }
            }
            if (isset($succesMsg)) {
            echo '<div class="msg success">' . $succesMsg . '</div>';
            } 
        ?>
    </div>
</div>
<?php
include $tpl . 'footer.php';
ob_end_flush();
?>
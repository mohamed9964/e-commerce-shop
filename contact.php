<?php
ob_start();
session_start();
$pageTitle = 'Contact-US';
include 'init.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user   = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email  = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone  = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
    $msg    = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    // creating array of errors
    $formErrors = array();
    if (strlen($user) <= 3) {
        $formErrors[] = 'Username Must be Larger Than 3 Characters';
    }
    if (strlen($msg) < 10) {
        $formErrors[] = 'Message Can\'t be Less Than 10 Characters';
    }
    // if no errors send the email
    $headers ='From: ' . $email . '\r\n';
    $myEmail = 'm.hanyy996@gmail.com';
    $subject = 'Contact Form';
    if (empty($formErrors)){
        mail($myEmail, $subject, $msg, $headers);
        header("Location: index.php");
    }
}
?>
<!-- Start Form -->
<div class="container contact-us">
    <h1 class="text-center">Contact Us</h1>
    <form class="contact-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <?php if (!empty($formErrors)) { ?>
            <div class="alert alert-danger alert-dismissible" role="start">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php
                foreach ($formErrors as $error) {
                    echo $error . '<br>';
                }
                ?>
            </div>
        <?php } ?>
        <div class="form-group">
            <input class="form-control" type="text" name="username" placeholder="Type Your Username" autocomplete="off" value="<?php if (isset($user)) {echo $user;} ?>">
            <i class="fa fa-user fa-fw"></i>
        </div>
        <div class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Please Type a Valid Email" value="<?php if (isset($email)) {echo $email;} ?>">
            <i class="fa fa-envelope fa-fw"></i>
        </div>
        <div class="form-group">
            <input class="form-control" type="text" name="phone" placeholder="Type Your Phone Number" autocomplete="off" value="<?php if (isset($phone)) {echo $phone;} ?>">
            <i class="fa fa-phone fa-fw"></i>
        </div>
        <div class="form-group">
            <textarea class="form-control" name="message" placeholder="Enter Your Message!"><?php if (isset($msg)) {echo $msg;} ?></textarea>
        </div>
            <input class="btn btn-success" type="submit" value="Send Message">
            <i class="fa fa-send fa-fw send-icon"></i>
    </form>
</div>
<!-- End Form -->
<?php
include $tpl . 'footer.php';
ob_end_flush();
?>
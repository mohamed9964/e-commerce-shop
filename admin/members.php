<?php

/*
      ================================================
      == Manage Members Page
      == You Can Add | Edit | Delete Members Here
      ================================================
      */
ob_start(); // Output Buffering Start
session_start();
if (isset($_SESSION['Username'])) {
  $pageTitle = 'Members';
  include 'init.php';
  $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
  // Start Manage Page
  if ($do == 'Manage') { // Manage Page
    $query = '';
    if (isset($_GET['page']) && $_GET['page'] == 'Pending') {
      $query = 'And RegStatus = 0';
    }
    // Select All User Except Admin
    $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");
    // Execute The Statement
    $stmt->execute();
    // Assign To Variable
    $rows = $stmt->fetchAll();
    if (!empty($rows)) {
?>
      <h1 class="text-center">Manage Member</h1>
      <div class="container">
        <div class="table-responsive">
          <table class="main-table manage-members text-center table table-bordered">
            <tr>
              <td>#ID</td>
              <td>Profile Picture</td>
              <td>Username</td>
              <td>Email</td>
              <td>Full Name</td>
              <td>Registerd Date</td>
              <td>Control</td>
            </tr>
            <?php
            foreach ($rows as $row) {
              echo "<tr>";
              echo "<td>" . $row['UserID'] . "</td>";
              echo "<td>";
              if (empty($row['profile'])){
                echo "<img src='uploads/profile_pic/avatar3.jpg' alt='Profile Picture'>";
              } else {
                echo "<img src='uploads/profile_pic/" . $row['profile'] . "' alt='Profile Picture'>";
              }
              echo "</td>";
              echo "<td>" . $row['Username'] . "</td>";
              echo "<td>" . $row['Email'] . "</td>";
              echo "<td>" . $row['FullName'] . "</td>";
              echo "<td>" . $row['Date'] . "</td>";
              echo "<td>
                <a href='members.php?do=Edit&userid=" . $row['UserID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                <a href='members.php?do=Delete&userid=" . $row['UserID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
              if ($row['RegStatus'] == 0) {
                echo "<a href='members.php?do=Activate&userid=" . $row['UserID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Activate</a>";
              }
              echo "</td>";
              echo "</tr>";
            }
            ?>
          </table>
        </div>
        <a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Member</a>
      </div>
    <?php } else {
      echo '<div class="container">';
      echo '<div class="nice-message">There\'s No Members To Manage</div>';
      echo '<a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Member</a>';
      echo '</div>';
    }
    ?>
  <?php } elseif ($do == 'Add') { //Add Members Page 
  ?>
    <h1 class="text-center">Add New Member</h1>
    <div class="container">
      <form class="form-horizontal" action="?do=Insert" method="post" enctype="multipart/form-data">
        <!-- Start Username Field -->
        <div class="form-group form-group-lg">
          <label class="col-sm-2 control-label">Username</label>
          <div class="col-sm-10 col-md-6">
            <input type="text" name="username" class="form-control" autocomplete="off" required placeholder="Username To Login Into Shop">
          </div>
        </div>
        <!-- End Username Field -->
        <!-- Start Password Field -->
        <div class="form-group form-group-lg">
          <label class="col-sm-2 control-label">Password</label>
          <div class="col-sm-10 col-md-6">
            <input type="password" name="password" class="password form-control" autocomplete="new-password" required placeholder="Password Must Be Hard & Complex">
            <i class="show-pass fa fa-eye fa-2x"></i>
          </div>
        </div>
        <!-- End Password Field -->
        <!-- Start Email Field -->
        <div class="form-group form-group-lg">
          <label class="col-sm-2 control-label">Email</label>
          <div class="col-sm-10 col-md-6">
            <input type="email" name="email" class="form-control" required placeholder="Email Must Be Valid">
          </div>
        </div>
        <!-- End Email Field -->
        <!-- Start Full Name Field -->
        <div class="form-group form-group-lg">
          <label class="col-sm-2 control-label">Full Name</label>
          <div class="col-sm-10 col-md-6">
            <input type="text" name="full" class="form-control" required placeholder="Full Name Appear In Your Profile Page">
          </div>
        </div>
        <!-- End Full Name Field -->
        <!-- Start Profile Picture Field -->
        <div class="form-group form-group-lg">
          <label class="col-sm-2 control-label">User Profile</label>
          <div class="col-sm-10 col-md-6">
            <input type="file" name="profile" class="form-control">
          </div>
        </div>
        <!-- End Profile Picture Field -->
        <!-- Start submit Field -->
        <div class="form-group form-group-lg">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" value="Add Member" class="btn btn-primary btn-lg">
          </div>
        </div>
        <!-- End submit Field -->
      </form>
    </div>
    <?php
  } elseif ($do == 'Insert') { // Insert Page
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      echo '<h1 class="text-center">Insert Member</h1>';
      echo "<div class= 'container'>";
      // Upload Variable
      $profileName      = $_FILES['profile']['name'];
      $profileSize      = $_FILES['profile']['size'];
      $profileTmp       = $_FILES['profile']['tmp_name'];
      $profileType      = $_FILES['profile']['type'];
      // list of file to upload 
      $profAllowExt = array("jpeg", "jpg", "png", "gif");
      // get variable extension
      $file_parts = explode('.', $_FILES['profile']['name']);
      $profExt = strtolower(end($file_parts));
      // Get Variables From The Form
      $user   = $_POST['username'];
      $pass   = $_POST['password'];
      $email  = $_POST['email'];
      $name   = $_POST['full'];
      $hashPass = sha1($_POST['password']);
      // Validate The Form
      $formErrors = array();
      if (strlen($user) < 4 && !empty($user)) {
        $formErrors[] = 'Username Cant Be Less Than <strong>4 Characters</strong>';
      }
      if (strlen($user) > 20) {
        $formErrors[] = 'Username Cant Be More Than <strong>20 Characters</strong>';
      }
      if (empty($user)) {
        $formErrors[] = 'Username Cant Be <strong>Empty</strong>';
      }
      if (empty($pass)) {
        $formErrors[] = 'Password Cant Be <strong>Empty</strong>';
      }
      if (empty($name)) {
        $formErrors[] = 'Full Name Cant Be <strong>Empty</strong>';
      }
      if (empty($email)) {
        $formErrors[] = 'Email Cant Be <strong>Empty</strong>';
      }
      if (! empty($profileName) && ! in_array($profExt, $profAllowExt)) {
        $formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
      }
      if (empty($profileName)) {
        $formErrors[] = 'Profile Picture Is <strong>Required</strong>';
      }
      if ($profileSize > 4194304) {
        $formErrors[] = 'Profile Picture Cant Be Larger Than <strong>4MB</strong>';
      }
      // Loop Into Errors Array And Echo It
      foreach ($formErrors as $error) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
      }
      // check If There 's No Error Proceed The Update Operation
      if (empty($formErrors)) {
        $profile = rand(0, 10000) . '_' . $profileName;
        move_uploaded_file($profileTmp, "uploads\profile_pic\\" . $profile);
        // Check If User Exist In Database
        $check = checkItem("Username", "users", $user);
        if ($check == 1) {
          $theMsg = '<div class="alert alert-danger">sorry Username Exist ... Enter New Username</div>';
          redirectHome($theMsg, 'back');
        } else {
          // Insert Userinfo In Database
          $stmt = $con->prepare("INSERT INTO
                                    users(Username, Password, Email, FullName, RegStatus, Date, profile)
                                    VALUES(:zuser, :zpass, :zmail, :zname, 1,now(), :zprofile)");
          $stmt->execute(array(
            'zuser' => $user,
            'zpass' => $hashPass,
            'zmail' => $email,
            'zname' => $name,
            'zprofile' => $profile
          ));
          // Echo Success Message
          $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Inserted</div>';
          redirectHome($theMsg, 'back');
        }
      }
    } else {
      echo "<div class='container'>";
      $theMsg = '<div class="alert alert-danger">Sorry You cant Browse This Page Directly</div>';
      redirectHome($theMsg);
      echo "</div>";
    }
    echo "</div>";
  } elseif ($do == 'Edit') { // Edit Page
    // Check If Get Request UserID Is numeric & Get The Integer Value Of It
    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
    // Select All Data Depend On This ID
    $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
    // Execute Query
    $stmt->execute(array($userid));
    // Fetch The Data
    $row = $stmt->fetch();
    // The Row Count
    $count = $stmt->rowCount();
    // If There's Such ID Show The Form
    if ($count > 0) { ?>
      <h1 class="text-center">Edit Member</h1>
      <div class="container">
        <form class="form-horizontal" action="?do=Update" method="post">
          <input type="hidden" name="userid" value="<?php echo $userid; ?>">
          <!-- Start Username Field -->
          <div class="form-group form-group-lg">
            <label class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10 col-md-6">
              <input type="text" name="username" value="<?php echo $row['Username'] ?>" class="form-control" autocomplete="off" required>
            </div>
          </div>
          <!-- End Username Field -->
          <!-- Start Password Field -->
          <div class="form-group form-group-lg">
            <label class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10 col-md-6">
              <input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>">
              <input type="password" name="newpassword" class="form-control" autocomplete="new-password" placeholder="Leave Blank If Dont Want To Change">
            </div>
          </div>
          <!-- End Password Field -->
          <!-- Start Email Field -->
          <div class="form-group form-group-lg">
            <label class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10 col-md-6">
              <input type="email" name="email" value="<?php echo $row['Email'] ?>" class="form-control" required>
            </div>
          </div>
          <!-- End Email Field -->
          <!-- Start Full Name Field -->
          <div class="form-group form-group-lg">
            <label class="col-sm-2 control-label">Full Name</label>
            <div class="col-sm-10 col-md-6">
              <input type="text" name="full" value="<?php echo $row['FullName'] ?>" class="form-control" required>
            </div>
          </div>
          <!-- End Full Name Field -->
          <!-- Start submit Field -->
          <div class="form-group form-group-lg">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="submit" value="Save" class="btn btn-primary btn-lg">
            </div>
          </div>
          <!-- End submit Field -->
        </form>
      </div>
<?php
      // If There's Such ID Show Error Message
    } else {
      echo "<div class='container'>";
      $theMsg = "<div class='alert alert-danger'>Theres No Such ID</div>";
      redirectHome($theMsg);
      echo "</div>";
    }
  } elseif ($do == 'Update') { // Update Page
    echo '<h1 class="text-center">Update Member</h1>';
    echo "<div class= 'container'>";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Get Variables From The Form
      $id     = $_POST['userid'];
      $user   = $_POST['username'];
      $email  = $_POST['email'];
      $name   = $_POST['full'];
      // Password Trick
      $pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);
      // Validate The Form
      $formErrors = array();
      if (strlen($user) < 4 && !empty($user)) {
        $formErrors[] = 'Username Cant Be Less Than <strong>4 Characters</strong>';
      }
      if (strlen($user) > 20) {
        $formErrors[] = 'Username Cant Be More Than <strong>20 Characters</strong>';
      }
      if (empty($user)) {
        $formErrors[] = 'Username Cant Be <strong>Empty</strong>';
      }
      if (empty($name)) {
        $formErrors[] = 'Full Name Cant Be <strong>Empty</strong>';
      }
      if (empty($email)) {
        $formErrors[] = 'Email Cant Be <strong>Empty</strong>';
      }
      // Loop Into Errors Array And Echo It
      foreach ($formErrors as $error) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
      }
      // check If There 's No Error Proceed The Update Operation
      if (empty($formErrors)) {
        $stmt2 = $con->prepare("SELECT 
                                    *
                                FROM 
                                    users
                                WHERE
                                    Username = ?
                                AND
                                  UserID != ?");
        $stmt2->execute(array($user, $id));
        $count = $stmt2->rowCount();
        if ($count == 1) {
          $theMsg = '<div class="alert alert-danger">Sorry Username Exist ... Enter New Username</div>';
          redirectHome($theMsg, 'back');
        } else {
          $stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ?");
          $stmt->execute(array($user, $email, $name, $pass, $id));

          // Echo Success Message
          $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
          redirectHome($theMsg, 'back');
        }
      }
    } else {
      $theMsg = "<div class='alert alert-danger'>Sorry You Cant Brows This is Page Directly</div>";
      redirectHome($theMsg, 'back');
    }
    echo "</div>";
  } elseif ($do == 'Delete') { // Delete Member Page
    echo '<h1 class="text-center">Delete Member</h1>';
    echo "<div class= 'container'>";
    // Check If Get Request UserID Is numeric & Get The Integer Value Of It
    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
    // Select All Data Depend On This ID
    $check = checkItem('UserID', 'users', $userid);
    // If There's Such ID Show The Form
    if ($check > 0) {
      $stmt = $con->prepare("DELETE FROM users WHERE UserID = :userid");
      $stmt->bindParam(':userid', $userid);
      $stmt->execute();
      // Echo Success Message
      echo '<div class="container">';
      $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
      redirectHome($theMsg, 'back');
      echo '</div>';
    } else {
      echo "<div class='container'>";
      $theMsg = '<div class="alert alert-danger">This ID Is Not Exist</div>';
      redirectHome($theMsg);
      echo "</div>";
    }
    echo '</div>';
  } elseif ($do == 'Activate') {
    // Activate Member Page
    echo '<h1 class="text-center">Activate Member</h1>';
    echo "<div class= 'container'>";
    // Check If Get Request UserID Is numeric & Get The Integer Value Of It
    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
    // Select All Data Depend On This ID
    $check = checkItem('UserID', 'users', $userid);
    // If There's Such ID Show The Form
    if ($check > 0) {
      $stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");
      $stmt->execute(array($userid));
      // Echo Success Message
      echo '<div class="container">';
      $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Activated</div>';
      redirectHome($theMsg, 'back');
      echo '</div>';
    } else {
      echo "<div class='container'>";
      $theMsg = '<div class="alert alert-danger">This ID Is Not Exist</div>';
      redirectHome($theMsg);
      echo "</div>";
    }
    echo '</div>';
  }
  include $tpl . 'footer.php';
} else {
  header('Location: index.php');
  exit();
}
ob_end_flush(); // Release The Output
?>
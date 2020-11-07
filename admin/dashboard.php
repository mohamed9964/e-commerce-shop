<?php
session_start();

if (isset($_SESSION['Username'])) {

  $pageTitle = 'Dashboard';
  include 'init.php';
  $numUsers = 6; // Number of Latest Users
  $numItems = 6; // Number of Latest Items
  $numComments = 4; // Number of Latest Comments
  $latestUsers = getLatest('*', 'users', 'UserID', $numUsers); // Function To Get Latest Users
  $latestItems = getLatest('*', 'items', 'ItemID', $numItems); // Function To Get Latest Items
?>
  <div class="container home-stats text-center">
    <h1>Dashboard</h1>
    <div class="row">
      <div class="col-md-3">
        <div class="stat st-members">
          <i class="fa fa-users"></i>
          <div class="info">
            Total Members
            <span><a href="members.php"><?php echo countItems('UserID', 'users'); ?></a></span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat st-pending">
          <i class="fa fa-user-plus"></i>
          <div class="info">
            Pending Members
            <span><a href="members.php?do=Manage&page=Pending"><?php echo checkItem('RegStatus', 'users', 0); ?></a></span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat st-items">
          <i class="fa fa-tag"></i>
          <div class="info">
            Total Items
            <span><a href="items.php"><?php echo countItems('ItemID', 'items'); ?></a></span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat st-comments">
          <i class="fa fa-comments"></i>
          <div class="info">
            Total Comments
            <span><a href="comments.php"><?php echo countItems('C_ID', 'comments'); ?></a></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container latest">
    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <i class="fa fa-users"></i> Latest <?php echo $numUsers; ?> Registerd Users
            <span class="toggle-info pull-right">
              <i class="fa fa-plus fa-lg"></i>
            </span>
          </div>
          <div class="panel-body">
            <ul class="list-unstyled latest-users">
              <?php
              if (!empty($latestUsers)) {
                foreach ($latestUsers as $user) {
                  echo '<li>';
                  echo $user['Username'];
                  echo '<a href="members.php?do=Edit&userid=' . $user['UserID'] . '">';
                  echo '<span class="btn btn-success pull-right">';
                  echo '<i class="fa fa-edit"></i> Edit';
                  echo '</span>';
                  echo '</a>';
                  if ($user['RegStatus'] == 0) {
                    echo "<a href='members.php?do=Activate&userid=" . $user['UserID'] . "' class='btn btn-info pull-right'><i class='fa fa-check'></i> Activate</a>";
                  }
                  echo '</li>';
                }
              } else {
                echo "There's No Members To Show";
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <i class="fa fa-tag"></i> Latest <?php echo $numItems; ?> Registerd Items
            <span class="toggle-info pull-right">
              <i class="fa fa-plus fa-lg"></i>
            </span>
          </div>
          <div class="panel-body">
            <ul class="list-unstyled latest-users">
              <?php
              if (!empty($latestItems)) {
                foreach ($latestItems as $item) {
                  echo '<li>';
                  echo $item['Name'];
                  echo '<a href="items.php?do=Edit&itemid=' . $item['ItemID'] . '">';
                  echo '<span class="btn btn-success pull-right">';
                  echo '<i class="fa fa-edit"></i> Edit';
                  echo '</span>';
                  echo '</a>';
                  if ($item['Approve'] == 0) {
                    echo "<a href='items.php?do=Approve&itemid=" . $item['ItemID'] . "' class='btn btn-info pull-right activate'><i class='fa fa-check'></i> Approve</a>";
                  }
                  echo '</li>';
                }
              } else {
                echo "There's No Items To Show";
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Start Latest Comment -->
    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <i class="fa fa-comments-o"></i> Latest <?php echo $numComments; ?> Comments
            <span class="toggle-info pull-right">
              <i class="fa fa-plus fa-lg"></i>
            </span>
          </div>
          <div class="panel-body">
            <?php
            $stmt = $con->prepare("SELECT
                                        comments.*, users.Username AS Member
                                    FROM
                                        comments
                                    INNER JOIN
                                        users
                                    ON
                                        users.UserID = comments.User_id
                                    ORDER BY 
                                        C_id DESC    
                                    LIMIT $numComments");
            $stmt->execute();
            $comments = $stmt->fetchAll();
            if (!empty($comments)) {
              foreach ($comments as $comment) {
                echo '<div class="comment-box">';
                echo '<span class="member-n"><a href="members.php?do=Edit&userid=' . $comment['User_id'] . '">' . $comment['Member'] . '</a></span>';
                echo '<p class="member-c">' . $comment['Comment'] . '</p>';
                echo '</div>';
              }
            } else {
              echo "There's No Comments To Show";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- End Latest Comments -->
  </div>
  </div>
<?php
  include $tpl . 'footer.php';
} else {

  header('Location: index.php');

  exit();
}

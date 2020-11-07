<?php
ob_start();
session_start();
$pageTitle = 'Show Items';
include 'init.php';
// Check If Get Request Item Is numeric & Get The Integer Value Of It
$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
// Select All Data Depend On This ID
$stmt = $con->prepare("SELECT items.*,
                              categories.Name AS CategoryName,
                              users.Username  AS Member
                        FROM 
                            items
                        INNER JOIN 
                            categories
                        ON
                            categories.ID = items.Cat_ID
                        INNER JOIN 
                            users
                        ON
                            users.UserID = items.Member_ID     
                        WHERE 
                            ItemID = ?
                        AND
                            Approve = 1");
// Execute Query
$stmt->execute(array($itemid));
$count = $stmt->rowCount();
if ($count > 0) {
    // Fetch The Data
    $item = $stmt->fetch();
?>
    <h1 class="text-center"><?php echo $item['Name'] ?></h1>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php
                if (empty($item['Image'])) {
                    echo "<img class='img-responsive' src='admin/uploads/image_pic/images.jpg' alt='item img'>";
                } else {
                    echo "<img class='img-responsive' src='admin/uploads/image_pic/" . $item['Image'] . "' alt='item img'>";
                }
                ?>
            </div>
            <div class="col-md-9 item-info">
                <h2><?php echo $item['Name'] ?></h2>
                <p><?php echo $item['Description'] ?></p>
                <ul class="list-unstyled">
                    <li>
                        <i class="fa fa-calendar fa-fw"></i>
                        <span>Added Date</span> : <?php echo $item['Add_Date'] ?>
                    </li>
                    <li>
                        <i class="fa fa-money fa-fw"></i>
                        <span>Price</span> : $<?php echo $item['Price'] ?>
                    </li>
                    <li>
                        <i class="fa fa-building fa-fw"></i>
                        <span>Made In</span> : <?php echo $item['Country_Made'] ?>
                    </li>
                    <li>
                        <i class="fa fa-tags fa-fw"></i>
                        <span>Category</span> : <a href="categories.php?pageid=<?php echo $item['Cat_ID'] ?>"><?php echo $item['CategoryName'] ?></a>
                    </li>
                    <li>
                        <i class="fa fa-user fa-fw"></i>
                        <span>Added By</span> : <a href="#"><?php echo $item['Member'] ?></a>
                    </li>
                    <li class="tags-items">
                        <i class="fa fa-user fa-fw"></i>
                        <span>Tags</span> :
                        <?php
                        $allTags = explode(",", $item['tags']);
                        foreach ($allTags as $tag) {
                            $tag = str_replace(' ', '', $tag);
                            $lowertag = strtolower($tag);
                            if (! empty($tag)) {
                            echo "<a href='tags.php?name={$lowertag}'>" . $tag . "</a>";
                            }
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="custom-hr">
        <?php if (isset($_SESSION['user'])) { ?>
            <!-- Start Add Comment -->
            <div class="row">
                <div class="col-md-offset-3">
                    <div class="add-comment">
                        <h3>Add Your Comment</h3>
                        <form action="<?php $_SERVER['PHP_SELF'] . '?itemid=' . $item['ItemID'] ?>" method="POST">
                            <textarea name="comment" re></textarea>
                            <input class="btn btn-primary" type="submit" value="Add Comment">
                        </form>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
                            $itemid = $item['ItemID'];
                            $userid = $_SESSION['userid'];
                            if (!empty($comment)) {
                                $stmt = $con->prepare("INSERT INTO 
                                                            comments(Comment, Status, Comment_date, Item_id, User_id)
                                                        VALUES(:zcomment, 0, NOW(), :zitemid, :zuserid)");
                                $stmt->execute(array(
                                    'zcomment' => $comment,
                                    'zitemid'  => $itemid,
                                    'zuserid'  => $userid
                                ));
                                if ($stmt) {
                                    $theMsg =  '<div class="alert alert-success">Comment Added</div>';
                                    redirectHome($theMsg, 'back');
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <!-- Start Add Comment -->
        <?php } else {
            echo '<a href="login.php">Login</a> or <a href="login.php">Register</a> To Add Comment';
        } ?>
        <hr class="custom-hr">
        <?php
        $stmt = $con->prepare("SELECT 
                                        comments.*, users.Username AS Member,
                                        users.profile AS profile
                                    FROM 
                                        comments
                                    INNER JOIN
                                        users
                                    ON
                                        users.UserID = comments.User_id
                                    WHERE 
                                        Item_id = ? 
                                    AND
                                        Status = 1       
                                    ORDER BY C_id DESC");
        $stmt->execute(array($item['ItemID']));
        $comments = $stmt->fetchAll();
        foreach ($comments as $comment) { ?>
            <div class="comment-box">
                <div class="row">
                    <div class="col col-sm-2 text-center">
                        <?php
                        if (empty($comment['profile'])) {
                            echo "<img class='img-responsive img-thumbnail img-circle center-block' src='admin/uploads/profile_pic/avatar3.jpg' alt='Profile Picture'>";
                        } else {
                            echo "<img class='img-responsive img-thumbnail img-circle center-block' src='admin/uploads/profile_pic/" . $comment['profile'] . "' alt='Profile Picture'>";
                        }
                             echo $comment['Member'];
                          ?>
                    </div>
                    <div class="col col-sm-10">
                        <p class="lead"><?php echo $comment['Comment'] ?></p>
                    </div>
                </div>
            </div>
            <hr class="custom-hr">
        <?php } ?>
    </div>
<?php
} else {
    echo '<div class="container text-center">';
    $theMsg = '<div class="alert alert-danger">There\'s No Such ID Or This Is Waiting Approval</div>';
    redirectHome($theMsg, 'back');
    echo '</div>';
}
include $tpl . 'footer.php';
ob_end_flush();
?>
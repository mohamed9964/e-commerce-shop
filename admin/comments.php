<?php
/*
    ================================================
    == Comments Page
    ================================================
    */
ob_start(); // Output Buffering Start
session_start();
$pageTitle = 'Comments';
if (isset($_SESSION['Username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {
        $stmt = $con->prepare("SELECT 
                                    comments.*,items.Name AS Item_Name,
                                    users.Username AS Member
                                FROM 
                                    comments
                                INNER JOIN
                                    items
                                ON
                                    items.ItemID = comments.Item_id
                                INNER JOIN
                                    users
                                ON
                                    users.UserID = comments.User_id
                                ORDER BY C_id DESC");
        $stmt->execute();
        $comments = $stmt->fetchAll();
        if (!empty($comments)) {
?>
            <h1 class="text-center">Manage Comment</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Comment</td>
                            <td>Item Name</td>
                            <td>User Name</td>
                            <td>Added Date</td>
                            <td>Control</td>
                        </tr>
                        <?php
                        foreach ($comments as $comment) {
                            echo "<tr>";
                            echo "<td>" . $comment['C_ID'] . "</td>";
                            echo "<td>" . $comment['Comment'] . "</td>";
                            echo "<td>" . $comment['Item_Name'] . "</td>";
                            echo "<td><a href='members.php?do=Edit&userid=" . $comment['User_id'] . "'>" . $comment['Member'] . "</td>";
                            echo "<td>" . $comment['Comment_date'] . "</td>";
                            echo "<td>
                            <a href='comments.php?do=Edit&comid=" . $comment['C_ID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                            <a href='comments.php?do=Delete&comid=" . $comment['C_ID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                            if ($comment['Status'] == 0) {
                                echo "<a href='comments.php?do=Approve&comid=" . $comment['C_ID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        <?php } else {
            echo '<div class="container">';
                echo '<div class="nice-message">There\'s No Comments To Manage</div>';
            echo '</div>';
        }
        ?>
        <?php } elseif ($do == 'Edit') {
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
        $stmt = $con->prepare("SELECT * FROM comments WHERE C_ID = ? LIMIT 1");
        // Execute Query
        $stmt->execute(array($comid));
        // Fetch The Data
        $row = $stmt->fetch();
        // The Row Count
        $count = $stmt->rowCount();
        // If There's Such ID Show The Form
        if ($count > 0) { ?>
            <h1 class="text-center">Edit Comment</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="post">
                    <input type="hidden" name="comid" value="<?php echo $comid; ?>">
                    <!-- Start Comment Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10 col-md-6">
                            <textarea class="form-control" name="comment"><?php echo $row['Comment'] ?></textarea>
                        </div>
                    </div>
                    <!-- End CommentField -->
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
    } elseif ($do == 'Update') {
        echo '<h1 class="text-center">Update Comment</h1>';
        echo "<div class= 'container'>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get Variables From The Form
            $comid     = $_POST['comid'];
            $comment   = $_POST['comment'];
            // Update The Database With This apc_cache_info
            $stmt = $con->prepare("UPDATE comments SET Comment = ? WHERE C_ID = ?");
            $stmt->execute(array($comment, $comid));
            // Echo Success Message
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
            redirectHome($theMsg, 'back');
        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Cant Brows This is Page Directly</div>";
            redirectHome($theMsg, 'back');
        }
        echo "</div>";
    } elseif ($do == 'Delete') {
        echo '<h1 class="text-center">Delete Comment</h1>';
        echo "<div class= 'container'>";
        // Check If Get Request Com ID Is numeric & Get The Integer Value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
        // Select All Data Depend On This ID
        $check = checkItem('C_ID', 'comments', $comid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("DELETE FROM comments WHERE C_ID = :comid");
            $stmt->bindParam(':comid', $comid);
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
    } elseif ($do == 'Approve') {
        // Approve Member Page
        echo '<h1 class="text-center">Approve Member</h1>';
        echo "<div class= 'container'>";
        // Check If Get Request Com ID Is numeric & Get The Integer Value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
        // Select All Data Depend On This ID
        $check = checkItem('C_ID', 'comments', $comid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("UPDATE comments SET Status = 1 WHERE C_ID = ?");
            $stmt->execute(array($comid));
            // Echo Success Message
            echo '<div class="container">';
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Approved</div>';
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

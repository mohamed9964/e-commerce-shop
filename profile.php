<?php
ob_start();
session_start();
$pageTitle = 'Profile';
include 'init.php';
if (isset($_SESSION['user'])) {
    $getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
    $getUser->execute(array($sessionUser));
    $info = $getUser->fetch();
    $userid = $info['UserID'];
?>
    <h1 class="text-center">My Profile</h1>
    <div class="information block">
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">My Information</div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li>
                            <i class="fa fa-unlock-alt fa-fw"></i> <!-- fw => fixed width -->
                            <span>Login Name</span> : <?php echo $info['Username'] ?>
                        </li>
                        <li>
                            <i class="fa fa-envelope-o fa-fw"></i>
                            <span>Email</span> : <?php echo $info['Email'] ?>
                        </li>
                        <li>
                            <i class="fa fa-user fa-fw"></i>
                            <span>Full Name</span> : <?php echo $info['FullName'] ?>
                        </li>
                        <li>
                            <i class="fa fa-calendar fa-fw"></i>
                            <span>Register Date</span> : <?php echo $info['Date'] ?>
                        </li>
                        <li>
                            <?php 
                            $stmt = $con->prepare("SELECT items.*,
                                                        favorite.*
                                                    FROM 
                                                        items
                                                    INNER JOIN 
                                                        favorite
                                                    ON
                                                        items.ItemID = favorite.item_id
                                                    WHERE 
                                            user_id = $userid");
                                // Execute Query
                                $stmt->execute();
                                $myItems = $stmt->fetchAll();
                            ?>
                            <i class="fa fa-tags fa-fw"></i>
                            <span>Favourite items</span> : <?php if(!empty($myItems)) { 
                                foreach ($myItems as $item){echo $item['Name'].' , ';} 
                                } else {echo 'There\'s No Favorite Items Selected';} 
                                ?>
                        </li>
                    </ul>
                    <a href="editUser.php?userid=<?php echo $info['UserID'] ?>" class="btn btn-default">Edit Information</a>
                </div>
            </div>
        </div>
    </div>
    <div id="my-ads" class="my-ads block">
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">My Items</div>
                <div class="panel-body">
                    <?php
                    $myItems = getAllFrom("*", "items", "where Member_id = $userid", "", "ItemID");
                    if (!empty($myItems)) {
                        echo '<div class="row">';
                        foreach ($myItems as $item) {
                            echo '<div class="col-sm-6 col-md-3">';
                            echo '<div class="thumbnail item-box">';
                            if ($item['Approve'] == 0) {
                                echo '<span class="approve-status">Waiting Approval</span>';
                            }
                            echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                            if (empty($item['Image'])) {
                                echo "<img class='img-responsive' src='admin/uploads/image_pic/images.jpg' alt='item img'>";
                            } else {
                                echo "<img class='img-responsive' src='admin/uploads/image_pic/" . $item['Image'] . "' alt='item img'>";
                            }
                            echo '<div class="caption">';
                            echo '<h3><a href="items.php?itemid=' . $item['ItemID'] . '">' . $item['Name'] . '</a></h3>';
                            echo '<p>' . $item['Description'] . '</p>';
                            $userid = $_SESSION['userid'];
                            $stmt = $con->prepare("SELECT favorite.*,
                              items.ItemID  AS fav_itm
                        FROM 
                            favorite
                        INNER JOIN 
                            items
                        ON
                            items.ItemID = favorite.item_id     
                        WHERE 
                            user_id = $userid
                         AND item_id = {$item['ItemID']}");
                            // Execute Query
                            $stmt->execute();
                            $fav = $stmt->fetch();
                            if ($fav['item_id'] == $item['ItemID']) {
                                echo '<div class="favorit"><a href="favorit.php?do=remove-favorite&itemid=' . $item['ItemID'] . '"><i class="favorit-icon fa fa-star fa-lg"></i></a></div>';
                            } else {
                                echo '<div class="favorit"><a href="favorit.php?do=add-favorite&itemid=' . $item['ItemID'] . '"><i class="favorit-icon fa fa-star-o fa-lg"></i></a></div>';
                            }
                            echo '<div class="date">' . $item['Add_Date'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            $stmt = $con->prepare("SELECT cart.*,
                              items.ItemID  AS cart_itm
                        FROM 
                            cart
                        INNER JOIN 
                            items
                        ON
                            items.ItemID = cart.item_id     
                        WHERE 
                            user_id = $userid
                         AND item_id = {$item['ItemID']}");
                            // Execute Query
                            $stmt->execute();
                            $cart = $stmt->fetch();
                            if ($cart['item_id'] == $item['ItemID']) {
                                echo '<div class="chart"><a class="btn btn-danger" href="mycart.php?do=remove-cart&itemid=' . $item['ItemID'] . '"><i class="fa fa-shopping-cart"></i> Remove From My Cart</a></div>';
                            } else {
                                echo '<div class="chart"><a class="btn btn-warning" href="mycart.php?do=add-cart&itemid=' . $item['ItemID'] . '"><i class="fa fa-shopping-cart"></i> Add To My Cart</a></div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo 'There\'s No Ads To Show, Create <a href=""newad.php>New Ad</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="my-fav" class="my-ads block">
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">My Favorite Items</div>
                <div class="panel-body">
                    <?php
    $stmt = $con->prepare("SELECT items.*,
                              favorite.*
                        FROM 
                            items
                        INNER JOIN 
                            favorite
                        ON
                            items.ItemID = favorite.item_id
                        WHERE 
                 user_id = $userid");
    // Execute Query
    $stmt->execute();
    $myItems = $stmt->fetchAll();
                    if (!empty($myItems)) {
                        echo '<div class="row">';
                        foreach ($myItems as $item) {
                            echo '<div class="col-sm-6 col-md-3">';
                            echo '<div class="thumbnail item-box">';
                            if ($item['Approve'] == 0) {
                                echo '<span class="approve-status">Waiting Approval</span>';
                            }
                            echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                            if (empty($item['Image'])) {
                                echo "<img class='img-responsive' src='admin/uploads/image_pic/images.jpg' alt='item img'>";
                            } else {
                                echo "<img class='img-responsive' src='admin/uploads/image_pic/" . $item['Image'] . "' alt='item img'>";
                            }
                            echo '<div class="caption">';
                            echo '<h3><a href="items.php?itemid=' . $item['ItemID'] . '">' . $item['Name'] . '</a></h3>';
                            echo '<p>' . $item['Description'] . '</p>';
                            if ($item['item_id'] == $item['ItemID']) {
                                echo '<div class="favorit"><a href="favorit.php?do=remove-favorite&itemid=' . $item['ItemID'] . '"><i class="favorit-icon fa fa-star fa-lg"></i></a></div>';
                            } else {
                                echo '<div class="favorit"><a href="favorit.php?do=add-favorite&itemid=' . $item['ItemID'] . '"><i class="favorit-icon fa fa-star-o fa-lg"></i></a></div>';
                            }
                            echo '<div class="date">' . $item['Add_Date'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            $stmt = $con->prepare("SELECT cart.*,
                              items.ItemID  AS cart_itm
                        FROM 
                            cart
                        INNER JOIN 
                            items
                        ON
                            items.ItemID = cart.item_id     
                        WHERE 
                            user_id = $userid
                         AND item_id = {$item['ItemID']}");
                            // Execute Query
                            $stmt->execute();
                            $cart = $stmt->fetch();
                            if ($cart['item_id'] == $item['ItemID']) {
                                echo '<div class="chart"><a class="btn btn-danger" href="mycart.php?do=remove-cart&itemid=' . $item['ItemID'] . '"><i class="fa fa-shopping-cart"></i> Remove From My Cart</a></div>';
                            } else {
                                echo '<div class="chart"><a class="btn btn-warning" href="mycart.php?do=add-cart&itemid=' . $item['ItemID'] . '"><i class="fa fa-shopping-cart"></i> Add To My Cart</a></div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo 'There\'s No Favorite Items Selected';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="my-comments block">
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">Latest Comments</div>
                <div class="panel-body">
                    <?php
                    $myComments = getAllFrom("Comment", "comments", "where User_id = $userid", "", "C_ID");
                    if (!empty($myComments)) {
                        foreach ($myComments as $comment) {
                            echo '<p>' . $comment['Comment'] . '</p>';
                        }
                    } else {
                        echo 'There\'s No Comments to Show';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
    header('Location: login.php');
    exit();
}
include $tpl . 'footer.php';
ob_end_flush();
?>
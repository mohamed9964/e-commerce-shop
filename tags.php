<?php
session_start();
$pageTitle = 'Show Category';
include 'init.php';
?>
<div class="container">
    <div class="row">
        <?php
        // $category = isset($_GET['pageid']) && is_numeric($_GET['pageid']) ? intval($_GET['pageid']) : 0;
        if (isset($_GET['name']) && is_string($_GET['name'])) {
            echo '<h1 class="text-center">Show Items By Tag Name</h1>';
            $tag = $_GET['name'];
            $tagItems = getAllFrom("*", "items", "where tags like '%$tag%'", "AND Approve = 1", "ItemID");
            foreach ($tagItems as $item) {
                echo '<div class="col-sm-6 col-md-3">';
                echo '<div class="thumbnail item-box">';
                echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                if (empty($item['Image'])) {
                    echo "<img class='img-responsive' src='admin/uploads/image_pic/images.jpg' alt='item img'>";
                } else {
                    echo "<img class='img-responsive' src='admin/uploads/image_pic/" . $item['Image'] . "' alt='item img'>";
                }
                echo '<div class="caption">';
                echo '<h3><a href="items.php?itemid=' . $item['ItemID'] . '">' . $item['Name'] . '</a></h3>';
                echo '<p>' . $item['Description'] . '</p>';
                if (isset($_SESSION['user'])) {
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
                } else {
                    echo '<i class="favorit-icon fa fa-star-o fa-lg"></i> <a href="login.php">Login</a> To Make Favorite Item';
                }
                echo '<div class="date">' . $item['Add_Date'] . '</div>';
                echo '</div>';
                echo '</div>';
                if (isset($_SESSION['user'])) {
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
                } else {
                    echo '<div class="alert alert-warning text-center">';
                    echo '<i class="fa fa-shopping-cart"></i><a href="login.php"> Login</a> To Add Ur Cart';
                    echo '</div>';
                }
                echo '</div>';
            }
        } else {
            $theMsg = '<div class="alert alert-danger">You Must Enter Tag Name</div>';
            redirectHome($theMsg, '', 5);
        }
        ?>
    </div>
</div>
<?php include $tpl . 'footer.php'; ?>
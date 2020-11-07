<?php
ob_start();
session_start();
$pageTitle = 'Profile';
include 'init.php';
if (isset($_SESSION['user'])) {
    $do = isset($_GET['do']) ? $_GET['do'] : 'error';
    if ($do == 'add-cart') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $userid = $_SESSION['userid'];
        $stmt = $con->prepare("INSERT INTO 
                                cart(item_id, user_id)
                            VALUES(:zitem, :zuser)");
        // Execute Query
        $stmt->execute(array(
            'zitem' => $itemid,
            'zuser' => $userid
        ));
        header('Location:' . $_SERVER['HTTP_REFERER']);
    } elseif ($do == 'remove-cart') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $userid = $_SESSION['userid'];
        // Select All Data Depend On This ID
        $check = checkItem('item_id', 'cart', $itemid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("DELETE FROM cart WHERE item_id = $itemid");
            $stmt->execute();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    } elseif ($do == 'shop') {
        $userid = $_SESSION['userid'];
        $total_price = 0;
        $stmt = $con->prepare("SELECT items.*,
                              cart.*
                        FROM 
                            items
                        INNER JOIN 
                            cart
                        ON
                            items.ItemID = cart.item_id
                        WHERE 
                 user_id = $userid");
        // Execute Query
        $stmt->execute();
        $myItems = $stmt->fetchAll();
        if (!empty($myItems)) {
?>
            <h1 class="text-center">Shop Items</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table manage-items text-center table table-bordered">
                        <tr>
                            <td>Item Image</td>
                            <td>Item Name</td>
                            <td>Quantity</td>
                            <td>Unit Price</td>
                            <td>Items Total</td>
                        </tr>
                        <?php
                        $key = 0;
                        foreach ($myItems as $item) {
                            echo "<tr>";
                            echo "<td>";
                            if (empty($item['Image'])) {
                                echo "<img src='admin/uploads/image_pic/images.jpg' width='50' height='40' alt='item img'>";
                            } else {
                                echo "<img src='admin/uploads/image_pic/" . $item['Image'] . "'width='50' height='40'  alt='item img'>";
                            }
                            echo "</td>";
                            echo "<td>" . $item['Name'] . "</td>";
                            echo "<td>"; ?>
                            <form method='post' action='?do=update'>
                                <input type="hidden" name="cartid" value="<?php echo $item['ID'] ?>">
                                <select name='quantity' class='quantity' onChange="this.form.submit()">
                                    <option <?php if ($item["quantity"] == 1) echo "selected"; ?> value="1">1</option>
                                    <option <?php if ($item["quantity"] == 2) echo "selected"; ?> value="2">2</option>
                                    <option <?php if ($item["quantity"] == 3) echo "selected"; ?> value="3">3</option>
                                    <option <?php if ($item["quantity"] == 4) echo "selected"; ?> value="4">4</option>
                                    <option <?php if ($item["quantity"] == 5) echo "selected"; ?> value="5">5</option>
                                </select>
                            </form>
                        <?php echo "</td>";
                            echo "<td>" . "$" . $item['Price'] . "</td>";
                            echo "<td>" . "$" . $item["Price"] * $item["quantity"] . "</td>";
                            echo "</tr>";
                            $total_price += ($item["Price"] * $item["quantity"]);
                        }
                        ?>
                        <tr>
                            <td colspan="3" align="center" style="font-size: 50px; letter-spacing:5px">
                                <strong>TOTAL :</strong>
                            </td>
                            <td colspan="2" align="center" style="font-size: 50px; letter-spacing:5px">
                                <strong><?php echo "$" . $total_price; ?></strong>
                            </td>
                        </tr>
                        <?php
                        ?>
                    </table>
                </div>
            </div>
        <?php } else {
            echo '<div class="container">';
            echo '<div class="nice-message">There\'s No Items To Add My Cart</div>';
            echo '</div>';
        }
        ?>
<?php
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $quantity = $_POST['quantity'];
            $cartid   = $_POST['cartid'];
            $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE ID = ?");
            $stmt->execute(array($quantity, $cartid));
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    } elseif ($do == 'error') {
        header('Location:' . $_SERVER['HTTP_REFERER']);
    }
} else {
    header('Location: login.php');
    exit();
}
include $tpl . 'footer.php';
ob_end_flush();

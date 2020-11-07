<?php
/*
    ================================================
    == Items Page
    ================================================
    */
ob_start(); // Output Buffering Start
session_start();
$pageTitle = 'Items';
if (isset($_SESSION['Username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    if ($do == 'Manage') {
        $stmt = $con->prepare("SELECT 
                                    items.*,categories.Name AS category_name,
                                    users.Username AS member_name
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
                                ORDER BY ItemID DESC");
        // Execute The Statement
        $stmt->execute();
        // Assign To Variable
        $items = $stmt->fetchAll();
        if (!empty($items)) {
?>
            <h1 class="text-center">Manage Item</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table manage-items text-center table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Item Image</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Price</td>
                            <td>Adding Date</td>
                            <td>Category</td>
                            <td>Username</td>
                            <td>Control</td>
                        </tr>
                        <?php
                        foreach ($items as $item) {
                            echo "<tr>";
                            echo "<td>" . $item['ItemID'] . "</td>";
                            echo "<td>";
                            if (empty($item['Image'])) {
                                echo "<img src='uploads/image_pic/images.jpg' alt='item img'>";
                            } else {
                                echo "<img src='uploads/image_pic/" . $item['Image'] . "' alt='item img'>";
                            }
                            echo "</td>";
                            echo "<td>" . $item['Name'] . "</td>";
                            echo "<td>" . $item['Description'] . "</td>";
                            echo "<td>" . $item['Price'] . "</td>";
                            echo "<td>" . $item['Add_Date'] . "</td>";
                            echo "<td>" . $item['category_name'] . "</td>";
                            echo "<td>" . $item['member_name'] . "</td>";
                            echo "<td>
                                <a href='items.php?do=Edit&itemid=" . $item['ItemID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                                <a href='items.php?do=Delete&itemid=" . $item['ItemID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
                            if ($item['Approve'] == 0) {
                                echo "<a href='items.php?do=Approve&itemid=" . $item['ItemID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Activate</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
                <a href="items.php?do=Add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Item</a>
            </div>
        <?php } else {
            echo '<div class="container">';
            echo '<div class="nice-message">There\'s No Items To Manage</div>';
            echo '<a href="items.php?do=Add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Item</a>';
            echo '</div>';
        }
        ?>
    <?php
    } elseif ($do == 'Add') { ?>
        <h1 class="text-center">Add New Item</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="post" enctype="multipart/form-data">
                <!-- Start Name Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="name" class="form-control" required placeholder="Name of The Item">
                    </div>
                </div>
                <!-- End Name Field -->
                <!-- Start Description Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="description" class="form-control" required placeholder="Description of The Item">
                    </div>
                </div>
                <!-- End Description Field -->
                <!-- Start Price Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Price</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="price" class="form-control" required placeholder="Price of The Item">
                    </div>
                </div>
                <!-- End Price Field -->
                <!-- Start Country Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="country" class="form-control" required placeholder="Country of Made">
                    </div>
                </div>
                <!-- End Country Field -->
                <!-- Start Tags Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Tags</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" name="tags" class="form-control" required placeholder="Separate Tags With Comma (,)">
                    </div>
                </div>
                <!-- End Tags Field -->
                <!-- Start Status Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="status">
                            <option value="0">...</option>
                            <option value="1">New</option>
                            <option value="2">Like New</option>
                            <option value="3">Used</option>
                            <option value="4">Very Old</option>
                        </select>
                    </div>
                </div>
                <!-- End Status Field -->
                <!-- Start Members Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Member</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="member">
                            <option value="0">...</option>
                            <?php
                            $allMembers = getAllFrom("*", "users", "", "", "UserID");
                            foreach ($allMembers as $user) {
                                echo "<option value='" . $user['UserID'] . "'>" . $user['Username'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- End Members Field -->
                <!-- Start Categories Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Category</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="category">
                            <option value="0">...</option>
                            <?php
                            $allCats = getAllFrom("*", "categories", "where Parent = 0", "", "ID");
                            foreach ($allCats as $cat) {
                                echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
                                $childCats = getAllFrom("*", "categories", "where Parent = {$cat['ID']}", "", "ID");
                                foreach ($childCats as $child) {
                                    echo "<option value='" . $child['ID'] . "'>----  " . $child['Name'] . " Sub Category from " . $cat['Name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- End Categories Field -->
                <!-- Start Profile Picture Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Item Image</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <!-- End Profile Picture Field -->
                <!-- Start submit Field -->
                <div class="form-group form-group-lg">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Add Item" class="btn btn-primary btn-sm">
                    </div>
                </div>
                <!-- End submit Field -->
            </form>
        </div>
        <?php
    } elseif ($do == 'Insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Insert Item</h1>';
            echo "<div class= 'container'>";
            // Upload Variable
            $imageName      = $_FILES['image']['name'];
            $imageSize      = $_FILES['image']['size'];
            $imageTmp       = $_FILES['image']['tmp_name'];
            $imageType      = $_FILES['image']['type'];
            // list of file to upload 
            $imgAllowExt = array("jpeg", "jpg", "png", "gif");
            // get variable extension
            $img_parts = explode('.', $_FILES['image']['name']);
            $imgExt = strtolower(end($img_parts));
            // Get Variables From The Form
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $member     = $_POST['member'];
            $cat        = $_POST['category'];
            $tags       = $_POST['tags'];
            // Validate The Form
            $formErrors = array();
            if (empty($name)) {
                $formErrors[] = 'Name Cant be <strong>Empty</strong>';
            }
            if (empty($desc)) {
                $formErrors[] = 'Description Cant be <strong>Empty</strong>';
            }
            if (empty($price)) {
                $formErrors[] = 'Price Cant be <strong>Empty</strong>';
            }
            if (empty($country)) {
                $formErrors[] = 'Country Cant be <strong>Empty</strong>';
            }
            if ($status == 0) {
                $formErrors[] = 'You Must Choose The <strong>Status</strong>';
            }
            if ($member == 0) {
                $formErrors[] = 'You Must Choose The <strong>Member</strong>';
            }
            if ($cat == 0) {
                $formErrors[] = 'You Must Choose The <strong>Category</strong>';
            }
            if (!empty($imageName) && !in_array($imgExt, $imgAllowExt)) {
                $formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
            }
            if (empty($imageName)) {
                $formErrors[] = 'Profile Picture Is <strong>Required</strong>';
            }
            if ($imageSize > 4194304) {
                $formErrors[] = 'Profile Picture Cant Be Larger Than <strong>4MB</strong>';
            }
            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }
            // check If There 's No Error Proceed The Update Operation
            if (empty($formErrors)) {
                $image = rand(0, 10000) . '_' . $imageName;
                move_uploaded_file($imageTmp, "uploads\image_pic\\" . $image);
                // Insert Userinfo In Database
                $stmt = $con->prepare("INSERT INTO
                                items(Name, Description, Price, Country_Made, Status, Add_Date, Member_ID, Cat_ID, tags, Image)
                                VALUES(:zname, :zdesc, :zprice, :zcountry, :zstatus, now(),:zmember, :zcat, :ztags, :zimage)");
                $stmt->execute(array(
                    'zname'     => $name,
                    'zdesc'     => $desc,
                    'zprice'    => $price,
                    'zcountry'  => $country,
                    'zstatus'   => $status,
                    'zmember'   => $member,
                    'zcat'      => $cat,
                    'ztags'     => $tags,
                    'zimage'     => $image
                ));
                // Echo Success Message
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Inserted</div>';
                redirectHome($theMsg, "back");
            }
        } else {
            echo "<div class='container'>";
            $theMsg = '<div class="alert alert-danger">Sorry You cant Browse This Page Directly</div>';
            redirectHome($theMsg);
            echo "</div>";
        }
        echo "</div>";
    } elseif ($do == 'Edit') {
        // Check If Get Request Item Is numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // Select All Data Depend On This ID
        $stmt = $con->prepare("SELECT * FROM items WHERE ItemID = ?");
        // Execute Query
        $stmt->execute(array($itemid));
        // Fetch The Data
        $item = $stmt->fetch();
        // The Row Count
        $count = $stmt->rowCount();
        // If There's Such ID Show The Form
        if ($count > 0) { ?>
            <h1 class="text-center">Edit Item</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="post">
                    <input type="hidden" name="itemid" value="<?php echo $itemid ?>">
                    <!-- Start Name Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="name" class="form-control" required placeholder="Name of The Item" value="<?php echo $item['Name'] ?>">
                        </div>
                    </div>
                    <!-- End Name Field -->
                    <!-- Start Description Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="description" class="form-control" required placeholder="Description of The Item" value="<?php echo $item['Description'] ?>">
                        </div>
                    </div>
                    <!-- End Description Field -->
                    <!-- Start Price Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="price" class="form-control" required placeholder="Price of The Item" value="<?php echo $item['Price'] ?>">
                        </div>
                    </div>
                    <!-- End Price Field -->
                    <!-- Start Country Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Country</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="country" class="form-control" required placeholder="Country of Made" value="<?php echo $item['Country_Made'] ?>">
                        </div>
                    </div>
                    <!-- End Country Field -->
                    <!-- Start Tags Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="text" name="tags" class="form-control" required placeholder="Separate Tags With Comma (,)" value="<?php echo $item['tags'] ?>">
                        </div>
                    </div>
                    <!-- End Tags Field -->
                    <!-- Start Status Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="status">
                                <option value="1" <?php if ($item['Status'] == 1) {
                                                        echo 'selected';
                                                    } ?>>New</option>
                                <option value="2" <?php if ($item['Status'] == 2) {
                                                        echo 'selected';
                                                    } ?>>Like New</option>
                                <option value="3" <?php if ($item['Status'] == 3) {
                                                        echo 'selected';
                                                    } ?>>Used</option>
                                <option value="4" <?php if ($item['Status'] == 4) {
                                                        echo 'selected';
                                                    } ?>>Very Old</option>
                            </select>
                        </div>
                    </div>
                    <!-- End Status Field -->
                    <!-- Start Members Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Member</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="member">
                                <?php
                                $allUsers = getAllFrom("*", "users", "", "", "UserID");
                                foreach ($allUsers as $user) {
                                    echo "<option value='" . $user['UserID'] . "'";
                                    if ($item['Member_ID'] == $user['UserID']) {
                                        echo 'selected';
                                    }
                                    echo ">" . $user['Username'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Members Field -->
                    <!-- Start Categories Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-10 col-md-6">
                            <select name="category">
                                <?php
                                $allCats = getAllFrom("*", "categories", "", "", "ID");
                                foreach ($allCats as $cat) {
                                    echo "<option value='" . $cat['ID'] . "'";
                                    if ($item['Cat_ID'] == $cat['ID']) {
                                        echo 'selected';
                                    }
                                    echo ">" . $cat['Name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Categories Field -->
                    <!-- Start submit Field -->
                    <div class=" form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Edit Item" class="btn btn-primary btn-sm">
                        </div>
                    </div>
                    <!-- End submit Field -->
                </form>
                <?php
                $stmt = $con->prepare("SELECT
                                            comments.*, users.Username AS Member
                                       FROM
                                            comments
                                       INNER JOIN
                                            users
                                        ON
                                            users.UserID = comments.User_id
                                        WHERE Item_id = ?");
                $stmt->execute(array($itemid));
                $comments = $stmt->fetchAll();
                if (!empty($comments)) {
                ?>
                    <h1 class="text-center">Manage [ <?php echo $item['Name'] ?> ] Comment</h1>
                    <div class="table-responsive">
                        <table class="main-table text-center table table-bordered">
                            <tr>
                                <td>Comment</td>
                                <td>User Name</td>
                                <td>Added Date</td>
                                <td>Control</td>
                            </tr>
                            <?php
                            foreach ($comments as $comment) {
                                echo "<tr>";
                                echo "<td>" . $comment['Comment'] . "</td>";
                                echo "<td>" . $comment['Member'] . "</td>";
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
                <?php } ?>
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
        echo '<h1 class="text-center">Update Item</h1>';
        echo "<div class= 'container'>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get Variables From The Form
            $itemid     = $_POST['itemid'];
            $name       = $_POST['name'];
            $desc       = $_POST['description'];
            $price      = $_POST['price'];
            $country    = $_POST['country'];
            $status     = $_POST['status'];
            $member     = $_POST['member'];
            $cat        = $_POST['category'];
            $tags       = $_POST['tags'];
            // Validate The Form
            $formErrors = array();
            if (empty($name)) {
                $formErrors[] = 'Name Cant be <strong>Empty</strong>';
            }
            if (empty($desc)) {
                $formErrors[] = 'Description Cant be <strong>Empty</strong>';
            }
            if (empty($price)) {
                $formErrors[] = 'Price Cant be <strong>Empty</strong>';
            }
            if (empty($country)) {
                $formErrors[] = 'Country Cant be <strong>Empty</strong>';
            }
            if ($status == 0) {
                $formErrors[] = 'You Must Choose The <strong>Status</strong>';
            }
            if ($member == 0) {
                $formErrors[] = 'You Must Choose The <strong>Member</strong>';
            }
            if ($cat == 0) {
                $formErrors[] = 'You Must Choose The <strong>Category</strong>';
            }
            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }
            // check If There 's No Error Proceed The Update Operation
            if (empty($formErrors)) {
                // Update The Database With This apc_cache_info
                $stmt = $con->prepare("UPDATE items SET Name = ?, Description = ?, Price = ?, Status = ?, Country_Made= ?, Member_ID = ?, Cat_ID = ?, tags = ?  WHERE ItemID = ?");
                $stmt->execute(array($name, $desc, $price, $status, $country, $member, $cat, $tags, $itemid));

                // Echo Success Message
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
                redirectHome($theMsg, 'back');
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>Sorry You Cant Brows This is Page Directly</div>";
            redirectHome($theMsg, 'back');
        }
        echo "</div>";
    } elseif ($do == 'Delete') {
        echo '<h1 class="text-center">Delete Member</h1>';
        echo "<div class= 'container'>";
        // Check If Get Request UserID Is numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // Select All Data Depend On This ID
        $check = checkItem('ItemID', 'items', $itemid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("DELETE FROM items WHERE ItemID = :itemid");
            $stmt->bindParam(':itemid', $itemid);
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
        echo '<h1 class="text-center">Approve Item</h1>';
        echo "<div class= 'container'>";
        // Check If Get Request UserID Is numeric & Get The Integer Value Of It
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        // Select All Data Depend On This ID
        $check = checkItem('ItemID', 'items', $itemid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE ItemID = ?");
            $stmt->execute(array($itemid));
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

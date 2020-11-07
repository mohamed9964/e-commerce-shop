<?php
ob_start();
session_start();
$pageTitle = 'Create New Ad';
include 'init.php';
if (isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $formErrors = array();
        $name       = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $desc       = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $price      = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
        $country    = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
        $status     = filter_var($_POST['status'], FILTER_SANITIZE_NUMBER_INT);
        $category   = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
        $tags       = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);
        if (strlen($name) < 4) {
            $formErrors[] = 'Item Title Must Be At Least 4 Characters';
        }
        if (strlen($desc) < 10) {
            $formErrors[] = 'Item Description Must Be At Least 10 Characters';
        }
        if (strlen($country) < 2) {
            $formErrors[] = 'Item Country Must Be At Least 2 Characters';
        }
        if (empty($price)) {
            $formErrors[] = 'Item Price Must Be Not Empty';
        }
        if (empty($status)) {
            $formErrors[] = 'Item Status Must Be Not Empty';
        }
        if (empty($category)) {
            $formErrors[] = 'Item Category Must Be Not Empty';
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
        // check If There 's No Error Proceed The Update Operation
        if (empty($formErrors)) {
            $image = rand(0, 10000) . '_' . $imageName;
            move_uploaded_file($imageTmp, "admin\uploads\image_pic\\" . $image);
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
                'zmember'   => $_SESSION['userid'],
                'zcat'      => $category,
                'ztags'     => $tags,
                'zimage'     => $image
            ));
            // Echo Success Message
            if ($stmt) {
                $succesMsg = '<div class="alert alert-success">Item Added</div>';
            }
        }
    }
?>
    <h1 class="text-center"><?php echo $pageTitle ?></h1>
    <div class="create-ad block">
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo $pageTitle ?></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form class="form-horizontal main-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                <!-- Start Name Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Name</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input pattern=".{4,}" title="This Field Require At Least 4 Characters" type="text" name="name" class="form-control live" required placeholder="Name of The Item" data-class=".live-title">
                                    </div>
                                </div>
                                <!-- End Name Field -->
                                <!-- Start Description Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input pattern=".{10,}" title="This Field Require At Least 10 Characters" type="text" name="description" class="form-control live" required placeholder="Description of The Item" data-class=".live-desc">
                                    </div>
                                </div>
                                <!-- End Description Field -->
                                <!-- Start Price Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Price</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input type="text" name="price" class="form-control live" required placeholder="Price of The Item" data-class=".live-price">
                                    </div>
                                </div>
                                <!-- End Price Field -->
                                <!-- Start Country Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Country</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input type="text" name="country" class="form-control" required placeholder="Country of Made">
                                    </div>
                                </div>
                                <!-- End Country Field -->
                                <!-- Start Tags Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Tags</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input type="text" name="tags" class="form-control" required placeholder="Separate Tags With Comma (,)">
                                    </div>
                                </div>
                                <!-- End Tags Field -->
                                <!-- Start Status Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-10 col-md-9">
                                        <select name="status" required>
                                            <option value="">...</option>
                                            <option value="1">New</option>
                                            <option value="2">Like New</option>
                                            <option value="3">Used</option>
                                            <option value="4">Very Old</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- End Status Field -->
                                <!-- Start Categories Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Category</label>
                                    <div class="col-sm-10 col-md-9">
                                        <select name="category" required>
                                            <option value="">...</option>
                                            <?php
                                            $cats = getAllFrom('*', 'categories', '', '', 'ID');
                                            foreach ($cats as $cat) {
                                                echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- End Categories Field -->
                                <!-- Start Profile Picture Field -->
                                <div class="form-group form-group-lg">
                                    <label class="col-sm-3 control-label">Item Image</label>
                                    <div class="col-sm-10 col-md-9">
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                </div>
                                <!-- End Profile Picture Field -->
                                <!-- Start submit Field -->
                                <div class="form-group form-group-lg">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <input type="submit" value="Add Item" class="btn btn-primary btn-sm">
                                    </div>
                                </div>
                                <!-- End submit Field -->
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="thumbnail item-box live-preview">
                                <span class="price-tag">
                                    $<span class="live-price">0</span>
                                </span>
                                <?php
                                    echo "<img class='img-responsive' src='admin/uploads/image_pic/images.jpg' alt='item img'>";
                                ?>
                                <div class="caption">
                                    <h3 class="live-title">Title</h3>
                                    <p class="live-desc">Description</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Start Looping Through Errors -->
                    <?php
                    if (!empty($formErrors)) {
                        foreach ($formErrors as $error) {
                            echo '<div class="alert alert-danger">' . $error . '</div>';
                        }
                    }
                    if (isset($succesMsg)) {
                        //redirectHome($succesMsg, 'back');
                    }
                    ?>
                    <!-- End Looping Through Errors -->
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
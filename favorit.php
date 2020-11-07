<?php
ob_start();
session_start();
$pageTitle = 'Profile';
include 'init.php';
if (isset($_SESSION['user'])) {
    $do = isset($_GET['do']) ? $_GET['do'] : 'error';
    if ($do == 'add-favorite') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $userid = $_SESSION['userid'];
    $stmt = $con->prepare("INSERT INTO 
                                favorite(item_id, user_id)
                            VALUES(:zitem, :zuser)");
    // Execute Query
    $stmt->execute(array(
        'zitem' => $itemid,
        'zuser' => $userid
    ));
    header('Location:' . $_SERVER['HTTP_REFERER']);
} elseif ($do == 'remove-favorite') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $userid = $_SESSION['userid'];
        // Select All Data Depend On This ID
        $check = checkItem('item_id', 'favorite', $itemid);
        // If There's Such ID Show The Form
        if ($check > 0) {
            $stmt = $con->prepare("DELETE FROM favorite WHERE item_id = $itemid");
            $stmt->execute();
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
?>
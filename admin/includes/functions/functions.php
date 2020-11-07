<?php

/* 
** Get All Function  
** Funstion To Get All Records From Any Dashboard Table
*/
function getAllFrom($field, $table, $where = NULL, $and = NULL, $orderfield, $ordering = "DESC")
{
  global $con;
  $getAll = $con->prepare("SELECT $field FROM $table $where $and ORDER BY $orderfield $ordering");
  $getAll->execute();
  $all = $getAll->fetchAll();
  return $all;
}

  function getTitle() {
    global $pageTitle;
    if (isset($pageTitle)) {
      echo $pageTitle;
    } else {
      echo "Default";
    }
  }
    // Home Redirect Function
    function redirectHome($theMsg, $url = null, $seconds = 3) {
      if ($url === null) {
        $url = 'index.php';
        $link = 'Homepage';
      } else {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){
          $url = $_SERVER['HTTP_REFERER'];
          $link = 'Previous Page';
        } else {
          $url = 'index.php';
          $link = 'Homepage';
        }

      }
      echo $theMsg;
      echo "<div class='alert alert-info'>You Will Be Redirected to $link After $seconds Seconds</div>";
      header("refresh:$seconds;url=$url");
      exit();
    }
    /*
     ** Function to Check Item In Database Accept Parameters
     ** $Select is Item TO Select (Column)
     ** $from is The Table To select From
     ** $Value is The Value Of Select
     */
    function checkItem($select, $form, $value) {
      global $con;
      $statment = $con->prepare("SELECT $select FROM $form WHERE $select = ?");
      $statment->execute(array($value));
      $count = $statment->rowCount();
      return $count;
    }
    /* 
    ** Count Number Of Items Function 
    ** Function To Count Number Of Items Rows 
    **
    */
    
    function countItems($item, $table) {
      global $con;
      $stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
      $stmt2->execute();
      return $stmt2->fetchColumn(); // value is inside column COUNT($item)
    }
    /*
     * 
     ** Get Latest Records Function  
     ** Funstion To Get Latest Items From Dashboard [Users, Items, Comments]
     ** Prameters are $select, $table, $order and $limit 
     * 
     */
    function getLatest($select, $table, $order, $limit = 5){
      global $con;
      $getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
      $getStmt->execute();
      $rows = $getStmt->fetchAll();
      return $rows;
    }























<?php
## Set the timezone to your location
ini_set("date.timezone", "America/Chicago");

require_once'../../includes/session_timeout_db.php';
require_once'../../includes/connection.php';
require_once '../../includes/utility_funcs.php';
$conn = dbConnect('write');
// initialize flags
$OK = false;
$deleted = false;
// initialize statement
$stmt = $conn->stmt_init();
// get details of selected record
if (isset($_GET['article_id']) && !$_POST) {
  // prepare SQL query
  $sql = 'SELECT article_id, title, created
          FROM php_blog_blog WHERE article_id = ?';
  if ($stmt->prepare($sql)) {
    // bind the query parameters
    $stmt->bind_param('i', $_GET['article_id']);
    // execute the query, and fetch the result
    $OK = $stmt->execute();
    // bind the result to variables
    $stmt->bind_result($article_id, $title, $created);
    $stmt->fetch();
  }
}
// if confirm deletion button has been clicked, delete record
if (isset($_POST['delete'])) {
  $sql = 'DELETE FROM php_blog_blog WHERE article_id = ?';
  if ($stmt->prepare($sql)) {
    $stmt->bind_param('i', $_POST['article_id']);
    $stmt->execute();
    // if there's an error affected_rows is -1
    if ($stmt->affected_rows > 0) {
      $deleted = true;
    } else {
      $error = 'There was a problem deleting the record. '; 
    }
  }
}
// redirect the page if deletion is successful, 
// cancel button clicked, or $_GET['article_id'] not defined
if ($deleted || isset($_POST['cancel_delete']) || !isset($_GET['article_id']))  {
  header('Location: /php/blog/admin/blog-list.php');
  exit;
  }
// if any SQL query fails, display error message
if (isset($stmt) && !$OK && !$deleted) {
      $error .= $stmt->error;
}

# Page specific variables.
$nav = "blog-admin";
$title_section = "Blog: Admin - Delete";

# Create a human friendly name based on file name.
include("../../includes/title-page-name.php");

# The header section of the layout.
include("../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>
            
            <?php 
            if (isset($error)  && !empty($error)) {
              echo "<p class='warning'>Error: $error</p>";
            }
            if($article_id == 0) { ?>
            <p class="error">Invalid request: record does not exist.</p>
            <?php } else { ?>
            <h2 class="error">Please confirm that you want to delete the following item.<br>Note: This action cannot be undone.</h2>
            <ul>
                <li>Date: <?= $created; ?></li>
                <li>Title: <?= safe($title); ?></li>
            </ul>
            <?php } ?>
            <form id="form1" method="post">
                <fieldset>
                    <legend><?php echo $title_page_name; ?></legend>
                    
                    <ol>
                        <li>
                        <?php if(isset($article_id) && $article_id > 0) { ?>
                            <input type="submit" name="delete" value="Confirm Deletion">
                        <?php } ?>
                            <input name="cancel_delete" type="submit" id="cancel_delete" value="Cancel">
                        <?php if(isset($article_id) && $article_id > 0) { ?>
                            <input name="article_id" type="hidden" value="<?= $article_id; ?>">
                        <?php } ?>
                        </li>
                    </ol>
                </fieldset>
            </form>
            <?php include('../../includes/logout_db.php'); ?>
        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("sidebar.php");

# The footer section of the layout.
include("../../includes/footer.php");
?>

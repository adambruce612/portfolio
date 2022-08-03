<?php


require_once('../../includes/session_timeout_db.php');

require_once('../../includes/connection.php');
// create database connection
    $conn = dbConnect('write');
    $stmt = $conn->stmt_init();
 // prepare SQL query
$sql = 'SELECT content
        FROM fp_about_us 
        WHERE page_id = 1';
    if ($stmt->prepare($sql)) {
        // execute the query, and fetch the result
        $OK = $stmt->execute();
        // bind the results to variables
        $stmt->bind_result($content);
        $stmt->fetch();
        // free the database resources for the second query
        $stmt->free_result();
    }


if(isset($_POST['update'])) {
    // check for empty fields
    $error = '';
    if(($_POST['content']) == ''){
        $error = "Please enter about us content.";
        $errorContent = true;
    }
} 
if (isset($_POST['update']) && ($error === '')) {

      // initialize flag
    $OK = false;
    
    // initialize prepared statement
    $stmt = $conn->stmt_init();
    
      // create SQL
      $sql = 'UPDATE fp_about_us SET content = ?
              WHERE page_id = 1';
      $stmt->prepare($sql);
      $stmt->bind_param('s', $_POST['content']);
    // execute and get number of affected rows
    $stmt->execute();
    $OK = $stmt->affected_rows;
    
    
    if ($OK) {
        header('Location: /php/blog/admin/blog-list.php');
    } else {
        $error = $stmt->error;
    }
  }

# Page specific variables.
$nav = "blog-admin";
$title_section = "Blog: Admin - About";

# Create a human friendly name based on file name.
include("../../includes/title-page-name.php");

# The header section of the layout.
include("../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>

        
            <form id="form1" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><?php echo $title_page_name; ?></legend>
                    
                    <ol>

        <li <?php if(isset($errorContent)) echo 'class="error"'?>>
            <label for="content">Content:</label>
            <textarea name="content" cols="60" rows="8" class="widebox" id="content"><?php 
                            if (isset($error)) {
                                echo htmlentities($_POST['content'], ENT_COMPAT, 'utf-8');
                            } else {
                                echo htmlentities($content, ENT_COMPAT, 'utf-8');
                            }
                            ?></textarea>
        </li>
        <li>
            <input type="submit" name="update" value="Update About Us">
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


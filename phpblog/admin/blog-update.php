<?php

# Source: php-7-solutions/ch18/blog_insert_mysqli_05.php
use php\PhpSolutions\Image\ThumbnailUpload;
require_once('../../includes/session_timeout_db.php');

// redirect if $_GET['article_id'] not defined
if (!isset($_GET['article_id'])) {
  header('Location: /php/blog/admin/blog-list.php');
  exit;
}

require_once('../../includes/connection.php');
// create database connection
    $conn = dbConnect('write');
    $stmt = $conn->stmt_init();

// get details of selected record
if (isset($_GET['article_id']) && !$_POST) {
    // prepare SQL query
    $sql = 'SELECT article_id, image_id, title, article
            FROM php_blog_blog WHERE article_id = ?';
    if ($stmt->prepare($sql)) {
        // bind the query parameter
        $stmt->bind_param('i', $_GET['article_id']);
        // execute the query, and fetch the result
        $OK = $stmt->execute();
        // bind the results to variables
        $stmt->bind_result($article_id, $image_id, $title, $article);
        $stmt->fetch();
        // free the database resources for the second query
        $stmt->free_result();
    }
}

if(isset($_POST['update'])) {
    // check for empty fields
    $error = '';
    if(($_POST['title']) == ''){
        $error .= "Please enter a title. ";
        $errorTitle = true;
    }

    if(($_POST['article']) == ''){
        $error = "Please enter an article. ";
        $errorArticle = true;
    }
} 
if (isset($_POST['update']) && ($error === '')) {
 
    
    
    // initialize flag
    $OK = false;
    
    // initialize prepared statement
    $stmt = $conn->stmt_init();
    
    
      // if a file has been uploaded, process it
  if(isset($_POST['upload_new']) && $_FILES['image']['error'] == 0) {
    $imageOK = false;


// set the maximum width or height size
$maxSize = 300; // no units PX expected

// set the maximum upload size in bytes
$size = 2048000; // 2048000/1024/1000 = 2MB

    
    require_once('../../PhpSolutions/Image/ThumbnailUpload.php');
    try {
        // Call __construct($path, $deleteOriginal = false)
        $loader = new ThumbnailUpload('../images/', false);

        // Call setThumbOptions($path, $maxSize = null, $suffix = null)
        $loader->setThumbOptions('../images/thumbs', $maxSize, '');

        // Call public function upload($fieldname, $size = null, ?array $mime = null, $renameDuplicates = true)
        $loader->upload('image', $size);
        // after uploading and creating the thumbnail
        // get the name of the image
        // must add new lines to the processFile method in the ThumbnailUpload class see page 438
        // new lines will add file name to the filenames array
        // must add new property to the Upload class to store the filename $_filenames
        // must add new method to the Upload class to retrieve the filename getFilenames
        $names = $loader->getFilenames();
        // now $names contains an array with the names of the uploaded images (note: we are only uploading a single image)
        $messages = $loader->getMessages();
    } catch (Exception $e) {
        $errors = $e->getMessage();
    }
    
    // $names will be an empty array if the upload failed
    if ($names) {
      $sql = 'INSERT INTO php_blog_images (filename, caption)
              VALUES (?, ?)';
      $stmt->prepare($sql);
      $stmt->bind_param('ss', $names[0], $_POST['caption']);
      $stmt->execute();
      $imageOK = $stmt->affected_rows;
    }
    // get the image's primary key or find out what went wrong
    if ($imageOK) {
      $image_id = $stmt->insert_id;
    } else {
        if(isset($errors)) {
            // capture exceptions from try/catch
            $imageError = $errors;
        } else {
            // capture exceptions from $loader object (ThumbnailUpload Class)
            $imageError = implode(' ', $loader->getMessages());
        }
    }
  } elseif (isset($_POST['image_id']) && !empty($_POST['image_id'])) {
    // get the primary key of a previously uploaded image from the select menu choice
    $image_id = $_POST['image_id'];
  }

  // don't insert blog details if the image failed to upload
  if (!isset($imageError)) {
    // if $image_id has been set, insert it as a foreign key
    if (isset($image_id)) {
      $sql = 'UPDATE php_blog_blog SET image_id = ?, title = ?, article = ?
              WHERE article_id = ?';
      $stmt->prepare($sql);
      $stmt->bind_param('issi', $image_id, $_POST['title'], $_POST['article'], $_POST['article_id']);
    } else {
      // create SQL
      $sql = 'UPDATE php_blog_blog SET image_id = NULL, title = ?, article = ?
              WHERE article_id = ?';
      $stmt->prepare($sql);
      $stmt->bind_param('ssi', $_POST['title'], $_POST['article'], $_POST['article_id']);
    }
    // execute and get number of affected rows
    $stmt->execute();
    $OK = $stmt->affected_rows;
  }

  // redirect if successful or display error
  if ($OK && !isset($imageError)) {
    header('Location: /php/blog/admin/blog-list.php');
    exit;
  } else {
    $error = $stmt->error;
    if (isset($imageError)) {
      $error .= ' ' . $imageError;
    }
  }
}
# Page specific variables.
$nav = "blog-admin";
$title_section = "Blog: Admin - Insert";

# Create a human friendly name based on file name.
include("../../includes/title-page-name.php");

# The header section of the layout.
include("../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>

            <?php if (isset($error)) {
              echo "<p>Error: $error</p>";
            } ?>
            <form id="form1" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><?php echo $title_page_name; ?></legend>
                    
                    <ol>
        <li <?php if(isset($errorTitle)) echo 'class="error"'?>>
            <label for="title">Title:</label>
            <input name="title" type="text" class="widebox" id="title" value="<?php 
                            if (isset($error)) {
                                echo htmlentities($_POST['title'], ENT_COMPAT, 'utf-8');
                            } else {
                                echo htmlentities($title, ENT_COMPAT, 'utf-8');
                            }
                            ?>">
        </li>
        <li <?php if(isset($errorArticle)) echo 'class="error"'?>>
            <label for="article">Article:</label>
            <textarea name="article" cols="60" rows="8" class="widebox" id="article"><?php 
                            if (isset($error)) {
                                echo htmlentities($_POST['article'], ENT_COMPAT, 'utf-8');
                            } else {
                                echo htmlentities($article, ENT_COMPAT, 'utf-8');
                            }
                            ?></textarea>
        </li>
        <li>
            <label for="image_id">Uploaded image:</label>
            <select name="image_id" id="image_id">
              <option value="">Select image</option>
                    <?php
            // get the list of images
            $getImages = 'SELECT image_id, filename
                         FROM php_blog_images ORDER BY filename';
            $images = $conn->query($getImages);
            while ($row = $images->fetch_assoc()) {
            ?>
              <option value="<?php echo $row['image_id']; ?>"
              <?php
                if (isset($_POST['image_id']) && $row['image_id'] == $_POST['image_id']) {
                    echo 'selected';
                }
                if (isset($image_id) && $row['image_id'] == $image_id) {
                    echo 'selected';
                }
            ?>><?php echo $row['filename']; ?></option>
              <?php } ?>
            </select>
        </li>
        <li id="allowUpload">
            <label for="upload_new">
            <input type="checkbox" name="upload_new" id="upload_new">
            Upload new image</label>
        </li>
        <li class="optional">
            <label for="image">Select image:</label>
            <input type="file" name="image" id="image">
        </li>
        <li class="optional">
            <label for="caption">Caption:</label>
            <input name="caption" type="text" class="widebox" id="caption">
        </li>
        <li>
            <input type="submit" name="update" value="Update Entry">
            <input name="article_id" type="hidden" value="<?= $article_id ?? $_POST['article_id'] ?>">
        </li>
    </ol>
                </fieldset>
            </form>
            <?php include('../../includes/logout_db.php'); ?>
        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("sidebar.php");

# set a variable with the HTML used to load the JavaScript
# then in the footer include check to see if the variable isset if yes echo the content of the variable
$js = '<script src="/php/js/toggle_fields.js"></script>';

# The footer section of the layout.
include("../../includes/footer.php");
?>

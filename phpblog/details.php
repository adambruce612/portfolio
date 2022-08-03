<?php
## Set the timezone to your location
ini_set("date.timezone", "America/Chicago");

require_once '../includes/connection.php';

// Updated New Functions: convertToParas() and getFirst() Ch16/utility_funcs.php
require_once '../includes/utility_funcs.php';

// create database connection
$conn = dbConnect('read');

// check for article_id in query string
if (isset($_GET['article_id']) && is_numeric($_GET['article_id'])) {
    $article_id = (int) $_GET['article_id'];
} else {
    $article_id = 0;
}

$sql = "SELECT
            title, 
            article, 
            DATE_FORMAT(updated, '%W, %M %D, %Y') AS updated, 
            filename, 
            caption
        FROM php_blog_blog 
        LEFT JOIN php_blog_images USING (image_id)
        WHERE php_blog_blog.article_id = $article_id";

$result = $conn->query($sql);
$row = $result->fetch_assoc();


$imageDir = './images/';
if ($row && !empty($row['filename'])) {
    $image = $imageDir . basename($row['filename']);
    if (file_exists($image) && is_readable($image)) {
        $imageSize = getimagesize($image)[3];
    }
}

# Page specific variables.
$nav = "blog";
$title_section = "Blog";

# Create a human friendly name based on file name.
include("../includes/title-page-name.php");

# The header section of the layout.
include("../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>
            <h2><?php if ($row) {
                echo safe($row['title']);
            } else {
                echo 'No record found';
            }
            ?></h2>
            
            <p><?php if ($row) { echo $row['updated']; } ?></p>
            <?php if (!empty($imageSize)) { ?>
            <figure>
                <img src="<?= $image ?>" alt="<?= safe($row['caption']) ?>" <?= $imageSize ?>>
                <figcaption><?= safe($row['caption']) ?></figcaption>
            </figure>
            <?php } if ($row) { echo convertToParas($row['article']); } ?>
            
            <p><a href="<?php
            // check that browser supports $_SERVER variables
            if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST'])) {
                $url = parse_url($_SERVER['HTTP_REFERER']);
                // find if visitor was referred from a different domain
                if ($url['host'] == $_SERVER['HTTP_HOST']) {
                    // if same domain, use referring URL
                    echo $_SERVER['HTTP_REFERER'];
                }
            } else {
                // otherwise, send to main page
                echo '/php/blog';
            } ?>">Back to the blog </a></p>
            

        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("sidebar.php");

# The footer section of the layout.
include("../includes/footer.php");
?>
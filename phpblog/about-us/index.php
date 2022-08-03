<?php
## Set the timezone to your location
ini_set("date.timezone", "America/Chicago");

require_once '../../includes/connection.php';

// Updated New Functions: convertToParas() and getFirst() php-7-solutions/Ch16/utility_funcs.php
require_once '../../includes/utility_funcs.php';
// create database connection
$conn = dbConnect('read');

//About us content
$sql = "SELECT content FROM fp_about_us
         WHERE page_id = 1";

//Random blog post
$sql2 = "SELECT
            article_id,
            image_id,
            title, 
            article, 
            DATE_FORMAT(created, '%W, %M %D, %Y') AS created_date, 
            filename, 
            caption
        FROM php_blog_blog 
        LEFT JOIN php_blog_images USING (image_id)
        ORDER BY RAND() LIMIT 1"; //sorting by created 20xx-xx-xx not the alias

$result = $conn->query($sql);
if (!$result) {
    $error = $conn->error;
}

$result2 = $conn->query($sql2);
if (!$result2) {
    $error = $conn->error;
}

# Page specific variables.
$nav = "about";
$title_section = "About Us";

# Create a human friendly name based on file name.
include("../../includes/title-page-name.php");

# The header section of the layout.
include("../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>
            
                <?php if (isset($error)) {
                echo "<p>$error</p>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo "<h2>{$row['content']}</h2>";

}?>
            <?php if (isset($error)) {
                echo "<p>$error</p>";
            } else {
                while ($row = $result2->fetch_assoc()) {
                    echo "<h2 class=\"clear\">{$row['title']}<span>{$row['created_date']}</span></h2>";
            ?>
                     <?php if (!empty($row['filename'])) { ?>
            
                <img src="/php/blog/images/thumbs/<?= safe($row['filename']) ?>" alt="<?= safe($row['caption']) ?>">
           
            <?php } 
                    if ($row) {
                    
                    $extract = getFirst($row['article']);
                    echo '<p>' . safe($extract[0]);
                    if ($extract[1]) {
                        echo '<a href="details.php?article_id=' . $row['article_id'] . '">
                            Read More&hellip;</a>';
                    }
                    echo '</p>';
                }
            }
                 }
}
            ?>
        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("../sidebar.php");

# The footer section of the layout.
include("../../includes/footer.php");
?>
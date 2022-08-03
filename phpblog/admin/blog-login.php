<?php
## Set the timezone to your location
ini_set("date.timezone", "America/Chicago");

$error = '';
if (isset($_POST['login'])) {
  session_start();
  $username = trim($_POST['username']);
  $password = trim($_POST['pwd']);
  // location to redirect on success
  $redirect = 'blog-list.php';
  require_once('../../includes/authenticate_mysqli.php');
}
    

# Page specific variables.
$nav = "blog-admin";
$title_section = "Blog: Admin - Login";

# Create a human friendly name based on file name.
include("../../includes/title-page-name.php");

# The header section of the layout.
include("../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>
            <?php
            if ($error) {
              echo "<p class=\"error\">$error</p>";
            } elseif (isset($_GET['expired'])) {
            ?>
            <p class="error">Your session has expired. Please log in again.</p>
            <?php } ?>
            <form id="form1" method="post">
                <fieldset>
                    <legend>Login</legend>
                    <ol>
                        <li>
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username">
                        </li>
                        <li>
                            <label for="pwd">Password:</label>
                            <input type="password" name="pwd" id="pwd">
                        </li>
                        <li>
                            <input name="login" type="submit" id="login" value="Log in">
                        </li>
                    </ol>
                    
                </fieldset>
            </form>
        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("sidebar.php");

# The footer section of the layout.
include("../../includes/footer.php");
?>

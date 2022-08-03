<?php
## Set the timezone to your location
ini_set("date.timezone", "America/Chicago");

if (isset($_POST['register'])) {
  $username = trim($_POST['username']);
  $password = trim($_POST['pwd']);
  $retyped = trim($_POST['conf_pwd']);
  require_once('../../../includes/register_user_mysqli.php');
}

# Page specific variables.
$nav = "blog";
$title_section = "Blog: Admin - Register";

# Create a human friendly name based on file name.
include("../../../includes/title-page-name.php");

# The header section of the layout.
include("../../../includes/header.php");
?>

        <main>
            <h2><?php echo $title_section; ?><span><?php echo $title_page_name; ?></span></h2>
            <?php
            if (isset($success)) {
              echo "<p>$success</p>";
            } elseif (isset($errors) && !empty($errors)) {
              echo '<ul>';
              foreach ($errors as $error) {
                echo "<li>$error</li>";
              }
              echo '</ul>';
            }
            ?>
            <form id="form1" method="post" novalidate>
                <fieldset>
                    <legend><?php echo $title_page_name; ?></legend>
                    <ol>
                        <li>
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" required>
                          </li>
                          <li>
                            <label for="pwd">Password:</label>
                            <input type="password" name="pwd" id="pwd" required>
                          </li>
                          <li>
                            <label for="conf_pwd">Confirm password:</label>
                            <input type="password" name="conf_pwd" id="conf_pwd" required>
                          </li>
                          <li>
                            <input name="register" type="submit" id="register" value="Register">
                          </li>
                    </ol>
                </fieldset>
            </form>
        </main>
<?php
# The side-bar section of the layout use custom path to load from a different folder.
include("../sidebar.php");

# The footer section of the layout.
include("../../../includes/footer.php");
?>
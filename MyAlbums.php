<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <?php
    session_start();
    include("./common/header.php");

    if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
        header("Location: Login.php");
    }

    $SID = $_SESSION["UserID"];


    if (isset($_POST["delete"])) {
        DeleteAlbum($_POST["albumToDelete"][0], GetPdo());
    }

    if (isset($_POST["Submit"])) {
        $accessibilityChangeList = array();
        foreach ($_POST["accessibility"] as $item) {
            $accessibilityChangeList[] = $item;
        }
        ChangeAccessibility($accessibilityChangeList, GetPdo());
    }

    $albums = GetAlbums($SID, GetPdo());
    $name = GetUserName($SID, GetPdo());
    $accessibilities = GetAccessibility(GetPdo());
    ?>
    <body>
        <div class="container">

            <h3>My Albums</h3>
            <p>Welcome <?php echo $name; ?> (not you? change user <a href="Login.php">here</a>)</p>
            <div class="row">
                <a class="col-md-2 col-md-offset-10" href="AddAlbum.php">Create New Album</a>
            </div>

            <form action="MyAlbums.php" method="POST">
                <table class="table">
                    <thead>
                    <th>Title</th>
                    <th>Date Updated</th>
                    <th>Number of Pictures</th>
                    <th>Accessibility</th>
                    <th></th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($albums as $album) {
                            echo "<tr>";
                            echo "<td>$album->title</td>";
                            echo "<td>$album->dateUpdated</td>";
                            echo "<td>$album->pictureCount</td>";
                            echo "<td>";
                            echo "<select name='accessibility[]'>";
                            foreach ($accessibilities as $accessibility) {
                                if ($accessibility->accessibilityCode == $album->accessibilityCode) {
                                    echo "<option value='$accessibility->accessibilityCode $album->albumID' selected> $accessibility->description </option>";
                                } else {
                                    echo "<option value='$accessibility->accessibilityCode $album->albumID'> $accessibility->description </option>";
                                }
                            }
                            echo "</select>";
                            echo"</td>";
                            echo " <input type='hidden' name='albumToDelete[]' value='$album->albumID'> ";
                            echo "<td> <input class='btn btn-link' type='submit' name='delete' value='delete' /> </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row form-group">
                    <div class="col-md-2">
                        <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
                        <input class="btn btn-primary" type="submit" name="Clear" value="Clear" />
                    </div>
                </div>
            </form>

        </div>
    </body>
</html>

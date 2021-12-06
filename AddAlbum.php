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

    $SID = $_SESSION["UserID"];
    $name = GetUserName($SID, GetPdo());
    $accessibilities = GetAccessibility(GetPdo());
    $title = "";
    $description = "";
    $accessibilityCode = "";
    $title_err_message = "";


    if (isset($_POST["Submit"])) {
        $title = $_POST["title"];
        $accessibilityCode = $_POST["accessibility"];
        $description = $_POST["description"];

        $title_err_message = ValidateAlbumTitle($title);
        if ($title_err_message == "") {
            AddAlbum($title, $accessibilityCode, $description, $SID, GetPdo());
            header("Location: MyAlbums.php");
        }
    }
    ?>
    <body>
        <div class="container">
            <h3>Create New Album</h3>
            <p>Welcome <?php echo $name; ?> (not you? change user <a href="Login.php">here</a>)</p>
            <form action="AddAlbum.php" method="POST">
                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Title:</label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" name="title" type="text" value="<?php echo $title; ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($title_err_message != "") {
                            echo "<p class='text-danger'>$title_err_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Accessibility:</label>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="accessibility">
                            <?php
                            foreach ($accessibilities as $accessibility) {
                                if ($accessibility->accessibilityCode == $accessibilityCode) {
                                    echo "<option value='$accessibility->accessibilityCode' selected> $accessibility->description </option>";
                                } else {
                                    echo "<option value='$accessibility->accessibilityCode'> $accessibility->description </option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Description:</label>
                    </div>
                    <div class="col-md-4">
                        <textarea class="form-control rounded-0" maxlength="3000" name="description" rows="5"><?php echo $description; ?></textarea>
                    </div>
                </div>

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

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
    $albums = GetAlbums($SID, GetPdo());
    $name = GetUserName($SID, GetPdo());
    $accessibilities = GetAccessibility(GetPdo());
    ?>
    <body>
        <div class="container">

            <h3>My Albums</h3>
            <p>Welcome <?php echo $name; ?> (not you? change user <a href="Login.php">here</a>)</p>
            <div class="row">
                <a class="col-md-2 col-md-offset-10">Create New Album</a>
            </div>

            <table class="table">
                <thead>
                <th>Title</th>
                <th>Date Updated</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                </thead>
                <tbody>
                    <?php
                    foreach ($albums as $album) {
                        echo "<tr>";
                        echo "<td>$album->title</td>";
                        echo "<td>$album->dateUpdated</td>";
                        echo "<td>$album->pictureCount</td>";
                        echo "<td>";
                        echo "<select name='accessibility'>";
                        foreach($accessibilities as $accessibility){
                           if ($accessibility->accessibilityCode == $album->accessibilityCode) {
                               echo "<option value='$album->accessibilityCode' selected> $accessibility->description </option>";
                           }else{
                               echo "<option value='$album->accessibilityCode'> $accessibility->description </option>";
                           }
                        }
                        echo "</select>";
                        echo"</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>

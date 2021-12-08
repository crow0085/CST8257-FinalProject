<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/css/bootstrap.min.css">
        <title></title>
    </head>
    <?php
    session_start();
    include("./common/header.php");


    $valid = false;
    $errMessage = "";
    $SID = "";
    $pass = "";


    if (isset($_POST["Clear"])) {

        $SID = "";
        $pass = "";
    }



    if (isset($_POST["Submit"])) {
        $SID = $_POST["SID"];
        $pass = $_POST["password"];
        $valid = false;
        try {
            $errMessage = ValidateLogin($SID, $pass, GetPdo());

            $valid = true;

            if ($errMessage != "") {
                $valid = false;
            }

            if ($valid) {
                $_SESSION["logged_in"] = true;
                $_SESSION["UserID"] = $SID;
                header("Location: Index.php");
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    ?>
    <body>
        <div class="container">
            <h3 class="text-center">Login</h3>
            <p>You need to <a href="NewUser.php" >Sign Up</a> if you are a new user</p>
            <form action="Login.php" method="POST">

                <div class="row form-group">
                    <div class="col-md-4">
                        <p><?php
                            if (!$valid) {
                                echo "<p class='text-danger'>$errMessage</p>";
                            }
                            ?></p>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">User ID:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="SID" type="text" value="<?php echo "$SID"; ?>" />
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Password:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="password" type="text" value="<?php echo "$pass" ?>" />
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2 col-md-offset-2">
                        <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
                        <input class="btn btn-primary" type="submit" name="Clear" value="Clear" />
                    </div>
                </div>
            </form>
        </div>
    </body>
    <?php include('./common/footer.php'); ?>
</html>

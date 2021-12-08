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
    unset($_SESSION['picId']);
    unset($_SESSION['dropValue']);
    $SID_error_message = "";
    $fname_error_message = "";
    $phone_error_message = "";
    $password_error_message = "";
    $SID = "";
    $fname = "";
    $phone = "";
    $pass = "";
    $password_confirm = "";


    $SID = "";
    $fname = "";
    $phone = "";
    $pass = "";
    $password_confirm = "";

    if (isset($_POST["Clear"])) {

        $SID = "";
        $fname = "";
        $phone = "";
        $pass = "";
        $password_confirm = "";
    }

    if (isset($_POST["Submit"])) {

        $SID = $_POST["SID"];
        $fname = $_POST["fname"];
        $phone = $_POST["phone"];
        $pass = $_POST["password"];
        $password_confirm = $_POST["password_confirm"];
        $valid = false;

        try {
            $SID_error_message = ValidateStudentID($SID, GetPdo());
            $fname_error_message = ValidateName($fname);
            $phone_error_message = ValidatePhone($phone);
            $password_error_message = ValidatePassword($pass, $password_confirm);


            $valid = true;

            if ($SID_error_message != "" || $fname_error_message != "" || $phone_error_message != "" || $password_error_message != null) {
                $valid = false;
            }

            if ($valid) {
                $hash_pass = hash("sha256", $pass);
                $sql = 'INSERT INTO User VALUES (:SID, :fname, :phone,:hash_pass);';
                $pSql = GetPdo()->prepare($sql);
                $pSql->execute(['SID' => $SID, 'fname' => $fname, 'phone' => $phone, 'hash_pass' => $hash_pass]);
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
            <h3 class="text-center">Sign Up</h3>
            <p>All fields are required</p>

            <form action="NewUser.php" method="POST">
                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">User ID:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="SID" type="text" value="<?php echo "$SID"; ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($SID_error_message != "") {
                            echo "<p class='text-danger'>$SID_error_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Name:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="fname" type="text" value="<?php echo "$fname"; ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($fname_error_message != "") {
                            echo "<p class='text-danger'>$fname_error_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Phone Number: <br/> (###-###-####)</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="phone" type="text" value="<?php echo "$phone"; ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($phone_error_message != "") {
                            echo "<p class='text-danger'>$phone_error_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Password:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="password" type="text" value="<?php echo "$pass" ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($password_error_message != "") {
                            echo "<p class='text-danger'>$password_error_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2">
                        <label class="font-weight-bold">Password Again:</label>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="password_confirm" type="text" value="<?php echo $password_confirm ?>" />
                    </div>
                    <div class="col-md-4">
                        <?php
                        if ($password_error_message != "") {
                            echo "<p class='text-danger'>$password_error_message</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-2 offset-md-2">
                        <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
                        <input class="btn btn-primary" type="submit" name="Clear" value="Clear" />
                    </div>
                </div>

            </form>
        </div>
    </body>
    <?php include('./common/footer.php'); ?>
</html>

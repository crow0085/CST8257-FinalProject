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

    function ValidateLogin($SID, $pass, $myPDO) {
        $hash_pass = hash("sha256",$pass);
        $sql = 'SELECT StudentID FROM Student WHERE Password = :hash_pass AND StudentID = :SID';
        $pSql = $myPDO->prepare($sql);
        $pSql->execute(['hash_pass' =>$hash_pass , 'SID' => $SID]);

        if ($pSql->rowCount() == 0) {
            return "incorrect student id or password";
        } else {
            return "";
        }
    }
    
    if (isset($_POST["Clear"])) {
        
        $SID = "";
        $pass = "";
    }

    $valid = false;
    
    if (isset($_POST["Submit"])) {

        $myPDO;

        $SID = $_POST["SID"];
        $pass = $_POST["password"];

        $valid = false;

        try {
            $dbConnection = parse_ini_file("Lab5.ini");

            extract($dbConnection);

            $myPDO = new PDO($dsn, $user, $password);

            $errMessage = ValidateLogin($SID, $pass, $myPDO);

            $valid = true;

            if ($errMessage != "") {
                $valid = false;
            }

            if ($valid) {
                $_SESSION["logged_in"] = true;
                $_SESSION["StudentID"] = $SID;
                header("Location: CourseSelection.php");
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        $myPdo = null;
    }

    include("./common/header.php");
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
                        <label class="font-weight-bold">Student ID:</label>
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

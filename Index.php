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
    ?>
    <body>
        <div class="container">
            <h3 class="text-center">Welcome to Algonquin Social Media Website</h3>
            <p>If you have never used this before, you have to <a href="NewUser.php" >Sign Up</a> first</p>
            <p>If you have used this before, you can <a href="Login.php" >log in</a> now</p>
        </div>
    </body>
    <?php include('./common/footer.php'); ?>
</html>

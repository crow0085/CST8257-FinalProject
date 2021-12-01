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
    
        session_destroy();
        header("Location: Index.php");
        
        include("./common/header.php");
        ?>
    <body>
        
    </body>
    <?php include('./common/footer.php'); ?>
</html>

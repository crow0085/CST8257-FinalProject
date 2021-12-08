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
        if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
        header("Location: Login.php");
    }
        include("./common/header.php");
        $SID = $_SESSION["UserID"];
        $name = GetUserName($SID, GetPdo());
        
    ?>
    <body>
        <div class="container">
            <h3>Add Friends</h3>
            <p>Welcome <?php echo $name; ?> (not you? change user <a href="Login.php">here</a>)</p>
            <p>Enter the ID of the user you want to friend</p>
            <?php
            
            $requesteeId = $_POST["requesteeId"];
            if(isset($requesteeId)){
             if($requesteeId == $SID){
                        echo "<p class='text-danger'>You cant friend yourself</p>";
                    }else{
            if(isset($requesteeId)){
                $requesteeId = trim($requesteeId);                
            }
            //joe ac456 requester wants to friend chrisH requestee
            //1 check if chrisH exists 
            //2 if he does check if chrisH sent ac456 a request 
            //3 if he did chrisH is requester and ac456 is requestee
            if(isset($_POST['request'])){
                $sql4 = "SELECT UserId, Name FROM User WHERE UserId = :requesteeId AND NOT EXISTS (SELECT * FROM Friendship WHERE Friend_RequesteeId = :requesteeId)";
                $pStmt4 = GetPdo()->prepare($sql4);
                $pStmt4->execute(['requesteeId'=>$requesteeId]);
                $row4 = $pStmt4->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT UserId, Name FROM User WHERE UserId = :requesteeId";
                $pStmt = GetPdo()->prepare($sql);
                $pStmt->execute(['requesteeId'=>$requesteeId]);
                $row = $pStmt->fetch(PDO::FETCH_ASSOC);
                if($row){                    
                    $sql2 = "SELECT * FROM Friendship WHERE (Friend_RequesterId = :requester OR Friend_RequesterId = :requestee) AND (Friend_RequesteeId = :requestee OR  Friend_RequesteeId = :requester) AND Status = 'accepted'";
                    $pStmt2 = GetPdo()->prepare($sql2);
                    $pStmt2->execute(['requester'=>$SID, 'requestee'=>$requesteeId]);
                    $row2 = $pStmt2->fetch(PDO::FETCH_ASSOC);
                    
                    $sql1 = "SELECT * FROM Friendship WHERE Friend_RequesterId = :requester AND Friend_RequesteeId = :requestee AND Status = 'request'";
                    $pStmt1 = GetPdo()->prepare($sql1);
                    $pStmt1->execute(['requester'=>$requesteeId, 'requestee'=>$SID]);
                    $row1 = $pStmt1->fetch(PDO::FETCH_ASSOC);
                    if($row1){
                        $sql1 = "UPDATE Friendship SET Status = 'accepted' WHERE Friend_RequesterId = :requester AND Friend_RequesteeId = :requestee";
                        $pStmt1 = GetPdo()->prepare($sql1);
                        $pStmt1->execute(['requester'=>$requesteeId, 'requestee'=>$SID]);                        
                        echo "<p class='text-danger'>".$requesteeId." has already sent you a friend request. You have just become friends. </p>";
                        
                    }
                    elseif($row2){
                        echo "<p class='text-danger'>You and ".$row2['Friend_RequesterId']. " are already friends </p>";
                        
                    }
                    elseif($row4){
                        $sql3 = "INSERT INTO Friendship VALUES (:requester, :requestee, 'request')";
                        $pStmt3 = GetPdo()->prepare($sql3);
                        $pStmt3->execute(['requester'=>$SID, 'requestee'=>$requesteeId]);
                        echo "<p class='text-danger'>Your request was sent to ".$requesteeId." Once ".$row4['Name']." accepts, you will see each other's albums</p>";    
                    
                        
                    }
                   
                    
                }
            }
               
                else{
                    echo "This user doesn't exist";
                }

                
            }
            
                    }
            ?>
            <form method="post" action="AddFriend.php">
                <div class="row form-group">
                    <label class="control-label font-weight-bold col-md-2">ID:</label>
                    
                <div class="col-sm-2">
                   <input class="form-control" name="requesteeId" type="text" value="<?php echo $requesteeId; ?>" />
                </div>
                <div class="col-sm-2">
                    <input class="btn btn-primary" type="submit" name="request" value="Send Friend Request" />
                </div>            
            
                </div>
           </form>

    </body>
</html>

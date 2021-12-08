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
    unset($_SESSION['picId']);
    unset($_SESSION['dropValue']);
        include("./common/header.php");
        $SID = $_SESSION["UserID"];
        $name = GetUserName($SID, GetPdo());
    ?>
    <body>
        
        <div class="container">
            <h3 class="text-center">My Friends</h3>
            <p>Welcome <?php echo $name; ?> (not you? change user <a href="Login.php">here</a>)</p>
            <?php
          
            
            ?>
            
        <form method="post" action="MyFriends.php">
            <table class="table">
                <thead>
                    <tr><td></td><td></td><td><a href="AddFriend.php">Add Friends</a></td></tr>
                    <tr>
                        <th>Name</th><th>Shared Albums</th><th>Unfriend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    if(isset($_POST['unfriend'])){
                        if(!empty($_POST['checkedFriends'])){
                            foreach($_POST['checkedFriends'] as $selectedId){
                                //echo $selectedId;
                                $sqlRegD = GetPdo()->prepare("DELETE FROM Friendship WHERE (Friend_RequesterId = :Id AND Friend_RequesteeId = :SID) OR (Friend_RequesteeId = :Id AND Friend_RequesterId = :SID)");
                                $result1 = $sqlRegD->execute(['Id'=>$selectedId,'SID'=>$SID]);
                            }
                            
                            
                        }
                    }
                    
                     $sql = "SELECT Friend_RequesteeId FROM Friendship "
                . "WHERE Friend_RequesterId = :userId AND Status = 'accepted'";
                    $pStmt = GetPdo()->prepare($sql);
                    $pStmt->execute(['userId'=>$SID]);
                    $row = $pStmt->fetchAll();
                    
                   				
                    $sql1 = "SELECT Friend_RequesterId FROM Friendship "
                . "WHERE Friend_RequesteeId = :userId AND Status = 'accepted'";
                    $pStmt1 = GetPdo()->prepare($sql1);
                    $pStmt1->execute(['userId'=>$SID]);
                    $row1 = $pStmt1->fetchAll();
                    
                    $result= array();
                    if(!empty($row) && !empty($row1)){
                        foreach($row as $item){
                            array_push($result,$item['Friend_RequesteeId']);
                        }
                        foreach($row1 as $item){
                            array_push($result,$item['Friend_RequesterId']);
                        }


                    }
                    elseif(!empty($row)){
                        foreach($row as $item){
                            array_push($result,$item['Friend_RequesteeId']);
                        }
                    }
                    elseif(!empty($row1)){
                        foreach($row1 as $item){
                            array_push($result,$item['Friend_RequesterId']);
                        }

                    }
                    else{
                        echo "You have no friends";

                    }
                    
                    if(!empty($result)){
                        foreach($result as $item){
                        $sql2 = "select UserId, Name, (SELECT count(Owner_Id) FROM album
                                WHERE Owner_Id = :item AND Accessibility_Code = 'shared') as Co from user Where UserId = :item";
                        $pStmt2 = GetPdo()->prepare($sql2);
                        $pStmt2->execute(['item'=>$item]); 
                            while($row2 = $pStmt2->fetch(PDO::FETCH_ASSOC)){
                                echo "<tr><td><a href='FriendsAlbum.php?fId=".$row2['UserId']."'>".$row2['Name']."</a></td><td>".$row2['Co']."</td><td><input type='checkbox' name = 'checkedFriends[]' value=".$row2['UserId']."></input></td></tr>";
                            //echo "<tr><td>".$row2['Name']."</td><td>".$row2['Co']."</td><td><input type='checkbox' name = 'checkedFriends[]' value=".$row2['UserId']."></input></td></tr>";
                            }
                        }



                    }

                  
                    
                    
//                    if(!empty($result)){
//                        foreach($result as $item){
//                        $sql2 = "SELECT UserId, Name, COUNT(Album_Id) FROM User LEFT OUTER JOIN Album ON User.UserId = Album.Owner_Id WHERE UserId = :item";    
                        //$sql2 = "SELECT UserId, Name, COUNT(Album_Id) FROM User INNER JOIN Album ON User.UserId = Album.Owner_Id WHERE "
                         //       . "UserId = :item AND Accessibility_Code = 'shared'";
//                        $pStmt2 = GetPdo()->prepare($sql2);
//                        $pStmt2->execute(['item'=>$item]); 
//                        while($row2 = $pStmt2->fetch(PDO::FETCH_ASSOC)){
//                            echo "<tr><td>".$row2['Name']."</td><td>".$row2['COUNT(Album_Id)']."</td><td><input type='checkbox' name = 'checkedFriends[]' value=".$row2['UserId']."></input></td></tr>";                                                       
//                           
//                        }
//                        
//                    }
//                        
//                    }
                    
                    
                    

                    ?>
                    <tr>
                        
                    </tr>
                    
                </tbody>
                
            </table>
            

                <div class="row form-group">
                    <div class="col-sm-2">
                        <input class="btn btn-primary" type="submit" name="unfriend" onclick="return confirm('The selected friends will be unfriended!');" value="Unfriend Selected" />
                    </div>
                </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th><th></th><th class="col-md-4">Accept or Deny</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(isset($_POST['Deny'])){
                        if(!empty($_POST['checkedRequests'])){
                            foreach($_POST['checkedRequests'] as $selectedIds){
                                $sqlRegD = GetPdo()->prepare("DELETE FROM Friendship WHERE Friend_RequesterId = :Id AND Friend_RequesteeId = :SID AND Status = 'request'");
                                $result1 = $sqlRegD->execute(['Id'=>$selectedIds,'SID'=>$SID]);
                            }
                            
                            
                        }
                    }
                    if(isset($_POST['Accept'])){
                        if(!empty($_POST['checkedRequests'])){
                            foreach($_POST['checkedRequests'] as $selectedIds){
                                $sqlRegD = GetPdo()->prepare("UPDATE Friendship SET Status = 'accepted' WHERE Friend_RequesterId = :Id AND Friend_RequesteeId = :SID");
                                $result1 = $sqlRegD->execute(['Id'=>$selectedIds,'SID'=>$SID]);
                            }
                            
                            
                        }
                    }
                    
                    $sql5= "SELECT Name, Friend_RequesterId FROM Friendship INNER JOIN User ON Friendship.Friend_RequesterId = User.UserId WHERE Friend_RequesteeId = :userId AND Status = 'request'";                
                    $pStmt5 = GetPdo()->prepare($sql5);
                    $pStmt5->execute(['userId'=>$SID]);
                    while($row5 = $pStmt5->fetch(PDO::FETCH_ASSOC)){ 
                        echo "<tr><td>".$row5['Name']."</td><td class='col-md-3'></td><td><input type='checkbox' name = 'checkedRequests[]' value=".$row5['Friend_RequesterId']."></input></td></tr>";                                                       
                           
                    }
                    
                    
                    ?>
                    
                </tbody>
                
            </table>
            <div class="row form-group">
                    <div class="col-md-2">
                        <input class="btn btn-primary" type="submit" name="Accept" value="Accept Selected" />
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary" type="submit" name="Deny" onclick="return confirm('The selected requests will be denied!');" value="Deny Selected" />

                    </div>
                </div>
            
        </form> 
      </div>

    </body>
</html>

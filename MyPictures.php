<?php

session_start();
extract ( $_POST ) ;
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
        header("Location: Login.php");
    }
include("./common/header.php");
$MyPDO = GetPdo();
$SID = $_SESSION["UserID"];
$albums = GetAlbums($SID, $MyPDO);
if(isset($_POST['Change'])){
    if($dropValue != -1){
    $sql = "SELECT `Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added` FROM `picture` WHERE Album_Id = :albumId;";
    $pSql = $MyPDO->prepare($sql);
    $pSql->execute(['albumId' => $dropValue]);
    $result = $pSql->fetchAll();
    }                    
    
}
?>

<form action="MyPictures.php" method="post">
    <div class="row">
<select id="dropDown" name="dropValue" class="col-lg-2 text-right">
                <option value="-1">Select An Album</option>
                <?php
                foreach ($albums as $row) {
                    echo"<option value='" . $row->albumID . "'";
                    
                     
                    echo">" .$row->title. "</option>";
                }
                
                
                ?>
            </select>
    </div>
    <div class="container">
    <div class="row" style="">
        <div class="" style="min-height:400px;min-width:600px; border: 10px solid #969696;background:; display:inline-block; float:left;">
        </div>
        <div class="" style=" border: 10px solid #969696;min-height:400px; min-width:250px; max-width:100px; overflow-y: auto; background:yellow; float:left;display:inline-block;">
        </div>
    </div>
    <div style="width:850px;  max-height:100px; min-height:100px;border: 10px solid #969696; max-height: 100px; overflow:scroll; overflow-y:hidden;background:;">
     <?php 
    if(isset($_POST["Change"])){
    foreach ($result as $row){
                    echo"<img src='./Pictures/".$row['FileName']."'  width='200' height='200'>";
                        
                }
    
    }
    ?>
    </div>
   
    </div>
    <input type="submit" name="Change" />
</form>
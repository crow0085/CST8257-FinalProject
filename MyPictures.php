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
<select id="dropDown" name="dropValue" class="col-lg-2 text-right">
                <option value="-1">Select An Album</option>
                <?php
                foreach ($albums as $row) {
                    echo"<option value='" . $row->albumID . "'";
                    
                     
                    echo">" .$row->title. "</option>";
                }
                
                
                ?>
            </select>
    
    <?php 
    if(isset($_POST["Change"])){
    foreach ($result as $row){
                    echo"<img src='/Pictures/".$row['FileName']."  width='200' height='200'>";
                        
                }
    
    }
    ?>
    <input type="submit" name="Change" />
</form>
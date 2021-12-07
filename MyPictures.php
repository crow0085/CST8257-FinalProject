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
if(isset($_POST['Change']) || isset($_GET['picId'])){
    if (!isset($dropValue) && isset($_SESSION['dropValue'])){
       $dropValue=$_SESSION['dropValue']; 
   }
        
    
    if($dropValue != -1){
    $sql = "SELECT `Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added` FROM `picture` WHERE Album_Id = :albumId;";
    $pSql = $MyPDO->prepare($sql);
    $pSql->execute(['albumId' => $dropValue]);
    $result = $pSql->fetchAll();
    $_SESSION['dropValue'] = $dropValue;
    }                    
    
}
if(isset($_GET['picId'])){
    $picId = $_GET['picId'];
    $sql = "SELECT `Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added` FROM `picture` WHERE Picture_Id = :picId";
    $pSql = $MyPDO->prepare($sql);
    $pSql->execute(['picId' => $picId]);
    
    $mooshoo = $pSql->fetch(PDO::FETCH_ASSOC);
    
}else{
    
}
?>

<form action="MyPictures.php" method="post">
    <div class="container" style="padding-left: 300px">
<select id="dropDown" name="dropValue" class="col-lg-3 text-right" >
                <option value="-1">Select An Album</option>
                <?php
                foreach ($albums as $row) {
                    echo"<option value='" . $row->albumID . "'";
                    
                     
                    echo">" .$row->title. "</option>";
                }
                
                
                ?>
            </select>
    </div>
    <div class="text-center">
      <?php 
        if(isset($_GET['picId'])){
            Echo$mooshoo['Title'];
        }
        ?>
    </div>
    <div class="container-fluid" style="width:900px;margin:auto; padding-top: 25px;">
    <div >
        <div class="" style="min-height:400px;min-width:600px; border: 1px solid #969696;background:; display:inline-block; float:left;">
        <?php 
        if(isset($_GET['picId'])){
            echo"<img src='./Pictures/".$mooshoo['FileName']."'width='600' height='400'>";
        }
        ?>
        </div>
        <div class="" style=" position:relative;border: 1px solid #969696;min-height:400px; min-width:250px; max-width:100px; overflow-y: auto; background:; float:left;display:inline-block;">
        <?php 
        if(isset($_GET['picId'])){
            Echo$mooshoo['Description'];
        }
        ?>
            <div style="position:absolute;bottom:0px;">
                <textarea name="comment" rows="4" cols="32" style="resize:none;"></textarea>
            </div>
        </div>
    </div>
    <div style="max-width:850px; min-width:850px; max-height:135px; min-height:125px;border: 1px solid #969696;  white-space:nowrap;overflow-x:auto;background:;">
     <?php 
    if(isset($_POST["Change"]) || isset($_GET["picId"])){
    foreach ($result as $row){
    echo"<a href='MyPictures.php?picId=".$row['Picture_Id']."'><img src='./thumbnails/".$row['FileName']."' width='150' height='115' style='border:1px solid #969696'></a>";
                        
                }
    
    }
    ?>
    </div>
   
    </div>
    <div style="padding-left:300px;">
    <input type="submit" name="Change" id="clicky" hidden/>
    </div>
</form>

<script>
    document.getElementById("dropDown").addEventListener('change', function (){
            var clicky = document.getElementById('clicky');
            clicky.click();
});
    
    </script>


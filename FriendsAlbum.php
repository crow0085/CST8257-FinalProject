<?php
session_start();

extract($_POST);
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
    header("Location: Login.php");
}
include("./common/header.php");
$MyPDO = GetPdo();
$SID = $_SESSION["UserID"];
if(isset($_GET['fId'])){
    $_SESSION['fId'] = $_GET['fId'];
}
if(isset($_SESSION['fId'])){
    $fId = $_SESSION['fId'];
}
$albums = GetAlbums($fId, $MyPDO);
if (!isset($_GET['picId']) && isset($_SESSION['picId'])) {
    $_GET['picId'] = $_SESSION['picId'];
}
if (isset($_GET['picId'])) {
    $_SESSION['picId'] = $_GET['picId'];
}
if (isset($_POST['Change']) || isset($_GET['picId']) || isset($_POST['commentSubmit'])) {
    if (!isset($dropValue) && isset($_SESSION['dropValue'])) {

        $dropValue = $_SESSION['dropValue'];
    }

    if ($dropValue != -1) {
        $sql = "SELECT `Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added` FROM `picture` WHERE Album_Id = :albumId;";
        $pSql = $MyPDO->prepare($sql);
        $pSql->execute(['albumId' => $dropValue]);
        $result = $pSql->fetchAll();
    }
    $_SESSION['dropValue'] = $dropValue;
}
if (isset($_GET['picId']) || isset($_POST['commentSubmit'])) {
    $picId = $_GET['picId'];
    $sql = "SELECT `Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added` FROM `picture` WHERE Picture_Id = :picId";
    $pSql = $MyPDO->prepare($sql);
    $pSql->execute(['picId' => $picId]);
    if (isset($_POST['comment'])) {
        $sql3 = "INSERT INTO Comment VALUES(NULL, :authorId, :pictureId, :commentText, :date)";
        $pSql2 = $MyPDO->prepare($sql3);
        $pSql2->execute(['authorId' => $SID, 'pictureId' => $picId, 'commentText' => $comment, 'date' => date("Y-m-d G:i:s", time())]);
    }
    $mooshoo = $pSql->fetch(PDO::FETCH_ASSOC);
    $sql2 = "SELECT * FROM Comment "
            . "INNER JOIN User ON Comment.Author_Id = User.UserId WHERE Picture_Id = :pictureId order by Date DESC";
    $pSql3 = $MyPDO->prepare($sql2);
    $pSql3->execute(['pictureId' => $picId]);
    $result3 = $pSql3->fetchAll();
    $dum = date("Y-d-d G:i:s", time());
} else {
    
}
?>

<form action="FriendsAlbum.php" method="post">
    <div class='container' style='padding-left:200px;'>
        <div class="row">
            <h3> <?php echo$fId; ?> Albums</h3>
        </div>
        <div class="row">
            <select id="dropDown" name="dropValue" class="col-lg-3 text-right" >
                <option value="-1">Select An Album</option>
<?php
foreach ($albums as $row) {
    echo"<option value='" . $row->albumID . "'";
    if (isset($_SESSION['dropValue'])) {
        if ($_SESSION['dropValue'] == $row->albumID) {
            echo "selected";
        }
    }

    echo">" . $row->title . "</option>";
}
?>
            </select>
            <div class="text-left">
                <?php
                if (isset($_GET['picId']) && !isset($_POST['Change'])|| isset($_POST['commentSubmit'])&& !isset($_POST['Change'])) {
                    if(isset($dropValue)){
            if($dropValue != -1){
                    Echo"<h3 class='align-left'>" . $mooshoo['Title'] . "</h3>";}}
                } else if (!isset($_POST['Change'])) {
                    
                }
                ?>
        </div>
        </div>
        
    </div>
    <div class="container-fluid" style="width:1050px;margin:auto; padding-top: 15px;">
        <div >
            <div class="" style="min-height:400px;min-width:700px;  display:inline-block; float:left;">
<?php
if (isset($_GET['picId']) && !isset($_POST['Change']) || isset($_POST['commentSubmit']) && !isset($_POST['Change'])) {
    echo"<img src='./Pictures/" . $mooshoo['FileName'] . "'width='700' height='400'>";
} else if (isset($_POST['Change'])) {
    if (isset($dropValue)) {
        if ($dropValue != -1) {
            echo"<div class='text-left'><h3> Select a picture</h3></div>";
        }
    }
}
?>
            </div>
            <div class="" style=" position:relative;min-height:400px; min-width:300px; max-width:300px; overflow-y: auto; background:; float:left;display:inline-block;">
                <div style='position:absolute; height:270px; width:298px;  overflow-wrap: break-word;overflow-y:auto;'> 
                    <?php
                    if (isset($_GET['picId']) && !isset($_POST['Change']) || isset($_POST['commentSubmit']) && !isset($_POST['Change'])) {
                        Echo"<b>Description</b></br>" . $mooshoo['Description'] . "</br></br>";

                        foreach ($result3 as $row) {
                            echo"<p><b style='color:blue;'>" . $row['UserId'] . "</b> - <i>" . $row['Date'] . "</i> -" . $row['Comment_Text'] . "</p></br>";
                        }
                    }
                    ?>    
                </div>
                    <?php
                    if (isset($_GET['picId']) && !isset($_POST['Change'])) {
                        echo"<div style='position:absolute;bottom:0px;'>"
                        . "<textarea name='comment' rows='4' cols='39' style='resize:none;'></textarea>"
                        . "<input type='submit' class='btn btn-primary' value='AddComment' name='commentSubmit'>"
                        . "</div>";
                    }
                    ?>
            </div>
        </div>
        <div style="max-width:1000px; min-width:1000px; max-height:135px; min-height:135px;  white-space:nowrap;overflow-x:auto;background:;">
            <ul class="navigation-menu" style='display:flex;'>
<?php
if (isset($_POST["Change"]) || isset($_GET["picId"]) || isset($_POST['commentSubmit'])) {
    if (isset($dropValue)) {
        if ($dropValue != -1) {
            foreach ($result as $row) {
                echo"<li class='";
                if(isset($_GET["picId"])){ if($_GET['picId']== $row['Picture_Id']){
                    echo "target";
                }}
                echo"'>'<a href='FriendsAlbum.php?picId=" . $row['Picture_Id'] . "'href='#".$row['Picture_Id']."'><img src='./thumbnails/" . $row['FileName'] . "' width='150' height='100' style='border:1px solid #969696'></a></li>";
            }
        }
    }
}
?>
            </ul>
        </div>

    </div>
    <div style="padding-left:300px;">
        <input type="submit" name="Change" id="clicky" hidden/>
    </div>
</form>

<script>
    document.getElementById("dropDown").addEventListener('change', function () {
        var clicky = document.getElementById('clicky');
        clicky.click();
    });


    $(document).ready(function () {
        $('ul li a').click(function () {
            $('li a').removeClass("active");
            $(this).addClass("active");
        });
    });
</script>



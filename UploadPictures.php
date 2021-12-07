<?php
session_start();
unset($_SESSION['picId']);
unset($_SESSION['dropValue']);
extract ( $_POST ) ;
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
        header("Location: Login.php");
    }
include("./common/header.php");
$MyPDO = GetPdo();
$SID = $_SESSION["UserID"];
$albums = GetAlbums($SID, $MyPDO);

define("IMAGE_DESTINATION", "./Pictures"); 
define("IMAGE_MAX_WIDTH", 800);
define("IMAGE_MAX_HEIGHT", 600);

define("THUMB_DESTINATION", "./thumbnails");  
define("THUMB_MAX_WIDTH", 100);
define("THUMB_MAX_HEIGHT", 100);
if (isset($_POST['btnUpload'])) 
{
    $destination = './Pictures';       	// define the path to a folder to save the file

	if (!file_exists($destination))
	{
		mkdir($destination);
	}
	for ($j = 0; $j < count($_FILES['txtUpload']['tmp_name']); $j++)
	{
		if ($_FILES['txtUpload']['error'][$j] == 0)
		{
			$fileTempPath = $_FILES['txtUpload']['tmp_name'][$j];
			$filePath = $destination."/".$_FILES['txtUpload']['name'][$j];
			
			$pathInfo = pathinfo($filePath);
			$dir = $pathInfo['dirname'];
			$fileName = $pathInfo['filename'];
			$ext = $pathInfo['extension'];
			$dbfileName = $fileName .".".$ext;
			$i="";
			while (file_exists($filePath))
			{	
				$i++;
				$filePath = $dir."/".$fileName."_".$i.".".$ext;
			}
			move_uploaded_file($fileTempPath, $filePath);
                        //resamplePicture($filePath, IMAGE_DESTINATION, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
                        resamplePicture($filePath, THUMB_DESTINATION, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
                        
                        $sql = "INSERT INTO `picture` (`Picture_Id`, `Album_Id`, `FileName`, `Title`, `Description`, `Date_Added`) VALUES (NULL, :albumId, :fileName, :title, :description, :dateAdded)";
                         $pSql = $MyPDO->prepare($sql);
                        $pSql->execute(['albumId' => $dropValue, 'fileName' => $dbfileName, 'title' => $title, 'description' => $description, 'dateAdded' => date('Y-m-d')]);
                        
                        }
		elseif ($_FILES['txtUpload']['error'][$j]  == 1)
		{			
			echo "$fileName is too large <br/>";
		}
		elseif ($_FILES['txtUpload']['error'][$j]  == 4)
		{
			echo "No upload file specified <br/>"; 
		}
		else
		{
			echo "Error happened while uploading the file(s). Try again late<br/>"; 
		}
	}   
        
	echo "<h2>All Uploaded Files</h2>";
	$files = scandir($destination);
	
                 
}
?>
<div class="container" style="padding-top: 5px">
    <h3>Upload Files</h3>
   <form action="UploadPictures.php" method="post"  enctype="multipart/form-data">
       <div class="row">
           <p>
               Allowed File types are JPEG PNG and GIF.
               </br>
               You can upload multiple files at once by pressing the shift key when selecting pictures.</br>
               When uploading pictures the description and text field apply to all pictures</br>
           </p>
       </div>
       <div class="row">
        File to Upload:&nbsp; <input type="file" name="txtUpload[]" multiple size='40'/>
       </div>
        <br/><br/>
        
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
        <div class="row">
            </br>
            </br>
            <label>Title</label>
        </div>
        <div class="row">
        <textarea  name="title"  rows="4"  cols="50"></textarea>
        </div>
        <div class="row">
            <label>Description</label>
        </div>
        <div class="row">
        <textarea name="description"  rows="4"  cols="50" ></textarea>
        </div>
        <div class="row">
		<input type="submit" name="btnUpload" value="Upload" />
		&nbsp; &nbsp; &nbsp;<input type="reset" name="btnReset" value="Reset" />
        </div>
   </form> 
</div>

<?php include("./common/footer.php"); ?>



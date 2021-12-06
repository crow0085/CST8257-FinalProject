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
			
			$i="";
			while (file_exists($filePath))
			{	
				$i++;
				$filePath = $dir."/".$fileName."_".$i.".".$ext;
			}
			move_uploaded_file($fileTempPath, $filePath);
                        $sql = "INSERT INTO 'Picture' ('Picture_Id',Album_Id', 'File_Name', 'Title', 'Description', 'Date_Added') VALUES(NULL :albumId, :fileName, :title, :description, :dateAdded)";
                        $pSql = $MyPDO->prepare($sql);
                        $pSql->execute(['albumId' => $dropValue, 'fileName' => $fileName, 'title' => $title , 'description' => $description , 'dateAdded' => date('Y-m-d')]);
               
                        
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
	foreach($files as $file)
	{   
		echo $file."<br/>"; 
	}
                 
}
?>

   <form action="UploadPictures.php" method="post"  enctype="multipart/form-data">
        File to Upload:&nbsp; <input type="file" name="txtUpload[]" multiple size='40'/>
        <br/><br/>
         <select id="dropDown" name="dropValue" class="col-lg-2 text-right">
                <option value="-1">Select An Album</option>
                <?php
                foreach ($albums as $row) {
                    echo"<option value='" . $row->albumID . "'";
                    
                     
                    echo">" .$row->title. "</option>";
                }
                ?>
            </select>
        <textarea  name="title"  rows="4"  cols="50"></textarea>
        <textarea name="description"  rows="4"  cols="50" ></textarea>
		<input type="submit" name="btnUpload" value="Upload" />
		&nbsp; &nbsp; &nbsp;<input type="reset" name="btnReset" value="Reset" />
   </form> 

<?php include("./common/footer.php"); ?>



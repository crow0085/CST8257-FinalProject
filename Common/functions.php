<?php

function ValidateName($fname) {
    if ($fname == "") {
        return "name cannot be blank";
    } else {
        return "";
    }
}

function ValidatePhone($phone) {
    if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
        return "phone number cannot be blank and must be in the format ###-###-####";
    } else {
        return "";
    }
}

function ValidateStudentID($SID, $myPDO) {

    $sql = "SELECT UserId FROM User WHERE UserId = '$SID'";
    $resultSet = $myPDO->query($sql);
    $row = $resultSet->fetch(PDO::FETCH_ASSOC);

    if ($SID == "") {
        return "User id cannot be blank";
    } else if ($row) {
        return "User id already exists";
    } else {
        return "";
    }
}

function ValidatePassword($pass, $password_confirm) {
    $upper = '/[A-Z]/';
    $lower = '/[a-z]/';
    $num = '/[0-9]/';
    if ($pass != $password_confirm) {
        return "passwords must match!";
    }
    if (preg_match($upper, $pass) && preg_match($lower, $pass) && preg_match($num, $pass) && strlen($pass) >= 6) {
        return "";
    } else {
        return "Password must contain one uppercase, one lowercase and one number and at least 6 characters";
    }
}

function ValidateLogin($SID, $pass, $myPDO) {
    $hash_pass = hash("sha256", $pass);
    $sql = 'SELECT UserId FROM User WHERE Password = :hash_pass AND UserId = :SID';
    $pSql = $myPDO->prepare($sql);
    $pSql->execute(['hash_pass' => $hash_pass, 'SID' => $SID]);

    if ($pSql->rowCount() == 0) {
        return "incorrect user id or password";
    } else {
        return "";
    }
}

function GetPdo() {
    $dbConnection = parse_ini_file("Assignment.ini");
    extract($dbConnection);
    $myPDO = new PDO($dsn, $user, $password);
    return $myPDO;
}

function GetUserName($SID, $myPDO) {

    $sql = "select UserId, Name from User where UserId = :user_id";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['user_id' => $SID]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    return $row['Name'];
}

function GetAlbums($SID, $myPDO) {
    $sql = "select * from Album where Owner_Id = :user_id";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['user_id' => $SID]);

    $albums = array();

    foreach ($pStmt as $row) {
        $album = new Album($row['Album_Id'], $row['Title'], $row['Description'], $row['Date_Updated'], $row['Owner_Id'], $row['Accessibility_Code']);
        $albums[] = $album;
    }

    foreach ($albums as $album) {
        $sql = "select count(*) as total from Picture where Album_Id = :album_id";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['album_id' => $album->albumID]);
        $row = $pStmt->fetch(PDO::FETCH_ASSOC);
        $album->pictureCount = $row['total'];
    }

    return $albums;
}

function GetAccessibility($myPDO) {
    $sql = "select * from Accessibility";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute();

    $accessibilities = array();

    foreach ($pStmt as $row) {
        $accessibility = new Accessibility($row['Accessibility_Code'], $row['Description']);
        $accessibilities[] = $accessibility;
    }

    return $accessibilities;
}

function ChangeAccessibility($accessibilityChangeList, $myPDO) {
    foreach ($accessibilityChangeList as $album) {
        $accessibilityCode = explode(" ", $album)[0];
        $albumID = explode(" ", $album)[1];

        $sql = "update Album set Accessibility_Code = :accessibility_code where Album_Id = :album_id";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['album_id' => $albumID, 'accessibility_code' => $accessibilityCode]);
    }
}

function DeleteAlbum($album, $myPDO) {


    // delete the comments for each picture
    $sql = "select * from Picture where Album_Id = :album_id";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['album_id' => $album]);

    foreach ($pStmt as $row) {
        $pictureID = $row['Picture_Id'];
        $sql = "delete from comment where Picture_Id = :picture_id";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['picture_id' => $pictureID]);
    }


    //delete the pictures from the album
    $sql = "delete from Picture where Album_Id = :album_id";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['album_id' => $album]);

    // delete the album
    $sql = "delete from Album where Album_Id = :album_id";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['album_id' => $album]);
}

function ValidateAlbumTitle($title) {
    if ($title == "") {
        return "Title cannot be blank!";
    } else {
        return "";
    }
}

function AddAlbum($title, $accessibility, $description, $SID, $myPDO) {
    $sql = "INSERT INTO `album` (`Album_Id`, `Title`, `Description`, `Date_Updated`, `Owner_Id`, `Accessibility_Code`) VALUES (NULL, :title, :description, :date, :owner_id, :accessibility_Code)";
    $pStmt = $myPDO->prepare($sql);
    $pStmt->execute(['title' => $title, 'description' => $description, 'date' => date('Y-m-d'), 'owner_id' => $SID, 'accessibility_Code' => $accessibility]);
}


function resamplePicture($filePath, $destinationPath, $maxWidth, $maxHeight)
{
  
    $imageDetails = getimagesize($filePath);

    $originalResource = null;
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
            $originalResource = imagecreatefromjpeg($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
            $originalResource = imagecreatefrompng($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
            $originalResource = imagecreatefromgif($filePath);
    }
    $widthRatio = $imageDetails[0] / $maxWidth;
    $heightRatio = $imageDetails[1] / $maxHeight;
    $ratio = max($widthRatio, $heightRatio);

    $newWidth = $imageDetails[0] / $ratio;
    $newHeight = $imageDetails[1] / $ratio;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    $success = imagecopyresampled($newImage, $originalResource, 0, 0, 0, 0, 
                                    $newWidth, $newHeight, $imageDetails[0], $imageDetails[1]);

    
    $pathInfo = pathinfo($filePath);
    $newFilePath = $destinationPath."/".$pathInfo['filename'];
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
            $newFilePath .= ".jpg";
            $success = imagejpeg($newImage, $newFilePath, 100);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
            $newFilePath .= ".png";
            $success = imagepng($newImage, $newFilePath, 0);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
            $newFilePath .= ".gif";
            $success = imagegif($newImage, $newFilePath);
    }

    imagedestroy($newImage);
    imagedestroy($originalResource);

    if (!$success)
    {
            return "";
    }
    else
    {
            return $newFilePath;
    }
}

?>
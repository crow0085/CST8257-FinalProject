<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title></title>
    </head>
    <?php
    session_start();
    if (!isset($_SESSION["logged_in"]) && $_SESSION["logged in"] == false) {
        header("Location: Login.php");
    }

    $SID = $_SESSION["StudentID"];
    $name = GetStudentName($SID, GetPdo());
    $semesterCode = "";
    $check_list = array();
    $submitMessage = "";

    class Semester {

        public $code;
        public $term;
        public $year;

        public function __construct(string $code, string $term, string $year) {
            $this->code = $code;
            $this->term = $term;
            $this->year = $year;
        }

    }

    class Course {

        public $code;
        public $title;
        public $hours;

        public function __construct(string $code, string $title, string $hours) {
            $this->code = $code;
            $this->title = $title;
            $this->hours = $hours;
        }

    }

    function GetPdo() {
        $dbConnection = parse_ini_file("Lab5.ini");
        extract($dbConnection);
        $myPDO = new PDO($dsn, $user, $password);
        return $myPDO;
    }

    function getCourseBySemeter($semester, $SID, $myPDO) {
        $sql = "SELECT c.CourseCode as Code, Title, WeeklyHours FROM Course as c INNER JOIN CourseOffer as co ON c.CourseCode = co.CourseCode WHERE co.SemesterCode = :semesterCode AND NOT EXISTS (SELECT * FROM registration WHERE co.CourseCode = registration.CourseCode and registration.StudentId = :SID)";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['semesterCode' => $semester, 'SID'=>$SID]);

        $courses = array();

        foreach ($pStmt as $row) {
            $course = new Course($row['Code'], $row['Title'], $row['WeeklyHours']);
            $courses[] = $course;
        }

        return $courses;
    }

    function GetStudentName($SID, $myPDO) {

        $sql = "select StudentId, Name from Student where StudentId = :student_id";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['student_id' => $SID]);
        $row = $pStmt->fetch(PDO::FETCH_ASSOC);
        return $row['Name'];
    }

    function GetSemesters($myPDO) {
        $sql = "SELECT SemesterCode, Term, Year from Semester";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute();

        $semesters = array();

        foreach ($pStmt as $row) {
            $semester = new Semester($row['SemesterCode'], $row['Term'], $row['Year']);
            $semesters[] = $semester;
        }

        return $semesters;
    }

    function ValidateCourseSelection($SID, $semesterCode, $courseCodes, $availableHours, $myPDO) {
       
        if (count($courseCodes) == 0){
            return "you must select at least one course!";
        }
        
        $sql = "select c.WeeklyHours as Hours, c.CourseCode as Code FROM course as c";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute();

        $totalHours = 0;
        foreach ($pStmt as $row) {
            if (in_array($row['Code'], $courseCodes)) {
                $totalHours += intval($row['Hours']);
            }
        }
        
        // we know that the course selection was valid if the amount of hours doesnt exceed the available hours
        if ($totalHours <= $availableHours) {
            foreach ($courseCodes as $code) {
                $sql = "insert into Registration values (:student_id, :code, :sem_code)";
                $pStmt = $myPDO->prepare($sql);
                $pStmt->execute(['student_id' => $SID, 'sem_code' => $semesterCode, 'code' => $code]);
            }
            
            return "successfully registered the selected courses!";
        }else{
            return "unable to register the selected courses they exceed the maximum weekly hours!";
        }
    }

    function GetCurrentSemesterHours($SID, $semesterCode, $myPDO) {
        $sql = "SELECT c.WeeklyHours as Hours from course as c INNER JOIN registration as r ON c.CourseCode = r.CourseCode INNER JOIN student as s ON s.StudentId = r.StudentId INNER JOIN semester as sem ON sem.SemesterCode = r.SemesterCode WHERE s.StudentId = :student_id AND r.SemesterCode = :semesterCode";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['student_id' => $SID, 'semesterCode' => $semesterCode]);

        $totalHours = 0;

        foreach ($pStmt as $row) {
            $totalHours += intval($row['Hours']);
        }
        return $totalHours;
    }

    if (isset($_POST["semesterChange"])) {
        $semesterCode = $_POST["semester"];
    }

    if (isset($_POST["Submit"])) {
        $semesterCode = $_POST["semester"];
        foreach ($_POST['check_list'] as $item) {
            $check_list[] = $item;
        }
        $hours = GetCurrentSemesterHours($SID, $semesterCode, GetPdo());
        $availableHours = (16 - $hours);
        $submitMessage = ValidateCourseSelection($SID, $semesterCode, $check_list, $availableHours, GetPdo());
    }

    $hours = GetCurrentSemesterHours($SID, $semesterCode, GetPdo());
    $availableHours = (16 - $hours);

    include("./common/header.php");
    ?>
    <body>

        <div class="container">

            <p>Welcome <?php echo $name ?>! not you? Logout<a href="Logout.php"> here</a> and change user</p>
            <p>You have registered <?php echo $hours ?> hours for the selected semester</p>
            <p>You can register <?php echo $availableHours ?> more hours of course(s) for this semester</p>
            <p>Please note the courses you have registered in will not be displayed below</p>
            <form class="course_selection_form" action="CourseSelection.php" method="POST">
                <div class="row form-group">
                    <div class="col-md-2 col-md-offset-8">
                        <select name="semester" id="semester">
                            <?php
                            $semesters = GetSemesters(GetPdo());
                            echo "<option hidden>Select a semester</option>";
                            foreach ($semesters as $semester) {
                                if ($semesterCode == $semester->code) {
                                    echo "<option value='$semester->code' selected> $semester->year $semester->term </option>";
                                } else {
                                    echo "<option  value='$semester->code'> $semester->year $semester->term </option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                if ($submitMessage != "" && $submitMessage == "successfully registered the selected courses!"){
                    echo "<p class='text-success'> $submitMessage </P>";
                }else if ($submitMessage != "" && $submitMessage != "successfully registered the selected courses!"){
                    echo "<p class='text-danger'> $submitMessage </P>";
                }
                ?>
                <table class="table">
                    <thead>
                    <th>Code</th>
                    <th>Course Title</th>
                    <th>Hours</th>
                    <th>Select</th>
                    </thead>
                    <tbody>
                        <?php
                        $Courses = getCourseBySemeter($semesterCode, $SID, GetPdo());
                        foreach ($Courses as $course) {

                            echo "<tr>";
                            echo "<td>";
                            echo $course->code;
                            echo "</td>";
                            echo "<td>";
                            echo $course->title;
                            echo "</td>";
                            echo "<td>";
                            echo $course->hours;
                            echo "</td>";
                            echo "<td>";
                            if (in_array($course->code, $check_list)) {
                                echo "<input checked type='checkbox' name='check_list[]' value='$course->code'>";
                            } else {
                                echo "<input type='checkbox' name='check_list[]' value='$course->code'>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="row form-group">
                    <div class="col-md-2 col-md-offset-8">
                        <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
                        <input class="btn btn-primary" type="submit" name="Clear" value="Clear" />
                        <input hidden id="semChange" type="submit" name="semesterChange" value="semesterChange" />
                    </div>
                </div>
            </form>

        </div>
        <script src="js/SemesterChange.js" type="text/javascript"></script>
    </body>
    <?php include('./common/footer.php'); ?>

</html>

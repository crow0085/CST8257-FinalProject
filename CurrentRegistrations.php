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
    $check_list = array();
    $name = GetStudentName($SID, GetPdo());

    function GetPdo() {
        $dbConnection = parse_ini_file("Lab5.ini");
        extract($dbConnection);
        $myPDO = new PDO($dsn, $user, $password);
        return $myPDO;
    }

    function GetStudentName($SID, $myPDO) {

        $sql = "select StudentId, Name from Student where StudentId = :student_id";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['student_id' => $SID]);
        $row = $pStmt->fetch(PDO::FETCH_ASSOC);
        return $row['Name'];
    }

    class Registration {

        public $code;
        public $semCode;
        public $title;
        public $hours;
        public $term;
        public $year;

        public function __construct(string $code, string $title, string $hours, string $term, string $year, string $semCode) {
            $this->code = $code;
            $this->title = $title;
            $this->hours = $hours;
            $this->term = $term;
            $this->year = $year;
            $this->semCode = $semCode;
        }

    }

    function GetRegistrations($SID, $myPDO) {
        $sql = "SELECT r.CourseCode as CourseCode, c.Title as Title, c.WeeklyHours as Hours, s.Year as Year, s.Term, s.SemesterCode as SemCode from registration as r INNER JOIN course as c on c.CourseCode = r.CourseCode INNER join semester as s on s.SemesterCode = r.SemesterCode where StudentId = :student_id ORDER BY r.SemesterCode";
        $pStmt = $myPDO->prepare($sql);
        $pStmt->execute(['student_id' => $SID]);

        $registrations = array();

        foreach ($pStmt as $row) {
            $registration = new Registration($row['CourseCode'], $row['Title'], $row['Hours'], $row['Term'], $row['Year'], $row['SemCode']);
            $registrations[] = $registration;
        }

        return $registrations;
    }

    function GetCourseHoursBySemester($registrations) {
        $courseHoursBySemester = array();

        foreach ($registrations as $registration) {
            $courseHoursBySemester[$registration->semCode] += $registration->hours;
        }
        return $courseHoursBySemester;
    }

    function RemoveRegistrations($SID, $check_list, $myPDO) {
        foreach ($check_list as $item) {
            $code = explode(" ", $item)[0];
            $semCode = explode(" ", $item)[1];

            $sql = "DELETE from registration where registration.StudentId = :student_id and registration.CourseCode = :code and registration.SemesterCode = :semCode";
            $pStmt = $myPDO->prepare($sql);
            $pStmt->execute(['student_id' => $SID, 'code' => $code, 'semCode' => $semCode]);
        }
    }

    if (isset($_POST['Submit'])) {
        foreach ($_POST['check_list'] as $item) {
            $check_list[] = $item;
        }

        RemoveRegistrations($SID, $check_list, GetPdo());
    }

    $registrations = GetRegistrations($SID, GetPdo());
    $courseHoursBySemester = GetCourseHoursBySemester($registrations);

    include("./common/header.php");
    ?>
    <body>
        <div class="container">
            <h3>Current registrations</h3>
            <p>Welcome <?php echo $name ?>! not you? Logout<a href="Logout.php"> here</a> and change user</p>
            <form action="CurrentRegistrations.php" method="POST">
                <table class="table">
                    <thead>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Hours</th>
                    <th>Select</th>
                    </thead>
                    <tbody>
                        <?php
                        $prevRegistration;
                        foreach ($registrations as $registration) {
                            if ($prevRegistration != null) {
                                if ($prevRegistration->semCode != $registration->semCode) {
                                    foreach ($courseHoursBySemester as $key => $value) {
                                        if ($key == $prevRegistration->semCode) {
                                            echo"<tr><td colspan='3'></td><th class='text-right'> Total Weekly Hours: $value </th><td></td><td></td></tr>";
                                            break;
                                        }
                                    }
                                }
                            }
                            echo "<tr>";
                            echo "<td> $registration->year </td>";
                            echo "<td> $registration->term </td>";
                            echo "<td> $registration->code </td>";
                            echo "<td> $registration->title </td>";
                            echo "<td> $registration->hours </td>";
                            echo "<td> <input type='checkbox' name='check_list[]' value='$registration->code $registration->semCode'> </td>";
                            echo "</tr>";
                            $prevRegistration = $registration;
                        }
                        if ($prevRegistration != null) {
                            foreach ($courseHoursBySemester as $key => $value) {
                                if ($key == $prevRegistration->semCode) {
                                    echo"<tr><td colspan='3'></td><th class='text-right'> Total Weekly Hours: $value </th><td></td><td></td></tr>";
                                    break;
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="row form-group">
                    <div class="col-md-2 col-md-offset-8">
                        <input class="btn btn-primary" id="submit" type="submit" name="Submit" value="Submit" />
                        <input class="btn btn-primary" type="submit" name="Clear" value="Clear" />
                    </div>
                </div>
            </form>
        </div>
    </body>
    <script>
        $("#submit").on('click', function () {
            return confirm('Please confirm you wish to delete the selected registrations');
        });
    </script>
    <?php include('./common/footer.php'); ?>
</html>

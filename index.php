<?php
require_once 'config.php';
require_once 'mysql.php';
require_once 'render.php';
require_once "selects.php";

$evfolyamok = ["2024-2025"];
$selectedEvfolyam = $evfolyamok[0];
$selectedClass = getClasses()[0]["name"];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["classSelector"])) {
    $selectedClass = $_POST['classSelector'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["export"])) {
    if(dbExists("school")) {
        $message = "Adatbázis már létezik.";
            echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        loadToDataBase();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    execSql("DROP DATABASE school");
    $message = "Adatbázis törölve.";
    echo "<script type='text/javascript'>alert('$message');</script>";
}

function generateClassList() {
    $classList = [];
    foreach (CLASSES as $class) {
        $studentCount = rand(MIN_CLASS_COUNT, MAX_CLASS_COUNT);
        for ($i = 0; $i < $studentCount; $i++) {
            $classList[$class][] = generateStudent($class);
        }
    }
    return $classList;
}
function generateStudent($class) {
    $lastname = NAMES['lastnames'][array_rand(NAMES['lastnames'])];
    $isMale = (bool) rand(0, 1);
    $firstname = $isMale ? NAMES['firstnames']['men'][array_rand(NAMES['firstnames']['men'])] : NAMES['firstnames']['women'][array_rand(NAMES['firstnames']['women'])];
    
    $grades = [];
    $average = 0;
    $needToDivideBy = 0;
    foreach (SUBJECTS as $subject) {
        $gradeCount = rand(3, MARKS_COUNT);
        $grades[$subject] = array_map(fn() => rand(1, 5), range(1, $gradeCount));
        foreach($grades[$subject] as $grade) {
            $average += $grade;
            $needToDivideBy += 1;
        }
    }
    $average /= $needToDivideBy;
    
    $gender = "Fiú";
    if($isMale == 0) {
        $gender = "Lány";
    }
    $student = array();
    $student['name'] = $lastname . ' ' . $firstname;
    $student['class'] = $class;
    $student['grades'] = $grades;
    $student['gender'] = $gender;
    $student["average"] = floatval(number_format((float)$average, 2, '.', ''));


    return $student;
}

function loadToDataBase(){
    
    createDatabase();
    execSql("CREATE TABLE osztaly (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(10) NOT NULL
    ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci");
    execSql("CREATE TABLE nev (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        gender VARCHAR(10) NOT NULL,
        class_id INT NOT NULL,
        FOREIGN KEY (class_id) REFERENCES osztaly(id)
    ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci");
    execSql("CREATE TABLE tantargyak (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) UNIQUE NOT NULL
    ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci");
    execSql("CREATE TABLE osztalyzat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        subject_id INT NOT NULL,
        grade TINYINT NOT NULL,
        date VARCHAR(255),
        FOREIGN KEY (student_id) REFERENCES nev(id),
        FOREIGN KEY (subject_id) REFERENCES tantargyak(id)
    ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci");

    $classIds = [];
    $subjectIds = [];
    $classList = generateClassList();
    foreach ($classList as $class => $students) {
        $classIds[$class] = execSql("INSERT INTO osztaly (name) VALUES ('$class')");
        
        $gradeData = [];

        foreach ($students as $student) {

            $studentId =execSql("INSERT INTO nev (name, gender, class_id) VALUES ('".$student["name"]."', '".$student["gender"]."', '".$classIds[$class]."')");

            foreach ($student["grades"] as $subject => $grades) {

                if (!isset($subjectIds[$subject])) {
                    $subjectIds[$subject] = execSql("INSERT IGNORE INTO tantargyak (name) VALUES ('$subject')");
                }


                if (is_array($grades)) {

                    foreach ($grades as $grade) {
                        $month = rand(1,12);
                        $day = rand(1,30);
                        $hour = rand(0,24);
                        $min = rand(0,60);
                        $sec = rand(0,60);
                        $date = "2024-".$month."-".$day." ".$hour.":".$min.":".$sec;
                        $gradeData[] = "('$studentId', '".$subjectIds[$subject]."', '$grade', '$date')";
                    }
                } else {

                    $gradeData[] = "('$studentId', '".$subjectIds[$subject]."', '$grades')";
                }
            }
        }


        if (!empty($gradeData)) {
            execSql("INSERT INTO osztalyzat (student_id, subject_id, grade, date) VALUES " . implode(", ", $gradeData));
        }
        
    }
}


showDatabaseThing();
showYearSelector($evfolyamok);
showTopTen();
showClassSelector($selectedEvfolyam,$selectedClass,getClasses());
showClassAvg(getClassAvg($selectedClass)["avg"]);
showStudents($selectedClass,getStudents($selectedClass));


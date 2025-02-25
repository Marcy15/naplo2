<?php
require_once "mysql.php";

function getClasses() {
    $sql = "SELECT name FROM `osztaly`";
    return execSql($sql);
}

function getStudents($class) {
    $sql = "SELECT nev.name FROM nev JOIN osztaly ON nev.class_id = osztaly.id WHERE osztaly.name = '$class' ORDER BY nev.name ASC;";
    return execSql($sql);
}

function getClassAvg($class) {
    $sql = "SELECT CAST(AVG(grade) AS DECIMAL(10,2)) as avg FROM osztalyzat JOIN nev ON nev.id = osztalyzat.student_id JOIN osztaly ON nev.class_id = osztaly.id WHERE osztaly.name = '$class';";
    return execSql($sql);
}

function getStudentAvg($student){
    $sql = "SELECT CAST(AVG(grade) AS DECIMAL(10,2)) as avg FROM osztalyzat JOIN nev ON nev.id = osztalyzat.student_id WHERE nev.name = '$student';";
    return execSql($sql);
}

function getStudentSubjectAvgs($student) {
    $sql = "SELECT tantargyak.name, CAST(AVG(osztalyzat.grade) AS DECIMAL(10,2)) AS avg FROM osztalyzat JOIN tantargyak ON tantargyak.id = osztalyzat.subject_id JOIN nev ON nev.id = osztalyzat.student_id WHERE nev.name = '$student' GROUP BY tantargyak.name ORDER BY tantargyak.name;";
    return execSql($sql);
}

function getTopTen(){
    $sql = "SELECT nev.name, AVG(osztalyzat.grade) AS avg FROM osztalyzat JOIN nev ON nev.id = osztalyzat.student_id GROUP BY osztalyzat.student_id ORDER BY avg DESC LIMIT 10;";
    return execSql($sql);
}
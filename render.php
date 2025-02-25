<?php


require_once "selects.php";

function showDatabaseThing(){
    echo '<form method="post">';
    echo '<button type="submit" name="export">Exportálás</button>';
    echo '<button type="submit" name="delete">Adatbázis törlése</button>';
    echo '</form>';
}

function showYearSelector($evfolyamok): void {
    
    echo '<form method="post" action="" id="evfolyamForm">
    Évfolyam:
    <select name="gyumolcs" onchange="document.getElementById(\'evfolyamForm\').submit()">';

    foreach ($evfolyamok as $evfolyam) {
        echo "<option value='$evfolyam'>$evfolyam</option>";
    }

    echo '</select>
    </form>';
}

function showClassSelector($evfolyam,$selectedClass,$classes) {
    echo '<form method="post" action="" id="classForm">
    Osztály: 
    <select name="classSelector" onchange="document.getElementById(\'classForm\').submit()">';

    foreach ($classes as $class) {
        $selected = ($class["name"] === $selectedClass) ? 'selected' : '';
        echo "<option $selected value='".$class["name"]."'>".$class["name"]."</option>";
    }

    echo '</select>
    </form>';

    
}

function showStudents($class,$students){
    foreach($students as $student) {
        echo $student["name"]." - ".getStudentAvg($student["name"])["avg"]."<br>";
        foreach(getStudentSubjectAvgs($student["name"]) as $row) {
            echo "- ".$row["name"].": ".$row["avg"]."<br>";
        }
        echo "<br>";
    } 
}

function showClassAvg($avg) {
    echo "Osztály átlaga: $avg<br>";
}

function showTopTen(){
    $top = getTopTen();
    echo "Iskola Top 10:<br>";
    for($i = 0;$i<10;$i++){
        echo "# ".($i+1)." ".$top[$i]["name"].": ".$top[$i]["avg"]."<br>";
    }
}
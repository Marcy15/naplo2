<?php


require_once "selects.php";

function showDatabaseThing(){
    echo '<form method="post">';
    echo '<button type="submit" name="export">Exportálás</button>';
    echo '<button type="submit" name="delete">Adatbázis törlése</button>';
    echo '</form>';
}

function showYearSelector($evfolyamok,$selectedEvfolyam): void {
    
    echo '<form method="post" action="" id="evfolyamForm">
    Évfolyam:
    <select name="yearSelector" onchange="document.getElementById(\'evfolyamForm\').submit()">';

    foreach ($evfolyamok as $evfolyam) {
        $selected = ($evfolyam === $selectedEvfolyam) ? 'selected' : '';

        echo "<option $selected value='$evfolyam'>$evfolyam</option>";
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

    echo '</select><br>';
    echo "<input type='text' name='newClassName' placeholder='9A'/>";
    echo '<button id="newclass" name="newclass">Új osztály</button>';
    echo "</form>";



    
}

function showStudents($class,$students){
    foreach($students as $student) {
        echo $student["name"]." - ".getStudentAvg($student["name"])["avg"]."<br>";
        foreach(getStudentSubjectAvgs($student["name"]) as $row) {
            echo "- ".$row["name"].": ".$row["avg"];
            
            echo "<br>";
        }
        echo "<br>";
    } 
}

function showClassAvg($avg) {
    echo "Osztály átlaga: $avg<br>";
}
function showAllTopTen(){
    $top = getAllTopTen();
    echo "Hall of fame Top 10:<br>";
    for($i = 0;$i<10;$i++){
        echo "# ".($i+1)." ".$top[$i]["name"].": ".$top[$i]["avg"]."<br>";
    }
}
function showTopTen($year){
    $top = getTopTen($year);
    echo "Évfolyam Top 10:<br>";
    for($i = 0;$i<10;$i++){
        echo "# ".($i+1)." ".$top[$i]["name"].": ".$top[$i]["avg"]."<br>";
    }
}
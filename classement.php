<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement</title>
    <link rel="stylesheet" href="css/classement.css">

</head>
<body>

<h2>Classement</h2>

<div class="button-group">
    <form action="classement.php" method="post">
        <button type="submit" name="classement" value="python">Classement Python</button>
        <button type="submit" name="classement" value="prog">Classement Programmation</button>
        <button type="submit" name="classement" value="rsociaux">Classement Réseaux Sociaux</button>
        <button type="submit" name="classement" value="general">Classement Général</button>
    </form>
</div>
<div class="table-container">
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ctf";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["classement"])) {
        $classement = $_POST["classement"];
        if ($classement == "rsociaux") {
            $sql = "SELECT id, nom, flag1, time_flag_1 FROM rsociaux ORDER BY flag1 DESC, time_flag_1 ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table id='table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='rank'></th>";
                echo "<th class='name'>Nom</th>";
                echo "<th class='flag1'>Flag 1</th>";
                if ($classement != "rsociaux") {
                    echo "<th class='flag2'>Flag 2</th>";
                    echo "<th class='flag3'>Flag 3</th>";
                    echo "<th class='time_flag_2'>Time 2</th>";
                    echo "<th class='time_flag_3'>Time 3</th>";
                }
                echo "<th class='time_flag_1'>Time 1</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                $rank = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='rank'>$rank</td>";
                    echo "<td class='name'>" . $row["nom"] . "</td>";
                    echo "<td class='flag1'>" . $row["flag1"] . "/1</td>";
                    echo "<td class='time_flag_1'>" . $row["time_flag_1"] . "</td>";
                    echo "</tr>";
                    $rank++;
                }
                
                echo "</tbody>";
                echo "</table>";
    
            }
        } 
        else if ($classement == "general") {
            $ctfId = "";
            
            $cookieValues = array();
            $tables = array("python", "prog", "rsociaux"); // Ajout de la table rsociaux
            foreach ($tables as $table) {
                if ($table === "rsociaux") {
                    $sql = "SELECT nom, flag1, cookie FROM $table"; // Sélectionner uniquement la colonne flag1 pour rsociaux
                } else {
                    $sql = "SELECT nom, flag1, flag2, flag3, cookie FROM $table";
                }
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $cookie = $row['cookie'];
                        $value = ($table == "rsociaux") ? "1/1" : $row['flag1'] + $row['flag2'] + $row['flag3'];
                        $nom = $row['nom'];
                        if (isset($cookieValues[$cookie][$table])) {
                            $cookieValues[$cookie][$table] += $value;
                        } else {
                            $cookieValues[$cookie][$table] = $value;
                            $cookieValues[$cookie]['nom'] = $nom;
                        }
                    }
                }
            }
            
            function compareValues($a, $b) {
                $sumA = ($a['python'] ?? 0) + ($a['prog'] ?? 0);
                $sumB = ($b['python'] ?? 0) + ($b['prog'] ?? 0);
                return $sumB <=> $sumA;
            }
            
            usort($cookieValues, 'compareValues');
            
            echo "<table id='table'>";
            echo "<thead>";
            echo "<tr><th class='flag3'></th><th class='name'>Nom</th><th class='flag1'>Python</th><th class='flag2'>Programmation</th><th class='flag3'>Réseaux Sociaux</th><th >Total</th></tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach ($cookieValues as $cookie => $values) {
                $total_python = isset($values['python']) ? $values['python'] : 0;
                $total_prog = isset($values['prog']) ? $values['prog'] : 0;
                $total_rsociaux = isset($values['rsociaux']) ? $values['rsociaux'] : "0/1";
                list($rsociaux_num, $rsociaux_denom) = explode("/", $total_rsociaux);
                $total = $total_python + $total_prog + ($rsociaux_num / $rsociaux_denom);
                $cookie++;
                // Ajoutez la classe 'highlight' si le cookie correspond
                $highlightClass = ($cookie == $ctfId) ? "highlight" : "";
                echo "<tr>";
                echo "<td class='rank $highlightClass'>$cookie</td>";
                echo "<td class='name $highlightClass'>{$values['nom']}</td>";
                echo "<td class='flag1 $highlightClass'>$total_python/3</td>";
                echo "<td class='flag2 $highlightClass'>$total_prog/3</td>";
                echo "<td class='flag3 $highlightClass'>$total_rsociaux</td>";
                echo "<td $highlightClass>$total</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
                        
        } else {
            $sql = "SELECT id, nom, flag1, flag2, flag3, time_flag_1, time_flag_2, time_flag_3 FROM $classement ORDER BY flag1 DESC, flag2 DESC, flag3 DESC, time_flag_1 ASC, time_flag_2 ASC, time_flag_3 ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table id='table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='rank'></th>";
                echo "<th class='name'>Nom</th>";
                echo "<th class='flag1'>Flag 1</th>";
                echo "<th class='flag2'>Flag 2</th>";
                echo "<th class='flag3'>Flag 3</th>";
                echo "<th class='time_flag_1'>Time 1</th>";
                echo "<th class='time_flag_2'>Time 2</th>";
                echo "<th class='time_flag_3'>Time 3</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                $rank = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='rank'>$rank</td>";
                    echo "<td class='name'>" . $row["nom"] . "</td>";
                    echo "<td class='flag1'>" . $row["flag1"] . "/1</td>";
                    echo "<td class='flag2'>" . $row["flag2"] . "/1</td>";
                    echo "<td class='flag3'>" . $row["flag3"] . "/1</td>";
                    echo "<td class='time_flag_1'>" . $row["time_flag_1"] . "</td>";
                    echo "<td class='time_flag_2'>" . $row["time_flag_2"] . "</td>";
                    echo "<td class='time_flag_3'>" . $row["time_flag_3"] . "</td>";
                    echo "</tr>";
                    $rank++;
                }
                
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "Aucun joueur trouvé.";
            }
        }
    } else {
        echo "Classement non spécifié.";
    }
}

$conn->close();
?>
</div>
<script>
    var ctfId = "<?php session_start(); echo $_SESSION['ctfNOM']; ?>";
    console.log(ctfId);
    if (ctfId) {
        ctfId = ctfId.split('=')[1];
        console.log(ctfId);
        var rowsToHighlight = document.querySelectorAll(".name");
        rowsToHighlight.forEach(row => {
            if (row.textContent.trim() === ctfId.trim()) { // Utilisez trim() pour supprimer les espaces en trop
                row.parentElement.classList.add('highlight');
            }
        });
    }
</script>


<script>
    const table = document.getElementById('table');

    document.body.addEventListener('mousemove', (e) => {
        const boundingRect = table.getBoundingClientRect();
        const offsetX = e.clientX - boundingRect.left;
        const offsetY = e.clientY - boundingRect.top;
        const percentX = offsetX / boundingRect.width;
        const percentY = offsetY / boundingRect.height;

        const rotX = 5 - percentY * 9;
        const rotY = percentX * 9 - 5;

        table.style.transform = `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg)`;
    });

</script>



</body>
</html>

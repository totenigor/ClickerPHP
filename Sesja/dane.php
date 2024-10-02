<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "clickerDb1";

// Sprawdzanie i inicjalizacja zmiennej sesji ranking
if (!isset($_SESSION['ranking'])) {
    $_SESSION['ranking'] = [];
}

// Utrzymywanie tylko 5 ostatnich wyników w rankingu
$_SESSION['ranking'] = array_slice($_SESSION['ranking'], 0, 5);

// Połączenie z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

// Sprawdzenie czy zmienne sesyjne istnieją
if (isset($_SESSION['imie']) && isset($_SESSION['nazwisko'])) {
    // Zapytanie o sumę kliknięć dla użytkownika
    $sql = "SELECT SUM(clickCount) FROM users WHERE imie = ? AND nazwisko = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $_SESSION['imie'], $_SESSION['nazwisko']);
    $stmt->execute();
    $stmt->bind_result($clickSum);
    $stmt->fetch();
    $stmt->close();
} else {
    $clickSum = 0;
}

// Zapytanie o 5 najlepszych wyników z sumowaniem punktów dla każdego imienia
$sql1 = "SELECT imie, SUM(clickCount) as totalClickCount 
         FROM users 
         GROUP BY imie 
         ORDER BY totalClickCount DESC 
         LIMIT 5";
$result = $conn->query($sql1);

// Czyszczenie tablicy rankingowej i dodawanie nowych wyników
$_SESSION['ranking'] = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION['ranking'][] = [
            'imie' => $row['imie'],
            'clickCount' => $row['totalClickCount']
        ];
    }
}

$conn->close();
?>

<?php
if($_SESSION['imie']&& $_SESSION['nazwisko']){
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dane.css?v=<?php echo time(); ?>" />
    <title>Dane</title>
</head>
<body>
    <div class="gradient-background">
        <?php 
        echo "Witaj " . htmlspecialchars($_SESSION['imie']) . ", twój ostatni czas wizyty to: " . htmlspecialchars($_SESSION['visitTime']) . "<br>"; 
        ?>
        
        <?php 
        echo "Oto twoje kliknięcia: " . ($clickSum !== null ? $clickSum : 0) . "<br>"; 
        ?>
        
        <div class="ranking-results">
        <?php
        echo "5 najlepszych wyników:<br>";
        foreach ($_SESSION['ranking'] as $ranga) {
            if ($_SESSION['imie'] == $ranga['imie']) {
                echo '<span class="highlight">' . htmlspecialchars($ranga['imie']) . ": " . htmlspecialchars($ranga['clickCount']) . "</span><br>";
            } else {
                echo htmlspecialchars($ranga['imie']) . ": " . htmlspecialchars($ranga['clickCount']) . "<br>";
            }
        }
        ?>
        <a href="main.php">Kliknij aby wrocic do klikania!</a>
        </div>
    </div>
</body>
</html>
<?php
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dane.css?v=<?php echo time(); ?>" />
</head>
<body>
    <div class="gradient-background">
        <h1>Nie podałeś nam jeszcze swoich danych!</h1>
        <a href="main.php">Wroc aby podac nam swoje dane!</a>
    </div>
</body>
</html>
<?php
}
?>
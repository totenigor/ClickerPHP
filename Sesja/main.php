<?php
session_start();

$value = date('l jS \of F Y h:i:s A');

setcookie('VisitTime', $value, time() + 3600);

if (isset($_COOKIE['VisitTime'])) {
    $visitTime = $_COOKIE['VisitTime'];
}

if (isset($visitTime)) {
    $_SESSION['visitTime'] = $visitTime;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clickerDb1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Połączenie nieudane" . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if imie and nazwisko are set and store in session
    if (isset($_POST['imie']) && isset($_POST['nazwisko'])) {
        $_SESSION['imie'] = $_POST['imie'];
        $_SESSION['nazwisko'] = $_POST['nazwisko'];
    }

    // Insert click count into the database
    if (isset($_POST['clickCountValue']) && isset($_SESSION['imie']) && isset($_SESSION['nazwisko'])) {
        $name = $_SESSION['imie'];
        $surname = $_SESSION['nazwisko'];
        $clickCount = $_POST['clickCountValue'];

        $sql = "INSERT INTO users(imie, nazwisko, clickCount) VALUE(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $surname, $clickCount);

        if (!$stmt->execute()) {
            echo "Błąd podczas zapisywania danych: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<?php
// Check if session variables for imie and nazwisko are set
if (isset($_SESSION['imie']) && isset($_SESSION['nazwisko'])) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clicker</title>
        <link rel="stylesheet" href="main.css?v=<?php echo time(); ?>" />
        <script src="main.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <div class="gradient-background">
            <button class="clickButton"><span class="clickCount"></span></button>
            <form action="#" method="POST">
                <input type="hidden" name="clickCountValue" id="clickcountvalue">
                <input type="submit" value="Wyslij dane" class="submit"></input>
            </form>
            <a href="dane.php">Zobacz swoje statystyki!</a>
        </div>
    </body>
    </html>
<?php
} else {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clicker</title>
        <link rel="stylesheet" href="main.css?v=<?php echo time(); ?>" />
        <script src="main.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <div class="gradient-background">
            <button class="clickButton"><span class="clickCount"></span></button>
            <form action="#" method="POST">
                <h3>Powiedz nam, jak sie nazywasz?</h3>
                <input type="text" name="imie" placeholder="imie" required>
                <input type="text" name="nazwisko" placeholder="nazwisko" required>
                <input type="hidden" name="clickCountValue" id="clickcountvalue">
                <input type="submit" value="Wyslij dane" class="submit"></input>
            </form>
            <a href="dane.php">Zobacz swoje statystyki!</a>
        </div>
    </body>
    </html>
<?php
}
?>

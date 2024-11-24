                <!--PHP ŁĄCZENIE Z BAZĄ/DODAWANIE DANYCH/USUWANIE DANYCH-->
<?php
$conn = new mysqli('localhost', 'root', '', 'zarzadzanie_projektami');

if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dodaj_projekt'])) {
    $nazwa = $_POST['nazwa'];
    $opis = $_POST['opis'];
    $data_rozpoczecia = $_POST['data_rozpoczecia'];
    $data_zakonczenia = $_POST['data_zakonczenia'];
    $priorytet = $_POST['priorytet'];

    $stmt = $conn->prepare("INSERT INTO projekty (nazwa, opis, data_rozpoczecia, data_zakonczenia, priorytet, status) VALUES (?, ?, ?, ?, ?, 'nieukończony')");
    $stmt->bind_param("sssss", $nazwa, $opis, $data_rozpoczecia, $data_zakonczenia, $priorytet);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

if (isset($_GET['usun']) && is_numeric($_GET['usun'])) {
    $id_projektu = $_GET['usun'];
    $stmt = $conn->prepare("DELETE FROM projekty WHERE id_projektu = ?");
    $stmt->bind_param("i", $id_projektu);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

if (isset($_GET['ukoncz']) && is_numeric($_GET['ukoncz'])) {
    $id_projektu = $_GET['ukoncz'];
    $stmt = $conn->prepare("UPDATE projekty SET status = 'ukończony' WHERE id_projektu = ?");
    $stmt->bind_param("i", $id_projektu);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

if (isset($_GET['usun_ukonczony']) && is_numeric($_GET['usun_ukonczony'])) {
    $id_projektu = $_GET['usun_ukonczony'];
    $stmt = $conn->prepare("DELETE FROM projekty WHERE id_projektu = ?");
    $stmt->bind_param("i", $id_projektu);
    $stmt->execute();
    header("Location: index.php");
    exit();
}
?>
                                        <!-- HTML -->
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Zarządzania Projektami</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>System Zarządzania Projektami</h1>
    </header>

    <main>
        <form action="index.php" method="POST">
            <h2>Dodaj projekt</h2>
            <input type="text" name="nazwa" placeholder="Nazwa projektu" required>
            <textarea name="opis" placeholder="Opis projektu"></textarea>
            <label>Data rozpoczęcia:</label>
            <input type="date" name="data_rozpoczecia" required>
            <label>Data zakończenia:</label>
            <input type="date" name="data_zakonczenia" required>
            <label>Priorytet:</label>
            <select name="priorytet">
                <option value="niski">Niski</option>
                <option value="sredni">Średni</option>
                <option value="wysoki">Wysoki</option>
            </select>
            <button type="submit" name="dodaj_projekt">Dodaj projekt</button>
        </form>

        <h2>Projekty</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Opis</th>
                    <th>Data rozpoczęcia</th>
                    <th>Data zakończenia</th>
                    <th>Priorytet</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $wynik = $conn->query("SELECT * FROM projekty WHERE status = 'nieukończony'");
                while ($wiersz = $wynik->fetch_assoc()) {
                    $klasa = '';
                    switch ($wiersz['priorytet']) {
                        case 'niski':
                            $klasa = 'tr-niski';
                            break;
                        case 'sredni':
                            $klasa = 'tr-sredni';
                            break;
                        case 'wysoki':
                            $klasa = 'tr-wysoki';
                            break;
                    }
                    echo "<tr class='$klasa'>
                        <td>{$wiersz['id_projektu']}</td>
                        <td>{$wiersz['nazwa']}</td>
                        <td>{$wiersz['opis']}</td>
                        <td>{$wiersz['data_rozpoczecia']}</td>
                        <td>{$wiersz['data_zakonczenia']}</td>
                        <td>{$wiersz['priorytet']}</td>
                        <td>
                            <a href='index.php?ukoncz={$wiersz['id_projektu']}'><button class='complete'>Ukończono</button></a>
                            <a href='index.php?usun={$wiersz['id_projektu']}'><button class='delete'>Usuń</button></a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Ukończone Projekty</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Opis</th>
                    <th>Data rozpoczęcia</th>
                    <th>Data zakończenia</th>
                    <th>Priorytet</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $wynik = $conn->query("SELECT * FROM projekty WHERE status = 'ukończony'");
                while ($wiersz = $wynik->fetch_assoc()) {
                    $klasa = '';
                    switch ($wiersz['priorytet']) {
                        case 'niski':
                            $klasa = 'tr-niski';
                            break;
                        case 'sredni':
                            $klasa = 'tr-sredni';
                            break;
                        case 'wysoki':
                            $klasa = 'tr-wysoki';
                            break;
                    }
                    echo "<tr class='$klasa'>
                        <td>{$wiersz['id_projektu']}</td>
                        <td>{$wiersz['nazwa']}</td>
                        <td>{$wiersz['opis']}</td>
                        <td>{$wiersz['data_rozpoczecia']}</td>
                        <td>{$wiersz['data_zakonczenia']}</td>
                        <td>{$wiersz['priorytet']}</td>
                        <td>
                            <a href='index.php?usun_ukonczony={$wiersz['id_projektu']}'><button class='delete'>Usuń</button></a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php
// 1. NEUEN DATENSATZ HINZUFÜGEN
if (isset($_POST["submit"])) {
    $datei = fopen("Mappe1.csv", "a");
    fputcsv($datei, [
        $_POST["Id"],
        $_POST["Name"],
        $_POST["Vorname"],
        $_POST["Anrede"],
        $_POST["Email"]
    ], ";");
    fclose($datei);
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 2. ZEILE LÖSCHEN
if (isset($_POST["loeschen"])) {
    $loeschId = $_POST["loesch_id"];
    $alleZeilen = [];

    if (($datei = fopen("Mappe1.csv", "r")) !== false) {
        while ($zeile = fgetcsv($datei, 1000, ";")) {
            if ($zeile[0] !== $loeschId) {
                $alleZeilen[] = $zeile;
            }
        }
        fclose($datei);
    }

    $datei = fopen("Mappe1.csv", "w");
    foreach ($alleZeilen as $zeile) {
        fputcsv($datei, $zeile, ";");
    }
    fclose($datei);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. ÄNDERUNGEN SPEICHERN
if (isset($_POST["speichern"])) {
    $alleZeilen = [];

    if (isset($_POST['daten'])) {
        foreach ($_POST['daten'] as $id => $spalten) {
            $alleZeilen[] = [
                $id,
                $spalten['Name'],
                $spalten['Vorname'],
                $spalten['Anrede'],
                $spalten['Email']
            ];
        }

        $datei = fopen("Mappe1.csv", "w");
        foreach ($alleZeilen as $zeile) {
            fputcsv($datei, $zeile, ";");
        }
        fclose($datei);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 4. TABELLE ANZEIGEN
if (file_exists("Mappe1.csv") && ($datei = fopen("Mappe1.csv", "r")) !== false) {

    // Formulaire global pour SAUVEGARDER
    echo '<form method="POST">';
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    echo '<tr><th>ID</th><th>Name</th><th>Vorname</th><th>Anrede</th><th>Email</th><th>Aktion</th></tr>';

    while ($z = fgetcsv($datei, 1000, ";")) {
        if (empty($z[0])) continue; 
        
        $id = $z[0];

        echo "<tr>
                <td><b>$id</b></td>
                <td><input type='text' name='daten[$id][Name]' value='$z[1]'></td>
                <td><input type='text' name='daten[$id][Vorname]' value='$z[2]'></td>
                <td><input type='text' name='daten[$id][Anrede]' value='$z[3]'></td>
                <td><input type='email' name='daten[$id][Email]' value='$z[4]'></td>
                <td>
                    <!-- Bouton séparé pour SUPPRIMER via un second formulaire indépendant -->
                    <button type='submit' form='form_delete_$id'>Löschen</button>
                </td>
              </tr>";
    }

    echo '</table><br>';
    fclose($datei);

    echo '<button type="submit" name="speichern">Änderungen speichern</button>';
    echo '</form>';

    // Génération des formulaires cachés de suppression pour chaque ID
    if (($datei = fopen("Mappe1.csv", "r")) !== false) {
        while ($z = fgetcsv($datei, 1000, ";")) {
            if (empty($z[0])) continue;
            $id = $z[0];
            echo "<form id='form_delete_$id' method='POST' style='display:none;'>
                    <input type='hidden' name='loesch_id' value='$id'>
                    <input type='hidden' name='loeschen' value='1'>
                  </form>";
        }
        fclose($datei);
    }
    echo '<br>';
}
?>

<!-- FORMULAR ZUM HINZUFÜGEN -->
<h3>Neuen Datensatz hinzufügen</h3>
<form method="POST">
    <input type="text" name="Id" placeholder="Id" required>
    <input type="text" name="Name" placeholder="Name">
    <input type="text" name="Vorname" placeholder="Vorname">
    <input type="text" name="Anrede" placeholder="Anrede">
    <input type="email" name="Email" placeholder="Email">

    <button type="submit" name="submit">Hinzufügen</button>
</form>
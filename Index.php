<?php
// 1. NEUEN DATENSATZ HINZUFÜGEN (Ajouter une nouvelle ligne)
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

// 2. ZEILE LÖSCHEN (Supprimer une ligne)
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

// 3. ÄNDERUNGEN SPEICHERN (Sauvegarder les modifications du tableau)
if (isset($_POST["speichern"])) {
    $alleZeilen = [];

    // On parcourt toutes les lignes modifiées dans le tableau
    foreach ($_POST['daten'] as $id => $spalten) {
        $alleZeilen[] = [
            $id,
            $spalten['Name'],
            $spalten['Vorname'],
            $spalten['Anrede'],
            $spalten['Email']
        ];
    }

    // Réécriture du fichier CSV avec les valeurs modifiées
    $datei = fopen("Mappe1.csv", "w");
    foreach ($alleZeilen as $zeile) {
        fputcsv($datei, $zeile, ";");
    }
    fclose($datei);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 4. TABELLE ANZEIGEN (Afficher le tableau avec des champs modifiables)
if (file_exists("Mappe1.csv") && ($datei = fopen("Mappe1.csv", "r")) !== false) {

    // Un grand formulaire englobe tout le tableau pour pouvoir tout enregistrer d'un coup
    echo '<form method="POST">';
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    
    echo '<tr><th>ID</th><th>Name</th><th>Vorname</th><th>Anrede</th><th>Email</th><th>Aktion</th></tr>';

    while ($z = fgetcsv($datei, 1000, ";")) {
        if (empty($z[0])) continue; 
        
        $id = $z[0];

        // Remplacement du simple texte par des <input>
        echo "<tr>
                <td><b>$id</b></td>
                <td><input type='text' name='daten[$id][Name]' value='$z[1]'></td>
                <td><input type='text' name='daten[$id][Vorname]' value='$z[2]'></td>
                <td><input type='text' name='daten[$id][Anrede]' value='$z[3]'></td>
                <td><input type='email' name='daten[$id][Email]' value='$z[4]'></td>
                <td>
                    <button type='submit' name='loesch_btn' onclick=\"document.getElementById('loesch_id_input').value='$id';\">Löschen</button>
                </td>
              </tr>";
    }

    echo '</table><br>';
    fclose($datei);

    // Bouton de sauvegarde globale + champ caché pour la suppression
    echo '<input type="hidden" id="loesch_id_input" name="loesch_id" value="">';
    echo '<button type="submit" name="speichern">Änderungen speichern</button>';
    echo '<button type="submit" name="loeschen" id="btn_loeschen_hidden" style="display:none;"></button>';
    echo '</form><br>';
}
?>

<!-- FORMULAR ZUM HINZUFÜGEN -->
<form method="POST">
    <input type="text" name="Id" placeholder="Id" required>
    <input type="text" name="Name" placeholder="Name">
    <input type="text" name="Vorname" placeholder="Vorname">
    <input type="text" name="Anrede" placeholder="Anrede">
    <input type="email" name="Email" placeholder="Email">

    <button type="submit" name="submit">Hinzufügen</button>
</form>
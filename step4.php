<?php
/**
 * step4.php – Schritt 4: Kontakt
 */
session_start();
if (!isset($_SESSION['form_data']['diet_type'])) {
    header('Location: step3.php');
    exit;
}
$values = $_SESSION['form_data'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email === false || strlen($_POST['email']) > 100) {
        $errors['email'] = 'Bitte gültige E-Mail bis 100 Zeichen.';
    } else {
        $values['email'] = htmlspecialchars(trim($email), ENT_QUOTES);
    }

    if (!$errors) {
        $_SESSION['form_data'] = $values;
        header('Location: process.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="page-title">
    <h1>CO₂-Rechner</h1>
    <p>Schritt 4 von 4 – Kontakt</p>
</div>

<form method="post" autocomplete="off">
    <fieldset>
        <legend>Kontakt</legend>

        <label for="email">E-Mail-Adresse</label>
        <input type="email" id="email" name="email" maxlength="100" required
            value="<?= htmlspecialchars($values['email'] ?? '') ?>">
        <span class="error"><?= $errors['email'] ?? '' ?></span>
    </fieldset>

    <div class="form-navigation">
        <button type="button" onclick="location.href='step3.php'">Zurück</button>
        <button type="submit">CO₂ Fussabdruck berechnen</button>
    </div>
</form>

<?php include 'includes/footer.php'; ?>

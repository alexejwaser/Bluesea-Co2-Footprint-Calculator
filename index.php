<?php
/**
 * index.php – Schritt 1: Wohnen
 *
 * Erfasst Haushaltsdaten und speichert sie in der Session.
 */
session_start();
$values = $_SESSION['form_data'] ?? [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['household_size'] = filter_input(
        INPUT_POST,
        'household_size',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 10]]
    );
    if ($values['household_size'] === false) {
        $errors['household_size'] = 'Ungültiger Wert (1–10).';
    }

    $values['living_area'] = filter_input(INPUT_POST, 'living_area', FILTER_VALIDATE_FLOAT);
    if (
        $values['living_area'] === false ||
        $values['living_area'] < 0 ||
        $values['living_area'] > 1000
    ) {
        $errors['living_area'] = 'Bitte Fläche zwischen 0 und 1000 angeben.';
    }

    $heating_type = $_POST['heating_type'] ?? '';
    $valid_heating_types = ['gas', 'oil', 'district', 'heatpump'];
    if (!in_array($heating_type, $valid_heating_types, true)) {
        $errors['heating_type'] = 'Bitte Heizungsart wählen.';
    } else {
        $values['heating_type'] = $heating_type;
    }

    $values['energy_consumption'] = filter_input(INPUT_POST, 'energy_consumption', FILTER_VALIDATE_INT);
    if (
        $values['energy_consumption'] === false ||
        $values['energy_consumption'] < 0 ||
        $values['energy_consumption'] > 200000
    ) {
        $errors['energy_consumption'] = 'Wert zwischen 0 und 200000 erforderlich.';
    }

    if (!$errors) {
        $_SESSION['form_data'] = $values;
        header('Location: step2.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="page-title">
    <h1>CO₂-Rechner</h1>
    <p>Berechne deinen CO₂-Fussabdruck und erfahre, wie du deinen Fussabdruck reduzieren kannst.</p>
</div>

<form method="post" autocomplete="off">
    <div class="form-progress">
        <div class="progress-bar">
            <div class="progress-fill" style="width:25%"></div>
        </div>
        <p>Schritt 1 von 4 – Wohnen</p>
    </div>

    <fieldset>
        <legend>Wohnen</legend>

        <label for="household_size">Haushaltsgrösse (1–10 Personen)</label>
        <input type="range" id="household_size" name="household_size" min="1" max="10" required
            value="<?= htmlspecialchars($values['household_size'] ?? 1) ?>">
        <span class="range-value" id="household_size_value"><?= htmlspecialchars($values['household_size'] ?? 1) ?></span>
        <span class="error"><?= $errors['household_size'] ?? '' ?></span>

        <label for="living_area">Wohnfläche (m²)</label>
        <input type="number" id="living_area" name="living_area" step="0.1" min="0" max="1000" required
            value="<?= htmlspecialchars($values['living_area'] ?? '') ?>">
        <span class="error"><?= $errors['living_area'] ?? '' ?></span>

        <label>Heizungsart</label>
        <?php
        $heating_types = [
            'gas' => 'Gas',
            'oil' => 'Öl',
            'district' => 'Fernwärme',
            'heatpump' => 'Wärmepumpe'
        ];
        foreach ($heating_types as $value => $label): ?>
            <span class="radio-group">
                <input type="radio" id="heating_<?= $value ?>" name="heating_type" value="<?= $value ?>" required
                    <?= (isset($values['heating_type']) && $values['heating_type'] === $value) ? 'checked' : '' ?>>
                <label for="heating_<?= $value ?>"><?= $label ?></label>
            </span>
        <?php endforeach ?>
        <span class="error"><?= $errors['heating_type'] ?? '' ?></span>

        <label for="energy_consumption">Jährlicher Energieverbrauch (kWh/Jahr)</label>
        <input type="number" id="energy_consumption" name="energy_consumption" min="0" max="200000" required
            value="<?= htmlspecialchars($values['energy_consumption'] ?? '') ?>">
        <span class="error"><?= $errors['energy_consumption'] ?? '' ?></span>
    </fieldset>

    <div class="form-navigation">
        <button type="submit" class="next-btn"><span>Weiter</span></button>
    </div>
</form>

<script src="js/sliders.js"></script>

<?php include 'includes/footer.php'; ?>

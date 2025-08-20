<?php
/**
 * step3.php – Schritt 3: Lifestyle
 */
session_start();
if (!isset($_SESSION['form_data']['car_type'])) {
    header('Location: step2.php');
    exit;
}
$values = $_SESSION['form_data'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diet_type = $_POST['diet_type'] ?? '';
    $valid_diet_types = ['omnivore', 'vegetarian', 'vegan'];
    if (!in_array($diet_type, $valid_diet_types, true)) {
        $errors['diet_type'] = 'Bitte Ernährungsweise wählen.';
    } else {
        $values['diet_type'] = $diet_type;
    }

    $values['meat_servings'] = filter_input(
        INPUT_POST,
        'meat_servings',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0, 'max_range' => 21]]
    );
    if ($values['meat_servings'] === false) {
        $errors['meat_servings'] = '0–21 erlaubt.';
    }

    $values['weekly_waste'] = filter_input(
        INPUT_POST,
        'weekly_waste',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0, 'max_range' => 50]]
    );
    if ($values['weekly_waste'] === false) {
        $errors['weekly_waste'] = '0–50 erlaubt.';
    }

    $values['clothing_items'] = filter_input(
        INPUT_POST,
        'clothing_items',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0, 'max_range' => 100]]
    );
    if ($values['clothing_items'] === false) {
        $errors['clothing_items'] = '0–100 erlaubt.';
    }

    if (!$errors) {
        $_SESSION['form_data'] = $values;
        header('Location: step4.php');
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
            <div class="progress-fill" style="width:75%"></div>
        </div>
        <p>Schritt 3 von 4 – Lifestyle</p>
    </div>

    <fieldset>
        <legend>Lifestyle</legend>

        <label for="diet_type">Ernährungsweise</label>
        <select id="diet_type" name="diet_type" required>
            <?php foreach (['omnivore' => 'Omnivor', 'vegetarian' => 'Vegetarisch', 'vegan' => 'Vegan'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= (isset($values['diet_type']) && $values['diet_type'] === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach ?>
        </select>
        <span class="error"><?= $errors['diet_type'] ?? '' ?></span>

        <label for="meat_servings">Fleischportionen pro Woche (0–21)</label>
        <input type="range" id="meat_servings" name="meat_servings" min="0" max="21" required
            value="<?= htmlspecialchars($values['meat_servings'] ?? 0) ?>">
        <span class="range-value" id="meat_servings_value"><?= htmlspecialchars($values['meat_servings'] ?? 0) ?></span>
        <span class="error"><?= $errors['meat_servings'] ?? '' ?></span>

        <label for="weekly_waste">Wöchentliche Abfallmenge (kg 0–50)</label>
        <input type="range" id="weekly_waste" name="weekly_waste" min="0" max="50" required
            value="<?= htmlspecialchars($values['weekly_waste'] ?? 0) ?>">
        <span class="range-value" id="weekly_waste_value"><?= htmlspecialchars($values['weekly_waste'] ?? 0) ?></span>
        <span class="error"><?= $errors['weekly_waste'] ?? '' ?></span>

        <label for="clothing_items">Kleidungsstücke pro Jahr (0–100)</label>
        <input type="range" id="clothing_items" name="clothing_items" min="0" max="100" required
            value="<?= htmlspecialchars($values['clothing_items'] ?? 0) ?>">
        <span class="range-value" id="clothing_items_value"><?= htmlspecialchars($values['clothing_items'] ?? 0) ?></span>
        <span class="error"><?= $errors['clothing_items'] ?? '' ?></span>
    </fieldset>

    <div class="form-navigation">
        <button type="button" class="back-btn" onclick="location.href='step2.php'"><span>Zurück</span></button>
        <button type="submit" class="next-btn"><span>Weiter</span></button>
    </div>
</form>

<script src="js/sliders.js"></script>
<script src="js/diet-logic.js"></script>

<?php include 'includes/footer.php'; ?>

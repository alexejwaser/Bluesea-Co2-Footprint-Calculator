<?php
/**
 * step2.php – Schritt 2: Mobilität
 */
session_start();
if (!isset($_SESSION['form_data']['household_size'])) {
    header('Location: index.php');
    exit;
}
$values = $_SESSION['form_data'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_type = $_POST['car_type'] ?? '';
    $valid_car_types = ['none', 'petrol', 'diesel', 'hybrid', 'electric'];
    if (!in_array($car_type, $valid_car_types, true)) {
        $errors['car_type'] = 'Bitte Fahrzeugtyp wählen.';
    } else {
        $values['car_type'] = $car_type;
    }

    $values['car_distance'] = filter_input(INPUT_POST, 'car_distance', FILTER_VALIDATE_INT);
    if (
        $values['car_distance'] === false ||
        $values['car_distance'] < 0 ||
        $values['car_distance'] > 100000
    ) {
        $errors['car_distance'] = 'Wert zwischen 0 und 100000 nötig.';
    }

    $values['public_transport_km'] = filter_input(INPUT_POST, 'public_transport_km', FILTER_VALIDATE_INT);
    if (
        $values['public_transport_km'] === false ||
        $values['public_transport_km'] < 0 ||
        $values['public_transport_km'] > 2000
    ) {
        $errors['public_transport_km'] = 'Wert zwischen 0 und 2000 nötig.';
    }

    $values['flights_per_year'] = filter_input(
        INPUT_POST,
        'flights_per_year',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0, 'max_range' => 20]]
    );
    if ($values['flights_per_year'] === false) {
        $errors['flights_per_year'] = '0–20 erlaubt.';
    }

    $values['avg_flight_distance'] = filter_input(INPUT_POST, 'avg_flight_distance', FILTER_VALIDATE_INT);
    if (
        $values['avg_flight_distance'] === false ||
        $values['avg_flight_distance'] < 0 ||
        $values['avg_flight_distance'] > 20000
    ) {
        $errors['avg_flight_distance'] = 'Wert zwischen 0 und 20000 nötig.';
    }

    if (!$errors) {
        $_SESSION['form_data'] = $values;
        header('Location: step3.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="page-title">
    <h1>CO₂-Rechner</h1>
    <p>Schritt 2 von 4 – Mobilität</p>
</div>

<form method="post" autocomplete="off">
    <fieldset>
        <legend>Mobilität</legend>

        <label>Fahrzeugtyp</label>
        <?php
        $car_types = [
            'none' => 'Kein',
            'petrol' => 'Benzin',
            'diesel' => 'Diesel',
            'hybrid' => 'Hybrid',
            'electric' => 'Elektro'
        ];
        foreach ($car_types as $value => $label): ?>
            <span class="radio-group">
                <input type="radio" id="car_<?= $value ?>" name="car_type" value="<?= $value ?>" required
                    <?= (isset($values['car_type']) && $values['car_type'] === $value) ? 'checked' : '' ?>>
                <label for="car_<?= $value ?>"><?= $label ?></label>
            </span>
        <?php endforeach ?>
        <span class="error"><?= $errors['car_type'] ?? '' ?></span>

        <label for="car_distance">PKW-Kilometer pro Jahr (km/Jahr)</label>
        <input type="number" id="car_distance" name="car_distance" min="0" max="100000" required
            value="<?= htmlspecialchars($values['car_distance'] ?? 0) ?>">
        <span class="error"><?= $errors['car_distance'] ?? '' ?></span>

        <label for="public_transport_km">ÖV-Nutzung (km/Woche)</label>
        <input type="number" id="public_transport_km" name="public_transport_km" min="0" max="2000" required
            value="<?= htmlspecialchars($values['public_transport_km'] ?? 0) ?>">
        <span class="error"><?= $errors['public_transport_km'] ?? '' ?></span>

        <label for="flights_per_year">Flugreisen pro Jahr (0–20)</label>
        <input type="range" id="flights_per_year" name="flights_per_year" min="0" max="20"
            value="<?= htmlspecialchars($values['flights_per_year'] ?? 0) ?>">
        <span class="range-value" id="flights_per_year_value"><?= htmlspecialchars($values['flights_per_year'] ?? 0) ?></span>
        <span class="error"><?= $errors['flights_per_year'] ?? '' ?></span>

        <label for="avg_flight_distance">Durchschnittliche Flugdistanz (km)</label>
        <input type="number" id="avg_flight_distance" name="avg_flight_distance" min="0" max="20000" required
            value="<?= htmlspecialchars($values['avg_flight_distance'] ?? 0) ?>">
        <span class="error"><?= $errors['avg_flight_distance'] ?? '' ?></span>
    </fieldset>

    <div class="form-navigation">
        <button type="button" onclick="location.href='index.php'">Zurück</button>
        <button type="submit">Weiter</button>
    </div>
</form>

<script src="js/sliders.js"></script>

<?php include 'includes/footer.php'; ?>

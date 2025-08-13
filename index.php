<?php

/**
 * index.php – Hauptformular für den CO₂-Rechner
 * 
 * Diese Datei zeigt das mehrstufige Formular zur Erfassung der CO₂-relevanten Daten an.
 * Das Formular ist in vier Kategorien unterteilt: Wohnen, Mobilität, Lifestyle und Kontakt.
 * 
 * Funktionen:
 * - Session-basierte Fehlerbehandlung und Wertewiederherstellung
 * - Mehrstufiges Formular mit Fortschrittsanzeige
 * - Responsive Design für alle Geräte
 * - Client-seitige Validierung mit JavaScript
 */

// Session starten für Fehlerbehandlung und Wertewiederherstellung
session_start();

// Fehler und Werte aus der Session holen (falls vorhanden nach Validierungsfehlern)
$errors = $_SESSION['errors'] ?? [];
$values = $_SESSION['values'] ?? [];

// Session-Variablen nach dem Auslesen löschen, damit sie nicht bei der nächsten Anfrage stören
unset($_SESSION['errors'], $_SESSION['values']);

// Header-Template einbinden (enthält HTML-Kopf, Navigation, etc.)
include 'includes/header.php';
?>

<!-- Seitentitel und Beschreibung -->
<div class="page-title">
    <h1>CO₂-Rechner</h1>
    <p>Berechne deinen CO₂-Fussabdruck und erfahre, wie du deinen Fussabdruck reduzieren kannst.</p>
</div>

<!-- Hauptformular - wird an process.php gesendet -->
<form action="process.php" method="post" autocomplete="off">

    <!-- Fortschrittsbalken für mehrstufiges Formular -->
    <div class="form-progress mb-4 text-center">
        <div class="progress-bar w-full h-2 bg-gray-200 rounded overflow-hidden focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" role="progressbar" aria-label="Fortschritt" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
            <div class="progress-fill h-full bg-blue-600" style="width:25%"></div>
        </div>
    </div>

    <!-- Statusmeldungen für abgeschlossene Schritte -->
    <div id="step-status" class="hidden mb-4 p-2 text-green-700 bg-green-100 rounded" aria-live="polite"></div>

    <!-- Container für alle Formularschritte -->
    <div class="form-steps">

        <!-- SCHRITT 1: Wohnen - Erfassung der Wohnsituation und Energieverbrauch -->
        <fieldset>
            <legend>Wohnen</legend>

            <!-- Haushaltsgrösse mit Range-Slider -->
            <label for="household_size">Haushaltsgrösse (1–10 Personen)</label>
            <input type="range" id="household_size" name="household_size" min="1" max="10" required
                value="<?= htmlspecialchars($values['household_size'] ?? 1) ?>" autofocus>
            <span class="range-value" id="household_size_value"><?= htmlspecialchars($values['household_size'] ?? 1) ?></span>
            <span class="error"><?= $errors['household_size'] ?? '' ?></span>

            <!-- Wohnfläche in Quadratmetern -->
            <label for="living_area">Wohnfläche (m²)</label>
            <input type="number" id="living_area" name="living_area" step="0.1" min="0" max="1000" required
                value="<?= htmlspecialchars($values['living_area'] ?? '') ?>">
            <span class="error"><?= $errors['living_area'] ?? '' ?></span>

            <!-- Heizungsart - Radio-Buttons für verschiedene Heizungstypen -->
            <label>Heizungsart</label>
            <?php
            // Array mit Heizungstypen und deren Anzeigenamen
            $heating_types = [
                'gas' => 'Gas',
                'oil' => 'Öl',
                'district' => 'Fernwärme',
                'heatpump' => 'Wärmepumpe'
            ];

            // Schleife durch alle Heizungstypen
            foreach ($heating_types as $value => $label):
            ?>
                <span class="radio-group">
                    <input type="radio" id="heating_<?= $value ?>" name="heating_type" value="<?= $value ?>" required
                        <?= (isset($values['heating_type']) && $values['heating_type'] == $value) ? 'checked' : '' ?>>
                    <label for="heating_<?= $value ?>"><?= $label ?></label>
                </span>
            <?php endforeach ?>
            <span class="error"><?= $errors['heating_type'] ?? '' ?></span>

            <!-- Jährlicher Energieverbrauch -->
            <label for="energy_consumption">Jährlicher Energieverbrauch (kWh/Jahr)</label>
            <input type="number" id="energy_consumption" name="energy_consumption" min="0" max="200000" required
                value="<?= htmlspecialchars($values['energy_consumption'] ?? '') ?>">
            <span class="error"><?= $errors['energy_consumption'] ?? '' ?></span>
        </fieldset>

        <!-- SCHRITT 2: Mobilität - Erfassung des Transportverhaltens -->
        <fieldset>
            <legend>Mobilität</legend>

            <!-- Fahrzeugtyp - Radio-Buttons für verschiedene Antriebsarten -->
            <label>Fahrzeugtyp</label>
            <?php
            // Array mit Fahrzeugtypen und deren Anzeigenamen
            $car_types = [
                'none' => 'Kein',
                'petrol' => 'Benzin',
                'diesel' => 'Diesel',
                'hybrid' => 'Hybrid',
                'electric' => 'Elektro'
            ];

            // Schleife durch alle Fahrzeugtypen
            foreach ($car_types as $value => $label):
            ?>
                <span class="radio-group">
                    <input type="radio" id="car_<?= $value ?>" name="car_type" value="<?= $value ?>" required
                        <?= (isset($values['car_type']) && $values['car_type'] == $value) ? 'checked' : '' ?>>
                    <label for="car_<?= $value ?>"><?= $label ?></label>
                </span>
            <?php endforeach ?>
            <span class="error"><?= $errors['car_type'] ?? '' ?></span>

            <!-- Jährliche PKW-Kilometer -->
            <label for="car_distance">PKW-Kilometer pro Jahr (km/Jahr)</label>
            <input type="number" id="car_distance" name="car_distance" min="0" max="100000" required
                value="<?= htmlspecialchars($values['car_distance'] ?? 0) ?>">
            <span class="error"><?= $errors['car_distance'] ?? '' ?></span>

            <!-- Öffentlicher Verkehr - wöchentliche Nutzung -->
            <label for="public_transport_km">ÖV-Nutzung (km/Woche)</label>
            <input type="number" id="public_transport_km" name="public_transport_km" min="0" max="2000" required
                value="<?= htmlspecialchars($values['public_transport_km'] ?? 0) ?>">
            <span class="error"><?= $errors['public_transport_km'] ?? '' ?></span>

            <!-- Flugreisen pro Jahr mit Range-Slider -->
            <label for="flights_per_year">Flugreisen pro Jahr (0–20)</label>
            <input type="range" id="flights_per_year" name="flights_per_year" min="0" max="20"
                value="<?= htmlspecialchars($values['flights_per_year'] ?? 0) ?>">
            <span class="range-value" id="flights_per_year_value"><?= htmlspecialchars($values['flights_per_year'] ?? 0) ?></span>
            <span class="error"><?= $errors['flights_per_year'] ?? '' ?></span>

            <!-- Durchschnittliche Flugdistanz -->
            <label for="avg_flight_distance">Durchschnittliche Flugdistanz (km)</label>
            <input type="number" id="avg_flight_distance" name="avg_flight_distance" min="0" max="20000" required
                value="<?= htmlspecialchars($values['avg_flight_distance'] ?? 0) ?>">
            <span class="error"><?= $errors['avg_flight_distance'] ?? '' ?></span>
        </fieldset>

        <!-- SCHRITT 3: Lifestyle - Erfassung des Lebensstils und Konsumverhaltens -->
        <fieldset>
            <legend>Lifestyle</legend>

            <!-- Ernährungsweise - Dropdown-Auswahl -->
            <label for="diet_type">Ernährungsweise</label>
            <select id="diet_type" name="diet_type" required>
                <?php
                // Array mit Ernährungstypen und deren Anzeigenamen
                $diet_types = [
                    'omnivore' => 'Omnivor',
                    'vegetarian' => 'Vegetarisch',
                    'vegan' => 'Vegan'
                ];

                // Schleife durch alle Ernährungstypen
                foreach ($diet_types as $value => $label):
                ?>
                    <option value="<?= $value ?>" <?= (isset($values['diet_type']) && $values['diet_type'] == $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach ?>
            </select>
            <span class="error"><?= $errors['diet_type'] ?? '' ?></span>

            <!-- Fleischkonsum pro Woche mit Range-Slider -->
            <label for="meat_servings">Fleischportionen pro Woche (0–21)</label>
            <input type="range" id="meat_servings" name="meat_servings" min="0" max="21" required
                value="<?= htmlspecialchars($values['meat_servings'] ?? 0) ?>">
            <span class="range-value" id="meat_servings_value"><?= htmlspecialchars($values['meat_servings'] ?? 0) ?></span>
            <span class="error"><?= $errors['meat_servings'] ?? '' ?></span>

            <!-- Wöchentliche Abfallmenge mit Range-Slider -->
            <label for="weekly_waste">Wöchentliche Abfallmenge (kg 0–50)</label>
            <input type="range" id="weekly_waste" name="weekly_waste" min="0" max="50" required
                value="<?= htmlspecialchars($values['weekly_waste'] ?? 0) ?>">
            <span class="range-value" id="weekly_waste_value"><?= htmlspecialchars($values['weekly_waste'] ?? 0) ?></span>
            <span class="error"><?= $errors['weekly_waste'] ?? '' ?></span>

            <!-- Jährlicher Kleidungskonsum mit Range-Slider -->
            <label for="clothing_items">Kleidungsstücke pro Jahr (0–100)</label>
            <input type="range" id="clothing_items" name="clothing_items" min="0" max="100" required
                value="<?= htmlspecialchars($values['clothing_items'] ?? 0) ?>">
            <span class="range-value" id="clothing_items_value"><?= htmlspecialchars($values['clothing_items'] ?? 0) ?></span>
            <span class="error"><?= $errors['clothing_items'] ?? '' ?></span>
        </fieldset>

        <!-- SCHRITT 4: Kontakt - E-Mail-Adresse für Ergebnisse -->
        <fieldset>
            <legend>Kontakt</legend>

            <!-- E-Mail-Adresse für Zusendung der Ergebnisse -->
            <label for="email">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" maxlength="100" required autofocus
                value="<?= htmlspecialchars($values['email'] ?? '') ?>">
            <span class="error"><?= $errors['email'] ?? '' ?></span>
        </fieldset>
    </div>

    <!-- Navigation für mehrstufiges Formular -->
    <div class="form-navigation">
        <!-- Zurück-Button (initial versteckt) -->
        <button type="button" class="back-btn focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" style="display:none" aria-label="Zurück zum vorherigen Schritt"><span>Zurück</span></button>
        <!-- Weiter-Button für Navigation zwischen Schritten -->
        <button type="button" class="next-btn focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-label="Zum nächsten Schritt"><span>Weiter</span></button>
        <!-- Submit-Button für finale Berechnung (initial versteckt) -->
        <button type="submit" class="submit-btn focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" style="display:none" aria-label="CO₂ Fussabdruck berechnen"><span>CO₂ Fussabdruck berechnen</span></button>
    </div>
</form>

<!-- JavaScript-Dateien für Formularfunktionalität -->
<script src="js/validation.js"></script> <!-- Schrittlogik & HTML5-Validierung -->
<script src="js/sliders.js"></script> <!-- Range-Slider Funktionalität -->
<script src="js/progress.js"></script> <!-- Fortschrittsbalken und Navigation -->
<script src="js/diet-logic.js"></script> <!-- Logik für Ernährungsabhängige Felder -->

<?php
// Footer-Template einbinden (enthält Schlusstags und Footer-Inhalt)
include 'includes/footer.php';
?>

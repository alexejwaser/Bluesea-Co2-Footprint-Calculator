<?php

/**
 * process.php – Validierung der Formulardaten und CO₂-Berechnung
 * 
 * Diese Datei verarbeitet die vom Formular gesendeten Daten:
 * 1. Validiert alle Eingaben auf Korrektheit und Bereiche
 * 2. Berechnet den CO₂-Fussabdruck basierend auf wissenschaftlichen Emissionsfaktoren
 * 3. Kategorisiert die Emissionen in Wohnen, Mobilität, Ernährung und Konsum
 * 4. Vergleicht mit dem schweizer Durchschnitt
 * 5. Speichert Ergebnisse in der Session für die Anzeige
 * 
 * Bei Validierungsfehlern: Weiterleitung zurück zum Formular
 * Bei Erfolg: Weiterleitung zur Ergebnisseite
 */

// Session starten für Datenaustausch zwischen Seiten
session_start();

$data = $_SESSION['form_data'] ?? [];
if (!$data) {
    header('Location: index.php');
    exit;
}

$errors = [];
$values = [];

// 1. Haushaltsgrösse (1-10 Personen)
$values['household_size'] = filter_var(
    $data['household_size'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 10]]
);
if ($values['household_size'] === false) {
    $errors['household_size'] = 'Ungültiger Wert (1–10).';
}

// 2. Wohnfläche (0-1000 m²)
$values['living_area'] = filter_var($data['living_area'] ?? null, FILTER_VALIDATE_FLOAT);
if (
    $values['living_area'] === false ||
    $values['living_area'] < 0 ||
    $values['living_area'] > 1000
) {
    $errors['living_area'] = 'Bitte Fläche zwischen 0 und 1000 angeben.';
}

// 3. Heizungsart (vordefinierte Optionen)
$heating_type = $data['heating_type'] ?? '';
$valid_heating_types = ['gas', 'oil', 'district', 'heatpump'];
if (!in_array($heating_type, $valid_heating_types, true)) {
    $errors['heating_type'] = 'Bitte Heizungsart wählen.';
} else {
    $values['heating_type'] = $heating_type;
}

// 4. Energieverbrauch (0-200'000 kWh/Jahr)
$values['energy_consumption'] = filter_var($data['energy_consumption'] ?? null, FILTER_VALIDATE_INT);
if (
    $values['energy_consumption'] === false ||
    $values['energy_consumption'] < 0 ||
    $values['energy_consumption'] > 200000
) {
    $errors['energy_consumption'] = 'Wert zwischen 0 und 200000 erforderlich.';
}

// 5. Fahrzeugtyp (vordefinierte Optionen)
$car_type = $data['car_type'] ?? '';
$valid_car_types = ['none', 'petrol', 'diesel', 'hybrid', 'electric'];
if (!in_array($car_type, $valid_car_types, true)) {
    $errors['car_type'] = 'Bitte Fahrzeugtyp wählen.';
} else {
    $values['car_type'] = $car_type;
}

// 6. PKW-Kilometer pro Jahr (0-100'000 km)
$values['car_distance'] = filter_var($data['car_distance'] ?? null, FILTER_VALIDATE_INT);
if (
    $values['car_distance'] === false ||
    $values['car_distance'] < 0 ||
    $values['car_distance'] > 100000
) {
    $errors['car_distance'] = 'Wert zwischen 0 und 100000 nötig.';
}

// 7. Öffentlicher Verkehr (0-2000 km/Woche)
$values['public_transport_km'] = filter_var($data['public_transport_km'] ?? null, FILTER_VALIDATE_INT);
if (
    $values['public_transport_km'] === false ||
    $values['public_transport_km'] < 0 ||
    $values['public_transport_km'] > 2000
) {
    $errors['public_transport_km'] = 'Wert zwischen 0 und 2000 nötig.';
}

// 8. Flugreisen pro Jahr (0-20 Flüge)
$values['flights_per_year'] = filter_var(
    $data['flights_per_year'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 0, 'max_range' => 20]]
);
if ($values['flights_per_year'] === false) {
    $errors['flights_per_year'] = '0–20 erlaubt.';
}

// 9. Durchschnittliche Flugdistanz (0-20'000 km)
$values['avg_flight_distance'] = filter_var($data['avg_flight_distance'] ?? null, FILTER_VALIDATE_INT);
if (
    $values['avg_flight_distance'] === false ||
    $values['avg_flight_distance'] < 0 ||
    $values['avg_flight_distance'] > 20000
) {
    $errors['avg_flight_distance'] = 'Wert zwischen 0 und 20000 nötig.';
}

// 10. Ernährungsweise (vordefinierte Optionen)
$diet_type = $data['diet_type'] ?? '';
$valid_diet_types = ['omnivore', 'vegetarian', 'vegan'];
if (!in_array($diet_type, $valid_diet_types, true)) {
    $errors['diet_type'] = 'Bitte Ernährungsweise wählen.';
} else {
    $values['diet_type'] = $diet_type;
}

// 11. Fleischportionen pro Woche (0-21 Portionen)
$values['meat_servings'] = filter_var(
    $data['meat_servings'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 0, 'max_range' => 21]]
);
if ($values['meat_servings'] === false) {
    $errors['meat_servings'] = '0–21 erlaubt.';
}

// 12. Wöchentliche Abfallmenge (0-50 kg)
$values['weekly_waste'] = filter_var(
    $data['weekly_waste'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 0, 'max_range' => 50]]
);
if ($values['weekly_waste'] === false) {
    $errors['weekly_waste'] = '0–50 erlaubt.';
}

// 13. Kleidungsstücke pro Jahr (0-100 Stück)
$values['clothing_items'] = filter_var(
    $data['clothing_items'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 0, 'max_range' => 100]]
);
if ($values['clothing_items'] === false) {
    $errors['clothing_items'] = '0–100 erlaubt.';
}

// 14. E-Mail-Adresse (gültige E-Mail, max. 100 Zeichen)
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
if ($email === false || strlen($data['email'] ?? '') > 100) {
    $errors['email'] = 'Bitte gültige E-Mail bis 100 Zeichen.';
} else {
    $values['email'] = htmlspecialchars(trim($email), ENT_QUOTES);
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = array_merge($data, $values);
    header('Location: index.php');
    exit;
}

// Validierte Werte als einzelne Variablen verfügbar machen
extract($values);

/**
 * TEIL 2: CO₂-FUSSABDRUCK BERECHNUNG
 * Basiert auf wissenschaftlichen Emissionsfaktoren für die Schweiz
 */

// Emissionsfaktoren (kg CO₂ pro Einheit) - basierend auf schweizer Durchschnittswerten
$emission_factors = [
    // Heizung: kg CO₂ pro kWh
    'heating' => [
        'gas' => 0.202,        // Erdgas
        'oil' => 0.266,        // Heizöl
        'district' => 0.066,   // Fernwärme (meist erneuerbar in CH)
        'heatpump' => 0.0117   // Wärmepumpe (schweizer Strommix)
    ],
    // Transport: kg CO₂ pro km
    'car' => [
        'none' => 0,           // Kein Auto
        'petrol' => 0.180,     // Benzinfahrzeug
        'diesel' => 0.160,     // Dieselfahrzeug
        'hybrid' => 0.072,     // Hybridfahrzeug
        'electric' => 0.0053   // Elektrofahrzeug (schweizer Strommix)
    ],
    'public_transport' => 0.040,  // ÖV: kg CO₂ pro km
    'flight' => 0.150,            // Flug: kg CO₂ pro km (inkl. Höhenfaktor)

    // Ernährung: kg CO₂ pro Jahr (Basis)
    'diet_base' => [
        'omnivore' => 2100,       // Mischkost
        'vegetarian' => 1600,     // Vegetarisch
        'vegan' => 1300           // Vegan
    ],
    'meat_portion' => 2.5,        // Zusätzliche kg CO₂ pro Fleischportion

    // Konsum: kg CO₂ pro Einheit
    'waste' => 1.0,               // Abfall: kg CO₂ pro kg Abfall
    'clothing' => 22              // Kleidung: kg CO₂ pro Kleidungsstück
];

// Hilfsvariablen für Jahresberechnungen
$weeks_per_year = 52;
$meat_per_year = $values['meat_servings'] * $weeks_per_year;
$waste_per_year = $values['weekly_waste'] * $weeks_per_year;
$public_transport_per_year = $values['public_transport_km'] * $weeks_per_year;

/**
 * TEIL 3: BERECHNUNG DER CO₂-EMISSIONEN NACH KATEGORIEN
 */

// 3.1 Wohnen: Heizung basierend auf Energieverbrauch und Heizungsart
$co2_heating = $values['energy_consumption'] * $emission_factors['heating'][$values['heating_type']];

// 3.2 Mobilität Auto: Jährliche Kilometer mal Emissionsfaktor des Fahrzeugtyps
$co2_car = $values['car_distance'] * $emission_factors['car'][$values['car_type']];

// 3.3 Mobilität ÖV: Wöchentliche km auf Jahr hochrechnen
$co2_public_transport = $public_transport_per_year * $emission_factors['public_transport'];

// 3.4 Mobilität Flug: Anzahl Flüge mal durchschnittliche Distanz
$co2_flights = $values['flights_per_year'] * $values['avg_flight_distance'] * $emission_factors['flight'];

// 3.5 Ernährung: Basis-Emissionen plus zusätzliche Fleischportionen (nur bei Omnivoren)
$co2_diet_base = $emission_factors['diet_base'][$values['diet_type']];
$co2_meat_extra = ($values['diet_type'] === 'omnivore') ? ($meat_per_year * $emission_factors['meat_portion']) : 0;
$co2_diet = $co2_diet_base + $co2_meat_extra;

// 3.6 Konsum: Abfall und Kleidung
$co2_waste = $waste_per_year * $emission_factors['waste'];
$co2_clothing = $values['clothing_items'] * $emission_factors['clothing'];

/**
 * TEIL 4: GESAMTBERECHNUNG UND KATEGORISIERUNG
 */

// Gesamtemissionen des Haushalts
$co2_total_household = $co2_heating + $co2_car + $co2_public_transport + $co2_flights + $co2_diet + $co2_waste + $co2_clothing;

// Pro-Kopf-Emissionen (Division durch Haushaltsgrösse, mindestens 1)
$co2_per_person = $co2_total_household / max(1, $values['household_size']);

// Kategorien für die Anzeige gruppieren (pro Person)
$co2_categories = [
    'wohnen' => $co2_heating / max(1, $values['household_size']),
    'mobilitat' => ($co2_car + $co2_public_transport + $co2_flights) / max(1, $values['household_size']),
    'ernahrung' => $co2_diet / max(1, $values['household_size']),
    'konsum' => ($co2_waste + $co2_clothing) / max(1, $values['household_size'])
];

/**
 * TEIL 5: VERGLEICH MIT SCHWEIZER DURCHSCHNITT UND BEWERTUNG
 */

// Schweizer Durchschnitt: 6 Tonnen CO₂ pro Person und Jahr
$swiss_average = 6000; // kg CO₂ pro Person pro Jahr
$percentage_vs_average = (($co2_per_person - $swiss_average) / $swiss_average) * 100;

// Bestimmung ob über oder unter dem Durchschnitt
$is_above_average = $co2_per_person > $swiss_average;
$comparison_text = $is_above_average ? 'über dem schweizer Durchschnitt' : 'unter dem schweizer Durchschnitt';

// Farbkodierung basierend auf Abweichung vom Durchschnitt
$performance_color = '#00b4d8'; // Standard: Blau (Durchschnitt)
$performance_class = 'average';

if ($percentage_vs_average < -20) {
    // Deutlich unter Durchschnitt (sehr gut)
    $performance_color = '#28a745'; // Grün
    $performance_class = 'excellent';
} elseif ($percentage_vs_average < -10) {
    // Mässig unter Durchschnitt (gut)
    $performance_color = '#20c997'; // Hellgrün
    $performance_class = 'good';
} elseif ($percentage_vs_average <= 20) {
    // Im Durchschnittsbereich (±20%)
    $performance_color = '#00b4d8'; // Blau
    $performance_class = 'average';
} elseif ($percentage_vs_average <= 40) {
    // Mässig über Durchschnitt (schlecht)
    $performance_color = '#fd7e14'; // Orange
    $performance_class = 'poor';
} else {
    // Deutlich über Durchschnitt (sehr schlecht)
    $performance_color = '#dc3545'; // Rot
    $performance_class = 'bad';
}

/**
 * TEIL 6: ERGEBNISSE IN SESSION SPEICHERN
 * Alle berechneten Werte werden für die Anzeige auf der Ergebnisseite gespeichert
 */
$_SESSION['co2_results'] = [
    'total_per_person' => $co2_per_person,           // CO₂ pro Person in kg/Jahr
    'total_household' => $co2_total_household,       // CO₂ gesamter Haushalt in kg/Jahr
    'categories' => $co2_categories,                 // CO₂ nach Kategorien (pro Person)
    'percentage_vs_average' => abs($percentage_vs_average), // Abweichung vom Durchschnitt (absolut)
    'is_above_average' => $is_above_average,         // Boolean: über Durchschnitt?
    'comparison_text' => $comparison_text,           // Text für Vergleich
    'swiss_average' => $swiss_average,               // Schweizer Durchschnitt
    'performance_color' => $performance_color,       // Farbe für Bewertung
    'performance_class' => $performance_class        // CSS-Klasse für Bewertung
];

// Validierte Werte ebenfalls in Session speichern (für eventuelle Rückkehr zum Formular)
$_SESSION['values'] = $values;

// Weiterleitung zur Ergebnisseite
header('Location: confirmation.php');
exit;

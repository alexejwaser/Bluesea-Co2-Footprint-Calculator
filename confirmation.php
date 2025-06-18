<?php

/**
 * confirmation.php – Anzeige der CO₂-Berechnungsergebnisse
 * 
 * Diese Seite zeigt die berechneten CO₂-Emissionen in einer übersichtlichen Form an:
 * - Animierter Kreis mit Gesamtemissionen pro Person
 * - Aufschlüsselung nach Kategorien (Wohnen, Mobilität, Ernährung, Konsum)
 * - Vergleich mit dem schweizer Durchschnitt
 * - Farbkodierte Bewertung der Umweltleistung
 * - Links für weitere Aktionen (Formular wiederholen, Broschüre)
 */

// Session starten um Berechnungsergebnisse abzurufen
session_start();

// Validierte Formulardaten und Berechnungsergebnisse aus Session holen
$values = $_SESSION['values'] ?? [];
$co2_results = $_SESSION['co2_results'] ?? [];

// Sicherheitscheck: Falls keine Daten vorhanden, zurück zum Formular
if (!$values || !$co2_results) {
    header('Location: index.php');
    exit;
}

// Header-Template einbinden (HTML-Kopf, Navigation)
include 'includes/header.php';

/**
 * DATENAUFBEREITUNG FÜR DIE ANZEIGE
 * Umrechnung von kg in Tonnen für bessere Lesbarkeit
 */

// Gesamtemissionen pro Person von kg in Tonnen umrechnen
$total_tons = $co2_results['total_per_person'] / 1000;

// Schweizer Durchschnitt von kg in Tonnen umrechnen
$swiss_avg_tons = $co2_results['swiss_average'] / 1000;

// Kategorien von kg in Tonnen umrechnen für die Anzeige
$categories_tons = [
    'wohnen' => $co2_results['categories']['wohnen'] / 1000,
    'mobilitat' => $co2_results['categories']['mobilitat'] / 1000,
    'ernahrung' => $co2_results['categories']['ernahrung'] / 1000,
    'konsum' => $co2_results['categories']['konsum'] / 1000
];

// Höchsten Kategoriewert finden für Skalierung der Balkendiagramme
$max_category = max($categories_tons);
?>

<!-- Hauptcontainer für die Ergebnisanzeige -->
<div class="results-container">

    <!-- CO₂-KREIS: Zentrale Anzeige der Gesamtemissionen -->
    <div class="co2-circle">
        <!-- Äusserer Kreis mit animiertem Rand (Farbe basierend auf Bewertung) -->
        <div class="circle-outer animate-circle" style="--performance-color: <?= $co2_results['performance_color'] ?>;">
            <!-- Innerer Kreis mit CO₂-Werten -->
            <div class="circle-inner">
                <h2>CO<sub>2</sub></h2>
                <p><?= number_format($total_tons, 1) ?><br>Tonnen CO<sub>2</sub>/Jahr</p>
            </div>
        </div>
    </div>

    <!-- KATEGORIEN-AUFSCHLÜSSELUNG: Detaillierte Emissionen nach Bereichen -->
    <div class="category-breakdown">
        <h2>Aufschlüsselung nach Kategorien</h2>

        <!-- Kategorie: Wohnen (Heizung, Energie) -->
        <div class="category-item">
            <div class="category-info">
                <span class="category-name">Wohnen</span>
                <span class="category-value"><?= number_format($categories_tons['wohnen'], 1) ?> t</span>
            </div>
            <!-- Balkendiagramm: Breite proportional zum Anteil an der höchsten Kategorie -->
            <div class="category-bar">
                <div class="bar-fill" style="width: <?= ($categories_tons['wohnen'] / $max_category) * 100 ?>%"></div>
            </div>
        </div>

        <!-- Kategorie: Mobilität (Auto, ÖV, Flüge) -->
        <div class="category-item">
            <div class="category-info">
                <span class="category-name">Mobilität</span>
                <span class="category-value"><?= number_format($categories_tons['mobilitat'], 1) ?> t</span>
            </div>
            <div class="category-bar">
                <div class="bar-fill" style="width: <?= ($categories_tons['mobilitat'] / $max_category) * 100 ?>%"></div>
            </div>
        </div>

        <!-- Kategorie: Ernährung (Diät, Fleischkonsum) -->
        <div class="category-item">
            <div class="category-info">
                <span class="category-name">Ernährung</span>
                <span class="category-value"><?= number_format($categories_tons['ernahrung'], 1) ?> t</span>
            </div>
            <div class="category-bar">
                <div class="bar-fill" style="width: <?= ($categories_tons['ernahrung'] / $max_category) * 100 ?>%"></div>
            </div>
        </div>

        <!-- Kategorie: Konsum (Abfall, Kleidung) -->
        <div class="category-item">
            <div class="category-info">
                <span class="category-name">Konsum</span>
                <span class="category-value"><?= number_format($categories_tons['konsum'], 1) ?> t</span>
            </div>
            <div class="category-bar">
                <div class="bar-fill" style="width: <?= ($categories_tons['konsum'] / $max_category) * 100 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- VERGLEICHSSEKTION: Gegenüberstellung mit schweizer Durchschnitt -->
    <div class="comparison-section">
        <h2>Vergleich zum Durchschnitt</h2>
        <div class="comparison-stats">
            <!-- Linke Seite: Prozentuale Abweichung vom Durchschnitt -->
            <div class="comparison-item">
                <!-- Prozentsatz in der Bewertungsfarbe anzeigen -->
                <div class="percentage" style="color: <?= $co2_results['performance_color'] ?>;">
                    <?= round($co2_results['percentage_vs_average']) ?>%
                </div>
                <!-- Beschreibungstext (über/unter Durchschnitt) -->
                <p><?= $co2_results['comparison_text'] ?></p>
            </div>

            <!-- Trennlinie zwischen den beiden Vergleichswerten -->
            <div class="comparison-divider"></div>

            <!-- Rechte Seite: Schweizer Durchschnittswert -->
            <div class="comparison-item">
                <div class="average-value"><?= number_format($swiss_avg_tons, 1) ?></div>
                <p>Tonnen CO<sub>2</sub>/Jahr<br>schweizer Durchschnitt</p>
            </div>
        </div>
    </div>

    <!-- AKTIONSBEREICH: Buttons für weitere Schritte -->
    <div class="results-actions">
        <!-- Button: Zurück zum Formular für neue Berechnung -->
        <a href="index.php" class="back-to-form">Zurück zum Formular</a>

        <!-- Button: Link zur Broschüre mit Tipps zur CO₂-Reduktion -->
        <a href="brochure.pdf" class="secondary-action-btn">
            <span class="icon">?</span>Finde heraus wie du deinen Fussabdruck verkleinern kannst
        </a>
    </div>
</div>

<!-- JavaScript für Kreis-Animation laden -->
<script src="js/animations.js"></script>

<?php
// Footer-Template einbinden (Schlusstags, Footer-Inhalt)
include 'includes/footer.php';
?>
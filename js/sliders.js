/**
 * sliders.js – Funktionalität für Range-Slider (Schieberegler)
 *
 * Diese Datei implementiert die Funktionalität für alle Range-Input-Felder:
 * - Anzeige des aktuellen Werts neben dem Slider
 * - Visuelle Fortschrittsanzeige mit Farbverlauf
 * - Automatische Aktualisierung bei Wertänderungen
 * - Responsive Berechnung der Fortschritts-Prozente
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * INITIALISIERUNG ALLER RANGE-SLIDER
   * Durchläuft alle input[type="range"] Elemente und fügt Funktionalität hinzu
   */
  document.querySelectorAll('input[type="range"]').forEach(function (input) {
    /**
     * WERTANZEIGE NEBEN DEM SLIDER
     * Sucht das entsprechende Anzeige-Element (ID: slider_id + "_value")
     */
    var output = document.getElementById(input.id + "_value");
    if (output) {
      // Initialen Wert anzeigen
      output.textContent = input.value;

      // Event-Listener: Wert bei Änderung aktualisieren
      input.addEventListener("input", function () {
        output.textContent = input.value;
      });
    }

    /**
     * FORTSCHRITTS-GRADIENT FUNKTION
     * Berechnet und setzt den visuellen Fortschritt des Sliders
     * Verwendet CSS Custom Property --progress für den Farbverlauf
     */
    function setProgress() {
      // Min/Max Werte aus HTML-Attributen lesen (mit Fallback-Werten)
      var min = input.min ? parseFloat(input.min) : 0;
      var max = input.max ? parseFloat(input.max) : 100;
      var val = parseFloat(input.value);

      // Prozentsatz berechnen: (aktueller Wert - Minimum) / (Maximum - Minimum) * 100
      var percent = ((val - min) / (max - min)) * 100;

      // CSS Custom Property setzen für Gradient-Anzeige
      // Wird in style.css für background-gradient verwendet
      input.style.setProperty("--progress", percent + "%");
    }

    // Initialen Fortschritt setzen
    setProgress();

    // Event-Listener: Fortschritt bei Wertänderung aktualisieren
    input.addEventListener("input", setProgress);
  });
});

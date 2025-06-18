/**
 * progress.js – Fortschrittsbalken für mehrstufiges Formular
 *
 * Diese Datei implementiert die Fortschrittsanzeige für das 4-stufige CO₂-Formular:
 * - Visueller Fortschrittsbalken oben im Formular
 * - Automatische Aktualisierung bei Schritt-Wechseln
 * - Kommunikation mit validation.js über Events und globale Funktionen
 * - Prozentuale Berechnung basierend auf aktueller Schritt-Position
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * INITIALISIERUNG DER FORTSCHRITTSBALKEN-ELEMENTE
   */
  const steps = document.querySelectorAll(".form-steps fieldset"); // Alle Formularschritte
  const progressFill = document.querySelector(".progress-fill"); // Fortschrittsbalken-Füllung

  /**
   * FUNKTION: FORTSCHRITTSBALKEN AKTUALISIEREN
   * @param {number} stepIndex - Aktueller Schritt (0-basiert: 0=Schritt 1, 1=Schritt 2, etc.)
   */
  function updateProgress(stepIndex) {
    console.log(
      "Updating progress to step:",
      stepIndex + 1,
      "of",
      steps.length
    );

    // Fortschritt in Prozent berechnen
    // (aktueller Schritt + 1) / Gesamtanzahl Schritte * 100
    // +1 weil stepIndex 0-basiert ist, aber wir Schritt 1-4 anzeigen wollen
    const progressPercentage = ((stepIndex + 1) / steps.length) * 100;

    if (progressFill) {
      // CSS width-Property setzen für visuelle Darstellung
      progressFill.style.width = progressPercentage + "%";
      console.log("Progress bar width set to:", progressPercentage + "%");
    } else {
      console.error("Progress fill element not found!");
    }
  }

  /**
   * INITIALISIERUNG: FORTSCHRITTSBALKEN AUF ERSTEN SCHRITT SETZEN
   * Startet bei 25% (Schritt 1 von 4)
   */
  updateProgress(0);

  /**
   * EVENT-LISTENER: SCHRITT-ÄNDERUNGEN VON VALIDATION.JS
   * Reagiert auf Custom Events die von der Validierungs-Logik gesendet werden
   */
  window.addEventListener("stepChanged", function (event) {
    console.log("Step changed event received:", event.detail);
    updateProgress(event.detail.currentStep);
  });

  /**
   * GLOBALE FUNKTION BEREITSTELLEN
   * Ermöglicht validation.js direkten Zugriff auf updateProgress()
   * Backup-Methode falls Event-System nicht funktioniert
   */
  window.updateProgress = updateProgress;
});

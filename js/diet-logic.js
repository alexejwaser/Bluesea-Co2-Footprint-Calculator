/**
 * diet-logic.js – Ernährungsabhängige Formular-Logik
 *
 * Diese Datei implementiert die bedingte Anzeige von Formularfeldern basierend auf der Ernährungsweise:
 * - Versteckt das Fleischportionen-Feld bei vegetarischer/veganer Ernährung
 * - Setzt Fleischportionen automatisch auf 0 bei vegetarisch/vegan
 * - Zeigt/versteckt alle zugehörigen Elemente (Label, Input, Wertanzeige, Fehlermeldung)
 * - Reagiert sowohl auf initiale Werte als auch auf Änderungen
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * INITIALISIERUNG DER ERNÄHRUNGS-ABHÄNGIGEN ELEMENTE
   * Sammelt alle Elemente die von der Ernährungsweise beeinflusst werden
   */
  const dietSelect = document.getElementById("diet_type"); // Dropdown für Ernährungsweise
  const meatLabel = document.querySelector('label[for="meat_servings"]'); // Label für Fleischportionen
  const meatInput = document.getElementById("meat_servings"); // Range-Slider für Fleischportionen
  const meatValue = document.getElementById("meat_servings_value"); // Wertanzeige für Fleischportionen
  const meatError = meatInput ? meatInput.nextElementSibling : null; // Fehlermeldung für Fleischportionen

  // Nur ausführen wenn alle notwendigen Elemente vorhanden sind
  if (dietSelect && meatInput) {
    /**
     * FUNKTION: FLEISCHPORTIONEN-FELD EIN-/AUSBLENDEN
     * Zeigt oder versteckt das Fleischportionen-Feld basierend auf der gewählten Ernährungsweise
     */
    function toggleMeatField() {
      const dietValue = dietSelect.value;

      // Prüfen ob vegetarische oder vegane Ernährung gewählt wurde
      const isVegetarianOrVegan =
        dietValue === "vegetarian" || dietValue === "vegan";

      /**
       * ALLE BETROFFENEN ELEMENTE SAMMELN
       * Liste aller Elemente die gezeigt/versteckt werden müssen
       */
      const elementsToToggle = [meatLabel, meatInput, meatValue];

      // Fehlermeldung hinzufügen falls vorhanden und korrekt klassifiziert
      if (meatError && meatError.classList.contains("error")) {
        elementsToToggle.push(meatError);
      }

      /**
       * SICHTBARKEIT ALLER ELEMENTE ANPASSEN
       * Versteckt bei vegetarisch/vegan, zeigt bei omnivorer Ernährung
       */
      elementsToToggle.forEach((element) => {
        if (element) {
          if (isVegetarianOrVegan) {
            element.style.display = "none"; // Verstecken
          } else {
            element.style.display = ""; // Anzeigen (Standard CSS)
          }
        }
      });

      /**
       * FLEISCHPORTIONEN AUF 0 SETZEN BEI VEGETARISCH/VEGAN
       * Automatische Anpassung des Werts für korrekte CO₂-Berechnung
       */
      if (isVegetarianOrVegan) {
        meatInput.value = 0; // Range-Slider auf 0 setzen

        // Wertanzeige ebenfalls auf 0 setzen (falls vorhanden)
        if (meatValue) {
          meatValue.textContent = 0;
        }
      }
    }

    /**
     * INITIALE AUSFÜHRUNG
     * Behandelt bereits vorausgewählte Werte (z.B. bei Validierungsfehlern)
     */
    toggleMeatField();

    /**
     * EVENT-LISTENER: ÄNDERUNGEN DER ERNÄHRUNGSWEISE
     * Reagiert auf Benutzer-Auswahl im Dropdown
     */
    dietSelect.addEventListener("change", toggleMeatField);
  }
});

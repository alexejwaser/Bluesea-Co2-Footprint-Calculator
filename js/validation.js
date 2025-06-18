/**
 * validation.js – Client-seitige Validierung für mehrstufiges Formular
 *
 * Diese Datei implementiert die Logik für das mehrstufige CO₂-Formular:
 * - Navigation zwischen den vier Formularschritten
 * - Client-seitige Validierung aller Eingabefelder
 * - Anzeige von Fehlermeldungen in deutscher Sprache
 * - Synchronisation mit dem Fortschrittsbalken
 * - Verhinderung der Formularübermittlung bei Validierungsfehlern
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * INITIALISIERUNG DER MEHRSTUFIGEN FORMULAR-LOGIK
   */

  // Alle Formularschritte (fieldsets) sammeln
  const steps = document.querySelectorAll(".form-steps fieldset");

  // Navigations-Elemente finden
  const nav = document.querySelector(".form-navigation");
  const nextBtn = nav.querySelector(".next-btn"); // "Weiter"-Button
  const backBtn = nav.querySelector(".back-btn"); // "Zurück"-Button
  const submitBtn = nav.querySelector(".submit-btn"); // "Berechnen"-Button

  // Aktueller Schritt (0-basiert: 0=Wohnen, 1=Mobilität, 2=Lifestyle, 3=Kontakt)
  let currentStep = 0;

  /**
   * FUNKTION: BESTIMMTEN FORMULARSCHRITT ANZEIGEN
   * @param {number} idx - Index des anzuzeigenden Schritts (0-3)
   */
  function showStep(idx) {
    // Alle Schritte durchgehen und nur den aktuellen anzeigen
    steps.forEach((fieldset, i) => {
      fieldset.style.display = i === idx ? "block" : "none";
    });

    // Navigations-Buttons je nach Schritt ein-/ausblenden
    backBtn.style.display = idx === 0 ? "none" : ""; // Zurück-Button nur ab Schritt 2
    nextBtn.style.display = idx === steps.length - 1 ? "none" : ""; // Weiter-Button nicht im letzten Schritt
    submitBtn.style.display = idx === steps.length - 1 ? "" : "none"; // Submit-Button nur im letzten Schritt

    // Fortschrittsbalken aktualisieren (falls verfügbar)
    if (window.updateProgress) {
      window.updateProgress(idx);
    }

    // Event für Fortschrittsbalken senden (Backup-Methode)
    window.dispatchEvent(
      new CustomEvent("stepChanged", {
        detail: { currentStep: idx },
      })
    );
  }

  // Ersten Schritt initial anzeigen
  showStep(currentStep);

  /**
   * FUNKTION: VALIDIERUNG EINES FORMULARSCHRITTS
   * @param {number} stepIdx - Index des zu validierenden Schritts
   * @returns {boolean} - true wenn alle Felder gültig sind, false bei Fehlern
   */
  function validateStep(stepIdx) {
    const fieldset = steps[stepIdx];
    let valid = true;

    // Alle vorherigen Fehlermeldungen löschen
    fieldset
      .querySelectorAll(".error")
      .forEach((errorElement) => (errorElement.textContent = ""));

    // Set für bereits validierte Radio-Button-Gruppen (um Duplikate zu vermeiden)
    const validatedRadios = new Set();

    // Alle Eingabefelder im aktuellen Schritt durchgehen
    fieldset.querySelectorAll("input, select, textarea").forEach((input) => {
      /**
       * VALIDIERUNG: RADIO-BUTTONS
       * Jede Radio-Gruppe nur einmal validieren
       */
      if (input.type === "radio") {
        if (validatedRadios.has(input.name)) return; // Gruppe bereits validiert
        validatedRadios.add(input.name);

        // Alle Radio-Buttons der Gruppe finden
        const radios = fieldset.querySelectorAll(
          'input[name="' + input.name + '"]'
        );
        const checked = fieldset.querySelector(
          'input[name="' + input.name + '"]:checked'
        );

        if (!checked) {
          // Fehler-Element nach dem letzten Radio-Button der Gruppe finden
          const lastRadio = radios[radios.length - 1];
          let error = lastRadio.parentElement.nextElementSibling;
          if (!error || !error.classList.contains("error")) {
            // Fallback: Fehler-Element im fieldset suchen
            error = lastRadio.closest("fieldset").querySelector(".error");
          }

          // Spezifische Fehlermeldungen je nach Feldname
          if (error) {
            if (input.name === "heating_type") {
              error.textContent = "Bitte Heizungsart wählen.";
            } else if (input.name === "car_type") {
              error.textContent = "Bitte Fahrzeugtyp wählen.";
            } else {
              error.textContent = "Bitte wählen.";
            }
          }
          valid = false;
        }
      } else if (input.type === "email") {
        /**
         * VALIDIERUNG: E-MAIL-FELDER
         */
        const error =
          input.nextElementSibling &&
          input.nextElementSibling.classList.contains("error")
            ? input.nextElementSibling
            : null;

        if (input.value.trim() === "") {
          if (error)
            error.textContent = "Bitte gültige E-Mail bis 100 Zeichen.";
          valid = false;
        } else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(input.value)) {
          if (error)
            error.textContent = "Bitte gültige E-Mail bis 100 Zeichen.";
          valid = false;
        } else if (input.value.length > 100) {
          if (error)
            error.textContent = "Bitte gültige E-Mail bis 100 Zeichen.";
          valid = false;
        }
      } else if (input.type === "number" || input.type === "range") {
        /**
         * VALIDIERUNG: ZAHLEN- UND RANGE-FELDER
         */
        const error =
          input.nextElementSibling &&
          input.nextElementSibling.classList.contains("error")
            ? input.nextElementSibling
            : null;

        // Prüfung auf leere oder ungültige Werte
        if (input.value === "" || isNaN(input.value)) {
          if (error) {
            // Spezifische Fehlermeldungen je nach Feldname (identisch mit PHP-Validierung)
            if (input.name === "household_size") {
              error.textContent = "Ungültiger Wert (0–10).";
            } else if (input.name === "living_area") {
              error.textContent = "Bitte Fläche zwischen 0 und 1000 angeben.";
            } else if (input.name === "energy_consumption") {
              error.textContent = "Wert zwischen 0 und 200000 erforderlich.";
            } else if (input.name === "car_distance") {
              error.textContent = "Wert zwischen 0 und 100000 nötig.";
            } else if (input.name === "public_transport_km") {
              error.textContent = "Wert zwischen 0 und 2000 nötig.";
            } else if (input.name === "flights_per_year") {
              error.textContent = "0–20 erlaubt.";
            } else if (input.name === "avg_flight_distance") {
              error.textContent = "Wert zwischen 0 und 20000 nötig.";
            } else if (input.name === "meat_servings") {
              error.textContent = "0–21 erlaubt.";
            } else if (input.name === "weekly_waste") {
              error.textContent = "0–50 erlaubt.";
            } else if (input.name === "clothing_items") {
              error.textContent = "0–100 erlaubt.";
            } else {
              error.textContent = "Bitte Wert eingeben.";
            }
          }
          valid = false;
        } else {
          // Bereichsprüfung (min/max Werte)
          const value = Number(input.value);
          const min = input.hasAttribute("min") ? Number(input.min) : null;
          const max = input.hasAttribute("max") ? Number(input.max) : null;

          // Minimum-Wert-Prüfung
          if (min !== null && value < min) {
            if (error) {
              // Spezifische Fehlermeldungen für Minimum-Verletzungen
              if (input.name === "household_size") {
                error.textContent = "Ungültiger Wert (0–10).";
              } else if (input.name === "living_area") {
                error.textContent = "Bitte Fläche zwischen 0 und 1000 angeben.";
              } else if (input.name === "energy_consumption") {
                error.textContent = "Wert zwischen 0 und 200000 erforderlich.";
              } else if (input.name === "car_distance") {
                error.textContent = "Wert zwischen 0 und 100000 nötig.";
              } else if (input.name === "public_transport_km") {
                error.textContent = "Wert zwischen 0 und 2000 nötig.";
              } else if (input.name === "flights_per_year") {
                error.textContent = "0–20 erlaubt.";
              } else if (input.name === "avg_flight_distance") {
                error.textContent = "Wert zwischen 0 und 20000 nötig.";
              } else if (input.name === "meat_servings") {
                error.textContent = "0–21 erlaubt.";
              } else if (input.name === "weekly_waste") {
                error.textContent = "0–50 erlaubt.";
              } else if (input.name === "clothing_items") {
                error.textContent = "0–100 erlaubt.";
              } else {
                error.textContent = `Mindestens ${min}`;
              }
            }
            valid = false;
          }

          // Maximum-Wert-Prüfung
          if (max !== null && value > max) {
            if (error) {
              // Spezifische Fehlermeldungen für Maximum-Verletzungen
              if (input.name === "household_size") {
                error.textContent = "Ungültiger Wert (0–10).";
              } else if (input.name === "living_area") {
                error.textContent = "Bitte Fläche zwischen 0 und 1000 angeben.";
              } else if (input.name === "energy_consumption") {
                error.textContent = "Wert zwischen 0 und 200000 erforderlich.";
              } else if (input.name === "car_distance") {
                error.textContent = "Wert zwischen 0 und 100000 nötig.";
              } else if (input.name === "public_transport_km") {
                error.textContent = "Wert zwischen 0 und 2000 nötig.";
              } else if (input.name === "flights_per_year") {
                error.textContent = "0–20 erlaubt.";
              } else if (input.name === "avg_flight_distance") {
                error.textContent = "Wert zwischen 0 und 20000 nötig.";
              } else if (input.name === "meat_servings") {
                error.textContent = "0–21 erlaubt.";
              } else if (input.name === "weekly_waste") {
                error.textContent = "0–50 erlaubt.";
              } else if (input.name === "clothing_items") {
                error.textContent = "0–100 erlaubt.";
              } else {
                error.textContent = `Höchstens ${max}`;
              }
            }
            valid = false;
          }
        }
      } else if (input.tagName === "SELECT") {
        /**
         * VALIDIERUNG: DROPDOWN-FELDER (SELECT)
         */
        const error =
          input.nextElementSibling &&
          input.nextElementSibling.classList.contains("error")
            ? input.nextElementSibling
            : null;

        if (!input.value) {
          if (error) {
            if (input.name === "diet_type") {
              error.textContent = "Bitte Ernährungsweise wählen.";
            } else {
              error.textContent = "Bitte wählen.";
            }
          }
          valid = false;
        }
      } else {
        /**
         * VALIDIERUNG: ANDERE TEXTFELDER
         */
        const error =
          input.nextElementSibling &&
          input.nextElementSibling.classList.contains("error")
            ? input.nextElementSibling
            : null;

        if (input.value.trim() === "") {
          if (error) error.textContent = "Bitte ausfüllen.";
          valid = false;
        }
      }
    });

    return valid;
  }

  /**
   * EVENT-LISTENER: "WEITER"-BUTTON
   * Validiert aktuellen Schritt und geht zum nächsten weiter
   */
  nextBtn.addEventListener("click", function () {
    if (validateStep(currentStep)) {
      if (currentStep < steps.length - 1) {
        currentStep++;
        showStep(currentStep);
      }
    }
  });

  /**
   * EVENT-LISTENER: "ZURÜCK"-BUTTON
   * Geht zum vorherigen Schritt zurück (ohne Validierung)
   */
  backBtn.addEventListener("click", function () {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  /**
   * EVENT-LISTENER: FORMULAR-ÜBERMITTLUNG
   * Validiert den letzten Schritt vor der Übermittlung
   */
  document.querySelector("form").addEventListener("submit", function (e) {
    if (!validateStep(currentStep)) {
      e.preventDefault(); // Formular-Übermittlung verhindern bei Validierungsfehlern
      return false;
    }
  });
});

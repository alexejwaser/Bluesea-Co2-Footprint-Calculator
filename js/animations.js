/**
 * animations.js – Animation des CO₂-Kreises auf der Ergebnisseite
 *
 * Diese Datei implementiert die animierte Darstellung des CO₂-Fussabdrucks:
 * - Erstellt einen SVG-Kreis über dem statischen CSS-Kreis
 * - Animiert den Kreis-Umriss von 0% auf 100% (stroke-dashoffset Animation)
 * - Verwendet die Bewertungsfarbe aus den CSS-Custom-Properties
 * - Responsive Anpassung an verschiedene Bildschirmgrössen
 * - Gleichzeitige Animation der Hintergrundfarbe
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * INITIALISIERUNG DER KREIS-ANIMATION
   * Sucht nach dem animierbaren Kreis-Element auf der Ergebnisseite
   */
  const circleOuter = document.querySelector(".circle-outer.animate-circle");

  // Nur ausführen wenn das Kreis-Element vorhanden ist (nur auf Ergebnisseite)
  if (circleOuter) {
    /**
     * SCHRITT 1: BEWERTUNGSFARBE AUS CSS AUSLESEN
     * Die Farbe wird basierend auf der CO₂-Bewertung in PHP gesetzt
     */
    const performanceColor = getComputedStyle(circleOuter)
      .getPropertyValue("--performance-color")
      .trim();

    /**
     * SCHRITT 2: RESPONSIVE KREIS-DIMENSIONEN BERECHNEN
     * Passt sich automatisch an CSS-Media-Queries an (300px Desktop, 250px Mobile)
     */
    const circleSize = circleOuter.offsetWidth; // Aktuelle Breite des äusseren Kreises
    const center = circleSize / 2; // Mittelpunkt für SVG-Koordinaten
    const strokeWidth = 4; // Strichstärke des animierten Rands

    // Radius berechnen: Leicht nach innen versetzt um Überlappung zu vermeiden
    // (strokeWidth / 2) würde den Rand genau auf die Kante setzen
    // +1px zusätzlich nach innen um visuelles "Überschiessen" zu verhindern
    const radius = center - (strokeWidth / 2 + 1); // Ergebnis: center - 3 bei strokeWidth 4

    /**
     * SCHRITT 3: SVG-OVERLAY ERSTELLEN
     * Wird über den statischen CSS-Kreis gelegt für die Animation
     */
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("width", circleSize);
    svg.setAttribute("height", circleSize);
    svg.style.position = "absolute"; // Über dem statischen Kreis positionieren
    svg.style.top = "0";
    svg.style.left = "0";
    svg.style.zIndex = "1"; // Über Hintergrund, aber unter innerem Kreis
    svg.style.pointerEvents = "none"; // Maus-Events durchlassen

    /**
     * SCHRITT 4: ANIMIERBAREN KREIS-PFAD ERSTELLEN
     * SVG-Circle-Element mit den berechneten Dimensionen
     */
    const circle = document.createElementNS(
      "http://www.w3.org/2000/svg",
      "circle"
    );
    circle.setAttribute("cx", center); // X-Koordinate des Mittelpunkts
    circle.setAttribute("cy", center); // Y-Koordinate des Mittelpunkts
    circle.setAttribute("r", radius); // Radius des Kreises
    circle.setAttribute("fill", "none"); // Keine Füllung, nur Rand
    circle.setAttribute("stroke", performanceColor); // Farbe des Rands (Bewertungsfarbe)
    circle.setAttribute("stroke-width", strokeWidth.toString()); // Strichstärke
    circle.setAttribute("stroke-linecap", "round"); // Abgerundete Enden

    /**
     * SCHRITT 5: ANIMATION VORBEREITEN
     * Stroke-dasharray Animation: Kreis wird von 0% auf 100% "gezeichnet"
     */
    const circumference = 2 * Math.PI * radius; // Umfang des Kreises berechnen
    circle.style.strokeDasharray = circumference; // Strich-Muster: Ein Strich über den ganzen Umfang
    circle.style.strokeDashoffset = circumference; // Initial: Strich komplett "verschoben" (unsichtbar)
    circle.style.transform = "rotate(-90deg)"; // Rotation: Animation startet oben (12 Uhr)
    circle.style.transformOrigin = `${center}px ${center}px`; // Rotationszentrum
    circle.style.transition = "stroke-dashoffset 2.5s ease-out"; // Sanfte Animation über 2.5 Sekunden

    /**
     * SCHRITT 6: SVG IN DOM EINFÜGEN
     */
    svg.appendChild(circle); // Kreis zum SVG hinzufügen
    circleOuter.appendChild(svg); // SVG zum äusseren Kreis hinzufügen

    /**
     * SCHRITT 7: ANIMATION STARTEN
     * Nach kurzer Verzögerung für besseren visuellen Effekt
     */
    setTimeout(() => {
      // Kreis-Animation: stroke-dashoffset auf 0 setzen = Kreis wird sichtbar "gezeichnet"
      circle.style.strokeDashoffset = "0";
    }, 500); // 500ms Verzögerung

    /**
     * SCHRITT 8: HINTERGRUNDFARBE PARALLEL ANIMIEREN
     * Der statische Kreis ändert gleichzeitig seine Hintergrundfarbe
     */
    setTimeout(() => {
      circleOuter.style.transition = "background-color 2.5s ease-out";
      circleOuter.style.backgroundColor = performanceColor;
    }, 500); // Gleiche Verzögerung wie Kreis-Animation
  }
});

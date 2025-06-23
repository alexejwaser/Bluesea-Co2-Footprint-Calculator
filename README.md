# 🌱 CO₂-Rechner

Ein interaktiver CO₂-Fussabdruck-Rechner, für die fiktive Firma "Bluesea", der dir hilft, deinen persönlichen CO₂-Ausstoss zu berechnen und zu verstehen.

Dies ist mein Abschlussprojekt für das Mediamatik-Modul M307 „PHP”

Ich habe das Projekt im Modul M431 geplant. 
Das Corporate Design und die Broschüre für „Bluesea” habe ich im Modul M274 erstellt.

## ✨ Features

### 📊 Mehrstufiges Formular

- **4 Kategorien**: Wohnen, Mobilität, Lifestyle & Kontakt
- **📈 Fortschrittsanzeige** mit visueller Navigation
- **🔄 Responsive Design** für alle Geräte
- **✅ Client & Server-seitige Validierung**

### 🏠 Wohnen

- 👥 **Haushaltsgrösse** (1-10 Personen)
- 📐 **Wohnfläche** in m²
- 🔥 **Heizungsart** (Gas, Öl, Fernwärme, Wärmepumpe)
- ⚡ **Energieverbrauch** pro Jahr

### 🚗 Mobilität

- 🚙 **Fahrzeugtyp** (Benzin, Diesel, Hybrid, Elektro, kein Auto)
- 🛣️ **PKW-Kilometer** pro Jahr
- 🚌 **Öffentlicher Verkehr** (km/Woche)
- ✈️ **Flugreisen** (Anzahl & Distanz pro Jahr)

### 🥗 Lifestyle

- 🍽️ **Ernährungsweise** (Omnivor, Vegetarisch, Vegan)
- 🥩 **Fleischkonsum** (Portionen/Woche) - _automatisch ausgeblendet bei vegetarisch/vegan_
- 🗑️ **Abfallmenge** pro Woche
- 👕 **Kleidungskonsum** pro Jahr

### 📧 Kontakt

- 📬 **E-Mail** für Ergebnisversendung

## 🔧 Technische Details

### 💻 Backend

- **PHP** für Datenverarbeitung und Validierung
- **Session-Management** für Fehlerbehandlung
- **Wissenschaftliche Emissionsfaktoren** für präzise Berechnungen
- **Schweizer Durchschnittswerte** als Vergleichsbasis

### 🎨 Frontend

- **Responsive CSS** mit modernem Design
- **JavaScript** für interaktive Elemente:
  - `progress.js` - Mehrstufige Navigation
  - `sliders.js` - Range-Slider Funktionalität
  - `validation.js` - Client-seitige Validierung
  - `diet-logic.js` - Bedingte Feldanzeige
  - `animations.js` - UI-Animationen

### 📁 Struktur

```
├── index.php          # Hauptformular
├── process.php         # Datenverarbeitung & CO₂-Berechnung
├── confirmation.php    # Ergebnisseite
├── css/               # Styling
├── js/                # JavaScript-Funktionalität
├── includes/          # PHP-Templates
└── images/            # Assets & Icons
```

## 🚀 Installation

1. **XAMPP** oder ähnlichen PHP-Server starten
2. Dateien in `htdocs` Verzeichnis kopieren
3. `http://localhost/` aufrufen
4. **Fertig!** 🎉

## 🧮 CO₂-Berechnung

Das System verwendet **wissenschaftlich validierte Emissionsfaktoren**:

- 🏠 Heizung: 0.0117-0.266 kg CO₂/kWh
- 🚗 Transport: 0-0.180 kg CO₂/km
- 🍽️ Ernährung: 1300-2100 kg CO₂/Jahr (Basis)
- 🛍️ Konsum: Variable Faktoren für Abfall & Kleidung



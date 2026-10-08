# Smart Dustbin Monitoring System

Web-based monitoring interface (PHP backend + minimalist frontend) for a
smart dustbin that sorts **WET** and **DRY** waste using an Arduino Uno,
HC-SR04 ultrasonic sensor, moisture sensor, SG90 servos and LEDs.

## Folder Structure

```
smart-dustbin/
├── login.php                 Login page (background image + form)
├── landing.php               Public landing page (logo + features, shown first)
├── index.php                 Entry point → redirects to landing.php
├── config/
│   └── database.php           PDO database connection + constants
├── includes/
│   ├── functions.php          Helpers (JSON, settings, csrf, bootstrap_admin)
│   ├── access.php             BASE_URL detection + page metadata
│   ├── auth.php               Session guard (require_login)
│   ├── header.php             Layout top: sidebar + topbar
│   └── footer.php             Layout bottom + live-status bootstrap
├── pages/
│   ├── dashboard.php          LIVE monitoring (status, bins, LEDs, recent)
│   ├── analytics.php          Statistics + charts (today/week/month)
│   ├── history.php            Waste detection records + filters
│   ├── alerts.php             Notifications list
│   └── settings.php           System info, alert thresholds, account
├── api/
│   ├── login.php              POST login (session + token)
│   ├── logout.php             Destroy session
│   ├── dashboard.php          GET live monitoring data
│   ├── analytics.php          GET stats + charts data (?range=today|week|month)
│   ├── history.php            GET records (?range&classification&q)
│   ├── alerts.php             GET list / POST create
│   ├── alerts-marker.php      POST mark alerts read
│   ├── settings.php           GET info / POST thresholds
│   └── arduino/
│       └── data.php           POST hardware ingestion (device key)
├── assets/
│   ├── css/style.css          Minimalist design system
│   └── js/app.js              Frontend logic (fetch, charts, polling)
├── database/
│   └── database.sql           Schema + seed data (phpMyAdmin import)
└── hardware/
    └── smart_dustbin.ino      Arduino Uno sketch
```

## Requirements

- PHP 7.4+ with PDO MySQL extension
- MySQL / MariaDB
- XAMPP / WAMP / LAMP recommended

## Setup

1. **Create the database**
   - Open phpMyAdmin and import `database/database.sql`, **or**
   - Run `mysql -u root -p < database/database.sql`

2. **Configure the database connection**
   - Edit `config/database.php` and set your `DB_USER` / `DB_PASS`.

3. **Serve the project**
- Copy the folder into `htdocs` (XAMPP). A junction already links
      `C:\xampp\htdocs\IT-114-PROJECT` to this project folder, so open
      **`http://localhost/IT-114-PROJECT/`** (redirects to the landing page, then `login.php`)
   - The default **admin** account is created automatically on first login attempt:
     - Username: `admin`
     - Password: `admin123`

4. **Connect the hardware** (optional)
   - Upload `hardware/smart_dustbin.ino` to the Arduino.
   - Point the sketch at your server (`HOST`) and make sure
     `DEVICE_KEY` in the sketch matches `DEVICE_KEY` in `api/arduino/data.php`.
   - The Arduino posts: classification, detection, bin capacities and LED state
     to `api/arduino/data.php`.

## Simulating Hardware (No Arduino)

Until the device is connected, post manual readings so the dashboard updates:

```bash
curl -G "http://localhost/smart-dustbin/api/arduino/data.php" \
  --data-urlencode "key=SMARTDUSTBIN2026" \
  --data-urlencode "classification=WET" \
  --data-urlencode "detected_item=banana peel" \
  --data-urlencode "wet_capacity=45" \
  --data-urlencode "dry_capacity=32" \
  --data-urlencode "green_led=off" \
  --data-urlencode "yellow_led=on"
```

The dashboard auto-refreshes every 5 seconds.

## Alert Thresholds

| Level      | Range    | Behavior                              |
|------------|----------|----------------------------------------|
| Normal     | 0–79%    | Green indicator                        |
| Warning    | 80–89%   | Yellow indicator + warning alert       |
| Critical   | 90–100%  | Red indicator + critical alert         |

Thresholds are adjustable in **Settings**.
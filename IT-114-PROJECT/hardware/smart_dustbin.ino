/*
 * Smart Dustbin — Arduino/ESP8266 sketch
 *
 * Reads HC-SR04 (presence/capacity) + moisture sensor (wet/dry),
 * rotates SG90 servo to sort waste, lights LEDs, then reports to
 * the PHP backend via the communication module (ESP8266 or HC-05).
 *
 * Connected to:  api/arduino/data.php
 * Auth key:      must match DEVICE_KEY in api/arduino/data.php
 */

#include <SoftwareSerial.h>

// Pin assignments (Arduino Uno)
const int TRIG = 9;
const int ECHO = 10;
const int MOISTURE = A0;
const int SERVO_WET = 5;    // SG90 wet bin
const int SERVO_DRY = 6;    // SG90 dry bin
const int LED_GREEN = 7;    // dry waste indicator
const int LED_YELLOW = 8;   // wet waste indicator

// Communication module (HC-06/ESP8266) — adjust ports
SoftwareSerial comm(2, 3);  // RX, TX

// Server settings for ESP8266 mode
const char HOST[] = "your-php-server.local";  // e.g. 192.168.1.100
const char PATH[] = "/smart-dustbin/api/arduino/data.php";
const char DEVICE_KEY[] = "SMARTDUSTBIN2026"; // must match data.php

// Capacity send interval (ms) even without new detection
const unsigned long REPORT_INTERVAL = 30000;

int wetLevel = 0;
int dryLevel = 0;

void setup() {
  Serial.begin(9600);
  comm.begin(9600);
  pinMode(TRIG, OUTPUT);
  pinMode(ECHO, INPUT);
  pinMode(SERVO_WET, OUTPUT);
  pinMode(SERVO_DRY, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_YELLOW, OUTPUT);
}

long readDistance() {
  digitalWrite(TRIG, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG, LOW);
  long us = pulseIn(ECHO, HIGH);
  return us / 58;  // distance in cm
}

void loop() {
  static unsigned long lastReport = 0;
  static unsigned long lastCycle = 0;

  long dist = readDistance();

  // Waste detected when object is closer than 30 cm
  if (dist > 0 && dist < 30 && millis() - lastCycle > 2000) {
    lastCycle = millis();

    int moisture = analogRead(MOISTURE);          // 0 = dry, ~1023 = wet
    boolean isWet = moisture > 500;

    if (isWet) {
      digitalWrite(LED_YELLOW, HIGH);
      digitalWrite(LED_GREEN, LOW);
      sweepServo(SERVO_WET);          // route to wet bin
      wetLevel = min(100, wetLevel + 5);
    } else {
      digitalWrite(LED_GREEN, HIGH);
      digitalWrite(LED_YELLOW, LOW);
      sweepServo(SERVO_DRY);          // route to dry bin
      dryLevel = min(100, dryLevel + 5);
    }

    sendData(isWet ? "WET" : "DRY", isWet ? "" : "");
    delay(500);
  }

  // Periodic capacity report
  if (millis() - lastReport > REPORT_INTERVAL) {
    lastReport = millis();
    sendData("", "");
  }
}

void sweepServo(int pin) {
  for (int a = 0; a <= 90; a++) {
    setServo(pin, a); delay(10);
  }
  delay(400);
  for (int a = 90; a >= 0; a--) {
    setServo(pin, a); delay(10);
  }
}

void setServo(int pin, int angle) {
  int pulse = map(angle, 0, 180, 500, 2500);
  digitalWrite(pin, HIGH);
  delayMicroseconds(pulse);
  digitalWrite(pin, LOW);
  delayMicroseconds(20000 - pulse);
}

void sendData(String classification, String item) {
  // Generic URL (works on ESP8266 via AT+HTTP or a host library).
  String url = String("GET ") + PATH
               + "?key=" + DEVICE_KEY
               + "&classification=" + classification
               + "&detected_item=" + item
               + "&wet_capacity=" + wetLevel
               + "&dry_capacity=" + dryLevel
               + "&green_led=" + (digitalRead(LED_GREEN) ? "on" : "off")
               + "&yellow_led=" + (digitalRead(LED_YELLOW) ? "on" : "off")
               + " HTTP/1.1\r\nHost: " + HOST + "\r\n\r\n";

  Serial.println("Sending: " + url);
  comm.println(url);   // forward to communication module
}
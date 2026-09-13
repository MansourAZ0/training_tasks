# ESP32 LED and servo web controls

This is the week 6 web-track task. The sketch starts an HTTP server on the ESP32 and serves a control page with two APIs:

- `GET /api/led?state=on|off` switches the LED on GPIO 2 by default.
- `GET /api/servo?angle=0..180` moves a hobby servo on GPIO 13 by default.
- `GET /api/state` returns both current values so the page can recover after refresh.

The HTML, CSS, and JavaScript are embedded in the sketch, so no separate filesystem upload is required. If the configured Wi-Fi is unavailable, the board starts a temporary access point named `ESP32-Control` with password `esp32demo`.

## Setup

1. Install the ESP32 Arduino board package and the `ESP32Servo` library.
2. Set `WIFI_SSID` and `WIFI_PASSWORD` in [`esp32-led-servo.ino`](esp32-led-servo.ino).
3. Connect the servo signal to GPIO 13, power to a suitable 5 V supply, and ground to ESP32 GND. Connect an LED with a 220 Ω resistor to GPIO 2 and GND, unless using the board's built-in LED.
4. Select your board, upload, and open the IP printed in Serial Monitor at 115200 baud.

Power the servo from an external supply when possible. Share ground with the ESP32, and never power a large servo from an ESP32 GPIO pin.

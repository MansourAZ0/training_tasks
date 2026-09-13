/* Week 6 web-track task: ESP32 web controls for an LED and servo. */
#include <WiFi.h>
#include <WebServer.h>
#include <ESP32Servo.h>

const char* WIFI_SSID = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";
constexpr uint8_t LED_PIN = 2;
constexpr uint8_t SERVO_PIN = 13;
WebServer server(80);
Servo servo;
bool ledOn = false;
int servoAngle = 90;

const char PAGE[] PROGMEM = R"HTML(<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ESP32 controls</title><style>
:root{color-scheme:dark;--bg:#0b1020;--panel:#151e36;--line:#33415f;--text:#f8fafc;--muted:#a8b3cf;--accent:#67e8f9;--danger:#fb7185}
*{box-sizing:border-box}body{margin:0;min-height:100vh;background:radial-gradient(circle at 10% 0,#172554,transparent 42%),var(--bg);color:var(--text);font:16px/1.5 system-ui,sans-serif}
main{width:min(680px,calc(100% - 2rem));margin:auto;padding:3.5rem 0}.eyebrow{color:var(--accent);font-size:.75rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}h1{font-size:clamp(2.5rem,8vw,4.5rem);line-height:1;margin:.4rem 0 .8rem;letter-spacing:-.06em}p{color:var(--muted)}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem}.card{padding:1.25rem;border:1px solid var(--line);border-radius:22px;background:var(--panel)}h2{margin:.1rem 0 1rem;font-size:1.1rem}.state{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;color:var(--muted)}.value{color:var(--text);font-weight:700}.buttons{display:flex;gap:.6rem}.btn{flex:1;padding:.75rem;border:1px solid var(--line);border-radius:12px;background:#1f2d4d;color:var(--text);font:inherit;font-weight:700;cursor:pointer}.btn:hover,.btn:focus-visible{border-color:var(--accent);outline:none}.btn.on{background:var(--accent);color:var(--bg)}.btn.stop{background:#451c2a;border-color:var(--danger)}input[type=range]{width:100%;accent-color:var(--accent)}#message{min-height:2rem;margin-top:1rem;padding:.7rem 1rem;border:1px solid var(--line);border-radius:12px;color:var(--muted)}@media(max-width:520px){.grid{grid-template-columns:1fr}}
</style></head><body><main><div class="eyebrow">ESP32 web server · week 6</div><h1>Hardware controls</h1><p>Switch the LED and set the servo angle over your local network.</p>
<div class="grid"><section class="card"><h2>LED</h2><div class="state"><span>Output</span><span id="ledState" class="value">Loading…</span></div><div class="buttons"><button class="btn" data-led="on">On</button><button class="btn stop" data-led="off">Off</button></div></section>
<section class="card"><h2>Servo</h2><div class="state"><span>Angle</span><span><b id="angleValue" class="value">90</b>°</span></div><input id="angle" type="range" min="0" max="180" value="90" aria-label="Servo angle"><div class="buttons"><button id="center" class="btn">Center at 90°</button></div></section></div><div id="message" role="status" aria-live="polite">Connecting…</div></main>
<script>const $=s=>document.querySelector(s),msg=$('#message'),angle=$('#angle');function report(t,e=false){msg.textContent=t;msg.style.color=e?'var(--danger)':'var(--muted)'}async function call(path){const r=await fetch(path);const d=await r.json();if(!r.ok||d.status!=='ok')throw Error(d.message||'Request failed');return d}async function setLed(s){try{const d=await call('/api/led?state='+s);$('#ledState').textContent=d.led?'On':'Off';document.querySelectorAll('[data-led]').forEach(b=>b.classList.toggle('on',b.dataset.led===s));report('LED updated')}catch(e){report(e.message,true)}}async function setServo(v){angle.value=v;$('#angleValue').textContent=v;try{await call('/api/servo?angle='+v);report('Servo set to '+v+'°')}catch(e){report(e.message,true)}}document.querySelectorAll('[data-led]').forEach(b=>b.addEventListener('click',()=>setLed(b.dataset.led)));angle.addEventListener('change',()=>setServo(angle.value));$('#center').addEventListener('click',()=>setServo(90));call('/api/state').then(d=>{setLed(d.led?'on':'off');angle.value=d.angle;$('#angleValue').textContent=d.angle;report('Connected to ESP32')}).catch(e=>report(e.message,true));</script></body></html>)HTML";

void sendJson(int code, const String& body) { server.send(code, "application/json; charset=utf-8", body); }
void handleState() { sendJson(200, String("{\"status\":\"ok\",\"led\":") + (ledOn ? "true" : "false") + ",\"angle\":" + servoAngle + "}"); }
void handleLed() {
  const String state = server.arg("state");
  if (state != "on" && state != "off") { sendJson(422, R"({"status":"error","message":"state must be on or off"})"); return; }
  ledOn = state == "on"; digitalWrite(LED_PIN, ledOn ? HIGH : LOW);
  sendJson(200, String("{\"status\":\"ok\",\"led\":") + (ledOn ? "true" : "false") + "}");
}
void handleServo() {
  if (!server.hasArg("angle")) { sendJson(422, R"({"status":"error","message":"angle is required"})"); return; }
  const int requested = server.arg("angle").toInt();
  if (requested < 0 || requested > 180) { sendJson(422, R"({"status":"error","message":"angle must be between 0 and 180"})"); return; }
  servoAngle = requested; servo.write(servoAngle);
  sendJson(200, String("{\"status\":\"ok\",\"angle\":") + servoAngle + "}");
}
void setup() {
  Serial.begin(115200); pinMode(LED_PIN, OUTPUT); digitalWrite(LED_PIN, LOW);
  servo.setPeriodHertz(50); servo.attach(SERVO_PIN, 500, 2400); servo.write(servoAngle);
  WiFi.mode(WIFI_STA); WiFi.begin(WIFI_SSID, WIFI_PASSWORD); Serial.print("Connecting to Wi-Fi");
  for (int attempt = 0; WiFi.status() != WL_CONNECTED && attempt < 30; ++attempt) { delay(500); Serial.print('.'); }
  if (WiFi.status() == WL_CONNECTED) Serial.println("\nOpen http://" + WiFi.localIP().toString());
  else { WiFi.mode(WIFI_AP); WiFi.softAP("ESP32-Control", "esp32demo"); Serial.println("\nConnect to ESP32-Control, then open http://" + WiFi.softAPIP().toString()); }
  server.on("/", HTTP_GET, [] { server.send_P(200, "text/html; charset=utf-8", PAGE); });
  server.on("/api/state", HTTP_GET, handleState); server.on("/api/led", HTTP_GET, handleLed); server.on("/api/servo", HTTP_GET, handleServo);
  server.onNotFound([] { sendJson(404, R"({"status":"error","message":"Not found"})"); }); server.begin();
}
void loop() { server.handleClient(); }

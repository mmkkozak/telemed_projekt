#ifdef ESP8266
 #include <ESP8266WiFi.h>
 #else
  #include <WiFi.h>
#endif

#include <WiFiUdp.h>
#include <NTPClient.h>
#include "DHTesp.h"
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>

#include "Credentials.h" // Change info in credentials for your use

#define DHTpin 2 // Pin where the DHT11 sensor should be connected
DHTesp dht;

const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWORD;

// MQTT broker details (user is necessary in our case)
const char* mqtt_server = SERVER;
const char* mqtt_username = USER;
const char* mqtt_password = USER_PSSWD;
const int mqtt_port = PORT;

// Secure WIFI Connection
WiFiClientSecure espClient;
// Defining NTP server
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP);
// MQTT initialization using WiFi connection
PubSubClient client(espClient);

unsigned long lastMsg = 0;
#define MSG_BUFFER_SIZE (50)
char msg[MSG_BUFFER_SIZE];

// Connecting to WiFi
void setup_wifi(){
  delay(10);
  Serial.println("\nConnecting to ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  randomSeed(micros());
  Serial.println("\nWiFi connected\nIP address: ");
  Serial.println(WiFi.localIP());

  timeClient.begin();
  timeClient.setTimeOffset(7200); // 7200 will be for polish timezone
}

// Connecting to MQTT broker 
void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    String clientId = "ESP8266Client";   // Creating random client IP for this device
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if(client.connect(clientId.c_str(), mqtt_username, mqtt_password)) {
    
    Serial.println("Connected.");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");   // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}
void publishMessage(const char* topic, String payload , boolean retained){
  if (client.publish(topic, payload.c_str(), true))
      Serial.println("Message publised ["+String(topic)+"]: "+payload);
}

void setup() {

  dht.setup(DHTpin, DHTesp::DHT11); //Set up DHT11 sensor
  Serial.begin(115200);
  while (!Serial) delay(1);
  setup_wifi();

  #ifdef ESP8266
    espClient.setInsecure(); 
  #endif
  client.setServer(mqtt_server, mqtt_port);
}

void loop() {

  if (!client.connected()) {
    Serial.println(WiFi.status(), WiFi.localIP());
    reconnect();} // check if client is connected
  client.loop();

  delay(dht.getMinimumSamplingPeriod());
  float humidity = dht.getHumidity();
  float temperature = dht.getTemperature();

  while(!timeClient.update()){
    timeClient.forceUpdate();
  }

  String date = timeClient.getFormattedDate();
  // extracting date
  int splitT = date.indexOf("T");
  String dayStamp = date.substring(0, splitT);

  String timeStamp = date.substring(splitT+1, date.length()-1);

  String datetime = dayStamp + " " + timeStamp;
  
  DynamicJsonDocument doc(1024);

  doc["deviceId"] = "nodemcu";
  doc["humidity"] = 31;
  doc["temperature"] = 25.07;
  doc["timestamp"] = datetime;

  char mqtt_message[128];
  serializeJson(doc, mqtt_message);

  publishMessage("dht_data", mqtt_message, true);

  delay(10000);
}
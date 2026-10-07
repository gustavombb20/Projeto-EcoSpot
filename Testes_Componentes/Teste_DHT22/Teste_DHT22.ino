#include <DHT.h>

#define DHT_PIN  18
#define DHT_TYPE DHT22

DHT dht(DHT_PIN, DHT_TYPE);

void setup() {
  Serial.begin(115200);
  dht.begin();

  Serial.println();
  Serial.println("Teste do DHT22");
  Serial.println("Aguardando primeira leitura...");
}

void loop() {
  delay(2000);

  float umidade = dht.readHumidity();
  float temperatura = dht.readTemperature(); // Celsius

  if (isnan(umidade) || isnan(temperatura)) {
    Serial.println(
      "Falha na leitura! Confira a ligacao, "
      "a alimentacao e o resistor de pull-up."
    );
    return;
  }

  Serial.print("Temperatura: ");
  Serial.print(temperatura, 1);
  Serial.print(" °C  |  Umidade: ");
  Serial.print(umidade, 1);
  Serial.println(" %");
}

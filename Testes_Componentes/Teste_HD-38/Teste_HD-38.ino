#define PINO_SOLO 34
#define AMOSTRAS 20

void setup() {
  Serial.begin(115200);
  delay(1000);

  pinMode(PINO_SOLO, INPUT);

  analogReadResolution(12);
  analogSetPinAttenuation(PINO_SOLO, ADC_11db);

  Serial.println();
  Serial.println("Teste do sensor de solo HD-38");
  Serial.println("Compare as leituras em solo seco e umido.");
  Serial.println("ADC bruto: 0 a 4095; nao e porcentagem.");
}

void loop() {
  unsigned long soma = 0;

  for (int i = 0; i < AMOSTRAS; i++) {
    soma += analogRead(PINO_SOLO);
    delay(5);
  }

  int leitura = soma / AMOSTRAS;

  Serial.print("Umidade do solo - ADC bruto: ");
  Serial.println(leitura);

  delay(900);
}

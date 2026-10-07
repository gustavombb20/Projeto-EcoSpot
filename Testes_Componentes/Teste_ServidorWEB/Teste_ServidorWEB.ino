#include <WiFi.h>
#include <WebServer.h>

const char* ssid = "Genevro";
const char* password = "12345678";

WebServer server(80);

bool estavaConectado = false;
unsigned long ultimoAviso = 0;

// HTML separado do codigo, sem precisar escapar as aspas.
const char paginaHTML[] = R"HTML(
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ESP32</title>
</head>
<body>
  <h1>Hello World</h1>
</body>
</html>
)HTML";

void paginaInicial() {
  server.send(200, "text/html; charset=utf-8", paginaHTML);
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  server.on("/", HTTP_GET, paginaInicial);

  server.onNotFound([]() {
    server.send(
      404,
      "text/plain; charset=utf-8",
      "Pagina nao encontrada"
    );
  });

  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.begin(ssid, password);

  Serial.println();
  Serial.print("Conectando ao Wi-Fi: ");
  Serial.println(ssid);

  ultimoAviso = millis();
}

void loop() {
  bool conectado = (WiFi.status() == WL_CONNECTED);

  if (conectado) {
    if (!estavaConectado) {
      server.begin();

      Serial.println("\nWi-Fi conectado!");
      Serial.print("Abra no navegador: http://");
      Serial.print(WiFi.localIP());
      Serial.println("/");
    }

    server.handleClient();
  } else {
    if (estavaConectado) {
      server.stop();
      Serial.println("Wi-Fi desconectado.");
      ultimoAviso = millis();
    }

    // Deixa a biblioteca cuidar da reconexao automatica.
    if (millis() - ultimoAviso >= 10000UL) {
      ultimoAviso = millis();
      Serial.println("Aguardando conexao ao Wi-Fi...");
    }
  }

  estavaConectado = conectado;
  delay(2);
}

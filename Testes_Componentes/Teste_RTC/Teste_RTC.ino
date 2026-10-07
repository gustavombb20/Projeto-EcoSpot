
#include <Wire.h>
#include <RtcDS3231.h>

RtcDS3231<TwoWire> rtc(Wire);

const bool AJUSTAR_HORA = false;

void pararComErro(const char* mensagem) {
  Serial.println(mensagem);

  while (true) {
    delay(1000);
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("\nTeste DS3231 - biblioteca Makuna");

  rtc.Begin(21, 22);
  Wire.beginTransmission(0x68);

  if (Wire.endTransmission() != 0) {
    pararComErro(
      "RTC nao encontrado."
    );
  }

  bool horaValida = rtc.IsDateTimeValid();

  if (rtc.LastError() != 0) {
    pararComErro("Erro ao consultar o RTC.");
  }

  if (!horaValida || AJUSTAR_HORA) {
    rtc.SetDateTime(RtcDateTime(__DATE__, __TIME__));

    if (rtc.LastError() != 0) {
      pararComErro("Erro ao ajustar data e hora.");
    }

    Serial.println("Hora ajustada para o momento da compilacao.");
  }

  bool funcionando = rtc.GetIsRunning();

  if (rtc.LastError() != 0) {
    pararComErro("Erro ao consultar o funcionamento do RTC.");
  }

  if (!funcionando) {
    rtc.SetIsRunning(true);

    if (rtc.LastError() != 0) {
      pararComErro("Erro ao iniciar o relogio.");
    }
  }

  Serial.println("RTC pronto!");
}

void loop() {
  RtcDateTime agora = rtc.GetDateTime();

  if (rtc.LastError() != 0) {
    Serial.println("Falha de comunicacao com o RTC.");
    delay(1000);
    return;
  }

  char texto[32];

  snprintf(
    texto,
    sizeof(texto),
    "%02u/%02u/%04u  %02u:%02u:%02u",
    (unsigned)agora.Day(),
    (unsigned)agora.Month(),
    (unsigned)agora.Year(),
    (unsigned)agora.Hour(),
    (unsigned)agora.Minute(),
    (unsigned)agora.Second()
  );

  Serial.println(texto);
  delay(1000);
}

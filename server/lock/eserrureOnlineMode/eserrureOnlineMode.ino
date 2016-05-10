#include <SD.h>
#include <SPI.h>
#include <Wire.h>
#include <WiFi.h>
#include <TextFinder.h>
#include <Adafruit_NFCShield_I2C.h>

/*************************************************
*  Define the port number for the Leds and buzzer*
**************************************************/
#define LED_GREEN   6
#define LED_RED     5
#define BUZZER      9
/**************************************
*  RFID/NFC variables                 *
**************************************/
#define IRQ   (2)
#define RESET (3)  // Not connected by default on the NFC Shield
uint8_t RFIDKey[] = { 0, 0, 0, 0, 0, 0, 0 };  // Buffer to store the returned UID
uint8_t RFIDKeyLength; 
Adafruit_NFCShield_I2C nfc(IRQ, RESET);

/**************************************
* WIFI variables                      *
**************************************/
//char ssid[] = "NASA_WIFI";                //your network SSID (name) 
//char password[] = "peaceintheworld";      //your network password (use for WPA, or use as key for WEP)
char ssid[] = "arduino";  
IPAddress server(192,168,43,40); 
//char server[] = "abdrassoul.issa.free.fr";// name address for server (using DNS)
WiFiClient client;
TextFinder finder(client, 1); // Use to retrieve data when the server send a response
 
/**************************************
* SD card                             *
**************************************/
File file; 

/**************************************
* Shared                              *
**************************************/
int i = 0;
char inChar; // Hold the incoming byte from different sources (RFID, SDCard, etc.) 
/**************************************
* Application status                  *
**************************************/
int readyToSend = 0;
int status = WL_IDLE_STATUS;
unsigned long lastConnectionTime = 0;

/*************************************
 *  Application activation part.     *
 *************************************/
 #define        activeDebug;   // You can active debug on the serial
  
/***********************************************
 *  For debug purpose                          *
************************************************/
 
#ifdef activeDebug
   #define        cleanDebug(str)  Serial.println(F(str))   
   #define        debugLn(str)     Serial.println(str)
   #define        debug(str)       Serial.print(str)
#else
   #define        cleanDebug   
   #define        debugLn
   #define        debug
#endif

/***********************************************
* Initialisation of the application            *
***********************************************/

void setup(void) {
  
  //Init the connection to the PC 
  Serial.begin(115200);
  
  //Start of the WIFI connection 
  initialiseWIFIConnection();
  
  //Initialize NFC connection
  initialiseNFCConnection();
  
  //Configuring components port
  pinMode(LED_GREEN, OUTPUT);        
  pinMode(LED_RED, OUTPUT);
  pinMode(BUZZER, OUTPUT); 
}

void loop(void) {
 
 if (readyToSend == 1)           
 {   
    // Connect to server. If the connection was successful    
    httpRequest();
    
    // Process the response of the server 
    if (client.connected())                                                    
    {
        // Wait the response from the server
        while(client.connected() && !client.available()){ 
          delay(10);
        }
        //Go to the http code position
        finder.find("HTTP/1.1 ");
        // If the http code is 200, open the door
        if(finder.findUntil("200", " ")) {
          accessAllowed();
          cleanDebug("accessAllowed");
        } 
        // Else keep it close
        else{
           accessDenied();
           cleanDebug("accessDenied");
        }
    }
 }
 else{
   readNFCKey();
 }

}


void initialiseNFCConnection(){
  nfc.begin();
  uint32_t versiondata = nfc.getFirmwareVersion();
  if (! versiondata) {
    cleanDebug("Didn't find PN53x board");
    return; // halt
  }
  // configure board to read RFID tags
  nfc.SAMConfig();
  cleanDebug("The electronic board is ready ...");
}

void initialiseWIFIConnection(){
  
 // attempt to connect to Wifi network:
  while (status != WL_CONNECTED) { 
    cleanDebug("\nAttempting to connect to SSID:  ");
    Serial.print(ssid);
    // Connect to WPA/WPA2 network. Change this line if using open or WEP network:    
    //status = WiFi.begin(ssid, password);
    status = WiFi.begin(ssid);
    // wait 10 seconds for connection:
    delay(10000);
    cleanDebug("\nHere...");
  } 
  cleanDebug("Connected to wifi");
  printWifiStatus(); 
}


void readNFCKey(){
  uint8_t success;    
  success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, RFIDKey, &RFIDKeyLength);   
  if (success) {
    // Display some basic information about the card
    Serial.print("\n\tRFIDKey Value: ");
    //nfc.PrintHex(RFIDKey, RFIDKeyLength);
    printRFIDKey();
    readyToSend = 1;
  }
}

void printRFIDKey(){
   Serial.print("\tRFIDKey Value:  ");  
   for (int i = 0; i < RFIDKeyLength; i++) {
     Serial.print(RFIDKey[i]);
   }
}


void accessAllowed(){
  // Open the door with the electrical strike  
  digitalWrite(LED_GREEN, HIGH); 
  // Display a message in the serial port        
  cleanDebug("Unlock"); 
  // Do a long bip
  
  for(i=0; i<50; i++){
    digitalWrite(BUZZER, HIGH);
    delay(10);
    digitalWrite(BUZZER, LOW);
    delay(10);
  }
  // Delay of 2 secondes          
  delay(1000);
  // Reset the application
  resetProcess();
}
  

  
void accessDenied(){
  // Power on the red led
  digitalWrite(LED_RED, HIGH);
  cleanDebug("\nKeep lock");
 
  tone(BUZZER, 196, 2000);
  delay(2600);
  noTone(BUZZER);
   
  // Delay of 2 secondes            
  delay(1000);
  // Reset the application
  resetProcess();
}
  
  
void resetProcess(){ 
  readyToSend = 0;
  file.close();
  inChar = 0;
  // Remove the server connection
  //client.stop();
  // Adding a delay
  for(i = 0;i < RFIDKeyLength;i++){
    RFIDKey[i] = 0;
  }
  
  delay(1000);        
  // Power of the leds    
  digitalWrite(LED_RED, LOW);                                                 
  digitalWrite(LED_GREEN, LOW);  
  Serial.print("\nReset OK");
}


// this method makes a HTTP connection to the server:
void httpRequest() {
  // close any connection before send a new request.
  // This will free the socket on the WiFi shield
  client.stop();

  // if there's a successful connection:
  if (client.connect(server, 80)) {
    Serial.println("\nconnecting...");
    // send the HTTP PUT request: 
    // Host configuration for the query    
    client.print("GET /index.php?key="); 
   // Retrieve the values of the key in is buffer and write it in the client      
    for(i = 0; i < RFIDKeyLength; i++){  
        client.print(RFIDKey[i]);   
    }
    client.println(" HTTP/1.1"); 
    client.print("Host: "); 
    client.println(server); 
    client.println("User-Agent: ArduinoWiFi/1.1");
    client.println("Connection: close");
    client.println();

    // note the time that the connection was made:
    lastConnectionTime = millis();
    Serial.println("\nconnected...");
  }
  else {
    // if you couldn't make a connection:
    Serial.println("connection failed");
  }
}

void printWifiStatus() {
  // print the SSID of the network you're attached to:
  Serial.println("SSID:\t");
  Serial.println(WiFi.SSID());
  // print your WiFi shield's IP address:
  IPAddress ip = WiFi.localIP();
  Serial.print("\nWiFi shield's IP address: ");
  Serial.println(ip);
}


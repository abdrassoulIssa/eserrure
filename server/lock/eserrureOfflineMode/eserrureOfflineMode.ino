#include <SD.h>
#include <SPI.h>
#include <Wire.h>
#include <TextFinder.h>
#include <Adafruit_NFCShield_I2C.h>

/**************************************
*  Define the port number for the Leds and buzzer*
**************************************/
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
  //Initialize NFC connection
  initialiseNFCConnection();
  //Configuring components port
  pinMode(LED_GREEN, OUTPUT);        
  pinMode(LED_RED, OUTPUT);
  pinMode(BUZZER, OUTPUT); 
  // Initialisation of the SD Card
  initialiseMICROSDConnection();
}

void loop(void) {
 
 if (readyToSend == 1)           
 {   
    // Pass in the offline mode if host is not reachable
    offlineMode();
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

void initialiseMICROSDConnection(){
  pinMode(10, OUTPUT); // laisser la broche SS en sortie - obligatoire avec librairie SD
  if (!SD.begin(4)) {                                         
    cleanDebug("Micro SD card : initialization failed!");
  }
  else cleanDebug("Micro SD card : initialization done.");
}


void offlineMode(){
  
  cleanDebug("\nOffline Mode");
  // Open the key file  
  int compteur = 0;
  while(SD.exists("keys.txt") == false && compteur < 3) {
     compteur++;
     delay(100);
   }
   
   if(file = SD.open("keys.txt")){
        Serial.println("File is opened");
        //Create a text finder for the file with a 1 seconde timeout
        TextFinder finderSD(file, 1);   
        // Find the key and is permission     
         if (file) {
          // If the key is found 
          char rfidKeys[256];
          String buffer= String("");
          for(i = 0; i < RFIDKeyLength;i++){  
              char tmp[256];     
              String str = String(RFIDKey[i]);
              str.toCharArray(tmp,256);
              buffer = buffer + String(tmp);        
          }
          
          buffer.toCharArray(rfidKeys,256);
       
          if (finderSD.find(rfidKeys)){
              // Get the permission number (the file.read() is use to jump the separator)
              // Key format in the keys file : IDKEY PermissionNumber. Exemple : 02005159B6C5 2
              file.read(); 
              inChar = file.peek();
              
              // Test the permission and open, or not, the door
              char *response;
              String(inChar).toCharArray(response,256);
             
              Serial.print("Authentification :");
              if (inChar != '1'){
                Serial.println(inChar);
                accessDenied();  
              }
              else{
                Serial.println(inChar);
                accessAllowed();
              }
              // Close the file
              file.close();
          }
          else
          {
            accessDenied();
          }
        }
        else
        {
          accessDenied();
        }
   }
}




void readNFCKey(){
  uint8_t success;    
  success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, RFIDKey, &RFIDKeyLength);   
  if (success) {
    // Display some basic information about the card
    Serial.print("\n\tRFIDKey Value: ");
    nfc.PrintHex(RFIDKey, RFIDKeyLength);
    Serial.print("\tRFIDKey Value:  ");     
    for (int i = 0; i < RFIDKeyLength; i++) {
      Serial.print(RFIDKey[i]);
    }
    readyToSend = 1;
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
  for(i = 0;i < RFIDKeyLength;i++){
    RFIDKey[i] = 0;
  }
  // Adding a delay
  delay(1000);        
  // Power of the leds    
  digitalWrite(LED_RED, LOW);                                                 
  digitalWrite(LED_GREEN, LOW);  
  Serial.print("\nReset OK");
}



# eserrure
This is an interface of Monitoring for an electronic lock connected to an authenticating web server.

La serrure fonctionne avec un système de lecture de badge NFC/RFID. Les utilisateurs présentent leur badge  
personnel sur l’antenne du lecteur. La serrure envoie l’identifiant du badge à un serveur web et ouvre la porte  
suivant la réponse de celui-ci. Les droits d’accès des utilisateurs sont g ́er ́es dans une base de données 
que l’on  peut administrer par une interface web.

Pour cette application, j'ai utilisé:
--> Un Arduino Uno
--> Un Arduino Wireless SD Shield
--> Un shield Adafruit PN532 NFC/RFID

Pour authentifier une clé grâce au serveur, la serrure effectue une requête de type GET et place 
la valeur de  la clé qu’elle lit dans un paramètre. Exemple : www.monsite.com/index.php?key=20122565 
Le script  se trouve dans index.php à la racine du site et s’occupe d’authentifier les clés placées 
dans le paramètre "key"  de l’URL. Après avoir vérifié l’existence de la clé dans la base de 
données et de la permission si elle avait  été  trouvée, le serveur retourne un code http 200 si la 
clé est autorisée à ouvrir la porte ou un code 401 si la  porte doit rester fermée.

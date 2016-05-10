
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Refresh" content="n">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Serrure électronique avec un serveur d'authentification et d'administration">
    <meta name="author" content="ESERRURE ZEUS TEAM">
    <title>Eserrure</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="admin/css/bootstrap.min.css">
    <link href="admin/css/starter-template.css" rel="stylesheet">
    <link rel="icon" href="../../favicon.ico">
</head>


  <body>
    <div class="container">
        <!--<?php  //include(dirname(__FILE__).'/navbar.php'); ?> -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="index.php">ESERRURE PROJECT</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                  <ul class="nav navbar-nav">
                    <li><a href="admin/index.php">Administration</a></li>
                  </ul>
                </div>
          </div>
        </nav>



        <div class="starter-template">
             <div class="jumbotron">
                    <p>This is an interface of Monitoring  for an electronic lock connected to an authenticating web server.</p>
                    <p> Eserrure is a project of a electronical lock connected to the Internet. 
                    The objective of the project was to provide source codes and manufacture plans of the lock.</p>
                    <h2>How does it work </h2>
                    Eserrure  operates with a RFID/NFC reading system. Users put their own badge on the antenna of the reader. The lock sends the identifier of the key to a web server, and opens the door following the response. The access rights of users are managed in a database that can be administered through a web interface.
This project was carried out with an Arduino Uno and  Wifi Shield, but it can be reproduced with an Ethernet Shield  making  changes to the source code. Arduino products were chosen because they are open-source software.</p>

             </div>
        </div>
    </div> 

        <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="admin/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="admin/admin/js/ie10-viewport-bug-workaround.js"></script>
  </body>

</html>

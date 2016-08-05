<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title>Teletext - Web-infusion</title>
    <style>
      @font-face {
          font-family: volterGoldfish;
          src: url(./fonts/volterGoldfish.ttf);
      }

      @-moz-keyframes blurry {from { -webkit-filter: blur(0px); }70% { -webkit-filter: blur(1px); }to { -webkit-filter: blur(0.5px);}}
      @-webkit-keyframes blurry {from { -webkit-filter: blur(0px); }70% { -webkit-filter: blur(1px); }to { -webkit-filter: blur(0.5px);}}
      @keyframes blurry {from { -webkit-filter: blur(0px); }70% { -webkit-filter: blur(1px); }to { -webkit-filter: blur(0.5px);}}

      * {
        box-sizing: border-box;
      }
      body {
        padding: 0;
        margin: 0;
        background: #000;
        text-align: center;
        font-family: volterGoldfish;
        color: #FFF;
      }
      #videoBg {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        object-fit: cover;
      }
      #wrapWrapper {
        background: #000000;
        opacity: 0.95;
        height: 100%;
      }
      #wrapper {
        display: inline-block;
        position: relative;
        width: 60%;
        min-width: 650px;
        max-width: 1200px;
      }
      #dummy {
        padding-top: 75%;
      }
      #screen {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: none;
        /*-webkit-animation:blurry 8s ease-in-out infinite alternate;
        -moz-animation:blurry 8s ease-in-out infinite alternate;
        animation:blurry 8s ease-in-out infinite alternate;*/
      }
      #screen .row {
        display: block;
        height: 4%;
      }
      #screen .row .pixel {
        display: block;
        float: left;
        width: 2.5%;
        height: 100%;
        font-size: 140%;
      }
      #screen .row .pixel .sub {
        display: block;
        float: left;
        width: 50%;
        height: 50%;
      }
      @media (max-width:1280px) {
        #screen .row .pixel {
          font-size: 120%;
        }
      }
      @media (max-width:1000px) {
        #screen .row .pixel {
          font-size: 100%;
        }
      }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
      $( document ).ready(function() {
        $('#screen').fadeIn(600);
      });
    </script>
    <link rel="shortcut icon" href="./favicon.ico">
  </head>
<body>

  <video id="videoBg" autoplay="true" loop >
    <source src="style/bg.mp4" type="video/mp4">
  </video>

  <div id="wrapWrapper">
    <div id="wrapper">
      <div id="dummy"></div>
      <?php
        echo getPage();
      ?>
    </div>
  </div>
</body>
</html>

<?php
  function getPage() {
    $X = 25;
    $Y = 40;

    $specialCharacters = array(
      1=>   array(0,0,0,1),
      2=>   array(0,0,1,0),
      3=>   array(0,0,1,1),
      4=>   array(0,1,0,0),
      5=>   array(0,1,0,1),
      6=>   array(0,1,1,0),
      7=>   array(1,0,0,0)
    );

    $pageInt = (int)$_GET['p'];
    if ($pageInt == "")
      $pageInt = 100;

    $filePath = './csv/'.$pageInt.'.csv';
    if (file_exists($filePath)) {
      $page = array();
      $file = fopen($filePath, 'r');
      while (($line = fgetcsv($file)) !== FALSE) {
        $page[] = $line;
      }
      fclose($file);
    }

    $pageIntStr = (string)$pageInt;
    $time = date('dmyHi', time());
    $page[0] = array(
      0=>'wkp',
      1=>'wk'.substr($pageIntStr,0,1),
      2=>'wk'.substr($pageIntStr,1,1),
      3=>'wk'.substr($pageIntStr,2,1),

      6=>'ck'.substr($time,0,1),
      7=>'ck'.substr($time,1,1),
      8=>'ck.',
      9=>'ck'.substr($time,2,1),
      10=>'ck'.substr($time,3,1),
      11=>'ck.',
      12=>'ck'.substr($time,4,1),
      13=>'ck'.substr($time,5,1),

      17=>'yk'.substr($time,6,1),
      18=>'yk'.substr($time,7,1),
      19=>'yk:',
      20=>'yk'.substr($time,8,1),
      21=>'yk'.substr($time,9,1),

      25=>'mkw',
      26=>'mke',
      27=>'mkb',
      28=>'mk-',
      29=>'mki',
      30=>'mkn',
      31=>'mkf',
      32=>'mku',
      33=>'mks',
      34=>'mki',
      35=>'mko',
      36=>'mkn',
      37=>'mk.',
      38=>'mkd',
      39=>'mkk',
    );

    $return = "<div id=\"screen\">";
    for ($x = 0; $x <= $X-1; $x++) {
      $return .= "<div class=\"row {$x}\">";
      for ($y = 0; $y <= $Y-1; $y++) {
        $cellString = $page[$x][$y];

        if (substr($cellString,0,1) == '$') {
          $colorFront   = substr($cellString,2,1);
          $colorBack    = substr($cellString,3,1);
          $special      = (int)substr($cellString,1,1);
          $thisChar     = $specialCharacters[$special];
          $return .= "<div class=\"pixel {$y}\">";
          foreach ($thisChar as $sub) {
            $return .= "<div class=\"sub\"";
            if ($sub == 1)
              $return .=" style=\"background: ".getColor($colorFront).";\"";
            else if ($sub == 0)
              $return .=" style=\"background: ".getColor($colorBack).";\"";
            $return .= "></div>";
          }
          $return .= "</div>";
        }

        else {
          if (strlen($cellString) == 1)
            $return .= "<div class=\"pixel {$y}\" style=\"background: ".getColor(substr($cellString,0,1)).";\"></div>";
          else if (strlen($cellString) > 1 && strlen($cellString) <= 2)
            $return .= "<div class=\"pixel {$y}\" style=\"color: ".getColor(substr($cellString,0,1)).";\">".strtoupper(substr($cellString,1,1))."</div>";
          else if (strlen($cellString) > 2 && strlen($cellString) <= 3)
            $return .= "<div class=\"pixel {$y}\" style=\"color: ".getColor(substr($cellString,0,1))."; background: ".getColor(substr($cellString,1,1)).";\">".strtoupper(substr($cellString,2,1))."</div>";
          else
            $return .= "<div class=\"pixel {$y}\"></div>";
        }
      }
      $return .= "</div>";
    }
    $return .= "</div>";
    return $return;
  }

  function getColor($input) {
    switch (strtolower($input)) {
      case 'w':
        return "#FFFFFF";
      break;
      case 'x':
        return "#4D4D4D";
      break;
      case 'r':
        return "#FF0000";
      break;
      case 'g':
        return "#00FF00";
      break;
      case 'b':
        return "#0000FF";
      break;
      case 'c':
        return "#00FFFF";
      break;
      case 'm':
        return "#FF00FF";
      break;
      case 'y':
        return "#FFFF00";
      break;
      case 'k':
        return "#000000";
      break;
      default:
        return "#000000";
      break;
    }
  }
?>

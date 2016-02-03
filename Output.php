<?php

//functie om een tabel te kunnen maken van een array

function build_table($array) {
    $html = '<table>';
    $xcordinaat = "0";
    $ycordinaat = "0";
    foreach ($array as $key => $value) {
        $html .= '<tr>';
        foreach ($value as $key2 => $value2) {
            $cordinaat = "x" . $xcordinaat . "y" . $ycordinaat;
            $html .= "<td class='$cordinaat'>" . $value2 . '</td>';
            $xcordinaat = $xcordinaat + 1;
        }
        $html .= '</tr>';
        $ycordinaat = $ycordinaat + 1;
        $xcordinaat = "0";
    }
    $html .= '</table>';
    return $html;
}

//array met letters om minnetjes te veranderen
$alfabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p",
    "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
);
//woordzoeker.txt in een array zetten per regel
//$woordenzoeker = file("woordzoeker_1.txt");
$woordenzoeker = file("woordzoeker.txt");

//array splitsen in een woordzoeker deel en een zoekwoorden deel
$witregel = file("witregel.txt");
$witregel = $witregel[0];
$MagischWitRegelNummer = array_search($witregel, $woordenzoeker);
unset($woordenzoeker[$MagischWitRegelNummer]);
$zoekwoorden = array_slice($woordenzoeker, $MagischWitRegelNummer);
foreach ($zoekwoorden as &$zoekwoord) {
    $zoekwoord = trim($zoekwoord);
}
$woordenzoeker = array_slice($woordenzoeker, 0, $MagischWitRegelNummer);



//woordzoeker array met regels verder splitsen in een multidimensionale array met lose letters
foreach ($woordenzoeker as &$regel) {
    $regel = str_split(trim($regel));
}

//minnetjes veranderen in letters
foreach ($woordenzoeker as &$value3) {
    foreach ($value3 as &$value4) {
        if ($value4 == "-") {
            $value4 = $alfabet[rand(0, 25)];
        }
    }
}

//letters in een regel tellen
$regelletters = count($woordenzoeker[0]);

//zoekwoorden ontdoen van hoofdletters zodat ze gezocht kunnen worden
//gzw = gesplitst zoekwoord
//gesplitst = gesplitste zoekwoorden
$zoekwoorden = array_map('strtolower', $zoekwoorden);
$gesplitst = $zoekwoorden;
foreach ($gesplitst as &$gzw) {
    $gzw = str_split($gzw);
}

//$zoekendwoord = $gesplitst[0];
$regelnummer = 0;
foreach ($woordenzoeker as $woordenzoekerregel) {
    $regelnummer++;
    foreach ($gesplitst as $woordIndex => $zoekendwoord) {
        $hetWoord = implode('', $zoekendwoord);
        $aantalkeer = $regelletters - count($zoekendwoord) + 1;
        $cordinaat[$hetWoord] = array();
        for ($j = 0; $j < $aantalkeer; $j++) {
            $check = 0;
            for ($i = 0; $i < count($zoekendwoord); $i++) {
                if ($zoekendwoord[$i] == $woordenzoekerregel[$i + $j]) {
                    $check = $check + 1;
                    $x = $i + $j;
                    $y = $regelnummer - 1;
                    $c = "x" . $x . "y" . $y;
                    array_push($cordinaat[$hetWoord], $c);
                }
            }
            if ($check == count($zoekendwoord)) {
                $z = implode($zoekendwoord);
                echo $z . " zit in regel " . $regelnummer;
                echo "</br>";
                echo "namelijk op de volgende cordinaten:";
                echo "<pre>", print_r($cordinaat[$hetWoord], true), "</pre>";
                $gevondenWoordenCoordinaten[$hetWoord] = $cordinaat[$hetWoord];
            }
            if ($check <> count($zoekendwoord)) {
                for ($a = 0; $a <= count($zoekendwoord); $a++) {
                    unset($cordinaat[$hetWoord][$a]);
                }
            }
        }
    }
}





//echo "<pre>", print_r($gevondenWoordenCoordinaten), "</pre>";
echo "<pre>", PRINT_R($gevondenWoordenCoordinaten), "</pre>";
?>


<html>
    <head>
        <title>Woordenzoeker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="opmaak.css">
        <?php
        $classes = $zoekwoorden;
        print "<style>";
        include_once 'opmaak.php';
        print "</style>";
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <?php
        foreach ($gevondenWoordenCoordinaten as $GEVONDENWOORDJE => $gevondenwoord) {
            echo '<script type=text/javascript>';
            echo '$(document).ready(function () {';
            echo '$("div.' . $GEVONDENWOORDJE . '").mouseenter(function () {';
            echo '$("td.' . $GEVONDENWOORDJE . '").css("background-color", "red");';
            echo "});";
            echo '});';
            echo '</script>';
            echo '<script type=text/javascript>';
            echo '$(document).ready(function () {';
            echo '$("div.' . $GEVONDENWOORDJE . '").mouseleave(function () {';
            echo '$("td.' . $GEVONDENWOORDJE . '").css("background-color", "white");';
            echo "});";
            echo "});";
            echo '</script>';
            echo '<script type=text/javascript>';
            echo '$(document).ready(function () {';
            echo '$("div.' . $GEVONDENWOORDJE . '").click(function () {';
            echo '$("td.' . $cordinaat . '").toggleClass("rood");';
            echo "});";
            echo "});";
            echo '</script>';
        }
        ?>
<?php
//sort ($gevondenWoordenCoordinaten);
foreach ($gevondenWoordenCoordinaten as $GEVONDENWOORDJE => $gevondenwoord) {
    //$GEVONDENWOORDJE = key($gevondenWoordenCoordinaten);
    //echo $GEVONDENWOORDJE . " ";
    foreach ($gevondenwoord as $cordinaat) {
        echo '<script type=text/javascript>';
        echo '$(document).ready(function () {';
        echo '$("td.' . $cordinaat . '").addClass("' . $GEVONDENWOORDJE . '");';
        echo '});';
        echo '</script>';
    }
}
?>
    </head>
    <body>
        <div id="tabel">
            <?php
//de tabel laten zien
            echo build_table($woordenzoeker);
            ?>   
            <div id="zoekwoorden">
                </br>
                <?php
//het lijstje met zoekwoorden laten zien
                sort($zoekwoorden);
                foreach ($zoekwoorden as &$zoekwoord) {
                    $ZOEKWOORD = ucfirst($zoekwoord);
                    echo "<div class=$zoekwoord>$ZOEKWOORD</div>";
                    //echo "</br>";
                }
                ?>
            </div>
        </div>
    </body>
</html>

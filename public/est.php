<?php

namespace App\Public;

function checkIfCorrectFormat($date, $format)
{
    $d = \DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) == $date;
}

function checkIfCorrectDate($date): bool {
    if($date > '2013-01-01T00:00:00+00:00') {
        return true;
    } else {
        return false;
    }
}


$file = file_get_contents(dirname(__DIR__) . '/src/Files/data.json');

$decoded = json_decode($file, true);


    $a = checkIfCorrectFormat($decoded['from'], 'Y-m-d\TH:i:sP');
    var_dump($a);

    $b = checkIfCorrectDate($decoded['from']);
    var_dump($b);


    /*$startDate = substr($decoded['from'], 0, 10);
    $endDate = substr($decoded['to'], 0, 10);

    echo $startDate . ' ' . $endDate;*/


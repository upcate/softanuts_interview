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


echo getenv('app.nbp_url');


    /*$startDate = substr($decoded['from'], 0, 10);
    $endDate = substr($decoded['to'], 0, 10);

    echo $startDate . ' ' . $endDate;*/


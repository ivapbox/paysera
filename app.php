<?php
foreach (explode("\n", file_get_contents($argv[1])) as $row) {
    if (empty($row)) continue;
    $json = json_decode($row);
    $value = [];
    foreach ($json as $datum) {
        $value[] = $datum;
    }
    if (count($value) == 3) {
        $binResults = file_get_contents('https://lookup.binlist.net/' . $value[0]);
        if (!$binResults)
            die('error!');
        $r = json_decode($binResults);
        $isEu = isEu(isset($r->{'country'}->{'alpha2'}) ? $r->{'country'}->{'alpha2'} : '');
        $rateData = file_get_contents('https://api.exchangeratesapi.io/latest');
        $rateJson = json_decode($rateData, true);
        $rate = isset($rateJson['rates'][$value[2]]) ? $rateJson['rates'][$value[2]] : 0;
        if ($value[2] === 'EUR' || $rate === 0) {
            $amntFixed = $value[1];
        } else {
            $amntFixed = $value[1] / $rate;
        }
        echo $amntFixed * ($isEu === 'yes' ? 0.01 : 0.02);
        print "\n";
    }
}

function isEu($c)
{
    switch ($c) {
        case 'AT':
        case 'BE':
        case 'BG':
        case 'CY':
        case 'CZ':
        case 'DE':
        case 'DK':
        case 'EE':
        case 'ES':
        case 'FI':
        case 'FR':
        case 'GR':
        case 'HR':
        case 'HU':
        case 'IE':
        case 'IT':
        case 'LT':
        case 'LU':
        case 'LV':
        case 'MT':
        case 'NL':
        case 'PO':
        case 'PT':
        case 'RO':
        case 'SE':
        case 'SI':
        case 'SK':
            $result = 'yes';
            break;
        default:
            $result = 'no';
            break;
    }
    return $result;
}

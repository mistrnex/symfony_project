<?php

$inputs = ['Petr', 'Jan', 'John', 'Vocas', 'H20', 'Pepek'];

$inputs2 = ['Jan', 'John', 'Vocas', 'H20', 'Pepek', 'Lizani'];


function generateRoundRobin($arrayInputs)
{
    $count = count($arrayInputs);
    $firstItem = $arrayInputs[0];
    $result = [];

    for ($i = 0; $i < $count - 1; $i++) {
        array_shift($arrayInputs);
        echo '<br>';
        $lastItem = end($arrayInputs);
        $otherInputs = array_unshift($arrayInputs, $lastItem);
        array_pop($arrayInputs);
        $arrayInputs = array_merge([$firstItem], $arrayInputs);

        // black and white problem
        //while loop s cislama j,k kde j roste a k klesa?
        if ($i % 2 === 0) {
            $result[$i][] = $arrayInputs[0] . ' - ' . $arrayInputs[$count-1] . '<br>';
        } else {
            $result[$i][] = $arrayInputs[$count-1] . ' - ' . $arrayInputs[0] . '<br>';

        }
        $result[$i][] = $arrayInputs[1] . ' - ' . $arrayInputs[4] . '<br>';
        $result[$i][] = $arrayInputs[2] . ' - ' . $arrayInputs[3] . '<br>';
    }

    return $result;
}

foreach (generateRoundRobin($inputs2) as $i => $inputs) {
    foreach ($inputs as $input) {
        echo ($i+1) . ': ' . $input;
    }
    echo '<br>';
}

?>
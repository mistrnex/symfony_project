<?php

$inputs = ['Petr', 'Jan', 'John', 'Vocas', 'H20', 'Pepek'];

 function roundRobinGenerator(array $inputs): array {
     $countParticipants = count($inputs);
     $numberOfRounds = $countParticipants-1;
     $numberOfPairs = floor($countParticipants/2);
     $roundRobinTable = [];
     $firstInput = $inputs[0];

     for ($i=1; $i <= $numberOfRounds; $i++) {
         $roundRobinTable[$i] = [
             $inputs[0], $inputs[1]
         ];

         echo $inputs[0] . '<br>' . $inputs[1];
         // posunout o jedna krome [0]
         $lastItem = end($inputs);
         echo '<br>' . $lastItem . '<br>';
         $otherInputs = array_unshift($inputs, $lastItem);
         $inputs = array_merge([$firstInput, $otherInputs]);
     }

    return $roundRobinTable;
    }

roundRobinGenerator($inputs);

//    var_dump(roundRobinGenerator($inputs));

//    foreach (roundRobinGenerator($inputs) as $i => $input) {
//        echo $input[0] . ' '. $i . '<br>';
//        echo $input[1] . ' '. $i . '<br>';
//        echo '<br>';
//    }

//    print(roundRobinGenerator($inputs));

?>
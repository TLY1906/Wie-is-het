<?php
    require_once __DIR__ . '/../includes/load_data.php';

    $characterDataset = load_data();
    $allNames = [];

    foreach ($characterDataset as $name => $person) {
        // _feature_order is not a character
        if ($name === '_feature_order') {
            continue;
        }
        $allNames[] = $name;
    }

    if (count($allNames) === 0) {
        die("No characters found in the JSON.\n");
    }

    $chosenName = $allNames[0]; // later the student should be able to make this dynamic based on feature or random
    $personData = $characterDataset[$chosenName];

    echo "Features of character: " . $chosenName . "\n";

    if (!isset($personData['features'])) {
        die("Error: 'features' not found for character " . $chosenName . "\n");
    }

    foreach ($personData['features'] as $featureName => $value) {
        // 1 = yes, 0 = no
        //$text = $value ? 'YES' : 'NO';
        echo $value;
        if ($value == true)
        {
            $text = 'YES';
        }
        else {
            $text = 'NO';
        }

        echo '<pre>';
        echo " " . $featureName . ": " . $text . "\n";
        echo '</pre>';
    }

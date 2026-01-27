<?php
    require_once __DIR__ . '/../includes/load_data.php';
    $characterDataset = load_data();

    // Loop through all the characters
    foreach ($characterDataset as $name => $person) {

        // skip _feature_order, that's not a character
        if ($name === '_feature_order') {
            continue;
        }

        // Check if the 'woman' feature exists and show all women on the screen where this is the case
        if (isset($person['features']['woman']) && $person['features']['woman'] == true)
        {
            echo $name . "<br>";
        }
    }

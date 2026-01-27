<?php
    require_once __DIR__ . '/../includes/load_data.php';
    $characterDataset = load_data();

    // Loop through all the characters
    foreach ($characterDataset as $name => $person) {

        // skip _feature_order, that's not a character
        if ($name === '_feature_order') {
            continue;
        }

        // is there a man who is bald and has glasses
        if (isset($person['features']['man']) &&
            $person['features']['man'] == true &&
            $person['features']['bald'] == true &&
            $person['features']['glasses'] == true)
        {
            echo $name . "<br>";
        }
    }

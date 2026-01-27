<?php
    require_once __DIR__ . '/../includes/load_data.php';

    $characterDataset = load_data();
    $allNames = [];

    //echo '<pre>';
    //var_dump($characterDataset);
    //echo '</pre>';

    foreach ($characterDataset as $name => $person) {
        // _feature_order is not a character
        if ($name === '_feature_order') {
            continue;
        }
        $allNames[] = $name;
    }

    // Show all character names
    echo "All characters:\n";
    foreach ($allNames as $name) {
        echo "- " . $name . "\n";
    }
    echo "\n";

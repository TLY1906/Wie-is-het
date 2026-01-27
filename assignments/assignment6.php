<?php
    require_once __DIR__ . '/../includes/load_data.php';
    $characterDataset = load_data();
    $allNames = [];

    echo '<ul>';
    // collect all valid character names
    foreach ($characterDataset as $key => $value) {
        if ($key !== '_feature_order') {
            $allNames[] = $key;
        }
    }

    // pick random name
    if (!empty($allNames)) {
        $randomName = $allNames[array_rand($allNames)];
        $safeName = htmlspecialchars($randomName, ENT_QUOTES, 'UTF-8');
        $imageSource = '../images/' . $safeName . '.png';

        echo '
            <li>
                ' . $safeName . '<br>
                <img src="' . $imageSource . '" alt="' . $safeName . '" style="max-width:120px; height:auto;">
            </li>
        ';
    }

    echo '</ul>';


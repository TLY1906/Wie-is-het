<?php
    require_once __DIR__ . '/../includes/load_data.php';
    $characterDataset = load_data(); // your JSON structure

    $allNames = [];
    foreach ($characterDataset as $name => $person) {
        // _feature_order is not a character
        if ($name === '_feature_order') {
            continue;
        }
        $allNames[] = $name;
    }

    $html = '<ul>';

    foreach ($allNames as $name) {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $imageSource = '../images/' . $safeName . '.png';

        $html .= '
            <li>
                ' . $safeName . '<br>
                <img src="' . $imageSource . '" alt="' . $safeName . '" style="max-width:120px; height:auto;">
            </li>
        ';
    }

    $html .= '</ul>';

    echo $html;

<?php
session_start();
require_once __DIR__ . '/../includes/load_data.php';
$characterDataset = load_data(); // your JSON structure

// --- Build list of all character names --------------------------------------
$allNames = [];
foreach ($characterDataset as $name => $person) {
    if ($name === '_feature_order') {
        continue;
    }
    $allNames[] = $name;
}

// --- Choose and store random character --------------------------------------
if (!isset($_SESSION['chosenName']) && !empty($allNames)) {
    $_SESSION['chosenName'] = $allNames[array_rand($allNames)];
}

$chosenName = $_SESSION['chosenName'] ?? null;

// --- Initialize remaining candidates ----------------------------------------
if (!isset($_SESSION['remainingCandidates'])) {
    $_SESSION['remainingCandidates'] = $allNames;
}
$remainingCandidates = $_SESSION['remainingCandidates'];

// --- Handle POST actions (explicit, single gate) ----------------------------
$message = '';
$isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($isPost) {

    // 1) Reset action
    if (isset($_POST['reset'])) {
        unset($_SESSION['chosenName'], $_SESSION['remainingCandidates']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // 2) Feature action
    elseif (isset($_POST['feature']) && $chosenName !== null) {
        $featureKey = (string)$_POST['feature'];

        if (isset($characterDataset[$chosenName]['features'][$featureKey])) {
            $value = (int)$characterDataset[$chosenName]['features'][$featureKey]; // 0 or 1
            $answerText = $value ? 'YES!' : 'NO!';

            $message = [
                    'feature' => $featureKey,
                    'answer'  => $answerText,
                    'yes'     => ($value === 1),
            ];

            // Filter remaining candidates to match this answer
            $newRemaining = [];
            foreach ($remainingCandidates as $name) {
                if (
                        isset($characterDataset[$name]['features'][$featureKey]) &&
                        (int)$characterDataset[$name]['features'][$featureKey] === $value
                ) {
                    $newRemaining[] = $name;
                }
            }

            $remainingCandidates = $newRemaining;
            $_SESSION['remainingCandidates'] = $remainingCandidates;
        } else {
            $message = ['error' => 'Unknown feature.'];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Guess Who – Board Game</title>

    <!-- XAMPP-friendly: URL path (NOT filesystem path) -->
    <link rel="stylesheet" href="/project/css/assignment8.css">
</head>
<body>
<div class="game-shell">
    <div class="game-header">
        <div class="game-title">
            <h1>Guess Who?</h1>
            <p>Ask about features and flip down the wrong faces until only one is left.</p>
        </div>
        <div class="pill-info">
            Classic Board Game · PHP Edition
        </div>
    </div>

    <div class="game-layout">
        <!-- Left: controls & status -->
        <div class="panel">
            <div class="panel-header">
                <h2>Ask a question</h2>
                <span>Pick a feature to check</span>
            </div>

            <div class="status-wrap">
                <?php if (is_array($message) && isset($message['feature'], $message['answer'])): ?>
                    <div class="status-message">
                        <span class="feature-label">
                            Feature:
                            <span class="badge">
                                <?php echo ucfirst(str_replace('_', ' ', $message['feature'])); ?>
                            </span>
                        </span>
                        <span class="<?php echo $message['yes'] ? 'answer-yes' : 'answer-no'; ?>">
                            <?php echo htmlspecialchars($message['answer'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                <?php elseif (is_array($message) && isset($message['error'])): ?>
                    <div class="status-message">
                        <span class="answer-no">
                            <?php echo htmlspecialchars($message['error'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="status-message">
                        <span class="feature-label">Ask your first question!</span>
                        <span>Does your person have <span class="badge">Glasses</span> or <span class="badge">Mustache</span>?</span>
                    </div>
                <?php endif; ?>
            </div>

            <form method="post">
                <div class="form-grid">
                    <div>
                        <label for="feature">Feature</label>
                        <select name="feature" id="feature">
                            <?php foreach ($characterDataset['_feature_order'] as $featureKey): ?>
                                <?php $label = ucfirst(str_replace('_', ' ', $featureKey)); ?>
                                <option value="<?php echo htmlspecialchars($featureKey, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span class="icon">?</span>
                        Ask
                    </button>

                    <button type="submit" name="reset" value="1" class="btn btn-secondary">
                        <span class="icon">🔁</span>
                        New Game
                    </button>
                </div>
            </form>

            <!-- Optional debugging: reveal secret character -->
            <!--
            <div style="margin-top:10px;font-size:11px;font-weight:bold;">
                Secret character: <?php echo htmlspecialchars($chosenName ?? 'none', ENT_QUOTES, 'UTF-8'); ?>
            </div>
            -->
        </div>

        <!-- Right: board -->
        <div class="panel">
            <div class="panel-header">
                <h2>Game board</h2>
                <span>Characters still standing</span>
            </div>

            <div class="board-meta">
                <div>
                    Remaining: <strong><?php echo count($remainingCandidates); ?></strong>
                    <?php if (count($remainingCandidates) === 1): ?>
                        · <span class="found-hint">You probably found them!</span>
                    <?php endif; ?>
                </div>
                <div>
                    Total characters: <strong><?php echo count($allNames); ?></strong>
                </div>
            </div>

            <ul class="character-grid">
                <?php foreach ($remainingCandidates as $name): ?>
                    <?php
                    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                    $imageSource = '../images/' . $safeName . '.png';
                    ?>
                    <li class="character-card">
                        <span class="character-name"><?php echo $safeName; ?></span>
                        <img src="<?php echo $imageSource; ?>" alt="<?php echo $safeName; ?>">
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="footer-hint">
                When there’s only one card left, shout it out:
                “It’s <?php echo count($remainingCandidates) === 1 ? htmlspecialchars($remainingCandidates[0], ENT_QUOTES, 'UTF-8') : '...'; ?>!”
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
function calculateIncome($wordCount, $subscribers, $privilegeCoins, $mgs, $winwinTier) {
    if ($wordCount >= 1000 && $wordCount <= 1200) {
        $coinPrice = 10; $coinValue = 0.005000;
    } elseif ($wordCount <= 1400) {
        $coinPrice = 12; $coinValue = 0.005000;
    } elseif ($wordCount <= 1600) {
        $coinPrice = 13; $coinValue = 0.004615;
    } elseif ($wordCount <= 1800) {
        $coinPrice = 15; $coinValue = 0.004444;
    } elseif ($wordCount <= 2000) {
        $coinPrice = 16; $coinValue = 0.004687;
    } elseif ($wordCount <= 2200) {
        $coinPrice = 18; $coinValue = 0.004762;
    } else {
        $coinPrice = 20; $coinValue = 0.004286;
    }

    $rawSub = $coinPrice * $coinValue * $subscribers;
    $finalSub = ($rawSub > 200) ? $rawSub : $rawSub * 0.9;
    $privilegeIncome = $privilegeCoins * 0.008;

    $winWinIncome = 0;
    if ($winwinTier == 'tier1') $winWinIncome = 50;
    elseif ($winwinTier == 'tier2') $winWinIncome = 200;
    elseif ($winwinTier == 'tier3') $winWinIncome = 400;

    $mgsIncome = 0;
    if ($mgs) {
        if ($finalSub > 60 && $finalSub <= 200) {
            return [200 + $winWinIncome, $finalSub, $privilegeIncome, 0, $winWinIncome];
        } else {
            $mgsIncome = 200;
        }
    }

    $total = $finalSub + $privilegeIncome + $mgsIncome + $winWinIncome;
    return [$total, $finalSub, $privilegeIncome, $mgsIncome, $winWinIncome];
}

$income = null;
$finalSub = $privilegeIncome = $mgsIncome = $winWinIncome = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $wordCount = (int)$_POST["word_count"];
    $subscribers = (int)$_POST["subscribers"];
    $privilegeCoins = (int)$_POST["privilege_coins"];
    $mgs = isset($_POST["mgs"]) && $_POST["mgs"] === 'yes';
    $winwin = $_POST["winwin"] ?? '';

    list($income, $finalSub, $privilegeIncome, $mgsIncome, $winWinIncome) = calculateIncome($wordCount, $subscribers, $privilegeCoins, $mgs, $winwin);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>WebNovel Income Calculator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --primary-color: #b30000;
      --background: #f9f9f9;
      --card-bg: #fff;
      --input-border: #ccc;
      --highlight: #4CAF50;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--background);
      padding: 1rem;
      margin: 0;
    }

    header h1 {
      text-align: center;
      color: var(--primary-color);
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
    }

    form, .result {
      max-width: 600px;
      margin: 0 auto;
      background: var(--card-bg);
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
    }

    label {
      display: block;
      margin-top: 1rem;
      font-weight: 600;
    }

    input, select, button {
      width: 100%;
      padding: 0.7rem;
      margin-top: 0.3rem;
      border: 1px solid var(--input-border);
      border-radius: 6px;
      font-size: 1rem;
    }

    button {
      background-color: var(--primary-color);
      color: #fff;
      border: none;
      margin-top: 1.2rem;
      cursor: pointer;
      font-size: 1rem;
    }

    button:hover {
      background-color: #900000;
    }

    .result {
      border-left: 5px solid var(--highlight);
      margin-top: 2rem;
    }

    ul {
      list-style: none;
      padding-left: 0;
    }

    .list-group-item {
      padding: 0.8rem;
      border-bottom: 1px solid #eee;
    }

    .bg-light {
      background-color: #f1f1f1;
      font-weight: bold;
    }

    @media (max-width: 480px) {
      header h1 {
        font-size: 1.4rem;
      }

      input, select, button {
        font-size: 0.95rem;
        padding: 0.6rem;
      }

      .list-group-item {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
<header>
  <h1>WebNovel Income Calculator</h1>
</header>

<main>
  <form method="POST">
    <label>Average Word Count:</label>
    <input type="number" name="word_count" required max="2800" min="1000" value="<?= isset($_POST['word_count']) ? htmlspecialchars($_POST['word_count']) : '' ?>">

    <label>Total Subscription:</label>
    <input type="number" name="subscribers" required value="<?= isset($_POST['subscribers']) ? htmlspecialchars($_POST['subscribers']) : '' ?>">

    <label>Total Privilege Coins:</label>
    <input type="number" name="privilege_coins" required value="<?= isset($_POST['privilege_coins']) ? htmlspecialchars($_POST['privilege_coins']) : '' ?>">

    <label>MGS (Did you meet the condition?):</label>
    <select name="mgs">
      <option value="" <?= empty($_POST['mgs']) ? 'selected' : '' ?>>No</option>
      <option value="yes" <?= (isset($_POST['mgs']) && $_POST['mgs'] === 'yes') ? 'selected' : '' ?>>Yes</option>
    </select>

    <label>Win Win Tier:</label>
    <select name="winwin">
      <option value="" <?= empty($_POST['winwin']) ? 'selected' : '' ?>>None</option>
      <option value="tier1" <?= (isset($_POST['winwin']) && $_POST['winwin'] === 'tier1') ? 'selected' : '' ?>>Tier 1 ($50)</option>
      <option value="tier2" <?= (isset($_POST['winwin']) && $_POST['winwin'] === 'tier2') ? 'selected' : '' ?>>Tier 2 ($200)</option>
      <option value="tier3" <?= (isset($_POST['winwin']) && $_POST['winwin'] === 'tier3') ? 'selected' : '' ?>>Tier 3 ($400)</option>
    </select>

    <button type="submit">Calculate</button>
  </form>

  <?php if ($income !== null): ?>
    <div class="result">
      <h2>📊 Estimated Income: $<?= number_format($income, 2) ?></h2>
      <p><em>(Estimated only — may vary 5–20%)</em></p>
      <ul>
        <li class="list-group-item"><strong>✍️ Word Count:</strong> <?= htmlspecialchars($_POST["word_count"]) ?></li>
        <li class="list-group-item"><strong>👥 Subscriptions:</strong> <?= htmlspecialchars($_POST["subscribers"]) ?></li>
        <li class="list-group-item"><strong>💰 Final Sub Income:</strong> $<?= number_format($finalSub, 2) ?></li>
        <li class="list-group-item"><strong>💎 Privilege Coin Income:</strong> $<?= number_format($privilegeIncome, 2) ?></li>
        <li class="list-group-item"><strong>✅ MGS Bonus:</strong> $<?= number_format($mgsIncome, 2) ?></li>
        <li class="list-group-item"><strong>🏆 Win Win Bonus:</strong> $<?= number_format($winWinIncome, 2) ?></li>
        <li class="list-group-item bg-light"><strong>📈 Total Estimated Income:</strong> $<?= number_format($income, 2) ?></strong></li>
      </ul>
    </div>
  <?php endif; ?>
</main>
</body>
</html>

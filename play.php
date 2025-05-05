<?php
session_start();

// Initialize game setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player1'])) {
    $_SESSION['players'] = [
        ['name' => $_POST['player1'], 'score' => 0],
        ['name' => $_POST['player2'], 'score' => 0],
        ['name' => $_POST['player3'], 'score' => 0],
    ];
    $_SESSION['dice_count'] = max(1, (int)$_POST['dice_count']);
    $_SESSION['rounds'] = max(1, (int)$_POST['rounds']);
    $_SESSION['current_round'] = 1;
    $_SESSION['current_player'] = 0;
}

if (!isset($_SESSION['players'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Gambling Room</title>
    <style>
    body {
        background-image: url('bc3.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 90vh;
        color: white;
        font-family: 'Cinzel Decorative', cursive, sans-serif;
        text-align: center;
        padding: 20px;
        margin: 0;
    }
    .main-title {
    font-size: 70px;
    color: #1e170c;
    text-shadow: 2px 2px 15px rgba(243, 156, 18, 0.8);
    letter-spacing: 6px;
    margin-top: 30px;
    margin-bottom: 30px;
    font-family: 'Cinzel Decorative', cursive, sans-serif;
}

    .container {
        max-width: 1000px;
        margin: auto;
    }

    .dice-area {
        border: 2px solid #e74c3c;
        margin: 20px auto;
        padding: 30px;
        min-height: 220px;
        background-color: rgba(30, 30, 30, 0.9);
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.8);
    }

    .players {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .player {
        background-color: #2a2a2a;
        border: 2px solid #444;
        border-radius: 10px;
        padding: 20px;
        width: 250px;
        margin: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    }

    .player:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.8);
    }

    .player.active {
        transform: scale(1.05);
    }

    .player.active.player1 {
        border-color:rgba(243, 156, 18, 0.8)
    }

    .player.active.player2,
    .player.active.player3 {
        border-color: rgba(243, 156, 18, 0.8);
    }

    img.dice {
        width: 80px;
        height: 80px;
        margin: 5px;
    }

    #dice-result {
        margin-top: 20px;
        min-height: 120px;
    }

    .button-area button {
        padding: 15px 30px;
        font-size: 24px;
        border-radius: 10px;
        cursor: pointer;
        background-color: #e74c3c;
        color:rgb(243, 157, 18);
        border: none;
        transition: background-color 0.3s, box-shadow 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 0 10px rgba(231, 76, 60, 0.6);
    }

    .button-area button:hover {
        background-color: #c0392b;
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.8);
    }
    .exit-button {
    height: 60px;
    width: 120px;
    padding: 10px 30px;
    font-size: 24px;
    border-radius: 10px;
    cursor: pointer;
    background-color: #e74c3c;
    color: rgb(243, 157, 18);
    border: none;
    transition: background-color 0.3s, box-shadow 0.3s;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 0 10px rgba(231, 76, 60, 0.6);
}

.exit-button:hover {
    background-color: #c0392b;
    box-shadow: 0 0 20px rgba(243, 156, 18, 0.8);
}

</style>
</head>
<body>
<header>
        <h1 class="main-title">GAMBLING ROOM</h1>
    </header>
<div class="container">
<p style="font-size: 20px; color: rgba(243, 156, 18, 0.8);">Krog: <?= $_SESSION['current_round'] ?> / <?= $_SESSION['rounds'] ?></p>

    <div class="dice-area">
        <h2 id="player-name" style="color: orange;">
            Na vrsti: <?= htmlspecialchars($_SESSION['players'][$_SESSION['current_player']]['name']) ?>
        </h2>
        <div id="dice-result"></div>

        <form id="rollForm" method="POST">
            <input type="hidden" name="roll" value="1">
            <div class="button-area">
                <button type="submit" id="rollButton">Vrzi</button>
            </div>
        </form>
    </div>

    <div class="players">
        <?php foreach ($_SESSION['players'] as $index => $player): 
            $isActive = ($index == $_SESSION['current_player']) ? 'active' : '';
            $playerClass = "player" . ($index + 1);
        ?>
            <div class="player <?= $isActive ?> <?= $playerClass ?>" id="player<?= $index ?>">
                <h3 style="color: orange;"><?= htmlspecialchars($player['name']) ?></h3>
                <div class="total-score" style="font-size: 36px;"><?= $player['score'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="index.html" style="margin-top: 30px;">
        <button class="exit-button" style="padding: 10px 20px; font-size: 18px;">Izhod</button>
    </form>
</div>

<script>
    const rollForm = document.getElementById("rollForm");
    const resultDiv = document.getElementById("dice-result");
    const playerNameDisplay = document.getElementById("player-name");
    const rollBtn = document.getElementById("rollButton");

    rollForm.addEventListener("submit", function(e) {
        e.preventDefault();
        rollBtn.disabled = true;

        resultDiv.innerHTML = '<img src="dice-roll.gif" class="dice" alt="Rolling...">';
        playerNameDisplay.innerText = "Kocka se vrti...";

        setTimeout(() => {
            fetch("roll.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        playerNameDisplay.innerText = "Napaka: " + data.error;
                        resultDiv.innerHTML = "";
                        return;
                    }

                    // Show rolled dice
                    resultDiv.innerHTML = "";
                    data.dice.forEach(value => {
                        const img = document.createElement("img");
                        img.src = "dice" + value + ".png";
                        img.className = "dice";
                        resultDiv.appendChild(img);
                    });

                    // Update current player result
                    playerNameDisplay.innerText = `${data.name} vrže skupaj ${data.sum} točk!`;
                    document.getElementById("player" + data.index)
                        .querySelector(".total-score").innerText = data.score;

                    // Update round number
                    document.querySelector("p").innerText = `Krog: ${data.round} / <?= $_SESSION['rounds'] ?>`;

                    if (data.game_over) {
                        // Game over — redirect to results
                        setTimeout(() => {
                            window.location.href = "end.php";
                        }, 2000);
                        return;
                    }

                    // Highlight next player
                    document.querySelectorAll(".player").forEach(div => div.classList.remove("active"));
                    document.getElementById("player" + data.next_player)?.classList.add("active");

                    setTimeout(() => {
                        playerNameDisplay.innerText = `Na vrsti: ${data.next_name}`;
                        rollBtn.disabled = false;
                    }, 1500);
                });
        }, 1500);
    });
</script>


</body>
</html>

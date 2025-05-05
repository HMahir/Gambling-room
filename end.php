<?php
session_start();
if (!isset($_SESSION['players'])) {
    header("Location: index.html");
    exit();
}

usort($_SESSION['players'], function($a, $b) {
    return $b['score'] <=> $a['score'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Over - Podium</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image:url(bc3.jpg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            overflow-x: hidden;
            text-align: center;
        }

        h1 {
            margin-top: 40px;
            font-size: 64px;
            color: gold;
            text-shadow: 0 0 20px #000, 0 0 40px #fcd303;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 10px #fcd303; }
            to { text-shadow: 0 0 20px #fff, 0 0 40px gold; }
        }

        .podium {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            height: 450px;
            margin-top: 40px;
            gap: 40px;
        }

        .block {
            width: 200px;
            background: linear-gradient(145deg, #111, #333);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
            transform: scale(0);
        }

        .block h2 {
            margin: 0;
            font-size: 22px;
            color: #ccc;
        }

        .block .name {
            margin: 15px 0 5px;
            font-size: 28px;
            color: white;
            font-weight: bold;
        }

        .block .score {
            font-size: 22px;
            color: #f39c12;
        }

        .first {
            height: 300px;
        }

        .second {
            height: 240px;
        }

        .third {
            height: 220px;
        }

        .crown {
            font-size: 40px;
            color: gold;
            animation: crownBounce 2s infinite ease-in-out;
        }

        @keyframes crownBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        button {
            margin: 30px 10px 50px;
            padding: 15px 30px;
            font-size: 20px;
            background-color: #e74c3c;
            color: gold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.7);
            transition: background 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        canvas {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1000;
        }
    </style>

    <!-- GSAP & Confetti -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body>

<h1>🏆 FINAL STANDINGS 🏆</h1>

<div class="podium">
    <div class="block second">
        <h2>2ND</h2>
        <div class="name"><?= $_SESSION['players'][1]['name'] ?></div>
        <div class="score">DICE SUM: <?= $_SESSION['players'][1]['score'] ?></div>
    </div>
    <div class="block first">
        <div class="crown">👑</div>
        <h2>1ST</h2>
        <div class="name"><?= $_SESSION['players'][0]['name'] ?></div>
        <div class="score">DICE SUM: <?= $_SESSION['players'][0]['score'] ?></div>
    </div>
    <div class="block third">
        <h2>3RD</h2>
        <div class="name"><?= $_SESSION['players'][2]['name'] ?></div>
        <div class="score">DICE SUM: <?= $_SESSION['players'][2]['score'] ?></div>
    </div>
</div>
<h2 id="countdown" style="font-size: 24px; margin-top: 30px; color: #f1c40f; text-shadow: 0 0 10px #000;">
    Next game is in <span id="seconds">12</span> seconds
</h2>
<form method="post" action="index.html">
    <button type="submit">Play Again</button>
</form>
<form method="post" action="index.html">
    <button type="submit">Main Page</button>
</form>

<script>
    // Animate podium blocks
    gsap.to(".first", { scale: 1, duration: 1, ease: "back.out(1.7)", delay: 0.3 });
    gsap.to(".second", { scale: 1, duration: 1, ease: "back.out(1.7)", delay: 0.6 });
    gsap.to(".third", { scale: 1, duration: 1, ease: "back.out(1.7)", delay: 0.9 });

    // Confetti burst
    const duration = 4 * 1000;
    const end = Date.now() + duration;

    (function burstConfetti() {
        confetti({
            particleCount: 8,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
            colors: ['#FFD700', '#FF4500', '#00FF7F']
        });
        confetti({
            particleCount: 8,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
            colors: ['#FFD700', '#FF69B4', '#1E90FF']
        });

        if (Date.now() < end) {
            requestAnimationFrame(burstConfetti);
        }
    })();

    // Optional auto-redirect
    let secondsLeft = 12;
    const secondsSpan = document.getElementById("seconds");

    const countdownInterval = setInterval(() => {
        secondsLeft--;
        secondsSpan.textContent = secondsLeft;
        if (secondsLeft <= 0) {
            clearInterval(countdownInterval);
            window.location.href = "index.html";
        }
    }, 1000);
</script>

<?php session_destroy(); ?>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotional Sliding Page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            height: 100vh;
            background-color: #FF6F3C;
        }
        .fixed-text {
            position: absolute;
            left: 0;
            width: 15%;
            height: 100%;
            background-color: #000000;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
            font-size: 3em;
        }
        .sliding-columns {
            display: flex;
            flex-grow: 1;
            height: 100%;
            overflow: hidden;
            margin-left: 20%;
        }
        .column {
            flex: 1;
            padding: 10px;
            overflow: hidden;
            position: relative;
        }
        .image-slide {
            position: absolute;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .slide-up {
            animation: slideUp 12s linear infinite;
        }
        .slide-down {
            animation: slideDown 12s linear infinite;
        }
        @keyframes slideUp {
            0% { transform: translateY(100%); }
            100% { transform: translateY(-100%); }
        }
        @keyframes slideDown {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        img {
            width: 70%;
            height: auto;
            display: block;
            border-radius: 20px;
            margin: auto;
            aspect-ratio: 9/16;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="fixed-text">
            <h2>Hey, You! Let's Play Tag.</h2>
            <p>1. Make a story<br>2. Tag @runofthemill<br>3. Upload to Instagram<br>4. Appear on our wall!</p>
        </div>
        <div class="sliding-columns">
            <div class="column">
                <div class="image-slide slide-up">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 1">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 2">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 3">
                </div>
            </div>
            <div class="column">
                <div class="image-slide slide-down">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 4">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 5">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 6">
                </div>
            </div>
            <div class="column">
                <div class="image-slide slide-up">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 7">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 8">
                    <img src="assets/athletes/file (5).jpg" alt="Smoothie 9">
                </div>
            </div>
        </div>
    </div>
</body>
</html>

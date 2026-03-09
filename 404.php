<?php
require_once __DIR__ . "/layouts/header-item.php";
?>

<style>
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .error-404-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
        position: relative;
        overflow: hidden;
    }

    .error-404-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
        pointer-events: none;
    }

    .error-404-container {
        max-width: 900px;
        text-align: center;
        padding: 60px 40px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
    }

    .error-404-code {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 180px;
        font-weight: 900;
        background: linear-gradient(135deg, #ffffff 0%, #999999 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 20px;
        text-shadow: 0 0 80px rgba(255, 255, 255, 0.3);
        letter-spacing: -5px;
    }

    @media (max-width: 768px) {
        .error-404-code {
            font-size: 120px;
        }
    }

    .error-404-title {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 38px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    @media (max-width: 768px) {
        .error-404-title {
            font-size: 28px;
        }
    }

    .error-404-message {
        font-family: "Inter", "Nunito-VariableFont_wght", sans-serif;
        font-size: 18px;
        color: #cccccc;
        line-height: 1.8;
        margin-bottom: 40px;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .error-404-message {
            font-size: 16px;
        }
    }

    .error-404-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .error-404-btn {
        font-family: "Inter", "Nunito-VariableFont_wght", sans-serif;
        border-radius: 50px;
        padding: 16px 40px;
        font-size: 16px;
        font-weight: 600;
        line-height: 1;
        outline: none;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .error-404-btn-primary {
        background-color: #ffffff;
        color: #000000;
    }

    .error-404-btn-primary:hover {
        background-color: #f0f0f0;
        color: #000000;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
    }

    .error-404-btn-secondary {
        background-color: transparent;
        color: #ffffff;
        border: 2px solid #ffffff;
    }

    .error-404-btn-secondary:hover {
        background-color: #ffffff;
        color: #000000;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
    }

    .error-404-icon {
        font-size: 100px;
        color: rgba(255, 255, 255, 0.2);
        margin-bottom: 30px;
    }

    .error-404-suggestions {
        margin-top: 50px;
        padding-top: 50px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .error-404-suggestions h3 {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 24px;
        color: #ffffff;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .error-404-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .error-404-links {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .error-404-link {
        font-family: "Inter", "Nunito-VariableFont_wght", sans-serif;
        color: #ffffff;
        text-decoration: none;
        padding: 15px 20px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        backdrop-filter: blur(5px);
    }

    .error-404-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 5px 20px rgba(255, 255, 255, 0.1);
    }

    .error-404-link i {
        margin-right: 8px;
        opacity: 0.8;
    }
</style>

<section class="error-404-section">
    <div class="error-404-container">
        <div class="error-404-icon">
            <i class="fas fa-dumbbell"></i>
        </div>

        <div class="error-404-code">404</div>

        <h1 class="error-404-title">Page Not Found</h1>

        <p class="error-404-message">
            The page you're looking for doesn't exist or has been moved. Don't worry - your fitness journey continues. Let's get you back on track.
        </p>

        <div class="error-404-buttons">
            <a href="<?= BASE_URL ?>" class="error-404-btn error-404-btn-primary">
                Back to Home
            </a>
            <a href="<?= BASE_URL ?>shop.php" class="error-404-btn error-404-btn-secondary">
                Browse Shop
            </a>
        </div>

        <div class="error-404-suggestions">
            <h3>Quick Navigation</h3>
            <div class="error-404-links">
                <a href="<?= BASE_URL ?>shop.php" class="error-404-link">
                    <i class="fas fa-store"></i> Shop
                </a>
                <a href="<?= BASE_URL ?>about-us.php" class="error-404-link">
                    <i class="fas fa-info-circle"></i> About
                </a>
                <a href="<?= BASE_URL ?>contact.php" class="error-404-link">
                    <i class="fas fa-envelope"></i> Contact
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>account/profile.php" class="error-404-link">
                        <i class="fas fa-user"></i> Profile
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auth/login.php" class="error-404-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/layouts/footer.php"; ?>

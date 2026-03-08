<?php
require_once __DIR__ . "/layouts/header-item.php";
?>

<style>
    .error-404-section {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f6f6f6 0%, #ffffff 100%);
    }

    .error-404-container {
        max-width: 800px;
        text-align: center;
        padding: 40px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .error-404-code {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 140px;
        font-weight: 900;
        color: #000000;
        line-height: 1;
        margin-bottom: 20px;
        text-shadow: 3px 3px 0px #e7e7e7;
    }

    @media (max-width: 768px) {
        .error-404-code {
            font-size: 100px;
        }
    }

    .error-404-title {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #000000;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .error-404-title {
            font-size: 24px;
        }
    }

    .error-404-message {
        font-family: "Nunito-VariableFont_wght", sans-serif;
        font-size: 18px;
        color: #333;
        line-height: 1.6;
        margin-bottom: 35px;
        max-width: 600px;
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
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .error-404-btn {
        font-family: "Nunito-VariableFont_wght", sans-serif;
        border-radius: 30px;
        padding: 14px 26px;
        font-size: 18px;
        font-weight: 600;
        line-height: 1;
        outline: none;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .error-404-btn-primary {
        background-color: #000000;
        color: #ffffff;
    }

    .error-404-btn-primary:hover {
        background-color: #333;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .error-404-btn-secondary {
        background-color: #fff;
        color: #000;
        border: 2px solid #000000;
    }

    .error-404-btn-secondary:hover {
        background-color: #000000;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .error-404-icon {
        font-size: 80px;
        color: #e7e7e7;
        margin-bottom: 20px;
    }

    .error-404-suggestions {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid #e7e7e7;
    }

    .error-404-suggestions h3 {
        font-family: "Orbitron-VariableFont_wght", sans-serif;
        font-size: 22px;
        color: #000000;
        margin-bottom: 20px;
    }

    .error-404-links {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }

    .error-404-link {
        font-family: "Nunito-VariableFont_wght", sans-serif;
        color: #000000;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 8px;
        background: #f6f6f6;
        transition: all 0.3s ease;
        font-size: 16px;
    }

    .error-404-link:hover {
        background: #000000;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .error-404-link i {
        margin-right: 8px;
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
            Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or you may have typed the wrong URL. Don't worry - let's get you back on track with your fitness journey!
        </p>

        <div class="error-404-buttons">
            <a href="<?= BASE_URL ?>" class="error-404-btn error-404-btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="<?= BASE_URL ?>shop.php" class="error-404-btn error-404-btn-secondary">
                <i class="fas fa-shopping-cart"></i> Browse Shop
            </a>
        </div>

        <div class="error-404-suggestions">
            <h3>Quick Links</h3>
            <div class="error-404-links">
                <a href="<?= BASE_URL ?>shop.php" class="error-404-link">
                    <i class="fas fa-store"></i> Shop
                </a>
                <a href="<?= BASE_URL ?>index.php#about" class="error-404-link">
                    <i class="fas fa-info-circle"></i> About Us
                </a>
                <a href="<?= BASE_URL ?>index.php#classes" class="error-404-link">
                    <i class="fas fa-dumbbell"></i> Classes
                </a>
                <a href="<?= BASE_URL ?>index.php#contact" class="error-404-link">
                    <i class="fas fa-envelope"></i> Contact
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>account/profile.php" class="error-404-link">
                        <i class="fas fa-user"></i> My Account
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

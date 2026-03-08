<?php
require_once "includes/session.php";
require_once "layouts/header.php";
?>
<main class="main-wrapper">
    <div class="breadcrumb-section">
        <img src="assets/images/vector/banner-bg-vector.svg" alt="vector" class="page-vector">
        <div class="container">
            <div class="breadcrumb-title wow fadeInUp">
                <h1>contact</h1>
                <ul>
                    <li>
                        <a href="index.html">Home</a>
                        <img src="assets/images/icons/arrow-white.svg" alt="">
                    </li>
                    <li>Contact</li>
                </ul>
            </div>
        </div>
    </div>

     <!-- Testimonials Section -->
     <section class="testimonials-section section-padding2" style="padding: 0px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center bg-grey">
                    <div class="w100">
                        <h2 class="mb-0">Get in Touch
                        </h2>
                        <p class="fadeInUp pt-3 mb-0">Contact Athletes Gym in Lusail, Doha, and join a community of athletes and elites dedicated to achieving their best. Whether you have questions about memberships, personal training, or our state-of-the-art facilities, we’re here to help. Don’t wait spaces are limited.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 p-0 m-0"><img loading='lazy' width="100%" src="assets/athletes/contact-us.png" alt="img"></div>

            </div>
        </div>
        </div>
    </section>
    
    <div class="contact-section section-padding">
        <div class="container">
            <div class="contact-info">
                <div class="row">
                    <div class="col-lg-4 d-flex justify-content-lg-start justify-content-center">
                        <div class="info wow fadeInUp">
                            <div class="icon-block" style="margin-bottom: 34px;">
                                <img src="assets/images/icons/contact-msg.svg" alt="">
                            </div>
                            <a href="mailto:contact@athletesgymqa.com">info@athletesgym.qa</a>
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-center">
                        <div class="info wow fadeInUp">
                            <div class="icon-block">
                                <img src="assets/images/icons/contact-call.svg" alt="">
                            </div>
                            <a href="#!">+974 3999 2247</a>
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-lg-end justify-content-center">
                        <div class="info wow fadeInUp mb-0">
                            <div class="icon-block">
                                <img src="assets/images/icons/contact-map.svg" alt="">
                            </div>
                            <p>G-27, Mamsha Bay, Lusail Marina 12-D, Doha, Qatar.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-padding pb-0">
                <div class="contact-form">
                    <?php if (!empty($_SESSION['contact_success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['contact_success']) ?></div>
                        <?php unset($_SESSION['contact_success']); ?>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['contact_error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['contact_error']) ?></div>
                        <?php unset($_SESSION['contact_error']); ?>
                    <?php endif; ?>
                    <form method="post" action="contact_handler.php" class="input-disabled-form wow fadeInUp">
                        <?php csrfField(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" name="fullname" class="user-input"
                                        placeholder="Full Name" required>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" name="phone" class="user-input"
                                        placeholder="Phone Number" required>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="email" name="email" class="user-input"
                                        placeholder="Email" required>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea name="message" class="user-input"
                                        placeholder="Type your message here"
                                        required></textarea>

                                </div>
                            </div>
                        </div>
                        <div class="form-btn mt-0 mt-3 text-center">
                            <button class="common-btn primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <section class="map-section section-padding pt-3">
        <div class="container position-relative">
            <div class="map-block wow fadeInUp">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3603.5896457865874!2d51.49821687610774!3d25.418543122698495!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e45e70072cead53%3A0xc80f2d4aec727cf4!2sMamsha%20Bay!5e0!3m2!1sen!2sqa!4v1726579179332!5m2!1sen!2sqa" class="map" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="address-block wow fadeInLeft">
                <div class="title">
                    <h2>Find Us</h2>
                    <p>Sidcup Family Golf is located on the south side of the A20, just west of the Frognall Corner junction with the A222.</p>
                </div>
                <div class="info">
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M25.0638 4.16667C29.7721 4.18754 34.2721 6.06254 37.5638 9.35421C40.8763 12.6667 42.7305 17.1459 42.7098 21.7917V21.8959C42.5846 28.2084 39.043 34.0209 34.6263 38.5625C32.1263 41.125 29.3555 43.3959 26.3346 45.3125C25.5221 46 24.3346 46 23.5221 45.3125C19.043 42.3959 15.0846 38.7292 11.8763 34.4584C9.0638 30.75 7.45964 26.2917 7.29297 21.6459C7.33464 11.9584 15.293 4.14588 25.0638 4.16667ZM25.0638 16.5209C21.9596 16.5209 19.4596 19 19.4596 22.0834C19.4596 25.1271 21.918 27.5834 25.0013 27.625H25.0638C26.543 27.625 27.9596 27.0625 29.0013 26.0417C30.0846 24.9792 30.6909 23.5646 30.6909 22.0834C30.6909 19 28.168 16.5209 25.0638 16.5209Z" fill="#fff" />
                    </svg>

                    <p style="color:#fff">Mamsha Bay,Lusail, Doha, Qatar</p>
                </div>
            </div>
        </div>

    </section>

</main>
<?php require_once "layouts/footer.php"; ?>
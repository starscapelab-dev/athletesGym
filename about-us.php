<?php 
require_once "layouts/header.php"; 
require_once __DIR__ . "/layouts/config.php";
?>

<style>

#readBtn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #d32f2f;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 50px;
  cursor: pointer;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  z-index: 999;
}
#readBtn:hover {
  background: #b71c1c;
}
    </style>
<main class="main-wrapper">
    <div class="breadcrumb-section">
        <img src="assets/images/vector/banner-bg-vector.svg" alt="vector" class="page-vector">
        <div class="container">
            <div class="breadcrumb-title wow fadeInUp">
                <h1>About</h1>
                <ul>
                    <li>
                        <a href="<?= BASE_URL ?>index.php">Home</a>
                        <img src="assets/images/icons/arrow-white.svg" alt="">
                    </li>
                    <li>About</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- New About Section -->
     <section class="testimonials-section section-padding2" style="padding: 0px;">
        <!-- <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center bg-grey">
                    <div class="w100">
                        <h2 class="mb-0">Welcome to Athletes Gym</h2>
                        <p class="fadeInUp pt-3 mb-0 ">Located in Lusail Marina, Mamsha Bay, Athletes Gym is designed exclusively for athletes and fitness elites. Our gym is not for everyone, it’s for those who are serious about pushing their limits, reaching peak performance, and achieving excellence.

                    With state-of-the-art equipment, thoughtfully designed environment, and a focus on results, Athletes Gym offers an unparalleled training experience. From the carefully selected flooring to the energizing lighting, every detail has been crafted to create the ultimate space for elite training.</p>

                    <button id="readBtn">🔊 Read This Page</button>

                    </div>
                </div>
                <div class="col-md-6 p-0 m-0"><img loading='lazy' width="100%" src="assets/athletes/about-us-1.jpg" alt="img"></div>

                </div>
        </div> -->
    
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 p-0 m-0"><img loading='lazy' width="100%" src="assets/athletes/about-us-2.jpg" alt="img"></div>
                <div class="col-md-6 d-flex align-items-center bg-grey">
                    <div class="w100">
                        <p class="fadeInUp pt-3 mb-0">We pride ourselves on exclusivity, with a limited number of memberships available to ensure a premium experience for every member. Our professional personal trainers, including both male and female coaches, provide tailored guidance to help you perform at your best and achieve your specific goals.

                    If you’re ready to take your fitness to the next level, Athletes Gym is the place for you. Located in Lusail, Doha, this is where champions train. Spaces are limited, so don’t miss your chance to join the elite.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>
    
<!-- <canvas id="pdf-canvas" style="border:1px solid #ccc;"></canvas>
<br>
<button id="prev">⬅ Prev</button>
<button id="next">Next ➡</button>
<button id="read">🔊 Read Page</button>
<span>Page: <span id="page-num">1</span> / <span id="page-count">?</span></span>

<button onclick="speechSynthesis.pause()">⏸ Pause</button>
<button onclick="speechSynthesis.resume()">▶ Resume</button>
<button onclick="speechSynthesis.cancel()">⛔ Stop</button> -->



    <!-- <embed src="assets/athletes/RUN OF THE MILL X ZAJEL PROPOSAL FINAL (2).pdf" type="application/pdf" width="100%" height="600px" /> -->

    <!-- About Us Section -->
    <!-- <section class="about-section section-padding about-page">
        <div class="container position-relative">

            <div
                class="row justify-content-xl-between justify-content-center align-items-xl-start align-items-center gy-lg-0 gy-4">
                <div class="col-xxl-6 col-xl-6 col-lg-9 col-md-8">
                    <div class="img-wrapper wow fadeInUp">
                        
                        <img src="assets/images/about-img-3.webp" alt="" class="about-img">
                    </div>


                </div>
                <div class="col-xl-6 col-lg-9">
                    <div class="mb-5 text-lg-start text-center">
                        <div class="section-heading wow fadeInUp">

                            <h3 style="color: #000;">Welcome to ATHLETES gym in Doha</h3>
                        </div>
                        <p class="section-info wow fadeInUp">Whether you're focused on gaining strength or shedding pounds, our gym
                            is where it all comes together. We're dedicated to being a fitness center
                            for everyone. Drop in for a workout or become a member to access our
                            facilities.</p>
                        <p class="section-info wow fadeInUp">We provide a range of affordable options so you can enjoy our
                            newly equipped facility and receive guidance from our expert training
                            staff.</p>
                    </div>

                    <div class="section-info wow fadeInUp info2">
                        <p>
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean masa commodo ligula. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean masa commodo ligula eget dolor aenean massa.
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean masa commodo ligula.</p>
                    </div>

                    <ul class="achivement-block wow fadeInUp">
                        <li>
                            <h6><span class="counter-value" data-count="30"></span>+</h6>
                            <p>Members</p>
                        </li>
                        <li>
                            <h6><span class="counter-value" data-count="50"></span>+</h6>
                            <p>Equipments</p>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </section> -->

    <!-- Class Section -->
    <!-- <section class="class-section section-padding feature-section">
        <div class="container">
            <div class="row align-items-center justify-content-between heading-row">
                <div class="col-xxl-5 col-xl-5 col-lg-6">
                    <div class="section-heading wow fadeInUp">
                        <span class="heading-tag">Features</span>
                        <h3 class="mb-0">Stay Focussed stay Fit & Healthy</h3>
                    </div>
                </div>

                <div class="col-xxl-5 col-xl-5 col-lg-6">
                    <p class="section-info wow fadeInUp mb-0">Both the term and the specific exercise method were developed by Dr Kenneth H. Cooper, an exercise physiologist, and Col. Pauline Potts, a physical therapist, both of the United.</p>
                </div>
            </div>

            <div class="row class-row justify-content-center gy-md-0 gy-4">
                <div class="col-lg-4 col-md-7 col-sm-9">
                    <div class="feature-block wow fadeInUp">
                        <div class="feature-info ">
                            <h2>Best Instructor</h2>
                            <p>Aerobic exercise differs from anaerobic exercise. Anaerobic exercises, such as weightlifting or sprinting, involve quick bursts of energy.</p>
                        </div>
                        <div class="icon-block icon-1">
                            <img src="assets/images/feature-1.svg" alt="icon">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-7 col-sm-9">
                    <div class="feature-block wow fadeInUp">
                        <div class="feature-info">
                            <h2>Effective Move</h2>
                            <p>Aerobic exercise differs from anaerobic exercise. Anaerobic exercises, such as weightlifting or sprinting, involve quick bursts of energy.</p>
                        </div>
                        <div class="icon-block icon-2">
                            <img src="assets/images/feature-2.svg" alt="icon">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-7 col-sm-9">
                    <div class="feature-block wow fadeInUp">
                        <div class="feature-info">
                            <h2>Best Training</h2>
                            <p>Aerobic exercise differs from anaerobic exercise. Anaerobic exercises, such as weightlifting or sprinting, involve quick bursts of energy.</p>
                        </div>
                        <div class="icon-block icon-3">
                            <img src="assets/images/feature-3.svg" alt="icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Why Choose Us Section -->
    <!-- <section class="choose-us-section section-padding">
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-start gy-lg-0 gy-4">
                <div class="col-xl-5 col-lg-6">
                    <div class="section-heading wow fadeInUp">
                        <span class="heading-tag">Why Choose Us</span>
                        <h3>Burn Calories While Having Fun</h3>
                    </div>
                    <p class="section-info wow fadeInUp mb-3">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean
                        masa commodo ligula eget dolor aenean massa. Cum sociis natoque penatibus et magnis dis
                        parturient montes.</p>

                    <p class="section-info wow fadeInUp mb-3">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean
                        masa commodo ligula eget dolor aenean massa.</p>

                    <p class="section-info wow fadeInUp mb-4">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean
                        masa commodo ligula eget dolor aenean massa.</p>

                    <a href="#" class="common-btn primary">Learn More</a>
                </div>

                <div class="col-lg-6 col-md-9">
                    <div class="features-wrapper">
                        <div class="feature-card wow fadeInLeft">
                            <div class="icon">
                                <img src="assets/images/icons/classes.svg" alt="classes">
                            </div>
                            <h6>Classes</h6>
                            <p>Discover the format right for your lifestyle and fitness goals.</p>
                        </div>

                        <div class="feature-card wow fadeInRight">
                            <div class="icon">
                                <img src="assets/images/icons/music.svg" alt="music">
                            </div>
                            <h6>Music</h6>
                            <p>Routines are choreographed to hit songs that change all year long.</p>
                        </div>

                        <div class="feature-card wow fadeInLeft">
                            <div class="icon">
                                <img src="assets/images/icons/tech.svg" alt="tech">
                            </div>
                            <h6>Tech</h6>
                            <p>Lorem Ipsum is simply dummy text of the printing and industry.</p>
                        </div>

                        <div class="feature-card wow fadeInRight">
                            <div class="icon">
                                <img src="assets/images/icons/weight.svg" alt="weight">
                            </div>
                            <h6>Weight</h6>
                            <p>A wight is a mythical sentient being, often undead. In its original.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Team Section -->
    <!-- <section class="team-section section-padding">
        <div class="container">
            <div class="row align-items-center heading-row">
                <div class="col-xl-6 col-lg-8">
                    <div class="section-heading wow fadeInUp">
                        <span class="heading-tag">Meet The Team</span>
                        <h3>Meet Multi-Talented People Who Are Passionate</h3>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-4">
                    <div class="text-end d-none d-lg-block">
                        <a href="coaches.php" class="view-more-btn">
                            <img src="assets/images/view-more-dark.svg" alt="view-more">
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center align-items-start content-row gy-lg-0 gy-4">
                <div class="col-lg-4 col-md-7">
                    <div class="team-card wow fadeInUp card-1">
                        <img class="main-img" src="assets/images/trainer-1.webp" alt="">
                        <div class="info">
                            <div class="info-group">
                                <h6>Jensen Mustafa</h6>
                                <div class="social-links">
                                    <a href="#!"><img src="assets/images/icons/instagram-grey.svg" alt=""></a>
                                    <a href="#!"><img src="assets/images/icons/linkedin-grey.svg" alt=""></a>
                                </div>
                            </div>
                            <p class="line-of-work">Instructor</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-7">
                    <div class="team-card wow fadeInUp card-2">
                        <img class="main-img" src="assets/images/trainer-2.webp" alt="">
                        <div class="info">
                            <div class="info-group">
                                <h6>Zoe Clark</h6>
                                <div class="social-links">
                                    <a href="#!"><img src="assets/images/icons/instagram-grey.svg" alt=""></a>
                                    <a href="#!"><img src="assets/images/icons/linkedin-grey.svg" alt=""></a>
                                </div>
                            </div>
                            <p class="line-of-work">Instructor</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-7">
                    <div class="team-card wow fadeInUp card-3">
                        <img class="main-img" src="assets/images/trainer-3.webp" alt="">
                        <div class="info">
                            <div class="info-group">
                                <h6>Jean Simmons</h6>
                                <div class="social-links">
                                    <a href="#!"><img src="assets/images/icons/instagram-grey.svg" alt=""></a>
                                    <a href="#!"><img src="assets/images/icons/linkedin-grey.svg" alt=""></a>
                                </div>
                            </div>
                            <p class="line-of-work">Instructor</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center d-block d-lg-none mt-3">
                <a href="team.html" class="view-more-btn">
                    <img src="assets/images/view-more-dark.svg" alt="view-more">
                </a>
            </div>
        </div>
    </section> -->

    <!-- Testimonials Section -->
    <!-- <section class="testimonials-section section-padding">
        <div class="container">
            <div class="row heading-row">
                <div class="col-lg-12">
                    <div class="section-heading wow fadeInUp text-center">
                        <span class="heading-tag">Testimonials</span>
                        <h3>What They Say About Us</h3>
                    </div>
                </div>
            </div>

            <div class="row content-row justify-content-center">
                <div class="col-lg-8 position-relative">
                    <img src="assets/images/icons/quote.svg" alt="icon" class="quote wow fadeInUp">
                    <div class="swiper mySwiper1 wow fadeInUp">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="testimonial-inner">
                                    <span class="tag">Member Review</span>

                                    <div class="text-block">
                                        <p>l’ve been to a few different places before but nothing compares to this The flexible schedule is great.</p>
                                    </div>

                                    <div class="testimonial-bottom">
                                        <div class="auth-info">
                                            <div class="img-block">
                                                <img src="assets/images/test-1.webp" alt="user">
                                            </div>
                                            <div class="auth-detail">
                                                <h5>Eimi Fukada</h5>
                                                <span>GymFit Member</span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-inner">
                                    <span class="tag">Member Review</span>

                                    <div class="text-block">
                                        <p>l’ve been to a few different places before but nothing compares to this The flexible schedule is great.</p>
                                    </div>

                                    <div class="testimonial-bottom">
                                        <div class="auth-info">
                                            <div class="img-block">
                                                <img src="assets/images/test-1.webp" alt="user">
                                            </div>
                                            <div class="auth-detail">
                                                <h5>Eimi Fukada</h5>
                                                <span>GymFit Member</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-inner">
                                    <span class="tag">Member Review</span>

                                    <div class="text-block">
                                        <p>l’ve been to a few different places before but nothing compares to this The flexible schedule is great.</p>
                                    </div>

                                    <div class="testimonial-bottom">
                                        <div class="auth-info">
                                            <div class="img-block">
                                                <img src="assets/images/test-1.webp" alt="user">
                                            </div>
                                            <div class="auth-detail">
                                                <h5>Eimi Fukada</h5>
                                                <span>GymFit Member</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>


                        </div>
                        <div class="swiper_btn">
                            <div class="swiper-button-next">
                                <img src="assets/images/icons/arrow.svg" alt="icon">
                            </div>
                            <div class="swiper-button-prev">
                                <img src="assets/images/icons/arrow.svg" alt="icon">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section> -->

</main>
<?php require_once "layouts/footer.php"; ?>
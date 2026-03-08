/************* Main Js File ************************
    Template Name: GYMFIT - AEROBICS
    Author: Netizens Technologies
    Version: 1.0
    Copyright 2023
    Please ❤ this if you like it!
*************************************************************/
/*------------------------------------------------------------------------------------ 
    =============
    = JS INDEX  =
    =============
    01 - HAMBURGER JS
    02 - ACTIVE CLASS JS
    03 - PRICING JS
    04 - COMING SOON COUNTER JS
    05 - WOW JS
    06 - HEADER JS
    07 - SWIPER JS
-------------------------------------------------------------------------------------*/

/*=====================================================================
1 - HAMBURGER JS
=====================================================================*/
// $('body').on('click', '.menu-btn', function () {
//     $('.hamburger').toggleClass('active');
// });


// $(document).on('click', '.menu-btn, .hamburger', function () {
//     $('.hamburger').toggleClass('active'); // Toggle the active class
//     $('.navbar-collapse').toggleClass('show'); // Toggle visibility of the collapse menu
// });
$(document).ready(function () {
    $('.hamburger').click(function () {
        $(this).toggleClass("active");
        $('.navbar-collapse').toggleClass('show');
    })
    $('.navbar-collapse').click(function () {
        $(this).toggleClass('show');
    })
})



/*=====================================================================
2 - ACTIVE CLASS JS
=====================================================================*/
$(document).ready(function () {
    $(".tabActive a:not(.dropdown-toggle)").click(function () {
        $(".tabActive a:not(.dropdown-toggle)").removeClass("active");
        $(this).addClass("active");
    });
    $(function () {
        var path = window.location.href;
        var pop = [];
        $('.tabActive a:not(.dropdown-toggle)').each(function () {
            var anchor = $(this).prop('href').split("/").pop();
            pop.push(anchor);
            var href = this.href.replace(".html", "").replace(".php", "");
            var navPath = path.replace(".html", "").replace(".php", "");
            if (href === navPath) {
                $(this).addClass('active');
            }
        });
        var anchor = $('.tabActive a[href*="' + pop[0] + '"]:not(.dropdown-toggle)');
        if (path.split('/').pop() == "") {
            $(anchor).addClass("active")
        }
    });
});

/*=====================================================================
3 - PRICING JS
=====================================================================*/
$(".switch").click(function () {
    $(this).children().toggleClass('active');
    $(".tabs:not(.active)").click();
});

$('#nav-yearly-tab').click(function () {
    $('.switch span').removeClass('active');
});

$('#nav-monthly-tab').click(function () {
    $('.switch span').addClass('active');
});

$('#nav-monthly').click(function () {
    $('#nav-monthly-tab').click();
});

$('#nav-yearly').click(function () {
    $('#nav-yearly-tab').click();
});



$('.pricing-card').mouseenter(function () {
    $('.pricing-card').removeClass('active');
    $(this).addClass('active');
});

/*=====================================================================
4 - COMING SOON COUNTER JS
=====================================================================*/

function makeTimer() {

    var endTime = new Date("29 April 2024 9:56:00 GMT+01:00");
    endTime = (Date.parse(endTime) / 1000);

    var now = new Date();
    now = (Date.parse(now) / 1000);

    var timeLeft = endTime - now;

    var days = Math.floor(timeLeft / 86400);
    var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
    var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600)) / 60);
    var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

    if (hours < "10") { hours = "0" + hours; }
    if (minutes < "10") { minutes = "0" + minutes; }
    if (seconds < "10") { seconds = "0" + seconds; }

    $("#days").html(days);
    $("#hours").html(hours);
    $("#minutes").html(minutes);
    $("#seconds").html(seconds);

}

setInterval(function () { makeTimer(); }, 1000);

/*=====================================================================
5 - WOW JS
=====================================================================*/
$(document).ready(function () {
    new WOW().init();
});


/*=====================================================================
6 - HEADER JS
=====================================================================*/

$(document).resize(function () {
    headerFixed();
});
$(document).ready(function () {
    headerFixed();
});
$(document).on('scroll', function () {
    headerFixed();
});
function headerFixed() {
    if ($(window).scrollTop() >= 40) {
        $('header').addClass('fixed-menu');
    } else {
        $('header').removeClass('fixed-menu');
    }
    if ($(window).scrollTop() >= 600) {
        $('.fixed-menu').addClass('sticky-header');
    } else {
        $('.fixed-menu').removeClass('sticky-header');
    }
}


$(".menu-btn").click(function (e) {
    var menuItem = $(this);

    if (menuItem.attr("aria-expanded") === "true") {
        $("body").addClass('blur-body');
    }
    else if (menuItem.attr("aria-expanded") === "false") {
        $("body").removeClass('blur-body');
    }

});

/*=====================================================================
7 - SWIPER JS
=====================================================================*/

if ($(".swiper").length > 0) {
    var swiper = new Swiper(".mySwiper1", {
        slidesPerView: 3,
        loop: true,
        autoplay: {
            delay: 5000,
            // pauseOnMouseEnter: true,
        },
        disableOnInteraction: true,
        breakpoints: {
            0: {
                slidesPerView: 1,

            },
            576: {
                slidesPerView: 1,

            },
            992: {
                slidesPerView: 1,

            },
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".mySwiper1 .swiper_btn .swiper-button-next img",
            prevEl: ".mySwiper1 .swiper_btn .swiper-button-prev img",
        },
    });
    $(".swiper").hover(function () {
        (this).swiper.autoplay.stop();
    }, function () {
        (this).swiper.autoplay.start();
    });
} else { }

var swiper2 = new Swiper(".mySwiper2", {
    slidesPerView: 3,
    loop: true,
    autoplay: {
        delay: 5000,
        // pauseOnMouseEnter: true,
    },
    disableOnInteraction: true,
    breakpoints: {
        0: {
            slidesPerView: 1,

        },
        576: {
            slidesPerView: 3,

        },
        992: {
            slidesPerView: 4,

        },
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".mySwiper2 .swiper_btn .swiper-button-next img",
        prevEl: ".mySwiper2 .swiper_btn .swiper-button-prev img",
    },
});
$(".swiper").hover(function () {
    (this).swiper2.autoplay.stop();
}, function () {
    (this).swiper2.autoplay.start();
});

/*=====================================================================
8 - COUNTER JS
=====================================================================*/
$(document).ready(function () {

    $('.counter-value').each(function () {
        var $this = $(this),
            countTo = $this.attr('data-count');
        $({
            countNum: $this.text()
        }).animate({
            countNum: countTo
        },
            {
                duration: 2000,
                easing: 'swing',
                step: function () {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function () {
                    $this.text(this.countNum);

                }

            });
    });

});

/*=====================================================================
9 - IMG CLICK, VIDEO POPUP OPEN JS
=====================================================================*/
$(document).ready(function () {
    $('#myModal').on('shown.bs.modal', function () {
        $('#video1')[0].play();
    })
    $('#myModal').on('hidden.bs.modal', function () {
        $('#video1')[0].pause();
    })
});




// Load the YouTube IFrame API script
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;

// Initialize the YouTube player when the API is ready
function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '760', // Set the height to 760px
        width: '100%', // Make it responsive
        videoId: 'g2qR_8zgwpw', // Replace with your video ID
        playerVars: {
            autoplay: 1,         // Auto-play the video
            mute: 1,             // Mute the video
            loop: 1,             // Enable looping (can be used but we'll manage with API)
            playlist: 'g2qR_8zgwpw', // Required for looping
            controls: 0,         // Hide the controls
            modestbranding: 1,   // Minimize YouTube branding
            rel: 0,              // Disable related videos
            fs: 0,               // Disable fullscreen button
            disablekb: 1,        // Disable keyboard controls
            iv_load_policy: 3,   // Hide video annotations
            enablejsapi: 1       // Enable JS API for further customization
        },
        events: {
            onReady: onPlayerReady,
            onStateChange: onPlayerStateChange // Listen for state changes
        }
    });
}

// Optional: Start playing when the player is ready
function onPlayerReady(event) {
    event.target.playVideo();
}

// This function handles the video state changes
function onPlayerStateChange(event) {
    // 0 means the video has ended
    if (event.data == YT.PlayerState.ENDED) {
        // Play the video again when it ends
        player.playVideo();
    }
}

// for video player for apple devices 
document.addEventListener("DOMContentLoaded", function () {
    var video = document.querySelector("video");
    if (video) {
        video.play().catch(error => console.log("Autoplay prevented:", error));
    }
});



    
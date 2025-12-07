const responsive = {
  0: {
    items: 1,
  },

  320: {
    items: 1,
  },
  560: {
    items: 2,
  },
  960: {
    items: 3,
  },
};

$(document).ready(function () {
  // Header functionality moved to header.js (modern implementation)
  // Keeping this for backward compatibility, but header.js takes precedence
  
  // Only run if ModernHeader is not available
  if (typeof ModernHeader === 'undefined') {
    $nav = $(".nav");
    $toggleCollapse = $(".toggle-collapse");

    /*  Click event on toggle menu */
    $toggleCollapse.click(function () {
      $nav.toggleClass("collapse");
    });
  }

  // Owl Carousel for blog
  $(".owl-carousel").owlCarousel({
    loop: true,
    autoplay: false,
    autoplayTimeout: 6000,
    dots: false,
    nav: true,
    navText: [
      $(".owl-navigation .owl-nav-prev"),
      $(".owl-navigation .owl-nav-next"),
    ],
    responsive: responsive,
  });

  // Click to scroll top
  $(".move-up span").click(function () {
    $("html,body").animate(
      {
        scrollTop: 0,
      },
      3000
    );
  });

  // AOS Instance
  AOS.init();
});

jQuery(document).ready(function ($) {
    $(document).scroll(function () {
      var scroll = $(this).scrollTop();
      var topDist = $(".header-body nav").position();
      if (scroll > topDist.top) {
        $(".header-body nav").css({ position: "fixed", top: "0" });
      } else {
        $(".header-body nav").css({ position: "static", top: "auto" });
      }
    });
});

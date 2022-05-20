(function($) {
  'use strict';

  bp.Nouveau = bp.Nouveau || {};

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  window.BPFrienFollow = {
    init: function() {
      this.BPFollowAction();
    },

    BPFollowAction: function() {
      $('.widget_bp_friend_follow_suggestion_widget').on('click', '[data-bp-btn-action]', bp.Nouveau, bp.Nouveau.buttonAction);
    }

  };

  $(document).on('ready', function() {
    BPFrienFollow.init();
  });

  var swiper = new Swiper(".horizontal-layout", {
    spaceBetween:20,
    centeredSlides: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

})(jQuery);
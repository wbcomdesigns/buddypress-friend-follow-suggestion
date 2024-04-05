window.bp = window.bp || {};
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

// Suggestions Slider
  if ($('.bffs_horizontal_layout').length) {
  var bffs = new Bffs(".bffs_horizontal_layout", {
    centeredSlides: true,
    loop: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    navigation: {
      nextEl: ".bffs-button-next",
      prevEl: ".bffs-button-prev",
    },
  });}

// Add swiper layout for the bp-friend-follow-swiper-widget
  var swiper = new Swiper(".swiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    loop: true,
    coverflowEffect: {
      rotate: 50,
      stretch: 0,
      depth: 100,
      modifier: 1,
      slideShadows: true,
    },
    pagination: {
      el: ".swiper-pagination",
    },
  });

  //exclude user on click remove button in bp-friend-follow-swiper-widget
  $('.swipe-cross-button a').on('click', function(e){
    e.preventDefault();
    var mem_id = $(this).data('mem_id');
    $.ajax({
      url: bffs_ajax_object.ajaxurl,
      type: "post",
      data: {
        'action': 'bffs_remove_user',
        'mem_id': mem_id,
        'nonce': bffs_ajax_object.ajax_nonce
      },
      success: function (data) {
        swiper.slideNext();
      },
    }); 
  })

})(jQuery);

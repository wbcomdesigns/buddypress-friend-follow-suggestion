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
            $('.widget_bp_friend_follow_suggestion_widget,.widget_bp_friend_suggestion_slider_widget').on('click', '[data-bp-btn-action]', bp.Nouveau, bp.Nouveau.buttonAction);
        }

    };

    $(document).on('ready', function() {
        BPFrienFollow.init();
		/**
		 * jTinder initialization
		 */		
		$(".bp-friend-swiper-slider").jTinder({			
			onDislike: function (item) {				
                $(item).find('.bffs_user_buttons .dislike').trigger('click');
				let parent_id = $(item).parents('.bp-friend-swiper-slider').attr( 'id' ); 
				item.remove();
				let panes2 = $(">ul>li", '#' + parent_id );
				let pane_count2 = panes2.length - 1;
				if(pane_count2 === 0 ){
					$('.like').hide();
					$('.dislike').hide();
				}
			},
			onLike: function (item) {				
				$(item).find('a.friendship-button[data-bp-btn-action]').trigger( 'click');
				let parent_id = $(item).parents('.bp-friend-swiper-slider').attr( 'id' );
				item.remove();
				let panes2 = $(">ul>li", '#' + parent_id);				
				let pane_count2 = panes2.length - 1;

				if(pane_count2 === 0 ){
					$('.like').hide();
					$('.dislike').hide();
				}
			},
			animationRevertSpeed: 200,
			animationSpeed: 400,
			threshold: 1,
			likeSelector: '.like',
			dislikeSelector: '.dislike'
		});

        $(document).on('click', '.bffs_user_buttons .dislike', function (e) {
            let rejectedMemberID = $(this).parents('li.item-entry').data('bp-item-id');
           
            $.post(bffs_ajax_object.ajaxurl,
                {
                    action:'member_dislike',
                    member_id: rejectedMemberID,
                    nonce: bffs_ajax_object.security
                },
                function(responce){
                    console.log(responce);
                }

            );
        });

		/**
		 * Set button action to trigger jTinder like & dislike.
		 */
		/*
		$( '.bffs_user_buttons .bp-friend-like, .bffs_user_buttons .bp-friend-dislike').click(function(e){
			e.preventDefault();
			$( this ).parents('.bp-friend-swiper-slider').jTinder($(this).attr('class'));
		});
		*/
		$( document ).on( 'click', '.bffs_user_buttons .like, .bffs_user_buttons .dislike', function(e) {
			e.preventDefault();
			$( this ).parents('.bp-friend-swiper-slider').jTinder( $(this).attr('class') );
		});

        
    });

    // Add swiper layout for the bp-friend-follow-swiper-widget
    // var swiper = new Swiper(".swiper", {
    //     effect: "coverflow",
    //     grabCursor: true,
    //     centeredSlides: true,
    //     slidesPerView: "auto",
    //     loop: true,
    //     navigation: {
    //         nextEl: ".swiper-button-next",
    //         prevEl: ".swiper-button-prev",
    //     },
    //     coverflowEffect: {
    //         rotate: 50,
    //         stretch: 0,
    //         depth: 100,
    //         modifier: 1,
    //         slideShadows: true,
    //     },
    //     pagination: {
    //         el: ".swiper-pagination",
    //     },
    // });

    // var swiper = new Swiper(".horizontal_swiper", {
    //     grabCursor: true,
    //     loop: true,
    //     speed: 500,
    //     pagination: {
    //         el: ".swiper-pagination",
    //     },
    //     navigation: {
    //         nextEl: ".swiper-button-next",
    //         prevEl: ".swiper-button-prev",
    //     },
    // });

    //exclude user on click remove button in bp-friend-follow-swiper-widget
    var clickCount = 1;
    $('.swipe-cross-button a').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.bffs-swipe-slides').remove();
        var mem_id = $(this).data('mem_id');
        var max_mem = $(this).data('max_mem');
        var total_mem = $(this).data('total_mem');        
        var friend_remove = new Swiper(".swiper", {
            effect: "coverflow",
        });
        if (clickCount == max_mem) {
            location.reload();
        } else if (total_mem < max_mem) {
            location.reload();
        }
        $.ajax({
            url: bffs_ajax_object.ajaxurl,
            type: "post",
            data: {
                'action': 'bffs_remove_user',
                'mem_id': mem_id,
                'nonce': bffs_ajax_object.ajax_nonce
            },
            success: function(data) {
                friend_remove.slideNext();
            },
        });
        clickCount++;
    })

    // Add ajax on add friend button
    var clickCount = 1;
    $('.bffs-friendship-button').on('click', function() {
        $(this).closest('.bffs-swipe-slides').remove();
        var mem_id = $(this).data('mem_id');
        var max_mem = $(this).data('max_mem');
        var total_mem = $(this).data('total_mem');
        var swiper_friend = new Swiper(".swiper", {
            effect: "coverflow",
        });
        if ( clickCount == max_mem ){
            location.reload();
        } else if( total_mem < max_mem ){
            location.reload();
        }
        $.ajax({
            url: bffs_ajax_object.ajaxurl,
            type: "post",
            data: {
                'action': 'bffs_add_friend',
                'mem_id': mem_id,
                'nonce': bffs_ajax_object.ajax_nonce
            },
            success: function(data) {                
                if (true == data.success) {
                    swiper_friend.slideNext();
                    
                }
            },
        });
        clickCount++;
    });

    var clickCount = 1;
    // Add ajax on follow button
    $('.bffs-follow-button').on('click', function() {
        $(this).closest('.bffs-swipe-slides').remove();
        var mem_id = $(this).data('mem_id');
        var max_mem = $(this).data('max_mem');
        var total_mem = $(this).data('total_mem');
        var swiper_follow = new Swiper(".swiper", {
            effect: "coverflow",
        });
        if (clickCount == max_mem) {
            location.reload();
        } else if (total_mem < max_mem) {
            location.reload();
        }
        $.ajax({
            url: bffs_ajax_object.ajaxurl,
            type: "post",
            data: {
                'action': 'bffs_follow_button',
                'mem_id': mem_id,
                'nonce': bffs_ajax_object.ajax_nonce
            },
            success: function(data) {
                if (true == data.success) {
                    swiper_follow.slideNext();
                }
            },
        });
        clickCount++;
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
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
		});
	}

})(jQuery);
/*
 * jTinder v.1.0.0
 * https://github.com/do-web/jTinder
 * Requires jQuery 1.7+, jQuery transform2d
 *
 * Copyright (c) 2014, Dominik Weber
 * Licensed under GPL Version 2.
 * https://github.com/do-web/jTinder/blob/master/LICENSE
 */
;(function ($, window, document, undefined) {
    var pluginName = "jTinder",
        defaults = {
            onDislike: null,
            onLike: null,
            animationRevertSpeed: 200,
            animationSpeed: 400,
            threshold: 1,
            likeSelector: '.like',
            dislikeSelector: '.dislike'
        };

    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;

        // Instance-specific variables
        this.container = null;
        this.panes = null;
        this.xStart = 0;
        this.yStart = 0;
        this.touchStart = false;
        this.posX = 0;
        this.posY = 0;
        this.lastPosX = 0;
        this.lastPosY = 0;
        this.pane_width = 0;
        this.pane_count = 0;
        this.current_pane = 0;

        this.init();
    }

    Plugin.prototype = {
        init: function () {
            this.container = $(">ul", this.element);
            this.panes = $(">ul>li", this.element);
            this.pane_width = this.container.width();
            this.pane_count = this.panes.length;
            this.current_pane = this.pane_count - 1;

            $(this.element).bind('touchstart mousedown', this.handler.bind(this));
            $(".bffs_swiper_layout_wrapper").bind('touchmove mousemove', this.handler.bind(this));
            $(".bffs_swiper_layout_wrapper").bind('touchend mouseup', this.handler.bind(this));
        },

        showPane: function (index) {
            this.panes.eq(this.current_pane).hide();
            this.current_pane = index;
        },

        next: function () {
            if(this.current_pane !== 1) {
                return this.showPane(this.current_pane - 1);
            }
        },

        dislike: function() {
            var self = this;
            this.panes.eq(this.current_pane).animate({"transform": "translate(-" + (this.pane_width) + "px," + (this.pane_width * -1.5) + "px) rotate(-60deg)"}, this.settings.animationSpeed, function () {
                if(self.settings.onDislike) {
                    self.settings.onDislike(self.panes.eq(self.current_pane));
                }
                self.next();
            });
        },

        like: function() {
            var self = this;
            this.panes.eq(this.current_pane).animate({"transform": "translate(" + (this.pane_width) + "px," + (this.pane_width * -1.5) + "px) rotate(60deg)"}, this.settings.animationSpeed, function () {
                if(self.settings.onLike) {
                    self.settings.onLike(self.panes.eq(self.current_pane));
                }
                self.next();
            });
        },

        handler: function (ev) {
            //ev.preventDefault();

            switch (ev.type) {
                case 'touchstart':
                case 'mousedown':
                    if(this.touchStart === false) {
                        this.touchStart = true;
                        this.xStart = (typeof ev.pageX == 'undefined') ? ev.originalEvent.touches[0].pageX : ev.pageX;
                        this.yStart = (typeof ev.pageY == 'undefined') ? ev.originalEvent.touches[0].pageY : ev.pageY;
                    }
                    break;

                case 'mousemove':
                case 'touchmove':
                    if(this.touchStart === true) {
                        var pageX = (typeof ev.pageX == 'undefined') ? ev.originalEvent.touches[0].pageX : ev.pageX;
                        var pageY = (typeof ev.pageY == 'undefined') ? ev.originalEvent.touches[0].pageY : ev.pageY;
                        var deltaX = parseInt(pageX) - parseInt(this.xStart);
                        var deltaY = parseInt(pageY) - parseInt(this.yStart);
                        var percent = ((100 / this.pane_width) * deltaX) / this.pane_count;

                        this.posX = deltaX + this.lastPosX;
                        this.posY = deltaY + this.lastPosY;

                        this.panes.eq(this.current_pane).css("transform", "translate(" + this.posX + "px," + this.posY + "px) rotate(" + (percent / 2) + "deg)");

                        var opa = (Math.abs(deltaX) / this.settings.threshold) / 100 + 0.2;
                        if(opa > 1.0) {
                            opa = 1.0;
                        }
                        if (this.posX >= 0) {
                            this.panes.eq(this.current_pane).find(this.settings.likeSelector).css('opacity', opa);
                            this.panes.eq(this.current_pane).find(this.settings.dislikeSelector).css('opacity', 0);
                        } else if (this.posX < 0) {
                            this.panes.eq(this.current_pane).find(this.settings.dislikeSelector).css('opacity', opa);
                            this.panes.eq(this.current_pane).find(this.settings.likeSelector).css('opacity', 0);
                        }
                    }
                    break;

                case 'mouseup':
                case 'touchend':
                    var self = this;
                    if (this.touchStart == true) {
                        var pageX = (typeof ev.pageX == 'undefined') ? ev.originalEvent.changedTouches[0].pageX : ev.pageX;
                        var pageY = (typeof ev.pageY == 'undefined') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY;
                        var deltaX = parseInt(pageX) - parseInt(this.xStart);
                        var deltaY = parseInt(pageY) - parseInt(this.yStart);

                        this.posX = deltaX + this.lastPosX;
                        this.posY = deltaY + this.lastPosY;
                        var opa = Math.abs((Math.abs(deltaX) / this.settings.threshold) / 100 + 0.2);

                        if (opa >= 1) {
                            if (this.posX > 0) {
                                this.panes.eq(this.current_pane).animate({"transform": "translate(" + (this.pane_width) + "px," + (this.posY + this.pane_width) + "px) rotate(60deg)"}, this.settings.animationSpeed, function () {
                                    if(self.settings.onLike) {
                                        self.settings.onLike(self.panes.eq(self.current_pane));
                                    }
                                    self.next();
                                });
                            } else {
                                this.panes.eq(this.current_pane).animate({"transform": "translate(-" + (this.pane_width) + "px," + (this.posY + this.pane_width) + "px) rotate(-60deg)"}, this.settings.animationSpeed, function () {
                                    if(self.settings.onDislike) {
                                        self.settings.onDislike(self.panes.eq(self.current_pane));
                                    }
                                    self.next();
                                });
                            }
                        } else {
                            this.lastPosX = 0;
                            this.lastPosY = 0;
                            this.panes.eq(this.current_pane).animate({"transform": "translate(0px,0px) rotate(0deg)"}, this.settings.animationRevertSpeed);
                            this.panes.eq(this.current_pane).find(this.settings.likeSelector).animate({"opacity": 0}, this.settings.animationRevertSpeed);
                            this.panes.eq(this.current_pane).find(this.settings.dislikeSelector).animate({"opacity": 0}, this.settings.animationRevertSpeed);
                        }
                        this.touchStart = false;
                    }
                    break;
            }
        }
    };

    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
            else if ($.isFunction(Plugin.prototype[options])) {
                $.data(this, 'plugin_' + pluginName)[options]();
            }
        });

        return this;
    };

})(jQuery, window, document);

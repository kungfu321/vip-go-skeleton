/* analytics */

/* global jQuery:{} */

var tdAnalytics = {};

(function () {
    "use strict";

    tdAnalytics = {

        _fbPixelId: '',
        _gaTrackingId: '',
        _gaErrors: [],
        _fbErrors: [],

        init: function() {

            //disabled on tdc iframe
            if ( tdAnalytics._inIframe() === true ) {
                return;
            }

            // check the google analytics tracking id
            if ( typeof window.dataLayer !== 'undefined' ) {
                window.dataLayer.forEach( function (element, index, array) {
                    if( element[0] === 'config' ) {
                        tdAnalytics._gaTrackingId = element[1];
                    }
                });
            }

            // check the facebook pixel tracking id
            if ( typeof fbq !== 'undefined' ) {
                // wait for fb to do its "thing"(to fix fbq.getState is not a function)
                setTimeout( function () {
                    // set the tdAnalytics._fbPixelId property
                    tdAnalytics._fbPixelId = fbq.getState().pixels[0].id;
                }, 500);

                // setTimeout( function () {
                //     console.log(tdAnalytics._fbPixelId);
                // }, 160);
            }

            // console.log(tdAnalytics._gaTrackingId);
            // console.log(tdAnalytics._fbPixelId);

            jQuery( 'body' ).on( 'click',
                '.tdm_block_button .tds-button .tdm-btn, ' +
                '.tdm_block_icon_box .tds-button .tdm-btn, ' +
                '.td_block_single_image .td_single_image_bg, ' +
                '.tds-newsletter .tdn-btn-wrap button',
            function(event) {

                if ( !event.target.classList.contains('tdn-submit-btn') ) {
                    event.preventDefault();
                }

                var $this = jQuery(this),
                    eventData = {
                        ga: {},
                        fb: {},
                        eventTarget: '',
                        eventTargetAtt: event.target.getAttribute('target')
                    };

                if( event.target.classList.contains('tdm-btn-text') ) {
                    eventData.eventTarget = event.target.parentElement.getAttribute('href');
                } else {
                    eventData.eventTarget = event.target.getAttribute('href');
                }

                if ( $this.data('ga-event-action') !== undefined  ) {
                    eventData.ga.eventAction = $this.data('ga-event-action');
                }

                if ( $this.data('ga-event-cat') !== undefined  ) {
                    eventData.ga.eventCategory = $this.data('ga-event-cat');
                }

                if ( $this.data('ga-event-label') !== undefined  ) {
                    eventData.ga.eventLabel = $this.data('ga-event-label');
                }

                if ( $this.data('fb-event-name') !== undefined  ) {
                    eventData.fb.eventName = $this.data('fb-event-name');
                }

                if ( $this.data('fb-event-content-name') !== undefined  ) {
                    eventData.fb.eventContentName = $this.data('fb-event-content-name');
                }

                tdAnalytics._trackEvent(eventData);

                //tdAnalytics._displayErrors();

            });
        },

        _trackEvent: function(tdEvent) {

            //console.log(tdEvent);

            // check for google analytics required fields
            if (typeof tdEvent.ga.eventAction === 'undefined') {
                tdAnalytics._gaErrors.push({
                    errorId: 'eventActionError',
                    errorMessage: 'Google analytics event action is undefined.'
                });
            }

            if (typeof tdEvent.ga.eventCategory === 'undefined') {
                tdAnalytics._gaErrors.push({
                    errorId: 'eventCategory',
                    errorMessage: 'Google analytics event category is undefined.'
                });
            }

            // check for google analytics
            if ( ! window[window['GoogleAnalyticsObject'] || 'ga'] ) {
                tdAnalytics._gaErrors.push({
                    errorId: 'GoogleAnalyticsPageCode',
                    errorMessage: 'Google Analytics code is not loaded on the current page.'
                });
            }

            // check for google analytics tracking id
            if ( tdAnalytics._gaTrackingId === '' ) {
                tdAnalytics._gaErrors.push({
                    errorId: 'GoogleAnalyticsTrackingId',
                    errorMessage: 'Google Analytics TrackingId is missing on the current page.'
                });
            }
            
            // send the ga track event
            if ( tdAnalytics._gaErrors.length === 0 ) {
                gtag( 'event', tdEvent.ga.eventAction,
                    {
                        'event_category': tdEvent.ga.eventCategory,
                        'event_label': tdEvent.ga.eventLabel,
                        'event_callback': function () {
                            console.log(' %c GA Success','color: green; font-weight: bold;');
                        }
                    }
                );
            }

            // check for fb pixel events
            if ( typeof window.fbq === 'undefined' ) {
                tdAnalytics._fbErrors.push({
                    errorId: 'FacebookPixelPageCode',
                    errorMessage: 'Facebook Pixel events code is not loaded on the current page.'
                });
            }

            // check for fb pixel analytics tracking id
            if ( tdAnalytics._fbPixelId === '' ) {
                tdAnalytics._fbErrors.push({
                    errorId: 'FacebookPixelTrackingId',
                    errorMessage: 'Facebook Pixel TrackingId is missing on the current page.'
                });
            }

            if (typeof tdEvent.fb.eventName === 'undefined') {
                tdAnalytics._fbErrors.push({
                    errorId: 'FacebookPixelEventName',
                    errorMessage: 'Facebook pixel standard event name is not set ( undefined ).'
                });
            }

            // send the fb pixel track event
            if ( tdAnalytics._fbErrors.length === 0 ) {

                if ( typeof tdEvent.fb.eventContentName !== 'undefined' ){
                    fbq( 'track', tdEvent.fb.eventName, {content_name: tdEvent.fb.eventContentName} );
                } else {
                    fbq( 'track', tdEvent.fb.eventName );
                }

                console.log(' %c FB track sent','color: green; font-weight: bold;');
            }

            // set a 150ms timeout to redirect
            // - we need this to allow ga/fb track events to be sent before the page navigates to the new url
            setTimeout( function() {

                if ( !tdEvent.eventTarget ) {
                    return;
                }

                if ( tdEvent.eventTargetAtt === '_blank' ) {
                    window.open(tdEvent.eventTarget);
                } else {
                    window.location = tdEvent.eventTarget;
                }
            }, 150);

        },

        _inIframe: function() {
            try {
                return window.self !== window.top;
            } catch (e) {
                return true;
            }
        },

        _displayErrors: function() {

            if ( tdAnalytics._gaErrors.length > 0 ) {
                while ( tdAnalytics._gaErrors.length > 0 ) {
                    var ga_item = tdAnalytics._gaErrors.shift();
                    console.warn( ga_item.errorId + ': ' + ga_item.errorMessage );
                }
            }

            if ( tdAnalytics._fbErrors.length > 0 ) {
                while ( tdAnalytics._fbErrors.length > 0 ) {
                    var fb_item = tdAnalytics._fbErrors.shift();
                    console.warn( fb_item.errorId + ': ' + fb_item.errorMessage );
                }
            }
        }

    }

})();

//init analytics
jQuery(window).ready(function() {
    tdAnalytics.init();
});

// jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
jQuery.easing.jswing=jQuery.easing.swing;
jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,a,c,b,d){return jQuery.easing[jQuery.easing.def](e,a,c,b,d)},easeInQuad:function(e,a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function(e,a,c,b,d){return-b*(a/=d)*(a-2)+c},easeInOutQuad:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a+c:-b/2*(--a*(a-2)-1)+c},easeInCubic:function(e,a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function(e,a,c,b,d){return b*((a=a/d-1)*a*a+1)+c},easeInOutCubic:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a+c:
    b/2*((a-=2)*a*a+2)+c},easeInQuart:function(e,a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function(e,a,c,b,d){return-b*((a=a/d-1)*a*a*a-1)+c},easeInOutQuart:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a+c:-b/2*((a-=2)*a*a*a-2)+c},easeInQuint:function(e,a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function(e,a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c},easeInOutQuint:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a*a+c:b/2*((a-=2)*a*a*a*a+2)+c},easeInSine:function(e,a,c,b,d){return-b*Math.cos(a/
    d*(Math.PI/2))+b+c},easeOutSine:function(e,a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c},easeInOutSine:function(e,a,c,b,d){return-b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function(e,a,c,b,d){return 0==a?c:b*Math.pow(2,10*(a/d-1))+c},easeOutExpo:function(e,a,c,b,d){return a==d?c+b:b*(-Math.pow(2,-10*a/d)+1)+c},easeInOutExpo:function(e,a,c,b,d){return 0==a?c:a==d?c+b:1>(a/=d/2)?b/2*Math.pow(2,10*(a-1))+c:b/2*(-Math.pow(2,-10*--a)+2)+c},easeInCirc:function(e,a,c,b,d){return-b*(Math.sqrt(1-(a/=d)*
    a)-1)+c},easeOutCirc:function(e,a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function(e,a,c,b,d){return 1>(a/=d/2)?-b/2*(Math.sqrt(1-a*a)-1)+c:b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(0==a)return c;if(1==(a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f))+c},easeOutElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(0==a)return c;if(1==
    (a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return g*Math.pow(2,-10*a)*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInOutElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(0==a)return c;if(2==(a/=d/2))return c+b;f||(f=d*0.3*1.5);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return 1>a?-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+c:0.5*g*Math.pow(2,-10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInBack:function(e,a,c,b,d,f){void 0==
    f&&(f=1.70158);return b*(a/=d)*a*((f+1)*a-f)+c},easeOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return b*((a=a/d-1)*a*((f+1)*a+f)+1)+c},easeInOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return 1>(a/=d/2)?b/2*a*a*(((f*=1.525)+1)*a-f)+c:b/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+c},easeInBounce:function(e,a,c,b,d){return b-jQuery.easing.easeOutBounce(e,d-a,0,b,d)+c},easeOutBounce:function(e,a,c,b,d){return(a/=d)<1/2.75?b*7.5625*a*a+c:a<2/2.75?b*(7.5625*(a-=1.5/2.75)*a+0.75)+c:a<2.5/2.75?
    b*(7.5625*(a-=2.25/2.75)*a+0.9375)+c:b*(7.5625*(a-=2.625/2.75)*a+0.984375)+c},easeInOutBounce:function(e,a,c,b,d){return a<d/2?0.5*jQuery.easing.easeInBounce(e,2*a,0,b,d)+c:0.5*jQuery.easing.easeOutBounce(e,2*a-d,0,b,d)+0.5*b+c}});



/*
 * Supersubs v0.3b - jQuery plugin
 * Copyright (c) 2013 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 *
 * This plugin automatically adjusts submenu widths of suckerfish-style menus to that of
 * their longest list item children. If you use this, please expect bugs and report them
 * to the jQuery Google Group with the word 'Superfish' in the subject line.
 *
 */

(function($){ // $ will refer to jQuery within this closure

    $.fn.supersubs = function(options){
        var opts = $.extend({}, $.fn.supersubs.defaults, options);
        // return original object to support chaining
        return this.each(function() {
            // cache selections
            var $$ = $(this);
            // support metadata
            var o = $.meta ? $.extend({}, opts, $$.data()) : opts;
            // cache all ul elements and show them in preparation for measurements
            var $ULs = $$.find('ul').show();
            // get the font size of menu.
            // .css('fontSize') returns various results cross-browser, so measure an em dash instead
            var fontsize = $('<li id="menu-fontsize">&#8212;</li>').css({
                'padding' : 0,
                'position' : 'absolute',
                'top' : '-999em',
                'width' : 'auto'
            }).appendTo($$)[0].clientWidth; //clientWidth is faster than .width()
            // remove em dash
            $('#menu-fontsize').remove();
            // loop through each ul in menu
            $ULs.each(function(i) {
                // cache this ul
                var $ul = $(this);
                // get all (li) children of this ul
                var $LIs = $ul.children();
                // get all anchor grand-children
                var $As = $LIs.children('a');
                // force content to one line and save current float property
                var liFloat = $LIs.css('white-space','nowrap').css('float');
                // remove width restrictions and floats so elements remain vertically stacked
                $ul.add($LIs).add($As).css({
                    'float' : 'none',
                    'width'	: 'auto'
                });
                // this ul will now be shrink-wrapped to longest li due to position:absolute
                // so save its width as ems.
                var emWidth = $ul[0].clientWidth / fontsize;
                // add more width to ensure lines don't turn over at certain sizes in various browsers
                emWidth += o.extraWidth;
                // restrict to at least minWidth and at most maxWidth
                if (emWidth > o.maxWidth)		{ emWidth = o.maxWidth; }
                else if (emWidth < o.minWidth)	{ emWidth = o.minWidth; }
                emWidth += 'em';
                // set ul to width in ems
                if ( o.applyMin ) {
                    $ul.css('min-width', emWidth);
                } else {
                    $ul.css('width', emWidth);
                }
                // restore li floats to avoid IE bugs
                // set li width to full width of this ul
                // revert white-space to normal
                $LIs.css({
                    'float' : liFloat,
                    'width' : '100%',
                    'white-space' : 'normal'
                })
                // update offset position of descendant ul to reflect new width of parent.
                // set it to 100% in case it isn't already set to this in the CSS
                    .each(function(){
                        var $childUl = $(this).children('ul');
                        var offsetDirection = $childUl.css('left') !== undefined ? 'left' : 'right';
                        $childUl.css(offsetDirection,'100%');
                    });
            }).hide();

        });
    };
    // expose defaults
    $.fn.supersubs.defaults = {
        minWidth		: 9,		// requires em unit.
        maxWidth		: 25,		// requires em unit.
        extraWidth		: 0,		// extra width can ensure lines don't sometimes turn over due to slight browser differences in how they round-off values,
        applyMin        : false     // change 'width' to 'min-width' if true
    };

})(jQuery); // plugin code ends




/*
 * iosSlider - http://iosscripts.com/iosslider/
 *
 * Touch Enabled, Responsive jQuery Horizontal Content Slider/Carousel/Image Gallery Plugin
 *
 * A jQuery plugin which allows you to integrate a customizable, cross-browser
 * content slider into your web presence. Designed for use as a content slider, carousel,
 * scrolling website banner, or image gallery.
 *
 * Copyright (c) 2013 Marc Whitbread
 *
 * Version: v1.3.43 (06/17/2014)
 * Minimum requirements: jQuery v1.4+
 *
 * Advanced requirements:
 * 1) jQuery bind() click event override on slide requires jQuery v1.6+
 *
 * Terms of use:
 *
 * 1) iosSlider is licensed under the Creative Commons – Attribution-NonCommercial 3.0 License.
 * 2) You may use iosSlider free for personal or non-profit purposes, without restriction.
 *	  Attribution is not required but always appreciated. For commercial projects, you
 *	  must purchase a license. You may download and play with the script before deciding to
 *	  fully implement it in your project. Making sure you are satisfied, and knowing iosSlider
 *	  is the right script for your project is paramount.
 * 3) You are not permitted to make the resources found on iosscripts.com available for
 *    distribution elsewhere "as is" without prior consent. If you would like to feature
 *    iosSlider on your site, please do not link directly to the resource zip files. Please
 *    link to the appropriate page on iosscripts.com where users can find the download.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * iosSlider - http://iosscripts.com/iosslider/
 *
 * Touch Enabled, Responsive jQuery Horizontal Content Slider/Carousel/Image Gallery Plugin
 *
 * A jQuery plugin which allows you to integrate a customizable, cross-browser
 * content slider into your web presence. Designed for use as a content slider, carousel,
 * scrolling website banner, or image gallery.
 *
 * Copyright (c) 2013 Marc Whitbread
 *
 * Version: v1.3.43 (06/17/2014)
 * Minimum requirements: jQuery v1.4+
 *
 * Advanced requirements:
 * 1) jQuery bind() click event override on slide requires jQuery v1.6+
 *
 * Terms of use:
 *
 * 1) iosSlider is licensed under the Creative Commons – Attribution-NonCommercial 3.0 License.
 * 2) You may use iosSlider free for personal or non-profit purposes, without restriction.
 *	  Attribution is not required but always appreciated. For commercial projects, you
 *	  must purchase a license. You may download and play with the script before deciding to
 *	  fully implement it in your project. Making sure you are satisfied, and knowing iosSlider
 *	  is the right script for your project is paramount.
 * 3) You are not permitted to make the resources found on iosscripts.com available for
 *    distribution elsewhere "as is" without prior consent. If you would like to feature
 *    iosSlider on your site, please do not link directly to the resource zip files. Please
 *    link to the appropriate page on iosscripts.com where users can find the download.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

;(function($) {

    /* global variables */
    var scrollbarNumber = 0;
    var xScrollDistance = 0;
    var yScrollDistance = 0;
    var scrollIntervalTime = 10;
    var scrollbarDistance = 0;
    var isTouch = 'ontouchstart' in window || (navigator.msMaxTouchPoints > 0);
    var supportsOrientationChange = 'onorientationchange' in window;
    var isWebkit = false;
    var has3DTransform = false;
    var isIe7 = false;
    var isIe8 = false;
    var isIe9 = false;
    var isIe = false;
    var isGecko = false;
    var grabOutCursor = 'pointer';
    var grabInCursor = 'pointer';
    var onChangeEventLastFired = new Array();
    var autoSlideTimeouts = new Array();
    var iosSliders = new Array();
    var iosSliderSettings = new Array();
    var isEventCleared = new Array();
    var slideTimeouts = new Array();
    var activeChildOffsets = new Array();
    var activeChildInfOffsets = new Array();
    var infiniteSliderOffset = new Array();
    var sliderMin = new Array();
    var sliderMax = new Array();
    var sliderAbsMax = new Array();
    var touchLocks = new Array();

    /* private functions */
    var helpers = {

        showScrollbar: function(settings, scrollbarClass) {

            if(settings.scrollbarHide) {
                $('.' + scrollbarClass).css({
                    opacity: settings.scrollbarOpacity,
                    filter: 'alpha(opacity:' + (settings.scrollbarOpacity * 100) + ')'
                });
            }

        },

        hideScrollbar: function(settings, scrollTimeouts, j, distanceOffsetArray, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber) {

            if(settings.scrollbar && settings.scrollbarHide) {

                for(var i = j; i < j+25; i++) {

                    scrollTimeouts[scrollTimeouts.length] = helpers.hideScrollbarIntervalTimer(scrollIntervalTime * i, distanceOffsetArray[j], ((j + 24) - i) / 24, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber, settings);

                }

            }

        },

        hideScrollbarInterval: function(newOffset, opacity, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber, settings) {

            scrollbarDistance = (newOffset * -1) / (sliderMax[sliderNumber]) * (stageWidth - scrollMargin - scrollBorder - scrollbarWidth);

            helpers.setSliderOffset('.' + scrollbarClass, scrollbarDistance);

            $('.' + scrollbarClass).css({
                opacity: settings.scrollbarOpacity * opacity,
                filter: 'alpha(opacity:' + (settings.scrollbarOpacity * opacity * 100) + ')'
            });

        },

        slowScrollHorizontalInterval: function(node, slideNodes, newOffset, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, activeChildOffset, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, endOffset, settings) {

            if(settings.infiniteSlider) {

                if((newOffset <= (sliderMax[sliderNumber] * -1)) || (newOffset <= (sliderAbsMax[sliderNumber] * -1))) {

                    var scrollerWidth = $(node).width();

                    if(newOffset <= (sliderAbsMax[sliderNumber] * -1)) {

                        var sum = originalOffsets[0] * -1;
                        $(slideNodes).each(function(i) {

                            helpers.setSliderOffset($(slideNodes)[i], sum + centeredSlideOffset);
                            if(i < childrenOffsets.length) {
                                childrenOffsets[i] = sum * -1;
                            }
                            sum = sum + slideNodeOuterWidths[i];

                        });

                        newOffset = newOffset + childrenOffsets[0] * -1;
                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;
                        infiniteSliderOffset[sliderNumber] = 0;

                    }

                    while(newOffset <= (sliderMax[sliderNumber] * -1)) {

                        var lowSlideNumber = 0;
                        var lowSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                        $(slideNodes).each(function(i) {

                            if(helpers.getSliderOffset(this, 'x') < lowSlideOffset) {
                                lowSlideOffset = helpers.getSliderOffset(this, 'x');
                                lowSlideNumber = i;
                            }

                        });

                        var tempOffset = sliderMin[sliderNumber] + scrollerWidth;
                        helpers.setSliderOffset($(slideNodes)[lowSlideNumber], tempOffset);

                        sliderMin[sliderNumber] = childrenOffsets[1] * -1 + centeredSlideOffset;
                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                        childrenOffsets.splice(0, 1);
                        childrenOffsets.splice(childrenOffsets.length, 0, tempOffset * -1 + centeredSlideOffset);

                        infiniteSliderOffset[sliderNumber]++;

                    }

                }

                if((newOffset >= (sliderMin[sliderNumber] * -1)) || (newOffset >= 0)) {

                    var scrollerWidth = $(node).width();

                    if(newOffset > 0) {

                        var sum = originalOffsets[0] * -1;
                        $(slideNodes).each(function(i) {

                            helpers.setSliderOffset($(slideNodes)[i], sum + centeredSlideOffset);
                            if(i < childrenOffsets.length) {
                                childrenOffsets[i] = sum * -1;
                            }
                            sum = sum + slideNodeOuterWidths[i];

                        });

                        newOffset = newOffset - childrenOffsets[0] * -1;
                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;
                        infiniteSliderOffset[sliderNumber] = numberOfSlides;

                        while(((childrenOffsets[0] * -1 - scrollerWidth + centeredSlideOffset) > 0)) {

                            var highSlideNumber = 0;
                            var highSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                            $(slideNodes).each(function(i) {

                                if(helpers.getSliderOffset(this, 'x') > highSlideOffset) {
                                    highSlideOffset = helpers.getSliderOffset(this, 'x');
                                    highSlideNumber = i;
                                }

                            });

                            var tempOffset = sliderMin[sliderNumber] - slideNodeOuterWidths[highSlideNumber];
                            helpers.setSliderOffset($(slideNodes)[highSlideNumber], tempOffset);

                            childrenOffsets.splice(0, 0, tempOffset * -1 + centeredSlideOffset);
                            childrenOffsets.splice(childrenOffsets.length-1, 1);

                            sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                            sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                            infiniteSliderOffset[sliderNumber]--;
                            activeChildOffsets[sliderNumber]++;

                        }

                    }

                    while(newOffset > (sliderMin[sliderNumber] * -1)) {

                        var highSlideNumber = 0;
                        var highSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                        $(slideNodes).each(function(i) {

                            if(helpers.getSliderOffset(this, 'x') > highSlideOffset) {
                                highSlideOffset = helpers.getSliderOffset(this, 'x');
                                highSlideNumber = i;
                            }

                        });

                        var tempOffset = sliderMin[sliderNumber] - slideNodeOuterWidths[highSlideNumber];
                        helpers.setSliderOffset($(slideNodes)[highSlideNumber], tempOffset);

                        childrenOffsets.splice(0, 0, tempOffset * -1 + centeredSlideOffset);
                        childrenOffsets.splice(childrenOffsets.length-1, 1);

                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                        infiniteSliderOffset[sliderNumber]--;

                    }

                }

            }

            var slideChanged = false;
            var newChildOffset = helpers.calcActiveOffset(settings, newOffset, childrenOffsets, stageWidth, infiniteSliderOffset[sliderNumber], numberOfSlides, activeChildOffset, sliderNumber);
            var tempOffset = (newChildOffset + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

            if(settings.infiniteSlider) {

                if(tempOffset != activeChildInfOffsets[sliderNumber]) slideChanged = true;

            } else {

                if(newChildOffset != activeChildOffsets[sliderNumber]) slideChanged = true;

            }

            if(slideChanged) {

                var args = new helpers.args('change', settings, node, $(node).children(':eq(' + tempOffset + ')'), tempOffset, endOffset);
                $(node).parent().data('args', args);

                if(settings.onSlideChange != '') {

                    settings.onSlideChange(args);

                }

            }

            activeChildOffsets[sliderNumber] = newChildOffset;
            activeChildInfOffsets[sliderNumber] = tempOffset;

            newOffset = Math.floor(newOffset);

            if(sliderNumber != $(node).parent().data('args').data.sliderNumber) return true;
            helpers.setSliderOffset(node, newOffset);

            if(settings.scrollbar) {

                scrollbarDistance = Math.floor((newOffset * -1 - sliderMin[sliderNumber] + centeredSlideOffset) / (sliderMax[sliderNumber] - sliderMin[sliderNumber] + centeredSlideOffset) * (scrollbarStageWidth - scrollMargin - scrollbarWidth));
                var width = scrollbarWidth - scrollBorder;

                if(newOffset >= (sliderMin[sliderNumber] * -1 + centeredSlideOffset)) {

                    width = scrollbarWidth - scrollBorder - (scrollbarDistance * -1);

                    helpers.setSliderOffset($('.' + scrollbarClass), 0);

                    $('.' + scrollbarClass).css({
                        width: width + 'px'
                    });

                } else if(newOffset <= ((sliderMax[sliderNumber] * -1) + 1)) {

                    width = scrollbarStageWidth - scrollMargin - scrollBorder - scrollbarDistance;

                    helpers.setSliderOffset($('.' + scrollbarClass), scrollbarDistance);

                    $('.' + scrollbarClass).css({
                        width: width + 'px'
                    });

                } else {

                    helpers.setSliderOffset($('.' + scrollbarClass), scrollbarDistance);

                    $('.' + scrollbarClass).css({
                        width: width + 'px'
                    });

                }

            }

        },

        slowScrollHorizontal: function(node, slideNodes, scrollTimeouts, scrollbarClass, xScrollDistance, yScrollDistance, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, currentEventNode, snapOverride, centeredSlideOffset, settings) {

            var nodeOffset = helpers.getSliderOffset(node, 'x');
            var distanceOffsetArray = new Array();
            var xScrollDistanceArray = new Array();
            var snapDirection = 0;
            var maxSlideVelocity = 25 / 1024 * stageWidth;
            var changeSlideFired = false;
            frictionCoefficient = settings.frictionCoefficient;
            elasticFrictionCoefficient = settings.elasticFrictionCoefficient;
            snapFrictionCoefficient = settings.snapFrictionCoefficient;

            if((xScrollDistance > settings.snapVelocityThreshold) && settings.snapToChildren && !snapOverride) {
                snapDirection = 1;
            } else if((xScrollDistance < (settings.snapVelocityThreshold * -1)) && settings.snapToChildren && !snapOverride) {
                snapDirection = -1;
            }

            if(xScrollDistance < (maxSlideVelocity * -1)) {
                xScrollDistance = maxSlideVelocity * -1;
            } else if(xScrollDistance > maxSlideVelocity) {
                xScrollDistance = maxSlideVelocity;
            }

            if(!($(node)[0] === $(currentEventNode)[0])) {
                snapDirection = snapDirection * -1;
                xScrollDistance = xScrollDistance * -2;
            }

            var tempInfiniteSliderOffset = infiniteSliderOffset[sliderNumber];

            if(settings.infiniteSlider) {

                var tempSliderMin = sliderMin[sliderNumber];
                var tempSliderMax = sliderMax[sliderNumber];

            }

            var tempChildrenOffsets = new Array();
            var tempSlideNodeOffsets = new Array();

            for(var i = 0; i < childrenOffsets.length; i++) {

                tempChildrenOffsets[i] = childrenOffsets[i];

                if(i < slideNodes.length) {
                    tempSlideNodeOffsets[i] = helpers.getSliderOffset($(slideNodes[i]), 'x');
                }

            }

            while((xScrollDistance > 1) || (xScrollDistance < -1)) {

                xScrollDistance = xScrollDistance * frictionCoefficient;
                nodeOffset = nodeOffset + xScrollDistance;

                if(((nodeOffset > (sliderMin[sliderNumber] * -1)) || (nodeOffset < (sliderMax[sliderNumber] * -1))) && !settings.infiniteSlider) {
                    xScrollDistance = xScrollDistance * elasticFrictionCoefficient;
                    nodeOffset = nodeOffset + xScrollDistance;
                }

                if(settings.infiniteSlider) {

                    if(nodeOffset <= (tempSliderMax * -1)) {

                        var scrollerWidth = $(node).width();

                        var lowSlideNumber = 0;
                        var lowSlideOffset = tempSlideNodeOffsets[0];
                        for(var i = 0; i < tempSlideNodeOffsets.length; i++) {

                            if(tempSlideNodeOffsets[i] < lowSlideOffset) {
                                lowSlideOffset = tempSlideNodeOffsets[i];
                                lowSlideNumber = i;
                            }

                        }

                        var newOffset = tempSliderMin + scrollerWidth;
                        tempSlideNodeOffsets[lowSlideNumber] = newOffset;

                        tempSliderMin = tempChildrenOffsets[1] * -1 + centeredSlideOffset;
                        tempSliderMax = tempSliderMin + scrollerWidth - stageWidth;

                        tempChildrenOffsets.splice(0, 1);
                        tempChildrenOffsets.splice(tempChildrenOffsets.length, 0, newOffset * -1 + centeredSlideOffset);

                        tempInfiniteSliderOffset++;

                    }

                    if(nodeOffset >= (tempSliderMin * -1)) {

                        var scrollerWidth = $(node).width();

                        var highSlideNumber = 0;
                        var highSlideOffset = tempSlideNodeOffsets[0];
                        for(var i = 0; i < tempSlideNodeOffsets.length; i++) {

                            if(tempSlideNodeOffsets[i] > highSlideOffset) {
                                highSlideOffset = tempSlideNodeOffsets[i];
                                highSlideNumber = i;
                            }

                        }

                        var newOffset = tempSliderMin - slideNodeOuterWidths[highSlideNumber];
                        tempSlideNodeOffsets[highSlideNumber] = newOffset;

                        tempChildrenOffsets.splice(0, 0, newOffset * -1 + centeredSlideOffset);
                        tempChildrenOffsets.splice(tempChildrenOffsets.length-1, 1);

                        tempSliderMin = tempChildrenOffsets[0] * -1 + centeredSlideOffset;
                        tempSliderMax = tempSliderMin + scrollerWidth - stageWidth;

                        tempInfiniteSliderOffset--;

                    }

                }

                distanceOffsetArray[distanceOffsetArray.length] = nodeOffset;
                xScrollDistanceArray[xScrollDistanceArray.length] = xScrollDistance;

            }

            var slideChanged = false;
            var newChildOffset = helpers.calcActiveOffset(settings, nodeOffset, tempChildrenOffsets, stageWidth, tempInfiniteSliderOffset, numberOfSlides, activeChildOffsets[sliderNumber], sliderNumber);

            var tempOffset = (newChildOffset + tempInfiniteSliderOffset + numberOfSlides)%numberOfSlides;

            if(settings.snapToChildren) {

                if(settings.infiniteSlider) {

                    if(tempOffset != activeChildInfOffsets[sliderNumber]) {
                        slideChanged = true;
                    }

                } else {

                    if(newChildOffset != activeChildOffsets[sliderNumber]) {
                        slideChanged = true;
                    }

                }

                if((snapDirection < 0) && !slideChanged) {

                    newChildOffset++;

                    if((newChildOffset >= childrenOffsets.length) && !settings.infiniteSlider) newChildOffset = childrenOffsets.length - 1;

                } else if((snapDirection > 0) && !slideChanged) {

                    newChildOffset--;

                    if((newChildOffset < 0) && !settings.infiniteSlider) newChildOffset = 0;

                }

            }

            if(settings.snapToChildren || (((nodeOffset > (sliderMin[sliderNumber] * -1)) || (nodeOffset < (sliderMax[sliderNumber] * -1))) && !settings.infiniteSlider)) {

                if(((nodeOffset > (sliderMin[sliderNumber] * -1)) || (nodeOffset < (sliderMax[sliderNumber] * -1))) && !settings.infiniteSlider) {
                    distanceOffsetArray.splice(0, distanceOffsetArray.length);
                } else {
                    distanceOffsetArray.splice(distanceOffsetArray.length * 0.10, distanceOffsetArray.length);
                    nodeOffset = (distanceOffsetArray.length > 0) ? distanceOffsetArray[distanceOffsetArray.length-1] : nodeOffset;
                }

                while((nodeOffset < (tempChildrenOffsets[newChildOffset] - 0.5)) || (nodeOffset > (tempChildrenOffsets[newChildOffset] + 0.5))) {

                    nodeOffset = ((nodeOffset - (tempChildrenOffsets[newChildOffset])) * snapFrictionCoefficient) + (tempChildrenOffsets[newChildOffset]);
                    distanceOffsetArray[distanceOffsetArray.length] = nodeOffset;

                }

                distanceOffsetArray[distanceOffsetArray.length] = tempChildrenOffsets[newChildOffset];
            }

            var jStart = 1;
            if((distanceOffsetArray.length%2) != 0) {
                jStart = 0;
            }

            var lastTimeoutRegistered = 0;
            var count = 0;

            for(var j = 0; j < scrollTimeouts.length; j++) {
                clearTimeout(scrollTimeouts[j]);
            }

            var endOffset = (newChildOffset + tempInfiniteSliderOffset + numberOfSlides)%numberOfSlides;

            var lastCheckOffset = 0;
            for(var j = jStart; j < distanceOffsetArray.length; j = j + 2) {

                if((j == jStart) || (Math.abs(distanceOffsetArray[j] - lastCheckOffset) > 1) || (j >= (distanceOffsetArray.length - 2))) {

                    lastCheckOffset	= distanceOffsetArray[j];

                    scrollTimeouts[scrollTimeouts.length] = helpers.slowScrollHorizontalIntervalTimer(scrollIntervalTime * j, node, slideNodes, distanceOffsetArray[j], scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, newChildOffset, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, endOffset, settings);

                }

            }

            var slideChanged = false;
            var tempOffset = (newChildOffset + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

            if(settings.infiniteSlider) {

                if(tempOffset != activeChildInfOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            } else {

                if(newChildOffset != activeChildOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            }

            if(settings.onSlideComplete != '' && (distanceOffsetArray.length > 1)) {

                scrollTimeouts[scrollTimeouts.length] = helpers.onSlideCompleteTimer(scrollIntervalTime * (j + 1), settings, node, $(node).children(':eq(' + tempOffset + ')'), endOffset, sliderNumber);

            }

            scrollTimeouts[scrollTimeouts.length] = helpers.updateBackfaceVisibilityTimer(scrollIntervalTime * (j + 1), slideNodes, sliderNumber, numberOfSlides, settings);

            slideTimeouts[sliderNumber] = scrollTimeouts;

            helpers.hideScrollbar(settings, scrollTimeouts, j, distanceOffsetArray, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber);

        },

        onSlideComplete: function(settings, node, slideNode, newChildOffset, sliderNumber) {

            var isChanged = (onChangeEventLastFired[sliderNumber] != newChildOffset) ? true : false;
            var args = new helpers.args('complete', settings, $(node), slideNode, newChildOffset, newChildOffset);
            $(node).parent().data('args', args);

            if(settings.onSlideComplete != '') {
                settings.onSlideComplete(args);
            }

            onChangeEventLastFired[sliderNumber] = newChildOffset;

        },

        getSliderOffset: function(node, xy) {

            var sliderOffset = 0;
            xy = (xy == 'x') ? 4 : 5;

            if(has3DTransform && !isIe7 && !isIe8) {

                var transforms = new Array('-webkit-transform', '-moz-transform', 'transform');
                var transformArray;

                for(var i = 0; i < transforms.length; i++) {

                    if($(node).css(transforms[i]) != undefined) {

                        if($(node).css(transforms[i]).length > 0) {

                            transformArray = $(node).css(transforms[i]).split(',');

                            break;

                        }

                    }

                }

                sliderOffset = (transformArray[xy] == undefined) ? 0 : parseInt(transformArray[xy], 10);

            } else {

                sliderOffset = parseInt($(node).css('left'), 10);

            }

            return sliderOffset;

        },

        setSliderOffset: function(node, sliderOffset) {

            sliderOffset = parseInt(sliderOffset, 10);

            if(has3DTransform && !isIe7 && !isIe8) {

                $(node).css({
                    'msTransform': 'matrix(1,0,0,1,' + sliderOffset + ',0)',
                    'webkitTransform': 'matrix(1,0,0,1,' + sliderOffset + ',0)',
                    'MozTransform': 'matrix(1,0,0,1,' + sliderOffset + ',0)',
                    'transform': 'matrix(1,0,0,1,' + sliderOffset + ',0)'
                });

            } else {

                $(node).css({
                    left: sliderOffset + 'px'
                });

            }

        },

        setBrowserInfo: function() {

            if(navigator.userAgent.match('WebKit') != null) {
                isWebkit = true;
                grabOutCursor = '-webkit-grab';
                grabInCursor = '-webkit-grabbing';
            } else if(navigator.userAgent.match('Gecko') != null) {
                isGecko = true;
                grabOutCursor = 'move';
                grabInCursor = '-moz-grabbing';
            } else if(navigator.userAgent.match('MSIE 7') != null) {
                isIe7 = true;
                isIe = true;
            } else if(navigator.userAgent.match('MSIE 8') != null) {
                isIe8 = true;
                isIe = true;
            } else if(navigator.userAgent.match('MSIE 9') != null) {
                isIe9 = true;
                isIe = true;
            }

        },

        has3DTransform: function() {

            var has3D = false;

            var testElement = $('<div />').css({
                'msTransform': 'matrix(1,1,1,1,1,1)',
                'webkitTransform': 'matrix(1,1,1,1,1,1)',
                'MozTransform': 'matrix(1,1,1,1,1,1)',
                'transform': 'matrix(1,1,1,1,1,1)'
            });

            if(testElement.attr('style') == '') {
                has3D = false;
            } else if(isGecko && (parseInt(navigator.userAgent.split('/')[3], 10) >= 21)) {
                //bug in v21+ which does not render slides properly in 3D
                has3D = false;
            } else if(testElement.attr('style') != undefined) {
                has3D = true;
            }

            return has3D;

        },

        getSlideNumber: function(slide, sliderNumber, numberOfSlides) {

            return (slide - infiniteSliderOffset[sliderNumber] + numberOfSlides) % numberOfSlides;

        },

        calcActiveOffset: function(settings, offset, childrenOffsets, stageWidth, infiniteSliderOffset, numberOfSlides, activeChildOffset, sliderNumber) {

            var isFirst = false;
            var arrayOfOffsets = new Array();
            var newChildOffset;

            if(offset > childrenOffsets[0]) newChildOffset = 0;
            if(offset < (childrenOffsets[childrenOffsets.length-1])) newChildOffset = numberOfSlides - 1;

            for(var i = 0; i < childrenOffsets.length; i++) {

                if((childrenOffsets[i] <= offset) && (childrenOffsets[i] > (offset - stageWidth))) {

                    if(!isFirst && (childrenOffsets[i] != offset)) {

                        arrayOfOffsets[arrayOfOffsets.length] = childrenOffsets[i-1];

                    }

                    arrayOfOffsets[arrayOfOffsets.length] = childrenOffsets[i];

                    isFirst = true;

                }

            }

            if(arrayOfOffsets.length == 0) {
                arrayOfOffsets[0] = childrenOffsets[childrenOffsets.length - 1];
            }

            var distance = stageWidth;
            var closestChildOffset = 0;

            for(var i = 0; i < arrayOfOffsets.length; i++) {

                var newDistance = Math.abs(offset - arrayOfOffsets[i]);

                if(newDistance < distance) {
                    closestChildOffset = arrayOfOffsets[i];
                    distance = newDistance;
                }

            }

            for(var i = 0; i < childrenOffsets.length; i++) {

                if(closestChildOffset == childrenOffsets[i]) {
                    newChildOffset = i;

                }

            }

            return newChildOffset;

        },

        changeSlide: function(slide, node, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings) {

            helpers.autoSlidePause(sliderNumber);

            for(var j = 0; j < scrollTimeouts.length; j++) {
                clearTimeout(scrollTimeouts[j]);
            }

            var steps = Math.ceil(settings.autoSlideTransTimer / 10) + 1;
            var startOffset = helpers.getSliderOffset(node, 'x');
            var endOffset = childrenOffsets[slide];
            var offsetDiff = endOffset - startOffset;
            var direction = slide - (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

            if(settings.infiniteSlider) {

                slide = (slide - infiniteSliderOffset[sliderNumber] + numberOfSlides * 2)%numberOfSlides;

                var appendArray = false;
                if((slide == 0) && (numberOfSlides == 2)) {

                    slide = numberOfSlides;
                    childrenOffsets[slide] = childrenOffsets[slide-1] - $(slideNodes).eq(0).outerWidth(true);
                    appendArray = true;

                }

                endOffset = childrenOffsets[slide];
                offsetDiff = endOffset - startOffset;

                var offsets = new Array(childrenOffsets[slide] - $(node).width(), childrenOffsets[slide] + $(node).width());

                if(appendArray) {
                    childrenOffsets.splice(childrenOffsets.length-1, 1);
                }

                for(var i = 0; i < offsets.length; i++) {

                    if(Math.abs(offsets[i] - startOffset) < Math.abs(offsetDiff)) {
                        offsetDiff = (offsets[i] - startOffset);
                    }

                }

            }

            if((offsetDiff < 0) && (direction == -1)) {
                offsetDiff += $(node).width();
            } else if((offsetDiff > 0) && (direction == 1)) {
                offsetDiff -= $(node).width();
            }

            var stepArray = new Array();
            var t;
            var nextStep;

            helpers.showScrollbar(settings, scrollbarClass);

            for(var i = 0; i <= steps; i++) {

                t = i;
                t /= steps;
                t--;
                nextStep = startOffset + offsetDiff*(Math.pow(t,5) + 1);

                stepArray[stepArray.length] = nextStep;

            }

            var tempOffset = (slide + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

            var lastCheckOffset = 0;
            for(var i = 0; i < stepArray.length; i++) {

                if((i == 0) || (Math.abs(stepArray[i] - lastCheckOffset) > 1) || (i >= (stepArray.length - 2))) {

                    lastCheckOffset	= stepArray[i];

                    scrollTimeouts[i] = helpers.slowScrollHorizontalIntervalTimer(scrollIntervalTime * (i + 1), node, slideNodes, stepArray[i], scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, slide, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, tempOffset, settings);

                }

                if((i == 0) && (settings.onSlideStart != '')) {

                    var tempOffset2 = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;
                    settings.onSlideStart(new helpers.args('start', settings, node, $(node).children(':eq(' + tempOffset2 + ')'), tempOffset2, slide));

                }

            }

            var slideChanged = false;

            if(settings.infiniteSlider) {

                if(tempOffset != activeChildInfOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            } else {

                if(slide != activeChildOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            }

            if(slideChanged && (settings.onSlideComplete != '')) {

                scrollTimeouts[scrollTimeouts.length] = helpers.onSlideCompleteTimer(scrollIntervalTime * (i + 1), settings, node, $(node).children(':eq(' + tempOffset + ')'), tempOffset, sliderNumber);

            }

            /*scrollTimeouts[scrollTimeouts.length] = setTimeout(function() {
             activeChildOffsets[sliderNumber] = activeChildOffsets[sliderNumber];
             }, scrollIntervalTime * (i + 1));*/

            slideTimeouts[sliderNumber] = scrollTimeouts;

            helpers.hideScrollbar(settings, scrollTimeouts, i, stepArray, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber);

            helpers.autoSlide(node, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

        },

        changeOffset: function(endOffset, node, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings) {

            helpers.autoSlidePause(sliderNumber);

            for(var j = 0; j < scrollTimeouts.length; j++) {
                clearTimeout(scrollTimeouts[j]);
            }

            if(!settings.infiniteSlider) {
                endOffset = (endOffset > (sliderMin[sliderNumber] * -1 + centeredSlideOffset)) ? sliderMin[sliderNumber] * -1 + centeredSlideOffset : endOffset;
                endOffset = (endOffset < (sliderMax[sliderNumber] * -1)) ? sliderMax[sliderNumber] * -1 : endOffset;
            }

            var steps = Math.ceil(settings.autoSlideTransTimer / 10) + 1;
            var startOffset = helpers.getSliderOffset(node, 'x');
            var slide = (helpers.calcActiveOffset(settings, endOffset, childrenOffsets, stageWidth, infiniteSliderOffset, numberOfSlides, activeChildOffsets[sliderNumber], sliderNumber) + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;
            var testOffsets = childrenOffsets.slice();

            if(settings.snapToChildren && !settings.infiniteSlider) {
                endOffset = childrenOffsets[slide];
            } else if(settings.infiniteSlider && settings.snapToChildren) {
                while(endOffset >= testOffsets[0]) {
                    testOffsets.splice(0, 0, testOffsets[numberOfSlides-1] + $(node).width());
                    testOffsets.splice(numberOfSlides, 1);
                }

                while(endOffset <= testOffsets[numberOfSlides-1]) {
                    testOffsets.splice(numberOfSlides, 0, testOffsets[0] - $(node).width());
                    testOffsets.splice(0, 1);
                }

                slide = helpers.calcActiveOffset(settings, endOffset, testOffsets, stageWidth, infiniteSliderOffset, numberOfSlides, activeChildOffsets[sliderNumber], sliderNumber);
                endOffset = testOffsets[slide];

            }

            var offsetDiff = endOffset - startOffset;

            var stepArray = new Array();
            var t;
            var nextStep;

            helpers.showScrollbar(settings, scrollbarClass);

            for(var i = 0; i <= steps; i++) {

                t = i;
                t /= steps;
                t--;
                nextStep = startOffset + offsetDiff*(Math.pow(t,5) + 1);

                stepArray[stepArray.length] = nextStep;

            }

            var tempOffset = (slide + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

            var lastCheckOffset = 0;
            for(var i = 0; i < stepArray.length; i++) {

                if((i == 0) || (Math.abs(stepArray[i] - lastCheckOffset) > 1) || (i >= (stepArray.length - 2))) {

                    lastCheckOffset	= stepArray[i];

                    scrollTimeouts[i] = helpers.slowScrollHorizontalIntervalTimer(scrollIntervalTime * (i + 1), node, slideNodes, stepArray[i], scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, slide, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, tempOffset, settings);

                }

                if((i == 0) && (settings.onSlideStart != '')) {
                    var tempOffset = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                    settings.onSlideStart(new helpers.args('start', settings, node, $(node).children(':eq(' + tempOffset + ')'), tempOffset, slide));
                }

            }

            var slideChanged = false;

            if(settings.infiniteSlider) {

                if(tempOffset != activeChildInfOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            } else {

                if(slide != activeChildOffsets[sliderNumber]) {
                    slideChanged = true;
                }

            }

            if(slideChanged && (settings.onSlideComplete != '')) {

                scrollTimeouts[scrollTimeouts.length] = helpers.onSlideCompleteTimer(scrollIntervalTime * (i + 1), settings, node, $(node).children(':eq(' + tempOffset + ')'), tempOffset, sliderNumber);
            }

            slideTimeouts[sliderNumber] = scrollTimeouts;

            helpers.hideScrollbar(settings, scrollTimeouts, i, stepArray, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber);

            helpers.autoSlide(node, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

        },

        autoSlide: function(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings) {

            if(!iosSliderSettings[sliderNumber].autoSlide) return false;

            helpers.autoSlidePause(sliderNumber);

            autoSlideTimeouts[sliderNumber] = setTimeout(function() {

                if(!settings.infiniteSlider && (activeChildOffsets[sliderNumber] > childrenOffsets.length-1)) {
                    activeChildOffsets[sliderNumber] = activeChildOffsets[sliderNumber] - numberOfSlides;
                }

                var nextSlide = activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + 1;

                helpers.changeSlide(nextSlide, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                helpers.autoSlide(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

            }, settings.autoSlideTimer + settings.autoSlideTransTimer);

        },

        autoSlidePause: function(sliderNumber) {

            clearTimeout(autoSlideTimeouts[sliderNumber]);

        },

        isUnselectable: function(node, settings) {

            if(settings.unselectableSelector != '') {
                if($(node).closest(settings.unselectableSelector).length == 1) return true;
            }

            return false;

        },

        /* timers */
        slowScrollHorizontalIntervalTimer: function(scrollIntervalTime, node, slideNodes, step, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, slide, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, endOffset, settings) {

            var scrollTimeout = setTimeout(function() {
                helpers.slowScrollHorizontalInterval(node, slideNodes, step, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, slide, originalOffsets, childrenOffsets, infiniteSliderWidth, numberOfSlides, slideNodeOuterWidths, sliderNumber, centeredSlideOffset, endOffset, settings);
            }, scrollIntervalTime);

            return scrollTimeout;

        },

        onSlideCompleteTimer: function(scrollIntervalTime, settings, node, slideNode, slide, scrollbarNumber) {

            var scrollTimeout = setTimeout(function() {
                helpers.onSlideComplete(settings, node, slideNode, slide, scrollbarNumber);
            }, scrollIntervalTime);

            return scrollTimeout;

        },

        hideScrollbarIntervalTimer: function(scrollIntervalTime, newOffset, opacity, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber, settings) {

            var scrollTimeout = setTimeout(function() {
                helpers.hideScrollbarInterval(newOffset, opacity, scrollbarClass, scrollbarWidth, stageWidth, scrollMargin, scrollBorder, sliderNumber, settings);
            }, scrollIntervalTime);

            return scrollTimeout;

        },

        updateBackfaceVisibilityTimer: function(scrollIntervalTime, slideNodes, sliderNumber, numberOfSlides, settings) {

            var scrollTimeout = setTimeout(function() {
                helpers.updateBackfaceVisibility(slideNodes, sliderNumber, numberOfSlides, settings);
            }, scrollIntervalTime);

            return scrollTimeout;

        },

        updateBackfaceVisibility: function(slideNodes, sliderNumber, numberOfSlides, settings) {

            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;
            var usedSlideArray = Array();

            //loop through buffered slides
            for(var i = 0; i < (settings.hardwareAccelBuffer * 2); i++) {

                var slide_eq = helpers.mod(slide+i-settings.hardwareAccelBuffer, numberOfSlides);

                //check if backface visibility applied
                if($(slideNodes).eq(slide_eq).css('-webkit-backface-visibility') == 'visible') {

                    usedSlideArray[usedSlideArray.length] = slide_eq;

                    var eq_h = helpers.mod(slide_eq+settings.hardwareAccelBuffer*2, numberOfSlides);
                    var eq_l = helpers.mod(slide_eq-settings.hardwareAccelBuffer*2, numberOfSlides);

                    //buffer backface visibility
                    $(slideNodes).eq(slide_eq).css('-webkit-backface-visibility', 'hidden');

                    if(usedSlideArray.indexOf(eq_l) == -1)
                        $(slideNodes).eq(eq_l).css('-webkit-backface-visibility', '');

                    if(usedSlideArray.indexOf(eq_h) == -1)
                        $(slideNodes).eq(eq_h).css('-webkit-backface-visibility', '');

                }

            }

        },

        mod: function(x, mod) {

            var rem = x % mod;

            return rem < 0 ? rem + mod : rem;

        },

        args: function(func, settings, node, activeSlideNode, newChildOffset, targetSlideOffset) {

            this.prevSlideNumber = ($(node).parent().data('args') == undefined) ? undefined : $(node).parent().data('args').prevSlideNumber;
            this.prevSlideObject = ($(node).parent().data('args') == undefined) ? undefined : $(node).parent().data('args').prevSlideObject;
            this.targetSlideNumber = targetSlideOffset + 1;
            this.targetSlideObject = $(node).children(':eq(' + targetSlideOffset + ')');
            this.slideChanged = false;

            if(func == 'load') {
                this.targetSlideNumber = undefined;
                this.targetSlideObject = undefined;
            } else if(func == 'start') {
                this.targetSlideNumber = undefined;
                this.targetSlideObject = undefined;
            } else if(func == 'change') {
                this.slideChanged = true;
                this.prevSlideNumber = ($(node).parent().data('args') == undefined) ? settings.startAtSlide : $(node).parent().data('args').currentSlideNumber;
                this.prevSlideObject = $(node).children(':eq(' + this.prevSlideNumber + ')');
            } else if(func == 'complete') {
                this.slideChanged = $(node).parent().data('args').slideChanged;
            }

            this.settings = settings;
            this.data = $(node).parent().data('iosslider');
            this.sliderObject = node;
            this.sliderContainerObject = $(node).parent();

            this.currentSlideObject = activeSlideNode;
            this.currentSlideNumber = newChildOffset + 1;
            this.currentSliderOffset = helpers.getSliderOffset(node, 'x') * -1;

        },

        preventDrag: function(event) {
            event.preventDefault();
        },

        preventClick: function(event) {
            event.stopImmediatePropagation();
            return false;
        },

        enableClick: function() {
            return true;
        }

    }

    helpers.setBrowserInfo();

    var methods = {

        init: function(options, node) {

            has3DTransform = helpers.has3DTransform();

            var settings = $.extend(true, {
                'elasticPullResistance': 0.6,
                'frictionCoefficient': 0.92,
                'elasticFrictionCoefficient': 0.6,
                'snapFrictionCoefficient': 0.92,
                'snapToChildren': false,
                'snapSlideCenter': false,
                'startAtSlide': 1,
                'scrollbar': false,
                'scrollbarDrag': false,
                'scrollbarHide': true,
                'scrollbarPaging': false,
                'scrollbarLocation': 'top',
                'scrollbarContainer': '',
                'scrollbarOpacity': 0.4,
                'scrollbarHeight': '4px',
                'scrollbarBorder': '0',
                'scrollbarMargin': '5px',
                'scrollbarBackground': '#000',
                'scrollbarBorderRadius': '100px',
                'scrollbarShadow': '0 0 0 #000',
                'scrollbarElasticPullResistance': 0.9,
                'desktopClickDrag': false,
                'keyboardControls': false,
                'tabToAdvance': false,
                'responsiveSlideContainer': true,
                'responsiveSlides': true,
                'navSlideSelector': '',
                'navPrevSelector': '',
                'navNextSelector': '',
                'autoSlideToggleSelector': '',
                'autoSlide': false,
                'autoSlideTimer': 5000,
                'autoSlideTransTimer': 750,
                'autoSlideHoverPause': true,
                'infiniteSlider': false,
                'snapVelocityThreshold': 5,
                'slideStartVelocityThreshold': 0,
                'horizontalSlideLockThreshold': 5,
                'verticalSlideLockThreshold': 3,
                'hardwareAccelBuffer': 5,
                'stageCSS': {
                    position: 'relative',
                    top: '0',
                    left: '0',
                    overflow: 'hidden',
                    zIndex: 1
                },
                'unselectableSelector': '',
                'onSliderLoaded': '',
                'onSliderUpdate': '',
                'onSliderResize': '',
                'onSlideStart': '',
                'onSlideChange': '',
                'onSlideComplete': ''
            }, options);

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function(i) {

                scrollbarNumber++;
                var sliderNumber = scrollbarNumber;
                var scrollTimeouts = new Array();
                iosSliderSettings[sliderNumber] = $.extend({}, settings);
                sliderMin[sliderNumber] = 0;
                sliderMax[sliderNumber] = 0;
                var minTouchpoints = 0;
                var xCurrentScrollRate = new Array(0, 0);
                var yCurrentScrollRate = new Array(0, 0);
                var scrollbarBlockClass = 'scrollbarBlock' + scrollbarNumber;
                var scrollbarClass = 'scrollbar' + scrollbarNumber;
                var scrollbarNode;
                var scrollbarBlockNode;
                var scrollbarStageWidth;
                var scrollbarWidth;
                var containerWidth;
                var containerHeight;
                var centeredSlideOffset = 0;
                var stageNode = $(this);
                var stageWidth;
                var stageHeight;
                var slideWidth;
                var scrollMargin;
                var scrollBorder;
                var lastTouch;
                var isFirstInit = true;
                var newChildOffset = -1;
                var webkitTransformArray = new Array();
                var childrenOffsets;
                var originalOffsets = new Array();
                var scrollbarStartOpacity = 0;
                var xScrollStartPosition = 0;
                var yScrollStartPosition = 0;
                var currentTouches = 0;
                var scrollerNode = $(this).children(':first-child');
                var slideNodes;
                var slideNodeWidths;
                var slideNodeOuterWidths;
                var numberOfSlides = $(scrollerNode).children().not('script').length;
                var xScrollStarted = false;
                var lastChildOffset = 0;
                var isMouseDown = false;
                var currentSlider = undefined;
                var sliderStopLocation = 0;
                var infiniteSliderWidth;
                infiniteSliderOffset[sliderNumber] = 0;
                var shortContent = false;
                onChangeEventLastFired[sliderNumber] = -1;
                var isAutoSlideToggleOn = false;
                iosSliders[sliderNumber] = stageNode;
                isEventCleared[sliderNumber] = false;
                var currentEventNode;
                var intermediateChildOffset = 0;
                var tempInfiniteSliderOffset = 0;
                var preventXScroll = false;
                var snapOverride = false;
                var clickEvent = 'touchstart.iosSliderEvent click.iosSliderEvent';
                var scrollerWidth;
                var anchorEvents;
                var onclickEvents;
                var allScrollerNodeChildren;
                touchLocks[sliderNumber] = false;
                slideTimeouts[sliderNumber] = new Array();
                if(settings.scrollbarDrag) {
                    settings.scrollbar = true;
                    settings.scrollbarHide = false;
                }
                var $this = $(this);
                var data = $this.data('iosslider');
                if(data != undefined) return true;

                if(parseInt($().jquery.split('.').join(''), 10) >= 14.2) {
                    $(this).delegate('img', 'dragstart.iosSliderEvent', function(event) { event.preventDefault(); });
                } else {
                    $(this).find('img').bind('dragstart.iosSliderEvent', function(event) { event.preventDefault(); });
                }

                if(settings.infiniteSlider) {
                    settings.scrollbar = false;
                }

                if(settings.infiniteSlider && (numberOfSlides == 1)) {
                    settings.infiniteSlider = false;
                }

                if(settings.scrollbar) {

                    if(settings.scrollbarContainer != '') {
                        $(settings.scrollbarContainer).append("<div class = '" + scrollbarBlockClass + "'><div class = '" + scrollbarClass + "'></div></div>");
                    } else {
                        $(scrollerNode).parent().append("<div class = '" + scrollbarBlockClass + "'><div class = '" + scrollbarClass + "'></div></div>");
                    }

                }

                if(!init()) return true;

                $(this).find('a').bind('mousedown', helpers.preventDrag);
                $(this).find("[onclick]").bind('click', helpers.preventDrag).each(function() {

                    $(this).data('onclick', this.onclick);

                });

                var newChildOffset = helpers.calcActiveOffset(settings, helpers.getSliderOffset($(scrollerNode), 'x'), childrenOffsets, stageWidth, infiniteSliderOffset[sliderNumber], numberOfSlides, undefined, sliderNumber);
                var tempOffset = (newChildOffset + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                var args = new helpers.args('load', settings, scrollerNode, $(scrollerNode).children(':eq(' + tempOffset + ')'), tempOffset, tempOffset);
                $(stageNode).data('args', args);

                if(settings.onSliderLoaded != '') {

                    settings.onSliderLoaded(args);

                }

                onChangeEventLastFired[sliderNumber] = tempOffset;

                function init() {

                    helpers.autoSlidePause(sliderNumber);

                    anchorEvents = $(scrollerNode).find('a');
                    onclickEvents = $(scrollerNode).find('[onclick]');
                    allScrollerNodeChildren = $(scrollerNode).find('*');

                    $(stageNode).css('width', '');
                    $(stageNode).css('height', '');
                    $(scrollerNode).css('width', '');
                    slideNodes = $(scrollerNode).children().not('script').get();
                    slideNodeWidths = new Array();
                    slideNodeOuterWidths = new Array();

                    if(settings.responsiveSlides) {
                        $(slideNodes).css('width', '100%');
                    }

                    sliderMax[sliderNumber] = 0;
                    childrenOffsets = new Array();
                    containerWidth = $(stageNode).parent().width();
                    stageWidth = $(stageNode).outerWidth(true);

                    if(settings.responsiveSlideContainer) {
                        stageWidth = ($(stageNode).outerWidth(true) > containerWidth) ? containerWidth : $(stageNode).width();
                    }

                    $(stageNode).css({
                        position: settings.stageCSS.position,
                        top: settings.stageCSS.top,
                        left: settings.stageCSS.left,
                        overflow: settings.stageCSS.overflow,
                        zIndex: settings.stageCSS.zIndex,
                        'webkitPerspective': 1000,
                        'webkitBackfaceVisibility': 'hidden',
                        'msTouchAction': 'pan-y',
                        width: stageWidth
                    });

                    $(settings.unselectableSelector).css({
                        cursor: 'default'
                    });

                    for(var j = 0; j < slideNodes.length; j++) {

                        slideNodeWidths[j] = $(slideNodes[j]).width();
                        slideNodeOuterWidths[j] = $(slideNodes[j]).outerWidth(true);
                        var newWidth = slideNodeOuterWidths[j];

                        if(settings.responsiveSlides) {

                            if(slideNodeOuterWidths[j] > stageWidth) {

                                newWidth = stageWidth + (slideNodeOuterWidths[j] - slideNodeWidths[j]) * -1;
                                slideNodeWidths[j] = newWidth;
                                slideNodeOuterWidths[j] = stageWidth;

                            } else {

                                newWidth = slideNodeWidths[j];

                            }

                            $(slideNodes[j]).css({
                                width: newWidth
                            });

                        }

                        $(slideNodes[j]).css({
                            overflow: 'hidden',
                            position: 'absolute'
                        });

                        childrenOffsets[j] = sliderMax[sliderNumber] * -1;

                        sliderMax[sliderNumber] = sliderMax[sliderNumber] + newWidth + (slideNodeOuterWidths[j] - slideNodeWidths[j]);

                    }

                    if(settings.snapSlideCenter) {
                        centeredSlideOffset = (stageWidth - slideNodeOuterWidths[0]) * 0.5;

                        if(settings.responsiveSlides && (slideNodeOuterWidths[0] > stageWidth)) {
                            centeredSlideOffset = 0;
                        }
                    }

                    sliderAbsMax[sliderNumber] = sliderMax[sliderNumber] * 2;

                    for(var j = 0; j < slideNodes.length; j++) {

                        helpers.setSliderOffset($(slideNodes[j]), childrenOffsets[j] * -1 + sliderMax[sliderNumber] + centeredSlideOffset);

                        childrenOffsets[j] = childrenOffsets[j] - sliderMax[sliderNumber];

                    }

                    if(!settings.infiniteSlider && !settings.snapSlideCenter) {

                        for(var i = 0; i < childrenOffsets.length; i++) {

                            if(childrenOffsets[i] <= ((sliderMax[sliderNumber] * 2 - stageWidth) * -1)) {
                                break;
                            }

                            lastChildOffset = i;

                        }

                        childrenOffsets.splice(lastChildOffset + 1, childrenOffsets.length);
                        childrenOffsets[childrenOffsets.length] = (sliderMax[sliderNumber] * 2 - stageWidth) * -1;

                    }

                    for(var i = 0; i < childrenOffsets.length; i++) {
                        originalOffsets[i] = childrenOffsets[i];
                    }

                    if(isFirstInit) {

                        iosSliderSettings[sliderNumber].startAtSlide = (iosSliderSettings[sliderNumber].startAtSlide > childrenOffsets.length) ? childrenOffsets.length : iosSliderSettings[sliderNumber].startAtSlide;
                        if(settings.infiniteSlider) {
                            iosSliderSettings[sliderNumber].startAtSlide = (iosSliderSettings[sliderNumber].startAtSlide - 1 + numberOfSlides)%numberOfSlides;
                            activeChildOffsets[sliderNumber] = (iosSliderSettings[sliderNumber].startAtSlide);
                        } else {
                            iosSliderSettings[sliderNumber].startAtSlide = ((iosSliderSettings[sliderNumber].startAtSlide - 1) < 0) ? childrenOffsets.length-1 : iosSliderSettings[sliderNumber].startAtSlide;
                            activeChildOffsets[sliderNumber] = (iosSliderSettings[sliderNumber].startAtSlide-1);
                        }
                        activeChildInfOffsets[sliderNumber] = activeChildOffsets[sliderNumber];
                    }

                    sliderMin[sliderNumber] = sliderMax[sliderNumber] + centeredSlideOffset;

                    $(scrollerNode).css({
                        position: 'relative',
                        cursor: grabOutCursor,
                        'webkitPerspective': '0',
                        'webkitBackfaceVisibility': 'hidden',
                        width: sliderMax[sliderNumber] + 'px'
                    });

                    scrollerWidth = sliderMax[sliderNumber];
                    sliderMax[sliderNumber] = sliderMax[sliderNumber] * 2 - stageWidth + centeredSlideOffset * 2;

                    shortContent = (((scrollerWidth + centeredSlideOffset) < stageWidth) || (stageWidth == 0)) ? true : false;

                    if(shortContent) {

                        $(scrollerNode).css({
                            cursor: 'default'
                        });

                    }

                    containerHeight = $(stageNode).parent().outerHeight(true);
                    stageHeight = $(stageNode).height();

                    if(settings.responsiveSlideContainer) {
                        stageHeight = (stageHeight > containerHeight) ? containerHeight : stageHeight;
                    }

                    $(stageNode).css({
                        height: stageHeight
                    });

                    helpers.setSliderOffset(scrollerNode, childrenOffsets[activeChildOffsets[sliderNumber]]);

                    if(settings.infiniteSlider && !shortContent) {

                        var currentScrollOffset = helpers.getSliderOffset($(scrollerNode), 'x');
                        var count = (infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides * -1;

                        while(count < 0) {

                            var lowSlideNumber = 0;
                            var lowSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                            $(slideNodes).each(function(i) {

                                if(helpers.getSliderOffset(this, 'x') < lowSlideOffset) {
                                    lowSlideOffset = helpers.getSliderOffset(this, 'x');
                                    lowSlideNumber = i;
                                }

                            });

                            var newOffset = sliderMin[sliderNumber] + scrollerWidth;
                            helpers.setSliderOffset($(slideNodes)[lowSlideNumber], newOffset);

                            sliderMin[sliderNumber] = childrenOffsets[1] * -1 + centeredSlideOffset;
                            sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                            childrenOffsets.splice(0, 1);
                            childrenOffsets.splice(childrenOffsets.length, 0, newOffset * -1 + centeredSlideOffset);

                            count++;

                        }

                        while(((childrenOffsets[0] * -1 - scrollerWidth + centeredSlideOffset) > 0) && settings.snapSlideCenter && isFirstInit) {

                            var highSlideNumber = 0;
                            var highSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                            $(slideNodes).each(function(i) {

                                if(helpers.getSliderOffset(this, 'x') > highSlideOffset) {
                                    highSlideOffset = helpers.getSliderOffset(this, 'x');
                                    highSlideNumber = i;
                                }

                            });

                            var newOffset = sliderMin[sliderNumber] - slideNodeOuterWidths[highSlideNumber];
                            helpers.setSliderOffset($(slideNodes)[highSlideNumber], newOffset);

                            childrenOffsets.splice(0, 0, newOffset * -1 + centeredSlideOffset);
                            childrenOffsets.splice(childrenOffsets.length-1, 1);

                            sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                            sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                            infiniteSliderOffset[sliderNumber]--;
                            activeChildOffsets[sliderNumber]++;

                        }

                        while(currentScrollOffset <= (sliderMax[sliderNumber] * -1)) {

                            var lowSlideNumber = 0;
                            var lowSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                            $(slideNodes).each(function(i) {

                                if(helpers.getSliderOffset(this, 'x') < lowSlideOffset) {
                                    lowSlideOffset = helpers.getSliderOffset(this, 'x');
                                    lowSlideNumber = i;
                                }

                            });

                            var newOffset = sliderMin[sliderNumber] + scrollerWidth;
                            helpers.setSliderOffset($(slideNodes)[lowSlideNumber], newOffset);

                            sliderMin[sliderNumber] = childrenOffsets[1] * -1 + centeredSlideOffset;
                            sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                            childrenOffsets.splice(0, 1);
                            childrenOffsets.splice(childrenOffsets.length, 0, newOffset * -1 + centeredSlideOffset);

                            infiniteSliderOffset[sliderNumber]++;
                            activeChildOffsets[sliderNumber]--;

                        }

                    }

                    helpers.setSliderOffset(scrollerNode, childrenOffsets[activeChildOffsets[sliderNumber]]);

                    helpers.updateBackfaceVisibility(slideNodes, sliderNumber, numberOfSlides, settings);

                    if(!settings.desktopClickDrag) {

                        $(scrollerNode).css({
                            cursor: 'default'
                        });

                    }

                    if(settings.scrollbar) {

                        $('.' + scrollbarBlockClass).css({
                            margin: settings.scrollbarMargin,
                            overflow: 'hidden',
                            display: 'none'
                        });

                        $('.' + scrollbarBlockClass + ' .' + scrollbarClass).css({
                            border: settings.scrollbarBorder
                        });

                        scrollMargin = parseInt($('.' + scrollbarBlockClass).css('marginLeft')) + parseInt($('.' + scrollbarBlockClass).css('marginRight'));
                        scrollBorder = parseInt($('.' + scrollbarBlockClass + ' .' + scrollbarClass).css('borderLeftWidth'), 10) + parseInt($('.' + scrollbarBlockClass + ' .' + scrollbarClass).css('borderRightWidth'), 10);
                        scrollbarStageWidth = (settings.scrollbarContainer != '') ? $(settings.scrollbarContainer).width() : stageWidth;
                        scrollbarWidth = (stageWidth / scrollerWidth) * (scrollbarStageWidth - scrollMargin);

                        if(!settings.scrollbarHide) {
                            scrollbarStartOpacity = settings.scrollbarOpacity;
                        }

                        $('.' + scrollbarBlockClass).css({
                            position: 'absolute',
                            left: 0,
                            width: scrollbarStageWidth - scrollMargin + 'px',
                            margin: settings.scrollbarMargin
                        });

                        if(settings.scrollbarLocation == 'top') {
                            $('.' + scrollbarBlockClass).css('top', '0');
                        } else {
                            $('.' + scrollbarBlockClass).css('bottom', '0');
                        }

                        $('.' + scrollbarBlockClass + ' .' + scrollbarClass).css({
                            borderRadius: settings.scrollbarBorderRadius,
                            background: settings.scrollbarBackground,
                            height: settings.scrollbarHeight,
                            width: scrollbarWidth - scrollBorder + 'px',
                            minWidth: settings.scrollbarHeight,
                            border: settings.scrollbarBorder,
                            'webkitPerspective': 1000,
                            'webkitBackfaceVisibility': 'hidden',
                            'position': 'relative',
                            opacity: scrollbarStartOpacity,
                            filter: 'alpha(opacity:' + (scrollbarStartOpacity * 100) + ')',
                            boxShadow: settings.scrollbarShadow
                        });

                        helpers.setSliderOffset($('.' + scrollbarBlockClass + ' .' + scrollbarClass), Math.floor((childrenOffsets[activeChildOffsets[sliderNumber]] * -1 - sliderMin[sliderNumber] + centeredSlideOffset) / (sliderMax[sliderNumber] - sliderMin[sliderNumber] + centeredSlideOffset) * (scrollbarStageWidth - scrollMargin - scrollbarWidth)));

                        $('.' + scrollbarBlockClass).css({
                            display: 'block'
                        });

                        scrollbarNode = $('.' + scrollbarBlockClass + ' .' + scrollbarClass);
                        scrollbarBlockNode = $('.' + scrollbarBlockClass);

                    }

                    if(settings.scrollbarDrag && !shortContent) {
                        $('.' + scrollbarBlockClass + ' .' + scrollbarClass).css({
                            cursor: grabOutCursor
                        });
                    }

                    if(settings.infiniteSlider) {

                        infiniteSliderWidth = (sliderMax[sliderNumber] + stageWidth) / 3;

                    }

                    if(settings.navSlideSelector != '') {

                        $(settings.navSlideSelector).each(function(j) {

                            $(this).css({
                                cursor: 'pointer'
                            });

                            $(this).unbind(clickEvent).bind(clickEvent, function(e) {

                                if(e.type == 'touchstart') {
                                    $(this).unbind('click.iosSliderEvent');
                                } else {
                                    $(this).unbind('touchstart.iosSliderEvent');
                                }
                                clickEvent = e.type + '.iosSliderEvent';

                                helpers.changeSlide(j, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                            });

                        });

                    }

                    if(settings.navPrevSelector != '') {

                        $(settings.navPrevSelector).css({
                            cursor: 'pointer'
                        });

                        $(settings.navPrevSelector).unbind(clickEvent).bind(clickEvent, function(e) {

                            if(e.type == 'touchstart') {
                                $(this).unbind('click.iosSliderEvent');
                            } else {
                                $(this).unbind('touchstart.iosSliderEvent');
                            }
                            clickEvent = e.type + '.iosSliderEvent';

                            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                            if((slide > 0) || settings.infiniteSlider) {
                                helpers.changeSlide(slide - 1, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);
                            }
                        });

                    }

                    if(settings.navNextSelector != '') {

                        $(settings.navNextSelector).css({
                            cursor: 'pointer'
                        });

                        $(settings.navNextSelector).unbind(clickEvent).bind(clickEvent, function(e) {

                            if(e.type == 'touchstart') {
                                $(this).unbind('click.iosSliderEvent');
                            } else {
                                $(this).unbind('touchstart.iosSliderEvent');
                            }
                            clickEvent = e.type + '.iosSliderEvent';

                            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                            if((slide < childrenOffsets.length-1) || settings.infiniteSlider) {
                                helpers.changeSlide(slide + 1, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);
                            }
                        });

                    }

                    if(settings.autoSlide && !shortContent) {

                        if(settings.autoSlideToggleSelector != '') {

                            $(settings.autoSlideToggleSelector).css({
                                cursor: 'pointer'
                            });

                            $(settings.autoSlideToggleSelector).unbind(clickEvent).bind(clickEvent, function(e) {

                                if(e.type == 'touchstart') {
                                    $(this).unbind('click.iosSliderEvent');
                                } else {
                                    $(this).unbind('touchstart.iosSliderEvent');
                                }
                                clickEvent = e.type + '.iosSliderEvent';

                                if(!isAutoSlideToggleOn) {

                                    helpers.autoSlidePause(sliderNumber);
                                    isAutoSlideToggleOn = true;

                                    $(settings.autoSlideToggleSelector).addClass('on');

                                } else {

                                    helpers.autoSlide(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                                    isAutoSlideToggleOn = false;

                                    $(settings.autoSlideToggleSelector).removeClass('on');

                                }

                            });

                        }

                    }

                    helpers.autoSlide(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                    $(stageNode).bind('mouseleave.iosSliderEvent', function() {

                        if(isAutoSlideToggleOn) return true;

                        helpers.autoSlide(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                    });

                    $(stageNode).bind('touchend.iosSliderEvent', function() {

                        if(isAutoSlideToggleOn) return true;

                        helpers.autoSlide(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);

                    });

                    if(settings.autoSlideHoverPause) {
                        $(stageNode).bind('mouseenter.iosSliderEvent', function() {
                            helpers.autoSlidePause(sliderNumber);
                        });
                    }

                    $(stageNode).data('iosslider', {
                        obj: $this,
                        settings: settings,
                        scrollerNode: scrollerNode,
                        slideNodes: slideNodes,
                        numberOfSlides: numberOfSlides,
                        centeredSlideOffset: centeredSlideOffset,
                        sliderNumber: sliderNumber,
                        originalOffsets: originalOffsets,
                        childrenOffsets: childrenOffsets,
                        sliderMax: sliderMax[sliderNumber],
                        scrollbarClass: scrollbarClass,
                        scrollbarWidth: scrollbarWidth,
                        scrollbarStageWidth: scrollbarStageWidth,
                        stageWidth: stageWidth,
                        scrollMargin: scrollMargin,
                        scrollBorder: scrollBorder,
                        infiniteSliderOffset: infiniteSliderOffset[sliderNumber],
                        infiniteSliderWidth: infiniteSliderWidth,
                        slideNodeOuterWidths: slideNodeOuterWidths,
                        shortContent: shortContent
                    });

                    isFirstInit = false;

                    return true;

                }

                if(settings.scrollbarPaging && settings.scrollbar && !shortContent) {

                    $(scrollbarBlockNode).css('cursor', 'pointer');

                    $(scrollbarBlockNode).bind('click.iosSliderEvent', function(e) {

                        if(this == e.target) {

                            if(e.pageX > $(scrollbarNode).offset().left) {
                                methods.nextPage(stageNode);
                            } else {
                                methods.prevPage(stageNode);
                            }

                        }

                    });

                }

                if(iosSliderSettings[sliderNumber].responsiveSlides || iosSliderSettings[sliderNumber].responsiveSlideContainer) {

                    var orientationEvent = supportsOrientationChange ? 'orientationchange' : 'resize';

                    $(window).bind(orientationEvent + '.iosSliderEvent-' + sliderNumber, function() {

                        if(!init()) return true;

                        var args = $(stageNode).data('args');

                        if(settings.onSliderResize != '') {
                            settings.onSliderResize(args);
                        }

                    });

                }

                if((settings.keyboardControls || settings.tabToAdvance) && !shortContent) {

                    $(document).bind('keydown.iosSliderEvent', function(e) {

                        if((!isIe7) && (!isIe8)) {
                            var e = e.originalEvent;
                        }

                        if(e.target.nodeName == 'INPUT') return true;

                        if(touchLocks[sliderNumber]) return true;

                        if((e.keyCode == 37) && settings.keyboardControls) {

                            e.preventDefault();

                            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                            if((slide > 0) || settings.infiniteSlider) {
                                helpers.changeSlide(slide - 1, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);
                            }

                        } else if(((e.keyCode == 39) && settings.keyboardControls) || ((e.keyCode == 9) && settings.tabToAdvance)) {

                            e.preventDefault();

                            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                            if((slide < childrenOffsets.length-1) || settings.infiniteSlider) {
                                helpers.changeSlide(slide + 1, scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, centeredSlideOffset, settings);
                            }

                        }

                    });

                }

                if(isTouch || settings.desktopClickDrag) {

                    var touchStartFlag = false;
                    var touchEndFlag = false;
                    var touchSelection = $(scrollerNode);
                    var touchSelectionMove = $(scrollerNode);
                    var preventDefault = null;
                    var isUnselectable = false;

                    if(settings.scrollbarDrag) {

                        touchSelection = touchSelection.add(scrollbarNode);
                        touchSelectionMove = touchSelectionMove.add(scrollbarBlockNode);

                    }

                    $(touchSelection).bind('mousedown.iosSliderEvent touchstart.iosSliderEvent', function(e) {

                        //if scroll starts, unbind dom from slider touch override
                        $(window).one('scroll.iosSliderEvent', function(e) { touchStartFlag = false; });

                        if(touchStartFlag) return true;
                        touchStartFlag = true;
                        touchEndFlag = false;

                        if(e.type == 'touchstart') {
                            $(touchSelectionMove).unbind('mousedown.iosSliderEvent');
                        } else {
                            $(touchSelectionMove).unbind('touchstart.iosSliderEvent');
                        }

                        if(touchLocks[sliderNumber] || shortContent) {
                            touchStartFlag = false;
                            xScrollStarted = false;
                            return true;
                        }

                        isUnselectable = helpers.isUnselectable(e.target, settings);

                        if(isUnselectable) {
                            touchStartFlag = false;
                            xScrollStarted = false;
                            return true;
                        }

                        currentEventNode = ($(this)[0] === $(scrollbarNode)[0]) ? scrollbarNode : scrollerNode;

                        if((!isIe7) && (!isIe8)) {
                            var e = e.originalEvent;
                        }

                        helpers.autoSlidePause(sliderNumber);

                        allScrollerNodeChildren.unbind('.disableClick');

                        if(e.type == 'touchstart') {

                            eventX = e.touches[0].pageX;
                            eventY = e.touches[0].pageY;

                        } else {

                            if (window.getSelection) {
                                if (window.getSelection().empty) {
                                    window.getSelection().empty();
                                } else if (window.getSelection().removeAllRanges) {
                                    window.getSelection().removeAllRanges();
                                }
                            } else if (document.selection) {
                                if(isIe8) {
                                    try { document.selection.empty(); } catch(e) { /* absorb ie8 bug */ }
                                } else {
                                    document.selection.empty();
                                }
                            }

                            eventX = e.pageX;
                            eventY = e.pageY;

                            isMouseDown = true;
                            currentSlider = scrollerNode;

                            $(this).css({
                                cursor: grabInCursor
                            });

                        }

                        xCurrentScrollRate = new Array(0, 0);
                        yCurrentScrollRate = new Array(0, 0);
                        xScrollDistance = 0;
                        xScrollStarted = false;

                        for(var j = 0; j < scrollTimeouts.length; j++) {
                            clearTimeout(scrollTimeouts[j]);
                        }

                        var scrollPosition = helpers.getSliderOffset(scrollerNode, 'x');

                        if(scrollPosition > (sliderMin[sliderNumber] * -1 + centeredSlideOffset + scrollerWidth)) {

                            scrollPosition = sliderMin[sliderNumber] * -1 + centeredSlideOffset + scrollerWidth;

                            helpers.setSliderOffset($('.' + scrollbarClass), scrollPosition);

                            $('.' + scrollbarClass).css({
                                width: (scrollbarWidth - scrollBorder) + 'px'
                            });

                        } else if(scrollPosition < (sliderMax[sliderNumber] * -1)) {

                            scrollPosition = sliderMax[sliderNumber] * -1;

                            helpers.setSliderOffset($('.' + scrollbarClass), (scrollbarStageWidth - scrollMargin - scrollbarWidth));

                            $('.' + scrollbarClass).css({
                                width: (scrollbarWidth - scrollBorder) + 'px'
                            });

                        }

                        var scrollbarSubtractor = ($(this)[0] === $(scrollbarNode)[0]) ? (sliderMin[sliderNumber]) : 0;

                        xScrollStartPosition = (helpers.getSliderOffset(this, 'x') - eventX - scrollbarSubtractor) * -1;
                        yScrollStartPosition = (helpers.getSliderOffset(this, 'y') - eventY) * -1;

                        xCurrentScrollRate[1] = eventX;
                        yCurrentScrollRate[1] = eventY;

                        snapOverride = false;

                    });

                    $(document).bind('touchmove.iosSliderEvent mousemove.iosSliderEvent', function(e) {

                        if((!isIe7) && (!isIe8)) {
                            var e = e.originalEvent;
                        }

                        if(touchLocks[sliderNumber] || shortContent || isUnselectable || !touchStartFlag) return true;

                        var edgeDegradation = 0;

                        if(e.type == 'touchmove') {

                            eventX = e.touches[0].pageX;
                            eventY = e.touches[0].pageY;

                        } else {

                            if(window.getSelection) {
                                if(window.getSelection().empty) {
                                    //window.getSelection().empty(); /* removed to enable input fields within the slider */
                                } else if(window.getSelection().removeAllRanges) {
                                    window.getSelection().removeAllRanges();
                                }
                            } else if(document.selection) {
                                if(isIe8) {
                                    try { document.selection.empty(); } catch(e) { /* absorb ie8 bug */ }
                                } else {
                                    document.selection.empty();
                                }
                            }

                            eventX = e.pageX;
                            eventY = e.pageY;

                            if(!isMouseDown) {
                                return true;
                            }

                            if(!isIe) {
                                if((typeof e.webkitMovementX != 'undefined' || typeof e.webkitMovementY != 'undefined') && e.webkitMovementY === 0 && e.webkitMovementX === 0) {
                                    return true;
                                }
                            }

                        }

                        xCurrentScrollRate[0] = xCurrentScrollRate[1];
                        xCurrentScrollRate[1] = eventX;
                        xScrollDistance = (xCurrentScrollRate[1] - xCurrentScrollRate[0]) / 2;

                        yCurrentScrollRate[0] = yCurrentScrollRate[1];
                        yCurrentScrollRate[1] = eventY;
                        yScrollDistance = (yCurrentScrollRate[1] - yCurrentScrollRate[0]) / 2;

                        if(!xScrollStarted) {

                            var slide = (activeChildOffsets[sliderNumber] + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;
                            var args = new helpers.args('start', settings, scrollerNode, $(scrollerNode).children(':eq(' + slide + ')'), slide, undefined);
                            $(stageNode).data('args', args);

                            if(settings.onSlideStart != '') {
                                settings.onSlideStart(args);
                            }

                        }

                        if(((yScrollDistance > settings.verticalSlideLockThreshold) || (yScrollDistance < (settings.verticalSlideLockThreshold * -1))) && (e.type == 'touchmove') && (!xScrollStarted)) {

                            preventXScroll = true;

                        }

                        if(((xScrollDistance > settings.horizontalSlideLockThreshold) || (xScrollDistance < (settings.horizontalSlideLockThreshold * -1))) && (e.type == 'touchmove')) {

                            e.preventDefault();

                        }

                        if(((xScrollDistance > settings.slideStartVelocityThreshold) || (xScrollDistance < (settings.slideStartVelocityThreshold * -1)))) {

                            xScrollStarted = true;

                        }

                        if(xScrollStarted && !preventXScroll) {

                            var scrollPosition = helpers.getSliderOffset(scrollerNode, 'x');
                            var scrollbarSubtractor = ($(currentEventNode)[0] === $(scrollbarNode)[0]) ? (sliderMin[sliderNumber]) : centeredSlideOffset;
                            var scrollbarMultiplier = ($(currentEventNode)[0] === $(scrollbarNode)[0]) ? ((sliderMin[sliderNumber] - sliderMax[sliderNumber] - centeredSlideOffset) / (scrollbarStageWidth - scrollMargin - scrollbarWidth)) : 1;
                            var elasticPullResistance = ($(currentEventNode)[0] === $(scrollbarNode)[0]) ? settings.scrollbarElasticPullResistance : settings.elasticPullResistance;
                            var snapCenteredSlideOffset = (settings.snapSlideCenter && ($(currentEventNode)[0] === $(scrollbarNode)[0])) ? 0 : centeredSlideOffset;
                            var snapCenteredSlideOffsetScrollbar = (settings.snapSlideCenter && ($(currentEventNode)[0] === $(scrollbarNode)[0])) ? centeredSlideOffset : 0;

                            if(e.type == 'touchmove') {
                                if(currentTouches != e.touches.length) {
                                    xScrollStartPosition = (scrollPosition * -1) + eventX;
                                }

                                currentTouches = e.touches.length;
                            }

                            if(settings.infiniteSlider) {

                                if(scrollPosition <= (sliderMax[sliderNumber] * -1)) {

                                    var scrollerWidth = $(scrollerNode).width();

                                    if(scrollPosition <= (sliderAbsMax[sliderNumber] * -1)) {

                                        var sum = originalOffsets[0] * -1;
                                        $(slideNodes).each(function(i) {

                                            helpers.setSliderOffset($(slideNodes)[i], sum + centeredSlideOffset);
                                            if(i < childrenOffsets.length) {
                                                childrenOffsets[i] = sum * -1;
                                            }
                                            sum = sum + slideNodeOuterWidths[i];

                                        });

                                        xScrollStartPosition = xScrollStartPosition - childrenOffsets[0] * -1;
                                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;
                                        infiniteSliderOffset[sliderNumber] = 0;

                                    } else {

                                        var lowSlideNumber = 0;
                                        var lowSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                                        $(slideNodes).each(function(i) {

                                            if(helpers.getSliderOffset(this, 'x') < lowSlideOffset) {
                                                lowSlideOffset = helpers.getSliderOffset(this, 'x');
                                                lowSlideNumber = i;
                                            }

                                        });

                                        var newOffset = sliderMin[sliderNumber] + scrollerWidth;
                                        helpers.setSliderOffset($(slideNodes)[lowSlideNumber], newOffset);

                                        sliderMin[sliderNumber] = childrenOffsets[1] * -1 + centeredSlideOffset;
                                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                                        childrenOffsets.splice(0, 1);
                                        childrenOffsets.splice(childrenOffsets.length, 0, newOffset * -1 + centeredSlideOffset);

                                        infiniteSliderOffset[sliderNumber]++;

                                    }

                                }

                                if((scrollPosition >= (sliderMin[sliderNumber] * -1)) || (scrollPosition >= 0)) {

                                    var scrollerWidth = $(scrollerNode).width();

                                    if(scrollPosition >= 0) {

                                        var sum = originalOffsets[0] * -1;
                                        $(slideNodes).each(function(i) {

                                            helpers.setSliderOffset($(slideNodes)[i], sum + centeredSlideOffset);
                                            if(i < childrenOffsets.length) {
                                                childrenOffsets[i] = sum * -1;
                                            }
                                            sum = sum + slideNodeOuterWidths[i];

                                        });

                                        xScrollStartPosition = xScrollStartPosition + childrenOffsets[0] * -1;
                                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;
                                        infiniteSliderOffset[sliderNumber] = numberOfSlides;

                                        while(((childrenOffsets[0] * -1 - scrollerWidth + centeredSlideOffset) > 0)) {

                                            var highSlideNumber = 0;
                                            var highSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                                            $(slideNodes).each(function(i) {

                                                if(helpers.getSliderOffset(this, 'x') > highSlideOffset) {
                                                    highSlideOffset = helpers.getSliderOffset(this, 'x');
                                                    highSlideNumber = i;
                                                }

                                            });

                                            var newOffset = sliderMin[sliderNumber] - slideNodeOuterWidths[highSlideNumber];
                                            helpers.setSliderOffset($(slideNodes)[highSlideNumber], newOffset);

                                            childrenOffsets.splice(0, 0, newOffset * -1 + centeredSlideOffset);
                                            childrenOffsets.splice(childrenOffsets.length-1, 1);

                                            sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                                            sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                                            infiniteSliderOffset[sliderNumber]--;
                                            activeChildOffsets[sliderNumber]++;

                                        }

                                    } else {

                                        var highSlideNumber = 0;
                                        var highSlideOffset = helpers.getSliderOffset($(slideNodes[0]), 'x');
                                        $(slideNodes).each(function(i) {

                                            if(helpers.getSliderOffset(this, 'x') > highSlideOffset) {
                                                highSlideOffset = helpers.getSliderOffset(this, 'x');
                                                highSlideNumber = i;
                                            }

                                        });

                                        var newOffset = sliderMin[sliderNumber] - slideNodeOuterWidths[highSlideNumber];
                                        helpers.setSliderOffset($(slideNodes)[highSlideNumber], newOffset);

                                        childrenOffsets.splice(0, 0, newOffset * -1 + centeredSlideOffset);
                                        childrenOffsets.splice(childrenOffsets.length-1, 1);

                                        sliderMin[sliderNumber] = childrenOffsets[0] * -1 + centeredSlideOffset;
                                        sliderMax[sliderNumber] = sliderMin[sliderNumber] + scrollerWidth - stageWidth;

                                        infiniteSliderOffset[sliderNumber]--;

                                    }

                                }

                            } else {

                                var scrollerWidth = $(scrollerNode).width();

                                if(scrollPosition > (sliderMin[sliderNumber] * -1 + centeredSlideOffset)) {

                                    edgeDegradation = (sliderMin[sliderNumber] + ((xScrollStartPosition - scrollbarSubtractor - eventX + snapCenteredSlideOffset) * -1 * scrollbarMultiplier) - scrollbarSubtractor) * elasticPullResistance * -1 / scrollbarMultiplier;

                                }

                                if(scrollPosition < (sliderMax[sliderNumber] * -1)) {

                                    edgeDegradation = (sliderMax[sliderNumber] + snapCenteredSlideOffsetScrollbar + ((xScrollStartPosition - scrollbarSubtractor - eventX) * -1 * scrollbarMultiplier) - scrollbarSubtractor) * elasticPullResistance * -1 / scrollbarMultiplier;

                                }

                            }

                            helpers.setSliderOffset(scrollerNode, ((xScrollStartPosition - scrollbarSubtractor - eventX - edgeDegradation) * -1 * scrollbarMultiplier) - scrollbarSubtractor + snapCenteredSlideOffsetScrollbar);

                            if(settings.scrollbar) {

                                helpers.showScrollbar(settings, scrollbarClass);

                                scrollbarDistance = Math.floor((xScrollStartPosition - eventX - edgeDegradation - sliderMin[sliderNumber] + snapCenteredSlideOffset) / (sliderMax[sliderNumber] - sliderMin[sliderNumber] + centeredSlideOffset) * (scrollbarStageWidth - scrollMargin - scrollbarWidth) * scrollbarMultiplier);

                                var width = scrollbarWidth;

                                if(scrollbarDistance <= 0) {

                                    width = scrollbarWidth - scrollBorder - (scrollbarDistance * -1);

                                    helpers.setSliderOffset($('.' + scrollbarClass), 0);

                                    $('.' + scrollbarClass).css({
                                        width: width + 'px'
                                    });

                                } else if(scrollbarDistance >= (scrollbarStageWidth - scrollMargin - scrollBorder - scrollbarWidth)) {

                                    width = scrollbarStageWidth - scrollMargin - scrollBorder - scrollbarDistance;

                                    helpers.setSliderOffset($('.' + scrollbarClass), scrollbarDistance);

                                    $('.' + scrollbarClass).css({
                                        width: width + 'px'
                                    });

                                } else {

                                    helpers.setSliderOffset($('.' + scrollbarClass), scrollbarDistance);

                                }

                            }

                            if(e.type == 'touchmove') {
                                lastTouch = e.touches[0].pageX;
                            }

                            var slideChanged = false;
                            var newChildOffset = helpers.calcActiveOffset(settings, (xScrollStartPosition - eventX - edgeDegradation) * -1, childrenOffsets, stageWidth, infiniteSliderOffset[sliderNumber], numberOfSlides, undefined, sliderNumber);
                            var tempOffset = (newChildOffset + infiniteSliderOffset[sliderNumber] + numberOfSlides)%numberOfSlides;

                            if(settings.infiniteSlider) {

                                if(tempOffset != activeChildInfOffsets[sliderNumber]) {
                                    slideChanged = true;
                                }

                            } else {

                                if(newChildOffset != activeChildOffsets[sliderNumber]) {
                                    slideChanged = true;
                                }

                            }

                            if(slideChanged) {

                                activeChildOffsets[sliderNumber] = newChildOffset;
                                activeChildInfOffsets[sliderNumber] = tempOffset;
                                snapOverride = true;

                                var args = new helpers.args('change', settings, scrollerNode, $(scrollerNode).children(':eq(' + tempOffset + ')'), tempOffset, tempOffset);
                                $(stageNode).data('args', args);

                                if(settings.onSlideChange != '') {
                                    settings.onSlideChange(args);
                                }

                                helpers.updateBackfaceVisibility(slideNodes, sliderNumber, numberOfSlides, settings);

                            }

                        }

                    });

                    var eventObject = $(window);

                    if(isIe8 || isIe7) {
                        var eventObject = $(document);
                    }

                    $(touchSelection).bind('touchcancel.iosSliderEvent touchend.iosSliderEvent', function(e) {

                        var e = e.originalEvent;

                        if(touchEndFlag) return false;
                        touchEndFlag = true;

                        if(touchLocks[sliderNumber] || shortContent) return true;

                        if(isUnselectable) return true;

                        if(e.touches.length != 0) {

                            for(var j = 0; j < e.touches.length; j++) {

                                if(e.touches[j].pageX == lastTouch) {
                                    helpers.slowScrollHorizontal(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, xScrollDistance, yScrollDistance, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, currentEventNode, snapOverride, centeredSlideOffset, settings);
                                }

                            }

                        } else {

                            helpers.slowScrollHorizontal(scrollerNode, slideNodes, scrollTimeouts, scrollbarClass, xScrollDistance, yScrollDistance, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, currentEventNode, snapOverride, centeredSlideOffset, settings);

                        }

                        preventXScroll = false;
                        touchStartFlag = false;

                        return true;

                    });

                    $(eventObject).bind('mouseup.iosSliderEvent-' + sliderNumber, function(e) {

                        if(xScrollStarted) {
                            anchorEvents.unbind('click.disableClick').bind('click.disableClick', helpers.preventClick);
                        } else {
                            anchorEvents.unbind('click.disableClick').bind('click.disableClick', helpers.enableClick);
                        }

                        onclickEvents.each(function() {

                            this.onclick = function(event) {
                                if(xScrollStarted) {
                                    return false;
                                }

                                if($(this).data('onclick')) $(this).data('onclick').call(this, event || window.event);
                            }

                            this.onclick = $(this).data('onclick');

                        });

                        if(parseFloat($().jquery) >= 1.8) {

                            allScrollerNodeChildren.each(function() {

                                var clickObject = $._data(this, 'events');

                                if(clickObject != undefined) {
                                    if(clickObject.click != undefined) {

                                        if(clickObject.click[0].namespace != 'iosSliderEvent') {

                                            if(!xScrollStarted) {
                                                return false;
                                            }

                                            $(this).one('click.disableClick', helpers.preventClick);
                                            var handlers = $._data(this, 'events').click;
                                            var handler = handlers.pop();
                                            handlers.splice(0, 0, handler);

                                        }

                                    }
                                }

                            });

                        } else if(parseFloat($().jquery) >= 1.6) {

                            allScrollerNodeChildren.each(function() {

                                var clickObject = $(this).data('events');

                                if(clickObject != undefined) {
                                    if(clickObject.click != undefined) {

                                        if(clickObject.click[0].namespace != 'iosSliderEvent') {

                                            if(!xScrollStarted) {
                                                return false;
                                            }

                                            $(this).one('click.disableClick', helpers.preventClick);
                                            var handlers = $(this).data('events').click;
                                            var handler = handlers.pop();
                                            handlers.splice(0, 0, handler);

                                        }

                                    }
                                }

                            });

                        }

                        if(!isEventCleared[sliderNumber]) {

                            if(shortContent) return true;

                            if(settings.desktopClickDrag) {
                                $(scrollerNode).css({
                                    cursor: grabOutCursor
                                });
                            }

                            if(settings.scrollbarDrag) {
                                $(scrollbarNode).css({
                                    cursor: grabOutCursor
                                });
                            }

                            isMouseDown = false;

                            if(currentSlider == undefined) {
                                return true;
                            }

                            helpers.slowScrollHorizontal(currentSlider, slideNodes, scrollTimeouts, scrollbarClass, xScrollDistance, yScrollDistance, scrollbarWidth, stageWidth, scrollbarStageWidth, scrollMargin, scrollBorder, originalOffsets, childrenOffsets, slideNodeOuterWidths, sliderNumber, infiniteSliderWidth, numberOfSlides, currentEventNode, snapOverride, centeredSlideOffset, settings);

                            currentSlider = undefined;

                        }

                        preventXScroll = false;
                        touchStartFlag = false;

                    });

                }

            });

        },

        destroy: function(clearStyle, node) {

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                if(clearStyle == undefined) {
                    clearStyle = true;
                }

                helpers.autoSlidePause(data.sliderNumber);
                isEventCleared[data.sliderNumber] = true;
                $(window).unbind('.iosSliderEvent-' + data.sliderNumber);
                $(document).unbind('.iosSliderEvent-' + data.sliderNumber);
                $(document).unbind('keydown.iosSliderEvent');
                $(this).unbind('.iosSliderEvent');
                $(this).children(':first-child').unbind('.iosSliderEvent');
                $(this).children(':first-child').children().unbind('.iosSliderEvent');
                $(data.settings.scrollbarBlockNode).unbind('.iosSliderEvent');

                if(clearStyle) {
                    $(this).attr('style', '');
                    $(this).children(':first-child').attr('style', '');
                    $(this).children(':first-child').children().attr('style', '');

                    $(data.settings.navSlideSelector).attr('style', '');
                    $(data.settings.navPrevSelector).attr('style', '');
                    $(data.settings.navNextSelector).attr('style', '');
                    $(data.settings.autoSlideToggleSelector).attr('style', '');
                    $(data.settings.unselectableSelector).attr('style', '');
                }

                if(data.settings.scrollbar) {
                    $('.scrollbarBlock' + data.sliderNumber).remove();
                }

                var scrollTimeouts = slideTimeouts[data.sliderNumber];

                for(var i = 0; i < scrollTimeouts.length; i++) {
                    clearTimeout(scrollTimeouts[i]);
                }

                $this.removeData('iosslider');
                $this.removeData('args');

            });

        },

        update: function(node) {

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                data.settings.startAtSlide = $this.data('args').currentSlideNumber;

                methods.destroy(false, this);

                if((data.numberOfSlides != 1) && data.settings.infiniteSlider) {
                    data.settings.startAtSlide = (activeChildOffsets[data.sliderNumber] + 1 + infiniteSliderOffset[data.sliderNumber] + data.numberOfSlides)%data.numberOfSlides;
                }

                methods.init(data.settings, this);

                var args = new helpers.args('update', data.settings, data.scrollerNode, $(data.scrollerNode).children(':eq(' + (data.settings.startAtSlide - 1) + ')'), data.settings.startAtSlide - 1, data.settings.startAtSlide - 1);
                $(data.stageNode).data('args', args);

                if(data.settings.onSliderUpdate != '') {
                    data.settings.onSliderUpdate(args);
                }

            });

        },

        addSlide: function(slideNode, slidePosition) {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                if($(data.scrollerNode).children().length == 0) {

                    $(data.scrollerNode).append(slideNode);
                    $this.data('args').currentSlideNumber = 1;

                } else if(!data.settings.infiniteSlider) {

                    if(slidePosition <= data.numberOfSlides) {
                        $(data.scrollerNode).children(':eq(' + (slidePosition - 1) + ')').before(slideNode);
                    } else {
                        $(data.scrollerNode).children(':eq(' + (slidePosition - 2) + ')').after(slideNode);
                    }

                    if($this.data('args').currentSlideNumber >= slidePosition) {
                        $this.data('args').currentSlideNumber++;
                    }

                } else {

                    if(slidePosition == 1) {
                        $(data.scrollerNode).children(':eq(0)').before(slideNode);
                    } else {
                        $(data.scrollerNode).children(':eq(' + (slidePosition - 2) + ')').after(slideNode);
                    }

                    if((infiniteSliderOffset[data.sliderNumber] < -1) && (true)) {
                        activeChildOffsets[data.sliderNumber]--;
                    }

                    if($this.data('args').currentSlideNumber >= slidePosition) {
                        activeChildOffsets[data.sliderNumber]++;
                    }

                }

                $this.data('iosslider').numberOfSlides++;

                methods.update(this);

            });

        },

        removeSlide: function(slideNumber) {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                $(data.scrollerNode).children(':eq(' + (slideNumber - 1) + ')').remove();
                if(activeChildOffsets[data.sliderNumber] > (slideNumber - 1)) {
                    activeChildOffsets[data.sliderNumber]--;
                }

                $this.data('iosslider').numberOfSlides--;

                methods.update(this);

            });

        },

        goToSlide: function(slide, duration, node) {

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');

                if((data == undefined) || data.shortContent) return false;

                slide = (slide > data.childrenOffsets.length) ? data.childrenOffsets.length - 1 : slide - 1;

                if(duration != undefined)
                    data.settings.autoSlideTransTimer = duration;

                helpers.changeSlide(slide, $(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);

            });

        },

        prevSlide: function(duration) {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                var slide = (activeChildOffsets[data.sliderNumber] + infiniteSliderOffset[data.sliderNumber] + data.numberOfSlides)%data.numberOfSlides;

                if(duration != undefined)
                    data.settings.autoSlideTransTimer = duration;

                if((slide > 0) || data.settings.infiniteSlider) {
                    helpers.changeSlide(slide - 1, $(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);
                }

                activeChildOffsets[data.sliderNumber] = slide;

            });

        },

        nextSlide: function(duration) {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                var slide = (activeChildOffsets[data.sliderNumber] + infiniteSliderOffset[data.sliderNumber] + data.numberOfSlides)%data.numberOfSlides;

                if(duration != undefined)
                    data.settings.autoSlideTransTimer = duration;

                if((slide < data.childrenOffsets.length-1) || data.settings.infiniteSlider) {
                    helpers.changeSlide(slide + 1, $(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);
                }

                activeChildOffsets[data.sliderNumber] = slide;

            });

        },

        prevPage: function(duration, node) {

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                var newOffset = helpers.getSliderOffset(data.scrollerNode, 'x') + data.stageWidth;

                if(duration != undefined)
                    data.settings.autoSlideTransTimer = duration;

                helpers.changeOffset(newOffset, $(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);

            });

        },

        nextPage: function(duration, node) {

            if(node == undefined) {
                node = this;
            }

            return $(node).each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if(data == undefined) return false;

                var newOffset = helpers.getSliderOffset(data.scrollerNode, 'x') - data.stageWidth;

                if(duration != undefined)
                    data.settings.autoSlideTransTimer = duration;

                helpers.changeOffset(newOffset, $(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);

            });

        },

        lock: function() {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                $(data.scrollerNode).css({
                    cursor: 'default'
                });
                touchLocks[data.sliderNumber] = true;

            });

        },

        unlock: function() {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                $(data.scrollerNode).css({
                    cursor: grabOutCursor
                });
                touchLocks[data.sliderNumber] = false;

            });

        },

        getData: function() {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                return data;

            });

        },

        autoSlidePause: function() {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                iosSliderSettings[data.sliderNumber].autoSlide = false;

                helpers.autoSlidePause(data.sliderNumber);

                return data;

            });

        },

        autoSlidePlay: function() {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('iosslider');
                if((data == undefined) || data.shortContent) return false;

                iosSliderSettings[data.sliderNumber].autoSlide = true;

                helpers.autoSlide($(data.scrollerNode), $(data.slideNodes), slideTimeouts[data.sliderNumber], data.scrollbarClass, data.scrollbarWidth, data.stageWidth, data.scrollbarStageWidth, data.scrollMargin, data.scrollBorder, data.originalOffsets, data.childrenOffsets, data.slideNodeOuterWidths, data.sliderNumber, data.infiniteSliderWidth, data.numberOfSlides, data.centeredSlideOffset, data.settings);

                return data;

            });

        }

    }

    /* public functions */
    $.fn.iosSlider = function(method) {

        if(methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('invalid method call!');
        }

    };

}) (jQuery);





/*! Magnific Popup - v0.9.9 - 2013-12-27
 * http://dimsemenov.com/plugins/magnific-popup/
 * Copyright (c) 2013 Dmitry Semenov; */
(function(e){var t,n,i,o,r,a,s,l="Close",c="BeforeClose",d="AfterClose",u="BeforeAppend",p="MarkupParse",f="Open",m="Change",g="mfp",h="."+g,v="mfp-ready",C="mfp-removing",y="mfp-prevent-close",w=function(){},b=!!window.jQuery,I=e(window),x=function(e,n){t.ev.on(g+e+h,n)},k=function(t,n,i,o){var r=document.createElement("div");return r.className="mfp-"+t,i&&(r.innerHTML=i),o?n&&n.appendChild(r):(r=e(r),n&&r.appendTo(n)),r},T=function(n,i){t.ev.triggerHandler(g+n,i),t.st.callbacks&&(n=n.charAt(0).toLowerCase()+n.slice(1),t.st.callbacks[n]&&t.st.callbacks[n].apply(t,e.isArray(i)?i:[i]))},E=function(n){return n===s&&t.currTemplate.closeBtn||(t.currTemplate.closeBtn=e(t.st.closeMarkup.replace("%title%",t.st.tClose)),s=n),t.currTemplate.closeBtn},_=function(){e.magnificPopup.instance||(t=new w,t.init(),e.magnificPopup.instance=t)},S=function(){var e=document.createElement("p").style,t=["ms","O","Moz","Webkit"];if(void 0!==e.transition)return!0;for(;t.length;)if(t.pop()+"Transition"in e)return!0;return!1};w.prototype={constructor:w,init:function(){var n=navigator.appVersion;t.isIE7=-1!==n.indexOf("MSIE 7."),t.isIE8=-1!==n.indexOf("MSIE 8."),t.isLowIE=t.isIE7||t.isIE8,t.isAndroid=/android/gi.test(n),t.isIOS=/iphone|ipad|ipod/gi.test(n),t.supportsTransition=S(),t.probablyMobile=t.isAndroid||t.isIOS||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent),o=e(document),t.popupsCache={}},open:function(n){i||(i=e(document.body));var r;if(n.isObj===!1){t.items=n.items.toArray(),t.index=0;var s,l=n.items;for(r=0;l.length>r;r++)if(s=l[r],s.parsed&&(s=s.el[0]),s===n.el[0]){t.index=r;break}}else t.items=e.isArray(n.items)?n.items:[n.items],t.index=n.index||0;if(t.isOpen)return t.updateItemHTML(),void 0;t.types=[],a="",t.ev=n.mainEl&&n.mainEl.length?n.mainEl.eq(0):o,n.key?(t.popupsCache[n.key]||(t.popupsCache[n.key]={}),t.currTemplate=t.popupsCache[n.key]):t.currTemplate={},t.st=e.extend(!0,{},e.magnificPopup.defaults,n),t.fixedContentPos="auto"===t.st.fixedContentPos?!t.probablyMobile:t.st.fixedContentPos,t.st.modal&&(t.st.closeOnContentClick=!1,t.st.closeOnBgClick=!1,t.st.showCloseBtn=!1,t.st.enableEscapeKey=!1),t.bgOverlay||(t.bgOverlay=k("bg").on("click"+h,function(){t.close()}),t.wrap=k("wrap").attr("tabindex",-1).on("click"+h,function(e){t._checkIfClose(e.target)&&t.close()}),t.container=k("container",t.wrap)),t.contentContainer=k("content"),t.st.preloader&&(t.preloader=k("preloader",t.container,t.st.tLoading));var c=e.magnificPopup.modules;for(r=0;c.length>r;r++){var d=c[r];d=d.charAt(0).toUpperCase()+d.slice(1),t["init"+d].call(t)}T("BeforeOpen"),t.st.showCloseBtn&&(t.st.closeBtnInside?(x(p,function(e,t,n,i){n.close_replaceWith=E(i.type)}),a+=" mfp-close-btn-in"):t.wrap.append(E())),t.st.alignTop&&(a+=" mfp-align-top"),t.fixedContentPos?t.wrap.css({overflow:t.st.overflowY,overflowX:"hidden",overflowY:t.st.overflowY}):t.wrap.css({top:I.scrollTop(),position:"absolute"}),(t.st.fixedBgPos===!1||"auto"===t.st.fixedBgPos&&!t.fixedContentPos)&&t.bgOverlay.css({height:o.height(),position:"absolute"}),t.st.enableEscapeKey&&o.on("keyup"+h,function(e){27===e.keyCode&&t.close()}),I.on("resize"+h,function(){t.updateSize()}),t.st.closeOnContentClick||(a+=" mfp-auto-cursor"),a&&t.wrap.addClass(a);var u=t.wH=I.height(),m={};if(t.fixedContentPos&&t._hasScrollBar(u)){var g=t._getScrollbarSize();g&&(m.marginRight=g)}t.fixedContentPos&&(t.isIE7?e("body, html").css("overflow","hidden"):m.overflow="hidden");var C=t.st.mainClass;return t.isIE7&&(C+=" mfp-ie7"),C&&t._addClassToMFP(C),t.updateItemHTML(),T("BuildControls"),e("html").css(m),t.bgOverlay.add(t.wrap).prependTo(t.st.prependTo||i),t._lastFocusedEl=document.activeElement,setTimeout(function(){t.content?(t._addClassToMFP(v),t._setFocus()):t.bgOverlay.addClass(v),o.on("focusin"+h,t._onFocusIn)},16),t.isOpen=!0,t.updateSize(u),T(f),n},close:function(){t.isOpen&&(T(c),t.isOpen=!1,t.st.removalDelay&&!t.isLowIE&&t.supportsTransition?(t._addClassToMFP(C),setTimeout(function(){t._close()},t.st.removalDelay)):t._close())},_close:function(){T(l);var n=C+" "+v+" ";if(t.bgOverlay.detach(),t.wrap.detach(),t.container.empty(),t.st.mainClass&&(n+=t.st.mainClass+" "),t._removeClassFromMFP(n),t.fixedContentPos){var i={marginRight:""};t.isIE7?e("body, html").css("overflow",""):i.overflow="",e("html").css(i)}o.off("keyup"+h+" focusin"+h),t.ev.off(h),t.wrap.attr("class","mfp-wrap").removeAttr("style"),t.bgOverlay.attr("class","mfp-bg"),t.container.attr("class","mfp-container"),!t.st.showCloseBtn||t.st.closeBtnInside&&t.currTemplate[t.currItem.type]!==!0||t.currTemplate.closeBtn&&t.currTemplate.closeBtn.detach(),t._lastFocusedEl&&e(t._lastFocusedEl).focus(),t.currItem=null,t.content=null,t.currTemplate=null,t.prevHeight=0,T(d)},updateSize:function(e){if(t.isIOS){var n=document.documentElement.clientWidth/window.innerWidth,i=window.innerHeight*n;t.wrap.css("height",i),t.wH=i}else t.wH=e||I.height();t.fixedContentPos||t.wrap.css("height",t.wH),T("Resize")},updateItemHTML:function(){var n=t.items[t.index];t.contentContainer.detach(),t.content&&t.content.detach(),n.parsed||(n=t.parseEl(t.index));var i=n.type;if(T("BeforeChange",[t.currItem?t.currItem.type:"",i]),t.currItem=n,!t.currTemplate[i]){var o=t.st[i]?t.st[i].markup:!1;T("FirstMarkupParse",o),t.currTemplate[i]=o?e(o):!0}r&&r!==n.type&&t.container.removeClass("mfp-"+r+"-holder");var a=t["get"+i.charAt(0).toUpperCase()+i.slice(1)](n,t.currTemplate[i]);t.appendContent(a,i),n.preloaded=!0,T(m,n),r=n.type,t.container.prepend(t.contentContainer),T("AfterChange")},appendContent:function(e,n){t.content=e,e?t.st.showCloseBtn&&t.st.closeBtnInside&&t.currTemplate[n]===!0?t.content.find(".mfp-close").length||t.content.append(E()):t.content=e:t.content="",T(u),t.container.addClass("mfp-"+n+"-holder"),t.contentContainer.append(t.content)},parseEl:function(n){var i,o=t.items[n];if(o.tagName?o={el:e(o)}:(i=o.type,o={data:o,src:o.src}),o.el){for(var r=t.types,a=0;r.length>a;a++)if(o.el.hasClass("mfp-"+r[a])){i=r[a];break}o.src=o.el.attr("data-mfp-src"),o.src||(o.src=o.el.attr("href"))}return o.type=i||t.st.type||"inline",o.index=n,o.parsed=!0,t.items[n]=o,T("ElementParse",o),t.items[n]},addGroup:function(e,n){var i=function(i){i.mfpEl=this,t._openClick(i,e,n)};n||(n={});var o="click.magnificPopup";n.mainEl=e,n.items?(n.isObj=!0,e.off(o).on(o,i)):(n.isObj=!1,n.delegate?e.off(o).on(o,n.delegate,i):(n.items=e,e.off(o).on(o,i)))},_openClick:function(n,i,o){var r=void 0!==o.midClick?o.midClick:e.magnificPopup.defaults.midClick;if(r||2!==n.which&&!n.ctrlKey&&!n.metaKey){var a=void 0!==o.disableOn?o.disableOn:e.magnificPopup.defaults.disableOn;if(a)if(e.isFunction(a)){if(!a.call(t))return!0}else if(a>I.width())return!0;n.type&&(n.preventDefault(),t.isOpen&&n.stopPropagation()),o.el=e(n.mfpEl),o.delegate&&(o.items=i.find(o.delegate)),t.open(o)}},updateStatus:function(e,i){if(t.preloader){n!==e&&t.container.removeClass("mfp-s-"+n),i||"loading"!==e||(i=t.st.tLoading);var o={status:e,text:i};T("UpdateStatus",o),e=o.status,i=o.text,t.preloader.html(i),t.preloader.find("a").on("click",function(e){e.stopImmediatePropagation()}),t.container.addClass("mfp-s-"+e),n=e}},_checkIfClose:function(n){if(!e(n).hasClass(y)){var i=t.st.closeOnContentClick,o=t.st.closeOnBgClick;if(i&&o)return!0;if(!t.content||e(n).hasClass("mfp-close")||t.preloader&&n===t.preloader[0])return!0;if(n===t.content[0]||e.contains(t.content[0],n)){if(i)return!0}else if(o&&e.contains(document,n))return!0;return!1}},_addClassToMFP:function(e){t.bgOverlay.addClass(e),t.wrap.addClass(e)},_removeClassFromMFP:function(e){this.bgOverlay.removeClass(e),t.wrap.removeClass(e)},_hasScrollBar:function(e){return(t.isIE7?o.height():document.body.scrollHeight)>(e||I.height())},_setFocus:function(){(t.st.focus?t.content.find(t.st.focus).eq(0):t.wrap).focus()},_onFocusIn:function(n){return n.target===t.wrap[0]||e.contains(t.wrap[0],n.target)?void 0:(t._setFocus(),!1)},_parseMarkup:function(t,n,i){var o;i.data&&(n=e.extend(i.data,n)),T(p,[t,n,i]),e.each(n,function(e,n){if(void 0===n||n===!1)return!0;if(o=e.split("_"),o.length>1){var i=t.find(h+"-"+o[0]);if(i.length>0){var r=o[1];"replaceWith"===r?i[0]!==n[0]&&i.replaceWith(n):"img"===r?i.is("img")?i.attr("src",n):i.replaceWith('<img src="'+n+'" class="'+i.attr("class")+'" />'):i.attr(o[1],n)}}else t.find(h+"-"+e).html(n)})},_getScrollbarSize:function(){if(void 0===t.scrollbarSize){var e=document.createElement("div");e.id="mfp-sbm",e.style.cssText="width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;",document.body.appendChild(e),t.scrollbarSize=e.offsetWidth-e.clientWidth,document.body.removeChild(e)}return t.scrollbarSize}},e.magnificPopup={instance:null,proto:w.prototype,modules:[],open:function(t,n){return _(),t=t?e.extend(!0,{},t):{},t.isObj=!0,t.index=n||0,this.instance.open(t)},close:function(){return e.magnificPopup.instance&&e.magnificPopup.instance.close()},registerModule:function(t,n){n.options&&(e.magnificPopup.defaults[t]=n.options),e.extend(this.proto,n.proto),this.modules.push(t)},defaults:{disableOn:0,key:null,midClick:!1,mainClass:"",preloader:!0,focus:"",closeOnContentClick:!1,closeOnBgClick:!0,closeBtnInside:!0,showCloseBtn:!0,enableEscapeKey:!0,modal:!1,alignTop:!1,removalDelay:0,prependTo:null,fixedContentPos:"auto",fixedBgPos:"auto",overflowY:"auto",closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',tClose:"Close (Esc)",tLoading:"Loading..."}},e.fn.magnificPopup=function(n){_();var i=e(this);if("string"==typeof n)if("open"===n){var o,r=b?i.data("magnificPopup"):i[0].magnificPopup,a=parseInt(arguments[1],10)||0;r.items?o=r.items[a]:(o=i,r.delegate&&(o=o.find(r.delegate)),o=o.eq(a)),t._openClick({mfpEl:o},i,r)}else t.isOpen&&t[n].apply(t,Array.prototype.slice.call(arguments,1));else n=e.extend(!0,{},n),b?i.data("magnificPopup",n):i[0].magnificPopup=n,t.addGroup(i,n);return i};var P,O,z,M="inline",B=function(){z&&(O.after(z.addClass(P)).detach(),z=null)};e.magnificPopup.registerModule(M,{options:{hiddenClass:"hide",markup:"",tNotFound:"Content not found"},proto:{initInline:function(){t.types.push(M),x(l+"."+M,function(){B()})},getInline:function(n,i){if(B(),n.src){var o=t.st.inline,r=e(n.src);if(r.length){var a=r[0].parentNode;a&&a.tagName&&(O||(P=o.hiddenClass,O=k(P),P="mfp-"+P),z=r.after(O).detach().removeClass(P)),t.updateStatus("ready")}else t.updateStatus("error",o.tNotFound),r=e("<div>");return n.inlineElement=r,r}return t.updateStatus("ready"),t._parseMarkup(i,{},n),i}}});var F,H="ajax",L=function(){F&&i.removeClass(F)},A=function(){L(),t.req&&t.req.abort()};e.magnificPopup.registerModule(H,{options:{settings:null,cursor:"mfp-ajax-cur",tError:'<a href="%url%">The content</a> could not be loaded.'},proto:{initAjax:function(){t.types.push(H),F=t.st.ajax.cursor,x(l+"."+H,A),x("BeforeChange."+H,A)},getAjax:function(n){F&&i.addClass(F),t.updateStatus("loading");var o=e.extend({url:n.src,success:function(i,o,r){var a={data:i,xhr:r};T("ParseAjax",a),t.appendContent(e(a.data),H),n.finished=!0,L(),t._setFocus(),setTimeout(function(){t.wrap.addClass(v)},16),t.updateStatus("ready"),T("AjaxContentAdded")},error:function(){L(),n.finished=n.loadError=!0,t.updateStatus("error",t.st.ajax.tError.replace("%url%",n.src))}},t.st.ajax.settings);return t.req=e.ajax(o),""}}});var j,N=function(n){if(n.data&&void 0!==n.data.title)return n.data.title;var i=t.st.image.titleSrc;if(i){if(e.isFunction(i))return i.call(t,n);if(n.el)return n.el.attr(i)||""}return""};e.magnificPopup.registerModule("image",{options:{markup:'<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',cursor:"mfp-zoom-out-cur",titleSrc:"title",verticalFit:!0,tError:'<a href="%url%">The image</a> could not be loaded.'},proto:{initImage:function(){var e=t.st.image,n=".image";t.types.push("image"),x(f+n,function(){"image"===t.currItem.type&&e.cursor&&i.addClass(e.cursor)}),x(l+n,function(){e.cursor&&i.removeClass(e.cursor),I.off("resize"+h)}),x("Resize"+n,t.resizeImage),t.isLowIE&&x("AfterChange",t.resizeImage)},resizeImage:function(){var e=t.currItem;if(e&&e.img&&t.st.image.verticalFit){var n=0;t.isLowIE&&(n=parseInt(e.img.css("padding-top"),10)+parseInt(e.img.css("padding-bottom"),10)),e.img.css("max-height",t.wH-n)}},_onImageHasSize:function(e){e.img&&(e.hasSize=!0,j&&clearInterval(j),e.isCheckingImgSize=!1,T("ImageHasSize",e),e.imgHidden&&(t.content&&t.content.removeClass("mfp-loading"),e.imgHidden=!1))},findImageSize:function(e){var n=0,i=e.img[0],o=function(r){j&&clearInterval(j),j=setInterval(function(){return i.naturalWidth>0?(t._onImageHasSize(e),void 0):(n>200&&clearInterval(j),n++,3===n?o(10):40===n?o(50):100===n&&o(500),void 0)},r)};o(1)},getImage:function(n,i){var o=0,r=function(){n&&(n.img[0].complete?(n.img.off(".mfploader"),n===t.currItem&&(t._onImageHasSize(n),t.updateStatus("ready")),n.hasSize=!0,n.loaded=!0,T("ImageLoadComplete")):(o++,200>o?setTimeout(r,100):a()))},a=function(){n&&(n.img.off(".mfploader"),n===t.currItem&&(t._onImageHasSize(n),t.updateStatus("error",s.tError.replace("%url%",n.src))),n.hasSize=!0,n.loaded=!0,n.loadError=!0)},s=t.st.image,l=i.find(".mfp-img");if(l.length){var c=document.createElement("img");c.className="mfp-img",n.img=e(c).on("load.mfploader",r).on("error.mfploader",a),c.src=n.src,l.is("img")&&(n.img=n.img.clone()),c=n.img[0],c.naturalWidth>0?n.hasSize=!0:c.width||(n.hasSize=!1)}return t._parseMarkup(i,{title:N(n),img_replaceWith:n.img},n),t.resizeImage(),n.hasSize?(j&&clearInterval(j),n.loadError?(i.addClass("mfp-loading"),t.updateStatus("error",s.tError.replace("%url%",n.src))):(i.removeClass("mfp-loading"),t.updateStatus("ready")),i):(t.updateStatus("loading"),n.loading=!0,n.hasSize||(n.imgHidden=!0,i.addClass("mfp-loading"),t.findImageSize(n)),i)}}});var W,R=function(){return void 0===W&&(W=void 0!==document.createElement("p").style.MozTransform),W};e.magnificPopup.registerModule("zoom",{options:{enabled:!1,easing:"ease-in-out",duration:300,opener:function(e){return e.is("img")?e:e.find("img")}},proto:{initZoom:function(){var e,n=t.st.zoom,i=".zoom";if(n.enabled&&t.supportsTransition){var o,r,a=n.duration,s=function(e){var t=e.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),i="all "+n.duration/1e3+"s "+n.easing,o={position:"fixed",zIndex:9999,left:0,top:0,"-webkit-backface-visibility":"hidden"},r="transition";return o["-webkit-"+r]=o["-moz-"+r]=o["-o-"+r]=o[r]=i,t.css(o),t},d=function(){t.content.css("visibility","visible")};x("BuildControls"+i,function(){if(t._allowZoom()){if(clearTimeout(o),t.content.css("visibility","hidden"),e=t._getItemToZoom(),!e)return d(),void 0;r=s(e),r.css(t._getOffset()),t.wrap.append(r),o=setTimeout(function(){r.css(t._getOffset(!0)),o=setTimeout(function(){d(),setTimeout(function(){r.remove(),e=r=null,T("ZoomAnimationEnded")},16)},a)},16)}}),x(c+i,function(){if(t._allowZoom()){if(clearTimeout(o),t.st.removalDelay=a,!e){if(e=t._getItemToZoom(),!e)return;r=s(e)}r.css(t._getOffset(!0)),t.wrap.append(r),t.content.css("visibility","hidden"),setTimeout(function(){r.css(t._getOffset())},16)}}),x(l+i,function(){t._allowZoom()&&(d(),r&&r.remove(),e=null)})}},_allowZoom:function(){return"image"===t.currItem.type},_getItemToZoom:function(){return t.currItem.hasSize?t.currItem.img:!1},_getOffset:function(n){var i;i=n?t.currItem.img:t.st.zoom.opener(t.currItem.el||t.currItem);var o=i.offset(),r=parseInt(i.css("padding-top"),10),a=parseInt(i.css("padding-bottom"),10);o.top-=e(window).scrollTop()-r;var s={width:i.width(),height:(b?i.innerHeight():i[0].offsetHeight)-a-r};return R()?s["-moz-transform"]=s.transform="translate("+o.left+"px,"+o.top+"px)":(s.left=o.left,s.top=o.top),s}}});var Z="iframe",q="//about:blank",D=function(e){if(t.currTemplate[Z]){var n=t.currTemplate[Z].find("iframe");n.length&&(e||(n[0].src=q),t.isIE8&&n.css("display",e?"block":"none"))}};e.magnificPopup.registerModule(Z,{options:{markup:'<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',srcAction:"iframe_src",patterns:{youtube:{index:"youtube.com",id:"v=",src:"//www.youtube.com/embed/%id%?autoplay=1"},vimeo:{index:"vimeo.com/",id:"/",src:"//player.vimeo.com/video/%id%?autoplay=1"},gmaps:{index:"//maps.google.",src:"%id%&output=embed"}}},proto:{initIframe:function(){t.types.push(Z),x("BeforeChange",function(e,t,n){t!==n&&(t===Z?D():n===Z&&D(!0))}),x(l+"."+Z,function(){D()})},getIframe:function(n,i){var o=n.src,r=t.st.iframe;e.each(r.patterns,function(){return o.indexOf(this.index)>-1?(this.id&&(o="string"==typeof this.id?o.substr(o.lastIndexOf(this.id)+this.id.length,o.length):this.id.call(this,o)),o=this.src.replace("%id%",o),!1):void 0});var a={};return r.srcAction&&(a[r.srcAction]=o),t._parseMarkup(i,a,n),t.updateStatus("ready"),i}}});var K=function(e){var n=t.items.length;return e>n-1?e-n:0>e?n+e:e},Y=function(e,t,n){return e.replace(/%curr%/gi,t+1).replace(/%total%/gi,n)};e.magnificPopup.registerModule("gallery",{options:{enabled:!1,arrowMarkup:'<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',preload:[0,2],navigateByImgClick:!0,arrows:!0,tPrev:"Previous (Left arrow key)",tNext:"Next (Right arrow key)",tCounter:"%curr% of %total%"},proto:{initGallery:function(){var n=t.st.gallery,i=".mfp-gallery",r=Boolean(e.fn.mfpFastClick);return t.direction=!0,n&&n.enabled?(a+=" mfp-gallery",x(f+i,function(){n.navigateByImgClick&&t.wrap.on("click"+i,".mfp-img",function(){return t.items.length>1?(t.next(),!1):void 0}),o.on("keydown"+i,function(e){37===e.keyCode?t.prev():39===e.keyCode&&t.next()})}),x("UpdateStatus"+i,function(e,n){n.text&&(n.text=Y(n.text,t.currItem.index,t.items.length))}),x(p+i,function(e,i,o,r){var a=t.items.length;o.counter=a>1?Y(n.tCounter,r.index,a):""}),x("BuildControls"+i,function(){if(t.items.length>1&&n.arrows&&!t.arrowLeft){var i=n.arrowMarkup,o=t.arrowLeft=e(i.replace(/%title%/gi,n.tPrev).replace(/%dir%/gi,"left")).addClass(y),a=t.arrowRight=e(i.replace(/%title%/gi,n.tNext).replace(/%dir%/gi,"right")).addClass(y),s=r?"mfpFastClick":"click";o[s](function(){t.prev()}),a[s](function(){t.next()}),t.isIE7&&(k("b",o[0],!1,!0),k("a",o[0],!1,!0),k("b",a[0],!1,!0),k("a",a[0],!1,!0)),t.container.append(o.add(a))}}),x(m+i,function(){t._preloadTimeout&&clearTimeout(t._preloadTimeout),t._preloadTimeout=setTimeout(function(){t.preloadNearbyImages(),t._preloadTimeout=null},16)}),x(l+i,function(){o.off(i),t.wrap.off("click"+i),t.arrowLeft&&r&&t.arrowLeft.add(t.arrowRight).destroyMfpFastClick(),t.arrowRight=t.arrowLeft=null}),void 0):!1},next:function(){t.direction=!0,t.index=K(t.index+1),t.updateItemHTML()},prev:function(){t.direction=!1,t.index=K(t.index-1),t.updateItemHTML()},goTo:function(e){t.direction=e>=t.index,t.index=e,t.updateItemHTML()},preloadNearbyImages:function(){var e,n=t.st.gallery.preload,i=Math.min(n[0],t.items.length),o=Math.min(n[1],t.items.length);for(e=1;(t.direction?o:i)>=e;e++)t._preloadItem(t.index+e);for(e=1;(t.direction?i:o)>=e;e++)t._preloadItem(t.index-e)},_preloadItem:function(n){if(n=K(n),!t.items[n].preloaded){var i=t.items[n];i.parsed||(i=t.parseEl(n)),T("LazyLoad",i),"image"===i.type&&(i.img=e('<img class="mfp-img" />').on("load.mfploader",function(){i.hasSize=!0}).on("error.mfploader",function(){i.hasSize=!0,i.loadError=!0,T("LazyLoadError",i)}).attr("src",i.src)),i.preloaded=!0}}}});var U="retina";e.magnificPopup.registerModule(U,{options:{replaceSrc:function(e){return e.src.replace(/\.\w+$/,function(e){return"@2x"+e})},ratio:1},proto:{initRetina:function(){if(window.devicePixelRatio>1){var e=t.st.retina,n=e.ratio;n=isNaN(n)?n():n,n>1&&(x("ImageHasSize."+U,function(e,t){t.img.css({"max-width":t.img[0].naturalWidth/n,width:"100%"})}),x("ElementParse."+U,function(t,i){i.src=e.replaceSrc(i,n)}))}}}}),function(){var t=1e3,n="ontouchstart"in window,i=function(){I.off("touchmove"+r+" touchend"+r)},o="mfpFastClick",r="."+o;e.fn.mfpFastClick=function(o){return e(this).each(function(){var a,s=e(this);if(n){var l,c,d,u,p,f;s.on("touchstart"+r,function(e){u=!1,f=1,p=e.originalEvent?e.originalEvent.touches[0]:e.touches[0],c=p.clientX,d=p.clientY,I.on("touchmove"+r,function(e){p=e.originalEvent?e.originalEvent.touches:e.touches,f=p.length,p=p[0],(Math.abs(p.clientX-c)>10||Math.abs(p.clientY-d)>10)&&(u=!0,i())}).on("touchend"+r,function(e){i(),u||f>1||(a=!0,e.preventDefault(),clearTimeout(l),l=setTimeout(function(){a=!1},t),o())})})}s.on("click"+r,function(){a||o()})})},e.fn.destroyMfpFastClick=function(){e(this).off("touchstart"+r+" click"+r),n&&I.off("touchmove"+r+" touchend"+r)}}(),_()})(window.jQuery||window.Zepto);


/**
 * placeholder
 */
(function(q,f,d){function r(b){var a={},c=/^jQuery\d+$/;d.each(b.attributes,function(b,d){d.specified&&!c.test(d.name)&&(a[d.name]=d.value)});return a}function g(b,a){var c=d(this);if(this.value==c.attr("placeholder")&&c.hasClass("placeholder"))if(c.data("placeholder-password")){c=c.hide().next().show().attr("id",c.removeAttr("id").data("placeholder-id"));if(!0===b)return c[0].value=a;c.focus()}else this.value="",c.removeClass("placeholder"),this==m()&&this.select()}function k(){var b,a=d(this),c=
    this.id;if(""==this.value){if("password"==this.type){if(!a.data("placeholder-textinput")){try{b=a.clone().attr({type:"text"})}catch(e){b=d("<input>").attr(d.extend(r(this),{type:"text"}))}b.removeAttr("name").data({"placeholder-password":a,"placeholder-id":c}).bind("focus.placeholder",g);a.data({"placeholder-textinput":b,"placeholder-id":c}).before(b)}a=a.removeAttr("id").hide().prev().attr("id",c).show()}a.addClass("placeholder");a[0].value=a.attr("placeholder")}else a.removeClass("placeholder")}
    function m(){try{return f.activeElement}catch(b){}}var h="placeholder"in f.createElement("input"),l="placeholder"in f.createElement("textarea"),e=d.fn,n=d.valHooks,p=d.propHooks;h&&l?(e=e.placeholder=function(){return this},e.input=e.textarea=!0):(e=e.placeholder=function(){this.filter((h?"textarea":":input")+"[placeholder]").not(".placeholder").bind({"focus.placeholder":g,"blur.placeholder":k}).data("placeholder-enabled",!0).trigger("blur.placeholder");return this},e.input=h,e.textarea=l,e={get:function(b){var a=
        d(b),c=a.data("placeholder-password");return c?c[0].value:a.data("placeholder-enabled")&&a.hasClass("placeholder")?"":b.value},set:function(b,a){var c=d(b),e=c.data("placeholder-password");if(e)return e[0].value=a;if(!c.data("placeholder-enabled"))return b.value=a;""==a?(b.value=a,b!=m()&&k.call(b)):c.hasClass("placeholder")?g.call(b,!0,a)||(b.value=a):b.value=a;return c}},h||(n.input=e,p.value=e),l||(n.textarea=e,p.value=e),d(function(){d(f).delegate("form","submit.placeholder",function(){var b=
        d(".placeholder",this).each(g);setTimeout(function(){b.each(k)},10)})}),d(q).bind("beforeunload.placeholder",function(){d(".placeholder").each(function(){this.value=""})}))})(this,document,jQuery);



// requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
// MIT license
(function(){for(var e=0,b=["ms","moz","webkit","o"],a=0;a<b.length&&!window.requestAnimationFrame;++a)window.requestAnimationFrame=window[b[a]+"RequestAnimationFrame"],window.cancelAnimationFrame=window[b[a]+"CancelAnimationFrame"]||window[b[a]+"CancelRequestAnimationFrame"];window.requestAnimationFrame||(window.requestAnimationFrame=function(a,b){var c=(new Date).getTime(),d=Math.max(0,16-(c-e)),f=window.setTimeout(function(){a(c+d)},d);e=c+d;return f});window.cancelAnimationFrame||(window.cancelAnimationFrame=
    function(a){clearTimeout(a)})})();


/* global jQuery:false */


var tdDetect = {};

( function(){
    "use strict";
    tdDetect = {
        isIe8: false,
        isIe9 : false,
        isIe10 : false,
        isIe11 : false,
        isIe : false,
        isSafari : false,
        isChrome : false,
        isIpad : false,
        isTouchDevice : false,
        hasHistory : false,
        isPhoneScreen : false,
        isIos : false,
        isAndroid : false,
        isOsx : false,
        isFirefox : false,
        isWinOs : false,
        isMobileDevice:false,
        htmlJqueryObj:null, //here we keep the jQuery object for the HTML element

        /**
         * function to check the phone screen
         * @see tdEvents
         * The jQuery windows width is not reliable cross browser!
         */
        runIsPhoneScreen: function () {
            if ( (jQuery(window).width() < 768 || jQuery(window).height() < 768) && false === tdDetect.isIpad ) {
                tdDetect.isPhoneScreen = true;

            } else {
                tdDetect.isPhoneScreen = false;
            }
        },


        set: function (detector_name, value) {
            tdDetect[detector_name] = value;
            //alert('tdDetect: ' + detector_name + ': ' + value);
        }
    };


    tdDetect.htmlJqueryObj = jQuery('html');


    // is touch device ?
    if ( -1 !== navigator.appVersion.indexOf("Win") ) {
        tdDetect.set('isWinOs', true);
    }

    // it looks like it has to have ontouchstart in window and NOT be windows OS. Why? we don't know.
    if ( !!('ontouchstart' in window) && !tdDetect.isWinOs ) {
        tdDetect.set('isTouchDevice', true);
    }


    // detect ie8
    if ( tdDetect.htmlJqueryObj.is('.ie8') ) {
        tdDetect.set('isIe8', true);
        tdDetect.set('isIe', true);
    }

    // detect ie9
    if ( tdDetect.htmlJqueryObj.is('.ie9') ) {
        tdDetect.set('isIe9', true);
        tdDetect.set('isIe', true);
    }

    // detect ie10 - also adds the ie10 class //it also detects windows mobile IE as IE10
    if( navigator.userAgent.indexOf("MSIE 10.0") > -1 ){
        tdDetect.set('isIe10', true);
        tdDetect.set('isIe', true);
    }

    //ie 11 check - also adds the ie11 class - it may detect ie on windows mobile
    if ( !!navigator.userAgent.match(/Trident.*rv\:11\./) ){
        tdDetect.set('isIe11', true);
        //this.isIe = true; //do not flag ie11 as isIe
    }


    //do we have html5 history support?
    if (window.history && window.history.pushState) {
        tdDetect.set('hasHistory', true);
    }

    //check for safary
    if ( -1 !== navigator.userAgent.indexOf('Safari')  && -1 === navigator.userAgent.indexOf('Chrome') ) {
        tdDetect.set('isSafari', true);
    }

    //chrome and chrome-ium check
    if (/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())) {
        tdDetect.set('isChrome', true);
    }

    if ( null !== navigator.userAgent.match(/iPad/i)) {
        tdDetect.set('isIpad', true);
    }


    if (/(iPad|iPhone|iPod)/g.test( navigator.userAgent )) {
        tdDetect.set('isIos', true);
    }


    //detect if we run on a mobile device - ipad included - used by the modal / scroll to @see scrollIntoView
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        tdDetect.set('isMobileDevice', true);
    }

    tdDetect.runIsPhoneScreen();

    //test for android
    var user_agent = navigator.userAgent.toLowerCase();
    if ( user_agent.indexOf("android") > -1 ) {
        tdDetect.set('isAndroid', true);
    }


    if ( -1 !== navigator.userAgent.indexOf('Mac OS X') ) {
        tdDetect.set('isOsx', true);
    }

    if ( -1 !== navigator.userAgent.indexOf('Firefox') ) {
        tdDetect.set('isFirefox', true);
    }

})();

/**
 * Created by tagdiv on 13.05.2015.
 */

/* global tdDetect: {} */
/* global jQuery: {} */

var tdViewport = {};

(function(){

    "use strict";

    tdViewport = {

        /**
         * - initial (default) value of the _currentIntervalIndex
         * - it's used by third part libraries
         * - it used just as constant value
         */
        INTERVAL_INITIAL_INDEX: -1,



        /**
         * - keep the current interval index
         * - it should be modified/taken just by setter/getter methods
         * - after computing, it should not be a negative value
         */
        _currentIntervalIndex : tdViewport.INTERVAL_INITIAL_INDEX,



        /**
         * - it keeps the interval index
         * - it should be modified/taken just by setter/getter methods
         * - it must be a crescendo positive values
         */
        _intervalList : [],



        /**
         *
         */
        init: function() {
            if (('undefined' !== typeof window.td_viewport_interval_list) && (Array === window.td_viewport_interval_list.constructor)) {

                for (var i = 0; i < window.td_viewport_interval_list.length; i++) {
                    var item = new tdViewport.item();

                    var currentVal = window.td_viewport_interval_list[i];

                    // the check is done to be sure that the intervals are well formatted
                    if (!currentVal.hasOwnProperty('limitBottom') || !currentVal.hasOwnProperty('sidebarWidth')) {
                        break;
                    }

                    item.limitBottom = currentVal.limitBottom;
                    item.sidebarWidth = currentVal.sidebarWidth;

                    tdViewport._items.push(item);
                }

                tdViewport.detectChanges();
            }
        },



        /**
         * - getter of the _currentIntervalIndex
         * - it should be used by outsiders libraries
         * @returns {*}
         */
        getCurrentIntervalIndex : function() {
            return tdViewport._currentIntervalIndex;
        },



        /**
         * - setter of the _intervalList
          - it should be used by outsiders libraries
         * @param value
         */
        setIntervalList : function(value) {
            tdViewport._intervalList = value;
        },



        /**
         * - getter of the _intervalList
         * - it should be used by outsiders libraries
         * @returns {*}
         */
        getIntervalList : function() {
            return tdViewport._intervalList;
        },



        /**
         * - getter of the tdViewport current item
         * - it should be used by outsiders libraries
         * @returns {*}
         */
        getCurrentIntervalItem : function() {

            if ((tdViewport.INTERVAL_INITIAL_INDEX === tdViewport._currentIntervalIndex) || (0 === tdViewport._currentIntervalIndex)) {
                return null;
            }
            return tdViewport._items[tdViewport._currentIntervalIndex - 1];
        },



        _items : [],



        item : function() {
            this.limitBottom = undefined;
            this.sidebarWidth = undefined;
        },





        /**
         * - detect view port changes
         * - it returns true if the change view port has changed, false otherwise
         * - it also sets the _currentIntervalIndex
         * @returns {boolean} True when viewport has changed
         */
        detectChanges: function() {
            var result = false;

            var realViewPortWidth = 0;
            var localCurrentIntervalIndex = 0;

            if (true === tdDetect.isSafari) {
                realViewPortWidth = this._safariWiewPortWidth.getRealWidth();
            } else {
                realViewPortWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            }

            for (var i = 0; i < tdViewport._items.length; i++) {

                if (realViewPortWidth <= tdViewport._items[i].limitBottom) {

                    if (localCurrentIntervalIndex !== tdViewport._currentIntervalIndex) {
                        tdViewport._currentIntervalIndex = localCurrentIntervalIndex;
                        result = true;

                        tdViewport.log('changing viewport ' + tdViewport._currentIntervalIndex + ' ~ ' + realViewPortWidth);
                    }
                    break;
                }
                localCurrentIntervalIndex++;
            }

            if ((false === result) && (localCurrentIntervalIndex !== tdViewport._currentIntervalIndex)) {
                tdViewport._currentIntervalIndex = localCurrentIntervalIndex;
                result = true;

                tdViewport.log('changing viewport ' + tdViewport._currentIntervalIndex + ' ~ ' + realViewPortWidth);
            }
            return result;
        },


        /**
         * get the real view port width on safari
         * @type {{divAdded: boolean, divJqueryObject: string, getRealWidth: Function}}
         */
        _safariWiewPortWidth : {
            divAdded : false,
            divJqueryObject : '',

            getRealWidth : function() {
                if (false === this.divAdded) {
                    // we don't have a div present
                    this.divJqueryObject = jQuery('<div>')
                        .css({
                            "height": "1px",
                            "position": "absolute",
                            "top": "-1px",
                            "left": "0",
                            "right": "0",
                            "visibility": "hidden",
                            "z-index": "-1"
                        });
                    this.divJqueryObject.appendTo('body');
                    this.divAdded = true;
                }
                return this.divJqueryObject.width();
            }
        },



        log: function log(msg) {
            //console.log(msg);
        }
    };

    tdViewport.init();

})();

/*  ----------------------------------------------------------------------------
    Menu script
 */

/* global jQuery:{} */
/* global tdDetect:{} */

//top menu works only on 1 level, the other submenus are hidden from css
//on tablets, wide level 3 submenus may go out of screen

var tdMenu = {};
(function(){
    'use strict';

    tdMenu = {

        //submenu items (used on unbind)
        _itemsWithSubmenu: null,
        //main menu (used on unbind)
        _mainMenu: null,

        //on touch - when you click outside the menu it will close all menus
        _outsideClickArea: null,
        _outsideClickExcludedAreas: '#td-header-menu .sf-menu, #td-header-menu .sf-menu *, .menu-top-container, .menu-top-container *',

        //added when menu is open
        _openMenuClass: 'sfHover',
        _openMenuBodyClass: 'td-open-menu',



        /*
         * initialize menu
         */
        init: function() {
            //get menu items
            var mainMenu = jQuery('#td-header-menu .sf-menu'),
                menus = jQuery('#td-header-menu .sf-menu, .top-header-menu'),
                menuLinks = menus.find('.menu-item-has-children > a, .td-mega-menu > a');

            //add dropdown arrow on items with submenu
            menuLinks.append('<i class="td-icon-menu-down"></i>');

            //main menu width adjustment (top menu will use css)
            mainMenu.supersubs({
                minWidth: 10, // minimum width of sub-menus in em units
                maxWidth: 20, // maximum width of sub-menus in em units
                extraWidth: 1 // extra width can ensure lines don't sometimes turn over
            });

            //add sf-with-ul class to all anchors
            menuLinks.addClass('sf-with-ul');
            //add sf-js-enabled class
            menus.addClass('sf-js-enabled');
            //hide all submenus
            menuLinks.parent().find('ul').first().css('display', 'none');

            //set unbind items
            tdMenu._mainMenu = mainMenu;
            tdMenu._itemsWithSubmenu = menuLinks;
            tdMenu._outsideClickArea = jQuery(window).not(tdMenu._outsideClickExcludedAreas);
            //initialize menu
            tdMenu._setHover(menuLinks, mainMenu);
        },




        /**
         * adjust submenu position - if it goes out of window move it to the left
         * @param item - submenu item
         * @private
         */
        _getSubmenuPosition: function(item) {
            var windowWidth = jQuery(window).width(),
                submenuElement = item.children("ul").first();
            if (submenuElement.length > 0) {
                var submenuOffsetWidth = submenuElement.offset().left + submenuElement.width();
                if (submenuOffsetWidth > windowWidth) {
                    if (submenuElement.parent().parent().hasClass("sf-menu")) {
                        //main menu
                        submenuElement.css("left", "-" + (submenuOffsetWidth - windowWidth) + "px");
                    } else {
                        //submenu
                        submenuElement.addClass("reversed").css("left", "-" + (submenuElement.width() + 0) + "px");
                    }
                }
            }
        },



        /**
         * calculate mouse direction
         * @param x1 - old x position
         * @param y1 - old y position
         * @param x2 - current x position
         * @param y2 - current y position
         * @returns {number}
         * @private
         */
        _getMouseAngleDirection: function(x1, y1, x2, y2) {
            var dx = x2 - x1,
                dy = y2 - y1;

            return Math.atan2(dx, dy) / Math.PI * 180;
        },



        /**
         * set menu functionality for desktop and touch devices
         * @param menuLinks - submenu links (anchors)
         * @param mainMenu - main menu
         * @private
         */
        _setHover: function(menuLinks, mainMenu) {

            /* TOUCH DEVICES */
            if (tdDetect.isTouchDevice) {

                //close menu when you tap outside of it
                jQuery(document).on('touchstart', 'body', function(e) {
                    var menuItems = menuLinks.parent(),
                        pageBody = jQuery('body');
                        //check if a menu is open and if the target is outside the menu
                        if (pageBody.hasClass(tdMenu._openMenuBodyClass) && !menuItems.is(e.target) && menuItems.has(e.target).length === 0) {
                            menuItems.removeClass(tdMenu._openMenuClass);
                            menuItems.children('ul').hide();
                            //remove open menu class from <body>
                            pageBody.removeClass(tdMenu._openMenuBodyClass);
                        }
                });

                //open-close the menu on touch
                menuLinks.on('touchstart',
                    function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        var currentMenuLink = jQuery(this),
                            currentMenu = currentMenuLink.parent(),
                            pageBody = jQuery('body');

                        //menu is open
                        if (currentMenu.hasClass(tdMenu._openMenuClass)) {
                            //has a link, open it
                            if (currentMenuLink.attr('href') !== null && currentMenuLink.attr('href') !== '#') {
                                window.location.href = currentMenuLink.attr('href');

                            //no link - close it
                            } else {
                                //if it's a main menu remove the body class
                                if (currentMenu.parent().hasClass('sf-menu') || currentMenu.parent().hasClass('top-header-menu')) {
                                    pageBody.removeClass(tdMenu._openMenuBodyClass);
                                }
                                currentMenu.removeClass(tdMenu._openMenuClass);
                                //close submenus
                                currentMenu.find('ul').hide();
                                currentMenu.find('li').removeClass(tdMenu._openMenuClass);
                            }

                        //menu is not open
                        } else {
                            //a sibling may be open and we have to close it
                            if (currentMenu.parent().hasClass('sf-menu') || currentMenu.parent().hasClass('top-header-menu')) {
                                //main menu - close all menus
                                menuLinks.parent().removeClass(tdMenu._openMenuClass);
                                menuLinks.parent().children('ul').hide();
                            } else {
                                //submenu - close all siblings-submenus and open the current one
                                var currentMenuSiblings = currentMenu.siblings();
                                currentMenuSiblings.removeClass(tdMenu._openMenuClass);
                                //close siblings
                                currentMenuSiblings.find('ul').hide();
                                currentMenuSiblings.find('li').removeClass(tdMenu._openMenuClass);
                            }
                            //open current
                            currentMenu.addClass(tdMenu._openMenuClass);
                            currentMenu.children('ul').show();
                            //adjust menu position
                            tdMenu._getSubmenuPosition(currentMenu);
                            //add body class
                            pageBody.addClass(tdMenu._openMenuBodyClass);
                        }
                    }
                );

             /* DESKTOP */
            } else {

                var lastMenuOpen = {},
                    newMenuTimeout,
                    timeoutCleared = true;

                mainMenu.on('mouseleave', function() {
                    //close all menus
                    menuLinks.parent().removeClass(tdMenu._openMenuClass);
                    menuLinks.parent().children('ul').hide();
                    //reset last menu
                    lastMenuOpen = {};
                });

                //apply hover only to main menu (top menu uses css)
                mainMenu.find('.menu-item').hover(
                    function(){

                        //open the new menu element
                        var currentMenu = jQuery(this),
                            currentMenuSiblings = '',
                            sensitivity = 5, //measure direction after x pixels
                            pixelCount,
                            oldX,
                            oldY,
                            mouseDirection;

                        //menu has submenus
                        if (currentMenu.hasClass('menu-item-has-children') || currentMenu.hasClass('td-mega-menu')) {

                            //main menu
                            if (currentMenu.parent().hasClass('sf-menu')) {
                                //no menu is open - instantly open the current one
                                if (jQuery.isEmptyObject(lastMenuOpen)) {
                                    currentMenu.addClass(tdMenu._openMenuClass);
                                    currentMenu.children('ul').show();
                                    //set the last open menu
                                    lastMenuOpen = currentMenu;

                                //menu is open
                                } else {

                                    //execute only if it's a new menu
                                    if (currentMenu[0] !== lastMenuOpen[0]) {

                                        //initialize variables used for calculating mouse direction
                                        pixelCount = 0;
                                        oldX = 0;
                                        oldY = 0;
                                        mouseDirection = null;

                                        //add timeout - when you enter a new menu
                                        if (timeoutCleared === true) {
                                            timeoutCleared = false;
                                            newMenuTimeout = setTimeout(function() {
                                                //close previous menus
                                                menuLinks.parent().removeClass(tdMenu._openMenuClass);
                                                menuLinks.parent().children('ul').hide();
                                                //open current menu
                                                currentMenu.addClass(tdMenu._openMenuClass);
                                                currentMenu.children('ul').show();
                                                //set the last open menu
                                                lastMenuOpen = currentMenu;
                                            }, 400);
                                        }

                                        currentMenu.on('mousemove', function(e) {
                                            //reset pixeCount, calculate direction and define old x and y
                                            if (pixelCount >= sensitivity) {
                                                pixelCount = 0;
                                                mouseDirection = tdMenu._getMouseAngleDirection(oldX, oldY, e.pageX, e.pageY);
                                                oldX = e.pageX;
                                                oldY = e.pageY;
                                            } else {
                                                pixelCount++;
                                                //set the first x and y
                                                if (oldX === 0 && oldY === 0) {
                                                    oldX = e.pageX;
                                                    oldY = e.pageY;
                                                }
                                            }

                                            //debug mouse direction
                                            //console.log(mouseDirection);

                                            //current menu is different than the last one
                                            if (mouseDirection !== null && (mouseDirection > 85 || mouseDirection < -85)) {
                                                //close previous menus
                                                menuLinks.parent().removeClass(tdMenu._openMenuClass);
                                                menuLinks.parent().children('ul').hide();
                                                //open current menu
                                                currentMenu.addClass(tdMenu._openMenuClass);
                                                currentMenu.children('ul').show();

                                                //unbind mousemove event - menu is open, there's no need for it
                                                currentMenu.off('mousemove');
                                                //clear timeout - menu is open
                                                clearTimeout(newMenuTimeout);
                                                timeoutCleared = true;
                                                //set the last open menu
                                                lastMenuOpen = currentMenu;
                                            }
                                        });
                                    }
                                }

                            //submenu
                            } else {
                                //submenu - close all siblings-submenus
                                currentMenuSiblings = currentMenu.siblings();
                                currentMenuSiblings.removeClass(tdMenu._openMenuClass);
                                //close submenus
                                currentMenuSiblings.find('ul').hide();
                                currentMenuSiblings.find('li').removeClass(tdMenu._openMenuClass);
                                //open current menu
                                currentMenu.addClass(tdMenu._openMenuClass);
                                currentMenu.children('ul').show();
                                //adjust menu position
                                tdMenu._getSubmenuPosition(currentMenu);
                            }

                        //menu item doesn't have submenu
                        } else {
                            //main menu
                            if (currentMenu.parent().hasClass('sf-menu') || currentMenu.parent().hasClass('top-header-menu')) {
                                //execute only if another menu is open
                                if (!jQuery.isEmptyObject(lastMenuOpen)) {

                                    //initialize variables used for calculating mouse direction
                                    pixelCount = 0;
                                    oldX = 0;
                                    oldY = 0;
                                    mouseDirection = null;

                                    //add timeout - when you enter a new menu
                                    if (timeoutCleared === true) {
                                        timeoutCleared = false;
                                        newMenuTimeout = setTimeout(function() {
                                            //close previous menus
                                            menuLinks.parent().removeClass(tdMenu._openMenuClass);
                                            menuLinks.parent().children('ul').hide();
                                            lastMenuOpen = {};
                                        }, 400);
                                    }

                                    currentMenu.on('mousemove', function(e) {
                                        //reset pixeCount, calculate direction and define old x and y
                                        if (pixelCount >= sensitivity) {
                                            pixelCount = 0;
                                            mouseDirection = tdMenu._getMouseAngleDirection(oldX, oldY, e.pageX, e.pageY);
                                            oldX = e.pageX;
                                            oldY = e.pageY;
                                        } else {
                                            pixelCount++;
                                            //set the first x and y
                                            if (oldX === 0 && oldY === 0) {
                                                oldX = e.pageX;
                                                oldY = e.pageY;
                                            }
                                        }

                                        //current menu is different than the last one
                                        if (mouseDirection !== null && (mouseDirection > 85 || mouseDirection < -85)) {
                                            //close previous menus
                                            menuLinks.parent().removeClass(tdMenu._openMenuClass);
                                            menuLinks.parent().children('ul').hide();
                                            //unbind mousemove event - menu is open, there's no need for it
                                            currentMenu.off('mousemove');
                                            //clear timeout - menu is open
                                            clearTimeout(newMenuTimeout);
                                            timeoutCleared = true;
                                            //set the last open menu
                                            lastMenuOpen = {};
                                        }
                                    });
                                }
                            //submenu
                            } else {
                                //close all siblings-submenus
                                lastMenuOpen = currentMenu.parent();
                                currentMenuSiblings = currentMenu.siblings();
                                currentMenuSiblings.removeClass(tdMenu._openMenuClass);
                                //close siblings submenus
                                currentMenuSiblings.find('ul').hide();
                                currentMenuSiblings.find('li').removeClass(tdMenu._openMenuClass);
                            }
                        }
                    },


                    //mouseleave
                    function(){

                        var currentMenu = jQuery(this);

                        //clear menu timeout
                        if (timeoutCleared === false) {
                            clearTimeout(newMenuTimeout);
                            timeoutCleared = true;
                        }
                        //unbind mousemove event
                        currentMenu.off('mousemove');
                    }
                );
            }

        },



        /**
         * unbind menu events
         */
        unsetHover: function() {
            if (tdMenu._itemsWithSubmenu !== null) {
                tdMenu._itemsWithSubmenu.off();
            }
            //unbind outside click area events
            if (tdMenu._outsideClickArea !== null) {
                tdMenu._outsideClickArea.off();
            }
        }

    };
})();


jQuery().ready(function() {

    'use strict';

    //initialize menu
    tdMenu.init();
});
/*
 td_util.js
 v2.0
 */
/* global jQuery:false */
/* global tdDetect:false */
/* global td`ScrollingAnimation:false */
/* jshint -W020 */

var tdUtil = {};

( function() {
    "use strict";

    tdUtil = {

        //patern to check emails
        email_pattern : /^[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-zA-Z0-9]@[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/,

        /**
         * stop propagation of an event - we should check this if we can remove window.event.cancelBubble - possible
         * a windows mobile issue
         * @param event
         */
        stopBubble: function( event ) {
            if ( event && event.stopPropagation ) {
                event.stopPropagation();
            } else {
                window.event.cancelBubble=true;
            }
        },

        /**
         * checks if a form input field value is a valid email address
         * @param val
         * @returns {boolean}
         */
        isEmail: function( val ) {
            return tdUtil.email_pattern.test(val);
        },

        /**
         * utility function, used by td_post_images.js
         * @param classSelector
         */
        imageMoveClassToFigure: function ( classSelector ) {
            jQuery('figure .' + classSelector).each( function() {
                jQuery(this).parents('figure:first').addClass(classSelector);
                jQuery(this).removeClass(classSelector);
            });
        },



        /**
         * safe function to read variables passed by the theme via the js buffer. If by some kind of error the variable is missing from the global scope, this function will return false
         * @param variableName
         * @returns {*}
         */
        getBackendVar: function ( variableName ) {
            if ( typeof window[variableName] === 'undefined' ) {
                return '';
            }
            return window[variableName];
        },


        /**
         * is a given variable undefined? - this is the underscore method of checking this
         * @param obj
         * @returns {boolean}
         */
        isUndefined : function ( obj ) {
            return obj === void 0;
        },




        /**
         * scrolls to a dom element
         * @param domElement
         */
        scrollToElement: function( domElement, duration ) {
            tdIsScrollingAnimation = true;
            jQuery("html, body").stop();


            var dest;

            //calculate destination place
            if ( domElement.offset().top > jQuery(document).height() - jQuery(window).height() ) {
                dest = jQuery(document).height() - jQuery(window).height();
            } else {
                dest = domElement.offset().top;
            }
            //go to destination
            jQuery("html, body").animate(
                { scrollTop: dest },
                {
                    duration: duration,
                    easing:'easeInOutQuart',
                    complete: function(){
                        tdIsScrollingAnimation = false;
                    }
                }
            );
        },


        /**
         * scrolls to a dom element - the element will be close to the center of the screen
         * !!! compensates for long distances !!!
         */
        scrollIntoView: function ( domElement ) {
            
            tdIsScrollingAnimation = true;

            if ( tdDetect.isMobileDevice === true ) {
                return; //do not run on any mobile device
            }

            jQuery("html, body").stop();


            var destination = domElement.find( 'img' ).offset().top;
            destination = destination - 150;

            var distance = Math.abs( jQuery(window).scrollTop() - destination );
            var computed_time = distance / 5;
            //console.log(distance + ' -> ' + computed_time +  ' -> ' + (1100+computed_time));

            //go to destination
            jQuery("html, body").animate(
                { scrollTop: destination },
                {
                    duration: 1100 + computed_time,
                    easing:'easeInOutQuart',
                    complete: function(){
                        tdIsScrollingAnimation = false;
                    }
                }
            );
        },

        /**
         * scrolls to a position
         * @param pxFromTop - pixels from top
         * @param duration
         */
        scrollToPosition: function( pxFromTop, duration ) {

            // check if the current window was loaded in an iframe
            if ( window.location !== window.parent.location ) {

                // get the top position of the iframe
                var parentWin = jQuery(window.parent.document),
                    iframeTop = parentWin.find('#'+jQuery('html').attr('id')).offset().top;

                tdIsScrollingAnimation = true;
                parentWin.find("html, body").stop();

                //go to destination
                parentWin.find("html, body").animate(
                    // if are on an iframe we add the top position of the iframe
                    { scrollTop: iframeTop + pxFromTop },
                    {
                        duration: duration,
                        easing:'easeInOutQuart',
                        complete: function(){
                            tdIsScrollingAnimation = false;
                        }
                    }
                );
            } else {
                tdIsScrollingAnimation = true;
                jQuery("html, body").stop();

                //go to destination
                jQuery("html, body").animate(
                    { scrollTop: pxFromTop },
                    {
                        duration: duration,
                        easing:'easeInOutQuart',
                        complete: function(){
                            tdIsScrollingAnimation = false;
                        }
                    }
                );
            }
        },
        tdMoveY: function ( elm, value ) {
            var translate = 'translate3d(0px,' + value + 'px, 0px)';
            elm.style['-webkit-transform'] = translate;
            elm.style['-moz-transform'] = translate;
            elm.style['-ms-transform'] = translate;
            elm.style['-o-transform'] = translate;
            elm.style.transform = translate;
        },


        isValidUrl: function ( str ) {
            var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator


            if( !pattern.test(str) ) {
                return false;
            } else {
                return true;
            }
        },


        round: function ( value, precision, mode ) {
            var m, f, isHalf, sgn; // helper variables
            // making sure precision is integer
            precision |= 0;
            m = Math.pow(10, precision);
            value *= m;
            // sign of the number
            sgn = (value > 0) | -(value < 0);
            isHalf = value % 1 === 0.5 * sgn;
            f = Math.floor(value);

            if (isHalf) {
                switch (mode) {
                    case 'PHP_ROUND_HALF_DOWN':
                        // rounds .5 toward zero
                        value = f + (sgn < 0);
                        break;
                    case 'PHP_ROUND_HALF_EVEN':
                        // rouds .5 towards the next even integer
                        value = f + (f % 2 * sgn);
                        break;
                    case 'PHP_ROUND_HALF_ODD':
                        // rounds .5 towards the next odd integer
                        value = f + !(f % 2);
                        break;
                    default:
                        // rounds .5 away from zero
                        value = f + (sgn > 0);
                }
            }

            return (isHalf ? value : Math.round(value)) / m;
        }







    };
})();






/**
 * Created by ra on 6/27/14.
 * copyright tagDiv 2014
 * V 1.1 - better iOS 8 support
 */

/* global jQuery:{} */
/* global tdDetect:{} */
/* global tdUtil:{} */

var tdAffix = {};

(function(){
    'use strict';

    tdAffix = {

        // flag used to stop scrolling
        allow_scroll: true,

        //settings, obtained from ext
        menu_selector: '', //the affix menu (this element will get the td-affix)
        menu_wrap_selector: '', //the menu wrapper / placeholder
        tds_snap_menu: '', //the panel setting
        tds_snap_menu_logo: '', //the panel setting

        is_menu_affix_height_computed: false, // flag used to compute menu_affix_height only once, when the menu is affix
        is_menu_affix_height_on_mobile_computed: false, // flag used to compute menu_affix_height_on_mobile only once, when the menu is affix

        menu_affix_height: 0, // the menu affix height [the height when it's really affix]
        menu_affix_height_on_mobile: 0, // the menu affix height on mobile [the height when it's really affix]


        main_menu_height: 0, // main menu height
        top_offset: 0, //how much the menu is moved from the original position when it's affixed
        menu_offset: 0, //used to hide the menu on scroll
        is_requestAnimationFrame_running: false, //prevent multiple calls to requestAnimationFrame
        is_menu_affix: false, //the current state of the menu, true if the menu is affix
        is_top_menu: false, //true when the menu is at the top of the screen (0px topScroll)

        //menu offset boundaries - so we do not fire the animation event when the boundary is hit
        menu_offset_max_hit: false,
        menu_offset_min_hit: true,


        scroll_window_scrollTop_last: 0, //last scrollTop position, used to calculate the scroll direction

        /**
         * run the affix, we use the menu wrap selector to compute the menu position from top
         *
         {
              menu_selector: '.td-header-main-menu',
              menu_wrap_selector: '.td-header-menu-wrap',
              tds_snap_menu: tdUtil.getBackendVar('tds_snap_menu')
          }
         */
        init : function ( atts ) {

            //read the settings
            tdAffix.menu_selector = atts.menu_selector;
            tdAffix.menu_wrap_selector = atts.menu_wrap_selector;
            tdAffix.tds_snap_menu = atts.tds_snap_menu;
            tdAffix.tds_snap_menu_logo = atts.tds_snap_menu_logo;
            tdAffix.menu_affix_height = atts.menu_affix_height;
            tdAffix.menu_affix_height_on_mobile = atts.menu_affix_height_on_mobile;

            // Do nothing if page does not have menu
            if ( ! jQuery( tdAffix.menu_selector).length || ! jQuery( tdAffix.menu_wrap_selector).length )  {
                return;
            }

            //the snap menu is disabled from the panel
            if ( ! tdAffix.tds_snap_menu ) {
                return;
            }


            // a computation before jquery.ready is necessary for firefox, where tdEvents.scroll comes before
            if ( tdDetect.isFirefox ) {
                tdAffix.compute_wrapper();
                tdAffix.compute_top();

            }

            jQuery().ready(function() {
                //compute on semi dom ready
                tdAffix.compute_wrapper();
                tdAffix.compute_top();

            });

            //recompute when all the page + logos are loaded
            jQuery( window ).load(function() {
                tdAffix.compute_wrapper();
                tdAffix.compute_top();

                //recompute after 1 sec for retarded phones
                setTimeout(function(){
                    tdAffix.compute_top();
                }, 1000 );
            });
        },


        /**
         * - get the real affix height.
         * The real affix height is computed only once, when the menu is affix. Till then, the function
         * return the values set at init.
         *
         * These values are important because they are used in the tdSmartSidebar.js for the
         * td_affix_menu_computed_height variable, which then is used to determine the sidebar position.
         *
         * For 'Newspaper', the sidebar needs a custom padding top (see @tdSmartSidebar.js), otherwise
         * the sidebar is sticked to the affix menu.
         *
         *
         * @returns {number} affix height
         * @private
         */
        _get_menu_affix_height: function() {

            //if (tdDetect.isPhoneScreen === true) {
            //    return tdAffix.menu_affix_height_on_mobile;
            //}
            //return tdAffix.menu_affix_height;

            if ( true === tdDetect.isPhoneScreen ) {
                if ( ! tdAffix.is_menu_affix_height_on_mobile_computed && tdAffix.is_menu_affix ) {

                    tdAffix.is_menu_affix_height_on_mobile_computed = true;

                    // overwrite the tdAffix.menu_affix_height_on_mobile variable with the real affix height
                    tdAffix.menu_affix_height_on_mobile = jQuery(tdAffix.menu_selector).height();
                }
                return tdAffix.menu_affix_height_on_mobile;
            }

            if ( ! tdAffix.is_menu_affix_height_computed && tdAffix.is_menu_affix ) {

                tdAffix.is_menu_affix_height_computed = true;

                // overwrite the tdAffix.menu_affix_height variable with the real affix height
                tdAffix.menu_affix_height = jQuery(tdAffix.menu_selector).height();

                // Fix used to solve 'Sticky Menu - Smart snap'
                if ( 'smart_snap_always' === tdAffix.tds_snap_menu ) {
                    tdAffix.top_offset = tdAffix.menu_affix_height;
                }
            }
            return tdAffix.menu_affix_height;
        },



        /**
         * called by tdEvents.js on scroll
         */
        td_events_scroll: function( scrollTop ) {

            if ( ! tdAffix.allow_scroll ) {
                return;
            }

            //do not run if we don't have a snap menu
            if ( ! tdAffix.tds_snap_menu ) {
                return;
            }


            /*  ----------------------------------------------------------------------------
             scroll direction + delta (used by affix for now)
             to run thios code:
             - tdAffix.tds_snap_menu != '' (from above)
             - tdAffix.tds_snap_menu != 'snap'
             */
            var scroll_direction = '';

            if ( 'snap' !== tdAffix.tds_snap_menu ) { //do not run on snap
                if ( ( 'smart_snap_mobile' !== tdAffix.tds_snap_menu || true === tdDetect.isPhoneScreen ) ) {  // different from smart_snap_mobile or tdDetect.isPhoneScreen === true

                    var scrollDelta = 0;

                    //check the direction
                    if ( scrollTop !== tdAffix.scroll_window_scrollTop_last ) { //compute direction only if we have different last scroll top
                        // compute the direction of the scroll
                        if ( scrollTop > tdAffix.scroll_window_scrollTop_last ) {
                            scroll_direction = 'down';
                        } else {
                            scroll_direction = 'up';
                        }
                        //calculate the scroll delta
                        scrollDelta = Math.abs( scrollTop - tdAffix.scroll_window_scrollTop_last );
                    }

                    tdAffix.scroll_window_scrollTop_last = scrollTop;
                }
            }

            /*  ---------------------------------------------------------------------------- */

            // show the logo on sticky menu
            if ( '' !== tdAffix.tds_snap_menu && '' !== tdAffix.tds_snap_menu_logo ) {
                jQuery( '.td-main-menu-logo' ).addClass( 'td-logo-sticky' );
            }




            //if the menu is in the affix state

            // the next check is to keep the text from the menu at the same position, when the menu comes from affix off to affix off
            if ( ( scrollTop > tdAffix.top_offset + ( tdAffix.main_menu_height / 2 - tdAffix._get_menu_affix_height() / 2 ) ) ||

                    // - the affix is OFF when the next condition is not accomplished, which means that the affix is ON
                    // and the scroll to the top is LOWER than the initial tdAffix.top_offset reduced by the affix real height
                    // - this condition makes the transition from the small affix menu to the larger menu of the page
                ( ( tdAffix.is_menu_affix === true ) && ( 'smart_snap_always' === tdAffix.tds_snap_menu) && scrollTop > ( tdAffix.top_offset - tdAffix._get_menu_affix_height() ) ) ||

                tdAffix.is_top_menu === true ) {

                //get the menu element
                var td_affix_menu_element = jQuery( tdAffix.menu_selector );

                if ( td_affix_menu_element.length ) {
                    //turn affix on for it
                    tdAffix._affix_on( td_affix_menu_element );
                }

                //if the menu is only with snap or we are on smart_snap_mobile + mobile, our job here in this function is done, return
                if ( 'snap' === tdAffix.tds_snap_menu || ( 'smart_snap_mobile' === tdAffix.tds_snap_menu && false === tdDetect.isPhoneScreen ) ) {
                    return;
                }

                /*    ---  end simple snap  ---  */


                /*  ----------------------------------------------------------------------------
                 check scroll directions (we may also have scroll_direction = '', that's why we have to check for the specific state (up or down))
                 */


                // boundary check - to not run the position on each scroll event
                if ( td_affix_menu_element.length && ( ( false === tdAffix.menu_offset_max_hit && 'down' === scroll_direction ) || ( false === tdAffix.menu_offset_min_hit && 'up' === scroll_direction ) ) ) {
                    //request animation frame
                    //if (tdAffix.is_requestAnimationFrame_running === false) {

                        window.requestAnimationFrame(function () {

                            //console.log(tdAffix.menu_offset);
                            //console.log(scrollDelta);
                            var offset = 0;


                            if (scrollTop > 0) { // ios returns negative scrollTop values
                                if ('down' === scroll_direction) {

                                    //compute the offset
                                    offset = tdAffix.menu_offset - scrollDelta;

                                    // the offset is a value in the [-tdAffix.menu_affix_height, 0] and
                                    // not into the interval [-tdAffix.main_menu_height, 0]
                                    if (offset < -tdAffix._get_menu_affix_height()) {
                                        offset = -tdAffix._get_menu_affix_height();
                                    }

                                } else if ('up' === scroll_direction) {
                                    //compute the offset
                                    offset = tdAffix.menu_offset + scrollDelta;
                                    if (offset > 0) {
                                        offset = 0;
                                    }
                                }
                            }

                            //td_debug.log_live(scroll_direction + ' | scrollTop: ' + scrollTop + '  | offset: ' + offset);

                            //tdAffix.is_requestAnimationFrame_running = true;

                            //console.log(offset);

                            //move the menu
                            tdUtil.tdMoveY(td_affix_menu_element[0], offset);

                            //td_affix_menu_element.css({top: (offset) + 'px'});  //legacy menu move code

                            //check boundaries
                            if (0 === offset) {
                                tdAffix.menu_offset_min_hit = true;
                            } else {
                                tdAffix.menu_offset_min_hit = false;
                            }


                            if (offset === -tdAffix._get_menu_affix_height()) {
                                tdAffix.menu_offset_max_hit = true;
                                //also hide the menu when it's 100% out of view on ios - the safari header is transparent and we can see the menu
                                if ((true === tdDetect.isIos) || tdDetect.isSafari) { // safari also
                                    td_affix_menu_element.hide();
                                }

                                //show the logo on smart sticky menu
                                if ('' !== tdAffix.tds_snap_menu_logo) {
                                    jQuery('.td-main-menu-logo').addClass('td-logo-sticky');
                                }
                            } else {
                                tdAffix.menu_offset_max_hit = false;

                                if ((true === tdDetect.isIos) || tdDetect.isSafari) { //ios safari fix
                                    td_affix_menu_element.show();
                                }
                            }

                            //tdAffix.is_requestAnimationFrame_running = false;

                            tdAffix.menu_offset = offset; //update the current offset of the menu

                        }, td_affix_menu_element[0]);

                    //}
                    //console.log(offset + ' ' + scroll_direction);

                } //end boundary check

            } else {
                var $menu = jQuery( tdAffix.menu_selector );
                if ( $menu.length ) {
                    tdAffix._affix_off( $menu );
                }
            }
        },


        /**
         * calculates the affix point (the distance from the top when affix should be enabled)
         * @see tdAffix.init()
         * @see tdEvents
         */
        compute_top: function() {

            //compute top is called in tdEvents.js, avoid error when menu is not present on page
            if (!jQuery( tdAffix.menu_wrap_selector ).length) {
                return;
            }

            // to compute from the bottom of the menu, the top offset is incremented by the menu wrap height
            tdAffix.top_offset = jQuery( tdAffix.menu_wrap_selector ).offset().top;// + jQuery(tdAffix.menu_wrap_selector).height();


            // The top_offset is incremented with the menu_affix_height only on 'smart_snap_always', because of the sidebar
            // which use the menu_offset (and menu_offset depends on this top_offset)
            //
            // Consider that the smart sidebar, increment the td_affix_menu_computed_height with the menu_offset value
            // when the menu is on 'smart_snap_always'
            if ( 'smart_snap_always' === tdAffix.tds_snap_menu ) {
                tdAffix.top_offset += tdAffix.menu_affix_height;
            }


            //check to see if the menu is at the top of the screen
            if ( 1 === tdAffix.top_offset ) {
                //switch to affix - because the menu is at the top of the page
                //tdAffix._affix_on(jQuery(tdAffix.menu_selector));
                tdAffix.is_top_menu = true;
            } else {
                //check to see the current top offset
                tdAffix.is_top_menu = false;
            }
            tdAffix.td_events_scroll( jQuery(window).scrollTop() );

            //alert(tdAffix.top_offset);
            //console.log('computed: ' + tdAffix.top_offset);
        },


        /**
         * recalculate the wrapper height. To support different menu heights
         */
        compute_wrapper: function() {

            // td-affix class is removed to compute a real height when the compute_wrapper is done on a scrolled page
            if ( jQuery( tdAffix.menu_selector ).hasClass( 'td-affix' ) ) {
                jQuery( tdAffix.menu_selector ).removeClass( 'td-affix' );

                //read the height of the menu
                tdAffix.main_menu_height = jQuery( tdAffix.menu_selector ).height();

                jQuery( tdAffix.menu_selector ).addClass( 'td-affix' );

            } else {
                //read the height of the menu
                tdAffix.main_menu_height = jQuery( tdAffix.menu_selector ).height();
            }

            // put the menu height to the wrapper. The wrapper remains in the place when the menu is affixed
            jQuery( tdAffix.menu_wrap_selector ).css( 'height', tdAffix.main_menu_height );
        },

        /**
         * turns affix on for the menu element
         * @param td_affix_menu_element
         * @private
         */
        _affix_on: function( td_affix_menu_element ) {
            if ( false === tdAffix.is_menu_affix ) {


                // Bug.Fix - affix menu flickering
                // - the td_affix_menu_element is hidden because he is outside of the viewport
                // - without it, there's a flicker effect of applying css style (classes) over it

                if ( ( 'smart_snap_always' === tdAffix.tds_snap_menu ) && ( tdDetect.isPhoneScreen !== true ) ) {
                    td_affix_menu_element.css( 'visibility', 'hidden' );
                }

                tdAffix.menu_offset = -tdAffix.top_offset;

                //make the menu fixed
                td_affix_menu_element.addClass( 'td-affix' );

                //add body-td-affix class on body for header style 8 -> when scrolling down the window jumps 76px up when the menu is changing from header style 8 default to header style 8 affix
                jQuery( 'body' ).addClass( 'body-td-affix' );

                tdAffix.is_menu_affix = true;
            } else {

                // the td_affix_menu element is kept visible
                if ( true  !== tdDetect.isPhoneScreen ) {
                    td_affix_menu_element.css( 'visibility', '' );
                }
            }
        },



        /**
         * Turns affix off for the menu element
         * @param td_affix_menu_element
         * @private
         */
        _affix_off: function( td_affix_menu_element ) {
            if ( true === tdAffix.is_menu_affix ) {
                //make the menu normal
                jQuery( tdAffix.menu_selector ).removeClass( 'td-affix' );

                //console.log(tdAffix.tds_snap_menu_logo);
                //hide the logo from sticky menu when the menu is not affix
                if( '' === tdAffix.tds_snap_menu_logo ) {
                    jQuery( '.td-main-menu-logo' ).removeClass( 'td-logo-sticky' );
                }

                //remove body-td-affix class on body for header style 8 -> when scrolling down the window jumps 76px up when the menu is changing from header style 8 default to header style 8 affix
                jQuery( 'body' ).removeClass( 'body-td-affix' );

                tdAffix.is_menu_affix = false;

                //move the menu to 0 (ios seems to skip animation frames)
                tdUtil.tdMoveY( td_affix_menu_element[0], 0 );

                if ( ( true === tdDetect.isIos ) || tdDetect.isSafari ) {
                    td_affix_menu_element.show();
                }
            }
        }
    };
})();







/*
    tagDiv - 2014
    Our portfolio:  http://themeforest.net/user/tagDiv/portfolio
    Thanks for using our theme! :)
*/


/* global jQuery:false */
/* global tdUtil:false */
/* global tdModalImageLastEl:{} */
/* global tdEvents:{} */


"use strict";





/*  ----------------------------------------------------------------------------
    On load
 */
jQuery().ready(function jQuery_ready() {

    //retina images
    td_retina();



    // the mobile pull left menu (off canvas)
    //td_mobile_menu();

    //handles the toogle efect on mobile menu
    td_mobile_menu_toogle();


    //resize all the videos if we have them
    td_resize_videos();

    //fake placeholder for ie
    jQuery('input, textarea').placeholder();

    //more stories box
    td_more_articles_box.init();

    //used for smart lists
    td_smart_lists_magnific_popup();

    //smart list drop down pagination
    td_smart_list_dropdown();

    // the top menu date
    if (typeof tdsDateFormat !== 'undefined') {

        // php time() equivalent - js deals in milliseconds and php in seconds
        var tdsDateTimestamp = Math.floor(new Date().getTime() / 1000);

        // replace the date
        var tdNewDateI18n = td_date_i18n(tdsDateFormat, tdsDateTimestamp);
        jQuery('.td_data_time').text(tdNewDateI18n);
    }

    setMenuMinHeight();

    // prevents comments form submission without filing the required form fields
    td_comments_form_validation();

    // bind events to scroll to css class elements
    td_scroll_to_class();

}); //end on load


/**
 * smart lists drop down pagination redirect
 */
function td_smart_list_dropdown() {
    jQuery('.td-smart-list-dropdown').on('change', function() {
        window.location = this.value;
    });
}


/**
 * More stories box
 */
var td_more_articles_box = {
    is_box_visible:false,
    cookie:'',
    distance_from_top:400,

    init: function init() {


        //read the cookie
        td_more_articles_box.cookie = td_read_site_cookie('td-cookie-more-articles');


        //setting distance from the top
        if(!isNaN(parseInt(tds_more_articles_on_post_pages_distance_from_top)) && isFinite(tds_more_articles_on_post_pages_distance_from_top) && parseInt(tds_more_articles_on_post_pages_distance_from_top) > 0){
            td_more_articles_box.distance_from_top = parseInt(tds_more_articles_on_post_pages_distance_from_top);
        } else {
            td_more_articles_box.distance_from_top = 400;
        }

        //adding event to hide the box
        jQuery('.td-close-more-articles-box').click(function(){

            //hiding the box
            jQuery('.td-more-articles-box').removeClass('td-front-end-display-block');
            jQuery('.td-more-articles-box').hide();

            //cookie life
            if(!isNaN(parseInt(tds_more_articles_on_post_time_to_wait)) && isFinite(tds_more_articles_on_post_time_to_wait)){
                //setting cookie
                td_set_cookies_life(['td-cookie-more-articles', 'hide-more-articles-box', parseInt(tds_more_articles_on_post_time_to_wait)*86400000]);//86400000 is the number of milliseconds in a day
            }
        });
    },

    /**
     * called by tdEvents.js on scroll
     */
    td_events_scroll: function td_events_scroll(scrollTop) {

        if(tdIsScrollingAnimation) { //do not fire the event on animations
            return;
        }

        //check to see if it's enable form panel and also from cookie
        if(tdUtil.getBackendVar('tds_more_articles_on_post_enable') == "show" && td_more_articles_box.cookie != 'hide-more-articles-box') {

            if (scrollTop > td_more_articles_box.distance_from_top ) {
                if (td_more_articles_box.is_box_visible === false) {
                    jQuery('.td-more-articles-box').addClass('td-front-end-display-block');
                    td_more_articles_box.is_box_visible = true;
                }
            } else {
                if (td_more_articles_box.is_box_visible === true) {
                    jQuery('.td-more-articles-box').removeClass('td-front-end-display-block');
                    td_more_articles_box.is_box_visible = false;
                }
            }
        }
    }
};






var td_resize_timer_id;
jQuery(window).resize(function() {
    clearTimeout(td_resize_timer_id);
    td_resize_timer_id = setTimeout(function() {
        td_done_resizing();
    }, 200);

});

function td_done_resizing(){
    td_resize_videos();
}

/*  ----------------------------------------------------------------------------
    Resize the videos
 */
function td_resize_videos() {
    //youtube in content
    jQuery(document).find('iframe[src*="youtube.com"]').each(function() {
        var videoMainContainer = jQuery(this).parent().parent().parent(),
            videoInPlaylist = jQuery(this).parent().hasClass("td_wrapper_playlist_player_vimeo"),
            video43AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-43"), //the video is set to 4:3 aspect ratio
            video235AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-235"); //the video is set to 2.35:1 aspect ratio
        if(videoInPlaylist || video43AspectRatio || video235AspectRatio) {
            //do nothing for playlist player youtube or aspect ratios 4:3 and 2.35:1
            //the video aspect ratio can be set on Visual Composer - Video Player widget settings
        } else {
            var td_video = jQuery(this);
            td_video.attr('width', '100%');
            var td_video_width = td_video.width();
            td_video.css('height', td_video_width * 0.5625, 'important');
        }
    });


    //vimeo in content
    jQuery(document).find('iframe[src*="vimeo.com"]').each(function() {
        var videoMainContainer = jQuery(this).parent().parent().parent(),
            videoInPlaylist = jQuery(this).parent().hasClass("td_wrapper_playlist_player_vimeo"),
            video43AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-43"), //the video is set to 4:3 aspect ratio
            video235AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-235"); //the video is set to 2.35:1 aspect ratio
        if(videoInPlaylist || video43AspectRatio || video235AspectRatio) {
            //do nothing for playlist player vimeo or aspect ratios 4:3 and 2.35:1
            //the video aspect ratio can be set on Visual Composer - Video Player widget settings
        } else {
            var td_video = jQuery(this);
            td_video.attr('width', '100%');
            var td_video_width = td_video.width();
            td_video.css('height', td_video_width * 0.5625, 'important');
        }
    });


    //daily motion in content
    jQuery(document).find('iframe[src*="dailymotion.com"]').each(function() {
        var videoMainContainer = jQuery(this).parent().parent().parent(),
            video43AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-43"), //video aspect ratio 4:3
            video235AspectRatio = videoMainContainer.hasClass("vc_video-aspect-ratio-235"); //video aspect ratio 2.35:1
        if (video43AspectRatio || video235AspectRatio) {
            //do nothing for video aspect ratios 4:3 and 2.35:1
            //the video aspect ratio can be set on Visual Composer - Video Player widget settings
        } else {
            var td_video = jQuery(this);
            td_video.attr('width', '100%');
            var td_video_width = td_video.width();
            td_video.css('height', td_video_width * 0.6, 'important');
        }

    });


    //facebook in content
    //jQuery(document).find('iframe[src*="facebook.com/plugins/video.php"]').each(function() {
    //    var td_video = jQuery(this);
    //    if ( td_video.parent().hasClass('wpb_video_wrapper') ) {
    //        td_video.attr('width', '100%');
    //        var td_video_width = td_video.width();
    //        td_video.css('height', td_video_width * 0.5625, 'important');
    //    } else {
    //        td_video.css('max-width', '100%');
    //    }
    //});


    //wordpress embedded
    //jQuery(document).find(".wp-video-shortcode").each(function() {
    //    var td_video = jQuery(this);
    //
    //    var td_video_width = td_video.width() + 3;
    //    jQuery(this).parent().css('height', td_video_width * 0.56, 'important');
    //    //td_video.css('height', td_video_width * 0.6, 'important')
    //    td_video.css('width', '100%', 'important');
    //    td_video.css('height', '100%', 'important');
    //})
}

//handles mobile menu
function td_mobile_menu() {
    //jQuery('#td-top-mobile-toggle a, .td-mobile-close a').click(function(){
    //    if(jQuery('body').hasClass('td-menu-mob-open-menu')) {
    //        jQuery('body').removeClass('td-menu-mob-open-menu');
    //    } else {
    //        if (tdDetect.isMobileDevice) {
    //            // on mobile devices scroll to top instantly and wait a bit and open the menu
    //            window.scrollTo(0, 0);
    //            setTimeout(function(){
    //                jQuery('body').addClass('td-menu-mob-open-menu');
    //            }, 100);
    //        } else {
    //            // on desktop open it with full animations
    //            jQuery('body').addClass('td-menu-mob-open-menu');
    //            setTimeout(function(){
    //                tdUtil.scrollToPosition(0, 1200);
    //            }, 200);
    //        }
    //    }
    //});
}


//handles open/close mobile menu
function td_mobile_menu_toogle() {

    //jQuery('#td-mobile-nav .menu-item-has-children ul').hide();
    //
    ////move thru all the menu and find the item with sub-menues to atach a custom class to them
    //jQuery(document).find('#td-mobile-nav .menu-item-has-children').each(function(i) {
    //
    //    var class_name = 'td_mobile_elem_with_submenu_' + i;
    //    jQuery(this).addClass(class_name);
    //
    //    //add an element to click on
    //    //jQuery(this).children("a").append('<div class="td-element-after" data-parent-class="' + class_name + '"></div>');
    //    jQuery(this).children("a").append('<i class="td-icon-menu-down td-element-after" data-parent-class="' + class_name + '"></i>');
    //
    //
    //    //click on link elements with #
    //    jQuery(this).children("a").addClass("td-link-element-after").attr("data-parent-class", class_name);
    //});
    //
    //jQuery(".td-element-after, .td-link-element-after").click(function(event) {
    //
    //    if(jQuery(this).hasClass("td-element-after") || jQuery(this).attr("href") == "#" ){
    //        event.preventDefault();
    //        event.stopPropagation();
    //    }
    //
    //
    //    //take the li parent class
    //    var parent_class = jQuery(this).data('parent-class');
    //
    //    //target the sub-menu to open
    //    var target_to_open = '#td-mobile-nav .' + parent_class + ' > a + ul';
    //    if(jQuery(target_to_open).css('display') == 'none') {
    //        jQuery(target_to_open).show();
    //    } else {
    //        jQuery(target_to_open).hide();
    //    }
    //
    //
    //});

    jQuery( '#td-top-mobile-toggle a, .td-mobile-close a, #tdb-mobile-menu-button' ).click(function(){
        if ( jQuery( 'body' ).hasClass( 'td-menu-mob-open-menu' ) ) {
            jQuery( 'body' ).removeClass( 'td-menu-mob-open-menu' );
        } else {
            jQuery( 'body' ).addClass( 'td-menu-mob-open-menu' );
        }
    });


    //handles open/close mobile menu

    //move thru all the menu and find the item with sub-menues to atach a custom class to them
    jQuery( document ).find( '#td-mobile-nav .menu-item-has-children' ).each(function( i ) {

        var class_name = 'td_mobile_elem_with_submenu_' + i;
        jQuery(this).addClass( class_name );

        //click on link elements with #
        jQuery(this).children('a').addClass( 'td-link-element-after' );

        jQuery(this).click(function( event ) {

            /**
             * currentTarget - the li element
             * target - the element clicked inside of the currentTarget
             */

            var jQueryTarget = jQuery( event.target );

            // html i element
            if ( jQueryTarget.length &&
                ( ( jQueryTarget.hasClass( 'td-element-after') || jQueryTarget.hasClass( 'td-link-element-after') ) &&
                ( '#' === jQueryTarget.attr( 'href' ) || undefined === jQueryTarget.attr( 'href' ) ) ) ) {

                event.preventDefault();
                event.stopPropagation();

                jQuery( this ).toggleClass( 'td-sub-menu-open' );
            }
        });
    });
}

/*  ----------------------------------------------------------------------------
    Add retina support
 */

function td_retina() {
    if (window.devicePixelRatio > 1) {
        jQuery('.td-retina').each(function(i) {
            var lowres = jQuery(this).attr('src');
            var highres = lowres.replace(".png", "@2x.png");
            highres = highres.replace(".jpg", "@2x.jpg");
            jQuery(this).attr('src', highres);

        });


        //custom logo support
        jQuery('.td-retina-data').each(function(i) {
            jQuery(this).attr('src', jQuery(this).data('retina'));
            //fix logo aligment on retina devices
            jQuery(this).addClass('td-retina-version');
        });

    }
}

/*
jQuery('body').click(function(e){
    if(! jQuery(e.target).hasClass('custom-background')){
        alert('clicked on something that has not the class theDIV');
    }

});*/

//click only on BACKGROUND, for devices that don't have touch (ex: phone, tablets)
if(!tdDetect.isTouchDevice && tdUtil.getBackendVar('td_ad_background_click_link') != '') {

    //var ev = ev || window.event;
    //var target = ev.target || ev.srcElement;
    jQuery('body').click(function(event) {

        //getting the target element that the user clicks - for W3C and MSIE
        var target = (event.target) ? event.target : event.srcElement;

        //only click on background

        var target_jquery_obj = jQuery(target);

        // td-outer-container for NEWSMAG and td-boxex-layout for NEWSPAPER
        if (target_jquery_obj.hasClass('td-outer-container') || target_jquery_obj.hasClass('td-theme-wrap') || target_jquery_obj.hasClass('td-header-wrap')) {

            //open the link ad page
            if(td_ad_background_click_target == '_blank') {
                //open in a new window
                window.open(td_ad_background_click_link);
            } else {
                //open in the same window
                location.href = td_ad_background_click_link;
            }
        }

        //e.stopPropagation();
        //stopBubble(event);
    });
}


/**
 * reading cookies
 * @param name
 * @returns {*}
 */
function td_read_site_cookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return null;
}


/**
 *
 * @param td_time_cookie_array
 *
 * @param[0]: name of the cookie
 * @param[1]: value of the cookie
 * @param[2]: expiration time
 */
function td_set_cookies_life(td_time_cookie_array) {
    var expiry = new Date();
    expiry.setTime(expiry.getTime() + td_time_cookie_array[2]);

    // Date()'s toGMTSting() method will format the date correctly for a cookie
    document.cookie = td_time_cookie_array[0] + "=" + td_time_cookie_array[1] + "; expires=" + expiry.toGMTString() + "; path=/";
}




















/*  ----------------------------------------------------------------------------
    Scroll to top + animation stop
 */

var tdIsScrollingAnimation = false;
var td_mouse_wheel_or_touch_moved = false; //we want to know if the user stopped the animation via touch or mouse move

//stop the animation on mouse wheel
jQuery(document).bind('mousewheel DOMMouseScroll MozMousePixelScroll', function(e){


    if (tdIsScrollingAnimation === false) {
        return;
    } else {

        tdIsScrollingAnimation = false;
        td_mouse_wheel_or_touch_moved = true;

        jQuery("html, body").stop();
    }
});

//stop the animation on touch
if (document.addEventListener){
    document.addEventListener('touchmove', function(e) {
        if (tdIsScrollingAnimation === false) {
            return;
        } else {
            tdIsScrollingAnimation = false;
            td_mouse_wheel_or_touch_moved = true;
            jQuery("html, body").stop();
        }
    }, false);
}

/**
 * called by tdEvents.js on scroll - back to top
 */
var td_scroll_to_top_is_visible = false;
function td_events_scroll_scroll_to_top(scrollTop) {
    if(tdIsScrollingAnimation) {  //do not fire the event on animations
        return;
    }
    if (scrollTop > 400) {
        if (td_scroll_to_top_is_visible === false) { //only add class if needed
            td_scroll_to_top_is_visible = true;
            jQuery('.td-scroll-up').addClass('td-scroll-up-visible');
        }
    } else {
        if (td_scroll_to_top_is_visible === true) { //only add class if needed
            td_scroll_to_top_is_visible = false;
            jQuery('.td-scroll-up').removeClass('td-scroll-up-visible');
        }
    }
}


jQuery('.td-scroll-up').click(function(){
    if(tdIsScrollingAnimation) { //double check - because when we remove the class, the button is still visible for a while
        return;
    }

    //hide the button
    td_scroll_to_top_is_visible = false;
    jQuery('.td-scroll-up').removeClass('td-scroll-up-visible');

    //hide more articles box
    td_more_articles_box.is_box_visible = false;
    jQuery('.td-more-articles-box').removeClass('td-front-end-display-block');

    //scroll to top
    tdUtil.scrollToPosition(0, 1200);

    return false;
});









// small down arrow on template 6 and full width index
jQuery('.td-read-down a').click(function(event){
    event.preventDefault();
    tdUtil.scrollToPosition(jQuery('.td-full-screen-header-image-wrap').height(), 1200);
    //tdUtil.scrollToPosition(jQuery('.td-full-screen-header-image-wrap').height() + jQuery('.td-full-screen-header-image-wrap').offset().top, 1200);
});

/**
 * make td-post-template-6 title move down and blurry
 * called from single_tempalte_6.php via the js buffer
 */
function td_post_template_6_title() {



    //define all the variables - for better performance ?
    var td_parallax_el = document.getElementById('td_parallax_header_6');

    var td_parallax_bg_el = document.getElementById('td-full-screen-header-image');

    var scroll_from_top = '';
    var distance_from_bottom;

    //attach the animation tick on scroll
    jQuery(window).scroll(function(){
        // with each scroll event request an animation frame (we have a polyfill for animation frame)
        // the requestAnimationFrame is called only once and after that we wait
        td_request_tick();
    });


    var td_animation_running = false; //if the tick is running, we set this to true

    function td_request_tick() {
        if (td_animation_running === false) {
            window.requestAnimationFrame(td_do_animation);
        }
        td_animation_running = true;
    }

    /**
     * the animation loop
     */
    function td_do_animation() {
        scroll_from_top = jQuery(document).scrollTop();
        if (scroll_from_top <= 950) { //stop the animation after scroll from top

            var blur_value = 1 - (scroll_from_top / 800); // !!!! trebuie verificata formula??
            if (tdDetect.isIe8 === true) {
                blur_value = 1;
            }


            blur_value = Math.round(blur_value * 100) / 100;

            //opacity
            td_parallax_el.style.opacity = blur_value;

            //move the bg
            var parallax_move = -Math.round(scroll_from_top / 4);
            tdUtil.tdMoveY(td_parallax_bg_el,-parallax_move);


            //move the title + cat
            distance_from_bottom = -Math.round(scroll_from_top / 8);
            tdUtil.tdMoveY(td_parallax_el,-distance_from_bottom);
            //td_parallax_el.style.bottom = distance_from_bottom + "px";  //un accelerated version


        }

        td_animation_running = false;
    }



}

//used for smart lists
function td_smart_lists_magnific_popup() {
    //magnific popup
    jQuery(".td-lightbox-enabled").magnificPopup({
        delegate: "a",
        type: "image",
        tLoading: "Loading image #%curr%...",
        mainClass: "mfp-img-mobile",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1],
            tCounter: tdUtil.getBackendVar('td_magnific_popup_translation_tCounter') // Markup for "1 of 7" counter
        },
        image: {
            tError: "<a href=\'%url%\'>The image #%curr%</a> could not be loaded.",
            titleSrc: function(item) {//console.log(item.el);
                //alert(jQuery(item.el).data("caption"));
                return item.el.attr("data-caption");
            }
        },
        zoom: {
            enabled: true,
            duration: 300,
            opener: function(element) {
                return element.find("img");
            }
        },
        callbacks: {
            change: function(item) {
                /**
                 * if we have pictures only on 3 from 4 slides then remove, from magnific popup, the one with no image
                 */
                //console.log(item);
                //console.log(item.el[0].id);
                //console.log(parseInt(nr_slide[1]) + 1);
                if(item.el[0].id != '') {
                    var nr_slide = item.el[0].id.split("_");

                    // Will fire when popup is closed
                    //jQuery(".td-iosSlider").iosSlider("goToSlide", this.currItem.index + 1 );
                    jQuery(".td-iosSlider").iosSlider("goToSlide", parseInt(nr_slide[1]) + 1);
                } else {
                    tdModalImageLastEl = item.el;
                    setTimeout(function(){
                        tdUtil.scrollIntoView(item.el);
                    }, 100);
                }
            },
            beforeClose: function() {
                if (tdModalImageLastEl != '') {

                    tdUtil.scrollIntoView(tdModalImageLastEl);
                }
            }
        }
    });


    // Add video magnific popup to 'data-mpf-src' elements
    jQuery('[data-mfp-src]').on('click', function(event) {
        event.preventDefault();

        // Do nothing in TagDiv Composer
        if ( 'undefined' !== typeof window.parent.tdcAdminSettings ) {
            return;
        }

        var $this = jQuery( this );

        if ( ! $this.hasClass( 'td-mfp-loaded' ) ) {

            $this.addClass( 'td-mfp-loaded' );

            $this.magnificPopup({

                preloader: true,
                tLoading: "Loading url #%curr%...",
                type: "iframe",
                markup: '<div class="mfp-iframe-scaler">'+
                        '<div class="mfp-close"></div>'+
                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                    '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                iframe: {
                    patterns: {
                        youtube: {

                            index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

                            //id: 'v=', // String that splits URL in a two parts, second part should be %id%
                            // Or null - full URL will be returned
                            // Or a function that should return %id%, for example:
                            // id: function(url) { return 'parsed id'; }

                            id: function( url ) {
                                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]{11,11}).*/;
                                var match = url.match(regExp);
                                if (match && match.length >= 2 ) {
                                    return match[2];
                                }
                                return null;
                            },

                            src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
                        },
                        vimeo: {

                            index: 'vimeo.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

                            id: '/',

                            src: '//player.vimeo.com/video/%id%?autoplay=1' // URL that will be set as a source for iframe.
                        }
                    },
                    srcAction: 'iframe_src' // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                }
            });
        }

        $this.magnificPopup( 'open' );
    });
}





function td_get_document_width() {
    var x = 0;
    if (self.innerHeight)
    {
        x = self.innerWidth;
    }
    else if (document.documentElement && document.documentElement.clientHeight)
    {
        x = document.documentElement.clientWidth;
    }
    else if (document.body)
    {
        x = document.body.clientWidth;
    }
    return x;
}

function td_get_document_height() {
    var y = 0;
    if (self.innerHeight)
    {
        y = self.innerHeight;
    }
    else if (document.documentElement && document.documentElement.clientHeight)
    {
        y = document.documentElement.clientHeight;
    }
    else if (document.body)
    {
        y = document.body.clientHeight;
    }
    return y;
}


/*  ----------------------------------------------------------------------------
 Set the mobile menu min-height property

 This is usually used to force vertical scroll bar appearance from the beginning.
 Without it, on some mobile devices (ex Android), at scroll bar appearance there are some
 visual issues.

 */
function setMenuMinHeight() {

    if ( 'undefined' === typeof tdEvents.previousWindowInnerWidth ) {

        // Save the previous width
        tdEvents.previousWindowInnerWidth = tdEvents.window_innerWidth;

    } else if ( tdEvents.previousWindowInnerWidth === tdEvents.window_innerWidth ) {

        // Stop if the width has not been modified
        return;
    }

    tdEvents.previousWindowInnerWidth = tdEvents.window_innerWidth;

    var $tdMobileMenu = jQuery( '#td-mobile-nav' ),
        cssHeight = tdEvents.window_innerHeight + 1;

    if ( $tdMobileMenu.length ) {
        $tdMobileMenu.css( 'min-height' , cssHeight + 'px' );
    }

    // Stop if we are not on mobile
    if ( ! tdDetect.isMobileDevice ) {
        return;
    }

    var $tdMobileBg = jQuery( '.td-menu-background' ),
        $tdMobileBgSearch = jQuery( '.td-search-background' );

    if ( $tdMobileBg.length ) {
        $tdMobileBg.css( 'height' , ( cssHeight + 70 ) + 'px' );
    }

    if ( $tdMobileBgSearch.length ) {
        $tdMobileBgSearch.css( 'height' , ( cssHeight + 70 ) + 'px' );
    }


}

/**
 * Used on comments form to prevent comments form submission without filing the required fields
 */

function td_comments_form_validation() {

    //on form submit
    jQuery('.comment-form').submit( function(event) {

        jQuery('form#commentform :input').each( function() {

            var current_input_field = jQuery(this);
            var form = jQuery(this).parent().parent();

            if (current_input_field.attr('aria-required')){
                if (current_input_field.val() == '') {
                    event.preventDefault();
                    form.addClass('td-comment-form-warnings');

                    if (current_input_field.attr('id') == 'comment') {
                        form.find('.td-warning-comment').show(200);
                        current_input_field.css('border', '1px solid #ff7a7a');
                    } else if (current_input_field.attr('id') == 'author') {
                        form.find('.td-warning-author').show(200);
                        current_input_field.css('border', '1px solid #ff7a7a');
                    } else if (current_input_field.attr('id') == 'email') {
                        form.find('.td-warning-email').show(200);
                        current_input_field.css('border', '1px solid #ff7a7a');
                    }
                } else if ( current_input_field.attr('id') == 'email' && tdUtil.isEmail( current_input_field.val() ) === false ) {
                    event.preventDefault();
                    form.addClass('td-comment-form-warnings');
                    form.find('.td-warning-email-error').show(200);
                }
            }
        });

    });

    //on form input fields focus
    jQuery('form#commentform :input').each( function() {

        var form = jQuery(this).parent().parent();
        var current_input_field = jQuery(this);

        current_input_field.focus( function(){

            if (current_input_field.attr('id') == 'comment') {
                form.find('.td-warning-comment').hide(200);
                current_input_field.css('border', '1px solid #e1e1e1');

            } else if (current_input_field.attr('id') == 'author') {
                form.find('.td-warning-author').hide(200);
                current_input_field.css('border', '1px solid #e1e1e1');


            } else if (current_input_field.attr('id') == 'email') {
                form.find('.td-warning-email').hide(200);
                form.find('.td-warning-email-error').hide(200);
                current_input_field.css('border', '1px solid #e1e1e1');
            }
        });
    });
}


function td_scroll_to_class() {
    jQuery('[data-scroll-to-class]').on('click', function(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        jQuery('body').removeClass('td-menu-mob-open-menu');

        var $this = jQuery( this),
            offsetThis = $this.offset(),
            dataScrollToClass = $this.data( 'scroll-to-class' ),
            dataScrollOffset = $this.data( 'scroll-offset' ),
            dataScrollTarget = $this.data( 'scroll-target' );

        if ( 'undefined' === typeof dataScrollOffset || '' === dataScrollOffset ) {
            dataScrollOffset = 0;
        }

        if ( 'undefined' !== typeof dataScrollToClass && '' !== dataScrollToClass ) {
            var $toScrollElement = jQuery( '.' + dataScrollToClass );

            if ( tdEvents.window_innerWidth < 768 ) {
                // Timeout necessary for mobile menu transition
                setTimeout(function() {
                    td_helper_scroll_to_class( $this, $toScrollElement, offsetThis, 0, dataScrollTarget, dataScrollToClass );
                }, 500);
            } else {
                td_helper_scroll_to_class( $this, $toScrollElement, offsetThis, dataScrollOffset, dataScrollTarget, dataScrollToClass );
            }
        }
    });
}

function td_helper_scroll_to_class( $this, $toScrollElement, offsetThis, dataScrollOffset, dataScrollTarget, dataScrollToClass ) {
    if ( $toScrollElement.length ) {
        var offsetElement = $toScrollElement.offset(),
            duration = Math.floor( Math.abs( offsetThis.top - offsetElement.top) / 100 ) * 400;

        if ( duration > 1500 ) {
            duration = 1500;
        } else if ( duration < 500 ) {
            duration = 500;
        }

        //console.log(Math.abs( offsetThis.top - offsetElement.top));
        //console.log(duration);
        tdUtil.scrollToPosition( offsetElement.top + dataScrollOffset, duration ) ;

        var $li = $this.parent().parent( 'li.menu-item' );
        if ( $li.length ) {
            $li.siblings( '.current-menu-item' ).removeClass( 'current-menu-item' );
            $li.addClass( 'current-menu-item' );
        }
        jQuery( 'body').removeClass( 'td-menu-mob-open-menu' );

    } else if ( 'undefined' !== typeof dataScrollTarget && '' !== dataScrollTarget ) {
        td_set_cookies_life(['td-cookie-scroll-to-class', dataScrollToClass, 86400000]);//86400000 is the number of milliseconds in a day
        td_set_cookies_life(['td-cookie-scroll-offset', dataScrollOffset, 86400000]);//86400000 is the number of milliseconds in a day

        jQuery( 'body').removeClass( 'td-menu-mob-open-menu' );
        window.location = dataScrollTarget;
    }
}

jQuery(window).load(function(){

    //read the cookie
    var td_cookie_scroll_to_class = td_read_site_cookie( 'td-cookie-scroll-to-class' ),
        td_cookie_scroll_offset = td_read_site_cookie( 'td-cookie-scroll-offset' );

    if ( 'undefined' !== typeof td_cookie_scroll_to_class && null !== td_cookie_scroll_to_class ) {

        // Delete cookies
        td_set_cookies_life(['td-cookie-scroll-to-class', '', 1]);
        td_set_cookies_life(['td-cookie-scroll-offset', '', 1]);

        var $toScrollElement = jQuery( '.' + td_cookie_scroll_to_class );

        if ( $toScrollElement.length ) {
            var offsetElement = $toScrollElement.offset(),
                duration = Math.floor(Math.abs(offsetElement.top) / 100) * 400;

            if (duration > 1500) {
                duration = 1500;
            } else if (duration < 500) {
                duration = 500;
            }

            var scrollOffset = 0;
            if ( 'undefined' !== typeof td_cookie_scroll_offset && null !== td_cookie_scroll_offset ) {
                scrollOffset = parseInt( td_cookie_scroll_offset );
            }
            tdUtil.scrollToPosition(offsetElement.top + scrollOffset, duration);

            var $currentItem = jQuery('[data-scroll-to-class="' + td_cookie_scroll_to_class + '"]');
            if ( $currentItem.length ) {
                var $li = $currentItem.parent().parent('li.menu-item');
                if ($li.length) {
                    $li.siblings('.current-menu-item').removeClass('current-menu-item');
                    $li.addClass('current-menu-item');
                }
            }
        }
    }
});
/* global jQuery:false */
/* global tdUtil:false */

var tdLoadingBox = {};


( function() {
    "use strict";
    tdLoadingBox = {

        //arrayColors: ['#ffffff', '#fafafa', '#ececec', '#dddddd', '#bfbfbf', '#9a9a9a', '#7e7e7e', '#636363'],//whiter -> darker

        speed: 40,

        arrayColorsTemp: [
            'rgba(99, 99, 99, 0)',
            'rgba(99, 99, 99, 0.05)',
            'rgba(99, 99, 99, 0.08)',
            'rgba(99, 99, 99, 0.2)',
            'rgba(99, 99, 99, 0.3)',
            'rgba(99, 99, 99, 0.5)',
            'rgba(99, 99, 99, 0.6)',
            'rgba(99, 99, 99, 1)'
        ],//whiter -> darker

        arrayColors: [],

        statusAnimation: 'stop',

        //stop loading box
        stop : function stop () {
            tdLoadingBox.statusAnimation = 'stop';
            //jQuery('.td-loader-gif').html("");
        },


        //init loading box
        init : function init (color, speed) {

            // set up the speed
            if (false === tdUtil.isUndefined(speed)) {
                tdLoadingBox.speed = speed;
            }

            //console.log('test');
            var tdColorRegExp = /^#[a-zA-Z0-9]{3,6}$/;
            if(color && tdColorRegExp.test(color)) {

                var colRgba = tdLoadingBox.hexToRgb(color);

                var rgbaString = "rgba(" + colRgba.r + ", " + colRgba.g + ", " + colRgba.b + ", ";

                tdLoadingBox.arrayColors[7] = rgbaString + " 0.9)";
                tdLoadingBox.arrayColors[6] = rgbaString + " 0.7)";
                tdLoadingBox.arrayColors[5] = rgbaString + " 0.5)";
                tdLoadingBox.arrayColors[4] = rgbaString + " 0.3)";
                tdLoadingBox.arrayColors[3] = rgbaString + " 0.15)";
                tdLoadingBox.arrayColors[2] = rgbaString + " 0.15)";
                tdLoadingBox.arrayColors[1] = rgbaString + " 0.15)";
                tdLoadingBox.arrayColors[0] = rgbaString + " 0.15)";

            } else {
                //default array
                tdLoadingBox.arrayColors = tdLoadingBox.arrayColorsTemp.slice(0);

            }

            if(tdLoadingBox.statusAnimation === 'stop') {
                tdLoadingBox.statusAnimation = 'display';
                this.render();
            }
        },


        //create the animation
        render: function render (color) {

            //call the animationDisplay function
            tdLoadingBox.animationDisplay(
                '<div class="td-lb-box td-lb-box-1" style="background-color:' + tdLoadingBox.arrayColors[0] + '"></div>' +
                '<div class="td-lb-box td-lb-box-2" style="background-color:' + tdLoadingBox.arrayColors[1] + '"></div>' +
                '<div class="td-lb-box td-lb-box-3" style="background-color:' + tdLoadingBox.arrayColors[2] + '"></div>' +
                '<div class="td-lb-box td-lb-box-4" style="background-color:' + tdLoadingBox.arrayColors[3] + '"></div>' +
                '<div class="td-lb-box td-lb-box-5" style="background-color:' + tdLoadingBox.arrayColors[4] + '"></div>' +
                '<div class="td-lb-box td-lb-box-6" style="background-color:' + tdLoadingBox.arrayColors[5] + '"></div>' +
                '<div class="td-lb-box td-lb-box-7" style="background-color:' + tdLoadingBox.arrayColors[6] + '"></div>' +
                '<div class="td-lb-box td-lb-box-8" style="background-color:' + tdLoadingBox.arrayColors[7] + '"></div>'
            );

            //direction right
            var tempColorArray = [
                tdLoadingBox.arrayColors[0],
                tdLoadingBox.arrayColors[1],
                tdLoadingBox.arrayColors[2],
                tdLoadingBox.arrayColors[3],
                tdLoadingBox.arrayColors[4],
                tdLoadingBox.arrayColors[5],
                tdLoadingBox.arrayColors[6],
                tdLoadingBox.arrayColors[7]
            ];

            tdLoadingBox.arrayColors[0] = tempColorArray[7];
            tdLoadingBox.arrayColors[1] = tempColorArray[0];
            tdLoadingBox.arrayColors[2] = tempColorArray[1];
            tdLoadingBox.arrayColors[3] = tempColorArray[2];
            tdLoadingBox.arrayColors[4] = tempColorArray[3];
            tdLoadingBox.arrayColors[5] = tempColorArray[4];
            tdLoadingBox.arrayColors[6] = tempColorArray[5];
            tdLoadingBox.arrayColors[7] = tempColorArray[6];

            if(tdLoadingBox.statusAnimation === 'display') {


                setTimeout(tdLoadingBox.render, tdLoadingBox.speed);
            } else {
                tdLoadingBox.animationDisplay('');
            }
        },


        //display the animation
        animationDisplay: function (animation_str) {
            jQuery('.td-loader-gif').html(animation_str);
        },


        //converts hex to rgba
        hexToRgb: function (hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    }; //tdLoadingBox.init();//tdLoadingBox.stop();
})();







/* global jQuery:{} */
/* global tdDetect:{} */
/* global tdLocalCache:{} */
/* global td_ajax_url:{} */

var tdAjaxSearch = {};

jQuery().ready(function() {

    'use strict';

    tdAjaxSearch.init();

});

(function() {

    'use strict';

    tdAjaxSearch = {

        // private vars
        _current_selection_index: 0,
        _last_request_results_count: 0,
        _first_down_up: true,
        _is_search_open: false,


        /**
         * init the class
         */
        init: function init() {

            // hide the drop down if we click outside of it
            jQuery(document).on( 'click', function(e) {
                if (
                    'td-icon-search' !== e.target.className &&
                    'td-header-search' !== e.target.id &&
                    'td-header-search-top' !== e.target.id &&
                    true === tdAjaxSearch._is_search_open
                ) {
                    tdAjaxSearch.hide_search_box();
                }
            });


            // show and hide the drop down on the search icon
            jQuery( '#td-header-search-button' ).on( 'click', function(event){
                event.preventDefault();
                event.stopPropagation();
                if (tdAjaxSearch._is_search_open === true) {
                    tdAjaxSearch.hide_search_box();

                } else {
                    tdAjaxSearch.show_search_box();
                }
            });


            // show and hide the drop down on the search icon for mobile
            jQuery( '#td-header-search-button-mob, #tdb-header-search-button-mob' ).on( 'click', function(event){
                jQuery( 'body' ).addClass( 'td-search-opened' );

                var search_input = jQuery('#td-header-search-mob');

                /**
                 * Note: the autofocus does not work for iOS and windows phone devices as it's considered bad user experience
                 */
                    //autofocus
                setTimeout(function(){
                    search_input.focus();
                }, 1300);

                var current_query = search_input.val().trim();
                if ( current_query.length > 0 ) {
                    tdAjaxSearch.do_ajax_call_mob();
                }
            });


            //close the search
            jQuery( '.td-search-close a' ).on( 'click', function(){
                jQuery( 'body' ).removeClass( 'td-search-opened' );
            });


            // keydown on the text box
            jQuery( '#td-header-search' ).keydown(function(event) {
                if (
                    ( event.which && 39 === event.which ) ||
                    ( event.keyCode && 39 === event.keyCode ) ||
                    ( event.which && 37 === event.which ) ||
                    ( event.keyCode && 37 === event.keyCode ) )
                {
                    //do nothing on left and right arrows
                    tdAjaxSearch.td_aj_search_input_focus();
                    return;
                }

                if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
                    // on enter
                    var td_aj_cur_element = jQuery('.td-aj-cur-element');
                    if (td_aj_cur_element.length > 0) {
                        //alert('ra');
                        var td_go_to_url = td_aj_cur_element.find('.entry-title a').attr('href');
                        window.location = td_go_to_url;
                    } else {
                        jQuery(this).parent().parent().submit();
                    }
                    return false; //redirect for search on enter

                } else if ( ( event.which && 40 === event.which ) || ( event.keyCode && 40 === event.keyCode ) ) {
                    // down
                    tdAjaxSearch.move_prompt_down();
                    return false; //disable the envent

                } else if ( ( event.which && 38 === event.which ) || ( event.keyCode && 38 === event.keyCode ) ) {
                    //up
                    tdAjaxSearch.move_prompt_up();
                    return false; //disable the envent

                } else {
                    //for backspace we have to check if the search query is empty and if so, clear the list
                    if ( ( event.which && 8 === event.which ) || ( event.keyCode && 8 === event.keyCode ) ) {
                        //if we have just one character left, that means it will be deleted now and we also have to clear the search results list
                        var search_query = jQuery(this).val();
                        if ( 1 === search_query.length ) {
                            jQuery('#td-aj-search').empty();
                        }
                    }

                    //various keys
                    tdAjaxSearch.td_aj_search_input_focus();
                    //jQuery('#td-aj-search').empty();
                    setTimeout(function(){
                        tdAjaxSearch.do_ajax_call();
                    }, 100);
                }
                return true;
            });

            // keydown on the text box
            jQuery('#td-header-search-mob').keydown(function(event) {

                if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
                    // on enter
                    var td_aj_cur_element = jQuery('.td-aj-cur-element');
                    if (td_aj_cur_element.length > 0) {
                        //alert('ra');
                        var td_go_to_url = td_aj_cur_element.find( '.entry-title a' ).attr( 'href' );
                        window.location = td_go_to_url;
                    } else {
                        jQuery(this).parent().parent().submit();
                    }
                    return false; //redirect for search on enter
                } else {

                    //for backspace we have to check if the search query is empty and if so, clear the list
                    if ( ( event.which && 8 === event.which ) || ( event.keyCode && 8 === event.keyCode ) ) {
                        //if we have just one character left, that means it will be deleted now and we also have to clear the search results list
                        var search_query = jQuery(this).val();
                        if ( 1 === search_query.length ) {
                            jQuery('#td-aj-search-mob').empty();
                        }
                    }

                    setTimeout(function(){
                        tdAjaxSearch.do_ajax_call_mob();
                    }, 100);

                    return true;
                }
            });
        },


        show_search_box: function() {
            jQuery( '.td-drop-down-search' ).addClass( 'td-drop-down-search-open' );
            // do not try to autofocus on ios. It's still buggy as of 18 march 2015
            if ( true !== tdDetect.isIos ) {
                setTimeout(function(){
                    document.getElementById( 'td-header-search' ).focus();
                }, 200);
            }
            tdAjaxSearch._is_search_open = true;
        },


        hide_search_box: function hide_search_box() {
            jQuery(".td-drop-down-search").removeClass('td-drop-down-search-open');
            tdAjaxSearch._is_search_open = false;
        },



        /**
         * moves the select up
         */
        move_prompt_up: function() {
            if (tdAjaxSearch._first_down_up === true) {
                tdAjaxSearch._first_down_up = false;
                if (tdAjaxSearch._current_selection_index === 0) {
                    tdAjaxSearch._current_selection_index = tdAjaxSearch._last_request_results_count - 1;
                } else {
                    tdAjaxSearch._current_selection_index--;
                }
            } else {
                if (tdAjaxSearch._current_selection_index === 0) {
                    tdAjaxSearch._current_selection_index = tdAjaxSearch._last_request_results_count;
                } else {
                    tdAjaxSearch._current_selection_index--;
                }
            }
            tdAjaxSearch._repaintCurrentElement();
        },



        /**
         * moves the select prompt down
         */
        move_prompt_down: function() {
            if (tdAjaxSearch._first_down_up === true) {
                tdAjaxSearch._first_down_up = false;
            } else {
                if (tdAjaxSearch._current_selection_index === tdAjaxSearch._last_request_results_count) {
                    tdAjaxSearch._current_selection_index = 0;
                } else {
                    tdAjaxSearch._current_selection_index++;
                }
            }
            tdAjaxSearch._repaintCurrentElement();
        },


        /**
         * Recompute the current element in the search results.
         * Used by the move_prompt_up and move_prompt_down
         * @private
         */
        _repaintCurrentElement: function() {
            jQuery( '.td_module_wrap' ).removeClass( 'td-aj-cur-element' );

            if (tdAjaxSearch._current_selection_index > tdAjaxSearch._last_request_results_count - 1 ) {
                //the input is selected
                jQuery( '.td-search-form' ).fadeTo( 100, 1 );
            } else {
                tdAjaxSearch.td_aj_search_input_remove_focus();
                jQuery( '.td_module_wrap' ).eq( tdAjaxSearch._current_selection_index ).addClass( 'td-aj-cur-element' );
            }
        },


        /**
         * puts the focus on the input box
         */
        td_aj_search_input_focus: function() {
            tdAjaxSearch._current_selection_index = 0;
            tdAjaxSearch._first_down_up = true;
            jQuery( '.td-search-form' ).fadeTo( 100, 1 );
            jQuery( '.td_module_wrap' ).removeClass( 'td-aj-cur-element' );
        },



        /**
         * removes the focus from the input box
         */
        td_aj_search_input_remove_focus: function() {
            if ( 0 !== tdAjaxSearch._last_request_results_count ) {
                jQuery( '.td-search-form' ).css( 'opacity', 0.5 );
            }
        },



        /**
         * AJAX: process the response from the server
         */
        process_ajax_response: function(data) {
            var current_query = jQuery( '#td-header-search' ).val();

            //the search is empty - drop results
            if ( '' === current_query ) {
                jQuery( '#td-aj-search' ).empty();
                return;
            }

            var td_data_object = jQuery.parseJSON(data); //get the data object
            //drop the result - it's from a old query
            if ( td_data_object.td_search_query !== current_query ) {
                return;
            }

            //reset the current selection and total posts
            tdAjaxSearch._current_selection_index = 0;
            tdAjaxSearch._last_request_results_count = td_data_object.td_total_in_list;
            tdAjaxSearch._first_down_up = true;

            //update the query
            jQuery( '#td-aj-search' ).html( td_data_object.td_data );

            /*
             td_data_object.td_data
             td_data_object.td_total_results
             td_data_object.td_total_in_list
             */

            // the .entry-thumb are searched for in the #td-aj-search object, sorted and added into the view port array items
            if ( ( 'undefined' !== typeof window.tdAnimationStack )  && ( true === window.tdAnimationStack.activated ) ) {
                window.tdAnimationStack.check_for_new_items( '#td-aj-search .td-animation-stack', window.tdAnimationStack.SORTED_METHOD.sort_left_to_right, true, false );
                window.tdAnimationStack.compute_items(false);
            }
        },


        /**
         * AJAX: process the response from the server for the responsive version of the theme
         */
        process_ajax_response_mob: function(data) {
            var current_query = jQuery( '#td-header-search-mob' ).val();

            //the search is empty - drop results
            if ( '' === current_query ) {
                jQuery('#td-aj-search-mob').empty();
                return;
            }

            var td_data_object = jQuery.parseJSON( data ); //get the data object
            //drop the result - it's from a old query
            if ( td_data_object.td_search_query !== current_query ) {
                return;
            }

            //update the query
            jQuery( '#td-aj-search-mob' ).html( td_data_object.td_data );

            // the .entry-thumb are searched for in the #td-aj-search-mob object, sorted and added into the view port array items
            if ( ( 'undefined' !== typeof window.tdAnimationStack )  && ( true === window.tdAnimationStack.activated ) ) {
                window.tdAnimationStack.check_for_new_items( '#td-aj-search-mob .td-animation-stack', window.tdAnimationStack.SORTED_METHOD.sort_left_to_right, true, false );
                window.tdAnimationStack.compute_items(false);
            }
        },


        /**
         * AJAX: do the ajax request
         */
        do_ajax_call: function() {
            var search_query = jQuery('#td-header-search').val();

            if ( '' === search_query ) {
                tdAjaxSearch.td_aj_search_input_focus();
                return;
            }

            //do we have a cache hit
            if (tdLocalCache.exist(search_query)) {
                tdAjaxSearch.process_ajax_response(tdLocalCache.get(search_query));
                return; //cache HIT
            }

            //fk no cache hit - do the real request

            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                data: {
                    action: 'td_ajax_search',
                    td_string: search_query
                },
                success: function(data, textStatus, XMLHttpRequest){
                    tdLocalCache.set(search_query, data);
                    tdAjaxSearch.process_ajax_response(data);
                },
                error: function(MLHttpRequest, textStatus, errorThrown){
                    //console.log(errorThrown);
                }
            });
        },


        /**
         * AJAX: do the ajax request for the responsive version of the theme
         */
        do_ajax_call_mob: function() {
            var search_query = jQuery( '#td-header-search-mob' ).val();

            if ( '' === search_query) {
                return;
            }

            //do we have a cache hit
            if ( tdLocalCache.exist( search_query ) ) {
                tdAjaxSearch.process_ajax_response_mob( tdLocalCache.get( search_query ) );
                return; //cache HIT
            }

            //fk no cache hit - do the real request

            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                data: {
                    action: 'td_ajax_search',
                    td_string: search_query
                },
                success: function( data, textStatus, XMLHttpRequest ){
                    tdLocalCache.set( search_query, data );
                    tdAjaxSearch.process_ajax_response_mob( data );
                },
                error: function( MLHttpRequest, textStatus, errorThrown ){
                    //console.log(errorThrown);
                }
            });
        }
    };

})();

;'use strict';

/* ----------------------------------------------------------------------------
 tdPostImages.js
 --------------------------------------------------------------------------- */

/* global jQuery:{} */
/* global tdUtil:{} */
/* global tdAffix:{} */
/* global tdIsScrollingAnimation:boolean */

/*  ----------------------------------------------------------------------------
 On load
 */
jQuery().ready(function() {

    //move classes from post images to figure - td-post-image-full etc
    tdUtil.imageMoveClassToFigure( 'td-post-image-full' );
    tdUtil.imageMoveClassToFigure( 'td-post-image-right' );
    tdUtil.imageMoveClassToFigure( 'td-post-image-left' );

    /**
     * - add a general td-modal-image class to the all post images
     */
    if ( ( 'undefined' !== typeof window.tds_general_modal_image ) && ( '' !== window.tds_general_modal_image ) ) {
        jQuery( '.single .td-post-content a > img' ).filter(function( index, element ) {
            if ( -1 !== element.className.indexOf( 'wp-image' ) ) {

                var $el = jQuery( element ),
                    image_link = $el.parent(),
                    href = image_link.attr("href");

                //add the modal class only on post image links that do not link to custom URLs ( for media linking images and attachments only )
                if ((-1 !== href.indexOf(document.domain)) && (-1 !== href.indexOf('uploads') || -1 !== href.indexOf('attachment') )) {
                    image_link.addClass( 'td-modal-image' );


                    if ( -1 !== href.indexOf('attachment') ) {
                        image_link.attr('href', $el.attr('src'));
                    }
                }
            }
        });
    }
});



// used for scrolling to the last element
var tdModalImageLastEl = '';
/**
 * tdBlocks.js
 * v3.0  5 August 2015
 * Converted to WP JS standards + jsHint
 */

/* global jQuery:false */
/* global td_ajax_url:false */
/* global tds_theme_color_site_wide:false */



/* global tdSmartSidebar:{} */
/* global tdAnimationStack:{} */
/* global tdUtil:false */                   //done
/* global tdLoadingBox:false */             //done
/* global tdInfiniteLoader:false */         //done
/* global tdBlocksArray:false */            //done
/* global tdDetect:false */                 //done
/* global tdLocalCache:false */             //done

var tdBlocks = {};

( function() {
    "use strict";

    /*  ----------------------------------------------------------------------------
     On load
     */
    jQuery().ready( function() {
        tdOnReadyAjaxBlocks();
    });






    function tdOnReadyAjaxBlocks() {


        /*  ----------------------------------------------------------------------------
            AJAX pagination next
         */
        jQuery(".td-ajax-next-page").on( 'click', function(event) {
            event.preventDefault();

            var currentBlockObj = tdBlocks.tdGetBlockObjById(jQuery(this).data('td_block_id'));

            if ( jQuery(this).hasClass('ajax-page-disabled') || true === currentBlockObj.is_ajax_running ) {
                return;
            }

            currentBlockObj.is_ajax_running = true; // ajax is running and we're waiting for a reply from server

            currentBlockObj.td_current_page++;
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'next');
        });

        /*  ----------------------------------------------------------------------------
            AJAX pagination prev
         */
        jQuery(".td-ajax-prev-page").on( 'click', function(event) {
            event.preventDefault();

            var currentBlockObj = tdBlocks.tdGetBlockObjById(jQuery(this).data('td_block_id'));

            if ( jQuery(this).hasClass('ajax-page-disabled') || true === currentBlockObj.is_ajax_running ) {
                return;
            }

            currentBlockObj.is_ajax_running = true; // ajax is running and we're waiting for a reply from server

            currentBlockObj.td_current_page--;
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'back');
        });

        /*  ----------------------------------------------------------------------------
            AJAX pagination load more
         */
        jQuery(".td_ajax_load_more_js").on( 'click', function(event) {
            event.preventDefault();
            if ( jQuery(this).hasClass('ajax-page-disabled') ) {
                return;
            }

            jQuery(this).css('visibility', 'hidden');

            var currentBlockObj = tdBlocks.tdGetBlockObjById(jQuery(this).data('td_block_id'));

            currentBlockObj.td_current_page++;
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'load_more');

            // load_more is hidden if there are no more posts
            if ( currentBlockObj.max_num_pages <= currentBlockObj.td_current_page ) {
                jQuery(this).addClass('ajax-page-disabled');
            }
        });

        /*  ----------------------------------------------------------------------------
            pull down open/close //on mobile devices use click event
         */
        if ( tdDetect.isMobileDevice ) {

            jQuery(".td-pulldown-filter-display-option").on( 'click', function () {
                var currentBlockUid = jQuery(this).data('td_block_id');
                jQuery("#td_pulldown_" + currentBlockUid).addClass("td-pulldown-filter-list-open");

                //animate the list
                var tdPullDownList = jQuery("#td_pulldown_" + currentBlockUid + "_list");
                tdPullDownList.removeClass('fadeOut');
                tdPullDownList.addClass('td_animated td_fadeIn'); //used for opacity animation
                //tdPullDownList.css('visibility', 'visible');
            });

            //on desktop devices use hover event
        } else {

            /**
             * (hover) open and close the drop down menu (on blocks on hover)
             */
            jQuery(".td-pulldown-filter-display-option").hover( function () {
                    // hover in
                    var current_block_uid = jQuery(this).data('td_block_id');
                    jQuery("#td_pulldown_" + current_block_uid).addClass("td-pulldown-filter-list-open");

                    //animate the list
                    var tdPullDownList = jQuery("#td_pulldown_" + current_block_uid + "_list");
                    tdPullDownList.removeClass('fadeOut');
                    tdPullDownList.addClass('td_animated td_fadeIn'); //used for opacity animation
                    tdPullDownList.css('visibility', 'visible');

                },
                function () {
                    // hover out
                    var currentBlockUid = jQuery(this).data('td_block_id');
                    jQuery("#td_pulldown_" + currentBlockUid).removeClass("td-pulldown-filter-list-open");


                }
            );
        }

        /*  ----------------------------------------------------------------------------
            click on related posts in single posts
         */
        jQuery('.td-related-title a').on( 'click', function(event) {
            event.preventDefault();

            jQuery('.td-related-title').children('a').removeClass('td-cur-simple-item');
            jQuery(this).addClass('td-cur-simple-item');

            //get the current block id
            var currentBlockUid = jQuery(this).data('td_block_id');

            //get current block
            var currentBlockObj = tdBlocks.tdGetBlockObjById(currentBlockUid);

            //change current filter value - the filter type is readed by td_ajax from the atts of the shortcode
            currentBlockObj.td_filter_value = jQuery(this).data('td_filter_value');

            currentBlockObj.td_current_page = 1; //reset the page

            //do request
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'pull_down');
        });


        /*  ----------------------------------------------------------------------------
            MEGA MENU
         */
        // Used to simulate on mobile doubleclick at 300ms @see the function tdAjaxSubCatMegaRun()
        var tdSubCatMegaRunLink = false;   // run the link if this is true, instead of loading via ajax the mega menu content
        var tdSubCatMegaLastTarget = '';   // last event target - to make sure the double click is on the same element


        /**
         * On touch screens check for double click and redirect to the subcategory page if that's the case,
         * if not double click... do the normal ajax request
         * @param event
         * @param jQueryObject
         */
        function tdAjaxSubCatMegaRunOnTouch(event, jQueryObject) {
            if ( (true === tdSubCatMegaRunLink) && (event.target === tdSubCatMegaLastTarget) ) {
                window.location = event.target;
            } else {
                tdSubCatMegaRunLink = true;
                tdSubCatMegaLastTarget = event.target;
                event.preventDefault();

                setTimeout( function() {
                    tdSubCatMegaRunLink = false;
                }, 300);

                tdAjaxSubCatMegaRun(event, jQueryObject);
            }
        }

        /**
         * this one makes the ajax request for mega menu filter
         * hover or click on mega menu subcategories
         */
        function tdAjaxSubCatMegaRun(event, jQueryObject) {
            /* global this:false */
            //get the current block id
            var currentBlockUid = jQueryObject.data('td_block_id');
            var currentBlockObj = tdBlocks.tdGetBlockObjById(currentBlockUid);

            // on mega menu, we allow parallel ajax request for better UI. We set is_ajax_running so that the preloader cache will work as expected
            currentBlockObj.is_ajax_running = true;

            //switch cur cat
            jQuery('.mega-menu-sub-cat-' + currentBlockUid).removeClass('cur-sub-cat');
            jQueryObject.addClass('cur-sub-cat');

            //get current block


            //change current filter value - the filter type is readed by td_ajax from the atts of the shortcode
            currentBlockObj.td_filter_value = jQueryObject.data('td_filter_value');

            currentBlockObj.td_current_page = 1; //reset the page

            //do request
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'mega_menu');
        }


        /**
         * Mega menu filters
         */
        //on touch devices use click
        // !!!! needs testing to determine why we need .click and touchend?
        // !!!! trebuie refactorizata sa transmita jQuery(this) de aici la functii
        if ( tdDetect.isTouchDevice ) {
            jQuery(".block-mega-child-cats a")
                .click( function(event) {
                    tdAjaxSubCatMegaRunOnTouch(event, jQuery(this));
                }, false)
                .each(function(index, element) {
                    element.addEventListener('touchend', function(event) {
                        tdAjaxSubCatMegaRunOnTouch(event, jQuery(this));
                    }, false);
                });

        } else {
            jQuery(".block-mega-child-cats a").hover( function(event) {
                tdAjaxSubCatMegaRun(event, jQuery(this));
            }, function (event) {} );
        }



        /*  ----------------------------------------------------------------------------
            Subcategories
         */
        /**
         * Newspaper ONLY
         * used by the drop down ajax filter on blocks
         */
        jQuery('.td-subcat-item a').on( 'click', function(event) {
            event.preventDefault();

            var currentBlockObj = tdBlocks.tdGetBlockObjById(jQuery(this).data('td_block_id'));

            //if ( jQuery(this).hasClass('ajax-page-disabled') || true === currentBlockObj.is_ajax_running ) {
            //    return;
            //}
            //
            if ( true === currentBlockObj.is_ajax_running ) {
                return;
            }


            currentBlockObj.is_ajax_running = true; // ajax is running and we're waiting for a reply from server


            jQuery('.' + jQuery(this).data('td_block_id') + '_rand').find('.td-cur-simple-item').removeClass('td-cur-simple-item');
            jQuery(this).addClass('td-cur-simple-item');


            //change current filter value - the filter type is read by td_ajax from the atts of the shortcode
            currentBlockObj.td_filter_value = jQuery(this).data('td_filter_value');



            //reset the page
            currentBlockObj.td_current_page = 1;

            // we ues 'pull_down' just for the 'td_animated_xlong td_fadeInDown' effect
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'pull_down');
        });


        /**
         * Newsmag ONLY
         * when a item is from the dropdown menu is clicked (on all the blocks)
         * !!!! asta face ceva cu ios slider-ul in plus fata de aia de pe Newspaper
         */
        jQuery(".td-pulldown-filter-link").on( 'click', function(event) {
            event.preventDefault();



            //get the current block id
            var currentBlockUid = jQuery(this).data('td_block_id');

            //destroy any iossliders to avoid bugs
            jQuery('#' + currentBlockUid).find('.iosSlider').iosSlider('destroy');

            //get current block
            var currentBlockObj = tdBlocks.tdGetBlockObjById(currentBlockUid);
            if ( true === currentBlockObj.is_ajax_running ) {
                return;
            }

            currentBlockObj.is_ajax_running = true;
            //change current filter value - the filter type is readed by td_ajax from the atts of the shortcode
            currentBlockObj.td_filter_value = jQuery(this).data('td_filter_value');

            jQuery('.' + jQuery(this).data('td_block_id') + '_rand').find('.td-cur-simple-item').removeClass('td-cur-simple-item');
            jQuery(this).addClass('td-cur-simple-item');

            currentBlockObj.td_current_page = 1;


            //put loading... text and hide the dropdown !!!! - tranlation pt loading
            //tdBlocks.tdPullDownFilterChangeValue(currentBlockObj.id, '<span>Loading... </span><i class="td-icon-menu-down"></i>');
            //tdBlocks.tdPullDownFilterChangeValue(currentBlockUid, '<span>' + jQuery(this).html() + ' </span><i class="td-icon-menu-down"></i>');


            //hide the dropdown
            jQuery('#td_pulldown_' + currentBlockUid).removeClass("td-pulldown-filter-list-open");


            //do request
            tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'pull_down');


            //on mobile devices stop event propagation
            if ( tdDetect.isMobileDevice ) {
                tdUtil.stopBubble(event);
            }

        });
    } // end tdOnReadyAjaxBlocks()






    tdBlocks = {


        /**
         * Newsmag ONLY change the pull down filter value to loading... and to the current category after an ajax reply
         * is received
         * @param td_block_uid
         * @param td_text
         */
        tdPullDownFilterChangeValue: function(td_block_uid, td_text) {
            jQuery('#td-pulldown-' + td_block_uid + '-val').html(td_text);
        },



        /**
         * makes a ajax block request
         * @param current_block_obj
         * @param td_user_action - load more or infinite loader (used by the animation)
         * @returns {string}
         */
        tdAjaxDoBlockRequest: function(current_block_obj, td_user_action) {
            //search the cache
            var currentBlockObjSignature = JSON.stringify(current_block_obj);
            if ( tdLocalCache.exist(currentBlockObjSignature) ) {
                //do the animation with cache hit = true
                tdBlocks.tdBlockAjaxLoadingStart(current_block_obj, true, td_user_action);
                tdBlocks.tdAjaxBlockProcessResponse(tdLocalCache.get(currentBlockObjSignature), td_user_action);
                return 'cache_hit'; //cache HIT
            }


            //cache miss - we make a full request! - cache hit - false
            tdBlocks.tdBlockAjaxLoadingStart(current_block_obj, false, td_user_action);
//return;
            var requestData = {
                action: 'td_ajax_block',
                td_atts: current_block_obj.atts,
                td_block_id:current_block_obj.id,
                td_column_number:current_block_obj.td_column_number,
                td_current_page:current_block_obj.td_current_page,
                block_type:current_block_obj.block_type,
                td_filter_value:current_block_obj.td_filter_value,
                td_user_action:current_block_obj.td_user_action,
                td_magic_token: tdBlockNonce
            };

            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                cache:true,
                data: requestData,
                success: function(data, textStatus, XMLHttpRequest) {

                    tdLocalCache.set(currentBlockObjSignature, data);
                    tdBlocks.tdAjaxBlockProcessResponse(data, td_user_action);
                },
                error: function(MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        },


        /**
         * process the response from the ajax query (it also processes the responses stored in the cache)
         * @param data
         * @param td_user_action - load more or infinite loader (used by the animation)
         */
        tdAjaxBlockProcessResponse: function(data, td_user_action) {

            //read the server response
            var tdReplyObj = jQuery.parseJSON(data); //get the data object

            //console.log('tdAjaxBlockProcessResponse:');
            //console.log(tdReplyObj);
            /*
             td_data_object.td_block_id
             td_data_object.td_data
             td_data_object.td_cur_cat
             */






            //load the content (in place or append)
            if ( 'load_more' === td_user_action || 'infinite_load' === td_user_action ) {

                // fix needed to keep sidebars fixed down when they are bottom of the content and the content grows up
                for ( var i = 0; i < tdSmartSidebar.items.length; i++ ) {
                    if ( 'case_3_bottom_of_content' === tdSmartSidebar.items[ i ].sidebar_state ) {
                        tdSmartSidebar.items[ i ].sidebar_state = 'case_1_fixed_down';
                    }
                }

                jQuery(tdReplyObj.td_data).appendTo('#' + tdReplyObj.td_block_id);
                //jQuery(tdReplyObj.td_data).addClass('td_animated_xxlong').appendTo('#' + tdReplyObj.td_block_id).addClass('td_fadeIn');
                //jQuery('#' + tdReplyObj.td_block_id).append(tdReplyObj.td_data); //append
            } else {
                jQuery('#' + tdReplyObj.td_block_id).html(tdReplyObj.td_data); //in place
            }


            //hide or show prev
            if ( true === tdReplyObj.td_hide_prev ) {
                jQuery('#prev-page-' + tdReplyObj.td_block_id).addClass('ajax-page-disabled');
            } else {
                jQuery('#prev-page-' + tdReplyObj.td_block_id).removeClass('ajax-page-disabled');
            }

            //hide or show next
            if ( true === tdReplyObj.td_hide_next ) {
                jQuery('#next-page-' + tdReplyObj.td_block_id).addClass('ajax-page-disabled');
            } else {
                jQuery('#next-page-' + tdReplyObj.td_block_id).removeClass('ajax-page-disabled');
            }


            var  currentBlockObj = tdBlocks.tdGetBlockObjById(tdReplyObj.td_block_id);
            if ( 'slide' === currentBlockObj.block_type ) {
                //make the first slide active (to have caption)
                jQuery('#' + tdReplyObj.td_block_id + ' .slide-wrap-active-first').addClass('slide-wrap-active');
            }

            currentBlockObj.is_ajax_running = false; // finish the loading for this block


            //loading effects
            tdBlocks.tdBlockAjaxLoadingEnd(tdReplyObj, currentBlockObj, td_user_action);   //td_user_action - load more or infinite loader (used by the animation)
        },



        /**
         * loading start
         * @param current_block_obj
         * @param cache_hit boolean - is true if we have a cache hit
         * @param td_user_action - the request type / infinite_load ?
         */
        tdBlockAjaxLoadingStart: function(current_block_obj, cache_hit, td_user_action) {

            //get the element
            var elCurTdBlockInner = jQuery('#' + current_block_obj.id);

            //remove any remaining loaders
            jQuery('.td-loader-gif').remove();

            //remove animation classes
            elCurTdBlockInner.removeClass('td_fadeInRight td_fadeInLeft td_fadeInDown td_fadeInUp td_animated_xlong');

            elCurTdBlockInner.addClass('td_block_inner_overflow');
            //auto height => fixed height
            var tdTmpBlockHeight = elCurTdBlockInner.height();
            elCurTdBlockInner.css('height', tdTmpBlockHeight);


            //show the loader only if we have a cache MISS
            if ( false === cache_hit ) {
                if ( 'load_more' === td_user_action) {
                    // on load more
                    elCurTdBlockInner.parent().append('<div class="td-loader-gif td-loader-infinite td-loader-blocks-load-more  td-loader-animation-start"></div>');
                    tdLoadingBox.init(current_block_obj.header_color ? current_block_obj.header_color : tds_theme_color_site_wide);  //init the loading box
                    setTimeout(function () {
                        jQuery('.td-loader-gif')
                            .removeClass('td-loader-animation-start')
                            .addClass('td-loader-animation-mid');
                    }, 50);

                } else if ('infinite_load' === td_user_action) {
                    // on infinite load
                    elCurTdBlockInner.parent().append('<div class="td-loader-gif td-loader-infinite td-loader-animation-start"></div>');
                    tdLoadingBox.init(current_block_obj.header_color ? current_block_obj.header_color : tds_theme_color_site_wide);  //init the loading box
                    setTimeout(function () {
                        jQuery('.td-loader-gif')
                            .removeClass('td-loader-animation-start')
                            .addClass('td-loader-animation-mid');
                    }, 50);

                } else {
                    /**
                     * the default animation if the user action is NOT load_more or infinite_load
                     * infinite load has NO animation !
                     */
                    elCurTdBlockInner.parent().append('<div class="td-loader-gif td-loader-animation-start"></div>');
                    tdLoadingBox.init(current_block_obj.header_color ? current_block_obj.header_color : tds_theme_color_site_wide);         //init the loading box (the parameter is the block title background color or tds_theme_color_site_wide)
                    setTimeout( function(){
                        jQuery('.td-loader-gif')
                            .removeClass('td-loader-animation-start')
                            .addClass('td-loader-animation-mid');
                    },50);
                    elCurTdBlockInner.addClass('td_animated_long td_fadeOut_to_1');

                }
            } // end cache_hit if
        },



        /**
         * we have a reply from the ajax request
         * @param td_reply_obj - the reply object that we got from the server, it's useful with infinite load
         * @param current_block_obj
         * @param td_user_action - load more or infinite loader (used by the animation)
         */
        tdBlockAjaxLoadingEnd: function(td_reply_obj, current_block_obj, td_user_action) {

            //jQuery('.td-loader-gif').remove();
            // remove the loader
            jQuery('.td-loader-gif')
                .removeClass('td-loader-animation-mid')
                .addClass('td-loader-animation-end');
            setTimeout( function() {
                jQuery('.td-loader-gif').remove();
                //stop the loading box
                tdLoadingBox.stop();
            },400);

            //get the current inner
            var elCurTdBlockInner = jQuery('#' + current_block_obj.id);

            elCurTdBlockInner.removeClass('td_animated_long td_fadeOut_to_1');

            // by default, the sort method used to animate the ajax response is left to the right
            var tdAnimationStackSortType;

            if ( true === tdAnimationStack.activated ) {
                tdAnimationStackSortType = tdAnimationStack.SORTED_METHOD.sort_left_to_right;
            }

            switch(td_user_action) {
                case 'next':
                    elCurTdBlockInner.addClass('td_animated_xlong td_fadeInRight');

                    // the default sort method is modified to work from right to the left
                    if ( undefined !== tdAnimationStackSortType ) {
                        tdAnimationStackSortType = tdAnimationStack.SORTED_METHOD.sort_right_to_left;
                    }

                    break;
                case 'back':
                    elCurTdBlockInner.addClass('td_animated_xlong td_fadeInLeft');
                    break;
                case 'pull_down':
                    elCurTdBlockInner.addClass('td_animated_xlong td_fadeInDown');
                    break;
                case 'mega_menu':
                    elCurTdBlockInner.addClass('td_animated_xlong td_fadeInDown');
                    break;
                case 'load_more':
                    //console.log('.' + current_block_obj.id + '_rand .td_ajax_load_more_js');
                    setTimeout ( function() {
                        jQuery('.' + current_block_obj.id + '_rand .td_ajax_load_more_js').css('visibility', 'visible');
                    }, 500);

                    break;
                case 'infinite_load':
                    setTimeout( function() {
                        //refresh waypoints for infinit scroll tdInfiniteLoader
                        tdInfiniteLoader.computeTopDistances();
                        if ( '' !== td_reply_obj.td_data  ) {
                            tdInfiniteLoader.enable_is_visible_callback(current_block_obj.id);
                        }
                    }, 500);


                    setTimeout( function() {
                        tdInfiniteLoader.computeTopDistances();
                        // load next page only if we have new data coming from the last ajax request
                    }, 1000);

                    setTimeout( function() {
                        tdInfiniteLoader.computeTopDistances();
                    }, 1500);
                    break;

            }

            setTimeout( function() {
                jQuery('.td_block_inner_overflow').removeClass('td_block_inner_overflow');
                elCurTdBlockInner.css('height', 'auto');

                tdSmartSidebar.compute();
            },200);

            setTimeout( function () {
                tdSmartSidebar.compute();
            }, 500);

            // the .entry-thumb are searched for in the current block object, sorted and added into the view port array items
            if ( undefined !== tdAnimationStackSortType ) {
                setTimeout( function () {
                    //console.log(current_block_obj);
                    if ( ( 'mega_menu' === td_user_action || 'back' === td_user_action || 'pull_down' === td_user_action ) && '' !== JSON.parse(tdBlocksArray[0].atts).td_ajax_preloading ){
                        tdAnimationStack.check_for_new_items('#' + current_block_obj.id + ' .td-animation-stack', tdAnimationStackSortType, true, true);
                    } else {
                        tdAnimationStack.check_for_new_items('#' + current_block_obj.id + ' .td-animation-stack', tdAnimationStackSortType, true, false);
                    }
                }, 200);
            }
        },

        /**
         * search by block _id
         * @param myID - block id
         * @returns {number} the index
         */
        tdGetBlockIndex: function(myID) {
            var cnt = 0;
            var tmpReturn = 0;
            jQuery.each(tdBlocksArray, function(index, td_block) {
                if ( td_block.id === myID ) {
                    tmpReturn = cnt;
                    return false; //brake jquery each
                } else {
                    cnt++;
                }
            });
            return tmpReturn;
        },



        /**
         * gets the block object using a block ID
         * @param myID
         * @returns {*} block object
         */
        tdGetBlockObjById: function(myID) {
            return tdBlocksArray[tdBlocks.tdGetBlockIndex(myID)];
        }





    };  //end tdBlocks



})();










/*
 td_util.js
 v1.1
 */

/* global jQuery:{} */
/* global tdDetect:{} */
/* global td_ajax_url:string */

/* global td_please_wait:string */
/* global td_email_user_pass_incorrect:string */
/* global td_email_user_incorrect:string */
/* global td_email_incorrect:string */



/*  ----------------------------------------------------------------------------
 On load
 */
jQuery().ready(function() {

    'use strict';

    /**
     * Modal window js code
     */

    var modalSettings = {
        type: 'inline',
        preloader: false,
        focus: '#name',
        removalDelay: 500,

        // When elemened is focused, some mobile browsers in some cases zoom in
        // It looks not nice, so we disable it:
        callbacks: {
            beforeOpen: function() {

                this.st.mainClass = this.st.el.attr('data-effect');

                //empty all fields
                tdLogin.clearFields();

                //empty error display div
                tdLogin.showHideMsg();

                if( jQuery( window ).width() < 700) {
                    this.st.focus = false;
                } else {
                    if ( false === tdDetect.isIe ) {
                        //do not focus on ie 10
                        this.st.focus = '#login_email';
                    }
                }
            },

            beforeClose: function() {
            }
        },

        // The modal login is disabled for widths under less than 750px
        disableOn: function() {
            if( jQuery(window).width() < 750 ) {
                return false;
            }
            return true;
        }
    };

    // this adds compatibility for cloud lib header user login/sign in shortcode
    if ( undefined !== window.tdb_login_sing_in_shortcode ) {
        // Set the modal magnific popup settings
        jQuery( '.tdb_header_user .td-login-modal-js' ).magnificPopup( modalSettings );
    }

    if ( undefined !== window.tds_login_sing_in_widget && window.tdc_is_installed === 'yes' ) {

        // The following settings are only for the modal magnific popup, which is disable when width is less than 750px
        jQuery( '.comment-reply-login' ).attr({
            'href': '#login-form',
            'data-effect': 'mpf-td-login-effect'
        });

        // Set the modal magnific popup settings
        jQuery( '.comment-reply-login, .td-login-modal-js' ).magnificPopup( modalSettings );
    }

    // - Set the normal link that will apply only for windows widths less than 750px
    // - Used for log in to leave a comment on post page to open the login section
    jQuery( '.td-login-modal-js, .comment-reply-login' ).on( 'click', function( event ) {

        if ( jQuery( window ).width() < 750 && undefined !== window.tds_login_sing_in_widget && ! event.target.parents('tdb_header_user').length ) {

            event.preventDefault();

            // open the menu background
            jQuery( 'body' ).addClass( 'td-menu-mob-open-menu' );

            // hide the menu content
            jQuery( '.td-mobile-container' ).hide();
            jQuery( '#td-mobile-nav' ).addClass( 'td-hide-menu-content' );

            setTimeout(function(){
                jQuery( '.td-mobile-container' ).show();
            }, 500);

            //hides or shows the divs with inputs
            tdLogin.showHideElementsMobile( [['#td-login-mob', 1], ['#td-register-mob', 0], ['#td-forgot-pass-mob', 0]] );
        }
    });


    //login
    jQuery( '#login-link' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLogin.showHideElements( [['#td-login-div', 1], ['#td-register-div', 0], ['#td-forgot-pass-div', 0]] );

        jQuery( '#login-form' ).addClass( 'td-login-animation' );

        if ( jQuery(window).width() > 700 && tdDetect.isIe === false ) {
            jQuery( '#login_email' ).focus();
        }

        //empty error display div
        tdLogin.showHideMsg();
    });


    //register
    jQuery( '#register-link' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLogin.showHideElements( [['#td-login-div', 0], ['#td-register-div', 1], ['#td-forgot-pass-div', 0]] );

        jQuery( '#login-form' ).addClass( 'td-login-animation' );

        if ( jQuery( window ).width() > 700  && false === tdDetect.isIe ) {
            jQuery( '#register_email' ).focus();
        }

        //empty error display div
        tdLogin.showHideMsg();
    });


    //forgot pass
    jQuery( '#forgot-pass-link' ).on( 'click', function(event) {
        //prevent scroll to page top
        event.preventDefault();
        //hides or shows the divs with inputs
        tdLogin.showHideElements( [['#td-login-div', 0], ['#td-register-div', 0], ['#td-forgot-pass-div', 1]] );

        jQuery( '#login-form' ).addClass( 'td-login-animation' );

        if (jQuery( window ).width() > 700 && false === tdDetect.isIe ) {
            jQuery( '#forgot_email' ).focus();
        }

        //empty error display div
        tdLogin.showHideMsg();
    });
    

    //login button
    jQuery( '#login_button' ).on( 'click', function() {
        tdLogin.handlerLogin();
    });

    //enter key on #login_pass
    jQuery( '#login_pass' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLogin.handlerLogin();
        }
    });


    //register button
    jQuery( '#register_button' ).on( 'click', function() {
        tdLogin.handlerRegister();
    });

    //enter key on #register_user
    jQuery( '#register_user' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLogin.handlerRegister();
        }
    });


    //forgot button
    jQuery( '#forgot_button' ).on( 'click', function() {
        tdLogin.handlerForgotPass();
    });

    //enter key on #forgot_email
    jQuery( '#forgot_email' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLogin.handlerForgotPass();
        }
    });


    // marius
    jQuery( '.td-back-button' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLogin.showHideElements( [['#td-login-div', 1], ['#td-register-div', 0], ['#td-forgot-pass-div', 0]] );

        jQuery( '#login-form' ).removeClass( 'td-login-animation' );

        // clear the error message when press back
        jQuery( '.td_display_err' ).hide();
    });
});//end jquery ready




var tdLogin = {};


(function(){

    'use strict';

    tdLogin = {

        //patern to check emails
        email_pattern : /^[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-zA-Z0-9]@[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/,

        /**
         * handle all request made from login tab
         */
        handlerLogin : function(shortcode) {
            var loginEmailEl = jQuery( '#login_email'),
                loginPassEl = jQuery( '#login_pass' );

            if ( loginEmailEl.length && loginPassEl.length ) {
                var loginEmailVal = loginEmailEl.val().trim(),
                    loginPassVal = loginPassEl.val().trim();

                if ( loginEmailVal && loginPassVal ) {
                    tdLogin.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLogin.showHideMsg( td_please_wait );

                    //call ajax for log in
                    tdLogin.doAction( 'td_mod_login', loginEmailVal, '', loginPassVal );
                } else {
                    tdLogin.showHideMsg( td_email_user_pass_incorrect );
                }
            }
        },


        /**
         * handle all request made from register tab
         */
        handlerRegister : function() {
            var registerEmailEl = jQuery( '#register_email' ),
                registerUserEl = jQuery( '#register_user' );

            if ( registerEmailEl.length && registerUserEl.length ) {
                var registerEmailVal = registerEmailEl.val().trim(),
                    registerUserVal = registerUserEl.val().trim();

                if ( tdLogin.email_pattern.test( registerEmailVal ) && registerUserVal ) {

                    tdLogin.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLogin.showHideMsg( td_please_wait );

                    //call ajax
                    tdLogin.doAction( 'td_mod_register', registerEmailVal, registerUserVal, '' );
                } else {
                    tdLogin.showHideMsg( td_email_user_incorrect );
                }
            }
        },


        /**
         * handle all request made from forgot password tab
         */
        handlerForgotPass : function() {
            var forgotEmailEl = jQuery( '#forgot_email' );

            if ( forgotEmailEl.length ) {
                var forgotEmailVal = forgotEmailEl.val().trim();

                if ( tdLogin.email_pattern.test( forgotEmailVal ) ){

                    tdLogin.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLogin.showHideMsg( td_please_wait );

                    //call ajax
                    tdLogin.doAction( 'td_mod_remember_pass', forgotEmailVal, '', '' );
                } else {
                    tdLogin.showHideMsg( td_email_incorrect );
                }
            }
        },


        /**
         * swhich the div's acordingly to the user action (Log In, Register, Remember Password)
         *
         * ids_array : array of ids that have to be showed or hidden
         */
        showHideElements : function( ids_array ) {
            if ( ids_array.constructor === Array ) {
                var length = ids_array.length;

                for ( var i = 0; i < length; i++ ) {
                    if ( ids_array[ i ].constructor === Array && 2 === ids_array[ i ].length ) {
                        var jqElement = jQuery( ids_array[ i ][0] );
                        if ( jqElement.length ) {
                            if ( 1 === ids_array[ i ][1] ) {
                                jqElement.removeClass( 'td-display-none' ).addClass( 'td-display-block' );
                            } else {
                                jqElement.removeClass( 'td-display-block' ).addClass( 'td-display-none' );
                            }
                        }
                    }
                }
            }
        },

        showHideElementsMobile : function( ids_array ) {
            if ( ids_array.constructor === Array ) {
                var length = ids_array.length;

                for ( var i = 0; i < length; i++ ) {
                    if ( ids_array[ i ].constructor === Array && 2 === ids_array[ i ].length ) {
                        var jqElement = jQuery( ids_array[ i ][0] );
                        if ( jqElement.length ) {
                            if ( 1 === ids_array[ i ][1] ) {
                                jqElement.removeClass( 'td-login-hide' ).addClass( 'td-login-show' );
                            } else {
                                jqElement.removeClass( 'td-login-show' ).addClass( 'td-login-hide' );
                            }
                        }
                    }
                }
            }
        },


        showTabs : function( ids_array ) {
            if ( ids_array.constructor === Array ) {
                var length = ids_array.length;

                for ( var i = 0; i < length; i++ ) {
                    if ( ids_array[ i ].constructor === Array && 2 === ids_array[ i ].length ) {
                        var jqElement = jQuery( ids_array[ i ][0] );
                        if ( jqElement.length ) {
                            if ( 1 === ids_array[ i ][1] ) {
                                jqElement.addClass( 'td_login_tab_focus' );
                            } else {
                                jqElement.removeClass( 'td_login_tab_focus' );
                            }
                        }
                    }
                }
            }
        },


        /**
         * adds or remove a class from an html object
         *
         * param : array with object identifier (id - # or class - .)
         * ex: ['.class_indetifier', 1, 'class_to_add'] or ['.class_indetifier', 0, 'class_to_remove']
         */
        addRemoveClass : function( param ) {
            if ( param.constructor === Array && 3 === param.length ) {
                var tdElement = jQuery( param[0] );
                if ( tdElement.length ) {
                    if ( 1 === param[1] ) {
                        tdElement.addClass( param[2] );
                    } else {
                        tdElement.removeClass( param[2] );
                    }
                }
            }
        },


        showHideMsg : function( msg ) {
            var tdDisplayErr = jQuery( '.td_display_err' );
            if ( tdDisplayErr.length ) {
                if ( undefined !== msg && msg.constructor === String && msg.length > 0 ) {
                    tdDisplayErr.show();
                    tdDisplayErr.html( msg );
                } else {
                    tdDisplayErr.hide();
                    tdDisplayErr.html( '' );
                }
            }
        },


        /**
         * empty all fields in modal window
         */
        clearFields : function() {
            //login fields
            jQuery( '#login_email' ).val( '' );
            jQuery( '#login_pass' ).val( '' );

            //register fields
            jQuery( '#register_email' ).val( '' );
            jQuery( '#register_user' ).val( '' );

            //forgot pass
            jQuery( '#forgot_email' ).val( '' );
        },


        /**
         * call to server from modal window
         *
         * @param $action : what action (log in, register, forgot email)
         * @param $email  : the email beening sent
         * @param $user   : the user name beening sent
         */
        doAction : function( sent_action, sent_email, sent_user, sent_pass ) {
            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                data: {
                    action: sent_action,
                    email: sent_email,
                    user: sent_user,
                    pass: sent_pass
                },
                success: function( data, textStatus, XMLHttpRequest ){
                    var td_data_object = jQuery.parseJSON( data ); //get the data object

                    //check the response from server
                    switch( td_data_object[0] ) {
                        case 'login':
                            if ( 1 === td_data_object[1] ) {
                                location.reload( true );
                            } else {
                                tdLogin.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                                tdLogin.showHideMsg( td_data_object[2] );
                            }
                            break;

                        case 'register':
                            if ( 1 === td_data_object[1] ) {
                                tdLogin.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                            } else {
                                tdLogin.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                            }
                            tdLogin.showHideMsg( td_data_object[2] );
                            break;

                        case 'remember_pass':
                            if ( 1 === td_data_object[1] ) {
                                tdLogin.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                            } else {
                                tdLogin.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                            }
                            tdLogin.showHideMsg( td_data_object[2] );
                            break;
                    }
                },
                error: function( MLHttpRequest, textStatus, errorThrown ){
                    //console.log(errorThrown);
                }
            });
        }
    };

})();
/*
 td_util.js
 v1.1
 */

/* global jQuery:{} */
/* global tdDetect:{} */
/* global td_ajax_url:string */

/* global td_please_wait:string */
/* global td_email_user_pass_incorrect:string */
/* global td_email_user_incorrect:string */
/* global td_email_incorrect:string */



/*  ----------------------------------------------------------------------------
 On load
 */
jQuery().ready(function() {

    'use strict';

    //login
    jQuery( '#login-link-mob' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLoginMob.showHideElements( [['#td-login-mob', 1], ['#td-register-mob', 0], ['#td-forgot-pass-mob', 0]] );

        jQuery( '#td-mobile-nav' ).addClass( 'td-hide-menu-content' );

        if ( jQuery(window).width() > 700 && tdDetect.isIe === false ) {
            jQuery( '#login_email-mob' ).focus();
        }

        //empty error display div
        tdLoginMob.showHideMsg();
    });

    //register
    jQuery( '#register-link-mob' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLoginMob.showHideElements( [['#td-login-mob', 0], ['#td-register-mob', 1], ['#td-forgot-pass-mob', 0]] );

        jQuery( '#td-mobile-nav' ).addClass( 'td-hide-menu-content' );

        if ( jQuery( window ).width() > 700  && false === tdDetect.isIe ) {
            jQuery( '#register_email-mob' ).focus();
        }

        //empty error display div
        tdLoginMob.showHideMsg();
    });

    //forgot pass
    jQuery( '#forgot-pass-link-mob' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLoginMob.showHideElements( [['#td-login-mob', 0], ['#td-register-mob', 0], ['#td-forgot-pass-mob', 1]] );

        if (jQuery( window ).width() > 700 && false === tdDetect.isIe ) {
            jQuery( '#forgot_email-mob' ).focus();
        }

        //empty error display div
        tdLoginMob.showHideMsg();
    });


    //login button
    jQuery( '#login_button-mob' ).on( 'click', function() {
        tdLoginMob.handlerLogin();
    });

    //enter key on #login_pass
    jQuery( '#login_pass-mob' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLoginMob.handlerLogin();
        }
    });


    //register button
    jQuery( '#register_button-mob' ).on( 'click', function() {
        tdLoginMob.handlerRegister();
    });

    //enter key on #register_user
    jQuery( '#register_user-mob' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLoginMob.handlerRegister();
        }
    });


    //forgot button
    jQuery( '#forgot_button-mob' ).on( 'click', function() {
        tdLoginMob.handlerForgotPass();
    });

    //enter key on #forgot_email
    jQuery( '#forgot_email-mob' ).keydown(function(event) {
        if ( ( event.which && 13 === event.which ) || ( event.keyCode && 13 === event.keyCode ) ) {
            tdLoginMob.handlerForgotPass();
        }
    });


    // marius
    // *****************************************************************************
    // *****************************************************************************
    // back login/register button
    jQuery( '#td-mobile-nav .td-login-close a, #td-mobile-nav .td-register-close a' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLoginMob.showHideElements( [['#td-login-mob', 0], ['#td-register-mob', 0], ['#td-forgot-pass-mob', 0]] );

        jQuery( '#td-mobile-nav' ).removeClass( 'td-hide-menu-content' );
    });

    // back forgot pass button
    jQuery( '#td-mobile-nav .td-forgot-pass-close a' ).on( 'click', function() {
        //hides or shows the divs with inputs
        tdLoginMob.showHideElements( [['#td-login-mob', 1], ['#td-register-mob', 0], ['#td-forgot-pass-mob', 0]] );
    });

});//end jquery ready





var tdLoginMob = {};


(function(){

    'use strict';

    tdLoginMob = {

        //patern to check emails
        email_pattern : /^[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-zA-Z0-9]@[a-zA-Z0-9][a-zA-Z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/,

        /**
         * handle all request made from login tab
         */
        handlerLogin : function() {
            var loginEmailEl = jQuery( '#login_email-mob'),
                loginPassEl = jQuery( '#login_pass-mob' );

            if ( loginEmailEl.length && loginPassEl.length ) {
                var loginEmailVal = loginEmailEl.val().trim(),
                    loginPassVal = loginPassEl.val().trim();

                if ( loginEmailVal && loginPassVal ) {
                    tdLoginMob.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLoginMob.showHideMsg( td_please_wait );

                    //call ajax for log in
                    tdLoginMob.doAction( 'td_mod_login', loginEmailVal, '', loginPassVal );
                } else {
                    tdLoginMob.showHideMsg( td_email_user_pass_incorrect );
                }
            }
        },


        /**
         * handle all request made from register tab
         */
        handlerRegister : function() {
            var registerEmailEl = jQuery( '#register_email-mob' ),
                registerUserEl = jQuery( '#register_user-mob' );

            if ( registerEmailEl.length && registerUserEl.length ) {
                var registerEmailVal = registerEmailEl.val().trim(),
                    registerUserVal = registerUserEl.val().trim();

                if ( tdLoginMob.email_pattern.test( registerEmailVal ) && registerUserVal ) {

                    tdLoginMob.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLoginMob.showHideMsg( td_please_wait );

                    //call ajax
                    tdLoginMob.doAction( 'td_mod_register', registerEmailVal, registerUserVal, '' );
                } else {
                    tdLoginMob.showHideMsg( td_email_user_incorrect );
                }
            }
        },


        /**
         * handle all request made from forgot password tab
         */
        handlerForgotPass : function() {
            var forgotEmailEl = jQuery( '#forgot_email-mob' );

            if ( forgotEmailEl.length ) {
                var forgotEmailVal = forgotEmailEl.val().trim();

                if ( tdLoginMob.email_pattern.test( forgotEmailVal ) ){

                    tdLoginMob.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                    tdLoginMob.showHideMsg( td_please_wait );

                    //call ajax
                    tdLoginMob.doAction( 'td_mod_remember_pass', forgotEmailVal, '', '' );
                } else {
                    tdLoginMob.showHideMsg( td_email_incorrect );
                }
            }
        },


        /**
         * swhich the div's acordingly to the user action (Log In, Register, Remember Password)
         *
         * ids_array : array of ids that have to be showed or hidden
         */
        showHideElements : function( ids_array ) {
            if ( ids_array.constructor === Array ) {
                var length = ids_array.length;

                for ( var i = 0; i < length; i++ ) {
                    if ( ids_array[ i ].constructor === Array && 2 === ids_array[ i ].length ) {
                        var jqElement = jQuery( ids_array[ i ][0] );
                        if ( jqElement.length ) {
                            if ( 1 === ids_array[ i ][1] ) {
                                jqElement.removeClass( 'td-login-hide' ).addClass( 'td-login-show' );
                            } else {
                                jqElement.removeClass( 'td-login-show' ).addClass( 'td-login-hide' );
                            }
                        }
                    }
                }
            }
        },


        /**
         * adds or remove a class from an html object
         *
         * param : array with object identifier (id - # or class - .)
         * ex: ['.class_indetifier', 1, 'class_to_add'] or ['.class_indetifier', 0, 'class_to_remove']
         */
        addRemoveClass : function( param ) {
            if ( param.constructor === Array && 3 === param.length ) {
                var jqElement = jQuery( param[0] );
                if ( jqElement.length ) {
                    if ( 1 === param[1] ) {
                        jqElement.addClass( param[2] );
                    } else {
                        jqElement.removeClass( param[2] );
                    }
                }
            }
        },


        showHideMsg : function( msg ) {
            var tdDisplayErr = jQuery( '.td_display_err' );
            if ( tdDisplayErr.length ) {
                if ( undefined !== msg && msg.constructor === String && msg.length > 0 ) {
                    tdDisplayErr.show();
                    tdDisplayErr.html( msg );
                } else {
                    tdDisplayErr.hide();
                    tdDisplayErr.html( '' );
                }
            }
        },


        /**
         * empty all fields in modal window
         */
        clearFields : function() {
            //login fields
            jQuery( '#login_email-mob' ).val( '' );
            jQuery( '#login_pass-mob' ).val( '' );

            //register fields
            jQuery( '#register_email-mob' ).val( '' );
            jQuery( '#register_user-mob' ).val( '' );

            //forgot pass
            jQuery( '#forgot_email-mob' ).val( '' );
        },


        /**
         * call to server from modal window
         *
         * @param $action : what action (log in, register, forgot email)
         * @param $email  : the email beening sent
         * @param $user   : the user name beening sent
         */
        doAction : function( sent_action, sent_email, sent_user, sent_pass ) {
            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                data: {
                    action: sent_action,
                    email: sent_email,
                    user: sent_user,
                    pass: sent_pass
                },
                success: function( data, textStatus, XMLHttpRequest ){
                    var td_data_object = jQuery.parseJSON( data ); //get the data object

                    //check the response from server
                    switch( td_data_object[0] ) {
                        case 'login':
                            if ( 1 === td_data_object[1] ) {
                                location.reload( true );
                            } else {
                                tdLoginMob.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                                tdLoginMob.showHideMsg( td_data_object[2] );
                            }
                            break;

                        case 'register':
                            if ( 1 === td_data_object[1] ) {
                                tdLoginMob.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                            } else {
                                tdLoginMob.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                            }
                            tdLoginMob.showHideMsg( td_data_object[2] );
                            break;

                        case 'remember_pass':
                            if ( 1 === td_data_object[1] ) {
                                tdLoginMob.addRemoveClass( ['.td_display_err', 1, 'td_display_msg_ok'] );
                            } else {
                                tdLoginMob.addRemoveClass( ['.td_display_err', 0, 'td_display_msg_ok'] );
                            }
                            tdLoginMob.showHideMsg( td_data_object[2] );
                            break;
                    }
                },
                error: function( MLHttpRequest, textStatus, errorThrown ){
                    //console.log(errorThrown);
                }
            });
        }
    };

})();



/*  ----------------------------------------------------------------------------
 tagDiv live css compiler ( 2013 )
 - this script is used on our demo site to customize the theme live
 - not used on production sites
 */

/* global jQuery:{} */
/* global td_read_site_cookie:Function */
/* global td_set_cookies_life:Function */
/* global tdDetect: {} */

var tdDemoMenu;

(function(jQuery, undefined) {

    'use strict';

    tdDemoMenu = {

        // document - horizontal mouse position
        mousePosX: 0,

        // document - vertical mouse position
        mousePosY: 0,





        init: function () {

            // Get document mouse position
            jQuery(document).mousemove(function (event) {
                if (event.pageX || event.pageY) {
                    tdDemoMenu.mousePosX = event.pageX;
                    tdDemoMenu.mousePosY = event.pageY;
                } else if (event.clientX || event.clientY) {
                    tdDemoMenu.mousePosX = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                    tdDemoMenu.mousePosY = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                }
            });


            // cloase the preview on mouse leave
            jQuery(document).mouseleave(function (event) {

                jQuery('.td-screen-demo:first').css('visibility', 'hidden');
            });

            // Show/hide the arrow skin scroll element
            jQuery('#td-theme-settings').find('.td-skin-wrap:first').scroll(function (event) {
                //console.log( event );

                var theTarget = event.currentTarget,
                    tdSkinScroll = jQuery(this).find('.td-skin-scroll:first');

                if (theTarget.clientHeight + theTarget.scrollTop < theTarget.scrollHeight) {
                    tdSkinScroll.css({
                        bottom: 0
                    });
                } else {
                    tdSkinScroll.css({
                        bottom: -40
                    });
                }
            });

            jQuery('#td-theme-settings').find('.td-skin-scroll:first').click(function (event) {
                //console.log( event );

                var theTarget = event.currentTarget,
                    tdSkinWrap = jQuery(this).closest('.td-skin-wrap');

                tdSkinWrap.animate(
                    {scrollTop: tdSkinWrap.scrollTop() + 200},
                    {
                        duration: 800,
                        easing: 'easeInOutQuart'
                    });
            }).mouseenter(function (event) {

                //jQuery( '#td-theme-settings' ).find( '.td-screen-demo:first' ).hide();
                jQuery('#td-theme-settings').find('.td-screen-demo:first').css('visibility', 'hidden');
            });




            jQuery('.td-set-theme-style-link').hover(

                // The mouse enter event handler
                function (event) {

                //console.log( 'in MAIN ' + contor++);

                    var
                    // The css class of the container element
                        cssClassContainer = 'td-set-theme-style',

                    // The jquery object of the current element
                        $this = jQuery(this),

                    // The jquery object of the container of the current element
                        $thisContainer = $this.closest('.' + cssClassContainer),

                    // The demo previewer
                        jQueryDisplayEl = jQuery('.td-screen-demo:first'),

                    // The ref top value considers the existing of the wpadminbar element
                        refTopValue = 0,

                    // The top value set to the css top setting
                        topValue = 0,

                    // The left value set to the css left setting
                        rightValue = 0,

                    // The padding value set to the css padding-left setting
                        paddingRightValue = 0,

                    // The extra value added to the css padding-left setting and removed from the css left setting (if we need to start earlier or later - does nothing with 0 value)
                        extraRightValue = 0,

                    // The jquery wpadminbar element
                        jqWPAdminBar = jQuery('#wpadminbar');



                    // Show the image into the image previewer
                    var imgElement = jQueryDisplayEl.find('img:first'),
                        dataImgUrl = $this.data('img-url');

                    if (imgElement.length) {
                        imgElement.replaceWith( '<img src="' + dataImgUrl + '"/>' );
                        //imgElement.attr('src', dataImgUrl);
                    } else {
                        jQueryDisplayEl.html('<img src="' + dataImgUrl + '"/>');
                    }


                    // The first column
                    if ( 0 === jQuery( '.td-set-theme-style-link' ).index( this ) % 2 ) {
                        rightValue = $thisContainer.outerWidth(true) * 2;

                        // The second column
                    } else {
                        var $thisPrevContainer = $thisContainer.prev('.' + cssClassContainer);

                        if ($thisPrevContainer.length) {
                            rightValue = $thisPrevContainer.outerWidth(true) - extraRightValue;
                            paddingRightValue = $thisPrevContainer.outerWidth(true) + extraRightValue;

                        }
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    topValue = event.pageY - document.body.scrollTop - ( jQueryDisplayEl.outerHeight( true ) / 2 );

                    // Do not allow displaying the previewer demo below the bottom of the window screen
                    if (topValue + jQueryDisplayEl.outerHeight(true) > window.innerHeight) {
                        topValue -= (topValue + jQueryDisplayEl.outerHeight(true)) - window.innerHeight;
                    }

                    // Do not allow displaying the previewer demo above the top of the window screen. It also checks if the wpadminbar exists.
                    if (jqWPAdminBar.length) {
                        refTopValue = jqWPAdminBar.outerHeight(true);
                    } else {
                        refTopValue = 0;
                    }

                    if (refTopValue > topValue) {
                        topValue = refTopValue;
                    }

                    // The 'width' css property is used for Chrome and IE browsers which do not display the previewer image with auto width and auto height
                    var cssSettings = {
                            'top': topValue,
                            'right': rightValue,
                            //'padding-right': paddingRightValue,
                            'width': ''
                        },
                        dataWidthPreview = jQueryDisplayEl.data('width-preview');


                    // For the first column of demos, the previewer has padding
                    if (paddingRightValue > 0) {
                        cssSettings.width = dataWidthPreview + paddingRightValue;
                    }




                    // Apply the computed css to the element
                    jQueryDisplayEl.css(cssSettings);

                    // The 'right-value' data will be used to set 'right' css value when the computed padding is < 0
                    jQueryDisplayEl.data('right-value', rightValue + paddingRightValue);

                    //jQueryDisplayEl.show();
                    jQueryDisplayEl.css('visibility', 'visible');
                },

                // The mouse exit event handler
                function (event) {

                    //console.log('out MAIN ');

                    var
                    // The jquery object of the previewer demo element
                        jQueryDisplayEl = jQuery('.td-screen-demo:first');

                    //jQueryDisplayEl.hide();
                    jQueryDisplayEl.css('visibility', 'hidden');
                }

            ).mousemove(function(event) {
                tdDemoMenu._moveScreenDemo( event );
            });
        },




        /**
         * Position the '.td-screen-demo' element according to the mouse position
         *
         * @param event - mouse move
         * @private
         */
        _moveScreenDemo: function( event ) {
            var
            // The jquery object of the previewer demo element
                $screenDemo = jQuery( '.td-screen-demo:first' ),

                $WPAdminBar = jQuery( '#wpadminbar' ),

            // new top value
                newTopValue = event.pageY - document.body.scrollTop - ( $screenDemo.outerHeight( true ) / 2 ),

            // The reference top value used when #wpadminbar is enabled
                refTopValue = 0;

            if ( $WPAdminBar.length ) {
                refTopValue = $WPAdminBar.outerHeight(true);
            } else {
                refTopValue = 0;
            }

            if ( refTopValue > newTopValue ) {
                newTopValue = refTopValue;
            }


            if ( newTopValue < 0 ) {
                newTopValue = 0;
            } else if ( jQuery( window ).height() - $screenDemo.outerHeight( true ) / 2 < event.pageY - document.body.scrollTop ) {
                newTopValue = jQuery( window ).height() - $screenDemo.outerHeight( true );
            }

            $screenDemo.css( 'top', newTopValue );
        },







        _checkMousePosition: function () {

            var theElement;

            jQuery('.td-set-theme-style-link').each(function (index, element) {

                tdDemoMenu._log(index);

                var $this = jQuery(element),
                    cssClassContainer = 'td-set-theme-style',
                    $thisContainer = $this.closest('.' + cssClassContainer);

                var verticalPosition = false;
                var horizontalPosition = false;

                if (0 === jQuery('.td-set-theme-style-link').index(element) % 2) {

                    if (parseInt($thisContainer.position().top) + parseInt(jQuery(window).scrollTop()) < tdDemoMenu.mousePosY && tdDemoMenu.mousePosY < parseInt($thisContainer.position().top) + parseInt(jQuery(window).scrollTop()) + parseInt($thisContainer.outerHeight())) {
                        verticalPosition = true;

                        if (parseInt(jQuery(window).width()) - 2 * parseInt($thisContainer.outerWidth()) < tdDemoMenu.mousePosX && tdDemoMenu.mousePosX < parseInt(jQuery(window).width()) - parseInt($thisContainer.outerWidth())) {
                            horizontalPosition = true;
                        }
                    }
                    //tdDemoMenu._log( 'caz A : ' + index + ' > vert: ' + verticalPosition + ' > hori: ' + horizontalPosition + ' > posY: ' + tdDemoMenu.mousePosY + ' > posX: ' + tdDemoMenu.mousePosX +
                    //    ' > top: ' + (parseInt($thisContainer.position().top) + parseInt(jQuery(window).scrollTop())) + ' > bottom: ' + (parseInt($thisContainer.position().top) + parseInt(jQuery(window).scrollTop()) + parseInt($thisContainer.outerHeight())) +
                    //    ' > left: ' + (parseInt(jQuery( window ).width()) - 2 * parseInt($thisContainer.outerWidth())) + ' > right: ' + (parseInt(jQuery( window ).width()) - parseInt($thisContainer.outerWidth())) );

                } else {
                    var $thisPrevContainer = $thisContainer.prev('.' + cssClassContainer);

                    if ($thisPrevContainer.length) {
                        if (parseInt($thisPrevContainer.position().top) + parseInt(jQuery(window).scrollTop()) < tdDemoMenu.mousePosY && tdDemoMenu.mousePosY < (parseInt($thisPrevContainer.position().top) + parseInt(jQuery(window).scrollTop()) + parseInt($thisPrevContainer.outerHeight()))) {
                            verticalPosition = true;

                            if (parseInt(jQuery(window).width()) - parseInt($thisContainer.outerWidth()) < tdDemoMenu.mousePosX && tdDemoMenu.mousePosX < parseInt(jQuery(window).width())) {
                                horizontalPosition = true;
                            }
                        }
                    }
                    //tdDemoMenu._log( 'caz B : ' + index + ' > vert: ' + verticalPosition + ' > hori: ' + horizontalPosition + ' > posY: ' + tdDemoMenu.mousePosY + ' > posX: ' + tdDemoMenu.mousePosX +
                    //    ' > top: ' + ($thisPrevContainer.position().top + parseInt(jQuery(window).scrollTop())) + ' > bottom: ' + (parseInt($thisPrevContainer.position().top) + parseInt(jQuery(window).scrollTop()) + parseInt($thisPrevContainer.outerHeight())) +
                    //    ' > left: ' + (parseInt(jQuery( window ).width()) - parseInt($thisContainer.outerWidth())) + ' > right: ' + parseInt(jQuery( window ).width()) );
                }

                // The element where the mouse is positioned, was found
                if (verticalPosition && horizontalPosition) {
                    theElement = element;
                    return false;
                }

            });

            if (undefined === theElement) {
                //jQuery( '#td-theme-settings').find( '.td-screen-demo:first' ).hide();
                jQuery('#td-theme-settings').find('.td-screen-demo:first').css('visibility', 'hidden');
            } else {
                jQuery(theElement).mouseenter();
            }
        },

        _log: function (msg) {

            //window.console.log( msg );
        }

    };

})( jQuery );


/**
 * show the panel if the cookie is set
 */
// (function() {
//     'use strict';
//     var td_current_panel_stat = td_read_site_cookie( 'td_show_panel' );
//     if ( 'hide' === td_current_panel_stat ) {
//         var jQueryObj = jQuery( '#td-theme-settings' );
//         if ( jQueryObj.length ) {
//             jQueryObj.removeClass( 'td-theme-settings-small' );
//             jQuery( '#td-theme-set-hide' ).html( 'DEMOS' );
//         }
//     } else {
//         jQuery( '#td-theme-set-hide' ).html( 'CLOSE');
//     }
//
// })();





/*  ----------------------------------------------------------------------------
 On load
 */
// jQuery().ready(function() {
//
//     'use strict';
//
//     // do not run on iOS
//     if (tdDetect.isIos === false && tdDetect.isAndroid === false) {
//         tdDemoMenu.init();
//     }
//
//
//
//     // Show/hide the demo menu panel
//     jQuery( '#td-theme-set-hide' ).on( 'click', function(event) {
//         event.preventDefault();
//         event.stopPropagation();
//
//         var $this = jQuery(this),
//             jQueryObj = jQuery( '#td-theme-settings' );
//
//         if ( jQueryObj.hasClass( 'td-theme-settings-small' ) ) {
//             // close
//             jQueryObj.removeClass( 'td-theme-settings-small' );
//             jQueryObj.addClass( 'td-theme-settings-closed' );
//             $this.html( 'DEMOS' );
//
//             setTimeout(function(){
//                 jQueryObj.addClass( 'td-ts-closed-no-transition' ); // add the remove transition class after the animation has finished
//             }, 450);
//
//             td_set_cookies_life( ['td_show_panel', 'hide', 86400000] );//86400000 is the number of milliseconds in a day
//
//
//         } else {
//             // open
//             jQueryObj.removeClass( 'td-ts-closed-no-transition' ); // remove the remove transition class :)
//
//
//             jQueryObj.addClass( 'td-theme-settings-small' );
//             jQueryObj.removeClass( 'td-theme-settings-closed' );
//             $this.html( 'CLOSE' );
//             td_set_cookies_life( ['td_show_panel', 'show', 86400000] );//86400000 is the number of milliseconds in a day
//         }
//     });
//
//
// }); 
//end on load
/**
 * Created by RADU on 6/24/14.
 */

/* global jQuery: {} */

var tdTrendingNow = {};

(function() {

    "use strict";

    tdTrendingNow = {

        // - the list of items
        items: [],

        // - trending now item
        item: function item() {
            //the block Unique id
            this.blockUid = '';
            //autostart
            this.trendingNowAutostart = 'manual';
            //autostart timer
            this.trendingNowTimer = 0;
            //slider position
            this.trendingNowPosition = 0;
            //posts list
            this.trendingNowPosts = [];
            // flag used to mark the initialization item
            this._is_initialized = false;
        },

        //function used to init tdTrendingNow
        init: function() {
            tdTrendingNow.items = [];
        },

        //internal utility function used to initialize an item
        _initialize_item: function( item ) {
            // an item must be initialized only once
            if ( true === item._is_initialized ) {
                return;
            }
            // the item is marked as initialized
            item._is_initialized = true;
        },

        //add an item
        addItem: function( item ) {

            //todo - add some checks on item
            // check to see if the item is ok
            if (typeof item.blockUid === 'undefined') {
                throw 'item.blockUid is not valid';
            }
            if (typeof item.trendingNowPosts === 'undefined' || item.trendingNowPosts.length < 1) {
                throw 'item.trendingNowPosts is not valid';
            }

            // the item is added in the items list
            tdTrendingNow.items.push( item );

            // the item is initialized only once when it is added
            tdTrendingNow._initialize_item( item );

            //autostart
            tdTrendingNow.tdTrendingNowAutoStart(item.blockUid);
        },

        //deletes an item base on blockUid
        deleteItem: function( blockUid ) {
            for (var cnt = 0; cnt < tdTrendingNow.items.length; cnt++) {
                if (tdTrendingNow.items[cnt].blockUid === blockUid) {
                    tdTrendingNow.items.splice(cnt, 1); // remove the item from the "array"
                    return true;
                }
            }
            return false;
        },

        //switch to the previous item
        itemPrev: function( blockUid ) {
            //current item
            var i, currentItem;
            //get current item
            for (var cnt = 0; cnt < tdTrendingNow.items.length; cnt++) {
                if (tdTrendingNow.items[cnt].blockUid === blockUid) {
                    currentItem = tdTrendingNow.items[cnt];
                }
            }

            // if there's just a single post to be shown, there's no need for next/prev/autostart
            if ((blockUid !== undefined) && (1 >= currentItem.trendingNowPosts.length))  {
                return;
            }

            /**
             * used when the trending now block is used on auto mod and we click on show prev or show next article title
             * this will make the auto mode wait another xx seconds before displaying the next article title
             */
            if ('manual' !== currentItem.trendingNowAutostart) {
                clearInterval(currentItem.trendingNowTimer);
                currentItem.trendingNowTimer = setInterval(function () {
                    tdTrendingNow.tdTrendingNowChangeText([blockUid, 'left'], true);
                }, 3000);
            }

            //call to change the text
            tdTrendingNow.tdTrendingNowChangeText([blockUid, 'right'], false);
        },

        //switch to the next item
        itemNext: function ( blockUid ) {
            //current item
            var i, currentItem;
            //get current item
            for (var cnt = 0; cnt < tdTrendingNow.items.length; cnt++) {
                if (tdTrendingNow.items[cnt].blockUid === blockUid) {
                    currentItem = tdTrendingNow.items[cnt];
                }
            }

            // if there's just a single post to be shown, there's no need for next/prev/autostart
            if ((blockUid !== undefined) && (1 >= currentItem.trendingNowPosts.length))  {
                return;
            }

            /**
             * used when the trending now block is used on auto mod and we click on show prev or show next article title
             * this will make the auto mode wait another xx seconds before displaying the next article title
             */
            if ('manual' !== currentItem.trendingNowAutostart) {
                clearInterval(currentItem.trendingNowTimer);
                currentItem.trendingNowTimer = setInterval(function () {
                    tdTrendingNow.tdTrendingNowChangeText([blockUid, 'left'], true);
                }, 3000);
            }

            //call to change the text
            tdTrendingNow.tdTrendingNowChangeText([blockUid, 'left'], true);
        },

        /*
         function for changing the posts in `trending now` display area
         *
         *array_param[0] : the id of current `trending now wrapper`
         *array_param[1] : moving direction (left or right)
         */
        tdTrendingNowChangeText: function(array_param, to_right) {

            //for consistency use the same variables names as thh parent function
            var blockUid = array_param[0],
                movingDirection = array_param[1],
                postsArrayListForThisTrend = [],
                postsArrayListPosition = 0,
                itemPosition;

            for (var cnt = 0; cnt < tdTrendingNow.items.length; cnt++) {
                if (tdTrendingNow.items[cnt].blockUid === blockUid) {
                    itemPosition = cnt;
                    postsArrayListForThisTrend = tdTrendingNow.items[cnt].trendingNowPosts;
                    postsArrayListPosition = tdTrendingNow.items[cnt].trendingNowPosition;
                }
            }
            
            if (typeof itemPosition !== 'undefined' && itemPosition !== null) {
                var previousPostArrayListPosition = postsArrayListPosition,
                    post_count = postsArrayListForThisTrend.length - 1;//count how many post are in the list

                if ( post_count < 1 ) {
                    return;
                }

                if ('left' === movingDirection) {
                    postsArrayListPosition += 1;

                    if (postsArrayListPosition > post_count) {
                        postsArrayListPosition = 0;
                    }

                } else {
                    postsArrayListPosition -= 1;

                    if (postsArrayListPosition < 0) {
                        postsArrayListPosition = post_count;
                    }
                }

                //update the new position in the global `tdTrendingNow`
                tdTrendingNow.items[itemPosition].trendingNowPosition = postsArrayListPosition;

                postsArrayListForThisTrend[previousPostArrayListPosition].css('opacity', 0);
                postsArrayListForThisTrend[previousPostArrayListPosition].css('z-index', 0);

                for (var trending_post in postsArrayListForThisTrend) {
                    if (true === postsArrayListForThisTrend.hasOwnProperty(trending_post)) {
                        postsArrayListForThisTrend[trending_post].removeClass('td_animated_xlong td_fadeInLeft td_fadeInRight td_fadeOutLeft td_fadeOutRight');
                    }
                }

                postsArrayListForThisTrend[postsArrayListPosition].css('opacity', 1);
                postsArrayListForThisTrend[postsArrayListPosition].css('z-index', 1);

                if (true === to_right) {

                    postsArrayListForThisTrend[previousPostArrayListPosition].addClass('td_animated_xlong td_fadeOutLeft');
                    postsArrayListForThisTrend[postsArrayListPosition].addClass('td_animated_xlong td_fadeInRight');
                } else {

                    postsArrayListForThisTrend[previousPostArrayListPosition].addClass('td_animated_xlong td_fadeOutRight');
                    postsArrayListForThisTrend[postsArrayListPosition].addClass('td_animated_xlong td_fadeInLeft');
                }
            }
        },

        //trending now function to auto start
        tdTrendingNowAutoStart: function(blockUid) {
            for (var cnt = 0; cnt < tdTrendingNow.items.length; cnt++) {
                // if there's just a single post to be shown, there's no need for next/prev/autostart
                if (tdTrendingNow.items[cnt].blockUid === blockUid && tdTrendingNow.items[cnt].trendingNowAutostart !== 'manual') {
                    tdTrendingNow.items[cnt].trendingNowTimer = tdTrendingNow.setTimerChangingText(blockUid);
                }
            }
        },

        setTimerChangingText: function( blockUid ) {
            return setInterval(function () {
                //console.log(i + "=>" + list[i] + "\n");
                tdTrendingNow.tdTrendingNowChangeText([blockUid, 'left'], true);
            }, 3000);
        }

    };

    tdTrendingNow.init();

})();
"use strict";


/*  ----------------------------------------------------------------------------
    history js
 */

var td_history = {
    td_history_change_event: false,

    // static class init
    init: function() {
        //hook the popstate event
        window.addEventListener('popstate', function(event) {
            td_history.td_history_change_event = true;
            if (typeof(event.state) != "undefined" && event.state != null) {
                jQuery("#" + event.state.slide_id).iosSlider("goToSlide", event.state.current_slide);
            }
        });
    },


    /**
     * generally used on load
     * @param data
     */
    replace_history_entry: function (data) {
        if (tdDetect.hasHistory === false) {
            return; //no history support
        }
        history.replaceState(data, null);
    },


    /**
     * ads an history entry - it also knows if we are using mod rewrite or not
     * @param data - the history data (state)
     * @param query_parm_id - 'slide' or other
     * @param query_parm_value - the value for slide
     */
    add_history_entry: function (data, query_parm_id, query_parm_value) {

        if (tdDetect.hasHistory === false) {
            return; //no history support
        }


        if (query_parm_value == '') {
            history.pushState(data, null,  null); //add the hash via history api
            return;
        }

        // !!!! - detect other types of pages ex: ?page_id
        var td_query_page_id = td_history.get_query_parameter('p');
        if (td_query_page_id != '') {
            //no mod rewrite, we go with ?p= etc
            if (query_parm_value == 1) {
                history.pushState(data, null,  '?p=' + td_query_page_id); //remove the parm for the first item
            } else {
                history.pushState(data, null,  '?p=' + td_query_page_id + '&' + query_parm_id + '=' + query_parm_value); //add the hash via history api
            }

        } else {
            //mod rewrite
            if (query_parm_value == 1) {
                history.pushState(data, null, td_history.get_mod_rewrite_base_url()); //add the hash via history api
            } else {
                history.pushState(data, null, td_history.get_mod_rewrite_base_url() + query_parm_value + '/'); //add the hash via history api
            }
        }

    },



    /**
     * returns the base url of urls with mod rewrite + pagination
     * @returns {string}
     */
    get_mod_rewrite_base_url: function () {
        var full_url = document.URL;

        //trim the last "/" in the url
        if (full_url.charAt(full_url.length - 1) == '/') {
            full_url = full_url.slice(0, - 1);
        }

        if (td_history.get_mod_rewrite_pagination(document.URL) === false) {
            // no pagination present
            return document.URL;
        }

        // we have pagination so we have to parse the url to remove it
        return full_url.substring(0, full_url.lastIndexOf("/"))+ '/';

    },



    /**
     * get the pagination from the urls with mod rewrite on
     * @returns {*}
     */
    get_mod_rewrite_pagination: function () {
        var full_url = document.URL;

        //trim the last "/" in the url
        if (full_url.charAt(full_url.length - 1) == '/') {
            full_url = full_url.slice(0, - 1);
        }

        var last_url_parameter = full_url.substring(full_url.lastIndexOf("/")+1, full_url.length);

        // return the page if it's indeed an integer
        if (td_history.isInt(last_url_parameter)) {
            return last_url_parameter;
        }

        //return false if we don't have a page
        return false;
    },


    /**
     * used by the iosslider @startAtSlide, it return 1 if there is no pagination or returns the pagination
     * @param query_parm_id
     * @returns {*}
     */
    get_current_page: function (query_parm_id) {
        var td_query_page_id = td_history.get_query_parameter('p');
        if (td_query_page_id != '') {
            //no mod rewrite, we go with ?p= etc
            var cur_page = td_history.get_query_parameter(query_parm_id);
            if (cur_page != '') {
                return cur_page;
            } else {
                return 1;
            }
        } else {
            //mod rewrite
            var cur_page = td_history.get_mod_rewrite_pagination();
            if (cur_page !== false) {
                return cur_page;
            } else {
                return 1;
            }
        }
    },


    /**
     * used to check if a number is an integer
     * @param n
     * @returns {boolean}
     */
    isInt: function (n) {
        return n % 1 === 0;
    },


    /**
     * returns a query parameter from the current url - we use it for ?p=
     * @param name
     * @returns {string}
     */
    get_query_parameter: function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    },

    /**
     * callback for slides with history
     * @param args
     */
    slide_changed_callback: function(args) {
        //do not add another history entry if the slide moved due to the history change event
        if (td_history.td_history_change_event === true) {
            td_history.td_history_change_event = false;
            return;
        }
        var current_slide = args.currentSlideNumber;
        var slide_id = args.sliderContainerObject.attr('id');

        td_history.add_history_entry({current_slide:current_slide, slide_id:slide_id}, 'slide', current_slide);
    }

};

/**
 * ie8 does not have pushState and history
 */
if (window.history && window.history.pushState) {
    td_history.init();
}

/**
 * @depends on:
 * td_util
 * td_events
 * tdAffix
 */

/* global jQuery:{} */
/* global tdUtil:{} */
/* global tdViewport:{} */
/* global tdAffix:{} */


var tdSmartSidebar = {};

(function(){

    'use strict';

    tdSmartSidebar = {
        hasItems: false, // this class will only work when this flag is true. If we don't have any items, all the calculations on scroll will be disabled by this flag
        items: [], //the array that has all the items
        scroll_window_scrollTop_last: 0, //last scrollTop position, used to calculate the scroll direction


        tds_snap_menu: tdUtil.getBackendVar( 'tds_snap_menu' ),   //read the snap menu setting from theme panel


        /**
         * @see tdSmartSidebar.td_events_resize
         */
        is_enabled: true, //if the smart sidebar is not needed (ex on mobile) put this flag to true
        is_enabled_state_run_once: false, // make sure that we dun enable and disable only once
        is_disabled_state_run_once: false,


        is_tablet_grid: false, //we detect if the current grid is the tablet portrait one


        _view_port_current_interval_index: tdViewport.getCurrentIntervalIndex(),


        item: function() {
            this.content_jquery_obj = '';
            this.sidebar_jquery_obj = '';


            // the position variables
            this.sidebar_top = 0;
            this.sidebar_bottom = 0;
            this.sidebar_height = 0;


            this.content_top = 0;
            this.content_bottom = 0;

            // the sidebar state
            this.sidebar_state = '';

            this.case_1_run_once = false;
            this.case_2_run_once = false;
            this.case_3_run_once = false;
            this.case_3_last_sidebar_height = 0; // case 3 has to be recalculated if the sidebar height changes
            this.case_3_last_content_height = 0; // recalculate case 3 if content height has changed
            this.case_4_run_once = false;
            this.case_4_last_menu_offset = 0;
            this.case_5_run_once = false;
            this.case_6_run_once = false;
        },


        //add item to the array
        add_item: function( item ) {
            tdSmartSidebar.hasItems = true; //put the flag that we have items

            /**
             * add clear fix to the content and sidebar.
             * we need the clear fix to clear the margin of the first and last element
             */
            item.sidebar_jquery_obj
                .prepend( '<div class="clearfix"></div>' )
                .append( '<div class="clearfix"></div>' );

            item.content_jquery_obj
                .prepend( '<div class="clearfix"></div>' )
                .append( '<div class="clearfix"></div>' );


            tdSmartSidebar.items.push( item );
        },


        td_events_scroll: function( scrollTop ) {


            // we don't have any smart sidebars, return
            if ( false === tdSmartSidebar.hasItems ) {
                return;
            }


            // check if the smart sidebar is enabled ( the sidebar can be enabled / disabled on runtime )
            if ( false === tdSmartSidebar.is_enabled ) {

                if ( false === tdSmartSidebar.is_disabled_state_run_once ) { // this call runs only ONCE / state change - we don't want any code to run on mobile
                    tdSmartSidebar.is_disabled_state_run_once = true;
                    for ( var item_index = 0; item_index < tdSmartSidebar.items.length; item_index++ ) {
                        tdSmartSidebar.items[ item_index ].sidebar_jquery_obj.css({
                            width: 'auto',
                            position: 'static',
                            top: 'auto',
                            bottom: 'auto'
                        });
                    }
                    tdSmartSidebar.log( 'smart_sidebar_disabled' );
                }

                return;
            }


            // all is done in an animation frame
            window.requestAnimationFrame(function() {


                /**
                 * this is the height of the menu, computed live. We
                 * @type {number}
                 */
                var td_affix_menu_computed_height = 0;
                if ( '' !== tdSmartSidebar.tds_snap_menu ) { // if the menu is not snapping in any way - do not calculate this

                    // The main_menu_height was replaced with the _get_menu_affix_height(), because we need the size of the
                    // affix menu. In the 'Newspaper' the menu has different sizes when it is affix 'on' and 'off'.
                    td_affix_menu_computed_height = tdAffix._get_menu_affix_height();

                    // Menu offset value is added when we are on 'smart_snap_always' case
                    if ('smart_snap_always' === tdAffix.tds_snap_menu) {
                        td_affix_menu_computed_height += tdAffix.menu_offset;
                    }
                }
                // The following height is added just for Newspaper theme.
                // In the Newsmag theme, the sidebar elements have already a 'padding-top' of 20px

                if ( ( 'undefined' !== typeof window.tdThemeName ) && ( 'Newspaper' === window.tdThemeName ) ) {
                    td_affix_menu_computed_height += 20;
                }





                // compute the scrolling direction
                var scroll_direction = '';
                //check the direction
                if ( scrollTop !== tdSmartSidebar.scroll_window_scrollTop_last ) { // compute direction only if we have different last scroll top
                    // compute the direction of the scroll
                    if ( scrollTop > tdSmartSidebar.scroll_window_scrollTop_last ) {
                        scroll_direction = 'down';
                    } else {
                        scroll_direction = 'up';
                    }
                }
                tdSmartSidebar.scroll_window_scrollTop_last = scrollTop;



                /**
                 * scrollTop - is the distance that is scrolled from the top of the document PLUS the height of the menu
                 */



                var view_port_height = jQuery( window ).height(); // ~ we can get this only once + on resize
                var view_port_bottom = scrollTop + view_port_height;

                scrollTop = scrollTop + td_affix_menu_computed_height;

                // go in all the sidebar items
                for ( var item_index = 0; item_index < tdSmartSidebar.items.length; item_index++ ) {

                    var cur_item_ref = tdSmartSidebar.items[ item_index ];

                    cur_item_ref.content_top = cur_item_ref.content_jquery_obj.offset().top;
                    cur_item_ref.content_height = cur_item_ref.content_jquery_obj.height();
                    cur_item_ref.content_bottom = cur_item_ref.content_top + cur_item_ref.content_height;

                    cur_item_ref.sidebar_top = cur_item_ref.sidebar_jquery_obj.offset().top;
                    cur_item_ref.sidebar_height = cur_item_ref.sidebar_jquery_obj.height();
                    cur_item_ref.sidebar_bottom = cur_item_ref.sidebar_top + cur_item_ref.sidebar_height;





                    /**
                     * Is the sidebar smaller than the content ?
                     */
                    if ( cur_item_ref.content_height <= cur_item_ref.sidebar_height ) {
                        cur_item_ref.sidebar_state = 'case_6_content_too_small';



                        /**
                         * the sidebar is smaller than the view port?  that means that we have to switch to a more simpler sidebar AKA affix
                         */

                    } else if ( cur_item_ref.sidebar_height < view_port_height ) {

                        // ref value used to compare the scroll top
                        var ref_value = cur_item_ref.content_top;

                        // For 'Newsmag' the ref value is incremented with td_affix_menu_computed_height
                        // It solves a case when the affix menu leaves the 'case_2_top_of_content' phase to 'case_4_fixed_up' too early
                        // It's because of how the grid, and smart sidebar, are built on Newspaper vs Newsmag
                        if ( ! tdAffix.is_menu_affix && ( 'undefined' !== typeof window.tdThemeName ) && ( 'Newsmag' === window.tdThemeName ) && ( 'smart_snap_always' === tdAffix.tds_snap_menu ) ) {
                            ref_value += td_affix_menu_computed_height;
                        }

                        //if (tdSmartSidebar._is_smaller_or_equal(scrollTop, cur_item_ref.content_top)) {
                        if ( tdSmartSidebar._is_smaller_or_equal( scrollTop, ref_value ) ) {
                            // not affix - we did not scroll to reach the sidebar
                            cur_item_ref.sidebar_state = 'case_2_top_of_content';
                        }

                        // [1] if the sidebar is visible and we have enough space in the sidebar, place it at the top affix top
                        // [2] if the sidebar is above the view port and nothing is visible, place the sidebar at the bottom of the column

                        else if ( true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, scrollTop ) ) {
                            if ( tdSmartSidebar._is_smaller( scrollTop, cur_item_ref.content_bottom - cur_item_ref.sidebar_height ) ) { //this is a special case where on the initial load, the bottom of the content is visible and we have a lot of space to show the widget at the top affixed.
                                cur_item_ref.sidebar_state = 'case_4_fixed_up'; // [1]87
                            } else {
                                cur_item_ref.sidebar_state = 'case_3_bottom_of_content'; // [2]
                            }


                        } else {

                            // affix
                            if ( tdSmartSidebar._is_smaller_or_equal( cur_item_ref.content_bottom, cur_item_ref.sidebar_bottom ) ) { // check to see if we reached the bottom of the content / row
                                if ( 'up' === scroll_direction && tdSmartSidebar._is_smaller_or_equal( scrollTop, cur_item_ref.sidebar_top ) ) {
                                    cur_item_ref.sidebar_state = 'case_4_fixed_up'; // get out of the case_3_bottom_of_content state
                                } else {
                                    cur_item_ref.sidebar_state = 'case_3_bottom_of_content';
                                }

                            } else {
                                if ( cur_item_ref.content_bottom - scrollTop >= cur_item_ref.sidebar_height ) {
                                    // Make sure that we have space for the sidebar to affix it to the top
                                    cur_item_ref.sidebar_state = 'case_4_fixed_up';  // we are not at the bottom of the content
                                } else {

                                    // this case isn't reached. It's accomplish by the tdSmartSidebar._is_smaller_or_equal(cur_item_ref.content_bottom, cur_item_ref.sidebar_bottom) case

                                    cur_item_ref.sidebar_state = 'case_3_bottom_of_content';
                                }
                                //console.log(cur_item_ref.content_bottom + ' >= ' +  cur_item_ref.sidebar_bottom); //!!!! fix this case pe ? @20may2016 era un url aici dar l-am sters din motive de securitate
                            }
                        }



                        /**
                         * the sidebar is larger than the view port and the content is bigger
                         */


                    } else {

                        //// if the sidebar is above the view port and nothing is visible, place the sidebar at the bottom of the column
                        //if (tdSmartSidebar._is_smaller(cur_item_ref.sidebar_bottom, scrollTop) === true) {
                        //    cur_item_ref.sidebar_state = 'case_3_bottom_of_content';
                        //    tdSmartSidebar.log(cur_item_ref.sidebar_bottom + ' ~ ' + scrollTop);
                        //}


                        // if the sidebar is above the view port and nothing is visible, place the sidebar fixed up if it's smaller than the viewport,
                        //      fixed down, meaning that a possible previous operation could be 'scroll down'
                        // if none of the above operations meets the conditions, the sidebar is placed at the bottom of the content
                        if ( true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, scrollTop ) ) {

                            if ( true === tdSmartSidebar._is_smaller_or_equal(scrollTop, cur_item_ref.sidebar_top ) &&

                                true === tdSmartSidebar._is_smaller_or_equal( cur_item_ref.content_top, scrollTop ) //we are scrolling up ... make sure that we don't overshoot the sidebar by going over content_top. This happens when the sidebar is offseted by x number of pixels vs content
                            ) {
                                //console.log('sidebar_top' + cur_item_ref.sidebar_top + ' content top:' + cur_item_ref.content_top);
                                cur_item_ref.sidebar_state = 'case_4_fixed_up';
                            }
                            else if (
                                true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, view_port_bottom ) &&
                                true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, cur_item_ref.content_bottom ) &&
                                cur_item_ref.content_bottom >= view_port_bottom
                            ) {
                                cur_item_ref.sidebar_state = 'case_1_fixed_down';
                            }
                            else {
                                cur_item_ref.sidebar_state = 'case_3_bottom_of_content';
                            }
                        }



                        // position:fixed; bottom:0
                        else if (
                            true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, view_port_bottom ) &&
                            true === tdSmartSidebar._is_smaller( cur_item_ref.sidebar_bottom, cur_item_ref.content_bottom ) &&
                            'down' === scroll_direction &&
                            cur_item_ref.content_bottom >= view_port_bottom
                        ) {
                            //console.log(cur_item_ref.sidebar_bottom + ' < ' + cur_item_ref.content_bottom);
                            cur_item_ref.sidebar_state = 'case_1_fixed_down';
                        }

                        // the sidebar is at the top of the content ( position:static )
                        else if (
                            true === tdSmartSidebar._is_smaller_or_equal( cur_item_ref.sidebar_top, cur_item_ref.content_top ) &&
                            'up' === scroll_direction &&
                            cur_item_ref.content_bottom >= view_port_bottom
                        ) {
                            cur_item_ref.sidebar_state = 'case_2_top_of_content';
                        }




                        // the sidebar reached the bottom of the content
                        else if (
                            ( true === tdSmartSidebar._is_smaller_or_equal(cur_item_ref.content_bottom, cur_item_ref.sidebar_bottom) && 'down' === scroll_direction ) ||
                            cur_item_ref.content_bottom < view_port_bottom

                        ) {
                            cur_item_ref.sidebar_state = 'case_3_bottom_of_content';

                        }
                        // scrolling up, the sidebar is fixed up ( position:fixed; top:0 )
                        else if ( true === tdSmartSidebar._is_smaller_or_equal( scrollTop, cur_item_ref.sidebar_top ) && 'up' === scroll_direction &&

                            true === tdSmartSidebar._is_smaller_or_equal( cur_item_ref.content_top, scrollTop ) //we are scrolling up ... make sure that we don't overshoot the sidebar by going over content_top. This happens when the sidebar is offseted by x number of pixels vs content
                        ) {
                            //console.log('sidebar_top' + cur_item_ref.sidebar_top + ' content top:' + cur_item_ref.content_top);
                            cur_item_ref.sidebar_state = 'case_4_fixed_up';
                        }




                        /**
                         * This is the case when the scroll direction is 'up', but the sidebar is above the viewport (it could be left behind by a fast operation like typing HOME key)
                         */
                        else if ('up' === scroll_direction && true === tdSmartSidebar._is_smaller_or_equal( view_port_bottom, cur_item_ref.sidebar_top ))
                        {
                            cur_item_ref.sidebar_state = 'case_2_top_of_content';
                        }



                        // when to put absolute?
                        if (
                            ( 'case_1_fixed_down' === cur_item_ref.sidebar_state && 'up' === scroll_direction ) ||
                            ( 'case_4_fixed_up' === cur_item_ref.sidebar_state && 'down' === scroll_direction )
                        ) {
                            cur_item_ref.sidebar_state = 'case_5_absolute'; //absolute while going up?
                        }

                    } // end sidebar length check   cur_item_ref.sidebar_height < view_port_height




                    /**
                     * after we have the state, we enter this switch that makes sure that we only have one state change
                     */

                    // we have to set the content width via JS
                    //var column_content_width = 339;
                    //if (tdSmartSidebar.is_tablet_grid) {
                    //    column_content_width = 251;
                    //}


                    var column_content_width = 0;

                    var view_port_current_item = tdViewport.getCurrentIntervalItem();

                    if ( null !== view_port_current_item ) {
                        column_content_width = cur_item_ref.sidebar_jquery_obj.parent( '.vc_column, .td-main-sidebar, .vc_column-inner, .vc_column_inner').width();
                        cur_item_ref.sidebar_jquery_obj.width( column_content_width );
                    }



                    switch ( cur_item_ref.sidebar_state ) {
                        case 'case_1_fixed_down':

                            if ( true === cur_item_ref.case_1_run_once ) {
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = true;
                            cur_item_ref.case_2_run_once = false;
                            cur_item_ref.case_3_run_once = false;
                            cur_item_ref.case_4_run_once = false;
                            cur_item_ref.case_5_run_once = false;
                            cur_item_ref.case_6_run_once = false;


                            cur_item_ref.sidebar_jquery_obj.css({
                                width: column_content_width,
                                position: 'fixed',
                                top: 'auto',
                                bottom: '0',
                                'z-index': '1'
                            });


                            break;

                        case 'case_2_top_of_content':

                            if ( true === cur_item_ref.case_2_run_once ) {
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = false;
                            cur_item_ref.case_2_run_once = true;
                            cur_item_ref.case_3_run_once = false;
                            cur_item_ref.case_4_run_once = false;
                            cur_item_ref.case_5_run_once = false;
                            cur_item_ref.case_6_run_once = false;


                            cur_item_ref.sidebar_jquery_obj.css({
                                width: 'auto',
                                position: 'static',
                                top: 'auto',
                                bottom: 'auto'
                            });
                            break;

                        case 'case_3_bottom_of_content':
                            // case 3 has to be recalculated if the sidebar height changes

                            if ( true === cur_item_ref.case_3_run_once &&
                                cur_item_ref.case_3_last_sidebar_height === cur_item_ref.sidebar_height &&
                                cur_item_ref.case_3_last_content_height === cur_item_ref.content_height
                            ) { //if the case already runned AND the sidebar height did not change
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = false;
                            cur_item_ref.case_2_run_once = false;
                            cur_item_ref.case_3_run_once = true;
                            cur_item_ref.case_3_last_sidebar_height = cur_item_ref.sidebar_height;
                            cur_item_ref.case_3_last_content_height = cur_item_ref.content_height;
                            cur_item_ref.case_4_run_once = false;
                            cur_item_ref.case_5_run_once = false;
                            cur_item_ref.case_6_run_once = false;


                            cur_item_ref.sidebar_jquery_obj.css({
                                width: column_content_width,
                                position: 'absolute',
                                top: cur_item_ref.content_bottom - cur_item_ref.sidebar_height - cur_item_ref.content_top,
                                bottom: 'auto'
                            });
                            break;

                        case 'case_4_fixed_up':

                            if ( true === cur_item_ref.case_4_run_once && cur_item_ref.case_4_last_menu_offset === td_affix_menu_computed_height ) { //if the case already runned AND the menu height did not changed
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = false;
                            cur_item_ref.case_2_run_once = false;
                            cur_item_ref.case_3_run_once = false;
                            cur_item_ref.case_4_run_once = true;
                            cur_item_ref.case_4_last_menu_offset = td_affix_menu_computed_height;
                            cur_item_ref.case_5_run_once = false;
                            cur_item_ref.case_6_run_once = false;


                            cur_item_ref.sidebar_jquery_obj.css({
                                width: column_content_width,
                                position: 'fixed',
                                top: td_affix_menu_computed_height,
                                bottom: 'auto'
                            });
                            break;

                        case 'case_5_absolute':

                            if ( true === cur_item_ref.case_5_run_once ) {
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = false;
                            cur_item_ref.case_2_run_once = false;
                            cur_item_ref.case_3_run_once = false;
                            cur_item_ref.case_4_run_once = false;
                            cur_item_ref.case_5_run_once = true;
                            cur_item_ref.case_6_run_once = false;


                            cur_item_ref.sidebar_jquery_obj.css({
                                width: column_content_width,
                                position: 'absolute',
                                top: cur_item_ref.sidebar_top - cur_item_ref.content_top,
                                bottom: 'auto'
                            });
                            break;

                        case 'case_6_content_too_small':

                            if ( true === cur_item_ref.case_6_run_once ) {
                                break;
                            }

                            tdSmartSidebar.log( 'sidebar_id: ' + item_index + ' ' + cur_item_ref.sidebar_state );

                            cur_item_ref.case_1_run_once = false;
                            cur_item_ref.case_2_run_once = false;
                            cur_item_ref.case_3_run_once = false;
                            cur_item_ref.case_4_run_once = false;
                            cur_item_ref.case_5_run_once = false;
                            cur_item_ref.case_6_run_once = true;

                            cur_item_ref.sidebar_jquery_obj.css({
                                width: 'auto',
                                position: 'static',
                                top: 'auto',
                                bottom: 'auto'
                            });
                            break;
                    }
                } // end for loop
            }); // end request animation frame
        }, // end td_events_scroll


        compute: function() {

            tdSmartSidebar.td_events_scroll( jQuery( window ).scrollTop() );
        },


        // resets the run once flags. It may fail sometimes due to case_3_last_sidebar_height & case_4_last_menu_offset
        reset_run_once_flags: function () {
            for ( var item_index = 0; item_index < tdSmartSidebar.items.length; item_index++ ) {
                tdSmartSidebar.items[ item_index ].case_1_run_once = false;
                tdSmartSidebar.items[ item_index ].case_2_run_once = false;
                tdSmartSidebar.items[ item_index ].case_3_run_once = false;
                tdSmartSidebar.items[ item_index ].case_3_last_sidebar_height = 0;
                tdSmartSidebar.items[ item_index ].case_3_last_content_height = 0;
                tdSmartSidebar.items[ item_index ].case_4_run_once = false;
                tdSmartSidebar.items[ item_index ].case_4_last_menu_offset = 0;
                tdSmartSidebar.items[ item_index ].case_5_run_once = false;
                tdSmartSidebar.items[ item_index ].case_6_run_once = false;
            }
        },



        td_events_resize: function() {
            // enable and disable the smart sidebar

            tdSmartSidebar._view_port_current_interval_index = tdViewport.getCurrentIntervalIndex();

            switch ( tdSmartSidebar._view_port_current_interval_index ) {

                case 0 :

                    tdSmartSidebar.is_enabled = false;

                    // flag marked false to be made true only once, when the view port has not the first interval index [0]
                    tdSmartSidebar.is_enabled_state_run_once = false;

                    break;

                case 1 :
                    if ( false === tdSmartSidebar.is_tablet_grid ) { // we switched

                        tdSmartSidebar.reset_run_once_flags();

                        tdSmartSidebar.is_tablet_grid = true;
                        tdSmartSidebar.is_desktop_grid = false;

                        tdSmartSidebar.log( 'view port tablet' );
                    }
                    tdSmartSidebar.is_enabled = true;
                    tdSmartSidebar.is_disabled_state_run_once = false;

                    if ( false === tdSmartSidebar.is_enabled_state_run_once ) {
                        tdSmartSidebar.is_enabled_state_run_once = true;
                        tdSmartSidebar.log( 'smart_sidebar_enabled' );
                    }
                    break;

                case 2 :
                case 3 :
                    if ( true === tdSmartSidebar.is_tablet_grid ) { // we switched

                        tdSmartSidebar.reset_run_once_flags();

                        tdSmartSidebar.is_tablet_grid = false;
                        tdSmartSidebar.is_desktop_grid = true;

                        tdSmartSidebar.log( 'view port desktop' );
                    }
                    tdSmartSidebar.is_enabled = true;
                    tdSmartSidebar.is_disabled_state_run_once = false;

                    if ( false === tdSmartSidebar.is_enabled_state_run_once ) {
                        tdSmartSidebar.is_enabled_state_run_once = true;
                        tdSmartSidebar.log( 'smart_sidebar_enabled' );
                    }
                    break;
            }

            // !!!! we may be able to delay the compute a bit (aka run it on the 500ms timer)
            tdSmartSidebar.compute();
        },


        log: function( msg ) {
            //console.log(msg);
        },


        /**
         * check if the two numbers are approximately equal OR the number1 is smaller.
         * This function is used to compensate for differences in the offset top reported by IE, FF but not chrome
         * IE and FF have an error for offset top of +- 0.5
         * @param number1 - this has to be smaller or approximately equal with number2 to return true
         * @param number2
         * @returns {boolean}
         * @private
         */
        _is_smaller_or_equal: function( number1, number2 ) {
            // check if the two numbers are approximately equal
            // - first we check if the difference between the numbers is bigger than 1 unit
            // - second we check if the first number is bigger than the second one
            // if the two conditions are met, we return false


            if ( Math.abs( number1 - number2 ) >= 1) {
                // we have a difference that is bigger than 1 unit (px), check if the numbers are smaller or bigger
                if ( number1 < number2 ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // the difference between the two numbers is smaller than one unit (1 px), this means that the two numbers are the same
                return true;
            }
        },


        /**
         * Checks to see if number1 < number2 by at least one unit!
         * @param number1
         * @param number2
         * @returns {boolean}
         * @private
         */
        _is_smaller: function( number1, number2 ) {
            if ( Math.abs( number1 - number2 ) >= 1) {
                if ( number1 < number2 ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // the difference between the two numbers is smaller than one unit (1 px), this means that the two numbers are the same
                return false;
            }
        }
    };

    //console.log(tdSmartSidebar.items);
})();



/**
 * Infinite loader v1.0 by Radu O. / tagDiv
 * USES:
 *  - tdEvents.js
 *  - for blocks:
 *      - td_block::get_block_pagination - custom load more
 *      - in td_js_generator.php - main block object has ajax_pagination_infinite_stop - to stop the infinite scroll after x number of pages and show the load more button after that
 *
 */

/* global jQuery:false */
/* global tdBlocks:false */


/**
 * Global infinite loader object
 */
var tdInfiniteLoader = {};

(function () {
    "use strict";

    /**
     * - register and keep track of dom elements
     * - calculate position from the top of each element
     * - monitor on scroll event
     *  - if one or more of the dom elements is visible
     *  - fire the callback for that dom element! only ONCE
     */


    tdInfiniteLoader = {

        hasItems: false, // this class will only work when this flag is true. If we don't have any items, all the calculations on scroll will be disabled by this flag

        items: [], //the array that has all the items

        // one item object (instantiable)
        item: function() {
            this.uid=''; // - an unique id of the item, usually is the block id! - it is used to enable the callback on a per item basis
            this.jqueryObj = ''; //find the item easily for animation ??
            this.bottomTop = 0;  //distance from the bottom of the dom element to top - computed in - @see tdInfiniteLoader.compute_top_distances();
            this.isVisibleCallbackEnabled = true; //the callback will fire only when this flag is true. We set it to true after the blocks ajax run @see tdBlocks.tdBlockAjaxLoadingEnd
            this.isVisibleCallback = function () { //callback when the item's bottom is visible :)
            };
        },

        addItem: function(item) {
            tdInfiniteLoader.hasItems = true; //put the flag that we have items
            tdInfiniteLoader.items.push(item);
        },


        /**
         * foreach element from items, compute the distances from the top
         *  - this is done only on load or when the page is resized
         */
        computeTopDistances: function() {

            //check the flag to see if we have any items
            if ( tdInfiniteLoader.hasItems === false ) {
                return;
            }

            jQuery.each(tdInfiniteLoader.items, function(index, v_event) {
                var topTop = tdInfiniteLoader.items[index].jqueryObj.offset().top;
                //top of document to bottom of element
                tdInfiniteLoader.items[index].bottomTop = topTop + tdInfiniteLoader.items[index].jqueryObj.height();
            });

            //also calculate the events
            tdInfiniteLoader.computeEvents();

        },


        /**
         * calculate if we have to fire an event like isVisibleCallback()
         *  - this is done on scroll and on resize!
         */
        computeEvents: function() {
            //check the flag to see if we have any items
            if ( tdInfiniteLoader.hasItems === false ) {
                return;
            }

            var topToViewportBottom = jQuery(window).height() + jQuery(window).scrollTop();


            jQuery.each(tdInfiniteLoader.items, function(index, item) {
                if ( tdInfiniteLoader.items[index].bottomTop < topToViewportBottom + 700 ) {

                    //check to see if we can call the callback again
                    if ( tdInfiniteLoader.items[index].isVisibleCallbackEnabled === true ) {
                        tdInfiniteLoader.items[index].isVisibleCallbackEnabled = false;
                        //the call
                        tdInfiniteLoader.items[index].isVisibleCallback();
                    }
                }


            });
        },


        /**
         * enables the isVisibleCallback - it is called by td_blocks.js only when a block receives an infinite loading ajax reply
         * @param $item_uid - an unique id of the item, usually is the block id!
         * @see tdBlocks.tdBlockAjaxLoadingEnd
         */
        enable_is_visible_callback: function($item_uid) {
            jQuery.each(tdInfiniteLoader.items, function(index, item) {
                if ( item.uid === $item_uid ) {
                    tdInfiniteLoader.items[index].isVisibleCallbackEnabled = true;
                    return false; //brake jquery each
                }
            });
        }

    };






    /**
     * we are using td_ajax_infinite to know when to trigger a block loading
     */
    jQuery('.td_ajax_infinite').each( function() {

        // create a new infinite loader item
        var tdInfiniteLoaderItem = new tdInfiniteLoader.item();

        tdInfiniteLoaderItem.jqueryObj = jQuery(this);
        tdInfiniteLoaderItem.uid = jQuery(this).data('td_block_id');


        /**
         * the callback when the bottom of the element is visible on screen and we need to do something - like load another page
         * - the callback does not fire again until tdInfiniteLoader.enable_is_visible_callback is called @see tdInfiniteLoader.js:95
         */
        tdInfiniteLoaderItem.isVisibleCallback = function () {      // the is_visible callback is called when we have to pull new content up because the element is visible

            // get the current block object
            var currentBlockObj = tdBlocks.tdGetBlockObjById(tdInfiniteLoaderItem.jqueryObj.data('td_block_id'));

            // if we don't have a infinite stop limit or if we have one we dint' hit it yet
            if ( currentBlockObj.ajax_pagination_infinite_stop === '' ||
                    currentBlockObj.td_current_page < (parseInt(currentBlockObj.ajax_pagination_infinite_stop) + 1) ) {

                // get the block data and increment the pagination
                currentBlockObj.td_current_page++;
                tdBlocks.tdAjaxDoBlockRequest(currentBlockObj, 'infinite_load');

            } else {
                /**
                 * show the load more button. The button is already there, hidden - do not know if it's the best solution :)
                 * @see td_block::get_block_pagination  in td_block.php
                 */
                if ( currentBlockObj.td_current_page < currentBlockObj.max_num_pages ) {
                    setTimeout( function(){
                        jQuery('#infinite-lm-' + currentBlockObj.id)
                            .css('display', 'block')
                            .css('visibility', 'visible')
                        ;
                    }, 400);
                }
            }
        };
        tdInfiniteLoader.addItem(tdInfiniteLoaderItem);
    });








    //compute to
    jQuery(window).load( function() {
        tdInfiniteLoader.computeTopDistances();
    });

    jQuery().ready( function() {
        tdInfiniteLoader.computeTopDistances();
    });
})();
/*
* used by vimeo in td_video shortcode
* */

"use strict";

var Froogaloop=function(){function e(a){return new e.fn.init(a)}function h(a,c,b){if(!b.contentWindow.postMessage)return!1;var f=b.getAttribute("src").split("?")[0],a=JSON.stringify({method:a,value:c});"//"===f.substr(0,2)&&(f=window.location.protocol+f);b.contentWindow.postMessage(a,f)}function j(a){var c,b;try{c=JSON.parse(a.data),b=c.event||c.method}catch(f){}"ready"==b&&!i&&(i=!0);if(a.origin!=k)return!1;var a=c.value,e=c.data,g=""===g?null:c.player_id;c=g?d[g][b]:d[b];b=[];if(!c)return!1;void 0!==
    a&&b.push(a);e&&b.push(e);g&&b.push(g);return 0<b.length?c.apply(null,b):c.call()}function l(a,c,b){b?(d[b]||(d[b]={}),d[b][a]=c):d[a]=c}var d={},i=!1,k="";e.fn=e.prototype={element:null,init:function(a){"string"===typeof a&&(a=document.getElementById(a));this.element=a;a=this.element.getAttribute("src");"//"===a.substr(0,2)&&(a=window.location.protocol+a);for(var a=a.split("/"),c="",b=0,f=a.length;b<f;b++){if(3>b)c+=a[b];else break;2>b&&(c+="/")}k=c;return this},api:function(a,c){if(!this.element||
    !a)return!1;var b=this.element,f=""!==b.id?b.id:null,d=!c||!c.constructor||!c.call||!c.apply?c:null,e=c&&c.constructor&&c.call&&c.apply?c:null;e&&l(a,e,f);h(a,d,b);return this},addEvent:function(a,c){if(!this.element)return!1;var b=this.element,d=""!==b.id?b.id:null;l(a,c,d);"ready"!=a?h("addEventListener",a,b):"ready"==a&&i&&c.call(null,d);return this},removeEvent:function(a){if(!this.element)return!1;var c=this.element,b;a:{if((b=""!==c.id?c.id:null)&&d[b]){if(!d[b][a]){b=!1;break a}d[b][a]=null}else{if(!d[a]){b=
    !1;break a}d[a]=null}b=!0}"ready"!=a&&b&&h("removeEventListener",a,c)}};e.fn.init.prototype=e.fn;window.addEventListener?window.addEventListener("message",j,!1):window.attachEvent("onmessage",j);return window.Froogaloop=window.$f=e}();
/* td_custom_events.js - handles the booster td_events that require throttling
 * v 1.0 - wp_011
 */

/* global tdAnimationScroll:{} */
/* global tdAnimationStack:{} */
/* global tdPullDown:{} */
/* global tdBackstr:{} */
/* global td_backstretch_items:Array */
/* global td_compute_backstretch_item:Function */

/* global setMenuMinHeight:Function */

var tdCustomEvents = {};

(function(){

    'use strict';

    tdCustomEvents = {


        /**
         * - callback real scroll called from td_events
         * @private
         */
        _callback_scroll: function() {
            tdAnimationScroll.compute_all_items();
        },


        /**
         * - callback real resize called from td_events
         * @private
         */
        _callback_resize: function() {

        },


        /**
         * - callback lazy scroll called from td_events at 100ms
         * @private
         */
        _lazy_callback_scroll_100: function() {
            if ( true === tdAnimationStack.activated ) {
                tdAnimationStack.td_events_scroll();
            }
        },


        /**
         * - callback lazy scroll called from td_events at 500ms
         * @private
         */
        _lazy_callback_scroll_500: function() {

        },



        /**
         * - callback lazy resize called from td_events at 100ms
         * @private
         */
        _lazy_callback_resize_100: function() {
            tdPullDown.td_events_resize();
            tdBackstr.td_events_resize();
            tdAnimationScroll.td_events_resize();
        },


        /**
         * - callback lazy resize called from td_events at 500ms
         * @private
         */
        _lazy_callback_resize_500: function() {
            if ( true === tdAnimationStack.activated ) {
                tdAnimationStack.td_events_resize();
            }

            // - every tdAnimationScroll.item item of the td_backstretch_items array must be reinitialized and repositioned for parallax effect
            for ( var i = 0; i < td_backstretch_items.length; i++ ) {
                tdAnimationScroll.reinitialize_item( td_backstretch_items[ i ], true );
                td_compute_backstretch_item( td_backstretch_items[ i ] );

                // compute_all_items is used instead, for requestAnimationFrame
                //tdAnimationScroll.compute_item(td_backstretch_items[i]);
            }

            // for better performance it's used tdAnimationScroll.compute_all_items, because it uses requestAnimationFrame
            tdAnimationScroll.compute_all_items();

            // !!!! It will be refactorized when td_site will be
            setMenuMinHeight();


            // Stretch video background
            jQuery( 'body' ).find( '.tdc-video-inner-wrapper' ).each(function() {

                var $wrapper = jQuery( this ),
                    $iframe = $wrapper.find( 'iframe' );

                if ( ! $iframe.length ) {
                    return;
                }

                var iframeAspectRatio = $iframe.attr( 'aspect-ratio' );

                if ( 'undefined' === typeof iframeAspectRatio ) {
                    return;
                }

                var wrapperWidth = $wrapper.width(),
                    wrapperHeight = $wrapper.height(),
                    wrapperAspectRatio = wrapperHeight / wrapperWidth;

                if ( iframeAspectRatio < wrapperAspectRatio ) {
                    $iframe.css({
                        width: wrapperHeight / iframeAspectRatio,
                        height: wrapperHeight
                    });
                } else if ( iframeAspectRatio > wrapperAspectRatio ) {
                    $iframe.css({
                        width: '100%',
                        height: iframeAspectRatio * wrapperWidth
                    });
                }
            });


            // every tdAnimationScroll.item item which has 'td_video_parallax' flag must be reinitialized and repositioned for parallax effect
            for ( var i = 0; i < tdAnimationScroll.items.length; i++ ) {
                if ( 'undefined' !== typeof tdAnimationScroll.items[i].td_video_parallax ) {
                    tdAnimationScroll.reinitialize_item( tdAnimationScroll.items[i], true );
                }
            }

        }
    };
})();

/* tdEvents.js - handles the events that require throttling
 * v 2.0 - wp_010
 *
 * moved in theme from wp_booster
 */

/* global jQuery:{} */
/* global tdAffix:{} */
/* global tdSmartSidebar:{} */
/* global tdViewport:{} */
/* global tdInfiniteLoader:{} */
/* global td_more_articles_box:{} */
/* global tdDetect:{} */
/* global tdCustomEvents:{} */
/* global tdHeader:{} */

/* global td_events_scroll_scroll_to_top:Function */

var tdEvents = {};

(function(){
    'use strict';

    tdEvents = {

        //the events - we have timers that look at the variables and fire the event if the flag is true
        scroll_event_slow_run: false,
        scroll_event_medium_run: false,

        resize_event_slow_run: false, //when true, fire up the resize event
        resize_event_medium_run: false,


        scroll_window_scrollTop: 0, //used to store the scrollTop

        window_pageYOffset: window.pageYOffset, // !!!! see if it can replace scroll_window_scrollTop [used by others]
        window_innerHeight: window.innerHeight, // used to store the window height
        window_innerWidth: window.innerWidth, // used to store the window width

        init: function() {

            jQuery( window ).scroll(function() {
                tdEvents.scroll_event_slow_run = true;
                tdEvents.scroll_event_medium_run = true;

                //read the scroll top
                tdEvents.scroll_window_scrollTop = jQuery( window ).scrollTop();
                tdEvents.window_pageYOffset = window.pageYOffset;

                /*  ----------------------------------------------------------------------------
                 Run affix menu event
                 */

                tdAffix.td_events_scroll( tdEvents.scroll_window_scrollTop ); //main menu

                tdSmartSidebar.td_events_scroll( tdEvents.scroll_window_scrollTop ); //smart sidebar scroll


                // call real tdCustomEvents scroll
                tdCustomEvents._callback_scroll();

                tdHeader.scroll();
            });


            jQuery( window ).resize(function() {
                tdEvents.resize_event_slow_run = true;
                tdEvents.resize_event_medium_run = true;

                tdEvents.window_innerHeight = window.innerHeight;
                tdEvents.window_innerWidth = window.innerWidth;

                //var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

                //w = jQuery(document).width();
                //console.log(w);


                // call real tdCustomEvents resize
                tdCustomEvents._callback_resize();

                tdHeader.resize();
            });



            //medium resolution timer for rest?
            setInterval(function() {

                // it must run before any others
                tdViewport.detectChanges();

                //scroll event
                if ( tdEvents.scroll_event_medium_run ) {
                    tdEvents.scroll_event_medium_run = false;
                    //compute events for the infinite scroll
                    tdInfiniteLoader.computeEvents();


                    // call lazy tdCustomEvents scroll
                    tdCustomEvents._lazy_callback_scroll_100();
                }

                if ( tdEvents.resize_event_medium_run ) {
                    tdEvents.resize_event_medium_run = false;
                    tdSmartSidebar.td_events_resize();


                    // call lazy tdCustomEvents resize
                    tdCustomEvents._lazy_callback_resize_100();
                }
            }, 100);



            //low resolution timer for rest?
            setInterval(function() {
                //scroll event
                if ( tdEvents.scroll_event_slow_run ) {
                    tdEvents.scroll_event_slow_run = false;

                    //back to top
                    td_events_scroll_scroll_to_top( tdEvents.scroll_window_scrollTop );

                    //more articles box
                    td_more_articles_box.td_events_scroll( tdEvents.scroll_window_scrollTop );


                    // call lazy tdCustomEvents scroll
                    tdCustomEvents._lazy_callback_scroll_500();
                }

                //resize event
                if ( tdEvents.resize_event_slow_run ) {
                    tdEvents.resize_event_slow_run = false;
                    tdAffix.compute_wrapper();
                    tdAffix.compute_top();
                    tdDetect.runIsPhoneScreen();


                    // call lazy tdCustomEvents resize
                    tdCustomEvents._lazy_callback_resize_500();
                }
            }, 500);
        }
    };

    tdEvents.init();
})();


/* global jQuery:{} */
/* global tdEvents:{} */

var tdHeader = {};

(function(){

    'use strict';

    tdHeader = {

        _selectorHeaderDesktop: '.td-header-desktop-wrap',
        _selectorHeaderDesktopSticky: '.td-header-desktop-sticky-wrap',
        _selectorHeaderMobile: '.td-header-mobile-wrap',
        _selectorHeaderMobileSticky: '.td-header-mobile-sticky-wrap',

        _selectorDataHeaderDesktop: 'tdc_header_desktop',
        _selectorDataHeaderDesktopSticky: 'tdc_header_desktop_sticky',
        _selectorDataHeaderMobile: 'tdc_header_mobile',
        _selectorDataHeaderMobileSticky: 'tdc_header_mobile_sticky',

        $_headerDesktop: undefined,
        $_headerDesktopSticky: undefined,
        $_mobileDesktop: undefined,
        $_mobileDesktopSticky: undefined,

        _headerDesktopHeight: undefined,
        _headerDesktopStickyHeight: undefined,
        _headerMobileHeight: undefined,
        _headerMobileStickyHeight: undefined,

        _headerDesktopStickyGap: 0,
        _headerMobileStickyGap: 0,

        _previousScrollPosition: 0,

        _isMobile: false,

        _isInitialized: false,

        init: function() {

            if ( tdHeader._isInitialized ) {
                return;
            }

            tdHeader.$_headerDesktop = jQuery( tdHeader._selectorHeaderDesktop  );
            tdHeader.$_headerDesktopSticky = jQuery( tdHeader._selectorHeaderDesktopSticky );
            tdHeader.$_headerMobile = jQuery( tdHeader._selectorHeaderMobile );
            tdHeader.$_headerMobileSticky = jQuery( tdHeader._selectorHeaderMobileSticky );

            if ( ! tdHeader.$_headerDesktop.length || ! tdHeader.$_headerDesktopSticky.length || ! tdHeader.$_headerMobile.length || ! tdHeader.$_headerMobileSticky.length ) {
                return;
            }

            if ( window.parent === window.top  && 'undefined' === typeof window.parent.tdcSidebar ) {

                tdHeader.$_headerDesktop.removeClass( 'tdc-zone-sticky-invisible tdc-zone-sticky-inactive' );
                tdHeader.$_headerDesktopSticky.removeClass( 'tdc-zone-sticky-invisible tdc-zone-sticky-inactive' );
                tdHeader.$_headerMobile.removeClass( 'tdc-zone-sticky-invisible tdc-zone-sticky-inactive' );
                tdHeader.$_headerMobileSticky.removeClass( 'tdc-zone-sticky-invisible tdc-zone-sticky-inactive' );
            }

            tdHeader.checkSizes();

            tdHeader._isInitialized = true;
        },


        computeItems: function() {

            if ( ! tdHeader._isInitialized ) {
                return;
            }

            if ( tdHeader._isMobile ) {

                tdHeader.$_headerDesktop.hide();
                tdHeader.$_headerDesktopSticky.hide();
                tdHeader.$_headerMobile.show();
                tdHeader.$_headerMobileSticky.show();

                if ( tdHeader.$_headerMobileSticky.hasClass( 'td-header-stop-transition' ) ) {
                    tdHeader.$_headerMobileSticky.removeClass( 'td-header-stop-transition' );
                }

                if ( tdHeader._headerMobileHeight < tdEvents.scroll_window_scrollTop ) {

                    if ( tdHeader.$_headerMobileSticky.find( '.tdc_zone:first' ).hasClass( 'td-header-sticky-smart' ) ) {
                        if ( tdHeader._previousScrollPosition < tdEvents.scroll_window_scrollTop ) {
                            if ( tdHeader.$_headerMobileSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }
                            tdHeader.$_headerMobileSticky.removeClass( 'td-header-active' );
                        } else {
                            if ( ! tdHeader.$_headerMobileSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }
                            tdHeader.$_headerMobileSticky.addClass( 'td-header-active' );
                        }
                    } else {
                        if ( ! tdHeader.$_headerMobileSticky.hasClass( 'td-header-active' ) ) {
                            tdHeader.doExtra();
                        }

                        tdHeader.$_headerMobile.removeClass( 'td-header-active' );
                        tdHeader.$_headerMobileSticky.addClass( 'td-header-active' );
                    }

                } else {

                    if ( tdHeader.$_headerMobile.hasClass( 'td-header-active' ) ) {
                        tdHeader.$_headerMobileSticky.addClass( 'td-header-stop-transition' );
                    }

                    if ( tdHeader.$_headerMobileSticky.hasClass( 'td-header-active' ) ) {
                        tdHeader.doExtra();
                    }

                    tdHeader.$_headerMobile.addClass( 'td-header-active' );
                    tdHeader.$_headerMobileSticky.removeClass( 'td-header-active' );
                }

            } else {

                tdHeader.$_headerDesktop.show();
                tdHeader.$_headerDesktopSticky.show();
                tdHeader.$_headerMobile.hide();
                tdHeader.$_headerMobileSticky.hide();

                if ( tdHeader.$_headerDesktopSticky.hasClass( 'td-header-stop-transition' ) ) {
                    tdHeader.$_headerDesktopSticky.removeClass( 'td-header-stop-transition' );
                }

                if ( tdHeader.$_headerDesktopSticky.find( '.tdc_zone:first' ).hasClass( 'td-header-sticky-smart' ) ) {

                    if ( tdHeader._previousScrollPosition < tdEvents.scroll_window_scrollTop ) {

                        // scroll down

                        if ( tdHeader._headerDesktopHeight < tdEvents.scroll_window_scrollTop ) {

                            if ( tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }
                            tdHeader.$_headerDesktop.addClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.removeClass( 'td-header-active' );
                        }

                    } else if ( tdHeader._previousScrollPosition > tdEvents.scroll_window_scrollTop ) {

                        // scroll up

                        if ( tdHeader._headerDesktopHeight + parseInt( tdHeader._headerDesktopStickyGap ) < tdEvents.scroll_window_scrollTop ) {

                            if ( ! tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.removeClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.addClass( 'td-header-active' );

                        } else {

                            if ( tdHeader._headerDesktopHeight > tdEvents.scroll_window_scrollTop && tdHeader.$_headerDesktop.hasClass( 'td-header-active' ) ) {
                                tdHeader.$_headerDesktopSticky.addClass( 'td-header-stop-transition' );
                            }

                            if ( tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.addClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.removeClass( 'td-header-active' );
                        }
                    }

                } else {

                    if ( tdHeader._previousScrollPosition < tdEvents.scroll_window_scrollTop ) {

                        // scroll down

                        if ( tdHeader._headerDesktopHeight < tdEvents.scroll_window_scrollTop ) {

                            if ( ! tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.removeClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.addClass( 'td-header-active' );

                        } else {

                            if ( tdHeader.$_headerDesktop.hasClass( 'td-header-active' ) ) {
                                tdHeader.$_headerDesktopSticky.addClass( 'td-header-stop-transition' );
                            }

                            if ( tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.addClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.removeClass( 'td-header-active' );
                        }

                    } else if ( tdHeader._previousScrollPosition > tdEvents.scroll_window_scrollTop ) {

                        // scroll up

                        if ( tdHeader._headerDesktopHeight + parseInt( tdHeader._headerDesktopStickyGap ) < tdEvents.scroll_window_scrollTop ) {

                            if ( ! tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.removeClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.addClass( 'td-header-active' );

                        } else {

                            if ( tdHeader._headerDesktopHeight > tdEvents.scroll_window_scrollTop && tdHeader.$_headerDesktop.hasClass( 'td-header-active' ) ) {
                                tdHeader.$_headerDesktopSticky.addClass( 'td-header-stop-transition' );
                            }

                            if ( ! tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                                tdHeader.doExtra();
                            }

                            tdHeader.$_headerDesktop.addClass( 'td-header-active' );
                            tdHeader.$_headerDesktopSticky.removeClass( 'td-header-active' );
                        }
                    }
                }
            }




            // Run only in composer - in iframe
            if ( window.parent === window.top  && 'undefined' !== typeof window.parent.tdcSidebar ) {

                var activeHeaderSelector = tdHeader.getActiveHeaderSelector();

                if ( 'undefined' === typeof tdHeader.previousActiveHeaderSelector || activeHeaderSelector !== tdHeader.previousActiveHeaderSelector ) {

                    tdHeader.previousActiveHeaderSelector = activeHeaderSelector;

                    window.parent.tdcSidebar.$_tdcZone.find( '.tdc-zone' ).removeClass( 'tdc-zone-active' );

                    if ( 'undefined' !== typeof tdHeader.timeoutHeaderTemplate ) {
                        clearTimeout( tdHeader.timeoutHeaderTemplate );
                    }

                    tdHeader.timeoutHeaderTemplate = setTimeout(function() {
                        window.parent.tdcSidebar.$_tdcZone.find( '.tdc-zone[data-type="' + activeHeaderSelector + '"]' ).addClass( 'tdc-zone-active' );

                        if ( activeHeaderSelector.indexOf( 'sticky' ) < 0 ) {
                            window.parent.tdcSidebar.setForcedHeaderZone( undefined );
                        }




                        if ( tdHeader._isMobile ) {

                            if ( tdHeader.$_headerMobileSticky.hasClass( 'tdc-zone-sticky-active' ) ) {
                                tdHeader.$_headerMobileSticky.removeClass('tdc-zone-sticky-invisible');
                            } else if ( tdHeader.$_headerMobileSticky.hasClass( 'tdc-zone-sticky-inactive' ) ) {
                                tdHeader.$_headerMobileSticky.addClass('tdc-zone-sticky-invisible');
                            }

                        } else {

                            if ( tdHeader.$_headerDesktopSticky.hasClass( 'tdc-zone-sticky-active' ) ) {
                                tdHeader.$_headerDesktopSticky.removeClass('tdc-zone-sticky-invisible');
                            } else if ( tdHeader.$_headerDesktopSticky.hasClass( 'tdc-zone-sticky-inactive' ) ) {
                                tdHeader.$_headerDesktopSticky.addClass('tdc-zone-sticky-invisible');
                            }
                        }


                    }, 250);
                }
            }

            tdHeader._previousScrollPosition = tdEvents.scroll_window_scrollTop;
        },


        scroll: function() {
            tdHeader.computeItems();
        },

        resize: function() {

            tdHeader.checkSizes();
            tdHeader.computeItems();
        },

        checkSizes: function() {

            if ( ! tdHeader._isInitialized ) {
                return;
            }

            tdHeader._headerDesktopHeight = tdHeader.$_headerDesktop.outerHeight( true );
            tdHeader._headerDesktopStickyHeight = tdHeader.$_headerDesktopSticky.outerHeight( true );
            tdHeader._headerMobileHeight = tdHeader.$_headerMobile.outerHeight( true );
            tdHeader._headerMobileStickyHeight = tdHeader.$_headerMobileSticky.outerHeight( true );

            var dataStickyOffset = tdHeader.$_headerDesktopSticky.find( '.tdc_zone:first' ).data( 'sticky-offset' );
            if ( 'undefined' !== typeof dataStickyOffset ) {
                tdHeader._headerDesktopStickyGap = dataStickyOffset;
            }

            tdHeader._isMobile = tdEvents.window_innerWidth < 768;
        },

        getFixedHeaderHeight: function() {

            if ( ! tdHeader._isInitialized ) {
                return;
            }

            tdHeader.checkSizes();

            if ( tdHeader._isMobile ) {
                return tdHeader._headerMobileHeight;
            }
            return tdHeader._headerDesktopHeight;
        },


        getActiveHeaderSelector: function() {

            if ( ! tdHeader._isInitialized ) {
                return;
            }

            if ( tdHeader._isMobile ) {
                if ( tdHeader.$_headerMobileSticky.hasClass( 'td-header-active' ) ) {
                    return tdHeader._selectorDataHeaderMobileSticky;
                }
                return tdHeader._selectorDataHeaderMobile;
            }

            if ( tdHeader.$_headerDesktopSticky.hasClass( 'td-header-active' ) ) {
                return tdHeader._selectorDataHeaderDesktopSticky;
            }
            return tdHeader._selectorDataHeaderDesktop;
        },


        doExtra: function() {

            if ( 'undefined' !== typeof window.tdbSearch.hideAllItems ) {
                window.tdbSearch.hideAllItems();
            }
        }

    };

    jQuery(window).load( function() {

        tdHeader.init();
        tdHeader.resize();
    });

})();
/**
 * updates the view counter thru ajax
 */

/* global jQuery:{} */
/* global td_ajax_url:string */

var tdAjaxCount = {};

(function(){

    'use strict';

    tdAjaxCount = {

        //td_get_views_counts_ajax : function( page_type, array_ids ) {
        tdGetViewsCountsAjax : function( postType, arrayIds ) {

            //what function to call based on postType
            var pageTypeAction = 'td_ajax_get_views';//postType = page
            if ( 'post' === postType ) {
                pageTypeAction = 'td_ajax_update_views';
            }

            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                cache: true,
                data: {
                    action: pageTypeAction,
                    td_post_ids: arrayIds
                },
                success: function( data, textStatus, XMLHttpRequest ) {
                    var tdAjaxPostCounts = jQuery.parseJSON( data );//get the return dara

                    //check the return var to be object
                    if ( tdAjaxPostCounts instanceof Object ) {
                        //alert('value is Object!');

                        //iterate throw the object
                        jQuery.each( tdAjaxPostCounts, function( idPost, value ) {
                            //alert(id_post + ": " + value);

                            //this is the count placeholder in witch we write the post count
                            var currentPostCount = '.td-nr-views-' + idPost;

                            jQuery( currentPostCount ).html( value );
                            //console.log(current_post_count + ': ' + value);
                        });
                    }
                },
                error: function( MLHttpRequest, textStatus, errorThrown ) {
                    //console.log(errorThrown);
                }
            });
        }
    };
})();

/*
 td_video_playlist.js
 v1.1
 */


/* global jQuery:{} */
/* global YT:{} */
/* global tdDetect:{} */
/* global $f:{} */

/* jshint -W069 */
/* jshint -W116 */

var tdYoutubePlayers = {};
var tdVimeoPlayers = {};

// !!!! this ready hook function must be moved from here
jQuery().ready(function() {

    'use strict';

    tdYoutubePlayers.init();
    tdVimeoPlayers.init();
});



(function() {

    'use strict';


    // the youtube list players (the init() method should be called before using the list)
    tdYoutubePlayers = {

        // the part name of the player id (they will be ex 'player_youtube_1', 'player_youtube_1', 'player_youtube_2', ...)
        tdPlayerContainer: 'player_youtube',

        // the internal list
        players: [],


        // the initialization of the youtube list players
        init: function() {

            var jqWrapperPlaylistPlayerYoutube = jQuery( '.td_wrapper_playlist_player_youtube' );

            for ( var i = 0; i < jqWrapperPlaylistPlayerYoutube.length; i++ ) {

                var jqPlayerWrapper = jQuery( jqWrapperPlaylistPlayerYoutube[ i ] ),
                    youtubePlayer = tdYoutubePlayers.addPlayer( jqPlayerWrapper),
                    playerId = youtubePlayer.tdPlayerContainer;

                jqPlayerWrapper.parent().find( '.td_youtube_control').data( 'player-id', playerId );

                var videoYoutubeElements = jqPlayerWrapper.parent().find( '.td_click_video_youtube');
                for ( var j = 0; j < videoYoutubeElements.length; j++ ) {
                    jQuery( videoYoutubeElements[ j ] ).data( 'player-id', playerId );

                    if ( j + 1 < videoYoutubeElements.length) {
                        jQuery( videoYoutubeElements[ j ] ).data( 'next-video-id', jQuery(videoYoutubeElements[ j + 1 ] ).data( 'video-id' ) );
                    } else {
                        jQuery( videoYoutubeElements[ j ] ).data( 'next-video-id', jQuery(videoYoutubeElements[0]).data( 'video-id' ) );
                    }
                }


                if ( '1' == jqPlayerWrapper.data( 'autoplay' ) ) {
                    youtubePlayer.autoplay = 1;
                }

                var firstVideo = jqPlayerWrapper.data( 'first-video' );

                if ( '' !== firstVideo ) {
                    youtubePlayer.tdPlaylistIdYoutubeVideoRunning = firstVideo;
                    youtubePlayer.playVideo( firstVideo );
                }
            }

            //click on a youtube movie
            jQuery( '.td_click_video_youtube' ).on( 'click', function(){

                var videoId = jQuery( this ).data( 'video-id' ),
                    playerId = jQuery( this ).data( 'player-id' );

                if ( undefined !== playerId && '' !== playerId && undefined !== videoId && '' !== videoId ) {
                    tdYoutubePlayers.operatePlayer( playerId, 'play', videoId );
                }
            });



            //click on youtube play control
            jQuery( '.td_youtube_control' ).on( 'click', function(){

                var playerId = jQuery( this ).data( 'player-id' );

                if ( undefined !== playerId && '' !== playerId ) {
                    if ( jQuery( this ).hasClass( 'td-sp-video-play' ) ){
                        tdYoutubePlayers.operatePlayer( playerId, 'play' );
                    } else {
                        tdYoutubePlayers.operatePlayer( playerId, 'pause' );
                    }
                }
            });
        },


        addPlayer: function( jqPlayerWrapper ) {

            var containerId = tdYoutubePlayers.tdPlayerContainer + '_' + tdYoutubePlayers.players.length,
                tdPlayer = tdYoutubePlayers.createPlayer( containerId, jqPlayerWrapper );

            tdYoutubePlayers.players.push( tdPlayer );

            return tdPlayer;
        },

        operatePlayer: function( playerId, option, videoId ) {
            for ( var i = 0; i < tdYoutubePlayers.players.length; i++ ) {
                if (tdYoutubePlayers.players[i].tdPlayerContainer == playerId ) {

                    var youtubePlayer = tdYoutubePlayers.players[ i ];

                    // This status is necessary just for mobile
                    youtubePlayer.playStatus();

                    if ( 'play' === option ) {

                        youtubePlayer.autoplay = 1;

                        if ( undefined === videoId ) {
                            youtubePlayer.playerPlay();
                        } else {
                            youtubePlayer.playVideo(videoId);
                        }
                    } else if ( 'pause' == option ) {
                        tdYoutubePlayers.players[i].playerPause();
                    }
                    break;
                }
            }
        },


        // create and return the youtube player object
        createPlayer: function( containerId, jqPlayerWrapper ) {

            var youtubePlayer = {

                tdYtPlayer: '',

                tdPlayerContainer: containerId,

                autoplay: 0,

                tdPlaylistIdYoutubeVideoRunning: '',

                jqTDWrapperVideoPlaylist: jqPlayerWrapper.closest( '.td_wrapper_video_playlist' ),

                jqPlayerWrapper: jqPlayerWrapper,

                jqControlPlayer: '',

                _videoId: '',

                playVideo: function( videoId ) {

                    youtubePlayer._videoId = videoId;

                    if ( 'undefined' === typeof( YT ) || 'undefined' === typeof( YT.Player ) ) {

                        window.onYouTubePlayerAPIReady = function () {

                            for ( var i = 0; i < tdYoutubePlayers.players.length; i++ ) {
                                tdYoutubePlayers.players[ i ].loadPlayer( );
                            }
                        };

                        jQuery.getScript('https://www.youtube.com/player_api').done(function( script, textStatus ) {
                            //alert(textStatus);
                        });
                    } else {
                        youtubePlayer.loadPlayer( videoId );
                    }
                },


                loadPlayer: function (videoId) {

                    var videoIdToPlay = youtubePlayer._videoId;

                    if ( undefined !== videoId ) {
                        videoIdToPlay = videoId;
                    }

                    if ( undefined === videoIdToPlay ) {
                        return;
                    }

                    //container is here in case we need to add multiple players on page
                    youtubePlayer.tdPlaylistIdYoutubeVideoRunning = videoIdToPlay;

                    var current_video_name = window.td_youtube_list_ids['td_' + youtubePlayer.tdPlaylistIdYoutubeVideoRunning]['title'],
                        current_video_time = window.td_youtube_list_ids['td_' + youtubePlayer.tdPlaylistIdYoutubeVideoRunning]['time'];

                    //remove focus from all videos from playlist
                    youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_click_video_youtube' ).removeClass( 'td_video_currently_playing' );

                    //add focus class on current playing video
                    youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_' + videoIdToPlay ).addClass( 'td_video_currently_playing' );

                    //ading the current video playing title and time to the control area
                    youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_current_video_play_title_youtube' ).html( current_video_name );
                    youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_current_video_play_time_youtube' ).html( current_video_time );

                    youtubePlayer.jqPlayerWrapper.html('<div id=' + youtubePlayer.tdPlayerContainer + '></div>');

                    youtubePlayer.jqControlPlayer = youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_youtube_control' );

                    youtubePlayer.tdYtPlayer = new YT.Player(youtubePlayer.tdPlayerContainer, {//window.myPlayer = new YT.Player(container, {
                        playerVars: {
                            //modestbranding: 1,
                            //rel: 0,
                            //showinfo: 0,
                            autoplay: youtubePlayer.autoplay
                        },
                        height: '100%',
                        width: '100%',
                        videoId: videoIdToPlay,
                        events: {
                            'onStateChange': youtubePlayer.onPlayerStateChange
                        }
                    });
                },


                onPlayerStateChange: function (event) {
                    if (event.data === YT.PlayerState.PLAYING) {

                        //add pause to playlist control
                        youtubePlayer.pauseStatus();

                    } else if (event.data === YT.PlayerState.ENDED) {

                        youtubePlayer.playStatus();

                        //if a video has ended then make auto play = 1; This is the case when the user set autoplay = 0 but start watching videos
                        youtubePlayer.autoplay = 1;


                        //get the next video
                        var nextVideoId = '',
                            tdVideoCurrentlyPlaying = youtubePlayer.jqTDWrapperVideoPlaylist.find( '.td_video_currently_playing' );

                        if ( tdVideoCurrentlyPlaying.length ) {
                            var nextSibling = jQuery( tdVideoCurrentlyPlaying ).next( '.td_click_video_youtube' );
                            if ( nextSibling.length ) {
                                nextVideoId = jQuery( nextSibling ).data( 'video-id' );
                            }
                            //else {
                            //    var firstSibling = jQuery(tdVideoCurrentlyPlaying).siblings( '.td_click_video_youtube:first' );
                            //    if ( firstSibling.length ) {
                            //        nextVideoId = jQuery( firstSibling ).data( 'video-id' );
                            //    }
                            //}
                        }

                        if ('' !== nextVideoId) {
                            youtubePlayer.playVideo(nextVideoId);
                        }

                    } else if (YT.PlayerState.PAUSED) {
                        //add play to playlist control
                        youtubePlayer.playStatus();
                    }
                },

                //tdPlaylistYoutubeStopVideo: function () {
                //    youtubePlayer.tdYtPlayer.stopVideo();
                //},

                playerPlay: function () {
                    youtubePlayer.tdYtPlayer.playVideo();
                },

                playerPause: function () {
                    youtubePlayer.tdYtPlayer.pauseVideo();
                },

                playStatus: function() {
                    youtubePlayer.jqControlPlayer.removeClass( 'td-sp-video-pause' ).addClass( 'td-sp-video-play' );
                },

                pauseStatus: function() {
                    youtubePlayer.jqControlPlayer.removeClass( 'td-sp-video-play' ).addClass( 'td-sp-video-pause' );
                }
            };

            return youtubePlayer;
        }
    };




    // the vimeo list players (to use it, the init() method should be called)
    // !Important. Usually, because of froogaloop implementation, there couldn't be multiple vimeo players running all at once on page.
    tdVimeoPlayers = {

        // the part name of the player id (they will be ex 'player_vimeo_0', 'player_vimeo_1', 'player_vimeo_2', ...)
        tdPlayerContainer: 'player_vimeo',

        // the internal list
        players: [],

        // Set to true at the first autoplayed player created
        // It's used to avoid the autoplay setting of the next players (multiple players can't have autoplay = 1 )
        existingAutoplay: false,


        // init the vimeo list players
        init: function() {
            var jqTDWrapperPlaylistPlayerVimeo = jQuery( '.td_wrapper_playlist_player_vimeo' );

            for ( var i = 0; i < jqTDWrapperPlaylistPlayerVimeo.length; i++ ) {
                var vimeoPlayer = tdVimeoPlayers.addPlayer( jQuery(jqTDWrapperPlaylistPlayerVimeo[i]) );
                if ( 0 !== vimeoPlayer.autoplay ) {
                    tdVimeoPlayers.existingAutoplay = true;
                }
            }


            //click on a vimeo
            jQuery( '.td_click_video_vimeo' ).on( 'click', function(){

                var videoId = jQuery( this ).data( 'video-id' ),
                    playerId = jQuery( this ).data( 'player-id' );

                if ( undefined !== playerId && '' !== playerId && undefined !== videoId && '' !== videoId ) {
                    tdVimeoPlayers.operatePlayer( playerId, 'play', videoId );
                }
            });


            //click on vimeo play control
            jQuery( '.td_vimeo_control' ).on( 'click', function(){

                var playerId = jQuery( this ).data( 'player-id' );

                if ( undefined !== playerId && '' !== playerId ) {
                    if ( jQuery( this ).hasClass( 'td-sp-video-play' ) ){
                        tdVimeoPlayers.operatePlayer( playerId, 'play' );
                    } else {
                        tdVimeoPlayers.operatePlayer( playerId, 'pause' );
                    }
                }
            });
        },


        // create and add player to the vimeo list players
        addPlayer: function( jqPlayerWrapper ) {
            var playerId = tdVimeoPlayers.tdPlayerContainer + '_' + tdVimeoPlayers.players.length,
                vimeoPlayer = tdVimeoPlayers.createPlayer(  playerId, jqPlayerWrapper );

            jqPlayerWrapper.parent().find( '.td_vimeo_control').data( 'player-id', playerId );

            var vimeoVideoElements = jqPlayerWrapper.parent().find( '.td_click_video_vimeo');
            for ( var j = 0; j < vimeoVideoElements.length; j++ ) {
                jQuery( vimeoVideoElements[ j ] ).data( 'player-id', playerId );

                if ( j + 1 < vimeoVideoElements.length ) {
                    jQuery( vimeoVideoElements[ j ] ).data( 'next-video-id', jQuery( vimeoVideoElements[ j + 1 ] ).data( 'video-id' ) );
                } else {
                    jQuery( vimeoVideoElements[ j ] ).data( 'next-video-id', jQuery( vimeoVideoElements[ 0 ] ).data( 'video-id' ) );
                }
            }

            if ( '1' == jqPlayerWrapper.data( 'autoplay' ) ) {
                vimeoPlayer.autoplay = 1;
            }

            var firstVideo = jqPlayerWrapper.data( 'first-video' );

            if ( undefined !== firstVideo && '' !== firstVideo ) {
                vimeoPlayer.createPlayer( firstVideo );
            }

            tdVimeoPlayers.players.push( vimeoPlayer );

            return vimeoPlayer;
        },


        // play or pause a video or the current (first) video
        operatePlayer: function( playerId, option, videoId ) {
            for ( var i = 0; i < tdVimeoPlayers.players.length; i++ ) {

                if ( tdVimeoPlayers.players[ i ].playerId == playerId ) {

                    var vimeoPlayer = tdVimeoPlayers.players[ i ];

                    if ( 'play' === option ) {

                        vimeoPlayer.autoplay = 1;

                        if ( undefined !== videoId ) {

                            // the existing autoplay is reset to allow autoplay when we have videoId (a video from the playlist was clicked)
                            tdVimeoPlayers.existingAutoplay = false;

                            vimeoPlayer.createPlayer( videoId );
                        } else {
                            vimeoPlayer.playerPlay();
                        }

                    } else if ( 'pause' === option ) {
                        vimeoPlayer.playerPause();
                    }

                    break;
                }
            }
        },


        // create and return the vimeo player object
        createPlayer: function( playerId, jqPlayerWrapper ) {

            var vimeoPlayer = {

                playerId: playerId,

                // the jq td playlist wrapper ( the player and the playlist)
                jqTDWrapperVideoPlaylist: jqPlayerWrapper.closest( '.td_wrapper_video_playlist' ),

                // the jq player wrapper
                jqPlayerWrapper: jqPlayerWrapper,

                currentVideoPlaying : '', // not used for the moment

                player: '',//a copy of the vimeo player : needed when playing or pausing the vimeo pleyer from the playlist control

                // main control button of the player
                jqControlPlayer: '',

                autoplay: 0,//autoplay

                createPlayer: function ( videoId ) {
                    if ( '' !== videoId ) {

                        this.currentVideoPlaying = videoId;

                        var autoplay = '',
                            current_video_name = window.td_vimeo_list_ids['td_' + videoId]['title'],
                            current_video_time = window.td_vimeo_list_ids['td_' + videoId]['time'];

                        //remove focus from all videos from playlist
                        vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_click_video_vimeo' ).removeClass( 'td_video_currently_playing' );

                        //add focus class on current playing video
                        vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_' + videoId ).addClass( 'td_video_currently_playing' );

                        //ading the current video playing title and time to the control area
                        vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_current_video_play_title_vimeo' ).html( current_video_name );
                        vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_current_video_play_time_vimeo' ).html( current_video_time );

                        vimeoPlayer.jqControlPlayer = vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_vimeo_control' );

                        //check autoplay
                        if ( !tdVimeoPlayers.existingAutoplay && 0 !== vimeoPlayer.autoplay ) {
                            autoplay = '&autoplay=1';

                            if ( tdDetect.isMobileDevice ) {
                                vimeoPlayer.playStatus();
                            } else {
                                vimeoPlayer.pauseStatus();
                            }
                        } else {
                            vimeoPlayer.playStatus();
                        }
                        vimeoPlayer.jqPlayerWrapper.html( '<iframe id="' + vimeoPlayer.playerId + '" src="https://player.vimeo.com/video/' + videoId + '?api=1&player_id=' + vimeoPlayer.playerId + '' + autoplay + '"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' );
                        vimeoPlayer.createVimeoObjectPlayer( jQuery );
                    }
                },

                createVimeoObjectPlayer : function( $ ) {
                    var player = '',
                        iframe = $( '#' + vimeoPlayer.playerId );

                    if ( iframe.length ) {
                        player = $f( iframe[0] );

                        //a copy of the vimeo player : needed when playing or pausing the vimeo pleyer from the playlist control
                        vimeoPlayer.player = player;

                        // When the player is ready, add listeners for pause, finish, and playProgress
                        player.addEvent( 'ready', function() {

                            player.addEvent( 'play', function( data ) {
                                vimeoPlayer.pauseStatus();
                                vimeoPlayer.autoplay = 1;
                            });

                            player.addEvent( 'pause', function( data ) {
                                vimeoPlayer.playStatus();
                            });

                            player.addEvent( 'finish', function( data ) {

                                var nextVideoId = '',
                                    tdVideoCurrentlyPlaying = vimeoPlayer.jqTDWrapperVideoPlaylist.find( '.td_video_currently_playing' );

                                if ( tdVideoCurrentlyPlaying.length ) {
                                    var nextSibling = jQuery( tdVideoCurrentlyPlaying ).next( '.td_click_video_vimeo' );
                                    if ( nextSibling.length ) {
                                        nextVideoId = jQuery( nextSibling ).data( 'video-id' );
                                    }
                                }

                                if ( '' !== nextVideoId ) {
                                    vimeoPlayer.createPlayer( nextVideoId );

                                    if ( tdDetect.isMobileDevice ) {
                                        vimeoPlayer.playStatus();
                                    } else {
                                        vimeoPlayer.pauseStatus();
                                    }
                                } else {
                                    vimeoPlayer.playStatus();
                                }
                            });
                        });
                    }
                },

                // play the current video
                playerPlay: function () {
                    vimeoPlayer.autoplay = 1;
                    vimeoPlayer.player.api( 'play' );
                },

                // pause the current video
                playerPause: function () {
                    vimeoPlayer.player.api( 'pause' );
                },

                // change status to 'play'
                playStatus: function() {
                    vimeoPlayer.jqControlPlayer.removeClass( 'td-sp-video-pause' ).addClass( 'td-sp-video-play' );
                },

                // change status to 'pause'
                pauseStatus: function() {
                    vimeoPlayer.jqControlPlayer.removeClass( 'td-sp-video-play' ).addClass( 'td-sp-video-pause' );
                }
            };

            return vimeoPlayer;
        }
    };

})();
/*
 td_slide.js
 */

"use strict";

//call function to resize the smartlist on ready (for safary)
jQuery(window).load(function() {
    td_resize_smartlist_sliders_and_update();
});

//call function to resize the smartlist on ready
jQuery().ready(function() {
    td_resize_smartlist_sliders_and_update();
});



//function to resize the height of the smartlist slide
function td_resize_smartlist_slides(args) {
    var slide_displayd = args.currentSlideNumber;


    //console.log(args.sliderObject[0]);
    //console.log(args.data.obj[0]);

    var current_slider = jQuery(args.data.obj[0]).attr("id");

    if(!tdDetect.isIe8) {
        jQuery("#" + current_slider).css("overflow", "none");
        jQuery("#" + current_slider + " .td-item").css("overflow", "visible");
    }

    var setHeight = 0;
    setHeight = jQuery("#" + current_slider + "_item_" + slide_displayd).outerHeight(true);


    jQuery("#" + current_slider + ", #" + current_slider + " .td-slider").css({
        height: setHeight
    });
}





//function to resize and update the height of the smartlist slide
function td_resize_smartlist_sliders_and_update() {
    jQuery(document).find('.td-smart-list-slider').each(function() {
        var current_slider = jQuery(this).attr("id");

        if(!tdDetect.isIe8) {
            jQuery("#" + current_slider).css("overflow", "none");
            jQuery("#" + current_slider + " .td-item").css("overflow", "visible");
        }

        var setHeight = 0;
        setHeight = jQuery("#" + current_slider + "_item_" + td_history.get_current_page("slide")).outerHeight(true);

        jQuery("#" + current_slider + ", #" + current_slider + " .td-slider").css({
            height: setHeight
        });

        if(tdDetect.isAndroid) {
            setTimeout(function () {
                jQuery("#" + current_slider).iosSlider("update");
            }, 2000);
        }
    });
}


//function to resize the height of the normal slide
function td_resize_normal_slide(args) {
    var slide_displayd = 0;//args.currentSlideNumber;

    var current_slider = jQuery(args.data.obj[0]).attr("id");

    //get window width
    var window_wight = td_get_document_width();

    if (!tdDetect.isIe8) {
        jQuery("#" + current_slider).css("overflow", "none");
        jQuery("#" + current_slider + " .td-item").css("overflow", "visible");
    }

    var setHeight = 0;
    var slide_outer_width = jQuery("#" + current_slider + "_item_" + slide_displayd).outerWidth(true);

    //only for android, width of the screen to start changing the height of the slide
    var max_wight_resize = 780;
    if(tdDetect.isAndroid) {
        max_wight_resize = 1000;
    }

    if (window_wight < max_wight_resize && !tdDetect.isIpad) {//problem because we cannot get an accurate page width
        if(slide_outer_width > 300) {
            setHeight = slide_outer_width * 0.5;
        } else {
            setHeight = slide_outer_width;
        }

        //console.log(window_wight);
        jQuery("#" + current_slider + ", #" + current_slider + " .td-slider, #" + current_slider + " .td-slider .td-module-thumb").css({
            height: setHeight
        });
    }

}



//function to resize and update the height of the slide for normal sliders
function td_resize_normal_slide_and_update(args) {


    //console.log('resize 2');
    var slide_displayd = 0;//args.currentSlideNumber;

    var current_slider = jQuery(args.data.obj[0]).attr("id");

    //get window width
    var window_wight = td_get_document_width();

    if(!tdDetect.isIe8) {
        jQuery("#" + current_slider).css("overflow", "none");
        jQuery("#" + current_slider + " .td-item").css("overflow", "visible");
    }

    var setHeight = 0;
    var slide_outer_width = jQuery("#" + current_slider + "_item_" + slide_displayd).outerWidth(true);

    //only for android, width of the screen to start changing the height of the slide
    var max_wight_resize = 780;
    if(tdDetect.isAndroid) {
        max_wight_resize = 1000;
    }

    if (window_wight < max_wight_resize && !tdDetect.isIpad) {//problem because we cannot get an accurate page width
        if(slide_outer_width > 300) {
            setHeight = slide_outer_width * 0.5;
        } else {
            setHeight = slide_outer_width;
        }

        //console.log(window_wight);
        jQuery("#" + current_slider + ", #" + current_slider + " .td-slider, #" + current_slider + " .td-slider .td-module-thumb").css({
            height: setHeight
        });

        setTimeout(function () {
            jQuery("#" + current_slider).iosSlider("update");



        }, 2000);

    }
}
/**
 * Created by tagdiv on 16.02.2015.
 */


/* global tdViewport:{} */
/* global jQuery:{} */

var tdPullDown = {};

( function(){

    'use strict';

    tdPullDown = {


        // - keeps internally the current interval index
        // - it's set at init()
        _view_port_interval_index : tdViewport.INTERVAL_INITIAL_INDEX,



        // - the list of items
        items: [],



        // - the item represents a pair of lists (a horizontal and a vertical one)
        // - to be initialized, every property with 'IT MUST BE SPECIFIED' is mandatory
        item: function item() {


            // OPTIONAL - here we store the block Unique ID. This enables us to delete the item via this id @see tdPullDown.deleteItem
            this.blockUid = '';

            // - the jquery object of the horizontal list.
            // IT MUST BE SPECIFIED.
            this.horizontal_jquery_obj = '';

            // - the jquery object of the vertical list.
            // IT MUST BE SPECIFIED
            this.vertical_jquery_obj = '';

            // - the jquery container object.
            // - it contains the horizontal and the vertical jquery objects
            // IT MUST BE SPECIFIED.
            this.container_jquery_obj = '';

            // - the css class of an horizontal element.
            // IT MUST BE SPECIFIED
            this.horizontal_element_css_class = '';

            // OPTIONAL - the css class to be added to the horizontal list when it has no items
            this.horizontal_no_items_css_class = '';

            // OPTIONAL - maximum width for the horizontal list
            this.horizontal_max_width = '';

            // the minimum no. of elements to be shown by the horizontal list
            // - IT CAN BE SPECIFIED
            this.minimum_elements = 1;



            // - the array of jquery elements whose widths must be excluded from the width of the container object
            // IT CAN BE SPECIFIED
            this.excluded_jquery_elements = [];

            // - the extra space of the horizontal jquery object occupied by the excluded jquery elements
            // - it's not initialized with 0 because widths of the elements can not be integer values
            // - now, it's set to 1px
            this._horizontal_extra_space = 1;



            // - the array of objects from the horizontal list
            this._horizontal_elements = [];

            // - the array of objects from the vertical list
            this._vertical_elements = [];



            // - the jquery object of the first ul container in the vertical list
            // - it is calculated as the first 'ul' of the vertical jquery object
            this._vertical_ul_jquery_obj = '';



            // - the outer width of the vertical top header (ex.'More')
            // - it's used to calculate if the last vertical element has enough space in the horizontal list,
            // without considering the vertical top header width
            this._vertical_jquery_obj_outer_width = 0;



            // flag used to mark the initialization item
            this._is_initialized = false;
        },




        /**
         * - function used to init the tdPullDown object
         * - it must be called before any item adding
         * - it initializes the _view_port_interval_index
         * - the items list is initialized
         */
        init: function() {

            tdPullDown._view_port_interval_index = tdViewport.getCurrentIntervalIndex();

            tdPullDown.items = [];
        },




        /**
         * - add an item to the item list and initialize it
         *
         * @param item The item to be added and initialized
         */
        add_item: function( item ) {

            // check to see if the item is ok
            if (item.vertical_jquery_obj.length !== 1) {
                throw 'item.vertical_jquery_obj is more or less than one: ' + item.vertical_jquery_obj.length;
            }
            if (item.horizontal_jquery_obj.length !== 1) {
                throw 'item.horizontal_jquery_obj is more or less than one: ' + item.horizontal_jquery_obj.length;
            }
            if (item.container_jquery_obj.length !== 1) {
                throw 'item.container_jquery_obj is more or less than one: ' + item.container_jquery_obj.length;
            }
            if (item.horizontal_element_css_class === '') {
                throw 'item.horizontal_element_css_class is empty';
            }

            // the item is added in the item list
            tdPullDown.items.push( item );

            // the item is initialized only once when it is added
            tdPullDown._initialize_item( item );

            //  the item is ready to be computed
            tdPullDown._compute_item( item );
        },


        /**
         * Deletes an item base on blockUid. Note that blockUid is optional (this library is also used outside of blocks)!
         * Make sure that you add blockUid to items that you expect to be deleted
         * @param blockUid
         */
        deleteItem: function(blockUid) {
            for (var cnt = 0; cnt < tdPullDown.items.length; cnt++) {
                if (tdPullDown.items[cnt].blockUid === blockUid) {
                    tdPullDown.items.splice(cnt, 1); // remove the item from the "array"
                    return true;
                }
            }
            return false;
        },


        /**
         * deletes an item but it also moves the dom elements to the main container. Used on the social share icons from the start end bottom of the article
         * @param blockUid
         */
        unloadItem: function (blockUid) {
            for (var cnt = 0; cnt < tdPullDown.items.length; cnt++) {
                if (tdPullDown.items[cnt].blockUid === blockUid) {
                    // move all the elements that are on the vertical list to the horizontal one
                    for ( var i = 0; i < tdPullDown.items[cnt]._vertical_elements.length; i++ ) {
                        var local_element = tdPullDown.items[cnt]._vertical_elements[i];
                        local_element.jquery_object.detach().appendTo( tdPullDown.items[cnt].horizontal_jquery_obj );
                    }
                    tdPullDown.deleteItem(blockUid); // delete the item also
                    return true;
                }
            }
            return false;
        },



        /**
         * - internal utility function used to initialize an item
         * - an item must be initialized only once
         * - every element having a specified css class is added in the horizontal list
         *
         * @param item {tdPullDown.item} The item to be initialized
         * @private
         */
        _initialize_item: function( item ) {

            // an item must be initialized only once
            if ( true === item._is_initialized ) {
                return;
            }


            //// the mandatory item properties are verified
            // @20/4/2016 - i've moved all the checks to add_item -ra
            //if ( ( '' === item.horizontal_jquery_obj ) ||
            //    ( '' === item.vertical_jquery_obj ) ||
            //    ( '' === item.container_jquery_obj ) ||
            //    ( '' === item.horizontal_element_css_class ) ) {
            //
            //    tdPullDown.log( 'Item can\' be initialized. It doesn\'t have all the mandatory properties' );
            //    return;
            //}


            // the jquery object of the first ul container in the vertical list is initialized
            item._vertical_ul_jquery_obj = item.vertical_jquery_obj.find( 'ul:first' );

            if ( 0 === item._vertical_ul_jquery_obj.length ) {

                tdPullDown.log( 'Item can\' be initialized. The vertical list doesn\'t have an \'ul\' container' );
                return;
            }


            // the elements of the horizontal jquery object, having a specified css class
            var elements = item.horizontal_jquery_obj.find( '.' + item.horizontal_element_css_class + ':visible' );

            var local_jquery_element = null;
            var local_object = null;

            // for each element an object is added in the horizontal list
            elements.each( function ( index, element ) {

                local_jquery_element = jQuery( element );

                // !!!! here we need a css class
                local_jquery_element.css( '-webkit-transition', 'opacity 0.2s' );
                local_jquery_element.css( '-moz-transition', 'opacity 0.2s' );
                local_jquery_element.css( '-o-transition', 'opacity 0.2s' );
                local_jquery_element.css( 'transition', 'opacity 0.2s' );

                local_jquery_element.css( 'opacity', '1' );


                // the cached object used to keep the jquery object and its outerWidth
                local_object = {

                    // the jquery element
                    jquery_object: local_jquery_element,

                    // the outer width including border
                    calculated_width: local_jquery_element.outerWidth( true )
                };

                // the horizontal list is populated
                item._horizontal_elements.push( local_object );
            });


            // the outer width of the vertical top header (ex.'More') is initialized
            item._vertical_jquery_obj_outer_width = item.vertical_jquery_obj.outerWidth( true );


            // by default, the vertical jquery object is hidden, being shown when at least one element is moved in it
            item.vertical_jquery_obj.css( 'display', 'none' );



            // the the extra space occupied by the horizontal jquery object is calculated
            var horizontal_jquery_obj_padding_left = item.horizontal_jquery_obj.css( 'padding-left' );
            if ( ( undefined !== horizontal_jquery_obj_padding_left ) && ( '' !== horizontal_jquery_obj_padding_left ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_padding_left.replace( 'px', '' ) );
            }

            var horizontal_jquery_obj_padding_right = item.horizontal_jquery_obj.css( 'padding-right' );
            if ( ( undefined !== horizontal_jquery_obj_padding_right ) && ( '' !== horizontal_jquery_obj_padding_right ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_padding_right.replace( 'px', '' ) );
            }


            var horizontal_jquery_obj_margin_left = item.horizontal_jquery_obj.css( 'margin-left' );
            if ( ( undefined !== horizontal_jquery_obj_margin_left ) && ( '' !== horizontal_jquery_obj_margin_left ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_margin_left.replace( 'px', '' ) );
            }

            var horizontal_jquery_obj_margin_right = item.horizontal_jquery_obj.css( 'margin-right' );
            if ( ( undefined !== horizontal_jquery_obj_margin_right ) && ( '' !== horizontal_jquery_obj_margin_right ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_margin_right.replace( 'px', '' ) );
            }


            var horizontal_jquery_obj_border_left = item.horizontal_jquery_obj.css( 'border-left' );
            if ( ( undefined !== horizontal_jquery_obj_border_left ) && ( '' !== horizontal_jquery_obj_border_left ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_border_left.replace( 'px', '' ) );
            }

            var horizontal_jquery_obj_border_right = item.horizontal_jquery_obj.css( 'border-right' );
            if ( ( undefined !== horizontal_jquery_obj_border_right ) && ( '' !== horizontal_jquery_obj_border_right ) ) {
                item._horizontal_extra_space += parseInt( horizontal_jquery_obj_border_right.replace( 'px', '' ) );
            }


            // the item is marked as initialized, being ready to be computed
            item._is_initialized = true;
        },




        /**
         * - internal utility function used to summarize width of the horizontal elements
         *
         * @param item {tdPullDown.item} The item whose horizontal list is processed
         * @returns {number}
         * @private
         */
        _get_horizontal_elements_width: function( item ) {

            var sum_width = 0;

            for ( var i = item._horizontal_elements.length - 1; i >= 0; i-- ) {
                sum_width += item._horizontal_elements[ i ].calculated_width;
            }
            return sum_width;
        },




        /**
         * - internal utility function used to reinitialize all items at the view resolution changing
         */
        _reinitialize_all_items: function() {

            for ( var i = tdPullDown.items.length - 1; i >= 0; i-- ) {
                tdPullDown._reinitialize_item( tdPullDown.items[ i ] );
            }
        },




        /**
         * - internal utility function used to reinitialize an item at the view resolution changing
         *
         * @param item The item being reinitialized
         */
        _reinitialize_item: function( item ) {

            // a not initialized item can't be reinitialized
            if ( false === item._is_initialized ) {
                return;
            }

            //  the flag is marked, so any further operation on this item is stopped
            item._is_initialized = false;

            // the html elements of the vertical list are all moved into the horizontal jquery object
            item.horizontal_jquery_obj.html( item.horizontal_jquery_obj.html() + item._vertical_ul_jquery_obj.html() );

            // the html content of the vertical list is cleared
            item._vertical_ul_jquery_obj.html( '' );

            // the horizontal list is empty initialized
            item._horizontal_elements = [];

            // the vertical list is empty initialized
            item._vertical_elements = [];

            // the extra space is initialized
            item._horizontal_extra_space = 1;

            // the item is ready to be initialized again
            tdPullDown._initialize_item( item );
        },




        /**
         * - an internal function used to move elements from the horizontal to the vertical list and vice versa, in according with
         * the space for horizontal elements.
         * - it's called every time at the viewport resize, when the space for horizontal elements is modified
         *
         * @param item - the item being computed
         * @private
         */
        _compute_item: function( item ) {

            // the item must be initialized first
            if ( false === item._is_initialized ) {
                return;
            }


            // the horizontal header margin is set 0 and the horizontal space is computing without its margin
            // @see tdPullDown._prepare_horizontal_header
            tdPullDown._prepare_horizontal_header( item, true );



            // - the space where horizontal elements lie
            // - it is the container width minus any extra horizontal space
            var space_for_horizontal_elements = 0;

            // the object container width
            //var container_jquery_width = item.container_jquery_obj.css( 'width' );
            var container_jquery_width = item.container_jquery_obj.width();

            if ( ( undefined !== container_jquery_width ) && ( '' !== container_jquery_width ) ) {

                // console.log(container_jquery_width);
                // console.log(item.horizontal_max_width);

                //space_for_horizontal_elements = container_jquery_width.replace( 'px', '' );
                space_for_horizontal_elements = container_jquery_width;

                if ( item.horizontal_max_width !== '' ) {

                    //var a = parseInt(container_jquery_width.replace( 'px', '' ));
                    var horizontal_max_width = parseInt(item.horizontal_max_width.replace( 'px', '' ));

                    // if we have a max width and the object container width is bigger than the max width set the space for new horizontal elements by the max width
                    if ( container_jquery_width > horizontal_max_width ) {
                        // console.log( 'container_jquery_width: ' + container_jquery_width);
                        // console.log( 'horizontal_max_width: ' + horizontal_max_width);
                        // console.log( 'cont width is bigger than max width' );
                        space_for_horizontal_elements = horizontal_max_width;
                    }
                }

                // then this space is reduced by the widths of the excluded elements
                for ( var i = item.excluded_jquery_elements.length - 1; i >= 0; i-- ) {
                    space_for_horizontal_elements -= item.excluded_jquery_elements[ i ].outerWidth( true );
                    //console.log(item.excluded_jquery_elements[ i ].outerWidth( true ));
                }
            }


            // if the vertical list is empty, the space for horizontal elements does not contain the width of the vertical head list
            if ( item._vertical_elements.length > 0 ) {
                space_for_horizontal_elements -= item._vertical_jquery_obj_outer_width;
            }

            // the space occupied by the horizontal elements is removed
            space_for_horizontal_elements -= tdPullDown._get_horizontal_elements_width( item );

            // the horizontal extra space is used to add an extra gap when the width of one element or a js math computation does a not integer value
            space_for_horizontal_elements -= item._horizontal_extra_space;


            // the current element being moved between the lists
            var local_current_element;


            // if there's not enough space for the horizontal elements, then the last of them are moved to the vertical list
            while ( space_for_horizontal_elements < 0 ) {

                // if there's specified a minimum number of horizontal elements, this must be considered
                if ( ( item.minimum_elements !== 0 ) && ( item._horizontal_elements.length <= item.minimum_elements ) ) {

                    // all elements are moved to the vertical list
                    tdPullDown._make_all_elements_vertical( item );

                    // the horizontal header margin is set before return
                    tdPullDown._prepare_horizontal_header( item );

                    // the following checks are not more eligible to do
                    return;

                } else {

                    // If the vertical list does not contain any elements yet,
                    // the space for horizontal elements is minimized by the vertical top header width
                    if ( 0 === item._vertical_elements.length ) {
                        space_for_horizontal_elements -= item._vertical_jquery_obj_outer_width;
                    }

                    local_current_element = tdPullDown._make_element_vertical( item );
                    space_for_horizontal_elements += local_current_element.calculated_width;
                }
            }



            // This is the case when there's specified a no. of minimum horizontal elements and the horizontal list is empty.
            // If the following conditions are accomplished the horizontal list is refilled with elements from the vertical list
            //
            //  - if there's specified a no. of minimum horizontal elements
            //  - if there is no horizontal elements
            //  - if there are vertical elements
            //  - if there's enough horizontal space for the first vertical element
            if ( ( 0 !== item.minimum_elements ) &&
                ( 0 === item._horizontal_elements.length ) &&
                ( item._vertical_elements.length > 0 ) &&
                ( space_for_horizontal_elements >= item._vertical_elements[ 0 ].calculated_width ) ) {

                // the necessary space needed for the minimum no. of horizontal elements
                var local_necessary_space = 0;

                for ( var j = 0; ( j < item.minimum_elements ) && ( j < item._vertical_elements.length ); j++ ) {
                    local_necessary_space += item._vertical_elements[ j ].calculated_width;
                }

                // the necessary space really occupied by the minimum no. of horizontal elements
                var local_space = 0;
                var local_minimum_elements = item.minimum_elements;

                while ( ( local_minimum_elements > 0 ) &&
                    ( item._vertical_elements.length > 0 ) &&
                    ( space_for_horizontal_elements >= local_necessary_space ) ) {

                    local_current_element = tdPullDown._make_element_horizontal( item );

                    if ( null !== local_current_element ) {
                        local_space += local_current_element.calculated_width;
                        local_minimum_elements--;
                    } else {

                        // the horizontal header margin is set before return
                        tdPullDown._prepare_horizontal_header( item );

                        return;
                    }

                }
                space_for_horizontal_elements -= local_space;
            }


            // It's the case when there isn't specified a no. of minimum horizontal elements or it is specified and the
            // horizontal list is not empty, and in the same time there's enough horizontal space for more elements
            while ( ( ( item._horizontal_elements.length > 0 ) || ( 0 === item._horizontal_elements.length && 0 === item.minimum_elements ) ) &&
                ( item._vertical_elements.length > 0 ) &&
                ( space_for_horizontal_elements >= item._vertical_elements[ 0 ].calculated_width ) ) {

                local_current_element = tdPullDown._make_element_horizontal( item );

                if ( null !== local_current_element ) {
                    space_for_horizontal_elements -= local_current_element.calculated_width;
                } else {

                    // the horizontal header margin is set before return
                    tdPullDown._prepare_horizontal_header( item );

                    return;
                }
            }

            // if the vertical list contains just one element, the horizontal space for it must be calculated without considering the vertical top header width (ex.'More')
            if ( ( 1 === item._vertical_elements.length ) &&
                ( space_for_horizontal_elements + item._vertical_jquery_obj_outer_width >= item._vertical_elements[ 0 ].calculated_width ) ) {
                tdPullDown._make_element_horizontal( item );
            }

            // add the no items in horizontal list class
            tdPullDown._add_no_items_class(item);

            // the horizontal header margin is set before return
            tdPullDown._prepare_horizontal_header( item );
        },


        /**
         * - add margin to the element with '.block-title' css class, to keep the vertical_jquery_obj not overlapping over it when
         * there are no horizontal elements and it is too wide [more strings in name]
         * @param item tdPullDown.item
         * @param clear_margin boolean True to just clear margin, or false to check the horizontal elements length and then set the margin
         * @private
         */
        _prepare_horizontal_header: function _prepare_horizontal_header( item, clear_margin ) {
            var block_title_jquery_obj = item.horizontal_jquery_obj.parent().siblings( '.block-title:first' );

            if ( 1 === block_title_jquery_obj.length ) {
                var content_element = block_title_jquery_obj.find( 'span:first' );

                if ( 1 === content_element.length ) {

                    if ( 'undefined' !== typeof( clear_margin ) && true === clear_margin ) {
                        content_element.css( 'margin-right', 0 );
                    } else {
                        if ( 0 === item._horizontal_elements.length ) {
                            content_element.css( 'margin-right', item._vertical_jquery_obj_outer_width + 'px' );
                        } else {
                            content_element.css( 'margin-right', 0 );
                        }
                    }
                }
            }
        },




        /**
         * - function used to compute all items in the item list
         *
         * @private
         */
        _compute_all_items: function() {
            for ( var i = tdPullDown.items.length - 1; i >= 0; i-- ) {

                // a type check is done for every item in the item list
                if ( tdPullDown.items[ i ].constructor === tdPullDown.item ) {
                    tdPullDown._compute_item( tdPullDown.items[ i ] );
                }
            }
        },




        /**
         * - function used to move one element from the vertical list to the horizontal one
         * - the function returns the element that has been moved, otherwise null
         * - the last element moving hides the vertical top header
         *
         * @param item - the item whose element is moved
         * @returns {T} - the moved element
         * @private
         */
        _make_element_horizontal: function( item ) {

            // the item must be initialized and the vertical list must contain at least an element
            if ( false === item._is_initialized || 0 === item._vertical_elements.length ) {
                return null;
            }

            // the first element of the vertical list is shifted
            var local_element = item._vertical_elements.shift();

            // the vertical list is shown when there's at least one vertical element
            if ( 0 === item._vertical_elements.length ) {
                item.vertical_jquery_obj.css( 'display', 'none' );
            }

            // the element is added on the last position in the horizontal list
            item._horizontal_elements.push( local_element );

            local_element.jquery_object.css( 'opacity', '0' );

            // the DOM is changing
            local_element.jquery_object.detach().appendTo( item.horizontal_jquery_obj );

            setTimeout( function() {
                local_element.jquery_object.css( 'opacity', '1' );
            }, 50);

            //tdPullDown.log('horizontal');

            return local_element;
        },




        /**
         * - function used to move one element from the horizontal list to the vertical one
         * - the function returns the element that has been moved, otherwise null
         * - the first element moving shows the vertical top header
         *
         * @param item - the item whose element is moved
         * @returns {T} - the moved element
         * @private
         */
        _make_element_vertical: function( item ) {

            // the item must be initialized and the horizontal list must contain at least an element
            if ( false === item._is_initialized || 0 === item._horizontal_elements.length ) {
                return null;
            }

            // the last element of the horizontal list is popped out
            var local_element = item._horizontal_elements.pop();

            // the vertical list is hidden when there are no vertical elements
            if ( 0 === item._vertical_elements.length ) {
                item.vertical_jquery_obj.css( 'display', '' );
            }

            //the element is added on the first position into the vertical list
            item._vertical_elements.unshift( local_element );

            // the DOM is changed
            local_element.jquery_object.detach().prependTo( item._vertical_ul_jquery_obj );

            //tdPullDown.log('vertical');

            return local_element;
        },




        /**
         * - function used to move all elements to the vertical list
         * - it's used when the minimum horizontal elements is greater than 0
         *
         * @param item - the item whose elements are moved
         * @private
         */
        _make_all_elements_vertical: function( item ) {
            while ( item._horizontal_elements.length > 0 ) {
                tdPullDown._make_element_vertical( item );
            }

            tdPullDown._add_no_items_class(item);
        },

        /**
         * - function used to add the item no items css class if the horizontal list is empty
         *
         * @param item
         * @private
         */
        _add_no_items_class: function (item) {

            if ( item.horizontal_no_items_css_class === '' ) {
                return;
            }

            if ( item._horizontal_elements.length === 0 ) {
                item.horizontal_jquery_obj.addClass(item.horizontal_no_items_css_class);
            } else {
                if ( item._horizontal_elements.length > 0 ) {
                    item.horizontal_jquery_obj.removeClass(item.horizontal_no_items_css_class);
                }
            }
        },



        /**
         * - function necessary to be called when the window is being resized
         */
        td_events_resize: function() {

            if ( 0 === tdPullDown.items.length ) {
                return;
            }

            if ( tdPullDown._view_port_interval_index !== tdViewport.getCurrentIntervalIndex() ) {

                tdPullDown._view_port_interval_index = tdViewport.getCurrentIntervalIndex();

                // Timer is necessary because reinitialization of all items it usually happens at viewport changing, and wait for custom css (ex. display: none ) to be applied
                if ( 'undefined' !== typeof tdPullDown.reinitTimeout ) {
                    clearTimeout( tdPullDown.reinitTimeout );
                }

                tdPullDown.reinitTimeout = setTimeout(function () {
                    tdPullDown._reinitialize_all_items();
                    tdPullDown._compute_all_items();
                }, 100);

                return;
            }

            tdPullDown._compute_all_items();
        },




        log: function log( msg ) {
            //console.log(msg);
        }
    };


    tdPullDown.init();

})();
var td_fps = {

    start_time: 0,

    current_time: 0,

    frame_number: 0,

    init: function init() {
        td_fps.start_time = 0;

        var previous_result = 0,
            result = 0,
            elapsed_time = 0;

        var td_fps_table = jQuery("#fps_table");

        if (td_fps_table.length == 0) {
            td_fps_table = jQuery('<div>').css({
                "position": "fixed",
                "top": "120px",
                "left": "10px",
                "width": "100px",
                "height": "20px",
                "border": "1px solid black",
                "font-size": "11px",
                "z-index": "100000",
                "background-color": "white"
            });

            td_fps_table.appendTo('body');
        }

        var get_fps = function() {
            td_fps.frame_number++;
            td_fps.current_time = Date.now();

            elapsed_time = (td_fps.current_time - td_fps.start_time) / 1000;

            result = (td_fps.frame_number / elapsed_time).toPrecision(2);

            if (result != previous_result) {
                previous_result = result;
                td_fps_table.html(previous_result + ' fps');
                //console.log(previous_result);
            }

            if (elapsed_time > 1) {
                td_fps.start_time = td_fps.current_time;
                td_fps.frame_number = 0;
            }

            requestAnimationFrame(get_fps);
        };

        get_fps();
    }
};

/**
 * Created by tagdiv on 16.02.2015.
 */

/* global jQuery: {} */
/* global tdEvents: {} */

var tdAnimationScroll = {};

(function() {

    'use strict';

    tdAnimationScroll = {


        // the bunch of tdAnimationScroll items
        items: [],



        // the current request animation frame id
        rAFIndex: 0,



        // flag used to not call 'requestAnimationFrame' when it's steel running
        animation_running: false,



        item: function item() {

            // the computed percent value of the jquery object in the viewport
            // - 0 when the top of object enters into the viewport
            // - 100 when the bottom of the object goes outside of the viewport
            this.percent_value = 0;

            // the animation callback function
            this.animation_callback = null;

            // the jquery object of the tdAnimationScroll.item
            this.jqueryObj = '';

            // optional - a jquery object that wraps the current item. Used in callback
            this.wrapper_jquery_obj = undefined;

            // a jquery span obj added dynamically added at the top of jqueryObj
            this.top_marker_jquery_obj = '';

            // the full outer height of the item
            this.full_height = 0;

            // the offset top of the top_marker_jquery_obj
            this.offset_top = '';

            // the offset top of the top_marker_jquery_obj and the full_height
            this.offset_bottom_top = '';

            // the properties registered with the item
            this.properties = {};

            // the computed properties that probably will be applied by animation callback function over the jquery object
            this.computed_item_properties = {};

            // flag made 'true' for items having at least one computed property
            this.redraw = false;

            // top is out of screen
            this.top_is_out = false;

            // flag used to mark the initialization item
            this._is_initialized = false;

            // flag used to stop an item to be computed
            this.computation_stopped = false;




            /**
             * - when a new item property is added, it's added as a real property in the item.properties object.
             * - if it's already added, the settings of the property are appended
             * - the settings for an item property must be added in order of the percents
             * - the percent intervals must not be overloaded (ex. 10-30 and 20-40)
             * - it doesn't matter how many settings are added to an item property
             * - after an adding the space of percentage is full, that means after adding
             * ex: add_item_property('opacity', 10, 30, 0, 1, easing)
             *
             * item.properties.opacity.settings :
             * [
             *  [0, 10, 0, 0, '']
             *  [10, 30, 0, 1, easing] - property added
             *  [30, 100, 1, 1, '']
             * ]
             *
             * ex: add_item_property('opacity', 40, 50, 1, 0)
             *
             * item.properties.opacity.settings :
             * [
             *  [0, 10, 0, 0, '']
             *  [10, 30, 0, 1, easing] - property added
             *  [30, 40, 1, 1, '']
             *  [40, 50, 1, 0, easing] - property added
             *  [50, 100, 0, 0, '']
             * ]
             *
             * - callable jQuery easing functions:
             * swing
             * easeInQuad
             * easeOutQuad
             * easeInOutQuad
             * easeInCubic
             * easeOutCubic
             * easeInOutCubic
             * easeInQuart
             * easeOutQuart
             * easeInOutQuart
             * easeInQuint
             * easeOutQuint
             * easeInOutQuint
             * easeInSine
             * easeOutSine
             * easeInOutSine
             * easeInExpo
             * easeOutExpo
             * easeInOutExpo
             * easeInCirc
             * easeOutCirc
             * easeInOutCirc
             * easeInElastic
             * easeOutElastic
             * easeInOutElastic
             * easeInBack
             * easeOutBack
             * easeInOutBack
             * easeInBounce
             * easeOutBounce
             * easeInOutBounce
             *
             * @param name string
             * @param start_percent numeric
             * @param end_percent numeric
             * @param start_value numeric
             * @param end_value numeric
             * @param easing string [optional]
             */
            this.add_item_property = function add_item_property(name, start_percent, end_percent, start_value, end_value, easing) {

                if (start_percent >= end_percent) {
                    return;
                }

                if (undefined === this.properties[name]) {

                    this.properties[name] = {
                        computed_value: '',
                        settings: []
                    };

                    if (0 !== start_percent) {
                        this.properties[name].settings[this.properties[name].settings.length] = {
                            start_percent: 0,
                            end_percent: start_percent,
                            start_value: start_value,
                            end_value: start_value,
                            easing: ''
                        };
                    }

                    this.properties[name].settings[this.properties[name].settings.length] = {
                        start_percent: start_percent,
                        end_percent: end_percent,
                        start_value: start_value,
                        end_value: end_value,
                        easing: easing
                    };

                    this.properties[name].settings[this.properties[name].settings.length] = {
                        start_percent: end_percent,
                        end_percent: 100,
                        start_value: end_value,
                        end_value: end_value,
                        easing: ''
                    };

                } else {

                    var last_setting = this.properties[name].settings[this.properties[name].settings.length - 1];

                    if (last_setting.start_percent !== start_percent) {
                        this.properties[name].settings[this.properties[name].settings.length - 1] = {
                            start_percent: last_setting.start_percent,
                            end_percent: start_percent,
                            start_value: last_setting.end_value,
                            end_value: last_setting.end_value,
                            easing: ''
                        };

                        this.properties[name].settings[this.properties[name].settings.length] = {
                            start_percent: start_percent,
                            end_percent: end_percent,
                            start_value: start_value,
                            end_value: end_value,
                            easing: easing
                        };
                    } else {
                        this.properties[name].settings[this.properties[name].settings.length - 1] = {
                            start_percent: start_percent,
                            end_percent: end_percent,
                            start_value: start_value,
                            end_value: end_value,
                            easing: easing
                        };
                    }

                    if (100 !== end_percent) {
                        this.properties[name].settings[this.properties[name].settings.length] = {
                            start_percent: end_percent,
                            end_percent: 100,
                            start_value: end_value,
                            end_value: end_value,
                            easing: ''
                        };
                    }
                }
            };


            /**
             * remove an item property
             *
             * @param name {String} The name of the property
             * @returns {boolean}
             */
            this.remove_item_property = function remove_item_property(name) {
                if (undefined === this.properties[name]) {
                    return false;
                }

                delete this.properties[name];

                return true;
            };
        },




        /**
        * - function used to init the tdAnimationScroll object
        * - it must be called before adding any item
        * - the _view_port_interval_index flag is initialized
        * - the items list is empty initialized
        */
        init: function init() {

            tdAnimationScroll.items = [];
        },




        /**
         * - used to add an item to the item list and initialize it
         *
         * @param item The item to be added and initialized
         */
        add_item: function add_item(item) {

            if (item.constructor !== tdAnimationScroll.item) {
                return;
            }

            // Don't add elements where 'td_marker_animation' is present
            if ( 'undefined' === typeof item.jqueryObj ) {
                return;
            } else {
                var $prevItem = item.jqueryObj.prev();
                if ( $prevItem.length && $prevItem.hasClass( 'td_marker_animation' ) ) {
                    return;
                }
            }

            // the item is added in the item list
            tdAnimationScroll.items.push(item);

            // the item is initialized only once when it is added
            tdAnimationScroll._initialize_item(item);

            // for efficiently rendering all items are computed at once, so do not compute item individually
        },




        /**
         * - used to initialize an item
         * - an item must be initialized only once
         *
         * @param item
         * @private
         */
        _initialize_item: function _initialize_item(item) {

            // an item must be initialized only once
            if (true === item._is_initialized) {
                return;
            }

            // the item full height is computed
            if (undefined === item.wrapper_jquery_obj) {
                item.full_height = item.jqueryObj.outerHeight(true);
            } else {
                item.full_height = item.wrapper_jquery_obj.height();
            }

            if (0 === item.full_height) {
                return;
            }

            var new_jquery_obj_reference = jQuery('<div class="td_marker_animation" style="height: 0; width: 0">');

            new_jquery_obj_reference.insertBefore(item.jqueryObj);

            item.top_marker_jquery_obj = new_jquery_obj_reference;

            item.offset_top = item.top_marker_jquery_obj.offset().top;

            //console.log("initializare " + tdAnimationScroll.items.length + " : " + item.top_marker_jquery_obj.offset().top);

            item.offset_bottom_top = item.offset_top + item.full_height;

            item.top_is_out = tdEvents.window_pageYOffset > item.offset_top;

            // the item is marked as initialized, being ready to be computed
            // for efficiently rendering all items are computed at once
            item._is_initialized = true;


            // maybe it's better to try a request animation frame after every initialization, for computing the already added items
            //tdAnimationScroll.compute_all_items();
        },




        /**
         * - used to reinitialize all items at the view resolution changing
         *
         * @param recompute_height boolean True if it's necessary to recompute the item's height [when view port changes]
         */
        reinitialize_all_items: function reinitialize_all_items(recompute_height) {

            for (var i = tdAnimationScroll.items.length - 1; i >= 0; i--) {
                tdAnimationScroll.reinitialize_item(tdAnimationScroll.items[i], recompute_height);
            }
        },






        /**
         * - used to reinitialize an item at the view resolution changing
         *
         * @param item tdAnimationScroll.item
         * @param recompute_height boolean True if it's necessary to recompute the item height [when view port changes]
         * @private
         */
        reinitialize_item: function reinitialize_item(item, recompute_height) {

            // a not initialized item can't be reinitialized
            if (false === item._is_initialized) {
                return;
            }

            // prevent the following item computing, till the reinitialization is finished
            item._is_initialized = false;

            item.offset_top = item.top_marker_jquery_obj.offset().top;

            //console.log("reinitializare " + tdAnimationScroll.items.length + " : " + item.top_marker_jquery_obj.offset().top);

            if (true === recompute_height) {
                if (undefined === item.wrapper_jquery_obj) {
                    item.full_height = item.jqueryObj.outerHeight(true);
                } else {
                    item.full_height = item.wrapper_jquery_obj.height();
                }

                if (0 === item.full_height) {
                    return;
                }
            }

            item.offset_bottom_top = item.offset_top + item.full_height;

            item._is_initialized = true;
        },




        /**
         * - used for computing item properties
         *
         * @param item The item whose properties are computed
         * @private
         */
        _compute_item_properties: function _compute_item_properties(item) {

            var computed_properties = {},
                current_item_property;

            for (var property in item.properties) {

                if (true === item.properties.hasOwnProperty(property)) {

                    current_item_property = item.properties[property];

                    var current_setting,
                        new_computed_value,
                        local_computed_value,
                        easing_step,
                        easing_computed_value,
                        easing_division_interval = 1000;

                    for (var i = 0; i < current_item_property.settings.length; i++) {

                        current_setting = current_item_property.settings[i];

                        // the check is done using this form [...) of the interval or the last position 100%
                        if ((current_setting.start_percent <= item.percent_value && item.percent_value < current_setting.end_percent) ||
                            (item.percent_value === current_setting.end_percent && 100 === item.percent_value)) {

                            if (current_setting.start_value === current_setting.end_value) {

                                new_computed_value = current_setting.start_value;

                            } else {

                                // local computed value can have a positive value or a negative value, it depends of the difference end_value - start_value
                                // for a linear easing function, the new computed value is the start_value + local_computed_value
                                // if start_value < end_value, the variable local_computed_value is positive
                                // if start_value > end_value, the variable local_computed_value is negative
                                local_computed_value = (item.percent_value - current_setting.start_percent) / (current_setting.end_percent - current_setting.start_percent) * (current_setting.end_value - current_setting.start_value);


                                // if there's specified an easing function, it's applied over the computed_value
                                if ((undefined === current_setting.easing) || ('' === current_setting.easing)) {

                                    // linear easing function

                                    new_computed_value = current_setting.start_value + local_computed_value;

                                } else {

                                    // specifying an easing function

                                    easing_step = Math.abs(current_setting.start_value - current_setting.end_value) / easing_division_interval;

                                    if (current_setting.start_value < current_setting.end_value) {

                                        easing_computed_value = current_setting.start_value + jQuery.easing[current_setting.easing](
                                            null,
                                            local_computed_value,
                                            0,
                                            easing_step,
                                            current_setting.end_value - current_setting.start_value) * easing_division_interval;

                                    } else {

                                        easing_computed_value = current_setting.start_value - jQuery.easing[current_setting.easing](
                                            null,
                                            -local_computed_value,
                                            0,
                                            easing_step,
                                            current_setting.start_value - current_setting.end_value) * easing_division_interval;
                                    }

                                    new_computed_value = easing_computed_value;

                                    //console.log(current_setting.easing + ' : ' + easing_step + ' ~ ' + easing_computed_value + ' ~ ' + (current_setting.start_value + computed_value) + ' & ' + current_setting.start_value + ' $ ' + current_setting.end_value);
                                }
                            }

                            // if the existing computed value is different, the new computed value is cached
                            if (current_item_property.computed_value !== new_computed_value) {
                                current_item_property.computed_value = new_computed_value;
                                computed_properties[property] = new_computed_value;

                                // the item is marked that it has at least one property that need to be redraw
                                // the animation callback functions are called just for the marked items
                                item.redraw = true;
                            }
                            break;
                        }
                    }
                }
            }

            // a plain javascript object is added if there is no computed property
            item.computed_item_properties = computed_properties;
        },




        /**
         * - used for computing item
         * - the item properties are computed only when the item is in the view port and it is moving
         *
         * @param item The tdAnimationScroll.item to be computed
         */
        compute_item: function compute_item(item) {
            //console.clear();

            // the item must be initialized first
            if (false === item._is_initialized) {
                return;
            }

            var percent_display_value = 0;

            if (tdEvents.window_pageYOffset + tdEvents.window_innerHeight >= item.offset_top) {

                if (tdEvents.window_pageYOffset > item.offset_bottom_top) {
                    percent_display_value = 100;
                } else {
                    percent_display_value = (tdEvents.window_pageYOffset + tdEvents.window_innerHeight - item.offset_top) * 100 / (tdEvents.window_innerHeight + item.full_height);
                }
            }

            //console.log(window.pageYOffset + ' : ' + item.offset_top + ' : ' + item.offset_bottom_top);

            if (item.percent_value !== percent_display_value) {
                item.percent_value = percent_display_value;
                tdAnimationScroll._compute_item_properties(item);
            }

            item.top_is_out = tdEvents.window_pageYOffset > item.offset_top;


            //console.log(percent_display_value);
        },




        /**
         * - used to request an animation frame for computing all items
         * - the flag animation_running is set to false by the last requestAnimationFrame callback (the last animation call),
         * so a new call to requestAnimationFrame can be done
         */
        compute_all_items: function compute_all_items() {
            //tdAnimationScroll.animate();

            if (false === tdAnimationScroll.animation_running) {
                tdAnimationScroll.rAFIndex = window.requestAnimationFrame( tdAnimationScroll._animate_all_items );
            }

            tdAnimationScroll.animation_running = true;
        },




        /**
         * - used to call the existing callback animate functions
         *
         * @private
         */
        _animate_all_items: function _animate_all_items() {
            //var start_time = Date.now();

            for (var i = 0; i < tdAnimationScroll.items.length; i++) {
                if ( false === tdAnimationScroll.items[i].computation_stopped) {
                    tdAnimationScroll.compute_item(tdAnimationScroll.items[i]);
                }
            }

            for (var j = 0; j < tdAnimationScroll.items.length; j++) {
                if (true === tdAnimationScroll.items[j].redraw) {
                    tdAnimationScroll.items[j].animation_callback();
                }
            }

            tdAnimationScroll.animation_running = false;

            //var end_time = Date.now();
            //
            //var debug_table = jQuery("#debug_table");
            //debug_table.html((end_time - start_time) + ' ms');
        },





        /** !!!! we'll see if it's necessary to make reinitialization just at the view port changing. Now, it's not
         * - necessary to be called when the window is being resized
         */
        td_events_resize: function td_events_resize() {

            if (0 === tdAnimationScroll.items.length) {
                return;
            }

            // this will be applied if it depends just by view port changing

            //if (tdAnimationScroll._changed_view_port_width()) {
            //    tdAnimationScroll.reinitialize_all_items();
            //}

            tdAnimationScroll.reinitialize_all_items(false);

            tdAnimationScroll.compute_all_items();
        },






        log: function log(msg) {
            //console.log(msg);
        }
    };

    tdAnimationScroll.init();

})();



/**
 * Created by tagdiv on 30.05.2016.
 */

/* global jQuery: {} */

var tdHomepageFull = {};

(function( jQuery, undefined ) {

    'use strict';

    tdHomepageFull = {

        items: [],

        item: function() {

            // OPTIONAL - here we store the block Unique ID. This enables us to delete the item via this id @see tdHomepageFull.deleteItem
            this.blockUid = '';

            this.$tmplBlock = undefined;
        },

        /**
         *
         * @param item tdHomepageFull.item
         */
        addItem: function( item ) {
            if ( tdHomepageFull.items.length ) {
                return;
            }

            switch ( item.theme_name ) {
                case 'Newsmag': tdHomepageFull._addNewsmagItem( item );
                    break;
                default:
                    tdHomepageFull._addItem( item );
                    break;
            }
        },


        deleteItem: function( blockUid ) {

            for (var i = 0; i < tdHomepageFull.items.length; i++) {

                var currentItem = tdHomepageFull.items[ i ];

                if ( currentItem.blockUid === blockUid ) {

                    switch ( currentItem.theme_name ) {
                        case 'Newsmag': tdHomepageFull._deleteNewsmagItem( currentItem, i );
                            break;
                        default:
                            tdHomepageFull._deleteItem( currentItem, i );
                            break;
                    }
                }
            }
            return false;
        },

        _addItem: function( item ) {
            // The block template script
            item.$tmplBlock = jQuery( '#' + item.blockUid + '_tmpl' );

            // add the template
            jQuery( '.td-header-wrap' ).after( item.$tmplBlock.html() );

            // make the wrapper and the image -> and add the image inside
            var td_homepage_full_bg_image_wrapper = jQuery( '<div class="backstretch"></div>' );
            var td_homepage_full_bg_image = jQuery( '<img class="td-backstretch not-parallax" src="' + item.postFeaturedImage + '"/>' );
            td_homepage_full_bg_image_wrapper.append( td_homepage_full_bg_image );

            // add to body
            jQuery( 'body' ).prepend( td_homepage_full_bg_image_wrapper );

            // run the backstracher
            var tdBackstrItem = new tdBackstr.item();
            tdBackstrItem.wrapper_image_jquery_obj = td_homepage_full_bg_image_wrapper;
            tdBackstrItem.image_jquery_obj = td_homepage_full_bg_image;
            tdBackstr.add_item( tdBackstrItem );


            // The DOM article reference (article has already been inserted)
            item.$article = jQuery( '#post-' + item.postId );

            // The background image
            item.$bgImageWrapper = td_homepage_full_bg_image_wrapper;

            // The backstretch item
            item.backstrItem = tdBackstrItem;

            tdHomepageFull.items.push( item );
        },

        _addNewsmagItem: function( item ) {

            /// The block template script
            item.$tmplBlock = jQuery( '#' + item.blockUid + '_tmpl' );

            jQuery('body').addClass('single_template_6'); // add single_template_6 to space the content
            jQuery('#td-outer-wrap').prepend( item.$tmplBlock.html());

            //'jQuery(\'body\').prepend(\'<div class="td-full-screen-header-image-wrap"><div id="td-full-screen-header-image" class="td-image-gradient"></div></div>\');' . "\r\n" .

            var td_homepage_full_bg_image_wrapper1 = jQuery('<div class="td-full-screen-header-image-wrap"></div>');
            var td_homepage_full_bg_image_wrapper2 = jQuery('<div id="td-full-screen-header-image" class="td-image-gradient"></div>');
            var td_homepage_full_bg_image = jQuery('<img class="td-backstretch" src="' + item.postFeaturedImage + '"/>');

            td_homepage_full_bg_image_wrapper1.append(td_homepage_full_bg_image_wrapper2);
            td_homepage_full_bg_image_wrapper2.append(td_homepage_full_bg_image);

            // add to body
            jQuery('#td-outer-wrap').prepend(td_homepage_full_bg_image_wrapper1);

            // The background image
            item.$bgImageWrapper = td_homepage_full_bg_image_wrapper1;

            // run the backstracher
            var tdBackstrItem = new tdBackstr.item();
            tdBackstrItem.wrapper_image_jquery_obj = td_homepage_full_bg_image_wrapper1;
            tdBackstrItem.image_jquery_obj = td_homepage_full_bg_image;

            tdBackstr.add_item(tdBackstrItem);

            // The DOM article reference (article has already been inserted)
            item.$article = jQuery( '#post-' + item.postId );

            // The backstretch item
            item.backstrItem = tdBackstrItem;

            jQuery('.td-read-down a').on( 'click', function(event){
                event.preventDefault();
                tdUtil.scrollToPosition(jQuery('.td-full-screen-header-image-wrap').height(), 1200);
            });

            tdHomepageFull.items.push( item );
        },


        _deleteItem: function( item, index ) {

            // Remove the block template script
            item.$tmplBlock.remove();

            // Remove the article
            item.$article.remove();

            // Remove the background image
            item.$bgImageWrapper.remove();

            tdHomepageFull.items.splice(index, 1); // remove the item from the "array"

            if ( tdBackstr.deleteItem( item.blockUid ) ) {

                item.backstrItem = undefined;
            }

            var existingClassName = document.body.className;

            existingClassName = existingClassName.replace(/td-boxed-layout/g, '');
            existingClassName = existingClassName.replace(/single_template_8/g, '');
            existingClassName = existingClassName.replace(/homepage-post/g, '');

            document.body.className = existingClassName;
        },

        _deleteNewsmagItem: function(item, index) {

            // Remove the block template script
            item.$tmplBlock.remove();

            // Remove the article
            item.$article.remove();

            // Remove the background image
            item.$bgImageWrapper.remove();

            tdHomepageFull.items.splice(index, 1); // remove the item from the "array"

            if ( tdBackstr.deleteItem( item.blockUid ) ) {

                item.backstrItem = undefined;
            }

            var existingClassName = document.body.className;

            existingClassName = existingClassName.replace(/single_template_6/g, '');

            document.body.className = existingClassName;
        }
    };

})( jQuery );

/**
 * Created by tagdiv on 23.02.2015.
 */

var tdBackstr = {};

(function(){

    'use strict';

    tdBackstr = {


        items: [],


        item: function() {

            // OPTIONAL - here we store the block Unique ID. This enables us to delete the item via this id @see tdBackstr.deleteItem
            this.blockUid = '';

            // check if is necessary to apply modification (css)
            this.previous_value = 0;

            // the image aspect ratio
            this.image_aspect_rate = 0;

            // the wrapper jquery object
            this.wrapper_image_jquery_obj = '';

            // the image jquery object
            this.image_jquery_obj = '';
        },


        /**
         *
         * @param item
         */
        add_item: function( item ) {

            if ( item.constructor !== tdBackstr.item ) {
                return;
            }

            //if ((item.image_jquery_obj.complete)
            //
            //    // this is a case when the image is still not loaded but the height() and width() return both 24px
            //    // !!!! it must be modified. It's used because for backstretch are usually used large images
            //    && ((item.image_jquery_obj.height() != 24) && (item.image_jquery_obj.width() != 24))
            //)

            if ( item.image_jquery_obj.get( 0 ).complete ) {
                tdBackstr._load_item_image( item );

            } else {

                item.image_jquery_obj.on( 'load', function() {
                    tdBackstr._load_item_image( item );
                });


                //var currentTimeStart = Date.now();
                //
                //var loaded_image_jquery_ojb = false;
                //
                //item.image_jquery_obj.on('load', function() {
                //
                //    loaded_image_jquery_ojb = true;
                //
                //
                //    tdBackstr._load_item_image(item);
                //    console.log('backstr tarziu ' + item.image_jquery_obj.height() + ' > timp : ' + (Date.now() - currentTimeStart));
                //});
                //
                //
                //var indexInterval = setInterval(function() {
                //    if (loaded_image_jquery_ojb) {
                //        clearInterval(indexInterval);
                //        console.log('imagine incarcata ' + item.image_jquery_obj.height() + ' > timp : ' + (Date.now() - currentTimeStart));
                //    }
                //}, 0);
            }
        },


        deleteItem: function( blockUid ) {
            for (var i = 0; i < tdBackstr.items.length; i++) {
                if ( tdBackstr.items[ i ].blockUid === blockUid ) {
                    tdBackstr.items.splice(i, 1); // remove the item from the "array"
                    return true;
                }
            }
            return false;
        },


        _load_item_image: function( item ) {
            item.image_aspect_rate = item.image_jquery_obj.width() / item.image_jquery_obj.height();
            tdBackstr.items.push( item );
            tdBackstr._compute_item( item );

            item.image_jquery_obj.css( 'opacity', '1' );
        },


        /**
         *
         * @param item
         * @private
         */
        _compute_item: function( item ) {

            // the wrapper aspect ratio can vary, so it's recomputed at computing item
            var wrapper_aspect_rate = item.wrapper_image_jquery_obj.width() / item.wrapper_image_jquery_obj.height();

            var current_value = 0;

            if ( wrapper_aspect_rate < item.image_aspect_rate ) {

                current_value = 1;

                if ( item.previous_value !== current_value ) {
                    item.image_jquery_obj.removeClass( 'td-stretch-width' );
                    item.image_jquery_obj.addClass( 'td-stretch-height' );

                    item.previous_value = current_value;
                }
            } else {

                current_value = 2;

                if ( item.previous_value !== current_value ) {
                    item.image_jquery_obj.removeClass( 'td-stretch-height' );
                    item.image_jquery_obj.addClass( 'td-stretch-width' );

                    item.previous_value = current_value;
                }
            }
        },


        /**
         *
         * @private
         */
        _compute_all_items: function() {
            for ( var i = 0; i < tdBackstr.items.length; i++ ) {
                tdBackstr._compute_item( tdBackstr.items[ i ] );
            }
        },


        td_events_resize: function() {

            if ( 0 === tdBackstr.items.length ) {
                return;
            }

            tdBackstr._compute_all_items();
        },




        log: function( msg ) {
            window.console.log( msg );
        }
    };


})();




/**
 * Created by tagdiv on 11.03.2015.
 */

/**
 * abstract:
 * - check all items in page, sort them using one of a sorted methods and add them in the items array
 * - at every scroll the items are verified if they are in view port or above
 * - every item in view port is added into the _items_in_view_port array and they are ready for animation
 * - items above view port are animated all at once
 * - items in view port are animated at crescendo intervals [interval / remaining items]
 * - there's a max and a min interval
 * - td_block ajax request response use a sort method and add founded items into view port array or into items array
 */


/* global jQuery:false */
/* global tdDetect:false */
/* global tdEvents:{} */

var tdAnimationStack = {};

( function() {

    "use strict";

    tdAnimationStack = {

        /*
            Important:
            1. The first animation step is produced by the the body selector @see animation-stack.less
            2. The second animation step can be applied by the animation_css_class1
            3. The final (the main) animation step is applied by the animation_css_class2
         */




        // - flag css class used by the non 'type0' animation effect
        // - flag used just to look for not yet computed item
        // - it's set by ready_init (on ready)
        // - all dom components that need to be animated will be marked with this css class in ready_init
        // - it can be used for a precomputed style, but carefully, because it's applied at ready_init (on ready)
        _animation_css_class1: '',



        // - flag css class used by the non 'type0' animation effect
        // - flag css class used to animate custom
        // - this css class applies the final animation
        _animation_css_class2: '',



        // - the default animation effect 'type0' is applied if the global window.td_animation_stack_effect is the empty string
        // - it's used for consistency of animation effects presented into the animation-stack.less [all types have a name (...type...)]
        _animation_default_effect: 'type0',



        // - tdAnimationStack runs just only when this flag is true
        // - it's done true by the init function
        activated: false,



        // flag checked by the major animation operations
        _ready_for_initialization: true,

        // interval used by ready_init to check tdAnimationStack state
        _ready_init_timeout: undefined,


        // max time[ms] interval waiting for first tdAnimationStack.init call
        max_waiting_for_init: 3000,



        // the specific selectors are used to look for new elements inside of the specific sections
        _specific_selectors: '',

        // the general selectors are used to look for elements over extend areas in DOM
        _general_selectors: '',

        // - tdAnimationStack loads the items just when the animation occurs
        live_load_items: false,





        /**
         * - wait for tdAnimationStack.init() for max_waiting_for_init time
         * - if time is elapsed, the animation is canceled
         * - the ready_init is canceled by a fast tdAnimationStack.init call
         */
        ready_init: function() {

            // - special case for IE8 and IE9 (and if Visual Composer image carousel exists)
            // Important! The Visual Compose images carousel has hidden elements (images) that does not allow for computing the real position of the other DOM elements in the viewport
            // - the animation is forced removed and the altered css body is cleaned
            if ( tdDetect.isIe8 || tdDetect.isIe9 || ( jQuery( '.vc_images_carousel' ).length > 0 ) ) {
                tdAnimationStack._ready_for_initialization = false;

                if ( undefined !== window.td_animation_stack_effect ) {
                    if ( '' === window.td_animation_stack_effect ) {
                        window.td_animation_stack_effect = tdAnimationStack._animation_default_effect;
                    }
                    jQuery( 'body' ).removeClass( 'td-animation-stack-' + window.td_animation_stack_effect );
                }
                return;
            }


            if ( undefined === window.tds_animation_stack || undefined === window.td_animation_stack_effect ) {

                // lock any further operation using the _ready_for_initialization flag
                tdAnimationStack._ready_for_initialization = false;

            } else {

                // the tdAnimationStack._specific_selectors is set by the global variable window.td_animation_stack_specific_selectors
                if ( undefined !== window.td_animation_stack_specific_selectors ) {
                    tdAnimationStack._specific_selectors = window.td_animation_stack_specific_selectors;
                }


                // if the global variable window.td_animation_stack_effect has the empty string value, the 'full fade' (type0) effect is prepared to be applied
                if ( '' === window.td_animation_stack_effect ) {
                    window.td_animation_stack_effect = tdAnimationStack._animation_default_effect;
                }

                tdAnimationStack._animation_css_class1 = 'td-animation-stack-' + window.td_animation_stack_effect + '-1';
                tdAnimationStack._animation_css_class2 = 'td-animation-stack-' + window.td_animation_stack_effect + '-2';


                // - the tdAnimationStack._general_selectors is set by the global variable window.td_animation_stack_general_selectors
                if ( undefined !== window.td_animation_stack_general_selectors ) {
                    tdAnimationStack._general_selectors = window.td_animation_stack_general_selectors;
                }

                // the tdAnimationStack._animation_css_class1 css class is applied for all elements need to be animated later
                jQuery( tdAnimationStack._general_selectors ).addClass( tdAnimationStack._animation_css_class1 );


                // - timeout used by the ready_init function, to cut down tdAnimationStack.init calling at loading page, when the call comes too late
                // - if tdAnimationStack.init comes earlier, it does a clearTimeout call over the tdAnimationStack._ready_init_timeout variable
                tdAnimationStack._ready_init_timeout = setTimeout( function() {

                    tdAnimationStack.log( '%c _ready_init_timeout run ', 'background: red; color: white;' );

                    // if tdAnimationStack is activated, do nothing
                    if ( true === tdAnimationStack.activated ) {
                        return;
                    }

                    // lock any further operation using the _ready_for_initialization flag
                    tdAnimationStack._ready_for_initialization = false;

                    jQuery( tdAnimationStack._general_selectors).not( '.' + tdAnimationStack._animation_css_class2 ).removeClass( tdAnimationStack._animation_css_class1 );

                    // for every element found.. set back the img src
                    var found_elements = jQuery( '.td-animation-stack, .post' ).find( tdAnimationStack._specific_selectors );

                    tdAnimationStack.log( '_ready_init_timeout found elements: ' + found_elements.length );

                    // found elements
                    found_elements.each( function( index, element ) {

                        var type = jQuery( element ).data('type');
                        var item = jQuery( element );

                        tdAnimationStack.log( 'type: ' + type );
                        tdAnimationStack.log( 'src: ' + item.data('img-url') );

                        switch ( type ) {

                            case 'image_tag':

                                if ( item.data( 'img-retina-url' ) !== undefined && tdAnimationStack._isHighDensity() === true ) {
                                    item.attr( 'src', item.data('img-retina-url') );
                                } else {
                                    item.attr( 'src', item.data('img-url') );
                                }

                                break;

                            case 'css_image':

                                if ( item.data( 'img-retina-url' ) !== undefined && tdAnimationStack._isHighDensity() === true ) {
                                    item.attr( 'style', 'background-image: url(' + item.data('img-retina-url') + ')' );
                                } else {
                                    item.attr( 'style', 'background-image: url(' + item.data('img-url') + ')' );
                                }

                                break;
                        }
                    });

                    // remove the loading animation css class effect from the body
                    // this class is applied from the theme settings
                    if ( undefined !== window.td_animation_stack_effect ) {
                        jQuery( 'body' ).removeClass( 'td-animation-stack-' + window.td_animation_stack_effect );
                    }

                }, tdAnimationStack.max_waiting_for_init );
            }
        },


        // flag marks items where they are
        _ITEM_TO_VIEW_PORT: {

            ITEM_ABOVE_VIEW_PORT: 0,

            ITEM_IN_VIEW_PORT: 1,

            ITEM_UNDER_VIEW_PORT: 2
        },


        // predefined sorting methods
        SORTED_METHOD: {

            sort_left_to_right: function sort_left_to_right( item1, item2 ) {
                if ( item1.offset_top > item2.offset_top ) {
                    return 1;
                } else if ( item1.offset_top < item2.offset_top ) {
                    return -1;
                } else if ( item1._order > item2._order ) {
                    return 1;
                } else if ( item1._order < item2._order ) {
                    return -1;
                }
                return 0;
            },


            sort_right_to_left: function sort_right_to_left( item1, item2 ) {
                if ( item1.offset_top > item2.offset_top ) {
                    return 1;
                } else if ( item1.offset_top < item2.offset_top ) {
                    return -1;
                } else if ( item1._order > item2._order ) {
                    return -1;
                } else if ( item1._order < item2._order ) {
                    return 1;
                }
                return -1;
            }
        },


        // keeps the DOM reading order, used in the sorting methods
        _order: 0,


        // interval divided to animate items
        // ex. interval 100 and 2 items => one item at 100 / 2 and one item at 100 / 1, but not lower than min_interval and not higher than max_interval
        interval: 70,

        // min interval of a set timer
        min_interval: 17,

        // max interval of a set timer
        max_interval: 40,



        // keep current setInterval
        _current_interval: undefined,

        // items in view port are moved here
        _items_in_view_port: [],

        // items above the view port are moved here
        _items_above_view_port: [],

        // all items that will be processed
        items: [],








        /**
         * - tdAnimationStack.item
         */
        item: function() {
            // offset from the top of the item, to the top
            // it's set at the initialization item
            this.offset_top = undefined;


            // offset from the bottom of the item, to the top
            // it's set at the initialization item
            this.offset_bottom_to_top = undefined;


            // jquery object reference
            // it's set before the initialization of the item
            this.jqueryObj = undefined;


            // the reading order from DOM
            // it's set at the initialization item
            this._order = undefined;

            // the img source
            this.itemSrc = undefined;

            // the img type
            this.itemType = undefined;

            // the retina img uuid
            this.itemImgRetinaSrc = undefined;
        },




        /**
         * - initialize a tdAnimationStack.item and add it in tdAnimationStack.items
         * @param item tdAnimationStack.item
         */
        //add_item: function add_item(item) {
        //
        //    if (item.constructor != tdAnimationStack.item) {
        //        return;
        //    }
        //
        //    tdAnimationStack.items.push(item);
        //},



        /**
         * - initialize the offset top of the tdAnimationStack.item parameter
         * @param item tdAnimationStack.item
         * @private
         */
        _initialize_item: function( item ) {
            item._order = tdAnimationStack._order++;

            item.offset_top = item.jqueryObj.offset().top;
            //item.offset_relative = Math.sqrt(Math.pow(item.jqueryObj.offset().top, 2) + Math.pow(item.jqueryObj.offset().left, 2));

            item.offset_bottom_to_top = item.offset_top + item.jqueryObj.height();

            //item.jqueryObj.parent().prepend('<div class="debug_item" style="position: absolute; width: 100%; height: 20px; border: 1px solid red; background-color: white">' + item.offset_top + '</div>');
        },


        /**
         * - dynamically search for new elements to create new tdAnimationStack.item
         * - the items are added in the tdAnimationStack._items_in_view_port, that means they are ready to be animated,
         * or in the tdAnimationStack.items to be computed later (checked if they are in the view port and animated)
         * @param selector {string} - jQuery selector
         * @param sort_type {tdAnimationStack.SORTED_METHOD} - a preferred tdAnimationStack.SORTED_METHOD
         * @param in_view_port {boolean} - add an item in the tdAnimationStack._items_in_view_port or in the tdAnimationStack.items
         * @param live_load_items {boolean} - the items are loaded just when the animation occurs
         */
        check_for_new_items: function( selector, sort_type, in_view_port, live_load_items ) {

            // tdAnimationStack must be activated and not stopped for initialization by the ready_init checker
            if ( ( false === tdAnimationStack.activated ) || ( false === tdAnimationStack._ready_for_initialization ) ) {
                return;
            }

            if ( undefined === selector ) {
                selector = '';
            }

            // the local stack of searched items
            var local_stack = [];

            jQuery( tdAnimationStack._general_selectors).not( '.' + tdAnimationStack._animation_css_class2 ).addClass( tdAnimationStack._animation_css_class1 );

            // for every founded element there's an instantiated tdAnimationStack.item, then initialized and added to the local stack
            var founded_elements = jQuery( selector + ', .post' ).find( tdAnimationStack._specific_selectors ).filter( function() {
                    return jQuery( this ).hasClass( tdAnimationStack._animation_css_class1 );
                });

            // found elements
            founded_elements.each( function( index, element ) {

                var item_animation_stack = new tdAnimationStack.item();

                item_animation_stack.jqueryObj = jQuery( element );
                item_animation_stack.itemSrc = jQuery( element ).data('img-url');
                item_animation_stack.itemType = jQuery( element ).data('type');

                if ( jQuery( element ).data('img-retina-url') !== undefined ) {
                    item_animation_stack.itemImgRetinaSrc = jQuery( element ).data('img-retina-url');
                }

                tdAnimationStack._initialize_item( item_animation_stack );

                local_stack.push( item_animation_stack );
            });

            if ( true === live_load_items ) {

                tdAnimationStack.log( '%c live load items ', 'background: brown; color: white;' );

                /**
                 * the images are loaded just when the animation occurs
                 */
                tdAnimationStack._precompute_items( local_stack, sort_type, in_view_port );
                tdAnimationStack.compute_items(live_load_items);
            } else {

                tdAnimationStack.log( '%c preload items ', 'background: brown; color: white;' );

                // new scope having its own timer used for checking not yet loaded images
                ( function(){

                    var images_loaded = true;

                    for ( var i = 0; i < local_stack.length; i++ ) {

                        // for every image element the 'complete' property is checked
                        // "If the image is finished loading, the complete property returns true"
                        // when tdAnimationStack.init is called on load, as normally, it calls tdAnimationStack.check_for_new_items and all these element has 'complete' property true
                        // when tdAnimationStack.check_for_new_items is called by block's ajax response, the next timer is used to wait for all elements being loaded
                        if ( false === founded_elements[ i ].complete ) {
                            images_loaded = false;
                            break;
                        }
                    }

                    // if there's at least one element not loaded, a timer is started to wait for
                    if ( false === images_loaded ) {

                        var date = new Date();
                        var start_time = date.getTime();


                        tdAnimationStack.log( 'TIMER - started' );


                        // the timer is started
                        var interval_check_loading_image = setInterval( function() {

                            // if there's too much time waiting for image loading, they are made visible
                            var date = new Date();

                            var i = 0;

                            if ( ( date.getTime() - start_time ) > tdAnimationStack.max_waiting_for_init ) {

                                clearInterval( interval_check_loading_image );

                                for ( i = 0; i < local_stack.length; i++ ) {

                                    //if (window.td_animation_stack_effect === 'type0') {
                                    //    local_stack[i].jqueryObj.css('opacity', 1);
                                    //} else {
                                    local_stack[ i ].jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                                    local_stack[ i ].jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                                    //}

                                }
                                return;
                            }


                            // at every interval step, the element's 'complete' property is checked again
                            images_loaded = true;

                            for ( i = 0; i < local_stack.length; i++ ) {

                                if ( false === founded_elements[ i ].complete ) {
                                    images_loaded = false;
                                    break;
                                }
                            }

                            if ( true === images_loaded ) {

                                clearInterval( interval_check_loading_image );

                                tdAnimationStack.log( 'TIMER - stopped' );

                                tdAnimationStack._precompute_items( local_stack, sort_type, in_view_port );
                                tdAnimationStack.compute_items(false);
                            }

                        }, 100);

                    } else {
                        tdAnimationStack._precompute_items( local_stack, sort_type, in_view_port );
                        tdAnimationStack.compute_items(false);
                    }

                })();
            }

            tdAnimationStack.log( 'checked for new items finished' );
        },


        /**
         * - _precompute_items sorts and adds items in the tdAnimationStack.items array or even in the tdAnimationStack._items_in_view_port array
         * - this function is necessary because at scroll just the tdAnimationStack.compute_items function is called
         *
         * @param stack_items {[]} founded items
         * @param sort_type {function} sorting method
         * @param in_view_port {boolean} add in view port to be already computed, or in the general items array
         * @private
         */
        _precompute_items: function( stack_items, sort_type, in_view_port ) {

            stack_items.sort( sort_type );

            if ( true === in_view_port ) {

                while ( stack_items.length > 0 ) {
                    tdAnimationStack.log( 'add item 1 : ' + stack_items.length );
                    tdAnimationStack._items_in_view_port.push( stack_items.shift() );
                }

            } else {

                while (stack_items.length > 0) {
                    tdAnimationStack.log( 'add item 2 : ' + stack_items.length );
                    tdAnimationStack.items.push( stack_items.shift() );
                }
            }
        },



        /**
         * - IT'S CALLED ON PAGE LOAD [actually in tdLastInit.js]
         * - the general init function
         * - the items are added to the tdAnimationStack.items using check_for_new_items method, and then computed
         * - the arrays are cleared to be prepared for a reinitialization
         */
        init: function() {
            if ( undefined === window.tds_animation_stack ) {
                tdAnimationStack.log( '%c theme lazy loading animation is off! ', 'background: #eb4026;' );
                return;
            }

            tdAnimationStack.log( '%c theme lazy loading animation is on! ', 'background: #03c04a; color: #fff;' );

            // tdAnimationStack must not be already stopped for initialization by a pre_init checker
            if ( false === tdAnimationStack._ready_for_initialization ) {
                return;
            }

            // clear the _ready_init_timeout, to stop it doing more checking
            clearTimeout( tdAnimationStack._ready_init_timeout );

            // the tdAnimationStack is activated
            tdAnimationStack.activated = true;

            tdAnimationStack.check_for_new_items( '.td-animation-stack', tdAnimationStack.SORTED_METHOD.sort_left_to_right, false, true );
        },


        /**
         * - the arrays are cleared to be prepared for a reinitialization
         * - the init call is done
         */
        reinit: function() {

            // tdAnimationStack must not be already stopped for initialization by a pre_init checker
            if ( false === tdAnimationStack._ready_for_initialization ) {
                return;
            }

            tdAnimationStack.items = [];
            tdAnimationStack._items_in_view_port = [];
            tdAnimationStack._items_above_view_port = [];

            tdAnimationStack.init();
        },


        /**
         * - compute all items
         * - live_load_items - if 'true' the items will also be loaded on show,
         *                   - if 'false' the items are preloded so they just need to be shown
         */
        compute_items: function(live_load_items) {

            // tdAnimationStack must be activated and not stopped for initialization by the ready_init checker
            if ( ( false === tdAnimationStack.activated ) || ( false === tdAnimationStack._ready_for_initialization ) ) {
                return;
            }

            // the tdAnimationStack.items are processed
            tdAnimationStack._separate_items();

            // the items above the port view are animated
            while ( tdAnimationStack._items_above_view_port.length > 0 ) {
                tdAnimationStack.log( 'animation - above the view port' );

                var item_above_view_port = tdAnimationStack._items_above_view_port.shift();

                if ( live_load_items === true ) {
                    // load current item
                    tdAnimationStack._load_item(item_above_view_port, false);
                    tdAnimationStack.log( '%c item above view port - loaded ', 'background: #fef24e; color: #000;' );

                } else {
                    item_above_view_port.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                    item_above_view_port.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                }

            }


            // the items in the port view are prepared to be animated
            if ( tdAnimationStack._items_in_view_port.length > 0 ) {

                // clear any opened interval by a previous compute_items call
                clearInterval( tdAnimationStack._current_interval );

                var current_animation_item = tdAnimationStack._get_item_from_view_port();

                if ( live_load_items === true ) {
                    // load current item
                    tdAnimationStack._load_item(current_animation_item, false);
                    tdAnimationStack.log( '%c item in view port - loaded ', 'background: #fef24e; color: #000;' );

                } else {
                    current_animation_item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                    current_animation_item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                }

                if ( tdAnimationStack._items_in_view_port.length > 0 ) {

                    tdAnimationStack.log( 'start animation timer' );

                    tdAnimationStack._to_timer( tdAnimationStack._get_right_interval( tdAnimationStack.interval * ( 1 / tdAnimationStack._items_in_view_port.length ) ), live_load_items );
                }
            }
        },


        /**
         * - timer function initially called by a tdAnimationStack.compute_items function, and then it's auto called
         * - it calls a setInterval using the interval parameter
         * @param interval {int} - interval ms
         * @param live_load_items {bool} - whether to also load the img items or just show them
         */
        _to_timer: function( interval, live_load_items ) {

            tdAnimationStack._current_interval = setInterval( function () {

                if ( tdAnimationStack._items_in_view_port.length > 0 ) {

                    var current_animation_item = tdAnimationStack._get_item_from_view_port();

                    tdAnimationStack.log( 'animation at interval: ' + interval );

                    if ( live_load_items === true ) {
                        // load current item
                        tdAnimationStack._load_item(current_animation_item, false);
                        tdAnimationStack.log( '%c item above view port - loaded > _to_timer ', 'background: #3895d3; color: #fff;' );
                    } else {
                        current_animation_item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                        current_animation_item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                    }

                    clearInterval( tdAnimationStack._current_interval );

                    if ( tdAnimationStack._items_in_view_port.length > 0 ) {
                        tdAnimationStack._to_timer( tdAnimationStack._get_right_interval( tdAnimationStack.interval * ( 1 / tdAnimationStack._items_in_view_port.length ) ), live_load_items );
                    }
                }
            }, interval );
        },


        /**
         * - get an item from the tdAnimationStack._items_in_view_port array
         * @returns {tdAnimationStack.item}
         * @private
         */
        _get_item_from_view_port: function() {

            return tdAnimationStack._items_in_view_port.shift();
        },



        /**
         * - get the interval considering tdAnimationStack.min_interval and tdAnimationStack.max_interval
         * @param interval {int} - the checked interval value
         * @returns {int} - the result interval value
         * @private
         */
        _get_right_interval: function( interval ) {

            if ( interval < tdAnimationStack.min_interval ) {
                return tdAnimationStack.min_interval;

            } else if ( interval > tdAnimationStack.max_interval ) {
                return tdAnimationStack.max_interval;
            }
            return interval;
        },


        /**
         * - check where the item is to the view port
         * @param item {tdAnimationStack.item}
         * @returns {number} _ITEM_TO_VIEW_PORT value
         * @private
         */
        _item_to_view_port: function( item ) {

            tdAnimationStack.log( 'position item relative to the view port >> yOffset ' + tdEvents.window_pageYOffset + ' | xOffset ' + tdEvents.window_innerHeight + ' : ' + item.offset_top );

            if ( tdEvents.window_pageYOffset + tdEvents.window_innerHeight < item.offset_top ) {
                return tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_UNDER_VIEW_PORT;

            } else if ( ( tdEvents.window_pageYOffset + tdEvents.window_innerHeight >= item.offset_top ) && ( tdEvents.window_pageYOffset <= item.offset_bottom_to_top ) ) {
                return tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_IN_VIEW_PORT;

            }
            return tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_ABOVE_VIEW_PORT;
        },


        /**
         * - check the sorted tdAnimationStack.items and move them into the _items_above_view_port array or into the _items_in_view_port
         * - the remaining items are kept by the tdAnimationStack.items for next processing
         * @private
         */
        _separate_items: function() {
            if ( 0 === tdAnimationStack.items.length ) {
                return;
            }

            tdAnimationStack.log( '%c _separate_items - total items: ' + tdAnimationStack.items.length + ' ', 'background: #999da0; color: #fff;' );

            while ( tdAnimationStack.items.length > 0 ) {
                var item_to_view_port = tdAnimationStack._item_to_view_port( tdAnimationStack.items[ 0 ] );

                switch ( item_to_view_port ) {
                    case tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_ABOVE_VIEW_PORT :
                        tdAnimationStack._items_above_view_port.push( tdAnimationStack.items.shift() );
                        break;

                    case tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_IN_VIEW_PORT :
                        tdAnimationStack._items_in_view_port.push( tdAnimationStack.items.shift() );
                        break;

                    case tdAnimationStack._ITEM_TO_VIEW_PORT.ITEM_UNDER_VIEW_PORT :
                        tdAnimationStack.log( 'after separation items >> above: ' + tdAnimationStack._items_above_view_port.length + ' in: ' + tdAnimationStack._items_in_view_port.length + ' under: ' + tdAnimationStack.items.length );
                        return;
                }
            }
        },

        /**
         * - loads an item(img)
         * @param item
         * @param item_test
         * @private
         */
        _load_item: function( item, item_test ) {

            if ( undefined === item.itemSrc ) {
                tdAnimationStack.log( '%c item with no data url ', 'background: #fc6600; color: #fff;' );

                // for items for which we have not att data source to load just show the img
                item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );

            } else {

                var itemType = item.itemType;

                if ( itemType !== undefined ) {

                    switch ( itemType ) {
                        case 'image_tag':

                            tdAnimationStack.log( '%c image tag ', 'background: #3ded97; color: #fff;' );

                            //if ( item_test ) {
                            //  item.itemSrc = item.itemSrc + 'caca';
                            //}

                            item.jqueryObj.data('complete', false);

                            // first we check if a retina image was delivered and if the device screen supports it
                            if ( item.itemImgRetinaSrc !== undefined && tdAnimationStack._isHighDensity() === true ) {
                                item.jqueryObj.attr( 'src', item.itemImgRetinaSrc ).load( function(){
                                    item.jqueryObj.data('complete', true);
                                });
                            } else {
                                item.jqueryObj.attr( 'src', item.itemSrc ).load( function(){
                                    item.jqueryObj.data('complete', true);
                                });
                            }

                            var date = new Date();
                            var start_time = date.getTime();

                            // the timer is started
                            var interval_check_loading_image = setInterval( function() {

                                // if there's too much time waiting for image loading, they are made visible
                                var date = new Date();

                                // at every interval step, the element's 'complete' property is checked again
                                if ( item.jqueryObj.data('complete') === true ){

                                    // the img item loading is complete, clear the interval
                                    clearInterval( interval_check_loading_image );

                                    // ..and show the img item
                                    item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                                    item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );

                                    return;
                                }

                                // check if the maximum waiting time has been reached
                                if ( ( date.getTime() - start_time ) > tdAnimationStack.max_waiting_for_init ) {

                                    // the img item loading maximum waiting time has been reached, clear the interval
                                    clearInterval( interval_check_loading_image );

                                    // ..and show the img item
                                    item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                                    item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                                }

                            }, 100);

                            break;

                        case 'css_image':

                            item.jqueryObj.data('complete', false);

                            tdAnimationStack.log( '%c image tag ', 'background: #3ded97; color: #fff;' );

                            // check for retina support and image delivery and if the device screen supports it then load the retina img
                            if ( item.itemImgRetinaSrc !== undefined && tdAnimationStack._isHighDensity() === true ) {

                                /**
                                 * to find out when the bg img is loaded, for spans with css image,
                                 * we create a new img tag and set the src to the css bg img then, after it loads, we set it as bg img for the span
                                 */
                                jQuery('<img/>').attr( 'src', item.itemImgRetinaSrc ).load( function() {
                                    jQuery(this).remove(); //remove the img to prevent memory leaks
                                    item.jqueryObj.data('complete', true);
                                });
                            } else {
                                jQuery('<img/>').attr( 'src', item.itemSrc ).load( function() {
                                    jQuery(this).remove(); //remove the img to prevent memory leaks
                                    item.jqueryObj.data('complete', true);
                                });
                            }

                            var date2 = new Date();
                            var start_time2 = date2.getTime();

                            // the timer is started
                            var interval_check_loading_css_image = setInterval( function() {

                                // if there's too much time waiting for image loading, they are made visible
                                var date = new Date();

                                // at every interval step, the element's 'complete' property is checked again
                                if ( item.jqueryObj.data('complete') === true ){

                                    if ( item.itemImgRetinaSrc !== undefined && tdAnimationStack._isHighDensity() === true ) {
                                        item.jqueryObj.attr( 'style', 'background-image: url(' + item.itemImgRetinaSrc + ')' );
                                    } else {
                                        item.jqueryObj.attr( 'style', 'background-image: url(' + item.itemSrc + ')' );
                                    }

                                    // the img item loading is complete, clear the interval
                                    clearInterval( interval_check_loading_css_image );

                                    // ..and show the img item
                                    item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                                    item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );

                                    return;
                                }

                                // check if the maximum waiting time has been reached
                                if ( ( date.getTime() - start_time2 ) > tdAnimationStack.max_waiting_for_init ) {

                                    // the img item loading maximum waiting time has been reached, clear the interval
                                    clearInterval( interval_check_loading_css_image );

                                    // set the bg image, if we have retina we set the retina img
                                    if ( item.itemImgRetinaSrc !== undefined && tdAnimationStack._isHighDensity() === true ) {
                                        item.jqueryObj.attr( 'style', 'background-image: url(' + item.itemImgRetinaSrc + ')' );
                                    } else {
                                        item.jqueryObj.attr( 'style', 'background-image: url(' + item.itemSrc + ')' );
                                    }

                                    // ..and show the img item
                                    item.jqueryObj.removeClass( tdAnimationStack._animation_css_class1 );
                                    item.jqueryObj.addClass( tdAnimationStack._animation_css_class2 );
                                }

                            }, 100);

                            break;
                    }
                }
            }
        },


        /**
         * - scroll event usually called by tdCustomEvents
         */
        td_events_scroll: function() {
            tdAnimationStack.compute_items(true);
        },



        /**
         * - resize event usually called by tdCustomEvents
         */
        td_events_resize: function() {
            // clear an existing interval
            clearInterval( tdAnimationStack._current_interval );

            // reinitialize tdAnimationStack searching in page for not already animated items [which were already repositioned by resize]
            tdAnimationStack.reinit();
        },



        log: function( msg, style ) {

            if ( style !== undefined ) {
                //console.log(msg, style);
            } else {
                //console.log(msg);
            }

        },


        _isHighDensity: function () {
            return (
                (
                    window.matchMedia && (
                        window.matchMedia(
                            'only screen and (min-resolution: 124dpi), ' +
                            'only screen and (min-resolution: 1.3dppx), ' +
                            'only screen and (min-resolution: 48.8dpcm)'
                        ).matches ||
                        window.matchMedia(
                            'only screen and (-webkit-min-device-pixel-ratio: 1.3), ' +
                            'only screen and (-o-min-device-pixel-ratio: 2.6/2), ' +
                            'only screen and (min--moz-device-pixel-ratio: 1.3), ' +
                            'only screen and (min-device-pixel-ratio: 1.3)'
                        ).matches
                    )
                ) || (
                    window.devicePixelRatio && window.devicePixelRatio > 1.3
                )
            );
        },

        _isRetina: function () {
            return (
                (
                    window.matchMedia && (
                        window.matchMedia(
                            'only screen and (min-resolution: 192dpi), ' +
                            'only screen and (min-resolution: 2dppx), ' +
                            'only screen and (min-resolution: 75.6dpcm)'
                        ).matches ||
                        window.matchMedia(
                            'only screen and (-webkit-min-device-pixel-ratio: 2), ' +
                            'only screen and (-o-min-device-pixel-ratio: 2/1), ' +
                            'only screen and (min--moz-device-pixel-ratio: 2), ' +
                            'only screen and (min-device-pixel-ratio: 2)'
                        ).matches
                    )
                ) || (
                    window.devicePixelRatio && window.devicePixelRatio >= 2
                )
            ) && /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
        }

    };
})();

/* global jQuery:{} */
/* global tdSmartSidebar:{} */
/* global tdUtil:{} */
/* global tdDetect:{} */
/* global tdPullDown:{} */
/* global tdAnimationScroll:{} */
/* global tdAnimationStack:{} */
/* global tdEvents:{} */
/* global tdAffix:{} */

'use strict';

/**
 * affix menu
 */
tdAffix.init({
    menu_selector: '.td-header-menu-wrap',
    menu_wrap_selector: '.td-header-menu-wrap-full',
    tds_snap_menu: tdUtil.getBackendVar('tds_snap_menu'),
    tds_snap_menu_logo: tdUtil.getBackendVar('tds_logo_on_sticky'),
    menu_affix_height: 48,   // value must be set because it can't be computed at runtime because at the time of td_affix.init() we can have no affixed menu on page
    menu_affix_height_on_mobile: 54
});



/**
 * sidebar init
 */
if ( tdUtil.getBackendVar('tds_smart_sidebar') === 'enabled' ) {
    jQuery(window).load(function() {
        // find the rows and the sidebars objects and add them to the magic sidebar object array
        jQuery('.td-ss-row').each(function () {
            var td_smart_sidebar_item = new tdSmartSidebar.item(),
                content = jQuery(this).children('.td-pb-span8').find('.wpb_wrapper:first'),
                sidebar = jQuery(this).children('.td-pb-span4').find('.wpb_wrapper:first');

            if (content.length > 0 && sidebar.length > 0) {
                td_smart_sidebar_item.sidebar_jquery_obj = sidebar;
                td_smart_sidebar_item.content_jquery_obj = content;
                tdSmartSidebar.add_item(td_smart_sidebar_item);
            }
        });


        [ { '.vc_row': '.vc_column'}, { '.vc_row_inner': '.vc_column_inner'} ].forEach(function(val) {

            for ( var prop in val ) {

                var wrapper_outside = prop,
                    wrapper_inside = val[prop];

                jQuery( wrapper_outside ).each(function () {
                    var sidebars = [],
                        content;

                    jQuery(this).children( wrapper_inside ).each(function (index, el) {

                        var $el = jQuery(el);

                        if ($el.hasClass('td-is-sticky')) {
                            sidebars.push($el.find('.wpb_wrapper:first'));
                        } else if ('undefined' === typeof content || content.outerHeight(true) < $el.outerHeight(true)) {
                            content = $el.find('.wpb_wrapper:first');
                        }
                    });


                    if (sidebars.length && 'undefined' !== typeof content) {
                        sidebars.forEach(function (el) {
                            var smartSidebar = new tdSmartSidebar.item();
                            smartSidebar.sidebar_jquery_obj = el;
                            smartSidebar.content_jquery_obj = content;
                            tdSmartSidebar.add_item(smartSidebar);
                        });
                    }
                });
            }
        });



        // check the page to see if we have smart sidebar classes (.td-ss-main-content and .td-ss-main-sidebar)
        if (jQuery('.td-ss-main-content').length > 0 && jQuery('.td-ss-main-sidebar').length > 0) {
            var td_smart_sidebar_item = new tdSmartSidebar.item();
            td_smart_sidebar_item.sidebar_jquery_obj = jQuery('.td-ss-main-sidebar');
            td_smart_sidebar_item.content_jquery_obj = jQuery('.td-ss-main-content');
            tdSmartSidebar.add_item(td_smart_sidebar_item);
        }

        tdSmartSidebar.td_events_resize();
    });
}


/**
 * pulldown lists
 *
 */

jQuery(window).load( function() {

// block subcategory ajax filters!
    jQuery('.td-subcat-filter').each(function (index, element) {
        var jquery_object_container = jQuery(element);
        var horizontal_jquery_obj = jquery_object_container.find('.td-subcat-list:first');

        var pulldown_item_obj = new tdPullDown.item();
        pulldown_item_obj.blockUid = jquery_object_container.parent().parent().data('td-block-uid'); // get the block UID
        pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
        pulldown_item_obj.vertical_jquery_obj = jquery_object_container.find('.td-subcat-dropdown:first');
        pulldown_item_obj.horizontal_element_css_class = 'td-subcat-item';
        pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.closest('.td-block-title-wrap');

        // '.td-pulldown-size' are the elements excluded
        pulldown_item_obj.excluded_jquery_elements = [pulldown_item_obj.container_jquery_obj.find('.td-pulldown-size')];
        tdPullDown.add_item(pulldown_item_obj);
    });


    // on category pages
    jQuery('.td-category-siblings').each(function (index, element) {

        var jquery_object_container = jQuery(element);

        var horizontal_jquery_obj = jquery_object_container.find('.td-category:first');
        var pulldown_item_obj = new tdPullDown.item();

        pulldown_item_obj.blockUid = jquery_object_container.parent().parent().data('td-block-uid'); // get the block UID
        pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
        pulldown_item_obj.vertical_jquery_obj = jquery_object_container.find('.td-subcat-dropdown:first');
        pulldown_item_obj.horizontal_element_css_class = 'entry-category';
        pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.parents('.td-category-siblings:first');
        tdPullDown.add_item(pulldown_item_obj);

    });






});



/**
 * parallax effect
 */

// array keeping the tdAnimationScroll.item items used for backstretch
var td_backstretch_items = [];


jQuery(window).ready(function() {

    jQuery('.td-backstretch').each(function (index, element) {

        if (!jQuery(element).hasClass('not-parallax')) {

            var item = new tdAnimationScroll.item();
            item.jqueryObj = jQuery(element);
            item.wrapper_jquery_obj = item.jqueryObj.parent();

            // - ideal translation is when the top of wrapper_jquery_obj is at the top of the view port, the jqueryObj image
            // is also already translated at the top of the view port
            // - the start_value should be item.wrapper_jquery_obj.offset().top + how much the jqueryObj was translated

            tdAnimationScroll.add_item(item);

            // we keep the tdAnimationScroll.item to change operation settings when the viewport is changing
            td_backstretch_items.push(item);

            td_compute_backstretch_item(item);
        }
    });


    jQuery('.td-parallax-header').each(function (index, element) {

        var item = new tdAnimationScroll.item();
        item.jqueryObj = jQuery(element);

        item.add_item_property('move_y', 50, 100, 0, 100, '');
        item.add_item_property('opacity', 50, 100, 1, 0, '');

        item.animation_callback = function () {

            var move_y_property = parseFloat(item.computed_item_properties['move_y']).toFixed();
            var opacity_property = parseFloat(item.computed_item_properties['opacity']);

            item.jqueryObj.css({
                '-webkit-transform': 'translate3d(0px,' + move_y_property + 'px, 0px)',
                'transform': 'translate3d(0px,' + move_y_property + 'px, 0px)'
            });

            item.jqueryObj.css('transform', 'translate3d(0px,' + move_y_property + 'px, 0px)');

            item.jqueryObj.css('opacity', opacity_property);

            item.redraw = false;
        }

        tdAnimationScroll.add_item(item);
    });



    tdAnimationScroll.compute_all_items();



    // We need to come after videos wrappers have been inserted in page (!!!! after iframe load??)
    setTimeout(function() {

        jQuery('.tdc-video-parallax-wrapper').each(function (index, element) {
            td_compute_parallax_background(element);
        });

        tdAnimationScroll.compute_all_items();

    }, 300);





    // load animation stack
    tdAnimationStack.ready_init();
});




function td_compute_parallax_background( htmlElement ) {

    var $el = jQuery(htmlElement);

    var move_y_val = Math.round( $el.height() * 0.2 ),
        start_move_y_val = -1 * move_y_val,
        end_move_y_val = move_y_val;

    var item = new tdAnimationScroll.item();
    item.jqueryObj = $el;

    item.add_item_property('move_y', 0, 100, start_move_y_val, end_move_y_val, '');

    item.animation_callback = function () {

        var move_y_property = parseFloat(item.computed_item_properties['move_y']).toFixed();

        item.jqueryObj.css({
            '-webkit-transform': 'translate3d(0px,' + move_y_property + 'px, 0px) scale(1.2)',
            'transform': 'translate3d(0px,' + move_y_property + 'px, 0px) scale(1.2)'
        });

        item.redraw = false;

        // Flag used to reinitialize item at resize (we need to reinitialize it because its container dimensions can change - and also the properties applied to the item )
        item.td_video_parallax = true;
    }

    tdAnimationScroll.add_item(item);
}





/**
 * Function used to register the 'move_y' property for every td_animations_scroll.item item of the td_backstretch_items array.
 * It scales the object image and translate it. At first it is translated so its bottom is at the bottom of the viewport,
 * but considering the backstretch css classes applied
 *
 * @param item tdAnimationScroll.item
 */
function td_compute_backstretch_item(item) {

        // Important! The following variables must be computed after add_item calling function, because they need item.full_height, item.offset_top, etc

        // percent when the object is in initial position
        // Important! It doesn't matter if the document is scrolled
        var initial_percent = (tdEvents.window_innerHeight - item.offset_top) * 100 / (tdEvents.window_innerHeight + item.full_height);

        // percent when the object has its top at the top of the window
        var intermediary_top_percent =  (tdEvents.window_innerHeight) * 100 / (tdEvents.window_innerHeight + item.full_height);


        // IMPORTANT! We suppose the item.offset_top is positive


        // the value used to compute the scale_factor
        // Important! It can be any value
        var scale_seed = item.offset_top / 4;

        // if item.offset_top is zero, we set the scale_seed at a custom value
        if (scale_seed == 0) {
            scale_seed = 100;
        }

        // the start_value is half of the scale_seed, so the object [image] is translated as its bottom is at the bottom of its view
        var start_value = - scale_seed / 2;


        // DO NOT DELETE THE NEXT CODE LINES. The right value would be the next, but the divide operation does not have 100% accuracy, so we increase the interval
        // and so we are sure the object is not translated more than needed when is at the top of the window
        //
        // When the top of the view is at the top of the window, the object [image] must be already translated at the top of the window.
        //
        //var end_value = ((100 - initial_percent) * scale_seed) / (intermediary_top_percent - initial_percent);;
        //
        //or actually
        //
        //var end_value = ((100 - initial_percent) * (item.offset_top / 2)) / (intermediary_top_percent - initial_percent);;

        var end_value = ((100 - initial_percent) * (scale_seed / 1.2)) / (intermediary_top_percent - initial_percent);

        // fix for firefox. It rounds up and loose 1 pixel
        start_value += 0.5;


        // if there already exists a 'move_y' property, it is removed
        item.remove_item_property('move_y');

        item.add_item_property('move_y', initial_percent, 100, start_value, end_value, '');


        var scale_factor = parseFloat(1 + Math.abs(scale_seed) / item.full_height).toFixed(2);


        // if there's already registered an 'animation_callback' function, it is removed
        delete item.animation_callback;

        item.animation_callback = function () {

            var property_value = parseFloat(item.computed_item_properties['move_y']).toFixed();

            item.jqueryObj.css({
                'left': '50%',
                '-webkit-transform': 'translate3d(-50%,' + property_value + 'px, 0px) scale(' + scale_factor + ',' + scale_factor + ')',
                'transform': 'translate3d(-50%,' + property_value + 'px, 0px) scale(' + scale_factor + ',' + scale_factor + ')'
            });

            item.redraw = false;
        };
}

/**
 * Created by ra on 8/12/2015.
 */

/* global jQuery:false */
/* global tdInfiniteLoader:false */
/* global tdAnimationStack:{} */
/* global tdSmartSidebar:false */
/* global tdLoadingBox:{} */
/* global tds_theme_color_site_wide:string */


/* global td_ajax_url:false */

/**
 *   tdAjaxLoop.init() is called from: @see includes/wp_booster/td_page_generator::render_infinite_pagination
 */
var tdAjaxLoop = {};

(function () {
    'use strict';

    tdAjaxLoop = {
        loopState: {
            'sidebarPosition': '',
            'moduleId': 1,
            'currentPage': 1,
            'max_num_pages': 0,
            'atts' : {},
            'ajax_pagination_infinite_stop' : 0,
            'server_reply_html_data': ''
        },


        /**
         *   tdAjaxLoop.init() is called from: @see includes/wp_booster/td_page_generator::render_infinite_pagination
         *   only when needed
         */
        init: function () {
            jQuery('.td-ajax-loop-infinite').each( function() {
                // create a new infinite loader item
                var tdInfiniteLoaderItem = new tdInfiniteLoader.item();

                tdInfiniteLoaderItem.jqueryObj = jQuery(this);
                tdInfiniteLoaderItem.uid = 'tdAjaxLoop';


                /**
                 * the callback when the bottom of the element is visible on screen and we need to do something - like load another page
                 * - the callback does not fire again until tdInfiniteLoader.enable_is_visible_callback is called @see tdInfiniteLoader.js:95
                 */
                tdInfiniteLoaderItem.isVisibleCallback = function () {      // the is_visible callback is called when we have to pull new content up because the element is visible

                    if (
                        0 !== tdAjaxLoop.loopState.ajax_pagination_infinite_stop &&
                        tdAjaxLoop.loopState.currentPage >= tdAjaxLoop.loopState.ajax_pagination_infinite_stop &&
                        tdAjaxLoop.loopState.currentPage + 1 < tdAjaxLoop.loopState.max_num_pages  // do we have a next page?
                    ) {
                        // stop the callback and show the load more button
                        jQuery('.td-load-more-infinite-wrap')
                            .css('display', 'block')
                            .css('visibility', 'visible')
                        ;

                    } else {
                        // load up the next page
                        tdAjaxLoop.infiniteNextPage(false);
                    }
                };
                tdInfiniteLoader.addItem(tdInfiniteLoaderItem);
            });


            // click on load more - the button should not be visible only when the  ajax_pagination_infinite_stop limit is reached
            jQuery('.td-load-more-infinite-wrap').on( 'click', function(event) {
                event.preventDefault();


                jQuery('.td-load-more-infinite-wrap').css('visibility', 'hidden');

                tdAjaxLoop.infiniteNextPage(true);
            });
        },


        infiniteNextPage: function (isLoadMoreButton) {

            // prepare the request object
            tdAjaxLoop.loopState.currentPage++ ;
            tdAjaxLoop.loopState.server_reply_html_data = '';

            // check here to avoid making an unnecessary ajax request when using infinite loading without button
            if ( tdAjaxLoop.loopState.currentPage > tdAjaxLoop.loopState.max_num_pages ) {
                //console.log('END' + tdAjaxLoop.loopState.currentPage + ' max: ' + tdAjaxLoop.loopState.max_num_pages);
                return;
            }



            jQuery('.td-ss-main-content').append('<div class="td-loader-gif td-loader-infinite td-loader-animation-start"></div>');
            tdLoadingBox.init(tds_theme_color_site_wide, 45);  //init the loading box
            setTimeout(function () {
                jQuery('.td-loader-gif')
                    .removeClass('td-loader-animation-start')
                    .addClass('td-loader-animation-mid');
            }, 50);


            var requestData = {
                action: 'td_ajax_loop',
                loopState: tdAjaxLoop.loopState
            };

            //console.log('request:');
            //console.log(tdAjaxLoop.loopState);
            jQuery.ajax({
                type: 'POST',
                url: td_ajax_url,
                cache:true,
                data: requestData,
                success: function(data, textStatus, XMLHttpRequest) {
                    tdAjaxLoop._processAjaxRequest(data, isLoadMoreButton);
                },
                error: function(MLHttpRequest, textStatus, errorThrown) {
                    //console.log(errorThrown);
                }
            });
        },

        _processAjaxRequest: function (data, isLoadMoreButton) {
            // stop the loader
            jQuery('.td-loader-gif').remove();
            tdLoadingBox.stop();

            var dataObj = jQuery.parseJSON(data);



            // empty reply - stop everything
            if ( '' === dataObj.server_reply_html_data  ) {
                jQuery('.td-load-more-infinite-wrap').css('visibility', 'hidden');
                return;
            }


            /**
             * @var {tdAjaxLoop.loopState}
             */

            jQuery('.td-ajax-loop-infinite').before(dataObj.server_reply_html_data);

            //console.log('reply:');
            //console.log(dataObj);

            if ( parseInt( dataObj.currentPage ) >= parseInt(dataObj.max_num_pages) ) {
                jQuery('.td-load-more-infinite-wrap').css('visibility', 'hidden');
            } else {
                if ( true === isLoadMoreButton ) {
                    jQuery('.td-load-more-infinite-wrap').css('visibility', 'visible');
                }
            }

            setTimeout( function () {
                tdAnimationStack.check_for_new_items('.td-main-content' + ' .td-animation-stack', tdAnimationStack.SORTED_METHOD.sort_left_to_right, true, false);
                //tdSmartSidebar.compute();
            }, 200);


            // on load more button, we don't have to compute the infinite loader event
            if ( true === isLoadMoreButton ) {
                return;
            }

            setTimeout( function() {
                //refresh waypoints for infinit scroll tdInfiniteLoader
                tdInfiniteLoader.computeTopDistances();
                tdInfiniteLoader.enable_is_visible_callback('tdAjaxLoop');
                //tdSmartSidebar.compute();
            }, 500);


            setTimeout( function() {
                tdInfiniteLoader.computeTopDistances();
            }, 1000);

            setTimeout( function() {
                tdInfiniteLoader.computeTopDistances();
            }, 1500);

        }
    };

})();
/**
 * Created by ra on 9/30/2015.
 */

/*
 tdWeather.js
 v1.0
 */
/* global jQuery:false */
/* global tdDetect:false */
/* global tdUtil:false */
/* global alert:false */
/* global tdLocalCache:false */




var tdWeather = {};

( function(){
    "use strict";

    tdWeather = {

        // used to translate the OWM code to icon
        _icons: {
            // day
            '01d' : 'clear-sky-d',
            '02d' : 'few-clouds-d',
            '03d' : 'scattered-clouds-d',
            '04d' : 'broken-clouds-d',
            '09d' : 'shower-rain-d',   // ploaie hardcore
            '10d' : 'rain-d',          // ploaie light
            '11d' : 'thunderstorm-d',
            '13d' : 'snow-d',
            '50d' : 'mist-d',

            //night:
            '01n' : 'clear-sky-n',
            '02n' : 'few-clouds-n',
            '03n' : 'scattered-clouds-n',
            '04n' : 'broken-clouds-n',
            '09n' : 'shower-rain-n',   // ploaie hardcore
            '10n' : 'rain-n',          // ploaie light
            '11n' : 'thunderstorm-n',
            '13n' : 'snow-n',
            '50n' : 'mist-n'
        },

        _currentRequestInProgress: false, // prevent multiple parallel requests
        _currentItem: '',  // current weather object, it is set on click and after we modify it, it will be displayed

        // latitude and longitude position, used in callback hell
        _currentLatitude: 0,
        _currentLongitude: 0,
        _currentPositionCacheKey: '',
        _currentLocationCacheKey: '',

        //location
        _currentLocation: '',

        // all the weather items
        items: [],  /** an item is json encoded from this in PHP: @see td_weather::$weather_data */

        // location set filed open
        _is_location_open: false,



        /**
         * Init the class, we hook the click event
         */
        init: function () {

            // weather location button click
            jQuery('.td-icons-location').on( 'click', function() {
                if (tdWeather._currentRequestInProgress === true) {
                    return;
                }
                tdWeather._currentRequestInProgress = true;

                // get the block id
                tdWeather._currentItem = tdWeather._getItemByBlockID(jQuery(this).data('block-uid'));

                // get the position + callback
                var timeoutVal = 10 * 1000 * 1000;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        tdWeather._updateLocationCallback,
                        tdWeather._displayLocationApiError,
                        {enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 600000});
                }

                tdWeather._currentRequestInProgress = false;

            });

            jQuery('.td-weather-now').on( 'click', function(){
                if (tdWeather._currentRequestInProgress === true) {
                    return;
                }
                tdWeather._currentRequestInProgress = true;

                // get the block id
                tdWeather._currentItem = tdWeather._getItemByBlockID(jQuery(this).data('block-uid'));

                if (tdWeather._currentItem.current_unit === 1) {
                    tdWeather._currentItem.current_unit = 0;
                } else {
                    tdWeather._currentItem.current_unit = 1;
                }
                tdWeather._renderCurrentItem();
            });

            /**
             *  set manual location
             *  */

            jQuery('.td-manual-location-form').submit( function(event){
                event.preventDefault();

                if (tdWeather._currentRequestInProgress === true) {
                    return;
                }

                tdWeather._currentRequestInProgress = true;

                tdWeather._currentItem = tdWeather._getItemByBlockID(jQuery(this).data('block-uid'));
                //console.debug(this);

                tdWeather._currentLocation = jQuery('input#' + jQuery(this).data('block-uid')).val();

                tdWeather._updateLocationCallback2(tdWeather._currentLocation);

                tdWeather._currentRequestInProgress = false;
                tdWeather._hide_manual_location_form();
            });


            jQuery(document).on( 'click', function(ev) {

                if ( tdWeather._is_location_open === true
                    && jQuery(ev.target).hasClass('td-location-set-input') !== true
                    && jQuery(ev.target).hasClass('td-location-set-button') !== true ) {
                    tdWeather._hide_manual_location_form();
                }

            });
        },


        /**
         * adds a new weather item
         * item.block_uid is REQUIERED, based on that id, we delete the item from the array *as of 27.4.2016 the id is not used
         * @param item object - an item is json encoded from this in PHP: @see td_weather::$weather_data
         */
        addItem: function (item) {
            tdWeather.items.push(item);
        },

        ///
        // For now it's not needed because td_weater.php does not add js if it detects td-composer
        // **
        // * Deletes an item base on blockUid.
        // * Make sure that you add block_uid to items that you expect to be deleted
        // * @param blockUid
        // */
        //deleteItem: function(blockUid) {
        //    for (var cnt = 0; cnt < tdWeather.items.length; cnt++) {
        //        if (tdWeather.items[cnt].block_uid === blockUid) {
        //            tdWeather.items.splice(cnt, 1); // remove the item from the "array"
        //            return true;
        //        }
        //    }
        //    return false;
        //},


        /**
         * 1. LOCATION api - position callback
         * @param position
         * @private
         */
        _updateLocationCallback: function(position) {
            tdWeather._currentLatitude = position.coords.latitude;
            tdWeather._currentLongitude = position.coords.longitude;
            tdWeather._currentPositionCacheKey = position.coords.latitude + '_' + position.coords.longitude; //  update the cache key for current position

            // check the cache first and avoid doing the same ajax request again
            if (tdLocalCache.exist(tdWeather._currentPositionCacheKey + '_today')) {
                tdWeather._owmGetTodayDataCallback(tdLocalCache.get(tdWeather._currentPositionCacheKey + '_today'));
            } else {
                var weather = 'https://api.openweathermap.org/data/2.5/weather?lat=' + tdWeather._currentLatitude + '&lon=' + tdWeather._currentLongitude + '&units=metric&lang=' + tdWeather._currentItem.api_language + '&appid=' + tdWeather._currentItem.api_key;
                jQuery.ajax({
                    dataType: "jsonp",
                    url: weather,
                    success: tdWeather._owmGetTodayDataCallback,
                    cache: true
                });
            }

            //alert(position.coords.latitude + ' ' + position.coords.longitude);

        },


        /**
         * 2. AJAX callback for today forecast, this also makes a call to ajax 5 days forecast
         * @param data - OWM api response - NOTICE: We don't check anything if it's correct :)
         * @private
         */
        _owmGetTodayDataCallback: function (data) {
            // save the data to localCache
            tdLocalCache.set(tdWeather._currentPositionCacheKey + '_today', data);


            // prepare the tdWeather._currentItem object, notice that tdWeather._currentItem is a reference to an object stored in tdWeather.items
            tdWeather._currentItem.api_location = data.name;
            tdWeather._currentItem.today_clouds = tdUtil.round(data.clouds.all);
            tdWeather._currentItem.today_humidity = tdUtil.round(data.main.humidity);
            tdWeather._currentItem.today_icon = tdWeather._icons[data.weather[0].icon];
            tdWeather._currentItem.today_icon_text = data.weather[0].description;
            tdWeather._currentItem.today_max[0] = tdUtil.round(data.main.temp_max, 1);                                  //celsius
            tdWeather._currentItem.today_max[1] = tdWeather._celsiusToFahrenheit(data.main.temp_max);                   //imperial
            tdWeather._currentItem.today_min[0] = tdUtil.round(data.main.temp_min, 1);                                  //celsius
            tdWeather._currentItem.today_min[1] = tdWeather._celsiusToFahrenheit(data.main.temp_min);                   //imperial
            tdWeather._currentItem.today_temp[0] = tdUtil.round(data.main.temp, 1);                                     //celsius
            tdWeather._currentItem.today_temp[1] = tdWeather._celsiusToFahrenheit(data.main.temp);                      //imperial
            tdWeather._currentItem.today_wind_speed[0] = tdUtil.round(data.wind.speed, 1);                              //metric
            tdWeather._currentItem.today_wind_speed[1] = tdWeather._kmphToMph(data.wind.speed);                         //imperial

            //console.log(tdWeather._currentItem);
            //console.log(data);

            // check the cache first and avoid doing the same ajax request again
            if (tdLocalCache.exist(tdWeather._currentPositionCacheKey)) {
                tdWeather._owmGetFiveDaysData(tdLocalCache.get(tdWeather._currentPositionCacheKey));
            } else {
                var weather = 'https://api.openweathermap.org/data/2.5/forecast?lat=' + tdWeather._currentLatitude + '&lon=' + tdWeather._currentLongitude + '&units=metric&lang=' + tdWeather._currentItem.api_language + '&appid=' + tdWeather._currentItem.api_key;
                //console.log('forecast: ' + weather);
                jQuery.ajax({
                    dataType: "jsonp",
                    url: weather,
                    success: tdWeather._owmGetFiveDaysData,
                    cache:true
                });
            }

        },


        /**
         * 3. AJAX callback for the 5 days forecast
         * @param data - OWM api response NOTICE: We don't check anything if it's correct :)
         * @private
         */
        _owmGetFiveDaysData: function (data) {
            // save the data to localCache
            tdLocalCache.set(tdWeather._currentPositionCacheKey, data);

            // process the data
            for (var item_index = 0; item_index < tdWeather._currentItem.forecast.length ; item_index++) {
                //limit forecast to 5 days - api brings 35 x 3 hour intervals and at the end of the day you get 6 days forecast
                if (item_index === 5) {
                    break;
                }
                var current_forecast = tdWeather._currentItem.forecast[item_index];
                current_forecast.day_temp[0] = tdUtil.round(data.list[current_forecast.owm_day_index].main.temp_max);   //celsius
                current_forecast.day_temp[1] = tdWeather._celsiusToFahrenheit(current_forecast.day_temp[0]);            //imperial
            }
            tdWeather._renderCurrentItem();
        },


        /**
         * 4. Here we render the global tdWeather._currentItem object to the screen. The object already contains all the needed information
         * about where and what we have to render.
         * @private
         */
        _renderCurrentItem: function () {

            //console.log('.' + tdWeather._currentItem.block_uid + ' .td-weather-city');

            var blockInner = jQuery('#' + tdWeather._currentItem.block_uid);

            var currentLatitude = tdWeather._currentLatitude;
            var currentLongitude = tdWeather._currentLongitude;
            var currentLocation = tdWeather._currentLocation;

            // city
            blockInner.find('.td-weather-city').html(tdWeather._currentItem.api_location);

            if (currentLocation === '' && ( currentLatitude === 0 && currentLongitude === 0)){
                blockInner.find('.td-weather-city').html(tdWeather._currentItem.api_location);
            }

            // conditions
            blockInner.find('.td-weather-condition').html(tdWeather._currentItem.today_icon_text);

            // animation
            // we remove all the classes! including the animation ones
            var icon_el = blockInner.find('.td-w-today-icon');
            icon_el.removeClass();
            icon_el.addClass('td-w-today-icon');
            icon_el.addClass(tdWeather._currentItem.today_icon);

            var currentTempUnit = tdWeather._currentItem.current_unit;
            var currentSpeedLabel = 'kmh';
            var currentTempLabel = 'C';

            // preapare the labels
            if (currentTempUnit === 1) {
                currentSpeedLabel = 'mph';
                currentTempLabel = 'F';
            }


            // main temp
            blockInner.find('.td-big-degrees').html(tdWeather._currentItem.today_temp[currentTempUnit]);

            // main temp units
            blockInner.find('.td-weather-unit').html(currentTempLabel);


            // high
            blockInner.find('.td-w-high-temp').html(tdWeather._currentItem.today_max[currentTempUnit]);

            // low
            blockInner.find('.td-w-low-temp').html(tdWeather._currentItem.today_min[currentTempUnit]);

            // humidity
            blockInner.find('.td-w-today-humidity').html(tdWeather._currentItem.today_humidity + '%');

            // wind speed
            blockInner.find('.td-w-today-wind-speed').html(tdWeather._currentItem.today_wind_speed[currentTempUnit] + currentSpeedLabel);

            // clouds
            blockInner.find('.td-w-today-clouds').html(tdWeather._currentItem.today_clouds + '%');

            // full list of items! - just the temperature
            for (var item_index = 0; item_index < tdWeather._currentItem.forecast.length ; item_index++) {
                blockInner.find('.td-day-' + item_index).html(tdWeather._currentItem.forecast[item_index].day_name);
                blockInner.find('.td-degrees-' + item_index).html(tdWeather._currentItem.forecast[item_index].day_temp[currentTempUnit]);
            }


            tdWeather._currentRequestInProgress = false; // allow other requests to take place
        },


        /**
         * gets a weather item based on block_uid
         * @param block_uid
         * @returns {*}
         * @private
         */
        _getItemByBlockID: function (block_uid) {
            for (var item_index = 0; item_index < tdWeather.items.length; item_index++) {
                if (tdWeather.items[item_index].block_uid === block_uid) {
                    return tdWeather.items[item_index];
                }
            }
            return false;
        },


        /**
         * Displays a friendly error when the location api fails
         * @param error - a location api error object?
         * @private
         */
        _displayLocationApiError: function (error) {

            if (error.code === 1) {
                if (tdDetect.isAndroid) {

                    //show manual location form
                    tdWeather._show_manual_location_form();

                    //alert('Please enable your gps and reload the page.');
                }

                else if (tdDetect.isIos) {
                    alert("Please enable Location services for Safari Websites and reload the page. \n ---------------------- \nSettings > Privacy > Location Services");
                    return;
                }

                //alert("Permission denied. Enable GPS or Location services and reload the page");
                //show manual location form
                tdWeather._show_manual_location_form();
            }

            //show manual location form
            tdWeather._show_manual_location_form();
        },


        /**
         * C to F converter. It rounds on big F numbers because we don't have space on the UI.
         * @param celsiusDegrees
         * @returns {*}
         * @private
         */
        _celsiusToFahrenheit: function (celsiusDegrees) {
            var f_degrees = celsiusDegrees * 9 / 5 + 32;

            var rounded_val = tdUtil.round(f_degrees, 1);
            if (rounded_val > 99.9) {  // if the value is bigger than 100, round it
                return tdUtil.round(f_degrees);
            }

            return rounded_val;
        },

        /**
         * converter for KMH -> MPH  ex: 2.3
         * @param $kmph
         * @returns {*}
         * @private
         */
        _kmphToMph: function ($kmph) {
            return tdUtil.round($kmph * 0.621371192, 1);
        },

        /**
         * *************************************************************************************************************
         *      set manual location for weather widget
         * *************************************************************************************************************
         */

        /**
         * shows the manual location form
         */

        _show_manual_location_form: function (){

            tdWeather._currentItem = tdWeather._getItemByBlockID(tdWeather._currentItem.block_uid);

            jQuery('#' + tdWeather._currentItem.block_uid).find('.td-weather-set-location').addClass( 'td-show-location' );
            jQuery('.td-manual-location-form input').focus();

            tdWeather._is_location_open = true;

        },

        /**
         * hides the manual location form
         */

        _hide_manual_location_form: function (){

            jQuery('#' + tdWeather._currentItem.block_uid).find('.td-weather-set-location').removeClass('td-show-location');

            tdWeather._is_location_open = false;
        },

        /**
         *  Location API - position callback 2 - used on chrome or other browsers that do not allow current position retrieving
         * @param location
         */

        _updateLocationCallback2: function(location){

            tdWeather._currentLocationCacheKey = location;

            // check the cache first and avoid doing the same ajax request again
            if (tdLocalCache.exist(tdWeather._currentLocationCacheKey + '_today')) {
                tdWeather._owmGetTodayDataCallback2(tdLocalCache.get(tdWeather._currentLocationCacheKey + '_today'));

            } else {

                //console.log('city weather api request!');
                var weather = 'https://api.openweathermap.org/data/2.5/weather?q=' + encodeURIComponent(location) + '&lang=' + tdWeather._currentItem.api_language + '&units=metric&appid=' + tdWeather._currentItem.api_key;

                //console.log('city api request url: ' + weather);

                jQuery.ajax({
                    dataType: "jsonp",
                    url: weather,
                    success: tdWeather._owmGetTodayDataCallback2,
                    cache: true
                });
            }
        },


        /**
         * AJAX callback for today forecast on manual city location api request
         * @param data - OWM api response
         *
         */

        _owmGetTodayDataCallback2: function (data) {
            // save the data to localCache
            tdLocalCache.set(tdWeather._currentLocationCacheKey + '_today', data);


            // prepare the tdWeather._currentItem object, notice that tdWeather._currentItem is a reference to an object stored in tdWeather.items
            tdWeather._currentItem.api_location = data.name;
            tdWeather._currentItem.today_clouds = tdUtil.round(data.clouds.all);
            tdWeather._currentItem.today_humidity = tdUtil.round(data.main.humidity);
            tdWeather._currentItem.today_icon = tdWeather._icons[data.weather[0].icon];
            tdWeather._currentItem.today_icon_text = data.weather[0].description;
            tdWeather._currentItem.today_max[0] = tdUtil.round(data.main.temp_max, 1);                                  //celsius
            tdWeather._currentItem.today_max[1] = tdWeather._celsiusToFahrenheit(data.main.temp_max);                   //imperial
            tdWeather._currentItem.today_min[0] = tdUtil.round(data.main.temp_min, 1);                                  //celsius
            tdWeather._currentItem.today_min[1] = tdWeather._celsiusToFahrenheit(data.main.temp_min);                   //imperial
            tdWeather._currentItem.today_temp[0] = tdUtil.round(data.main.temp, 1);                                     //celsius
            tdWeather._currentItem.today_temp[1] = tdWeather._celsiusToFahrenheit(data.main.temp);                      //imperial
            tdWeather._currentItem.today_wind_speed[0] = tdUtil.round(data.wind.speed, 1);                              //metric
            tdWeather._currentItem.today_wind_speed[1] = tdWeather._kmphToMph(data.wind.speed);                         //imperial


            // check the cache first and avoid doing the same ajax request again
            if (tdLocalCache.exist(tdWeather._currentLocationCacheKey)) {
                tdWeather._owmGetFiveDaysData2(tdLocalCache.get(tdWeather._currentLocationCacheKey));

            } else {

                //console.log('api forecast request!');

                var weather = 'https://api.openweathermap.org/data/2.5/forecast?q=' + tdWeather._currentItem.api_location + '&lang=' + tdWeather._currentItem.api_language + '&units=metric&cnt=35&appid=' + tdWeather._currentItem.api_key;

                //console.log('city forecast api request url: ' + weather);

                jQuery.ajax({
                    dataType: "jsonp",
                    url: weather,
                    success: tdWeather._owmGetFiveDaysData2,
                    cache:true
                });
            }

        },


        /**
         * AJAX callback for 5 days forecast on manual city location api request
         * @param data - OWM api response
         *
         */

        _owmGetFiveDaysData2: function (data) {
            // save the data to localCache
            tdLocalCache.set(tdWeather._currentLocationCacheKey, data);

            var newForecast = {},
                newForecastIsEmpty = true,
                ObjProto = Object.prototype,
                hasOwnProperty = ObjProto.hasOwnProperty; //safe reference to the hasOwnProperty function, in case it's been overridden accidentally

            for (var list_item_index = 0; list_item_index <  data.list.length ; list_item_index++) {
                if (hasOwnProperty.call(data.list[list_item_index], 'dt')) {

                    var timestamp = data.list[list_item_index].dt,
                        currentDay = td_date_i18n('Ymd', timestamp);

                    if (hasOwnProperty.call(newForecast, currentDay) === false) {
                        newForecast[currentDay] = {
                            timestamp: timestamp,
                            day_name: td_date_i18n('D', timestamp),
                            day_temp: [
                                tdUtil.round(data.list[list_item_index].main.temp_max),
                                tdUtil.round(tdWeather._celsiusToFahrenheit(data.list[list_item_index].main.temp_max))
                            ],
                            owm_day_index: list_item_index
                        }
                    } else {
                        if (newForecast[currentDay].day_temp[0] < tdUtil.round(data.list[list_item_index].main.temp_max)) {

                            newForecast[currentDay].day_temp[0] = tdUtil.round(data.list[list_item_index].main.temp_max);
                            newForecast[currentDay].day_temp[1] = tdUtil.round(tdWeather._celsiusToFahrenheit(data.list[list_item_index].main.temp_max));
                        }
                    }

                    newForecastIsEmpty = false;
                }
            }


            if (newForecastIsEmpty === false) {
                tdWeather._currentItem.forecast = [];
                for (var key in newForecast) {
                    //limit forecast to 5 days - api brings 35 x 3 hour intervals and at the end of the day you get 6 days forecast
                    if (tdWeather._currentItem.forecast.length === 5) {
                        break;
                    }
                    tdWeather._currentItem.forecast[tdWeather._currentItem.forecast.length] = newForecast[key];
                }
            }

            tdWeather._renderCurrentItem();
        }

    };  // end tdWeather
})();

tdWeather.init(); //init the class
/* global jQuery:{} */
/* global tdUtil:{} */
/* global tdTrendingNow:{} */

jQuery( window ).load(function() {

    'use strict';

    jQuery( 'body' ).addClass( 'td-js-loaded' );

    window.tdAnimationStack.init();
});

jQuery( window ).ready(function() {

    'use strict';

    /*
     - code used to allow external links from td_smart_list, when the Google Yoast "Track outbound click and downloads" is checked
     - internal links ("#with-hash") are allowed too
     - test the links on incognito, by default Google analytics by yoast ignores the Administrator and Editor users
     */

    jQuery( '.td_smart_list_1 a, .td_smart_list_3 a').on( 'click', function( event ) {
        if ( event.target === event.currentTarget ) {
            var targetAttributeContent = jQuery( this ).attr( 'target' );
            var donwloadAttributeIsSet = jQuery( this )[0].hasAttribute( 'download' );
            var currentUrl = jQuery( this ).attr( 'href' );
            //if target is _blank open the link in a new window
            if (donwloadAttributeIsSet) {
                //link contains download attribute - do nothing, let it download
            } else if (targetAttributeContent == '_blank') {
                event.preventDefault();
                window.open(currentUrl);
            } else {
            //regular links
                if (( window.location.href !== currentUrl ) && tdUtil.isValidUrl(currentUrl)) {
                    window.location.href = currentUrl;
                }
            }
        }
    });

    //trending now
    jQuery('.td_block_trending_now').each(function(){
        var item = new tdTrendingNow.item(),
            wrapper = jQuery(this).find('.td-trending-now-wrapper'),
            autoStart = wrapper.data('start'),
            iCont = 0;

        //block unique ID
        item.blockUid = jQuery(this).data('td-block-uid');

        //set trendingNowAutostart
        if (autoStart !== 'manual') {
            item.trendingNowAutostart = autoStart;
        }

        //take the text from each post from current trending-now-wrapper
        jQuery('#' + item.blockUid + ' .td-trending-now-post').each(function() {
            //trending_list_posts[i_cont] = jQuery(this)[0].outerHTML;
            item.trendingNowPosts[iCont] = jQuery(this);
            //increment the counter
            iCont++;
        });

        /**
         * if an item does not have posts no animation is required so no item is created
         * because the tdTrendingNow library for animations is not needed
         * @see tdTrendingNow.addItem
         */

        if (typeof item.trendingNowPosts === 'undefined' || item.trendingNowPosts.length < 1) {
            return;
        }
        //add the item
        tdTrendingNow.addItem(item);

    });
    jQuery('.td-trending-now-nav-left').on('click', function(event) {
        event.preventDefault();
        var blockUid = jQuery(this).data('block-id');
        tdTrendingNow.itemPrev(blockUid);
    });
    jQuery('.td-trending-now-nav-right').on('click', function(event) {
        event.preventDefault();
        var blockUid = jQuery(this).data('block-id');
        tdTrendingNow.itemNext(blockUid);

    });

    //trending now
    //tdTrendingNowObj.tdTrendingNow();

    //call to trending now function to start auto scroll
    //tdTrendingNowObj.tdTrendingNowAutoStart();
});

/**
 * Created by tagdiv on 29.09.2015.
 */

/* global jQuery:{} */

var tdAnimationSprite = {};

(function(){
    'use strict';

    tdAnimationSprite = {

        items: [],

        // flag used to not call requestAnimationFrame until the previous requestAnimationFrame callback runs
        isInRequestAnimation: false,


        // The item that needs animation
        item: function item() {



            // here we store the block Unique ID. This enables us to delete the item via this id @see tdPullDown.deleteItem
            this.blockUid = '';

            // boolean - an item must be initialized only once
            this._isInitialized = false;

            // boolean - an item can be paused and restarted
            this.paused = false;

            // boolean - the animation automatically starts at the computing item
            this.automatStart = true;

            // object - css properties that will be changed (key - value; ex: 'color' : '#00FFCC')
            this.properties = [];

            // boolean - flag used by the requestAnimationFrame callback to know which items have properties to apply
            this.readyToAnimate = false;

            // the index of the current frame
            this.nextFrame = 1;

            // number - the current interval id set for the animation
            this.interval = undefined;

            // the jquery obj whose background will be animated
            this.jqueryObj = undefined;

            // the css class selector of the jqueryObj
            this.animationSpriteClass = undefined;

            // string - default direction for parsing the sprite img
            this._currentDirection = 'right';

            // number - the executed loops
            this._executedLoops = 0;


            // string - css background position
            this._prop_background_position = undefined;


            // The followings will be set from the class selector

            // int - number of frames (it must be greater than 1 to allow animation)
            this.frames = undefined;

            // the width(px) of a frame
            this.frameWidth = undefined;

            // int - the interval time (ms) the animation runs
            this.velocity = undefined;

            // boolean - to the right and vice versa
            this.reverse = undefined;

            // number - number of loops to animate
            this.loops = undefined;


            // Function actually compute the params for animation, prepare the params for next animation and calls t
            // he requestAnimationFrame with a callback function to animate all items ready for animation
            this.animate = function() {

                this._prop_background_position = ( -1 * this.nextFrame * this.frameWidth ) + 'px 0';
                this.readyToAnimate = true;


                // The nextFrame value is computed for next frame
                if ( true === this.reverse) {

                    if ( 'right' === this._currentDirection ) {

                        if ( this.nextFrame === this.frames - 1 ) {
                            this._currentDirection = 'left';
                            this.nextFrame--;
                        } else {
                            this.nextFrame++;
                        }

                    } else if ( 'left' === this._currentDirection ) {
                        if ( 0 === this.nextFrame ) {

                            this._currentDirection = 'right';
                            this.nextFrame++;
                            this._executedLoops++;

                            if ( ( 0 !== this.loops ) && ( this._executedLoops === this.loops ) ) {
                                clearInterval( this.interval );
                            }
                        } else {
                            this.nextFrame--;
                        }
                    }

                } else {

                    if ( this.nextFrame === this.frames - 1 ) {

                        this._executedLoops++;

                        // complete tour ( once to the right ), so we stop
                        if ( ( 0 !== this.loops ) && ( this._executedLoops === this.loops ) ) {
                            clearInterval( this.interval );
                        }

                        this.nextFrame = 0;
                    } else {
                        this.nextFrame++;
                    }
                }


                //this.jqueryObj.css('background-position', horizontalPosition + 'px 0');

                // Any calls to requestAnimationFrame are stopped. Anyway, the settings of the current item are ready,
                // so the callback will consider it.
                if ( false === tdAnimationSprite.isInRequestAnimation ) {
                    tdAnimationSprite.isInRequestAnimation = true;
                    window.requestAnimationFrame( tdAnimationSprite.animateAllItems );
                }
            };
        },

        /**
         * The css class selector must be some like this 'td_animation_sprite-10-50-500-0-1-1'
         * It must start with 'td_animation_sprite'
         * Fields order:
         * - number of frames
         * - width of a frame
         * - velocity in ms
         * - loops (number) : reload the animation cycle at infinity or specify the number of loops
         * - reverse (0 or 1) : the loop include, or not, the reverse path
         * - automatstart (0 or 1) : the item animation starts, or not, automatically
         *
         * @param item
         * @private
         */
        _initializeItem: function( item ) {
            if ( ( true === item._isInitialized ) ) {
                return;
            }

            // get all strings containing 'td_animation_sprite'
            var regex = /(td_animation_sprite\S*)/gi;

            if ( 'undefined' !== typeof item.jqueryObj.attr( 'class' ) ) {

                // resultMatch is an array of matches, or null if there's no matching
                var resultMatch = item.jqueryObj.attr( 'class' ).match( regex );

                if (null !== resultMatch) {

                    item.offsetTop = item.jqueryObj.offset().top;
                    item.offsetBottomToTop = item.offsetTop + item.jqueryObj.height();

                    // the last matching is considered, because new css classes that matches, can be added before recomputing an item
                    item.animationSpriteClass = resultMatch[resultMatch.length - 1];

                    var sceneParams = item.animationSpriteClass.split('-');

                    if (7 === sceneParams.length) {

                        item.frames = parseInt(sceneParams[1]);
                        item.frameWidth = parseInt(sceneParams[2]);
                        item.velocity = parseInt(sceneParams[3]);
                        item.loops = parseInt(sceneParams[4]);

                        if (1 === parseInt(sceneParams[5])) {
                            item.reverse = true;
                        } else {
                            item.reverse = false;
                        }

                        if (1 === parseInt(sceneParams[6])) {
                            item.automatStart = true;
                        } else {
                            item.automatStart = false;
                        }

                        item._isInitialized = true;
                    }
                }
            }
        },



        addItem: function( item ) {

            if ( item.constructor === tdAnimationSprite.item ) {
                tdAnimationSprite.items.push( item );
                tdAnimationSprite._initializeItem( item );

                if ( true === item.automatStart ) {
                    tdAnimationSprite.computeItem( item );
                }
            }
        },


        /**
         * Deletes an item base on blockUid.
         * Make sure that you add blockUid to items that you expect to be deleted
         * @param blockUid
         */
        deleteItem: function(blockUid) {
            for (var cnt = 0; cnt < tdAnimationSprite.items.length; cnt++) {
                if (tdAnimationSprite.items[cnt].blockUid === blockUid) {
                    tdAnimationSprite.items.splice(cnt, 1); // remove the item from the "array"
                    return true;
                }
            }
            return false;
        },

        computeItem: function( item ) {

            // set interval just for frames greater than 1
            if ( item.frames > 1 ) {

                // Check the item interval to not be set
                if ( undefined !== item.interval ) {
                    return;
                }

                item.interval = setInterval(function(){

                    if ( false === item.paused ) {
                        item.animate();
                    }

                }, item.velocity );
            }
        },

        // At recomputing, an item will check again its last css class selector and it is reinitialized. So, if a new
        // css class selector is added, it will use it. This way the animation can be modified
        recomputeItem: function( item ) {

            // stop any animation
            clearInterval( item.interval );

            // reset the item interval
            item.interval = undefined;

            // reset the _isInitialized flag
            item._isInitialized = false;

            // reinitialize item
            tdAnimationSprite._initializeItem( item );

            // compute the item again
            tdAnimationSprite.computeItem( item );
        },

        // Clear the interval set for an item.
        stopItem: function( item ) {
            if ( ( item.constructor === tdAnimationSprite.item ) && ( true === item._isInitialized ) ) {
                clearInterval( item.interval );

                // reset the item interval
                item.interval = undefined;
            }
        },

        // Start animation of a paused item
        startItem: function( item ) {
            if ( ( item.constructor === tdAnimationSprite.item ) && ( true === item._isInitialized ) ) {
                item.paused = false;
            }
        },

        // Pause animation of an item
        pauseItem: function( item ) {
            if ( ( item.constructor === tdAnimationSprite.item ) && ( true === item._isInitialized ) ) {
                item.paused = true;
            }
        },




        computeAllItems: function() {
            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                tdAnimationSprite.computeItem( tdAnimationSprite.items[i] );
            }
        },

        recomputeAllItems: function() {
            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                tdAnimationSprite.recomputeItem( tdAnimationSprite.items[i] );
            }
        },

        stopAllItems: function() {
            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                tdAnimationSprite.stopItem( tdAnimationSprite.items[i] );
            }
        },

        pauseAllItems: function() {
            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                tdAnimationSprite.pauseItem( tdAnimationSprite.items[i] );
            }
        },

        startAllItems: function() {
            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                tdAnimationSprite.startItem( tdAnimationSprite.items[i] );
            }
        },


        // The requestAnimationFrame callback function.
        // The 'background-position' is set and then the 'readyToAnimate' flag is set to false
        animateAllItems: function() {
            var currentItem;

            for ( var i = 0; i < tdAnimationSprite.items.length; i++ ) {
                currentItem = tdAnimationSprite.items[i];
                if ( true === currentItem.readyToAnimate ) {
                    currentItem.jqueryObj.css( 'background-position', currentItem._prop_background_position );
                    currentItem.readyToAnimate = false;
                }
            }
            tdAnimationSprite.isInRequestAnimation = false;
        }
    };

    /*
     <div class="td_animation_sprite-a-b-c-d-e-f"></div>

    @note - we should have used the data- html attribute here!

     a - number of frames
     b - width(px) of a frame
     c - velocity
     d - loops number (0 for infinity)
     e - loop include reverse
     f - animation start automatically
     */

    var tdAnimationSpriteElements = jQuery( 'span[class^="td_animation_sprite"]' );

    for ( var i = 0; i < tdAnimationSpriteElements.length; i++ ) {
        var tdAnimationSpriteItem = new tdAnimationSprite.item();

        tdAnimationSpriteItem.jqueryObj = jQuery( tdAnimationSpriteElements[i] );
        tdAnimationSpriteItem.blockUid = tdAnimationSpriteItem.jqueryObj.data('td-block-uid');   // the block uid is used on the front end editor when we want to delete this item via it's blockuid
        tdAnimationSprite.addItem( tdAnimationSpriteItem );
    }
})();

function td_date_i18n(format, timestamp) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard
    // +   improved by: Tim Wiel
    // +   improved by: Bryan Elliott
    // +   improved by: David Randall
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Theriault
    // +  derived from: gettimeofday
    // +      input by: majak
    // +   bugfixed by: majak
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Alex
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Thomas Beaucourt (http://www.webapp.fr)
    // +   improved by: JT
    // +   improved by: Theriault
    // +   improved by: Rafał Kukawski (http://blog.kukawski.pl)
    // +   bugfixed by: omid (http://phpjs.org/functions/380:380#comment_137122)
    // +      input by: Martin
    // +      input by: Alex Wilson
    // +      input by: Haravikk
    // +   improved by: Theriault
    // +   bugfixed by: Chris (http://www.devotis.nl/)
    // +   improved by: Jari Pennanen (https://github.com/ciantic/) - date_i18n from WordPress
    // %        note 1: Uses global: php_js to store the default timezone
    // %        note 2: Although the function potentially allows timezone info (see notes), it currently does not set
    // %        note 2: per a timezone specified by date_default_timezone_set(). Implementers might use
    // %        note 2: this.php_js.currentTimezoneOffset and this.php_js.currentTimezoneDST set by that function
    // %        note 2: in order to adjust the dates in this function (or our other date functions!) accordingly
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000);
    // *     example 4: (x+'').length == 10 // 2009 01 09
    // *     returns 4: true
    // *     example 5: date('W', 1104534000);
    // *     returns 5: '53'
    // *     example 6: date('B t', 1104534000);
    // *     returns 6: '999 31'
    // *     example 7: date('W U', 1293750000.82); // 2010-12-31
    // *     returns 7: '52 1293750000'
    // *     example 8: date('W', 1293836400); // 2011-01-01
    // *     returns 8: '52'
    // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
    // *     returns 9: '52 2011-01-02'
    var that = this,
        jsdate,
        f,
    // Keep this here (works, but for code commented-out
    // below for file size reasons)
    //, tal= [],
    // trailing backslash -> (dropped)
    // a backslash followed by any character (including backslash) -> the character
    // empty string -> empty string
        formatChr = /\\?(.?)/gi,
        formatChrCb = function (t, s) {
            return f[t] ? f[t]() : s;
        },
        _pad = function (n, c) {
            n = String(n);
            while (n.length < c) {
                n = '0' + n;
            }
            return n;
        };
    f = {
        // Day
        d: function () { // Day of month w/leading 0; 01..31
            return _pad(f.j(), 2);
        },
        D: function () { // Shorthand day name; Mon...Sun
            return tdDateNamesI18n.day_names_short[f.w()];
        },
        j: function () { // Day of month; 1..31
            return jsdate.getDate();
        },
        l: function () { // Full day name; Monday...Sunday
            return tdDateNamesI18n.day_names[f.w()];
        },
        N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
            return f.w() || 7;
        },
        S: function(){ // Ordinal suffix for day of month; st, nd, rd, th
            var j = f.j(),
                i = j%10;
            if (i <= 3 && parseInt((j%100)/10, 10) == 1) {
                i = 0;
            }
            return ['st', 'nd', 'rd'][i - 1] || 'th';
        },
        w: function () { // Day of week; 0[Sun]..6[Sat]
            return jsdate.getDay();
        },
        z: function () { // Day of year; 0..365
            var a = new Date(f.Y(), f.n() - 1, f.j()),
                b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 864e5);
        },

        // Week
        W: function () { // ISO-8601 week number
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                b = new Date(a.getFullYear(), 0, 4);
            return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
        },

        // Month
        F: function () { // Full month name; January...December
            return tdDateNamesI18n.month_names[f.n() - 1];
        },
        m: function () { // Month w/leading 0; 01...12
            return _pad(f.n(), 2);
        },
        M: function () { // Shorthand month name; Jan...Dec
            return tdDateNamesI18n.month_names_short[f.n() - 1];
        },
        n: function () { // Month; 1...12
            return jsdate.getMonth() + 1;
        },
        t: function () { // Days in month; 28...31
            return (new Date(f.Y(), f.n(), 0)).getDate();
        },

        // Year
        L: function () { // Is leap year?; 0 or 1
            var j = f.Y();
            return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
        },
        o: function () { // ISO-8601 year
            var n = f.n(),
                W = f.W(),
                Y = f.Y();
            return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
        },
        Y: function () { // Full year; e.g. 1980...2010
            return jsdate.getFullYear();
        },
        y: function () { // Last two digits of year; 00...99
            return f.Y().toString().slice(-2);
        },

        // Time
        a: function () { // am or pm
            return jsdate.getHours() > 11 ? "pm" : "am";
        },
        A: function () { // AM or PM
            return f.a().toUpperCase();
        },
        B: function () { // Swatch Internet time; 000..999
            var H = jsdate.getUTCHours() * 36e2,
            // Hours
                i = jsdate.getUTCMinutes() * 60,
            // Minutes
                s = jsdate.getUTCSeconds(); // Seconds
            return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
        },
        g: function () { // 12-Hours; 1..12
            return f.G() % 12 || 12;
        },
        G: function () { // 24-Hours; 0..23
            return jsdate.getHours();
        },
        h: function () { // 12-Hours w/leading 0; 01..12
            return _pad(f.g(), 2);
        },
        H: function () { // 24-Hours w/leading 0; 00..23
            return _pad(f.G(), 2);
        },
        i: function () { // Minutes w/leading 0; 00..59
            return _pad(jsdate.getMinutes(), 2);
        },
        s: function () { // Seconds w/leading 0; 00..59
            return _pad(jsdate.getSeconds(), 2);
        },
        u: function () { // Microseconds; 000000-999000
            return _pad(jsdate.getMilliseconds() * 1000, 6);
        },

        // Timezone
        e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
            // The following works, but requires inclusion of the very large
            // timezone_abbreviations_list() function.
            /*              return that.date_default_timezone_get();
             */
            //throw 'Not supported (see source code of date() for timezone on how to add support)';
            console.log('Not supported (see source code of date() for timezone on how to add support)');
        },
        I: function () { // DST observed?; 0 or 1
            // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
            // If they are not equal, then DST is observed.
            var a = new Date(f.Y(), 0),
            // Jan 1
                c = Date.UTC(f.Y(), 0),
            // Jan 1 UTC
                b = new Date(f.Y(), 6),
            // Jul 1
                d = Date.UTC(f.Y(), 6); // Jul 1 UTC
            return ((a - c) !== (b - d)) ? 1 : 0;
        },
        O: function () { // Difference to GMT in hour format; e.g. +0200
            var tzo = jsdate.getTimezoneOffset(),
                a = Math.abs(tzo);
            return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
        },
        P: function () { // Difference to GMT w/colon; e.g. +02:00
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2));
        },
        T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
            // The following works, but requires inclusion of the very
            // large timezone_abbreviations_list() function.
            /*              var abbr = '', i = 0, os = 0, default = 0;
             if (!tal.length) {
             tal = that.timezone_abbreviations_list();
             }
             if (that.php_js && that.php_js.default_timezone) {
             default = that.php_js.default_timezone;
             for (abbr in tal) {
             for (i=0; i < tal[abbr].length; i++) {
             if (tal[abbr][i].timezone_id === default) {
             return abbr.toUpperCase();
             }
             }
             }
             }
             for (abbr in tal) {
             for (i = 0; i < tal[abbr].length; i++) {
             os = -jsdate.getTimezoneOffset() * 60;
             if (tal[abbr][i].offset === os) {
             return abbr.toUpperCase();
             }
             }
             }
             */
            return 'UTC';
        },
        Z: function () { // Timezone offset in seconds (-43200...50400)
            return -jsdate.getTimezoneOffset() * 60;
        },

        // Full Date/Time
        c: function () { // ISO-8601 date.
            return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
        },
        r: function () { // RFC 2822
            return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
        },
        U: function () { // Seconds since UNIX epoch
            return jsdate / 1000 | 0;
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = (timestamp === undefined ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
                new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
        );
        return format.replace(formatChr, formatChrCb);
    };
    return this.date(format, timestamp);
}

/* global tdDetect: {} */
/* global jQuery: {} */
/* global tdPullDown: {} */

var tdSocialSharing = {};

(function(){
    "use strict";

    tdSocialSharing = {

        init: function() {

            // hook up the social buttons to the popup window
            jQuery('.td-social-sharing-button').on( 'click', function(event){

                var $theLinkEl = jQuery(this);
                var blockUid = '';

                // for email just open the url like normal
                if ($theLinkEl.hasClass('td-social-mail') || $theLinkEl.hasClass('td-social-share-text')) {
                    return;
                }

                event.preventDefault();
                if ($theLinkEl.hasClass('td-social-expand-tabs')) {

                    blockUid = $theLinkEl.data('block-uid');
                    var $blockWrapEl = jQuery('#' + blockUid);
                    var $iconEl = $theLinkEl.find('.td-social-expand-tabs-icon');

                    if ($blockWrapEl.hasClass('td-social-show-all')) { // hide icons

                        // move the plus sign back
                        $theLinkEl.detach().appendTo($blockWrapEl.find('.td-social-sharing-hidden:first'));

                        // hide icons and init a new pulldown
                        var jquery_object_container = $blockWrapEl;
                        var horizontal_jquery_obj = jquery_object_container.find('.td-post-sharing-visible:first');
                        var pulldown_item_obj = new tdPullDown.item();
                        pulldown_item_obj.blockUid = jquery_object_container.attr('id');
                        pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
                        pulldown_item_obj.vertical_jquery_obj = jquery_object_container.find('.td-social-sharing-hidden:first');
                        pulldown_item_obj.horizontal_element_css_class = 'td-social-sharing-button-js';
                        pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.parents('.td-post-sharing:first');
                        tdPullDown.add_item(pulldown_item_obj);


                        jQuery('#' + blockUid).removeClass('td-social-show-all');
                        $iconEl.removeClass('td-icon-minus');
                        $iconEl.addClass('td-icon-plus');

                    } else {
                        // show ALL icons
                        tdPullDown.unloadItem(blockUid);
                        jQuery('#' + blockUid).addClass('td-social-show-all');
                        $iconEl.removeClass('td-icon-plus');
                        $iconEl.addClass('td-icon-minus');

                        // move the minus button in the vertical list
                        $theLinkEl.detach().appendTo($blockWrapEl.find('.td-post-sharing-visible:first'));
                    }

                    return;
                }




                // if ($theLinkEl.hasClass('td-social-twitter')) {
                //     return;
                // }

                if ($theLinkEl.hasClass('td-social-print')) {
                    window.print();
                    return;
                }

                event.preventDefault();
                var left  = (jQuery(window).width()/2)-(900/2);
                var top   = (jQuery(window).height()/2)-(600/2);
                window.open($theLinkEl.attr('href'), 'mywin','left=' + left + ',top=' + top + ',width=900,height=600,toolbar=0');
            });

            // on firefox fix small issue, the icons where all hidden
            setTimeout(function(){
                // on social images, init a new pull down
                jQuery('.td-post-sharing').each(function (index, element) {

                    var jquery_object_container = jQuery(element);
                    var horizontal_jquery_obj = jquery_object_container.find('.td-post-sharing-visible:first');
                    var pulldown_item_obj = new tdPullDown.item();

                    pulldown_item_obj.blockUid = jquery_object_container.attr('id');
                    pulldown_item_obj.horizontal_jquery_obj = horizontal_jquery_obj;
                    pulldown_item_obj.vertical_jquery_obj = jquery_object_container.find('.td-social-sharing-hidden:first');
                    //console.log(pulldown_item_obj.vertical_jquery_obj);
                    pulldown_item_obj.horizontal_element_css_class = 'td-social-sharing-button-js';
                    pulldown_item_obj.container_jquery_obj = horizontal_jquery_obj.parents('.td-post-sharing:first');
                    tdPullDown.add_item(pulldown_item_obj);

                    //console.log(pulldown_item_obj.container_jquery_obj);

                    //console.log(pulldown_item_obj);
                });
            },50);

        }

    };



    tdSocialSharing.init();

})();
;'use strict';

/* ----------------------------------------------------------------------------
 tdPostImages.js
 --------------------------------------------------------------------------- */

/* global jQuery:{} */
/* global tdUtil:{} */
/* global tdAffix:{} */
/* global tdIsScrollingAnimation:boolean */

/*  ----------------------------------------------------------------------------
 On load
 */
jQuery().ready(function() {

    // handles modal images for: Featured images, inline image, inline image with caption, galleries
    tdModalImage();
});



// handles modal images for: Featured images, inline image, inline image with caption, galleries
function tdModalImage() {

    //fix wordpress figure + figcaption (we move the figcaption in the data-caption attribute of the link)
    jQuery( 'figure.wp-caption' ).each(function() {
        var caption_text = jQuery( this ).children( 'figcaption' ).html();
        jQuery( this ).children( 'a' ).data( 'caption', caption_text );
    });

    //move td-modal-image class to the parent a from the image. We can only add this class to the image via word press media editor
    jQuery( '.td-modal-image' ).each(function() {
        var $this = jQuery( this ),
            $parent = $this.parent();

        $parent.addClass( 'td-modal-image' );
        $this.removeClass( 'td-modal-image' );
    });



    //popup on modal images in articles
    jQuery( 'article' ).magnificPopup({
        type: 'image',
        delegate: ".td-modal-image",
        gallery: {
            enabled: true,
            tPrev: tdUtil.getBackendVar( 'td_magnific_popup_translation_tPrev' ), // Alt text on left arrow
            tNext: tdUtil.getBackendVar( 'td_magnific_popup_translation_tNext' ), // Alt text on right arrow
            tCounter: tdUtil.getBackendVar( 'td_magnific_popup_translation_tCounter' ) // Markup for "1 of 7" counter
        },
        ajax: {
            tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_ajax_tError' )
        },
        image: {
            tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_image_tError' ),
            titleSrc: function( item ) {//console.log(item.el);
                //alert(jQuery(item.el).data("caption"));
                var td_current_caption = jQuery( item.el ).data( 'caption' );
                if ( 'undefined' !== typeof td_current_caption ) {
                    return td_current_caption;
                } else {
                    return '';
                }
            }
        },
        zoom: {
            enabled: true,
            duration: 300,
            opener: function( element ) {
                return element.find( 'img' );
            }
        },
        callbacks: {
            change: function( item ) {
                window.tdModalImageLastEl = item.el;
                //setTimeout(function(){
                tdUtil.scrollIntoView( item.el );
                //}, 100);
            },
            beforeClose: function() {
                tdAffix.allow_scroll = false;

                tdUtil.scrollIntoView( window.tdModalImageLastEl );

                var interval_td_affix_scroll = setInterval(function() {

                    if ( ! tdIsScrollingAnimation ) {
                        clearInterval( interval_td_affix_scroll );
                        setTimeout(function() {
                            tdAffix.allow_scroll = true;
                            //tdAffix.td_events_scroll(td_events.scroll_window_scrollTop);
                        }, 100 );
                    }
                }, 100 );
            }
        }
    });

    //popup on modal images in .td-main-content-wrap
    jQuery( '.td-main-content-wrap' ).magnificPopup({
        type: 'image',
        delegate: ".td-modal-image",
        gallery: {
            enabled: true,
            tPrev: tdUtil.getBackendVar( 'td_magnific_popup_translation_tPrev' ), // Alt text on left arrow
            tNext: tdUtil.getBackendVar( 'td_magnific_popup_translation_tNext' ), // Alt text on right arrow
            tCounter: tdUtil.getBackendVar( 'td_magnific_popup_translation_tCounter' ) // Markup for "1 of 7" counter
        },
        ajax: {
            tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_ajax_tError' )
        },
        image: {
            tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_image_tError' ),
            titleSrc: function( item ) {//console.log(item.el);
                //alert(jQuery(item.el).data("caption"));
                var td_current_caption = jQuery( item.el ).data( 'caption' );
                if ( 'undefined' !== typeof td_current_caption ) {
                    return td_current_caption;
                } else {
                    return '';
                }
            }
        },
        zoom: {
            enabled: true,
            duration: 300,
            opener: function( element ) {
                return element.find( 'img' );
            }
        },
        callbacks: {
            change: function( item ) {
                window.tdModalImageLastEl = item.el;
                //setTimeout(function(){
                tdUtil.scrollIntoView( item.el );
                //}, 100);
            },
            beforeClose: function() {
                tdAffix.allow_scroll = false;

                tdUtil.scrollIntoView( window.tdModalImageLastEl );

                var interval_td_affix_scroll = setInterval(function() {

                    if ( ! tdIsScrollingAnimation ) {
                        clearInterval( interval_td_affix_scroll );
                        setTimeout(function() {
                            tdAffix.allow_scroll = true;
                            //tdAffix.td_events_scroll(td_events.scroll_window_scrollTop);
                        }, 100 );
                    }
                }, 100 );
            }
        }
    });





    //gallery popup
    //detect jetpack carousel and disable the theme popup
    if ( 'undefined' === typeof jetpackCarouselStrings ) {

        // copy gallery caption from figcaption to data-caption attribute of the link to the full image, in this way the modal can read the caption
        jQuery( 'figure.gallery-item' ).each(function() {
            var caption_text = jQuery( this ).children( 'figcaption' ).html();
            jQuery( this ).find( 'a' ).data( 'caption', caption_text );
        });


        //jquery tiled gallery
        jQuery( '.tiled-gallery' ).magnificPopup({
            type: 'image',
            delegate: "a",
            gallery: {
                enabled: true,
                tPrev: tdUtil.getBackendVar( 'td_magnific_popup_translation_tPrev' ), // Alt text on left arrow
                tNext: tdUtil.getBackendVar( 'td_magnific_popup_translation_tNext' ), // Alt text on right arrow
                tCounter: tdUtil.getBackendVar( 'td_magnific_popup_translation_tCounter' ) // Markup for "1 of 7" counter
            },
            ajax: {
                tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_ajax_tError' )
            },
            image: {
                tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_image_tError' ),
                titleSrc: function( item ) {//console.log(item.el);
                    var td_current_caption = jQuery( item.el ).parent().find( '.tiled-gallery-caption' ).text();
                    if ( 'undefined' !== typeof td_current_caption ) {
                        return td_current_caption;
                    } else {
                        return '';
                    }
                }
            },
            zoom: {
                enabled: true,
                duration: 300,
                opener: function( element ) {
                    return element.find( 'img' );
                }
            },
            callbacks: {
                change: function( item ) {
                    window.tdModalImageLastEl = item.el;
                    tdUtil.scrollIntoView( item.el );
                },
                beforeClose: function() {
                    tdUtil.scrollIntoView( window.tdModalImageLastEl );
                }
            }
        });



        jQuery( '.gallery' ).magnificPopup({
            type: 'image',
            delegate: '.gallery-icon > a',
            gallery: {
                enabled: true,
                tPrev: tdUtil.getBackendVar( 'td_magnific_popup_translation_tPrev' ), // Alt text on left arrow
                tNext: tdUtil.getBackendVar( 'td_magnific_popup_translation_tNext' ), // Alt text on right arrow
                tCounter: tdUtil.getBackendVar( 'td_magnific_popup_translation_tCounter' ) // Markup for "1 of 7" counter
            },
            ajax: {
                tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_ajax_tError' )
            },
            image: {
                tError: tdUtil.getBackendVar( 'td_magnific_popup_translation_image_tError' ),
                titleSrc: function( item ) {//console.log(item.el);
                    var td_current_caption = jQuery( item.el ).data( 'caption' );
                    if ( 'undefined' !== typeof td_current_caption ) {
                        return td_current_caption;
                    } else {
                        return '';
                    }
                }
            },
            zoom: {
                enabled: true,
                duration: 300,
                opener: function( element ) {
                    return element.find( 'img' );
                }
            },
            callbacks: {
                change: function( item ) {
                    window.tdModalImageLastEl = item.el;
                    tdUtil.scrollIntoView( item.el );
                },
                beforeClose: function() {
                    tdUtil.scrollIntoView( window.tdModalImageLastEl );
                }
            }
        });
    }
} //end modal

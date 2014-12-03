/*!
 * jQuery lightweight plugin boilerplate
 * Licensed under the MIT license
 */
;(function ( $, window, document, undefined ) {
    'use strict';
    
    var pluginId = "jSprite",
        defaults = {
            sprite: null,
            speed: 2000,
            x: 1,
            y: 1,
            opacity: 1,
            start: null,
            complete: null
        };

    /**
     * Constructor
     * @param {type} element
     * @param {Object} options
     * @returns {jsprite_L7.Plugin}
     */
    function Plugin( element, options ) {
        if (typeof options.sprite === "undefined") {
            _err('No sprite specified. Please use $.jSprite({"sprite":"./foo.png"})');
            return;
        }
        
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginId;
        this._isLoaded = false;

        this.init();
    }
    
    /**
     * Message log
     * @param {mixed} message
     * @returns {undefined}
     */
    var _log = function(message) {
        if (window.console) {
            window.console.log(message);
        }
    };
    
    /**
     * Error log
     * @param {mixed} message
     * @returns void
     */
    var _err = function(message) {
        if (window.console) {
            window.console.error(message);
        }
    };
    
    Plugin.prototype = {
        /**
         * Initialize function
         */
        init: function() {
            var spr = this._new();
            return spr;
            
        },
        /**
         * Create object
         */
        _new: function() {
            var _this = this;
            var sprite = document.createElement("img");
            sprite.src = this.options.sprite;
            
            sprite.setAttribute("class", "jsprite");
            
            $(sprite).css({
                'left':     this.options.x,
                'top':      this.options.y,
                'position': 'absolute',
                'opacity':  this.options.opacity
            });

            this.element.append(sprite);
            this._sprite = sprite;
            
            $(sprite).load(function(e) {
                _this._isLoaded = true;
                _this.size = {
                    width:$(this).width(),
                    height:$(this).height()
                };
                
                if (_this.options.start !== null) {
                    _this.options.start(_this);
                }
            });  
        },
        
        /**
         * @param {Object} params Options (eq {x:200, y:300, speed:300})
         */
        moveTo: function(params) {
            var _this = this;
            this.options.speed = params.speed || this.options.speed;
            var duration, _distx, _disty = 0;
            var config = {};
            var css = {};
            if (typeof params.x !== "undefined") {
                css.left = params.x;
                _distx = Math.abs($(this._sprite).position().left - parseInt(params.x));
            }
            if (typeof params.y !== "undefined") {
                css.top = params.y;
                _disty = Math.abs($(this._sprite).position().top - params.y);
            }

            duration = parseInt((Math.max(_distx, _disty) * 60000) / this.options.speed);
            config.duration = duration;
            config.easing = "linear";
            
            config.complete = function() {
                if (_this.options.complete !== null) {
                    _this.options.complete(_this);
                }
            };
            
            config.start = function() {
                /**
                 * Calcul the real duration here
                 * based on position
                 */  
            };
            
            $(this._sprite).animate(css, config);
        }
    };

    /**
     * @param {Object} options
     * @see jSprite.defaults for documentation
     */
    $.fn[pluginId] = function ( options ) {
        return new Plugin( this, options );
    };
})( jQuery, window, document );
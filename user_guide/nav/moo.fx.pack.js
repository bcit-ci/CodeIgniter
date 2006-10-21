/*
moo.fx pack, effects extensions for moo.fx.
by Valerio Proietti (http://mad4milk.net) MIT-style LICENSE
for more info visit (http://moofx.mad4milk.net).
Wednesday, November 16, 2005
v1.0.4
*/

//text size modify, now works with pixels too.
fx.Text = Class.create();
fx.Text.prototype = Object.extend(new fx.Base(), {
	initialize: function(el, options) {
		this.el = $(el);
		this.setOptions(options);
		if (!this.options.unit) this.options.unit = "em";
	},

	increase: function() {
		this.el.style.fontSize = this.now + this.options.unit;
	}
});

//composition effect, calls Width and Height alltogheter
fx.Resize = Class.create();
fx.Resize.prototype = {
	initialize: function(el, options) {
		this.h = new fx.Height(el, options);
		if (options) options.onComplete = null;
		this.w = new fx.Width(el, options);
		this.el = $(el);
	},

	toggle: function(){
		this.h.toggle();
		this.w.toggle();
	},

	modify: function(hto, wto) {
		this.h.custom(this.el.offsetHeight, this.el.offsetHeight + hto);
		this.w.custom(this.el.offsetWidth, this.el.offsetWidth + wto);
	},

	custom: function(hto, wto) {
		this.h.custom(this.el.offsetHeight, hto);
		this.w.custom(this.el.offsetWidth, wto);
	},

	hide: function(){
		this.h.hide();
		this.w.hide();
	}
}

//composition effect, calls Opacity and (Width and/or Height) alltogheter
fx.FadeSize = Class.create();
fx.FadeSize.prototype = {
	initialize: function(el, options) {
		this.el = $(el);
		this.el.o = new fx.Opacity(el, options);
		if (options) options.onComplete = null;
		this.el.h = new fx.Height(el, options);
		this.el.w = new fx.Width(el, options);
	},

	toggle: function() {
		this.el.o.toggle();
		for (var i = 0; i < arguments.length; i++) {
			if (arguments[i] == 'height') this.el.h.toggle();
			if (arguments[i] == 'width') this.el.w.toggle();
		}
	},

	hide: function(){
		this.el.o.hide();
		for (var i = 0; i < arguments.length; i++) {
			if (arguments[i] == 'height') this.el.h.hide();
			if (arguments[i] == 'width') this.el.w.hide();
		}
	}
}

//intended to work with arrays.
var Multi = new Object();
Multi = function(){};
Multi.prototype = {
	initialize: function(elements, options){
		this.options = options;
		this.el = this.getElementsFromArray(elements);
		for (i=0;i<this.el.length;i++){
			this.effect(this.el[i]);
		}
	},

	getElementsFromArray: function(array) {
		var elements = new Array();
		for (i=0;i<array.length;i++) {
			elements.push($(array[i]));
		}
		return elements;
	}
}

//Fadesize with arrays
fx.MultiFadeSize = Class.create();
fx.MultiFadeSize.prototype = Object.extend(new Multi(), {
	effect: function(el){
		el.fs = new fx.FadeSize(el, this.options);
	},

	showThisHideOpen: function(el, delay, mode){
		for (i=0;i<this.el.length;i++){
			if (this.el[i].offsetHeight > 0 && this.el[i] != el && this.el[i].h.timer == null && el.h.timer == null){
				this.el[i].fs.toggle(mode);
				setTimeout(function(){el.fs.toggle(mode);}.bind(el), delay);
			}
			
		}
	},

	hide: function(el, mode){
		el.fs.hide(mode);
	}
});

var Remember = new Object();
Remember = function(){};
Remember.prototype = {
	initialize: function(el, options){
		this.el = $(el);
		this.days = 365;
		this.options = options;
		this.effect();
		var cookie = this.readCookie();
		if (cookie) {
			this.fx.now = cookie;
			this.fx.increase();
		}
	},

	//cookie functions based on code by Peter-Paul Koch
	setCookie: function(value) {
		var date = new Date();
		date.setTime(date.getTime()+(this.days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
		document.cookie = this.el+this.el.id+this.prefix+"="+value+expires+"; path=/";
	},

	readCookie: function() {
		var nameEQ = this.el+this.el.id+this.prefix + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return false;
	},

	custom: function(from, to){
		if (this.fx.now != to) {
			this.setCookie(to);
			this.fx.custom(from, to);
		}
	}
}

fx.RememberHeight = Class.create();
fx.RememberHeight.prototype = Object.extend(new Remember(), {
	effect: function(){
		this.fx = new fx.Height(this.el, this.options);
		this.prefix = 'height';
	},
	
	toggle: function(){
		if (this.el.offsetHeight == 0) this.setCookie(this.el.scrollHeight);
		else this.setCookie(0);
		this.fx.toggle();
	},
	
	resize: function(to){
		this.setCookie(this.el.offsetHeight+to);
		this.fx.custom(this.el.offsetHeight,this.el.offsetHeight+to);
	},

	hide: function(){
		if (!this.readCookie()) {
			this.fx.hide();
		}
	}
});

fx.RememberText = Class.create();
fx.RememberText.prototype = Object.extend(new Remember(), {
	effect: function(){
		this.fx = new fx.Text(this.el, this.options);
		this.prefix = 'text';
	}
});


//use to attach effects without using js code, just classnames and rel attributes.
ParseClassNames = Class.create();
ParseClassNames.prototype = {
	initialize: function(options){
		var babies = document.getElementsByTagName('*') || document.all;
		for (var i = 0; i < babies.length; i++) {
			var el = babies[i];
			//attach the effect, from the classNames;
			var effects = this.getEffects(el);
			for (var j = 0; j < effects.length; j++) {
				if (j == 1 && options) options.onComplete = null;
				el[effects[j]+"fx"] = new fx[effects[j]](el, options);
			}
			//execute methods, from rel
			if (el.rel) {
				el.crel = el.rel.split(' ');
				if (el.crel[0].indexOf("fx_") > -1) {
					var event = el.crel[0].replace('fx_', '');
					var tocompute = this.getEffects($(el.crel[1]));
					el["on"+event] = function(){
						for (var f = 0; f < tocompute.length; f++) {
							$(this.crel[1])[tocompute[f]+"fx"][this.crel[2] || "toggle"](this.crel[3] || null, this.crel[4] || null);
						}
					}
				}
			}
		}
	},

	getEffects: function(el){
		var effects = new Array();
		var css = el.className.split(' ');
		for (var i = 0; i < css.length; i++) {
			if (css[i].indexOf('fx_') > -1) {
				var effect = css[i].replace('fx_', '');
				effects.push(effect);
			}
		}
		return effects;
	}
}
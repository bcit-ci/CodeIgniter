/*  Prototype JavaScript framework
 *  (c) 2005 Sam Stephenson <sam@conio.net>
 *
 *  Prototype is freely distributable under the terms of an MIT-style license.
 *
 *  For details, see the Prototype web site: http://prototype.conio.net/
 *
/*--------------------------------------------------------------------------*/


//note: this is a stripped down version of prototype, to be used with moo.fx by mad4milk (http://moofx.mad4milk.net).

var Class = {
  create: function() {
	return function() {
	  this.initialize.apply(this, arguments);
	}
  }
}

Object.extend = function(destination, source) {
  for (property in source) {
	destination[property] = source[property];
  }
  return destination;
}

Function.prototype.bind = function(object) {
  var __method = this;
  return function() {
	return __method.apply(object, arguments);
  }
}

function $() {
  var elements = new Array();

  for (var i = 0; i < arguments.length; i++) {
	var element = arguments[i];
	if (typeof element == 'string')
	  element = document.getElementById(element);

	if (arguments.length == 1)
	  return element;

	elements.push(element);
  }

  return elements;
}

//-------------------------

document.getElementsByClassName = function(className) {
  var children = document.getElementsByTagName('*') || document.all;
  var elements = new Array();

  for (var i = 0; i < children.length; i++) {
	var child = children[i];
	var classNames = child.className.split(' ');
	for (var j = 0; j < classNames.length; j++) {
	  if (classNames[j] == className) {
		elements.push(child);
		break;
	  }
	}
  }

  return elements;
}

//-------------------------

if (!window.Element) {
  var Element = new Object();
}

Object.extend(Element, {
  remove: function(element) {
	element = $(element);
	element.parentNode.removeChild(element);
  },

  hasClassName: function(element, className) {
	element = $(element);
	if (!element)
	  return;
	var a = element.className.split(' ');
	for (var i = 0; i < a.length; i++) {
	  if (a[i] == className)
		return true;
	}
	return false;
  },

  addClassName: function(element, className) {
	element = $(element);
	Element.removeClassName(element, className);
	element.className += ' ' + className;
  },

  removeClassName: function(element, className) {
	element = $(element);
	if (!element)
	  return;
	var newClassName = '';
	var a = element.className.split(' ');
	for (var i = 0; i < a.length; i++) {
	  if (a[i] != className) {
		if (i > 0)
		  newClassName += ' ';
		newClassName += a[i];
	  }
	}
	element.className = newClassName;
  },

  // removes whitespace-only text node children
  cleanWhitespace: function(element) {
	element = $(element);
	for (var i = 0; i < element.childNodes.length; i++) {
	  var node = element.childNodes[i];
	  if (node.nodeType == 3 && !/\S/.test(node.nodeValue))
		Element.remove(node);
	}
  }
});
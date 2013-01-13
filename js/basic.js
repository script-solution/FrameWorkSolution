/**
 * Contains the basic javascript-functions
 * 
 * @package			FrameWorkSolution
 * @subpackage	js
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Detect and store the browser-type
 * Borrowed from prototype :-)
 */
var Browser = {
	isIE : !!(window.attachEvent && !window.opera),
	isOpera : !!window.opera,
	isWebKit : navigator.userAgent.indexOf('AppleWebKit/') > -1,
	isGecko : navigator.userAgent.indexOf('Gecko') > -1 && navigator.userAgent.indexOf('KHTML') == -1
};

/**
 * Builds a string with all ids to delete. The function assumes that
 * you have checkboxes for the entries that indicate which entries should
 * be deleted. Every checkbox has the id <checkboxPrefix><number> where
 * <number> starts with <start>. Additionally the function assumes
 * that the value of the checkbox is the id of the entry.
 * As soon as a checkbox with the mentioned id does not exist the function
 * breaks. You will get a string will all found ids separated by ",".
 *
 * @param string checkboxPrefix the prefix for all checkbox-ids
 * @param int start with which index for the checkbox-ids do you want to start? (default=0)
 * @return string a string with all selected ids
 */
function FWS_getDeleteIds(checkboxPrefix,start)
{
	if(typeof start == 'undefined')
		start = 0;

	var ids = '';
	for(var i = start;;i++)
	{
		var checkbox = document.getElementById(checkboxPrefix + i);
		if(checkbox == null)
			break;
		
		if(checkbox.checked)
			ids += checkbox.value + ',';
	}
	
	return ids;
}

/**
 * Sets the cookie <name> to <value>.
 *
 * @param string name the name of the cookie
 * @param mixed value the value of the cookie
 * @param int lifetime the lifetime (optional)
 * @param string path the path of the cookie (default="/")
 * @param string domain the domain of the cookie (default="")
 */
function FWS_setCookie(name,value,lifetime,path,domain)
{
	var str = escape(name) + "=" + escape(value);
	
	// add lifetime
	if(typeof lifetime != 'undefined')
		str += ";expires=" + (new Date((new Date()).getTime() + (1000 * lifetime))).toGMTString();

	// add path and domain
	if(typeof path == 'undefined')
		path = '/';
	if(typeof domain == 'undefined')
		domain = '';
	str += ";path=" + path + ";domain=" + domain;
	
	// set cookie
	document.cookie = str;
}

/**
 * opens a popup with default-options
 *
 * @param string filename the name of the file to display in the popup
 * @param string title the title of the popup
 * @param int width the width of the popup
 * @param int height the height of the popup
 */
function FWS_openDefaultPopup(filename,title,width,height)
{
	var options = 'toolbar=no,status=no,menubar=no,width=' + width + ',height=' + height;
	options += ',resizable=yes,scrollbars=yes,left=0,top=0,screenX=0,screenY=0';
	window.open(filename,title,options);
}

/**
 * opens a popup with the given parameters
 *
 * @param string filename the name of the file to display in the popup
 * @param string title the title of the popup
 * @param string options the options of die popup
 */
function FWS_openPopup(filename,title,options)
{
	window.open(filename,title,options);
}

/**
 * Prints all properties of the given object on the screen
 *
 * @param mixed object the object
 * @param boolean showFunctions do you want to show functions? (default=false)
 */
function FWS_printObject(object,showFunctions)
{
	if(typeof showFunctions == 'undefined')
		showFunctions = false;

	var props = '<pre style="max-height: 500px; overflow: auto; border-top: 1px solid #555;';
	props += ' border-bottom: 1px solid #555;">';
	props += FWS_getObjectProperties(object,showFunctions);
	props += '</pre>';

	var el;
	if(!FWS_getElement('FWS_debug_field'))
	{
		var str = '';
		str += '<h3 align="center">JS-Debugging of ' + object + ' [' + (typeof object) + ']</h3>';
		str += props;
		str += '<center><a href="javascript:FWS_hideElement(\'FWS_debug_field\');">Close</a></center>';
		
		el = document.createElement('div');
		el.id = 'FWS_debug_field';
		el.innerHTML = str;
		el.style.backgroundColor = '#ebebeb';
		el.style.position = 'absolute';
		el.style.zindex = 100;
		el.style.padding = '4px';
		el.style.margin = '0px';
		el.style.border = '1px dotted #000';
		document.getElementsByTagName('body')[0].appendChild(el);
	}
	else
	{
		el = FWS_getElement('FWS_debug_field');
		el.innerHTML += props;
	}
	
	el.style.width = (window.innerWidth - 20) + 'px';
	el.style.left = (window.innerWidth / 2 - parseInt(el.style.width) / 2 - 4) + 'px;';
	var top = (window.innerHeight / 2 - el.offsetHeight / 2);
	if(top < 0)
		top = 0;
	el.style.top = top + 'px';
	el.style.display = 'block';
}

/**
 * Returns a string with all properties of the given object
 *
 * @param mixed object the object
 * @param boolean showFunctions do you want to show functions? (default=false)
 * @return string the properties
 */
function FWS_getObjectProperties(object,showFunctions)
{
	if(typeof object == 'object' || typeof object == 'array')
	{
		var str = object + " {\n";
		for(var x in object)
		{
			try
			{
				if(typeof object[x] != 'undefined' && (showFunctions || typeof object[x] != 'function'))
				{
					str += '  ' + x + ' => ';
					str += FWS_escapeHTML(object[x]);
					str += "\n";
				}
			}
			catch(Exception)
			{
			
			}
		}
		str += '}' + "\n";
	}
	else
		str = FWS_escapeHTML(object);
	
	return str;
}

/**
 * Escapes the chars "<" and ">" to force the browser to display the code
 *
 * @param string input the input-string
 * @return string the output-string
 */
function FWS_escapeHTML(input)
{
	if(typeof input == 'string')
	{
		if(input.length > 100)
			input = input.substring(0,100);
		
		while(input.match(/</))
			input = input.replace(/</,"&lt;");
		while(input.match(/>/))
			input = input.replace(/>/,"&gt;");
	}
	return input;
}

/**
 * Displays the element with id=elId relative to the element with id=relId.
 * The location may be one of the following:
 * 	topleft,topleftright,top,toprightleft,topright,
 *	lefttop,righttop,
 * 	center,
 *	left,right,
 *	leftbottom,rightbottom,
 *	bottomleft,bottomleftright,bottom,bottomrightleft,bottomright
 *
 * This graphic should give you a better idea of the position:
 *	tl tlr      t      trl tr
 *	lt /-----------------\ rt
 *     |                 |
 *	l  |        c        | r
 *     |                 |
 *	lb \-----------------/ rb
 *	bl blr      b      brl br
 *
 * @param mixed elId the id of the element to display
 * @param mixed relId the id of the element to which you want to display it relative to
 * @param string location the location you want to have
 * @param int padding the padding to the relative element (default=0)
 * @param string display the display-value (default=block)
 */
function FWS_displayElement(elId,relId,location,padding,display)
{
	var element = FWS_getElement(elId);
	var relative = FWS_getElement(relId);
	
	// do both exist?
	if(element && relative)
	{
		// set default values
		if(typeof padding == 'undefined')
			padding = 0;
		if(typeof display == 'undefined')
			display = 'block';

		// we have to display the element first to get the offsetWidth
		if(element.offsetWidth == 0)
		{
			element.style.position = 'absolute';
			element.style.top = "-600px";
			element.style.left = "0px";
			element.style.display = 'block';
		}
		
		// get some properties we need
		var ewidth = parseInt(element.offsetWidth);
		var eheight = parseInt(element.offsetHeight);
		var rwidth = parseInt(relative.offsetWidth);
		var rheight = parseInt(relative.offsetHeight);
		var left = FWS_getPageOffsetLeft(relative);
		var top = FWS_getPageOffsetTop(relative);
	
		switch(location)
		{
			case 'topleft':
			case 'tl':
				element.style.top = (top - eheight - padding) + "px";
				element.style.left = (left - ewidth - padding) + "px";
				break;
			
			case 'topleftright':
			case 'tlr':
				element.style.top = (top - eheight - padding) + "px";
				element.style.left = left + "px";
				break;
			
			case 'top':
			case 't':
				element.style.top = (top - eheight - padding) + "px";
				element.style.left = (left + (rwidth / 2) - (ewidth / 2)) + "px";
				break;
			
			case 'toprightleft':
			case 'trl':
				element.style.top = (top - eheight - padding) + "px";
				element.style.left = (left + rwidth - ewidth)  + "px";
				break;
			
			case 'topright':
			case 'tr':
				element.style.top = (top - eheight - padding) + "px";
				element.style.left = (left + rwidth + padding)  + "px";
				break;
			
			case 'lefttop':
			case 'lt':
				element.style.top = top + "px";
				element.style.left = (left - ewidth - padding) + "px";
				break;
			
			case 'righttop':
			case 'rt':
				element.style.top = top + "px";
				element.style.left = (left + rwidth + padding) + "px";
				break;
			
			case 'c':
			case 'center':
				element.style.top = (top + (rheight / 2) - (eheight / 2)) + "px";
				element.style.left = (left + (rwidth / 2) - (ewidth / 2)) + "px";
				break;
			
			case 'left':
			case 'l':
				element.style.top = (top + (rheight / 2) - (eheight / 2)) + "px";
				element.style.left = (left - ewidth - padding) + "px";
				break;
			
			case 'right':
			case 'r':
				element.style.top = (top + (rheight / 2) - (eheight / 2)) + "px";
				element.style.left = (left + rwidth + padding) + "px";
				break;
			
			case 'leftbottom':
			case 'lb':
				element.style.top = (top + rheight - eheight) + "px";
				element.style.left = (left - ewidth - padding) + "px";
				break;
			
			case 'rightbottom':
			case 'rb':
				element.style.top = (top + rheight - eheight) + "px";
				element.style.left = (left + rwidth + padding) + "px";
				break;
			
			case 'bottomleft':
			case 'bl':
				element.style.top = (top + rheight + padding) + "px";
				element.style.left = (left - ewidth - padding) + "px";
				break;
			
			case 'bottomleftright':
			case 'blr':
				element.style.top = (top + rheight + padding) + "px";
				element.style.left = left + "px";
				break;
			
			case 'bottom':
			case 'b':
				element.style.top = (top + rheight + padding) + "px";
				element.style.left = (left + (rwidth / 2) - (ewidth / 2)) + "px";
				break;
			
			case 'bottomrightleft':
			case 'brl':
				element.style.top = (top + rheight + padding) + "px";
				element.style.left = (left + rwidth - ewidth) + "px";
				break;
			
			case 'bottomright':
			case 'br':
				element.style.top = (top + rheight + padding) + "px";
				element.style.left = (left + rwidth + padding) + "px";
				break;
		}
		
		element.style.position = 'absolute';
		element.style.display = display;
	}
}

/**
 * Sets the opacity of the given element.
 *
 * @param object el the element
 * @param int value the value (0..100)
 */
function FWS_setOpacity(el,value)
{
	if(Browser.isIE)
		el.style.filter = "Alpha(opacity=" + value + ")";
	else if(Browser.isOpera || Browser.isWebKit)
		el.style.opacity = value / 100;
	else
		el.style.MozOpacity = value / 100;
}

/**
 * Adds the given HTML-code to the page (the end of the innerHTML of the body-tag)
 *
 * @param string html the HTML-code to add
 */
function FWS_addToPage(html)
{
	var body = document.getElementsByTagName('body')[0];
	body.innerHTML += html;
}

/**
 * Adds or removes the given classname to the given element
 *
 * @param object element the element
 * @param string className the class-name to add/remove
 */
function FWS_toggleClassName(element,className)
{
	if(element.className.indexOf(className) >= 0)
		FWS_removeClassName(element,className);
	else
		FWS_addClassName(element,className);
}

/**
 * Adds the given className to the given element
 * 
 * @param object element the element
 * @param string className the className to add
 */
function FWS_addClassName(element,className)
{
	FWS_removeClassName(element,className);
	element.className += " " + className;
}

/**
 * Removes the given className from the given element
 * 
 * @param object element the element
 * @param string className the className to remove
 */
function FWS_removeClassName(element,className)
{
	var cls = element.className.split(" ");
	var ar = new Array();
	for(var i = cls.length;i > 0;)
	{
		if(cls[--i] != className)
			ar[ar.length] = cls[i];
	}
	element.className = ar.join(" ");
}

/**
 * Adds the given event-function to the given element
 *
 * @param object element the element
 * @param string evname the event-name
 * @param function func the callback-function
 */
function FWS_addEvent(el,evname,func)
{
	// IE
	if(el.attachEvent)
		el.attachEvent("on" + evname,func);
	// Gecko / W3C
	else if(el.addEventListener)
		el.addEventListener(evname,func,true);
	else
		el["on" + evname] = func;
}

/**
 * Removes the given event-function from the given element
 *
 * @param object element the element
 * @param string evname the event-name
 * @param function func the callback-function
 */
function FWS_removeEvent(el,evname,func)
{
	// IE
	if(el.detachEvent)
		el.detachEvent("on" + evname,func);
	// Gecko / W3C
	else if(el.removeEventListener)
		el.removeEventListener(evname,func,true);
	else
		el["on" + evname] = null;
}

/**
 * "Includes" the given javascript-file
 *
 * @param string file the file to include
 */
function FWS_includeJS(file)
{
	var head = document.getElementsByTagName('head')[0];
	head.innerHTML += '<script type="text/javascript" src="' + file + '"></script>';
}

/**
 * Hides or shows the element with given id
 *
 * @param mixed elId the id of the element
 * @param string display the value of style.display (default 'block')
 */
function FWS_toggleElement(elId,display)
{
	if(typeof display == 'undefined')
		display = 'block';
	
	var element = FWS_getElement(elId);
	if(element.style.display == 'none')
		element.style.display = display;
	else
		element.style.display = 'none';
}

/**
 * Hides the element with given id
 *
 * @param mixed elId the id of the element
 * @param string display the value of style.display (default 'block')
 */
function FWS_showElement(elId,display)
{
	if(typeof display == 'undefined')
		display = 'block';
	
	var element = FWS_getElement(elId);
	if(element)
		element.style.display = display;
}

/**
 * Hides the element with given id
 *
 * @param mixed elId the id of the element
 */
function FWS_hideElement(elId)
{
	var element = FWS_getElement(elId);
	if(element)
		element.style.display = 'none';
}

/**
 * Replaces the content of the element with given id with the given content
 * That means that element.innerHTML will be replaced by content.
 *
 * @param mixed id the id of the element
 * @param string content the new content
 */
function FWS_replaceContent(id,content)
{
	var element = FWS_getElement(id);
	if(element)
		element.innerHTML = content;
}

/**
 * determines the left page-offset of the given element
 *
 * @param object el the element
 * @return int the left page-offset
 */
function FWS_getPageOffsetLeft(el)
{
	var x = el.offsetLeft;
	if(el.offsetParent != null)
    x += FWS_getPageOffsetLeft(el.offsetParent);
	
  return x;
}

/**
 * determines the top page-offset of the given element
 *
 * @param object el the element
 * @return int the top page-offset
 */
function FWS_getPageOffsetTop(el)
{
  var y = el.offsetTop;
  if(el.offsetParent != null)
    y += FWS_getPageOffsetTop(el.offsetParent);

  return y;
}

/**
 * @return the body-element of the IE
 */
function FWS_getIEBody()
{
	if(document.compatMode && document.compatMode != "BackCompat")
		return document.documentElement;
	return document.body;
}

/**
 * Determines the scroll-offset of the page
 *
 * @return array an numeric array with the offset (x,y)
 */
function FWS_getScrollOffset()
{
	var dim;
	if(Browser.isIE)
	{
		var body = FWS_getIEBody();
		dim = new Array(body.scrollLeft,body.scrollTop);
	}
	else
		dim = new Array(window.pageXOffset,window.pageYOffset);
	return dim;
}

/**
 * Determines the size of the window
 *
 * @return array an numeric array with the window-size (width,height)
 */
function FWS_getWindowSize()
{
	var size;
	if(Browser.isIE)
	{
		var body = FWS_getIEBody();
		size = new Array(body.clientWidth,body.clientHeight);
	}
	else
		size = new Array(window.innerWidth,window.innerHeight);
	return size;
}

/**
 * Determines the size of the page
 *
 * @return array an numeric array with the page-size (width,height)
 */
function FWS_getPageSize()
{
	var size;
	if(Browser.isIE)
	{
		var body = FWS_getIEBody();
		size = new Array(body.clientWidth,body.clientHeight);
	}
	else
	{
		var body = document.getElementsByTagName('body')[0];
		size = new Array(body.offsetWidth,body.offsetHeight);
	}
	return size;
}

/**
 * @param mixed id the id of the element
 * @return object the element with given id
 */
function FWS_getElement(id)
{
	return document.getElementById(id);
}

/**
 * Checks wether an array contains the given entry
 *
 * @param mixed entry the entry to search for
 * @return boolean true if the array contains the entry
 */
Array.prototype.contains = function(entry)
{
	for(var i = 0;i < this.length;i++)
	{
		if(this[i] == entry)
			return true;
	}
	return false;
};

/**
 * Removes the (first found) given entry from an array
 * Note that this method may change the order of the elements!
 *
 * @param mixed entry the entry to remove
 */
Array.prototype.removeEntry = function(entry)
{
	var index = -1;
	for(var i = 0;i < this.length;i++)
	{
		if(this[i] == entry)
		{
			index = i;
			break;
		}
	}
	
	if(index >= 0)
	{
		var temp = this[index];
		this[index] = this[this.length - 1];
		this[this.length - 1] = temp;
		this.pop();
	}
};

/**
 * Checks wether the string ends with the given substring.
 *
 * @param string str the substring with which is may end
 * @return boolean true if the string ends with the given one
 */
String.prototype.endsWith = function(str)
{
	return this.substr(this.length - str.length) == str;
};

/**
 * Returns a trimmed version of this string. That means all whitespace and the beginning
 * and the end will be removed.
 *
 * @return string the trimmed string
 */
String.prototype.trim = function()
{
	if(this == "")
		return this;

	// determine the first not-whitespace-character
	var i = 0;
	for(;i < this.length;i++)
	{
		if(this[i] != '\n' && this[i] != '\r' && this[i] != '\t' && this[i] != ' ')
			break;
	}

	// determine the last not-whitespace-character
	var a = this.length - 1;
	for(;a >= 0;a--)
	{
		if(this[a] != '\n' && this[a] != '\r' && this[a] != '\t' && this[a] != ' ')
			break;
	}

	return this.substr(i,a - i + 1);
};

/**
 * Determines how many occurrences of needle this string has
 *
 * @param string needle the string where to look for
 * @return int the number of occurrences
 */
String.prototype.substr_count = function substr_count(needle)
{
	var str = this;
	var matches = 0;
	var i = 0;
	while(matches < 5)
	{
		i = -1;
		try
		{
			i = str.indexOf(needle);
		}
		catch(Exception)
		{
		
		}
		
		if(i < 0)
			break;
		
		matches++;
		str = str.substring(i + needle.length);
	}
	
	return matches;
};
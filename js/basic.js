/**
 * Contains the basic javascript-functions
 *
 * @version			$Id: basic.js 710 2008-05-16 07:00:16Z nasmussen $
 * @package			PHPLib
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

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
function PLIB_getDeleteIds(checkboxPrefix,start)
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
function PLIB_setCookie(name,value,lifetime,path,domain)
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
function PLIB_openDefaultPopup(filename,title,width,height)
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
function PLIB_openPopup(filename,title,options)
{
	window.open(filename,title,options);
}

/**
 * Prints all properties of the given object on the screen
 *
 * @param mixed object the object
 * @param boolean showFunctions do you want to show functions? (default=false)
 */
function PLIB_printObject(object,showFunctions)
{
	if(typeof showFunctions == 'undefined')
		showFunctions = false;

	var props = '<pre style="max-height: 500px; overflow: auto; border-top: 1px solid #555;';
	props += ' border-bottom: 1px solid #555;">';
	props += PLIB_getObjectProperties(object,showFunctions);
	props += '</pre>';

	var el;
	if(!PLIB_getElement('PLIB_debug_field'))
	{
		var str = '';
		str += '<h3 align="center">JS-Debugging of ' + object + ' [' + (typeof object) + ']</h3>';
		str += props;
		str += '<center><a href="javascript:PLIB_hideElement(\'PLIB_debug_field\');">Close</a></center>';
		
		el = document.createElement('div');
		el.id = 'PLIB_debug_field';
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
		el = PLIB_getElement('PLIB_debug_field');
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
function PLIB_getObjectProperties(object,showFunctions)
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
					str += PLIB_escapeHTML(object[x]);
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
		str = PLIB_escapeHTML(object);
	
	return str;
}

/**
 * Escapes the chars "<" and ">" to force the browser to display the code
 *
 * @param string input the input-string
 * @return string the output-string
 */
function PLIB_escapeHTML(input)
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
 *	left,right,
 *	leftbottom,rightbottom,
 *	bottomleft,bottomleftright,bottom,bottomrightleft,bottomright
 *
 * This graphic should give you a better idea of the position:
 *	tl tlr      t      trl tr
 *	lt /-----------------\ rt
 *     |                 |
 *	l  |                 | r
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
function PLIB_displayElement(elId,relId,location,padding,display)
{
	var element = PLIB_getElement(elId);
	var relative = PLIB_getElement(relId);
	
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
		var left = PLIB_getPageOffsetLeft(relative);
		var top = PLIB_getPageOffsetTop(relative);
	
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
 * Adds the given HTML-code to the page (the end of the innerHTML of the body-tag)
 *
 * @param string html the HTML-code to add
 */
function PLIB_addToPage(html)
{
	var body = document.getElementsByTagName('body')[0];
	body.innerHTML += html;
}

/**
 * Adds the given className to the given element
 * 
 * @param object element the element
 * @param string className the className to add
 */
function PLIB_addClassName(element,className)
{
	PLIB_removeClassName(element,className);
	element.className += " " + className;
}

/**
 * Removes the given className from the given element
 * 
 * @param object element the element
 * @param string className the className to remove
 */
function PLIB_removeClassName(element,className)
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
function PLIB_addEvent(el,evname,func)
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
function PLIB_removeEvent(el,evname,func)
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
function PLIB_includeJS(file)
{
	var head = document.getElementsByTagName('head')[0];
	head.innerHTML += '<script type="text/javascript" src="' + file + '"></script>';
}

/**
 * Hides or shows the element with given id
 *
 * @param mixed elId the id of the element
 */
function PLIB_toggleElement(elId)
{
	var element = PLIB_getElement(elId);
	if(element.style.display == 'none')
		element.style.display = 'block';
	else
		element.style.display = 'none';
}

/**
 * Hides the element with given id
 *
 * @param mixed elId the id of the element
 * @param string display the value of style.display (default 'block')
 */
function PLIB_showElement(elId,display)
{
	if(typeof display == 'undefined')
		display = 'block';
	
	var element = PLIB_getElement(elId);
	if(element)
		element.style.display = display;
}

/**
 * Hides the element with given id
 *
 * @param mixed elId the id of the element
 */
function PLIB_hideElement(elId)
{
	var element = PLIB_getElement(elId);
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
function PLIB_replaceContent(id,content)
{
	var element = PLIB_getElement(id);
	if(element)
		element.innerHTML = content;
}

/**
 * determines the left page-offset of the given element
 *
 * @param object el the element
 * @return int the left page-offset
 */
function PLIB_getPageOffsetLeft(el)
{
	var x = el.offsetLeft;
	if(el.offsetParent != null)
    x += PLIB_getPageOffsetLeft(el.offsetParent);
	
  return x;
}

/**
 * determines the top page-offset of the given element
 *
 * @param object el the element
 * @return int the top page-offset
 */
function PLIB_getPageOffsetTop(el)
{
  var y = el.offsetTop;
  if(el.offsetParent != null)
    y += PLIB_getPageOffsetTop(el.offsetParent);

  return y;
}

/**
 * Determines the size of the page
 *
 * @return array an numeric array with the page-size
 */
function PLIB_getPageSize()
{
	var size;
	if(document.all && !window.opera)
		size = new Array(document.body.offsetWidth,document.body.offsetHeight);
	else
		size = new Array(window.innerWidth,window.innerHeight);
	return size;
}

/**
 * @param mixed id the id of the element
 * @return object the element with given id
 */
function PLIB_getElement(id)
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
 * Builds a string with all elements of the array separated by the given string.
 *
 * @param string sep the separator of the elements
 * @return string the result-string
 */
Array.prototype.implode = function(sep)
{
	var res = '';
	for(var i = 0;i < this.length;i++)
	{
		res += this[i];
		if(i < this.length - 1)
			res += sep;
	}
	return res;
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
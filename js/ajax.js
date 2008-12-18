/**
 * Contains the AJAX-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Constructor for the AJAX-class
 */
function FWS_Ajax()
{
	// properties
	this.mimeType = 'text/plain; charset=UTF-8';
	this.xmlHttp = null;

	// private methods
	this.onStart = function()
	{
		// do nothing by default
	};
	
	this.onFinish = function()
	{
		// do nothing by default
	};

	/**
	 * Prepares an HTTP-request
	 *
	 * @param function onfinish the finish-callback
	 */
	this._prepare_request = function(onfinish)
	{
		// we have to do this for every request with IE
		if(this.xmlHttp == null || Browser.isIE)
			this.xmlHttp = FWS_getXmlHttpObject();
		
		// copy to local variables
		var cxmlHttp = this.xmlHttp;
		var conFinish = this.onFinish;
		if(!cxmlHttp)
			return;
		
		this.onStart();
		
		// does not work in IE
		// TODO: the special chars will not be transferred correctly in IE. how to fix that?
		if(Browser.isIE)
			cxmlHttp.overrideMimeType(this.mimeType);
		
		// build callback
		var callback = function()
		{
			if(cxmlHttp.readyState == 4 || cxmlHttp.readyState == 'complete')
			{
				onfinish(cxmlHttp.responseText);
				conFinish();
			}
		};
		
		cxmlHttp.onreadystatechange = callback;
	};
	
	// public methods
	this.setMimeType = setMimeType;
	this.setEventHandler = setEventHandler;
	this.sendGetRequest = sendGetRequest;
	this.sendPostRequest = sendPostRequest;
}

/**
 * Sets the mimetype that should be used for the requests. That could be
 * something like "text/html; charset=iso-8859-1".
 *
 * @param string mimeType the new value
 */
function setMimeType(mimeType)
{
	this.mimeType = mimeType;
}

/**
 * Sets an event-handler that should be used for every ajax-request
 * This may be usefull to display a kind of "wait-symbol" of something
 * like that.
 *
 * @param string event the event-name: onstart, onfinish
 * @param function handler the callback-function to connect with the event
 */
function setEventHandler(event,handler)
{
	if(typeof handler != 'function')
		return;

	switch(event)
	{
		case 'start':
		case 'onstart':
			this.onStart = handler;
			break;
		
		case 'finish':
		case 'onfinish':
			this.onFinish = handler;
			break;
	}
}

/**
 * Sends a GET-request via AJAX
 *
 * @param string url the URL to call
 * @param function onfinish the function to call after THIS request. Note that
 * 			the onfinish-event-function will be called, too!
 */
function sendGetRequest(url,onfinish)
{
	this._prepare_request(onfinish);
	this.xmlHttp.open('GET',url,true);
	
	try {
		this.xmlHttp.send(null);
	}
	catch(e) {
		// ignore
	}
}

/**
 * Sends a POST-request via AJAX
 *
 * @param string url the URL to call
 * @param string parameters the parameter to send via POST
 * @param function onfinish the function to call after THIS request. Note that
 * 			the onfinish-event-function will be called, too!
 */
function sendPostRequest(url,parameters,onfinish)
{
	this._prepare_request(onfinish);
	this.xmlHttp.open('POST',url,true);
	this.xmlHttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	this.xmlHttp.setRequestHeader('Content-length',parameters.length);
	this.xmlHttp.setRequestHeader('Connection','close');
	
	try {
		this.xmlHttp.send(parameters);
	}
	catch(e) {
		// ignore
	}
}

/**
 * Displays a "wait-bar" at the top of the page (fixed)
 *
 * @param mixed id the id of the bar
 * @param string message the message to display (default=Please wait)
 */
function FWS_displayWaitBar(id,message)
{
	if(typeof message == 'undefined')
		message = 'Please wait';
	
	// do we have to create the element?
	var bar = FWS_getElement(id);
	if(!bar)
	{
		var body = document.getElementsByTagName('body')[0];
		var element = document.createElement('div');
		element.id = id;
		element.align = 'center';
		element.style.display = 'none';
		//element.style.position = 'absolute';
		element.innerHTML = 'Please wait...';
		body.appendChild(element);
		
		bar = FWS_getElement(id);
	}
	
	// determine width
	var pageSize = FWS_getPageSize();
	var width = pageSize[0] - 23;
	
	// display
	bar.style.display = 'block';
	bar.style.width = width + 'px';
	bar.style.top = '0px';
	bar.style.position = 'fixed';
}

/**
 * Builds the XML-HTTP-object
 * 
 * @return object the object
 */
function FWS_getXmlHttpObject()
{
	var req = null;
	if(typeof XMLHttpRequest != "undefined")
		req = new XMLHttpRequest();
	
	if(!req && typeof ActiveXObject != "undefined")
	{
		try
		{
			req = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e2)
			{
				try
				{
					req = new ActiveXObject("Msxml2.XMLHTTP.4.0");
				}
				catch(e3)
				{
					req = null;
				}
			}
		}
	}
	
	if(!req && window.createRequest)
		req = window.createRequest();
		
	return req;
}
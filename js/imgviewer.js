/**
 * Contains the javascript image-viewer functions
 * 
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// the config-object
var ivConfig = {
	showBG : true,
	bgOpacity : 80,
	bgFadeSteps : 10,
	imgFadeSteps : 10,
	imgResizeSteps : 10,
	initialBlockSize : '100px',
	loadingImg : null,
	blockPadding : 0
};

/**
 * Configures the image-viewer
 *
 * @param boolean showBG do you want to show the background (which is faded in)? (default=true)
 * @param int bgOpacity the final opacity of the background (0..100) (default=80)
 * @param int bgFadeSteps the number of steps to fade in the background (default=10)
 * @param int imgFadeSteps the number of steps to fade in the image (default=10)
 * @param int imgResizeSteps the number of steps to resize the image (default=10)
 * @param string initialBlockSize the initial size of the image-block (while loading the image)
 *		(default=100px)
 * @param string loadingImg the image-URL to display while the real image is loading (null = none)
 * 		(default=null)
 * @param int blockPadding how much padding do you have for the block? (in pixel) (default=0)
 */
function PLIB_configureImageViewer(showBG,bgOpacity,bgFadeSteps,imgFadeSteps,imgResizeSteps,
	initialBlockSize,loadingImg,blockPadding)
{
	ivConfig.showBG = showBG;
	ivConfig.bgOpacity = bgOpacity;
	ivConfig.bgFadeSteps = bgFadeSteps;
	ivConfig.imgFadeSteps = imgFadeSteps;
	ivConfig.imgResizeSteps = imgResizeSteps;
	ivConfig.initialBlockSize = initialBlockSize;
	ivConfig.loadingImg = loadingImg;
	ivConfig.blockPadding = blockPadding;
}

/**
 * Shows the image from the given URL. The complete page will get a background with the defined
 * opacity. The id of the background-div is "plib_image_viewer_bg". So you can specify
 * CSS-attributes for it.
 * The image will be loaded in another div-area, that has the id "plib_image_viewer". As soon as
 * the image is loaded it will be resized smoothly to the complete size. A click on it hides it.
 * The image will get the id "plib_image_viewer_img".
 *
 * Note that you can configure many values with PLIB_configureImageViewer().
 *
 * @param string imgURL the URL of the image
 */
function PLIB_showImage(imgURL)
{
	var size = PLIB_getWindowSize();
	var psize = PLIB_getPageSize();
	var offset = PLIB_getScrollOffset();
	
	// create background
	if(ivConfig.showBG)
	{
		var bg;
		if((bg = PLIB_getElement('plib_image_viewer_bg')) == null)
		{
			bg = document.createElement('div');
			bg.id = 'plib_image_viewer_bg';
			bg.style.width = size[0] + 'px';
			bg.style.height = psize[1]  + 'px';
			document.getElementsByTagName('body')[0].appendChild(bg);
		}
		
		// reset background
		if(Browser.isIE)
		{
			bg.style.position = 'absolute';
			bg.style.left = offset[0] + 'px';
			bg.style.top = (offset[1] - 100) + 'px';
			bg.style.height = (psize[1] + 200)  + 'px';
		}
		else
		{
			bg.style.position = 'fixed';
			bg.style.left = '0px';
			bg.style.top = '0px';
		}
		PLIB_setOpacity(bg,0);
		PLIB_showElement(bg.id);
	}

	// create image-container
	var el;
	if((el = PLIB_getElement('plib_image_viewer')) == null)
	{
		el = document.createElement('div');
		el.style.position = 'absolute';
		el.id = 'plib_image_viewer';
		el.style.overflow = 'hidden';
		el.onclick = function() {
			PLIB_hideElement(bg.id);
			PLIB_hideElement(el.id);
		}
		document.getElementsByTagName('body')[0].appendChild(el);
	}
	
	// reset image-container
	var initialSize = ivConfig.initialBlockSize;
	var iinitialSize = parseInt(initialSize);
	el.style.display = 'block';
	el.style.width = initialSize;
	el.style.height = initialSize;
	el.style.top = (size[1] / 2 - iinitialSize / 2 + offset[1]) + 'px';
	el.style.left = (size[0] / 2 - iinitialSize / 2 + offset[0]) + 'px';
	
	// set content
	var html = '<img id="plib_image_viewer_img" src="' + imgURL + '" />';
	if(ivConfig.loadingImg != null)
		html += '<img id="plib_image_viewer_loadimg" src="' + ivConfig.loadingImg + '" />';
	el.innerHTML = html;
	
	// fade in the background
	if(ivConfig.showBG)
		_fadeIn(bg.id,ivConfig.bgOpacity,ivConfig.bgFadeSteps);
	
	// fade in the image and resize it as soon as the image has been loaded
	var img = PLIB_getElement('plib_image_viewer_img');
	img.style.visibility = 'hidden';
	PLIB_setOpacity(img,0);
	
	// set the position of the loading-image
	if(ivConfig.loadingImg != null)
	{
		var loadImg = PLIB_getElement('plib_image_viewer_loadimg');
		loadImg.style.position = 'absolute';
		loadImg.style.left = ((iinitialSize + ivConfig.blockPadding * 2) / 2 - loadImg.width / 2) + 'px';
		loadImg.style.top = ((iinitialSize + ivConfig.blockPadding * 2) / 2 - loadImg.height / 2) + 'px';
	}
	
	var loadFunc = function() {
		// hide loading-image
		if(ivConfig.loadingImg != null)
			loadImg.style.visibility = 'hidden';
		
		var iwidth = parseInt(img.width);
		var iheight = parseInt(img.height);
		
		// display real image
		_setImageSize(img,iinitialSize,iinitialSize);
		img.style.visibility = 'visible';
		_fadeIn(img.id,100,ivConfig.imgFadeSteps);
		_smoothResize(el.id,img.id,iwidth,iheight,ivConfig.imgResizeSteps);
	};
	
	// some browser don't seem to fire the load-event if the image is already loaded. Therefore we
	// check that and if so we call the function directly
	if(img.complete)
		loadFunc();
	else
		PLIB_addEvent(img,'load',loadFunc);
}

/**
 * Fades in the element with given id. You can specify the maximum opacity and the number of steps
 *
 * @param mixed elId the id of the element
 * @param int max the opacity to reach (0..100)
 * @param int steps the number of steps
 * @param int i the current step index (you don't have to set this)
 */
function _fadeIn(elId,max,steps,i)
{
	if(typeof i == 'undefined')
		i = 0;

	var el = PLIB_getElement(elId);
	if(i < steps)
	{
		PLIB_setOpacity(el,i * max / steps);
		window.setTimeout('_fadeIn("' + elId + '",' + max + ',' + steps + ',' + (i + 1) + ')',5);
	}
	else
		PLIB_setOpacity(el,max);
}

/**
 * Resizes the element with given id smoothly to the given width and height. You can specify
 * the number of steps.
 *
 * @param mixed elId the id of the element
 * @param mixed imgId the id of the image
 * @param int width the width to reach
 * @param int height the height to reach
 * @param int steps the number of steps
 * @param int i the current step index (you don't have to set this)
 */
function _smoothResize(elId,imgId,width,height,steps,i)
{
	if(typeof i == 'undefined')
		i = 0;
	
	var el = PLIB_getElement(elId);
	var img = PLIB_getElement(imgId);
	var csize = [parseInt(el.style.width),parseInt(el.style.height)];
	var wdiff = width - csize[0];
	var hdiff = height - csize[1];
	// anything more to do?
	if(wdiff > 0 || hdiff > 0)
	{
		var realwidth = wdiff / (steps - i);
		var realheight = hdiff / (steps - i);
		_setImageSize(img,csize[0] + realwidth,csize[1] + realheight);
		el.style.width = (csize[0] + realwidth) + 'px';
		el.style.height = (csize[1] + realheight) + 'px';
		
		el.style.left = (parseInt(el.style.left) - realwidth / 2) + 'px';
		el.style.top = (parseInt(el.style.top) - realheight / 2) + 'px';
		window.setTimeout('_smoothResize("' + elId + '","' + imgId + '",' + width + ',' + height +
			',' + steps + ',' + (i + 1) + ')',5);
	}
	else
	{
		// ensure that everything is visible
		if(parseInt(el.style.top) < 0)
			el.style.top = '0px';
	}
}

/**
 * Sets the size of the given image to the given one
 *
 * @param object img the image
 * @param int width the new width
 * @param int height the new height
 */
function _setImageSize(img,width,height)
{
	if(Browser.isIE)
	{
		img.width = width;
		img.height = height;
	}
	else
	{
		img.style.width = width + 'px';
		img.style.height = height + 'px';
	}
}
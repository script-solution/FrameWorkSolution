/**
 * Contains the colorpicker-class
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
 * The id of the color-picker-area
 */
FWS_ColorPicker.id = 'fws_colorpicker';

/**
 * The current instance
 */
FWS_ColorPicker.instance = null;

/**
 * Constructor
 *
 * @param string fwspath the path to the framework
 * @param mixed inputId the id of the input-element (which holds the color)
 * @param function onselected a function that should be called as soon as a color has been
 *	selected (optional)
 * @param function onhidden a function that should be called as soon as the color-picker has been
 *	hidden (optional
 */
function FWS_ColorPicker(fwspath,inputId,onselected,onhidden)
{
	this.fwspath = fwspath;
	this.cols = 6;
	this.inputId = inputId;
	if(typeof onselected != 'undefined')
		this.onselected = onselected;
	else
		this.onselected = null;
	if(typeof onhidden != 'undefined')
		this.onhidden = onhidden;
	else
		this.onhidden = null;
	
	FWS_ColorPicker.instance = this;
	
	/**
	 * Builds the html-code for the color-picker
	 *
	 * @return string the html-code
	 */
	this.getHTML = function()
	{
		var html = '';
		var colors = this.getColors();
		html += '<table>' + "\n";
		html += '	<tbody>' + "\n";
		html += '	<tr>' + "\n";
		html += '		<td id="fws_cp_preview" colspan="' + (colors[0].length - 1) + '">&nbsp;</td>' + "\n";
		html += '		<td id="fws_cp_close" onclick="FWS_ColorPicker.instance.hide()">X</td>' + "\n";
		html += '	</tr>' + "\n";
		for(var i = 0;i < colors.length;i++)
		{
			html += '		<tr>' + "\n";
			for(var x = 0;x < colors[i].length;x++)
			{
					html += '			<td id="col_' + colors[i][x] + '"';
					html += ' style="background-color: ' + colors[i][x] + ';"';
					html += ' onmouseover="FWS_ColorPicker.instance.hoverCell(this.id)"';
					html += ' onclick="FWS_ColorPicker.instance.clickCell(this.id)"';
					html += '>&nbsp;</td>' + "\n";
			}
			html += '		</tr>' + "\n";
		}
		html += '	</tbody>' + "\n";
		
		html += '</table>' + "\n";
		return html;
	};
	
	/**
	 * Collects the colors that should be displayed
	 *
	 * @return array an array with the colors
	 */
	this.getColors = function()
	{
		var colors = new Array();
		
		// default colors
		var defcolors = new Array(
			'FF0000','00FF00','0000FF','FFFF00','FF00FF','00FFFF',
			'D50000','00D500','0000D5','D5D500','D500D5','00D5D5',
			'C00000','00C000','0000C0','C0C000','C000C0','00C0C0',
			'A00000','00A000','0000A0','A0A000','A000A0','00A0A0',
			'950000','009500','000095','959500','950095','009595',
			'800000','008000','000080','808000','800080','008080',
			'600000','006000','000060','606000','600060','006060',
			'400000','004000','000040','404000','400040','004040',
			'200000','002000','000020','202000','200020','002020',
			'000000','202020','404040','606060','808080','959595',
			'A0A0A0','C0C0C0','D5D5D5','EBEBEB','F0F0F0','FFFFFF'
		);
		
		var i = 0;
		var row = -1;
		for(var x = 0;x < defcolors.length;x++)
		{
			if(x % this.cols == 0)
			{
				colors[++row] = new Array();
				i = 0;
			}
		
			colors[row][i] = '#' + defcolors[x];
			
			i++;
		}
		
		// other colors
		var used_values = new Array('00','33','66','99','CC','FF');
		//var used_values = new Array('00','22','44','66','88','99','CC','FF');
		var r_desc = {
			start : used_values.length - 1,
			cmp: 0,
			upd: -1
		};
		var r_asc = {
			start: 0,
			cmp: used_values.length,
			upd: 1
		};
		var r_array = r_desc;
		
		var offset = 0;
		for(var b = used_values.length - 1;b >= 0;b--)
		{
			if(b == 3)
			{
				offset = used_values.length;
				row = -1;
			}
			
			for(var r = r_array['start'];r != r_array['cmp'];r += r_array['upd'])
			{
				row++;
				if(!colors[row])
					colors[row] = new Array();
		
				for(var g = 0;g < used_values.length;g++)
					colors[row][g + offset] = '#' + used_values[r] + used_values[g] + used_values[b];
			}
			r_array = (b % 2 == 0) ? r_desc : r_asc;
		}
		
		return colors;
	};
	
	/**
	 * The hover-handler
	 *
	 * @param mixed cellid the id of the cell
	 */
	this.hoverCell = function(cellid)
	{
		FWS_ColorPicker.instance._hoverCell(cellid);
	};
	
	/**
	 * The actual hover-handler
	 *
	 * @param mixed cellid the id of the cell
	 */
	this._hoverCell = function(cellid)
	{
		FWS_getElement('fws_cp_preview').innerHTML = cellid.substring(4);
	};
	
	/**
	 * The click-handler
	 *
	 * @param mixed cellid the id of the cell
	 */
	this.clickCell = function(cellid)
	{
		FWS_ColorPicker.instance._clickCell(cellid);
	};
	
	/**
	 * The actual click-handler
	 *
	 * @param mixed cellid the id of the cell
	 */
	this._clickCell = function(cellid)
	{
		var input = FWS_getElement(this.inputId);
		if(input != null)
			input.value = cellid.substring(5);
		if(this.onselected != null)
			this.onselected(cellid.substring(5));
		FWS_hideElement(FWS_ColorPicker.id);
	};
	
	this.toggle = toggle;
	this.hide = hide;
}

/**
 * Hides the colorpicker
 */
function hide()
{
	FWS_hideElement(FWS_ColorPicker.id);
	if(this.onhidden != null)
		this.onhidden();
}

/**
 * Toggles the display of the colorpicker relative to the element with given id
 *
 * @param mixed relId the id of the element to use for the positioning
 * @param string position the position (see FWS_displayElement for the possible values)
 */
function toggle(relId,position)
{
	if(typeof position == 'undefined')
		position = 'lt';
	
	// do we have to create it?
	var created = false;
	if(!FWS_getElement(FWS_ColorPicker.id))
	{
		var body = document.getElementsByTagName('body')[0];
		var element = document.createElement('div');
		element.id = FWS_ColorPicker.id;
		element.zindex = 100;
		element.innerHTML = this.getHTML();
		body.appendChild(element);
		
		var head = document.getElementsByTagName('head')[0];
		var element = document.createElement('link');
		element.rel = 'stylesheet';
		element.type = 'text/css';
		element.href = this.fwspath + 'js/colorpicker.css';
		head.appendChild(element);
		created = true;
	}
	
	FWS_ColorPicker.instance = this;
	
	var cp = FWS_getElement(FWS_ColorPicker.id);
	if(cp.style.display == 'block')
	{
		FWS_hideElement(FWS_ColorPicker.id);
		return;
	}
	
	var rel = FWS_getElement(relId);
	
	// we have to display it at first because otherwise offsetWidth is not set
	cp.style.position = 'absolute';
	cp.style.top = '-600px';
	cp.style.display = 'block';
	
	// wait a little bit until we display it
	window.setTimeout(function() {
		// check if the space on the right side of the relative-element is enough
		// for our color-picker
		var windowWidth = FWS_getPageSize()[0];
		if(FWS_getPageOffsetLeft(rel) + cp.offsetWidth + rel.offsetWidth > windowWidth - 25)
			FWS_displayElement(FWS_ColorPicker.id,relId,position,2);
		else
			FWS_displayElement(FWS_ColorPicker.id,relId,position,2);
	},created ? 50 : 0);
}
/**
 * Contains the javascript-calendar
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Stores the instance of the currently displayed calendar
 */
FWS_Calendar.instance = null;

/**
 * The id of the calendar-area
 */
FWS_Calendar.id = 'fws_calendar';

/**
 * The language-entries
 */
FWS_Calendar.language = new Array();

/**
 * Constructor
 *
 * @param string path the path to the folder of the calendar.js
 * @param string inputId the id of the input-field
 * @param function onSelected the callback-function which should be called as soon
 * 			as the user selects a date
 */
function FWS_Calendar(path,inputId,onSelected)
{
	// properties
	this.cssFile = '';
	this.path = path;
	this.inputId = inputId;
	this.onStartUp = null;
	this.maxYear = 2020;
	this.minYear = 1900;
	
	// default-select-function?
	if(typeof onSelected != 'function')
	{
		this.onSelected = function(date) {
			var input = FWS_getElement(this.inputId);
			var val = date.getFullYear() + "-";
			val += this.get2Digits((date.getMonth() + 1)) + "-" + this.get2Digits(date.getDate());
			input.value = val;
		};
	}
	// ok, use the given one
	else
		this.onSelected = onSelected;
	
	this.selectedDate = null;
	this.date = new Date();
	
	// public methods
	this.display = display;
	this.setCSSFile = setCSSFile;
	this.nextMonth = nextMonth;
	this.prevMonth = prevMonth;
	this.prevYear = prevYear;
	this.nextYear = nextYear;
	this.setStartUpFunction = setStartUpFunction;
	this.setSelectedDate = setSelectedDate;
	this.setMinYear = setMinYear;
	this.setMaxYear = setMaxYear;
	this.enableButton = enableButton;
	this.disableButton = disableButton;
	this.adjustButtons = adjustButtons;
	
	// private methods
	
	/**
	 * Adds a '0' in front of the number if it has just one digit
	 *
	 * @param mixed input the input-number
	 * @return string the number to display
	 */
	this.get2Digits = function(input)
	{
		if(input < 10)
			return '0' + input;
		
		return input;
	};
	
	/**
	 * Builds the html-code for the calendar
	 *
	 * @return string the html-code
	 */
	this.getHTML = function()
	{
		var html = '';
		html += '<table>' + "\n";
		html += '	<thead>' + "\n";
		html += '		<tr>' + "\n";
		html += '			<td id="fwscal_prev_year" class="cal_button"';
		html += ' onmouseover="this.className = \'cal_button_hover\';"';
		html += ' onmouseout="this.className = \'cal_button\';"';
		html += ' onclick="FWS_Calendar.instance.prevYear();">&lt;&lt;</td>' + "\n";
		html += '			<td id="fwscal_prev_month" class="cal_button"';
		html += ' onmouseover="this.className = \'cal_button_hover\';"';
		html += ' onmouseout="this.className = \'cal_button\';"';
		html += ' onclick="FWS_Calendar.instance.prevMonth();">&lt;</td>' + "\n";
		html += '			<td id="cal_headline" class="cal_month" colspan="4">&nbsp;</td>' + "\n";
		html += '			<td id="fwscal_next_month" class="cal_button"';
		html += ' onmouseover="this.className = \'cal_button_hover\';"';
		html += ' onmouseout="this.className = \'cal_button\';"';
		html += ' onclick="FWS_Calendar.instance.nextMonth();">&gt;</td>' + "\n";
		html += '			<td id="fwscal_next_year" class="cal_button"';
		html += ' onmouseover="this.className = \'cal_button_hover\';"';
		html += ' onmouseout="this.className = \'cal_button\';"';
		html += ' onclick="FWS_Calendar.instance.nextYear();">&gt;&gt;</td>' + "\n";
		html += '		</tr>' + "\n";
		
		html += '		<tr>' + "\n";
		for(var x = 0;x < 8;x++)
		{
			if(x == 0)
				html += '			<td class="cal_wkcorner">&nbsp;</td>' + "\n";
			else
				html += '			<td class="cal_wkday">' + FWS_Calendar.language['wd_' + (x - 1)] + '</td>' + "\n";
		}
		html += '		</tr>' + "\n";
		html += '	</thead>' + "\n";
		
		html += '	<tbody>' + "\n";
		for(var i = 0;i < 6;i++)
		{
			html += '		<tr>' + "\n";
			for(var x = 0;x < 8;x++)
			{
				if(x == 0)
					html += '			<td class="cal_wkno" id="cal_col_' + i + '_' + x + '">&nbsp;</td>' + "\n";
				else
					html += '			<td id="cal_col_' + i + '_' + x + '">&nbsp;</td>' + "\n";
			}
			html += '		</tr>' + "\n";
		}
		html += '	</tbody>' + "\n";
		
		html += '	<tfoot>' + "\n";
		html += '		<tr>' + "\n";
		html += '			<td colspan="8">';
		html += '<a href="javascript:FWS_hideElement(\'' + FWS_Calendar.id + '\');">';
		html += FWS_Calendar.language['close'] + '</a></td>' + "\n";
		html += '		</tr>' + "\n";
		html += '	</tfoot>' + "\n";
		
		html += '</table>' + "\n";
		return html;
	};

	/**
	 * Sets the date-object to the previous month
	 */
	this.setPrevMonth = function()
	{
		if(this.date.getMonth() > 0)
			this.date.setMonth(this.date.getMonth() - 1);
		else
		{
			if(this.date.getFullYear() > this.minYear)
			{
				this.date.setMonth(11);
				this.date.setFullYear(this.date.getFullYear() - 1);
			}
		}
		
		this.adjustButtons();
	};
	
	/**
	 * Sets the date-object to the next month
	 *
	 * @param boolean adjustButtons wether the buttons should be enabled / disabled
	 */
	this.setNextMonth = function(adjustButtons)
	{
		if(this.date.getMonth() < 11)
			this.date.setMonth(this.date.getMonth() + 1);
		else
		{
			if(this.date.getFullYear() < this.maxYear)
			{
				this.date.setMonth(0);
				this.date.setFullYear(this.date.getFullYear() + 1);
			}
		}
		
		if(typeof adjustButtons != 'undefined' && adjustButtons)
			this.adjustButtons();
	};

	/**
	 * This method will be called as soon as the user has selected a date.
	 * It writes the date to the specified input-field
	 */
	this._onSelected = function(id)
	{
		var col = FWS_getElement(id);
		var day = parseInt(col.innerHTML);
		if(day)
		{
			this.selectedDate = new Date(this.date.getFullYear(),this.date.getMonth(),day,0,0,0);
			this.onSelected(this.selectedDate);
		}
	};
	
	/**
	 * Fills the calendar with the corresponding values for the current date
	 */
	this.fill = function()
	{
		// init selected date
		var year = this.date.getFullYear();
		var month = this.date.getMonth();
		var cday = this.date.getDate();
		var day = 1;
		
		// store now
		var now = new Date();
		var nyear = now.getFullYear();
		var nmonth = now.getMonth();
		var nday = now.getDate();
		
		var syear = this.selectedDate != null ? this.selectedDate.getFullYear() : -1;
		var smonth = this.selectedDate != null ? this.selectedDate.getMonth() : -1;
		var sday = this.selectedDate != null ? this.selectedDate.getDate() : -1;
		
		// calculate some vars
		var monthDays = this.date.getMonthDays();
		var lastMonthDays = this.date.getMonthDays(month > 0 ? month - 1 : 11);
		var weekDay = this.date.getFirstGerWeekDayInMonth();
		var realDay = lastMonthDays - weekDay;
		
		for(var y = 0;y < 6;y++)
		{
			// determine week-number
			if(day > monthDays + 1)
				this.setNextMonth();
			this.date.setDate(day > monthDays + 1 ? realDay : day);
			
			var week = this.date.getWeekOfYear();
			
			// restore date
			this.date.setDate(cday);
			this.date.setMonth(month);
			this.date.setFullYear(year);
			
			// set week-number
			var col = FWS_getElement('cal_col_' + y + '_0');
			col.innerHTML = week;
		
			// loop through the week-days
			for(var x = 1;x < 8;x++)
			{
				var col = FWS_getElement('cal_col_' + y + '_' + x);
				
				// adjust realDay for the days out of the current month
				if(weekDay != null || day > monthDays + 1)
					realDay++;
				else
					realDay = 1;
				
				// is it in the current month?
				if(day <= monthDays && (weekDay == null || weekDay == x - 1))
				{
					col.innerHTML = day;
					col.className = 'cal_valid';
					
					// set classes
					if(nyear == year && nmonth == month && nday == day)
						FWS_addClassName(col,'cal_today');
					if(syear == year && smonth == month && sday == day)
						FWS_addClassName(col,'cal_selected');
					
					// change css-classes on hover
					col.onmouseover = function()
					{
						FWS_addClassName(this,'cal_valid_hover');
					};
					
					col.onmouseout = function()
					{
						FWS_removeClassName(this,'cal_valid_hover');
					};
					
					// we want to select the date on click
					col.onclick = function()
					{
						FWS_hideElement(FWS_Calendar.id);
						FWS_Calendar.instance._onSelected(this.id);
					};
					
					weekDay = null;
				}
				else
				{
					col.onmouseover = null;
					col.onmouseout = null;
					col.onclick = null;
					col.innerHTML = realDay;
					col.className = 'cal_invalid';
				}
				
				if(weekDay == null)
					day++;
			}
		}
		
		// show date in the headline
		var headline = FWS_getElement('cal_headline');
		headline.innerHTML = FWS_Calendar.language['month_' + month] + ' ' + year;
	}
}

/**
 * Sets the minimum year that can be selected
 *
 * @param int year the new value
 */
function setMinYear(year)
{
	this.minYear = year;
}

/**
 * Sets the maximum year that can be selected
 *
 * @param int year the new value
 */
function setMaxYear(year)
{
	this.maxYear = year;
}

/**
 * Sets the given function for the startup-event. That means that as soon
 * as the calendar will be displayed this function will be called.
 * This may be usefull if you want to set the initial date from an input-field
 * or something like that.
 *
 * @param function func the callback-function
 */
function setStartUpFunction(func)
{
	this.onStartUp = func;
}

/**
 * Sets the selected date to the given one
 *
 * @param int year the year
 * @param int month the month (starting with 1)
 * @param int day the day (starting with 1)
 */
function setSelectedDate(year,month,day)
{
	year = year < this.minYear ? this.minYear : year;
	year = year > this.maxYear ? this.maxYear : year;

	this.date = new Date(year,month - 1,day,0,0,0);
	this.selectedDate = new Date();
	this.selectedDate.setTime(this.date.getTime());
	
	this.adjustButtons();
}

/**
 * Adjusts the buttons for the current date
 */
function adjustButtons()
{
	if(this.date.getMonth() == 11 && this.date.getFullYear() == this.maxYear)
		this.disableButton('fwscal_next_month');
	else
		this.enableButton('fwscal_next_month');
	
	if(this.date.getMonth() == 0 && this.date.getFullYear() == this.minYear)
		this.disableButton('fwscal_prev_month');
	else
		this.enableButton('fwscal_prev_month');
	
	if(this.date.getFullYear() == this.maxYear)
		this.disableButton('fwscal_next_year');
	else
		this.enableButton('fwscal_next_year');
	
	if(this.date.getFullYear() == this.minYear)
		this.disableButton('fwscal_prev_year');
	else
		this.enableButton('fwscal_prev_year');
}

/**
 * Displays the previous month
 */
function prevMonth()
{
	this.setPrevMonth();
	this.fill();
}

/**
 * Displays the next month
 */
function nextMonth()
{
	this.setNextMonth(true);
	this.fill();
}

/**
 * Displays the previous year
 */
function prevYear()
{
	if(this.date.getFullYear() > this.minYear)
	{
		this.date.setFullYear(this.date.getFullYear() - 1);
		this.adjustButtons();
		this.fill();
	}
}

/**
 * Displays the next year
 */
function nextYear()
{
	if(this.date.getFullYear() < this.maxYear)
	{
		this.date.setFullYear(this.date.getFullYear() + 1);
		this.adjustButtons();
		this.fill();
	}
}

/**
 * Enables the button with given id
 *
 * @param mixed id the id of the button
 */
function enableButton(id)
{
	var el = FWS_getElement(id);
	el.className = 'cal_button';
	el.onmouseover = function() {el.className = 'cal_button_hover'; };
	el.onmouseout = function() {el.className = 'cal_button'; };
}

/**
 * Disables the button with given id
 *
 * @param mixed id the id of the button
 */
function disableButton(id)
{
	var el = FWS_getElement(id);
	el.className = 'cal_button_disabled';
	el.onmouseover = null;
	el.onmouseout = null;
}

/**
 * Sets the CSS-file for the calendar
 *
 * @param string file the file
 */
function setCSSFile(file)
{
	this.cssFile = file;
}

/**
 * Displays the calendar relative to the element with given id
 *
 * @param mixed relId the id of the element to use for the positioning
 */
function display(relId)
{
	// do we have to create it?
	if(!FWS_getElement(FWS_Calendar.id))
	{
		var body = document.getElementsByTagName('body')[0];
		var element = document.createElement('div');
		element.id = FWS_Calendar.id;
		element.zindex = 100;
		element.className = 'calendar';
		element.innerHTML = this.getHTML();
		body.appendChild(element);
		
		if(this.cssFile)
		{
			var head = document.getElementsByTagName('head')[0];
			var element = document.createElement('link');
			element.rel = 'stylesheet';
			element.type = 'text/css';
			element.href = this.cssFile;
			head.appendChild(element);
		}
	}
	
	FWS_Calendar.instance = this;
	
	// call startup-function
	if(this.onStartUp != null)
		this.onStartUp();
	
	var cal = FWS_getElement(FWS_Calendar.id);
	var rel = FWS_getElement(relId);
	
	// we have to display it at first because otherwise offsetWidth is not set
	cal.style.top = '-600px';
	cal.style.position = 'absolute';
	cal.style.display = 'block';
	
	// check if the space on the right side of the relative-element is enough
	// for our calendar
	var windowWidth = FWS_getPageSize()[0];
	if(FWS_getPageOffsetLeft(rel) + cal.offsetWidth + rel.offsetWidth > windowWidth - 25)
		FWS_displayElement(FWS_Calendar.id,relId,'lt',2);
	else
		FWS_displayElement(FWS_Calendar.id,relId,'rt',2);
	
	// set the days of the selected month&year
	this.fill();
}

/**
 * The number of days per month
 */
Date._monthDays = new Array(31,28,31,30,31,30,31,31,30,31,30,31);

/**
 * An additional method for Date-objects. Returns the number of days in the
 * given month or the current one
 *
 * @param int month you can specify the month, if you like
 * @return the number of days in the month
 */
Date.prototype.getMonthDays = function(month)
{
	var year = this.getFullYear();
	if(typeof month == "undefined")
		month = this.getMonth();
	
	if(month == 1 && year % 4 == 0 && (year % 100 != 0 || year % 400 == 0))
		return 29;
	
	return Date._monthDays[month];
};

/**
 * An additional method for Date-objects. Returns the week-number
 *
 * @return the week-number
 */
Date.prototype.getWeekOfYear = function()
{
	var d = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
	var DoW = d.getDay();
	d.setDate(d.getDate() - (DoW + 6) % 7 + 3); // Nearest Thu
	var ms = d.valueOf(); // GMT
	d.setMonth(0);
	d.setDate(4); // Thu in Week 1
	return Math.round((ms - d.valueOf()) / (7 * 864e5)) + 1;
};

/**
 * An additional method for Date-objects. Returns the number of the first
 * weekday in the current month for germany (starts with monday)
 *
 * @return the weekday
 */
Date.prototype.getFirstGerWeekDayInMonth = function()
{
	var oldDay = this.getDate();
	this.setDate(1);

	var wd = this.getDay();
	var no;
	if(wd == 0)
		no = 6;
	else
		no = wd - 1;
	
	this.setDate(oldDay);
	return no;
};

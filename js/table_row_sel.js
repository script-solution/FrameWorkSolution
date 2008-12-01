/**
 * Contains the table-row-selector-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	js
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The constructor of the table-row-selector
 *
 * @param string rowIDPrefix the prefix of all rows-ids. The row-ids must have the
 * 		format <prefix><index>
 * @param string cbIDPrefix the prefix of all checkbox-ids. The cb-ids must have the
 * 		format <prefix><index>
 * @param string defClass the the default-row-class
 * @param string selClass the selection-row-class
 */
function FWS_TableRowSelector(rowIDPrefix,cbIDPrefix,defClass,selClass)
{
	this.rowIDPrefix = rowIDPrefix;
	this.cbIDPrefix = cbIDPrefix;
	this.disabledRows = new Array();
	this.selectedRows = new Array();
	this.lastHoverRow = -1;
	
	// init classes
	if(typeof defClass == 'undefined')
		this.defClass = 'hlCol';
	else
		this.defClass = defClass;
	
	if(typeof selClass == 'undefined')
		this.selClass = 'hlCol_sel';
	else
		this.selClass = selClass;

	/**
	 * Sets the style of all columns in the given row
	 *
	 * @param int index the index of the row
	 * @param object row the row-object
	 * @param string style the style-type: def or sel
	 */
	this.setRowStyle = function(index,row,style) {
		if(style != 'sel' && this.selectedRows[index])
			style = 'sel';
		
		for(var i = 0;i < row.childNodes.length;i++)
		{
			if(row.childNodes[i].nodeName.toLowerCase() == 'td')
			{
				if(row.childNodes[i].style)
					this.setColStyle(row.childNodes[i],style);
			}
		}
	};
	
	/**
	 * Sets the style of the given column
	 *
	 * @param object column the column-object
	 * @param string style the style-type: def, hl or sel
	 */
	this.setColStyle = function(column,style) {
		// just change the style if the classname is known
		if(column.className == this.defClass || column.className == this.selClass)
		{
			switch(style)
			{
				case 'def':
					column.className = this.defClass;
					break;
				
				case 'sel':
					column.className = this.selClass;
					break;
			}
		}
		
		// look if there are children
		for(var i = 0;i < column.childNodes.length;i++)
		{
			if(column.childNodes[i].hasChildNodes())
				this.setColStyle(column.childNodes[i],style);
		}
	};
	
	// the public functions
	this.toggleRowSelected = toggleRowSelected;
	this.toggleAllSelected = toggleAllSelected;
	this.setRowDisabled = setRowDisabled;
	this.setRowEnabled = setRowEnabled;
	this.toggleRows = toggleRows;
}

/**
 * Marks the given row as disabled. This rows will be skipped when inverting
 * the selection for example.
 *
 * @param int row the row-index
 */
function setRowDisabled(row)
{
	this.disabledRows[row] = true;
}

/**
 * Marks the given row as enabled (if it has been disabled).
 *
 * @param int row the row-index
 */
function setRowEnabled(row)
{
	this.disabledRows[row] = false;
}

/**
 * Toggles all rows with given indices
 *
 * @param string indices a comma-separated list of indices
 */
function toggleRows(indices)
{
	var aindices = indices.split(',');
	for(var i = 0;i < aindices.length;i++)
	{
		var index = parseInt(aindices[i]);
		if(index >= 0)
			this.toggleRowSelected(index);
	}
}

/**
 * Toggles the selection-status of the row with given index
 *
 * @param int index the row-index
 */
function toggleRowSelected(index)
{
	if(this.disabledRows[index])
		return;
	
	// do we have a checkbox?
	var cb = null;
	if(this.cbIDPrefix)
		cb = FWS_getElement(this.cbIDPrefix + index);
	
	var row = FWS_getElement(this.rowIDPrefix + index);
	
	// is the row already selected?
	if(this.selectedRows[index])
	{
		this.selectedRows[index] = false;
		this.setRowStyle(index,row,'def');
		if(this.cbIDPrefix)
			cb.checked = false;
	}
	else
	{
		this.selectedRows[index] = true;
		this.setRowStyle(index,row,'sel');
		if(this.cbIDPrefix)
			cb.checked = true;
	}
}

/**
 * Toggles the selection-status of all rows. This function assumes that
 * your row-ids have the format <prefix><index> and that <index> starts with 0!
 * As soon as no element with this id has been found the function aborts.
 */
function toggleAllSelected()
{
	for(var i = 0;;i++)
	{
		var row = FWS_getElement(this.rowIDPrefix + i);
		var enabled = !this.disabledRows[i];
		if(row && enabled)
			this.toggleRowSelected(i);
		else if(enabled)
			break;
	}
}
/**
 * Contains the javascript-table-sorter
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
 * Constructor of the table-sorter
 *
 * @param mixed tableID the id of the table to sort
 */
function FWS_TableSorter(tableID)
{
	this.tableID = tableID;
	this.table = FWS_getElement(tableID);
	this.lastColumn = -1;
	this.lastDir = 0;

	// store functions
	this.sortTable = sortTable;

	/**
	 * Compares the two given objects
	 *
	 * @param mixed a the first object
	 * @param mixed b the second object
	 * @return integer < 0 if a < b, > 0 if a > b, = 0 otherwise
	 */
	this._compare = function _compare(a,b)
	{
		// compare numeric value?
		if(Number(a) && Number(b))
		{
			var ia = parseInt(a);
			var ib = parseInt(b);
			return ia - ib;
		}

		// compare in lowercase!
		a = a.toLowerCase();
		b = b.toLowerCase();
		if(a > b)
			return 1;
		if(b > a)
			return -1;

	 	return 0;
	};

	/**
	 * Swaps the body-rows with given indices in our table
	 *
	 * @param array trs the table-rows
	 * @param int i the first row
	 * @param int j the second row
	 */
	this._swapRows = function _swapRows(trs,i,j)
	{
		if(i == j + 1)
			this.table.tBodies[0].insertBefore(trs[i],trs[j]);
	 	else if(j == i + 1)
			this.table.tBodies[0].insertBefore(trs[j],trs[i]);
	 	else
	 	{
			var tmpNode = this.table.tBodies[0].replaceChild(trs[i],trs[j]);
			if(typeof(trs[i]) != 'undefined')
				this.table.tBodies[0].insertBefore(tmpNode,trs[i]);
			else
				this.table.appendChild(tmpNode);
	 	}
	};

	/**
	 * A quicksort implementation to sort the table-rows
	 *
	 * @param array A the table-rows
	 * @param int low the first element (0)
	 * @param int high the last element (A.length - 1)
	 * @param int columnIndex the columnIndex to sort by
	 * @param int cmpDir the direction: 0 = asc, 1 = desc
	 */
	this._qSort = function _qSort(A,low,high,columnIndex,cmpDir)
	{
		var lo = low;
		var hi = high;
		var mid = A[Math.round((lo + hi) / 2)].cells[columnIndex].innerHTML;
		do
		{
			var cmpContent = A[lo].cells[columnIndex].innerHTML;
			var cmp = this._compare(mid,cmpContent);
			while((cmpDir == 0 && cmp > 0) || (cmpDir == 1 && cmp < 0))
			{
				lo++;
				cmpContent = A[lo].cells[columnIndex].innerHTML;
				cmp = this._compare(mid,cmpContent);
			}

			var cmpContent = A[hi].cells[columnIndex].innerHTML;
			var cmp = this._compare(mid,cmpContent);
			while((cmpDir == 0 && cmp < 0) || (cmpDir == 1 && cmp > 0))
			{
				hi--;
				cmpContent = A[hi].cells[columnIndex].innerHTML;
				cmp = this._compare(mid,cmpContent);
			}

			if(lo <= hi)
			{
				this._swapRows(A,lo,hi); 
				lo++;
				hi--; 
			}
		}
		while(lo <= hi);

		if(hi > low)
			this._qSort(A,low,hi,columnIndex,cmpDir);    
		if(lo < high)
			this._qSort(A,lo,high,columnIndex,cmpDir);
	};
}

/**
 * Sorts the table by given column. This method will also toggle the
 * direction.
 *
 * @param int columnIndex the columnIndex to sort by
 */
function sortTable(columnIndex)
{
	var trs = this.table.tBodies[0].getElementsByTagName('tr');

	// determine direction
	var cmpDir;
	if(this.lastColumn == columnIndex)
		cmpDir = this.lastDir == 0 ? 1 : 0;
	else
		cmpDir = 0;

	// sort the rows via quicksort
	this._qSort(trs,0,trs.length - 1,columnIndex,cmpDir);

	// store column and direction
	this.lastDir = cmpDir;
	this.lastColumn = columnIndex;
}
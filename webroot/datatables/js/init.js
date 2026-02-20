(function($) {

$.fn.dataTableExt.oApi.fnSetPaging = function ( oSettings, iLength, bRedraw) {
    // By default we want to redraw the table.
    if (typeof bRedraw == "undefined") {
        bRedraw = true;
    }

    // Set the new length
    oSettings._iDisplayLength = parseInt(iLength, 10);

    if (bRedraw) {
        this.fnDraw();
    }
};

/*
 * Function: fnGetColumnData
 * Purpose:  Return an array of table values from a particular column.
 * Returns:  array string: 1d data array 
 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
 *           int:iColumn - the id of the column to extract the data from
 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
 */
$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
    // check that we have a column id
    if ( typeof iColumn == "undefined" ) return new Array();
     
    // by default we only wany unique data
    if ( typeof bUnique == "undefined" ) bUnique = true;
     
    // by default we do want to only look at filtered data
    if ( typeof bFiltered == "undefined" ) bFiltered = true;
     
    // by default we do not wany to include empty values
    if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
     
    // list of rows which we're going to loop through
    var aiRows;
     
    // use only filtered rows
    if (bFiltered == true) aiRows = oSettings.aiDisplay; 
    // use all rows
    else aiRows = oSettings.aiDisplayMaster; // all row numbers
 
    // set up data array    
    var asResultData = new Array();
     
    for (var i=0,c=aiRows.length; i<c; i++) {
        iRow = aiRows[i];
        var aData = this.fnGetData(iRow);
        var sValue = aData[iColumn];
         
        // ignore empty values?
        if (bIgnoreEmpty == true && sValue.length == 0) continue;
 
        // ignore unique values?
        else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
         
        // else push the value onto the result data array
        else asResultData.push(sValue);
    }
     
    return asResultData;
}}(jQuery));

function fnShowHide(iCol) {
    /* Get the DataTables object again - this is not a recreation, just a get of the object */
    var oTable = $('.data-table').dataTable();
    var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
    oTable.fnSetColumnVis(iCol, bVis ? false : true);
}

function fnCreateSelect( aData, name )
{
    var r='<select><option value="All">' + name + '</option>';
    var i, iLen=aData.length;
    for ( i=0 ; i<iLen ; i++ )
    {
        if (aData[i].indexOf("/") >= 0) {
            continue;
        }
        r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
    }
    return r+'</select>';
}

$(document).ready(function() {
    /* Initialise the DataTable */
    var oTable = $('.data-table').dataTable( {
        "oLanguage": {
            "sSearch": "Search all columns:",
                "sLengthMenu": 'Show <select>'+
                 '<option value="5">5</option>'+
                 '<option value="10">10</option>'+
                 '<option value="25">25</option>'+
                 '<option value="50">50</option>'+
                 '<option value="100">100</option>'+
                 '<option value="200">200</option>'+
                 '<option value="-1">All</option>'+
                 '</select> entries'
        },
        'iDisplayLength' : 25,
        'sPaginationType' : 'full_numbers',
        'aaSorting': []
    } );
    
    //allow for sorting of toggle buttons based on their link src
    $.fn.dataTableExt.afnSortData['dom-toggle'] = function (oSettings, iColumn) {
      return $.map( oSettings.oApi._fnGetTrNodes(oSettings), function (tr, i) {
        return ($('td:eq('+iColumn+') img', tr).attr('src') == "/img/icons/on.png") ? '1' : '0';
      });
    };
    
    oTable.find('th[data-ssortdatatype]').each(function(){
      var index = $(this).index();
      var tableSettings = oTable.fnSettings();
      tableSettings.aoColumns[index].sSortDataType = $(this).data('ssortdatatype');
      //rebuild the table with the sort type
      oTable.fnDestroy();
      oTable = oTable.dataTable(tableSettings);
    });
    
    //populate the select boxes by the contents of the column allowing for columns to have multiple values separated by ',' or ';'
    $('.filter-select').each( function (){
        var select = $(this);
        var selectName = select.text();
        //allow for spaces between the separators and the values
        var splitter = select.data("separator") || / ?[,;] ?/;
        //if the splitter isn't a regex object then convert it into one
        if ( typeof splitter == 'string' && splitter.indexOf("/") == 0 && splitter.lastIndexOf("/") == splitter.length - 1 ) {
          splitter = new RegExp(splitter.substr(1, splitter.length-2));
        }
        var rawData = oTable.fnGetColumnData(select.index());
        var splitData = [];
        var i;
        for (i=0; i<Object.keys(rawData).length; i++){
          var split = rawData[i].split(splitter);
          var z;
          for (z=0; z<Object.keys(split).length; z++){
            splitData.push(split[z]);
          }
        }
        //make sure that we only populate the select box with unique options
        var uniqueData = [];
        for (i=0; i<Object.keys(splitData).length; i++) {
          if (uniqueData.indexOf(splitData[i]) == -1) {
            uniqueData.push(splitData[i]);
          }
        }
        this.innerHTML = fnCreateSelect(uniqueData.sort(), selectName);
        
        //simple filtering on change
        $( 'select' , this).change( function () {
          if( $(this).val() != "All" ) {
             oTable.fnFilter($(this).val(), select.index() , true, true );
          } else {
            oTable.fnFilter( '' , select.index() );
          }
        });
        
        //prevent the sort order from being changed when the user clicks the select box
        $( 'select' , this).click(function (e){
          e.stopPropagation();
        });
    });

});
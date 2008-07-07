/**
 * Performs a HTML form input autocompletion
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: autocomplete.js,v 1.1 2008/07/07 18:01:11 lsces Exp $
 * @author http://wact.sourceforge.net/examples/tags/form/inputautocomplete.php
 */
<!--
function wactjavascript_autoComplete (dataArray, input, evt) {
  if (input.value.length == 0) {
    return;
  }
  //allow backspace to work in IE
  if (typeof input.selectionStart == 'undefined' && evt.keyCode == 8) { input.value = input.value.substr(0,input.value.length-1); }
  var match = false;
  for (var i = 0; i < dataArray.length; i++) {
    if ((match = dataArray[i].toLowerCase().indexOf(input.value.toLowerCase()) == 0)) {
      break;
    }
  }
  if (match) {
    var typedText = input.value;
    if (typeof input.selectionStart != 'undefined') {
      switch (evt.keyCode) {
       case 37: //left arrow
       case 39: //right arrow
       case 33: //page up
       case 34: //page down
       case 36: //home
       case 35: //end
       case 13: //enter
       case 9: //tab
       case 27: //esc
       case 16: //shift
       case 17: //ctrl
       case 18: //alt
       case 20: //caps lock
       case 8: //backspace
       case 46: //delete
        return;
       case 38: //up arrow
       	if (i > 0) { input.value = dataArray[i-1]; }
       	return;
       case 40: //down arrow
       	if (i < dataArray.length - 1) { input.value = dataArray[i+1]; }
       	return;
       break;
      }
      input.value = dataArray[i];
      input.setSelectionRange(typedText.length, input.value.length);
    }
    else if (input.createTextRange) {
      if (evt.keyCode == 16) {
        return;
      }
      if (evt.keyCode == 38) {
      	if (i > 0) { input.value = dataArray[i-1]; return; }
      }
      if (evt.keyCode == 40) {
      	if (i < dataArray.length - 1) { input.value = dataArray[i+1]; return; }
      }
      input.value = dataArray[i];
      var range = input.createTextRange();
      range.moveStart('character', typedText.length);
      range.moveEnd('character', input.value.length);
      range.select();
    }
    else {
      if (confirm("Are you looking for '" + dataArray[i] + "'?")) {
        input.value = dataArray[i];
      }
    }
  }
}
//-->

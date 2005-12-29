/**
 * Common strings functions
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
 * @version $Id: strings.js,v 1.2 2006/10/01 22:44:03 lsces Exp $
 */
	function trim(str) {
		return str.replace(/(^\s*)|(\s*$)/g,'');
	}
	function strclean(s) {
		s=s.replace(/[\u00E0-\u00E5]/g,'a');
		s=s.replace(/[\u00E6-\u00E6]/g,'ae');
		s=s.replace(/[\u00E7]/g,'c');
		s=s.replace(/[\u00E8-\u00EB]/g,'e');
		s=s.replace(/[\u00EC-\u00EF]/g,'i');
		s=s.replace(/[\u00F1]/g,'n');
		s=s.replace(/[\u00F2-\u00F6]/g,'o');
		s=s.replace(/[\u00F8]/g,'o');
		s=s.replace(/[\u0153]/g,'oe');
		s=s.replace(/[\u00F9-\u00FC]/g,'u');
		s=s.replace(/[\u00FD\u00FF]/g,'y');
		s=s.replace(/[\u00C0-\u00C5]/g,'A');
		s=s.replace(/[\u00C6]/g,'AE');
		s=s.replace(/[\u00C7]/g,'C');
		s=s.replace(/[\u00C8-\u00CB]/g,'E');
		s=s.replace(/[\u00CC-\u00CF]/g,'I');
		s=s.replace(/[\u00D1]/g,'N');
		s=s.replace(/[\u00D2-\u00D6]/g,'O');
		s=s.replace(/[\u00D8]/g,'O');
		s=s.replace(/[\u0152]/g,'OE');
		s=s.replace(/[\u00D9-\u00DC]/g,'U');
		s=s.replace(/[\u00DD]/g,'Y');
		s=s.replace(/[\s\']/g,'-');
		return s;
	}

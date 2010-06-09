<?php
/**
 * String handling functions for strings optionally containing UTF-8 characters.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_UTF8_PHP', '');

/*
 * Expand the input string into an array of UTF-8 characters
 */
function UTF8_str_split($text, $splitLen=1) {
	if (is_array($text)) return $text;		// No action:  input has already been expanded
	if (is_int($text)) return array(UTF8_chr($text));		// Integer: Convert to UTF8 character
	$result = array();
	if ($text=='' || $splitLen<1) return $result;

	$charPos = 0;
	$textLen = strlen($text);

	while ($charPos<$textLen) {
		$UTF8_string = '';
		for ($i=0; ($i<$splitLen && $charPos<$textLen); $i++) {
			$charLen = 1;
			$letter = substr($text, $charPos, 1);
			if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
			if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
			if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence
			$UTF8_string .= substr($text, $charPos, $charLen);
			$charPos += $charLen;
		}
		$result[] = $UTF8_string;
	}

	return $result;
}


/*
 * Get the length of the input UTF8-encoded string
 */
function UTF8_strlen($text) {
	$UTF8_text = UTF8_str_split($text);

	return count($UTF8_text);
}


/*
 * Get the ordinal value of the input UTF8-encoded character
 */
function UTF8_ord($char) {
	$charLen = strlen($char);

	switch ($charLen) {
	case 1:
		$value = ord($char);
		break;
	case 2:
		$value = ((ord(substr($char,0,1)) & 0x1F) << 6) + (ord(substr($char,1,1)) & 0x3F);
		break;
	case 3:
		$value = ((ord(substr($char,0,1)) & 0x0F) << 12) + ((ord(substr($char,1,1)) & 0x3F) << 6) + (ord(substr($char,1,2)) & 0x3F);
		break;
	case 4:
		$value = ((ord(substr($char,0,1)) & 0x07) << 18) + ((ord(substr($char,1,1)) & 0x3F) << 12) + ((ord(substr($char,1,2)) & 0x3F) << 6) + (ord(substr($char,1,3)) & 0x3F);
		break;
	default:
		$value = 0;
	}

	return $value;
}


/*
 * Get the UTF-8 encoded character from the input ordinal value
 */
function UTF8_chr($value) {
	$value = intval($value);		// make sure we're dealing with an integer
	if ($value<=0x7F) return chr($value);		// values from \x00 to \x7F are returned as single letters

	$value = $value & 0x1FFFFF;		// make sure we don't exceed the range covered by 4-byte sequences

	$mask1 = 0x80;
	$char = array();

	// Split the input integer into 6-bit chunks.
	// Emit each chunk except the first as an 8-bit byte with the high-order bit set.
	while ($value>0x3F) {
		$char[] = chr(0x80 | ($value & 0x3F));
		$value = $value >> 6;
		$mask1 = 0x80 | ($mask1 >> 1);		// Keep track of how often we did this
	}

	// Emit the first byte with the high-order bits indicating the length of the whole thing:
	// 0		1 byte
	// 10		not valid as first byte
	// 110		2 bytes
	// 1110		3 bytes
	// 11110	4 bytes
	$char[] = chr($value | $mask1);

	return implode('', array_reverse($char));
}


/*
 * Extract substring from input string
 */
function UTF8_substr($text, $start=0, $len=null) {
	$UTF8_text=UTF8_str_split($text);
	$textLen=count($UTF8_text);
	if ($textLen==0) {
		return $text;
	}

	if ($start>$textLen || $start<0 && -$start>$textLen) {
		return false;
	}
	if ($start<0) {
		$start=$textLen+$start;
	}
	if (is_null($len)) {
		$len=$textLen;
	} elseif ($len<0) {
		if ($start>$textLen+$len) {
			$len=0;
		}
	} elseif ($len>0) {
		if ($start+$len>$textLen) {
			$len=$textLen-$start;
		}
	}
 	$result=array_slice($UTF8_text, $start, $len);
	if (is_array($text)) {
		return $result;
	} else {
		return implode('', $result);
	}
}


/*
 * Pad string
 */
function UTF8_str_pad($text, $outLen, $pad='', $padType=STR_PAD_RIGHT) {
	$UTF8_text = UTF8_str_split($text);
	$textLen = count($UTF8_text);
	if ($textLen>=$outLen) return $text;
	$UTF8_pad = UTF8_str_split($pad);
	$padLen = count($UTF8_pad);

	switch ($padType) {
	case STR_PAD_BOTH:
		$textLeftLen = ($outLen - $textLen) >> 1;
		$textRightLen = $outLen - $textLeftLen;
		break;
	case STR_PAD_LEFT:
		$textLeftLen = $outLen - $textLen;
		$textRightLen = 0;
		break;
	case STR_PAD_RIGHT:
	default:
		$textLeftLen = 0;
		$textRightLen = $outLen - $textLen;
		break;
	}

	$UTF8_textLeft = array();
	for ($i=0; $i<$textLeftLen; $i+=$padLen) {
		for ($j=0; $j<$padLen; $j++) {
			$UTF8_textLeft[] = $UTF8_pad[$j];
		}
	}
	if (count($UTF8_textLeft)>$textLeftLen) $UTF8_textLeft = array_slice($UTF8_textLeft, (count($UTF8_textLeft)-$textLeftLen));

	$UTF8_textRight = array();
	for ($i=0; $i<$textRightLen; $i+=$padLen) {
		for ($j=0; $j<$padLen; $j++) {
			$UTF8_textRight[] = $UTF8_pad[$j];
		}
	}
	if (count($UTF8_textRight)>$textRightLen) $UTF8_textRight = array_slice($UTF8_textRight, 0, $textRightLen);

	$result = $UTF8_textleft + $UTF8_text + $UTF8_textRight;
	$result = array_slice($result, 0, $outLen);

	if (is_array($text)) return $result;
	else return implode('', $result);
}


/*
 * Convert input string to upper case
 */
function UTF8_strtoupper($text) {
	global $UTF8_LC_letters;
	$UTF8_text = UTF8_str_split($text);
	$textLen = count($UTF8_text);
	if ($textLen==0) return $text;

	$result = array();
	foreach ($UTF8_text as $letter) {
		if (isset($UTF8_LC_letters[$letter])) $result[] = $UTF8_LC_letters[$letter];
		else $result[] = $letter;		// No translation when no matching upper case letter exists
	}

	if (is_array($text)) return $result;
	else return implode('', $result);
}


/*
 * Convert input string to lower case
 */
function UTF8_strtolower($text) {
	global $UTF8_LC_letters;
	$UTF8_text = UTF8_str_split($text);
	$textLen = count($UTF8_text);
	if ($textLen==0) return $text;

	$result = array();
	$UTF8_UC_letters = array_flip($UTF8_LC_letters);
	foreach ($UTF8_text as $letter) {
		if (isset($UTF8_UC_letters[$letter])) $result[] = $UTF8_UC_letters[$letter];
		else $result[] = $letter;		// No translation when no matching lower case letter exists
	}

	if (is_array($text)) return $result;
	else return implode('', $result);
}


/*
 * Case sensitive search for the first occurrence of a string within in another string
 */
function UTF8_strstr($haystack, $needle) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$haystackLen = count($UTF8_haystack);
	if (!is_string($needle) && !is_array($needle)) $UTF8_needle = array(UTF8_chr(intval($needle)));
	else $UTF8_needle = UTF8_str_split($needle);
	$needleLen = count($UTF8_needle);
	if ($haystackLen==0 || $needleLen==0) return false;

	$stringPos = UTF8_strpos($UTF8_haystack, $UTF8_needle, 0);
	if ($stringPos===false) return false;

	$result = array_slice($UTF8_haystack, $stringPos);

	if (is_array($haystack)) return $result;
	else return implode('', $result);
}


/*
 * Case insensitive search for the first occurrence of a string within in another string
 */
function UTF8_stristr($haystack, $needle, $offset=0) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$haystackLen = count($UTF8_haystack);
	if (!is_string($needle) && !is_array($needle)) $UTF8_needle = array(UTF8_chr(intval($needle)));
	else $UTF8_needle = UTF8_str_split($needle);
	$needleLen = count($UTF8_needle);
	if ($haystackLen==0 || $needleLen==0) return false;

	$stringPos = UTF8_strpos(UTF8_strtoupper($UTF8_haystack), UTF8_strtoupper($UTF8_needle, 0));
	if ($stringPos===false) return false;

	$result = array_slice($UTF8_haystack, $stringPos);

	if (is_array($haystack)) return $result;
	else return implode('', $result);
}


/*
 * Case sensitive search for a string to be contained in another string
 */
function UTF8_strpos($haystack, $needle, $offset=0) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$haystackLen = count($UTF8_haystack);
	if (!is_string($needle) && !is_array($needle)) $UTF8_needle = array(UTF8_chr(intval($needle)));
	else $UTF8_needle = UTF8_str_split($needle);
	$needleLen = count($UTF8_needle);
	if ($offset<0) $offset += $haystackLen;
	if ($haystackLen==0 || $needleLen==0 || $offset<0) return false;

	$lastPos = $haystackLen - $needleLen;

	$found = false;
	for ($currPos=$offset; $currPos<=$lastPos; $currPos++) {
		$found = true;
		for ($i=0; $i<$needleLen; $i++) {
			if ($UTF8_haystack[$currPos+$i]!=$UTF8_needle[$i]) {
				$found = false;
				break;
			}
		}
		if ($found) break;
	}

	if ($found) return $currPos;
	else return false;
}


/*
 * Case insensitive search for a string to be contained in another string
 */
function UTF8_stripos($haystack, $needle, $offset=0) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$UTF8_needle = UTF8_str_split($needle);

	return UTF8_strpos(UTF8_strtoupper($UTF8_haystack), UTF8_strtoupper($UTF8_needle, $offset));
}


/*
 * Case sensitive reverse search for a string to be contained in another string
 */
function UTF8_strrpos($haystack, $needle, $offset=0) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$haystackLen = count($UTF8_haystack);
	if (!is_string($needle) && !is_array($needle)) $UTF8_needle = array(UTF8_chr(intval($needle)));
	else $UTF8_needle = UTF8_str_split($needle);
	$needleLen = count($UTF8_needle);
	if ($offset<=0) $offset += $haystackLen;
	if ($haystackLen==0 || $needleLen==0 || $offset<0) return false;

	$lastPos = $offset - $needleLen - 1;
	if ($lastPos>($haystackLen-$needleLen)) return false;

	for ($currPos=$lastPos; $currPos>=0; $currPos--) {
		$found = true;
		for ($i=0; $i<$needleLen; $i++) {
			if ($UTF8_haystack[$currPos+$i]!=$UTF8_needle[$i]) {
				$found = false;
				break;
			}
		}
		if ($found) break;
	}

	if ($found) return $currPos;
	else return false;
}


/*
 * Case insensitive reverse search for a string to be contained in another string
 */
function UTF8_strripos($haystack, $needle, $offset=0) {
	$UTF8_haystack = UTF8_str_split($haystack);
	$UTF8_needle = UTF8_str_split($needle);

	return UTF8_strrpos(UTF8_strtoupper($UTF8_haystack), UTF8_strtoupper($UTF8_needle, $offset));
}


/*
 * Case sensitive comparison of two strings
 */
function UTF8_strcmp($text1, $text2) {
	$UTF8_text1 = UTF8_str_split($text1);
	$text1Len = count($UTF8_text1);
	$UTF8_text2 = UTF8_str_split($text2);
	$text2Len = count($UTF8_text2);

	$minLen = min($UTF8_text1, $UTF8_text2);

	for ($i=0; $i<$minLen; $i++) {
		$UTF8_ord1 = UTF8_ord($UTF8_text1[$i]);
		$UTF8_ord2 = UTF8_ord($UTF8_text2[$i]);
		if ($UTF8_ord1<$UTF8_ord2) return -1;
		if ($UTF8_ord1>$UTF8_ord2) return 1;
	}

	return $text1Len - $text2Len;
}


/*
 * Case sensitive comparison of two strings, max length specifiable
 */
function UTF8_strncmp($text1, $text2, $maxLen=0) {
	$UTF8_text1 = UTF8_str_split($text1);
	$UTF8_text2 = UTF8_str_split($text2);
	if ($maxLen>0) {
		$UTF8_text1 = array_slice($UTF8_text1, 0, $maxLen);
		$UTF8_text2 = array_slice($UTF8_text2, 0, $maxLen);
	}

	return UTF8_strcmp($UTF8_text1, $UTF8_text2);
}


/*
 * Case insensitive comparison of two strings
 */
function UTF8_strcasecmp($text1, $text2) {
	$UTF8_text1 = UTF8_str_split($text1);
	$UTF8_text2 = UTF8_str_split($text2);

	return UTF8_strcmp(UTF8_strtoupper($UTF8_text1), UTF8_strtoupper($UTF8_text2));
}


/*
 * Case insensitive comparison of two strings, max length specifiable
 */
function UTF8_strncasecmp($text1, $text2, $maxLen=0) {
	$UTF8_text1 = UTF8_str_split($text1);
	$UTF8_text2 = UTF8_str_split($text2);
	if ($maxLen>0) {
		$UTF8_text1 = array_slice($UTF8_text1, 0, $maxLen);
		$UTF8_text2 = array_slice($UTF8_text2, 0, $maxLen);
	}

	return UTF8_strcmp(UTF8_strtoupper($UTF8_text1), UTF8_strtoupper($UTF8_text2));
}


/*
 * Word wrap
 */
function UTF8_wordwrap($text, $width=75, $break="\n", $cut=FALSE) {
	if ($width<=0) $width = 75;
	if (is_string($text) && strlen($text)<=$width) return $text;	// Nothing to do
	if ($break=='') $break = "\n";

	$UTF8_text = UTF8_str_split($text);
	$UTF8_break = UTF8_str_split($break);
	$UTF8_result = array();

	while (UTF8_strlen($UTF8_text)>$width) {
		$longWord = FALSE;
		for ($i=$width; $i>=0; $i--) {
			if ($UTF8_text[$i]==' ') break;
		}
		if ($i<0) {
			// We're dealing with a very long word
			// Not too sure what $cut is supposed to accomplish -- we'll just chop the word
			$longWord = TRUE;	// This means: "not wrapping at a space"
			$i = $width;
		}

		$thisPiece = array_slice($UTF8_text, 0, $i);
		foreach ($thisPiece as $char) {
			// Copy front part of input string
			$UTF8_result[] = $char;
		}
		foreach ($UTF8_break as $char) {
			// Copy separator string
			$UTF8_result[] = $char;
		}

		if (!$longWord) $i++;		// Skip space at end of piece we've just worked on
		$UTF8_text = UTF8_substr($UTF8_text, $i);		// Remove that piece
	}

	foreach ($UTF8_text as $char) {
		// Copy remainder of input string
		$UTF8_result[] = $char;
	}

	if (is_array($text)) return $UTF8_result;
	return implode('', $UTF8_result);
}
?>

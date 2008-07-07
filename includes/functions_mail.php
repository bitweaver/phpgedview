<?php
/**
 * Mail specific functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * this function is a wrapper to the php mail() function so that we can change settings globally
 * for more info on format="flowed" see: http://www.joeclark.org/ffaq.html
 * for deatiled info on MIME (RFC 1521) email see: http://www.freesoft.org/CIE/RFC/1521/index.htm
 */
function pgvMail($to, $from, $subject, $message) {
	global $pgv_lang, $CHARACTER_SET, $LANGUAGE, $PGV_STORE_MESSAGES, $TEXT_DIRECTION;
	$mailFormat = "plain";
	//$mailFormat = "html";
	//$mailFormat = "multipart"

	$mailFormatText = "text/plain";

	$boundry = "PGV-123454321-PGV"; //unique identifier for multipart
	$boundry2 = "PGV-123454321-PGV2";

	if ($TEXT_DIRECTION == "rtl") { // needed for rtl but we can change this to a global config
		$mailFormat = "html";
	}

	if ($mailFormat == "html") {
		$mailFormatText = "text/html";
	} else if ($mailFormat == "multipart") {
		$mailFormatText = "multipart/related; \r\n\tboundary=\"$boundry\""; //for double display use:multipart/mixed
	} else {
		$mailFormatText = "text/plain";
	}

	$extraHeaders = "From: $from\r\nContent-type: $mailFormatText;";

	if ($mailFormat != "multipart") {
		$extraHeaders .= "\tcharset=\"$CHARACTER_SET\";\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit";
	}

	if ($mailFormat == "html" || $mailFormat == "multipart") {
		$extraHeaders .= "\r\nMime-Version: 1.0";
	}

	//-- doesn't this line just concatenate onto itself?
	//$extraHeaders .= $extraHeaders; //add custom extra header
	$extraHeaders .= "\r\n";


	if ($mailFormat == "html") {
		//wrap message in html
		$htmlMessage = "";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre></body>";
		$htmlMessage .= "</html>";
		$message = $htmlMessage;
	} else if ($mailFormat == "multipart") {
		//wrap message in html
		$htmlMessage = "--$boundry\r\n";
		$htmlMessage .= "Content-Type: multipart/alternative; \r\n\tboundry=--$boundry2\r\n\r\n";
		$htmlMessage = "--$boundry2\r\n";
		$htmlMessage .= "Content-Type: text/plain; \r\n\tcharset=\"$CHARACTER_SET\";\r\n\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
		$htmlMessage .= $message;
		$htmlMessage .= "\r\n\r\n--$boundry2\r\n";
		$htmlMessage .= "Content-Type: text/html; \r\n\tcharset=\"$CHARACTER_SET\";\r\n\tformat=\"flowed\"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
		$htmlMessage .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlMessage .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		$htmlMessage .= "<head>";
		$htmlMessage .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		$htmlMessage .= "</head>";
		$htmlMessage .= "<body dir=\"$TEXT_DIRECTION\"><pre>";
		$htmlMessage .= $message; //add message
		$htmlMessage .= "</pre>";
		$htmlMessage .= "<img src=\"cid:pgvlogo@pgvserver\" alt=\"\" style=\"border: 0px; display: block; margin-left: auto; margin-right: auto;\" />";
		$htmlMessage .= "</body>";
		$htmlMessage .= "</html>";
		$htmlMessage .= "\r\n--$boundry2--\r\n";
		$htmlMessage .= "\r\n--$boundry\r\n";
		$htmlMessage .= getPgvMailLogo();
		$htmlMessage .= "\r\n\r\n\r\n\r\n--$boundry--";
		$message = $htmlMessage;
	}
	mail($to, hex4email($subject,$CHARACTER_SET), $message, $extraHeaders);
}

function getPgvMailLogo() {
// the following is a base64 encoded PGV logo for use in html formatted email.
	$pgvLogo =
"Content-Type: image/gif;
	name=\"gedview.gif\"
Content-Transfer-Encoding: base64
Content-ID: <pgvlogo@pgvserver>
Content-Description: gedview.gif
Content-Location: gedview.gif

R0lGODlhZAAtAPcAAAMZLwUmRRIsRBo0SwctURU2UyY+UxE8ZTJHWx5GagxFehVKeyZIaC1NazdT
bSVTfT5Zc0dYaFNjc25ubmBtemRyf3V1dXt7extRgx9SgShbijdegjRkkD1rlERmhk1vj1NqgFl0
jkJwml99mWl6iUxhrFZpr0t2oFF7oVR+pV1utGx6q2N1vG18wHmJmFeApmaMr2uLqHWArHaSq32I
sHuatn+gvXiFwoCAgIODg4WEhYaEhYmGh4eIiImJiYyMjIKPmpGRkZeXl5qbm4aNroiXpJGdqYiP
tImfsouUuJacu5WhrJmksp2os5Knup+lvpWpvKChoaenp6Onraqrq6Cst6ast6qyu7Gys7O2urW4
vLu8vL6/v4ONx42WzJae05asxp2yxp+2zJyj1KWsw6W3x6yywq+2wa2zx6u4xKK6z6q6y7S6wbi9
w7K2yLS7ybm8y6Sr2Kq/06qx3Lu/0ru/3bC34rzAxbXBzb3Cy7LF1rnF0L3J1bTH2bfJ2rzM27/P
38LCw8LFysTGz8fIysvMzcXL0szO0sLO2cTS38jR2c3X39DR09PW2dTY3dnZ2t3e38HE48jL58HR
4MbU4cnW48rX5MvZ5szY5M3Z5c7Z5c3a5s/a5s/b58zQ7sra6Mzb6c7d6tLa4dfa4NPc4NTc4dDa
5NDc59Td5dTV7NLd6NPe6dDe69rc79TX8dbg5t7g4tbg6tPg7Nbh7dji69zi6N/l6tnj7Nvk7Nvk
7dvm793m7d3l7t/n7t/o79/h893o8uLk5eTm6eDn7ebo6eHo7+Tq7ebr7ejp6evs7OLp8OPr8uTr
8ebs8uDq9OLt9ubu9err8+nt8Ovv8+3u8O7v8Onu9O3t9uLk+Orr+uvw9Ovx9uzx9ezy9u7y9u/z
9+3z+e/1+vHy8vDy9PDz9fL09vX29vH0+PL1+PL2+fH2+vP3+/T3+fDw/PH2/PT0/PX4+vb5+/P5
/Pj5+vv7+/n6/Pz7/Pr8/fv+//z8/Pz9/v7+/v7+//7//////wAAACH5BAkAAP8ALAAAAABkAC0A
AAj+AP8JFLglCA8dOxIqVMjDh8OFECNKnEjxx4+FPi5S3KhQB48gWwaKhBXokTl/KFOq9GdOnLiT
K2PKnEkTpT5kyVSKC0avps+U5hoFEvnvUaCe/vYpXcrUHxYcOXJEQZYUpVKr+5Jm1Yqy39WUX7Ve
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";

return $pgvLogo;
}

/**
 * hex encode a string
 *
 * this function encodes a string in quoted_printable format
 * found at http://us3.php.net/bin2hex
 */
function hex4email ($string,$charset) {
	global $LANGUAGE;

	//-- check if the string has extended characters in it
	$str = utf8_decode($string);
	//-- if the strings are the same no conversion is necessary
	if ($str==$string) return $string;
	//-- convert to string into quoted_printable format
	$string = bin2hex ($string);
	$encoded = chunk_split($string, 2, '=');
	$encoded = preg_replace ("/=$/","",$encoded);
	$string = "=?$charset?Q?=" . $encoded . "?=";
	return $string;
}


function RFC2047Encode($string, $charset) {
	if (preg_match('/[^a-z ]/i', $string)) {
		$string = preg_replace('/([^a-z ])/ie', 'sprintf("=%02x", ord(StripSlashes("\\1")))', $string);
		$string = str_replace(' ', '_', $string);
		return "=?$charset?Q?$string?=";
	}
}

?>

	print "<tr><td class=\"topbottombar $TEXT_DIRECTION\" colspan=\"2\">";
	print "<a href=\"javascript: {tr}Validate GEDCOM{/tr}\" onclick=\"expand_layer('validate_gedcom');return false\"><img id=\"validate_gedcom_img\" src=\"".$PGV_IMAGE_DIR."/";
	if ($startimport != "true") print $PGV_IMAGES["minus"]["other"];
	else print $PGV_IMAGES["plus"]["other"];
	print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	print_help_link("validate_gedcom_help", "qm","validate_gedcom");
	print "&nbsp;<a href=\"javascript: {tr}Validate GEDCOM{/tr}\" onclick=\"expand_layer('validate_gedcom');return false\">".$pgv_lang["validate_gedcom"]."</a>";
	print "</td></tr>";
	print "<tr><td class=\"optionbox\">";
	print "<div id=\"validate_gedcom\" style=\"display: ";
	if ($startimport != "true") print "block ";
	else print "none ";
	print "\">";
		print "<table class=\"facts_table\">";
		print "<tr><td class=\"descriptionbox\" colspan=\"2\">Performing GEDCOM validation...<br />";
		if (!empty($error)) print "<span class=\"error\">$error</span>\n";
		
		if ($import != true && $skip_cleanup != $pgv_lang["skip_cleanup"]) {
			require_once("includes/functions_tools.php");
			if ($override == "yes") {
				copy($bakfile, $GEDCOMS[$GEDFILENAME]["path"]);
				if (file_exists($bakfile)) unlink($bakfile);
				$bakfile = false;
			}
			$l_headcleanup = false;
			$l_macfilecleanup = false;
			$l_lineendingscleanup = false;
			$l_placecleanup = false;
			$l_datecleanup=false;
			$l_isansi = false;
			$fp = fopen($GEDCOMS[$GEDFILENAME]["path"], "r");
			//-- read the gedcom and test it in 8KB chunks
			while(!feof($fp)) {
				$fcontents = fread($fp, 1024*8);
				if (!$l_headcleanup && need_head_cleanup()) $l_headcleanup = true;
				if (!$l_macfilecleanup && need_macfile_cleanup()) $l_macfilecleanup = true;
				if (!$l_lineendingscleanup && need_line_endings_cleanup()) $l_lineendingscleanup = true;
				if (!$l_placecleanup && ($placesample = need_place_cleanup()) !== false) $l_placecleanup = true;
				if (!$l_datecleanup && ($datesample = need_date_cleanup()) !== false) $l_datecleanup = true;
				if (!$l_isansi && is_ansi()) $l_isansi = true;
			}
			fclose($fp);
			
			if (!isset($cleanup_needed)) $cleanup_needed = false;
			if (!$l_datecleanup && !$l_isansi  && !$l_headcleanup && !$l_macfilecleanup &&!$l_placecleanup && !$l_lineendingscleanup) {
				print $pgv_lang["valid_gedcom"];
				print "</td></tr>";
				$import = true;
			}
			else {
				$cleanup_needed = true;
				print "<input type=\"hidden\" name=\"cleanup_needed\" value=\"cleanup_needed\">";
				if (!file_is_writeable($GEDCOMS[$GEDFILENAME]["path"]) && (file_exists($GEDCOMS[$GEDFILENAME]["path"]))) {
					print "<span class=\"error\">".str_replace("#GEDCOM#", $GEDCOM, $pgv_lang["error_header_write"])."</span>\n";
					print "</td></tr>";
				}
				// NOTE: Check for head cleanu
				if ($l_headcleanup) {
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print_help_link("invalid_header_help", "qm", "invalid_header");
					print "<span class=\"error\">".$pgv_lang["invalid_header"]."</span>\n";
					print "</td></tr>";
				}
				// NOTE: Check for mac file cleanup
				if ($l_macfilecleanup) {
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print_help_link("macfile_detected_help", "qm", "macfile_detected");
					print "<span class=\"error\">".$pgv_lang["macfile_detected"]."</span>\n";
					print "</td></tr>";
				}
				// NOTE: Check for line endings cleanup
				if ($l_lineendingscleanup) {
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print_help_link("empty_lines_detected_help", "qm", "empty_lines_detected");
					print "<span class=\"error\">".$pgv_lang["empty_lines_detected"]."</span>\n";
					print "</td></tr>";
				}
				// NOTE: Check for place cleanup
				if ($l_placecleanup) {
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print "<table class=\"facts_table\">";
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print "<span class=\"error\">".$pgv_lang["place_cleanup_detected"]."</span>\n";
					print "</td></tr>";
					print "<tr><td class=\"descriptionbox wrap width20\">";
					print_help_link("cleanup_places_help", "qm", "cleanup_places");
					print $pgv_lang["cleanup_places"];
					print "</td><td class=\"optionbox\" colspan=\"2\"><select name=\"cleanup_places\">\n";
					print "<option value=\"YES\" selected=\"selected\">".$pgv_lang["yes"]."</option>\n<option value=\"NO\">".$pgv_lang["no"]."</option>\n</select>";
					print "</td></tr>";
					print "</td></tr><tr><td class=\"optionbox\" colspan=\"2\">".$pgv_lang["example_place"]."<br />".PrintReady(nl2br($placesample[0]));
					print "</table>\n";
					print "</td></tr>";
				}
				// NOTE: Check for date cleanup
				if ($l_datecleanup) {
					print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
					print "<span class=\"error\">".$pgv_lang["invalid_dates"]."</span>\n";
					print "<table class=\"facts_table\">";
					print "<tr><td class=\"descriptionbox width20\">";
					print_help_link("detected_date_help", "qm");
					print $pgv_lang["date_format"];
					
					print "</td><td class=\"optionbox\" colspan=\"2\">";
					if (isset($datesample["choose"])){
						print "<select name=\"datetype\">\n";
						print "<option value=\"1\">".$pgv_lang["day_before_month"]."</option>\n<option value=\"2\">".$pgv_lang["month_before_day"]."</option>\n</select>";
					}
					else print "<input type=\"hidden\" name=\"datetype\" value=\"3\" />";
					print "</td></tr><tr><td class=\"optionbox\" colspan=\"2\">".$pgv_lang["example_date"]."<br />".$datesample[0];
					print "</td></tr>";
					print "</table>\n";
					print "</td></tr>";
				}
				// NOTE: Check for ansi encoding
				if ($l_isansi) {
					print "<tr><td class=\"optionbox\" colspan=\"2\">";
					print "<span class=\"error\">".$pgv_lang["ansi_encoding_detected"]."</span>\n";
					print "<table class=\"facts_table\">";
					print "<tr><td class=\"descriptionbox wrap width20\">";
					print_help_link("detected_ansi2utf_help", "qm", "ansi_to_utf8");
					print $pgv_lang["ansi_to_utf8"];
					print "</td><td class=\"optionbox\"><select name=\"utf8convert\">\n";
					print "<option value=\"YES\" selected=\"selected\">".$pgv_lang["yes"]."</option>\n";
					print "<option value=\"NO\">".$pgv_lang["no"]."</option>\n</select>";
					print "</td></tr>";
					print "</table>\n";
				}
			}
		}
		else if (!$cleanup_needed) {
			print $pgv_lang["valid_gedcom"];
			$import = true;
		}
		else $import = true;
		?>
		<input type = "hidden" name="GEDFILENAME" value="<?php if (isset($GEDFILENAME)) print $GEDFILENAME; ?>" />
		<input type = "hidden" name="verify" value="validate_form" />
		<input type = "hidden" name="bakfile" value="<?php if (isset($bakfile)) print $bakfile; ?>" />
		<input type = "hidden" name="path" value="<?php if (isset($path)) print $path; ?>" />
		<input type = "hidden" name="no_upload" value="<?php if (isset($no_upload)) print $no_upload; ?>" />
		<input type = "hidden" name="override" value="<?php if (isset($override)) print $override; ?>" />
		<input type = "hidden" name="ok" value="<?php if (isset($ok)) print $ok; ?>" />
		<?php
	print "</table>";
	print "</div>";
	print "</td></tr>";


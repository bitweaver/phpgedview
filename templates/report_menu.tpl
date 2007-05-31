{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/report_menu.tpl,v 1.1 2007/05/31 13:56:58 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$pagetitle}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<form name="choosereport" method="get" action="bit_reportengine.php">
		<input type="hidden" name="action" value="setup" />
		<input type="hidden" name="output" value="$output" />
		<table class="facts_table center">
			<tr>
				<td class="descriptionbox wrap width20 vmiddle">{tr}Select Report{/tr}
				</td>
				<td class="optionbox">
					<select name="report">

					{foreach from = $reports key=myId item=report}
						<option value="{$report.file}">{$report.title}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="2" style="text-align:center;"><input type="submit" value="{tr}Run Report{/tr}" />
				</td>
			</tr>
		</table>
		</form>
	</div><!-- end .body -->

</div><!-- end .gedcom -->

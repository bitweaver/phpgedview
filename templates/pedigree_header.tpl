<div class="header">
<table><tr><td valign="middle">
	<h1>{$pagetitle}</h1>
	<h1>{$name}</h1>
	</td><td width="50px">&nbsp;</td>
	<td>
	<form name="people" id="people" method="get" action="?">
	<input type="hidden" name="show_full" value="show_full" />
		<table class="list_table" width="500" align="center">
			<tr>
				<td colspan="4" class="topbottombar" style="text-align:center; ">
					Options
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">Root_person
				</td>
				<td class="descriptionbox wrap">Generations
				</td>
				<td class="descriptionbox wrap">Orientation
				</td>
				<td class="descriptionbox wrap">Show_details
				</td>
			</tr>

			<tr>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="1" />
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</td>
				<td class="optionbox">
					<select name="talloffset">
						<option value="0">Portrait</option>
						<option value="1">Landscape</option>
						<option value="2">Landscape_top</option>
						<option value="3">Landscape_down</option>
					</select>
				</td>
				<td class="optionbox">
					<input type="checkbox" value="1" checked="checked" onclick="document.people.show_full.value='0';">
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="4">
					<input type="submit" class="btn" value="{$tree.tall}" />
				</td>
			</tr>
		</table>
	</form>
</td></tr>
</table>
</div>
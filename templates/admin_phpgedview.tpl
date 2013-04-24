{strip}
{if !$settings}
	{assign var=settings value=$gBitSystem->mConfig}
{/if}

{form legend="phpgedview Settings"}
	{jstabs}
		{jstab title="GEDCOM Settings"}
			{legend legend="GEDCOM Settings"}
				<input type="hidden" name="page" value="{$page}" />
				<div class="control-group">
					{formlabel label=pgv_calendar_format for=$item}
					{forminput}
						{html_options name=pgv_calendar_format output=$calendar values=$calendar selected=$gBitSystem->getConfig('pgv_calendar_format') id=pgv_calendar_format}
						{formhelp note="Selection of gedcom display calendar format."}
					{/forminput}
				</div>

				{foreach from=$formGedcomFeatures key=item item=output}
					<div class="control-group">
						{formlabel label=$output.label for=$item}
						{forminput}
							{html_options name=$output.label output=$generation values=$generation selected=$gBitSystem->getConfig($item) id=$output.label}
							{formhelp note=$output.note}
						{/forminput}
					</div>
				{/foreach}

				<div class="control-group">
					{formlabel label="Use RIN References" for="pgv_use_RIN"}
					{forminput}
						{html_checkboxes name="pgv_use_RIN" values="y" checked=$gBitSystem->getConfig('pgv_use_RIN') labels=false id=Use_RIN}
						{formhelp note="Allow users to select to use RIN reference identifiers."}
					{/forminput}
				</div>
				<div class="control-group submit">
					<input type="submit" class="btn" name="gedcomTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Gedcom Entries Ident Prefixes"}
			{legend legend="Gedcom Entries Ident Prefixes"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$gedcomPrefixValues key=item item=output}
					<div class="control-group">
						{formlabel label=$output.label for=$item}
						{forminput}
							<input type="text" id="{$item}" name="{$item}" value="{$gBitSystem->getConfig($item)}" size="10">
							{formhelp note=$output.note}
						{/forminput}
					</div>
				{/foreach}

				<div class="control-group submit">
					<input type="submit" class="btn" name="gedcomPrefixSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Privacy Features"}
			{legend legend="Privacy Features"}
				<input type="hidden" name="page" value="{$page}" />

				{if $gBitSystem->isPackageActive( 'categories' )}
				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="gedcom_categ" id="gedcom_categ"Use a category for posts
							{if $gedcom_categ eq 'y'}checked="checked"{/if} />
					</label>
				</div>
				{/if}

				<div class="control-group submit">
					<input type="submit" class="btn" name="featuresTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="List Settings"}
			{legend legend="List Settings"}
				<input type="hidden" name="page" value="{$page}" />

				<div class="control-group">
					{formlabel label="Default ordering for gedcom listing" for="blog_list_order"}
					{forminput}
						<select name="gedcom_list_order" id="gedcom_list_order">
							<option value="created_desc" {if $gedcom_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date{/tr} ({tr}desc{/tr})</option>
							<option value="last_modified_desc" {if $gedcom_list_order eq 'last_modified_desc'}selected="selected"{/if}>{tr}Last modification date{/tr} ({tr}desc{/tr})</option>
							<option value="title_asc" {if $gedcom_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title{/tr} ({tr}asc{/tr})</option>
							<option value="posts_desc" {if $gedcom_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts{/tr} ({tr}desc{/tr})</option>
							<option value="hits_desc" {if $gedcom_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits{/tr} ({tr}desc{/tr})</option>
							<option value="activity_desc" {if $gedcom_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity{/tr} ({tr}desc{/tr})</option>
						</select>
					{/forminput}
				</div>

				{foreach from=$formGedcomLists key=item item=output}
					<div class="control-group">
						{formlabel label=$output.label for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=$output.note page=$output.page}
						{/forminput}
					</div>
				{/foreach}

				<div class="control-group submit">
					<input type="submit" class="btn" name="listTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}

{/strip}

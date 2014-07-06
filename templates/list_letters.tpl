{* $Header$ *}

	{foreach from=$indialpha key=letId item=letter }
		{if $letter ne '@' }
			{if isset($SEARCH_SPIDER) }
				<a href="?alpha={$letter.url}&amp;surname_sublist=no&amp;ged={$GEDCOM}">
			{else}
				<a href="?alpha={$letter.url}&amp;surname_sublist={$surname_sublist}">
			{/if}
			{if $alpha eq $letter && $show_all eq 'no' }
				<span class=\"warning\">{$letter}</span>
			{else}
				{$letter}
			{/if}
			</a>&nbsp;|&nbsp;
		{/if}
	{/foreach}
	{if isset($pass) }
		{if isset($SEARCH_SPIDER) }
			{if isset($alpha) && $alpha eq '@' } 
				<a href="?alpha=@&amp;ged={$GEDCOM}&amp;surname_sublist=no&amp;surname=@N.N."><span class=\"warning\">{tr}(unknown){/tr}</span></a>
			{else}
				<a href="?alpha=@&amp;ged={$GEDCOM}&amp;surname_sublist=no&amp;surname=@N.N.">{tr}(unknown){/tr}</a>
			{/if}	
		{else}
			{if isset($alpha) && $alpha eq '@' } 
				<a href="?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N."><span class=\"warning\">{tr}(unknown){/tr}</span></a>
			{else}
				<a href="?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.">{tr}(unknown){/tr}</a>
			{/if}
		{/if}
	{/if}
	{if !isset($SEARCH_SPIDER) }
		{if $show_all eq 'yes' } 
			<a href="?show_all=yes&amp;ged={$GEDCOM}&amp;surname_sublist=no"><span class=\"warning\">{tr}ALL{/tr}</span></a>
		{else}
			<a href="?show_all=yes&amp;ged={$GEDCOM}&amp;surname_sublist=no">{tr}ALL{/tr}</a>
		{/if}
	{else}
		{if $show_all eq 'yes' }
			<a href="?show_all=yes&amp;surname_sublist={$surname_sublist}"><span class=\"warning\">{tr}ALL{/tr}</span></a>
		{else}
			<a href="?show_all=yes&amp;surname_sublist={$surname_sublist}">{tr}ALL{/tr}</a>
		{/if}
	{/if}

{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/pedigree.tpl,v 1.3 2009/11/01 22:31:17 lsces Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

	{include file="bitpackage:phpgedview/pedigree_header.tpl"}
	{formfeedback error=$errors}

	<div class="body">
		<div id="pedigree_chart" style="position: relative; z-index: 1;">
			{foreach from=$pos key=i item=tree }
				{if isset($boxes.$i.left) }
					<div id="line{$i}" style="position:absolute; left:{$boxes.$i.left}px; top:{$boxes.$i.top}px; z-index: 0;">
						<img src="{$smarty.const.PHPGEDVIEW_PKG_URL}images/vline.gif" width="3" height="{$boxes.$i.height}" alt="" />
					</div>
				{/if}
					<div id="box{$tree.id}.1.{$i}" style="position:absolute; left:{$tree.left}px; top:{$tree.top}px; width:{$tree.width}px; height:{$tree.height}px; z-index: 0;">
						<table border="0" cellspacing="0" cellpadding="0" width="100%" dir="ltr">
						<tr>
							{if isset($tree.tall) }
								<td>
								<img src="{$smarty.const.PHPGEDVIEW_PKG_URL}images/hline.gif" align="left" hspace="0" vspace="0" alt="" />
								</td>
							{/if}
							<td width="100%">
							<div id="out{$tree.id}.1.{$i}" class="person_box{$tree.sexflag}" 
								style="width:{$tree.width-26}px; height:{$tree.height-5}px; padding: 2px; overflow: hidden;"
								onclick="expandbox('{$tree.id}.1.{$i}', 1); return false;">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									{if isset($tree.image) }
										{if isset($tree.imagepop) } {$tree.imagepop} {/if}
											<img src="{$smarty.const.PHPGEDVIEW_PKG_URL}media/{$tree.image}" hspace="0" vspace="0" class="{$tree.imageclass}" alt="" title="" />
		    							{if isset($tree.imagepop) } </a> {/if}
		    						{/if}
										<span id="namedef-{$tree.id}.1.{$i}" class="name1 BIRT">{$tree.name}</span>
		    							<span class="name1">{$tree.seximage}</span><span class="details1">&lrm;({$tree.id})&lrm; </span>
										<div id="fontdef-{$tree.id}.1.{$i}" class="details1">
											<div id="inout2-{$tree.id}.1.{$i}"  style="display: block;">
												<span class="details_label">Birth</span>{$tree.dob}
											</div>
										</div>
		    						</td>
		    						</tr>
		    					</table>
		    				</div>
		    				{if isset($tree.rarrow) }
		    					</td>
		    					<td valign="middle">{$tree.rarrow}
		    				{/if}
		    			</td></tr>
		    			</table>
					</div>
			{/foreach}
		</div><!-- end .chart -->
{literal}
<script language="JavaScript" type="text/javascript">
	pedigree_div = document.getElementById("pedigree_chart");
	if (pedigree_div) {
		pedigree_div.style.height = "{/literal}{$maxyoffset}{literal}px";
	}
</script>
{/literal}
	</div><!-- end .body -->

</div><!-- end .gedcom -->
{/strip}
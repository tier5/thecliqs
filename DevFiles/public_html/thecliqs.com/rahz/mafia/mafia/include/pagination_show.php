<?
	function pagination_show($pagination){ ?>
	
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td nowrap>Showing <?= $pagination['first_row'] ?> - <?= $pagination['last_row'] ?> of <?= $pagination['total_rows'] ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width="100%" align="right">
		<p style="text-align: right; margin: 0; padding:0;">
			<? if ($pagination['pages']): ?>
			   
				<? if ($pagination['first']): ?>
					<a href="<?= $pagination['first'] ?>">&lt; first</a>
					<a href="<?= $pagination['prev'] ?>">&lt; prev</a>
				<? else: ?>
					&lt; first
					&lt; prev
				<? endif; ?>
				|
				<? foreach ($pagination['pages'] as $p): ?>
					<? if ($p['link']): ?><a href="<?= $p['link'] ?>"><? endif; ?><?= $p['no'] ?><? if ($p['link']): ?></a><? endif; ?> |
				<? endforeach; ?>
	
				<? if ($pagination['last']): ?>
					<a href="<?= $pagination['next'] ?>">next &gt;</a>
					<a href="<?= $pagination['last'] ?>">last &gt;</a>
				<? else: ?>
					next &gt;
					last &gt;
				<? endif; ?>
			<? endif; ?>
		</p>
		</td>
	</tr>
</table>

<?	
	}
?>
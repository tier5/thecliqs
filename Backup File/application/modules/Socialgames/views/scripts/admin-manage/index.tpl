<h2>
	<?php echo $this->translate("SocialGames Settings") ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
    </div>
<?php endif; ?>
<p>
	Here you may manage games, view, edit, delete it.
</p>
<?php if ($this->import) { ?>
<div style='margin-top:10px; border:1px solid #ccc;padding:10px;display:table;'>
	You are not import games please do it!
</div>
<div style='margin-top:10px;border:3px double #ccc;padding:10px;display:table'>
	<?php echo $this->htmlLink(array('reset' => false, 'import' => 1), $this->translate('Do Import')) ?>
</div>
<?php } ?>
<?php if (!$this->import) { ?>
<div style='margin-top:10px;'>
	<?php echo $this->form->render($this) ?>
	<div style='clear:both'></div>
</div>
<?php } ?>
<?php if( count($this->paginator) ): ?>

<table class='admin_table' style='margin-top:20px;'>
  <thead>
    <tr>
		<th class='admin_table_short'>Image</th>
		<th class='admin_table_short'>Title</th>
		<th class='admin_table_short'>Category</th>
		<th class='admin_table_short'>Total Members</th>
		<th class='admin_table_short'>Total Views</th>
		<th class='admin_table_short'>Options</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr <?php if ($item->is_featured) { ?>style="background:#e5ed00;"<?php } ?>>
		<td><img src='<?php echo $item->image; ?>' style='width:100px;'/></td>
		<td><?php echo $item->title; ?></td>
		<td><?php echo $item->category; ?></td>
		<td><?php echo $item->total_members; ?></td>
		<td><?php echo $item->total_views; ?></td>
        <td>
           <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'socialgames', 'controller' => 'admin-manage', 'action' => 'gameedit', 'id' => $item->game_id),
                $this->translate("edit"),
                array('class' => 'smoothbox')) ?>
				 | 
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'socialgames', 'controller' => 'admin-manage', 'action' => 'gamedelete', 'id' => $item->game_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
				|
		<a href='<?php echo $this->url(array('game_id' => $item->game_id,'slug' => $item->title), "games_view"); ?>'> view </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php endif; ?>
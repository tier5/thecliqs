<?php
 $this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
?>
<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected songs?');?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br>
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table' style="width: 100%;">
    <thead>
      <tr>
       <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th style="width: 40%;"><?php echo $this->translate("Title") ?></th>
        <th style="width: 30%;"><?php echo $this->translate("Album") ?></th>
        <th><?php echo $this->translate("Plays") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
         <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
          <td><?php echo $item->getTitle() ?></td>
          <td><?php $album = Engine_Api::_()->getItem('mp3music_album', $item->album_id);
          echo $album->getTitle() ?></td>
          <td><?php echo $this->locale()->toNumber($item->play_count) ?></td>
          <td>
            <a target="_blank" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$item->album_id,'song_id'=>$item->song_id,'popout'=>true), 'mp3music_album');?>',500,565)">play</a>    
            |
            <?php echo $this->htmlLink(array('route' => 'default', 'module'=>'mp3music','controller'=>'album', 'action' => 'delete-song', 'song_id' => $item->getIdentity()), $this->translate('delete'), array(
              'class' => 'smoothbox',
            )) ?>
          </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<br />

<div class='buttons'>
  <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>
<br/>
  <div>
     <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no songs.") ?>
    </span>
  </div>
<?php endif; ?>

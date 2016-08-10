<?php
 $this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
?>
<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected albums ?');?>");
}

function selectAll()
{
  var checkboxes = $$('td.delete_albums input[type=checkbox]');
  checkboxes.each(function(item, index)
  { 
        item.checked =  $('check_all').checked; 
   }); 
}
function album_feature(album_id)
{
    var element = document.getElementById('album_'+ album_id);
    var checkbox = document.getElementById('featurealbum_'+ album_id);
    var status = 0;

    if(checkbox.checked==true) status = 1;
    else status = 0;
    var content = element.innerHTML;
    new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'mp3music', 'controller' => 'manage', 'action' => 'feature'), 'admin_default') ?>',
      'data' : {
        'format' : 'json',
        'album_id' : album_id,
        'status' : status
      },
      'onRequest' : function(){
          element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Mp3music/externals/images/loading.gif'></img>";
      },
      'onSuccess' : function(responseJSON, responseText)
      {
        element.innerHTML = content;
        checkbox = document.getElementById('featurealbum_'+ album_id);
        if( status == 1) checkbox.checked=true;
        else checkbox.checked=false;
      }
    }).send();

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
        <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th style="width: 40%;"><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Featured") ?></th>
        <th><?php echo $this->translate("Owner") ?></th>
        <th><?php echo $this->translate("Songs") ?></th>
        <th><?php echo $this->translate("Plays") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class='delete_albums' ><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
          <td><?php echo $item->getTitle() ?></td>
          <td>
          <div id='album_<?php echo $item->album_id; ?>' style ="text-align: center;" >
              <?php if($item->is_featured): ?>
                <input type="checkbox" id='featurealbum_<?php echo $item->getIdentity(); ?>' onclick="album_feature(<?php echo $item->getIdentity(); ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='featurealbum_<?php echo $item->getIdentity(); ?>' onclick="album_feature(<?php echo $item->getIdentity(); ?>,this)" />
              <?php endif; ?>
          </div>
        </td>
          <td><?php echo $item->getOwner()->getTitle() ?></td>
          <td><?php echo count($item->getSongs()) ?>
          <td><?php echo $this->locale()->toNumber($item->play_count) ?></td>
          <td><?php echo $item->creation_date ?></td>
          <td>
            <a target="_blank" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$item->album_id), 'mp3music_album');?>',500,565)">play</a>    
            |
            <?php echo $this->htmlLink($item->getDeleteHref(),
              'delete',
              array('class'=>'smoothbox'
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
  <br />
  <div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no albums.") ?>
    </span>
  </div>
<?php endif; ?>

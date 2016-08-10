<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  16.12.11 16:41 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('username', '<?php echo $this->url(array('module' => 'page', 'controller' => 'claim', 'action' => 'add'), 'admin_default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>


<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translate("Manage Claims") ?></h2>

<p>
  <?php echo $this->translate("PAGE_CLAIMABLE_PAGE_CREATORS_DESC") ?>
</p>
<br />

<script type="text/javascript">
  function multiModify()
  {
    var multimodify_form = $('multimodify_form');
    if (multimodify_form.submit_button.value == 'delete')
    {
      return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
    }
  }

  function selectAll()
  {
    var i;
    var multimodify_form = $('multimodify_form');
    var inputs = multimodify_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
    <li style="width:200px">
      <ul >

        <li class="hecore-menu-tab <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'page', 'controller' => 'claim'), 'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('Claimable Page Creators'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab <?php if ($this->menu == 'process'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'page', 'controller'=>'claim', 'action' => 'process'), 'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('Page Claims'); ?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this)?>

<?php if( count($this->paginator) ): ?>
  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s member found", "%s members found", $count),
          $this->locale()->toNumber($count)) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true
      )); ?>
    </div>
  </div>

  <br />

<div class="admin_table_form">
<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("Display Name") ?></th>
        <th><?php echo $this->translate("Username") ?></th>
        <th style='width: 1%;'><?php echo $this->translate("Email") ?></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("User Level") ?></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Approved") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ):
          $user = $this->item('user', $item->user_id);
          ?>
          <tr>
            <td><input name='delete_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'></td>
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($user->getHref(),
                  $this->string()->truncate($user->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
            <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td class='admin_table_email'>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class="admin_table_centered nowrap">
              <a href="<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>">
                <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
              </a>
            </td>
            <td class='admin_table_centered'>
              <?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') ) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="delete-creators"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
</div>


<?php else : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("there are no claimable page creators") ?>
    </span>
  </div>
<?php endif; ?>
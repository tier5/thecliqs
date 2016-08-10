<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: process.tpl  19.12.11 15:36 TeaJay $
 * @author     Taalay
 */
?>

<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translate("Page Claims Processes") ?></h2>

<p>
  <?php echo $this->translate("PAGE_CLAIM_PROCESS_DESCRIPTION") ?>
</p>
<br />

<script type="text/javascript">
  function multiModify()
  {
    var multimodify_form = $('multimodify_form');
    if (multimodify_form.submit_button.value == 'delete')
    {
      return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected claims?")) ?>');
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

<div class="admin_clr"></div>

<?php if ( count($this->paginator) ): ?>
  <div class="admin_table_form">
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
      <table class='admin_table'>
        <thead>
          <tr>
            <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
            <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
            <th><?php echo $this->translate("Page Name") ?></th>
            <th><?php echo $this->translate("Username") ?></th>
            <th><?php echo $this->translate("Claimer Name") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Email") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Claimed Date") ?></th>
            <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("status") ?></th>
            <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("option") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if( count($this->paginator) ): ?>
            <?php foreach( $this->paginator as $item ):
              $user = $this->item('user', $item->user_id);
              $page = $this->item('page', $item->page_id);
              ?>
            <?php
              if(!$user || !$user->getIdentity()) {
                continue;
              }
            ?>
              <tr>
                <td><input name='remove_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'></td>

                <td><?php echo $item->claim_id ?></td>

                <td class='admin_table_bold'>
                  <?php echo $this->htmlLink($page->getHref(),
                    $this->string()->truncate($page->getTitle(), 10),
                    array('target' => '_blank'));
                  ?>
                </td>

                <td class='admin_table_user'>
                  <?php echo $this->htmlLink($user->getHref(),
                    $user->displayname,
                    array('target' => '_blank'))
                  ?>
                </td>

                <td class='admin_table_user'><?php echo $item->claimer_name; ?></td>

                <td class='admin_table_email'>
                  <a href='mailto:<?php echo $item->claimer_email ?>'><?php echo $item->claimer_email ?></a>
                </td>

                <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>

                <td><?php echo $item->status; ?></td>
                <td>
                  <?php
                    if ($item->status == 'approved' || $item->status == 'declined') {
                      $title = 'view result';
                    } else {
                      $title = 'take action';
                    }
                    echo $this->htmlLink(
                    $this->url(
                      array(
                        'module' => 'page',
                        'controller' => 'claim',
                        'action' => 'take-action',
                        'claim_id' => $item->claim_id
                      ), 'admin_default', true
                    ), $this->translate($title), array('class' => 'smoothbox')
                  )?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <br />
      <div class='buttons'>
        <button type='submit' name="submit_button" value="delete-claims"><?php echo $this->translate("Delete Selected") ?></button>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("there are no claims") ?>
    </span>
  </div>
<?php endif; ?>
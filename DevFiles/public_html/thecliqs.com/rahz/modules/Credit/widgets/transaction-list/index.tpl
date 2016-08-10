<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  07.01.12 12:59 TeaJay $
 * @author     Taalay
 */
?>

<div class="credit_table_form" id="credit_transaction">
  <a id="credit_loader_browse" class="credit_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Credit/externals/images/loader.gif', ''); ?></a>
  <form id="credit_browsemembers_ul">
    <table class='credit_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><?php echo $this->translate("Action Date") ?></th>
          <th><?php echo $this->translate("Action Type") ?></th>
          <th style='width: 1%;'><?php echo $this->translate("Credits") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ):
            $user = $this->item('user', $item->user_id);
            ?>
            <tr>
              <td class="nowrap">
                <?php echo $this->timestamp($item->creation_date) ?>
              </td>
              <td>
                <?php
                  if ($item->object_type == null) {
                    echo $this->translate($item->action_name, $item->body);
                  } else {
                    if (!Engine_Api::_()->credit()->isModuleEnabled($item->action_module)) {
                      if ($item->body) {
                        echo $this->translate($item->action_name, $item->body, '<i style="color: red">'.$this->translate('Plugin Disabled').'</i>');
                      } else {
                        echo $this->translate($item->action_name, '<i style="color: red">'.$this->translate('Plugin Disabled').'</i>');
                      }
                    } else {
                      try {
                        $object = $this->item($item->object_type, $item->object_id);
                      } catch (Exception $e) {
                        $object = null;
                      }
                      if ($object !== null) {
                        if ($item->object_type == 'answer') {
                          $uri = $object->getHref();
                          $href = $uri['uri'];
                        } else {
                          $href = $object->getHref();
                        }
                        if ($item->body) {
                          if ($item->action_type == 'hequestion_ask_self') {
                            echo $this->translate($item->action_name, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')), $item->body);
                          } elseif ($item->action_type == 'hequestion_ask') {
                            $action_name = str_replace('arrow', '<span class="hequestion_arrow">→</span>', $item->action_name);
                            echo $this->translate($action_name, $user, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')), $item->body);
                          } elseif ($item->action_type == 'hequestion_answer') {
                            echo $this->translate($item->action_name, $user, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')), $item->body);
                          } else {
                            echo $this->translate($item->action_name, $item->body, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')));
                          }
                        } else {
                          echo $this->translate($item->action_name, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')));
                        }
                      } else {
                        if ($item->body) {
                          echo $this->translate($item->action_name, $item->body, '<i style="color: red">'.$this->translate('Deleted').'</i>');
                        }  else {
                          echo $this->translate($item->action_name, '<i style="color: red">'.$this->translate('Deleted').'</i>');
                        }
                      }
                    }
                  }
                ?>
              </td>
              <td style="color: <?php echo ($item->credit > 0) ? 'green' : 'red'?>"><?php echo $this->locale()->toNumber($item->credit) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <br />
  </form>
</div>

<?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","credit"), array('identity' => $this->identity, 'class' => '.layout_credit_transaction_list')); ?>
<?php endif; ?>
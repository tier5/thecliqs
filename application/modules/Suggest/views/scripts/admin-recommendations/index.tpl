<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<h2><?php echo $this->translate("Suggest Plugin"); ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<h3>
  <?php echo $this->translate("Manage Your Recommendations"); ?>
</h3>

<p>
  <?php echo $this->translate("SUGGEST_ADMIN_RECOMMEND_DESCRIPTION"); ?>
</p>
<br />

<div class="rec-types">
  <span class="bold"><?php echo $this->translate('What do you want to recommend?'); ?></span>
  <select name="rectype" onchange="window.location.href=this.value">
    <?php foreach($this->itemTypes as $key => $type): ?>
      <option value="<?php echo $this->url(array('module' => 'suggest', 'controller' => 'recommendations', 'action' => 'index', 'rectype' => $key, 'onlyrec' => $this->onlyRec), 'admin_default'); ?>" <?php echo $key == $this->activeType ? 'selected' : '';?> ><?php echo $type; ?></option>
    <?php endforeach; ?>
  </select>
  <input id='only_rec' type="checkbox" <?php echo $this->onlyRec ? 'checked' : ''; ?> onclick="window.location.href = this.checked ? '<?php echo $this->string()->escapeJavascript($this->url(array('module' => 'suggest', 'controller' => 'recommendations', 'action' => 'index', 'rectype' => $this->activeType, 'onlyrec' => 1), 'admin_default')); ?>' : '<?php echo $this->string()->escapeJavascript($this->url(array('module' => 'suggest', 'controller' => 'recommendations', 'action' => 'index', 'rectype' => $this->activeType, 'onlyrec' => 0), 'admin_default')); ?>'; " value="1" />
  <label for='only_rec' class="bold"><?php echo $this->translate('Show only recommended?'); ?></label>
</div>

<br />
<div class="rec-list">
  <div>
    <?php echo $this->paginationControl($this->items); ?>
  </div>
  <br />
  <?php if ($this->items->getTotalItemCount() > 0): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
        <?php if ($this->activeType == 'user'): ?>
          <th><?php echo $this->translate("User") ?></th>
        <?php else: ?>
          <th><?php echo $this->translate("Title") ?></th>
          <th><?php echo $this->translate("Description") ?></th>
        <?php endif; ?>
        <th style='width: 10%;'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($this->items as $item): ?>
        <tr>
          <td><?php echo $item->getIdentity(); ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('target' => '_blank')); ?></td>
          <?php if ($this->activeType != 'user'): ?>
            <td><?php echo $this->truncate($item->getDescription(), 100); ?></td>
          <?php endif; ?>
          <td>
            <?php if (!_ENGINE_ADMIN_NEUTER && !$item->rec): ?>
              <?php /*echo $this->htmlLink(
                $this->url(
                  array(
                    'action' => 'recommend',
                    'controller' => 'recommendations',
                    'module' => 'suggest',
                    'object_type' => $item->getType(),
                    'object_id' => $item->getIdentity()
                  ), 'admin_default'),
                $this->translate('Recommend'),
                array(
                  'class' => 'bold'
                ));*/

            echo '<a class="buttonlink" title="'. $this->translate('Recommend').'" alt="'. $this->translate('Recommend').'" style="background: url(application/modules/Suggest/externals/images/recommend.png) no-repeat; margin-left:15px;"
             href="'.$this->url(array('action' => 'recommend', 'controller' => 'recommendations', 'module' => 'suggest', 'object_type' => $item->getType(), 'object_id' => $item->getIdentity()), 'admin_default').'"></a>';
              ?>
            <?php else: ?>
              <?php /*echo $this->htmlLink(
                $this->url(
                  array(
                    'action' => 'unrecommend',
                    'controller' => 'recommendations',
                    'module' => 'suggest',
                    'object_type' => $item->getType(),
                    'object_id' => $item->getIdentity()
                  ), 'admin_default'),
                $this->translate('Unrecommend'),
                array(
                  'class' => 'bold'
                ));*/

            echo '<a class="buttonlink" title="'. $this->translate('Unrecommend').'" alt="'. $this->translate('Unrecommend').'" style="background: url(application/modules/Suggest/externals/images/unrecommend.png) no-repeat; margin-left:15px;"
             href="'.$this->url(array('action' => 'unrecommend', 'controller' => 'recommendations', 'module' => 'suggest', 'object_type' => $item->getType(), 'object_id' => $item->getIdentity()), 'admin_default').'"></a>';
              ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <div class="tip">
      <span><?php echo $this->translate('There is no recommendations.'); ?></span>
    </div>
  <?php endif; ?>
  <br />
  <div>
    <?php echo $this->paginationControl($this->items); ?>
  </div>

</div>
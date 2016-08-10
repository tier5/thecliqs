<div class="headvmessages_view_between">
  <?php
  // Resource
  if( $this->resource ) {
    echo $this->translate('To members of %1$s', $this->resource->toString());
  }
  // Recipients
  else {
    $you  = array_shift($this->recipients);
    $you  = $this->htmlLink($you->getHref(), ($this->viewer()->isSelf($you) ? $this->translate('You') : $you->getTitle()));
    $them = array();
    foreach ($this->recipients as $r) {
      if ($r != $this->viewer()) {
        $them[] = ($r==$this->blocker?"<s>":"").$this->htmlLink($r->getHref(), $r->getTitle()).($r==$this->blocker?"</s>":"");
      } else {
        $them[] = $this->htmlLink($r->getHref(), $this->translate('You'));
      }
    }

    if (count($them)) echo $this->translate('HEADVMESSAGES_With %1$s', $this->fluentList($them));
  }
  ?>
</div>
<?php echo $this->render('_messages_list.tpl'); ?>
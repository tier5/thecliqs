<div class="acc_element">
	<ul>
	  <?php foreach( $this->container as $link ): ?>
	    <li>
	      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
	        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
	        'style' => 'background-image: url('.$link->get('icon').');',
	        'target' => $link->get('target'),
	      )) ?>
	    </li>
	  <?php endforeach; ?>
	</ul>
</div>


<script type="text/javascript">
window.addEvent('domready', function() {
  var accordion = new Fx.Accordion($$('.acc_toggler'),$$('.acc_element'), {
    opacity: 0, alwaysHide : true, display:-1, duration: 300,
    onActive: function(toggler) { toggler.addClass('acc_toggler_active'); },
    onBackground: function(toggler) { toggler.removeClass('acc_toggler_active'); }
  });
});
</script>
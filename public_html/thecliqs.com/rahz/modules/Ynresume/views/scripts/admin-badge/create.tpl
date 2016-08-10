<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
<?php
// Render the menu
//->setUlClass()
echo $this->navigation()->menu()->setContainer($this->navigation)->render()
?>
</div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
    <?php echo $this -> form -> render($this); ?>
    </div>
</div>

<script>
    window.addEvent('domready', function() {
        if ($('condition-completeness').checked) {
            $('count_value-wrapper').hide();
            $('completeness_value-wrapper').show();
        }
        else {
            $('count_value-wrapper').show();
            $('completeness_value-wrapper').hide();
        }
        $$('input[type="radio"][name="condition"]').addEvent('click', function() {
            if (this.get('id') == 'condition-completeness') {
                $('count_value-wrapper').hide();
                $('completeness_value-wrapper').show();
            }
            else {
                $('count_value-wrapper').show();
                $('completeness_value-wrapper').hide();
            }    
        });        
    });
</script>
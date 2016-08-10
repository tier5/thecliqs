<?php echo $this->form->render($this) ?>

<?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>

<?php
  // Render the menu creation
  echo $this->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->render();
?>
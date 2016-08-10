<ul class = "global_form_box">
  <li>
    <?php echo $this->translate("Total Contests").": "; ?>
    <span>
      <?php echo $this->locale()->toNumber($this->totalContests); ?>
    </span>
  </li>
  <li>
    <?php echo $this->translate("Photo Contests").": "; ?>
    <span>
      <?php echo $this->locale()->toNumber($this->contestAlbum); ?>
    </span>
  </li>
  <li>
    <?php echo $this->translate("Video Contests").": "; ?>
    <span>
      <?php echo $this->locale()->toNumber($this->contestVideo); ?>
    </span>
  </li>  
  <li>
    <?php echo $this->translate("Blog Contests").": "; ?>
    <span>
      <?php echo $this->locale()->toNumber($this->contestBlog); ?>
    </span>
  </li>
  <li>
    <?php echo $this->translate("Winners").": "; ?>
    <span>
      <?php echo $this->locale()->toNumber($this->totalWinner); ?>
    </span>
  </li>
</ul>
<div class="layout_page_ynrestapi_authorize">
  <h2>
    <?php echo $this->client->title; ?>
  </h2>
  <div class="authorize_body">
  <p>
    <strong><?php echo $this->client->title; ?></strong> would like to access the following data:
  </p>
  <ul class="authorize_scopes">
    <?php foreach ($this->authorizeScopes as $key => $value) : ?>
    <li><?php echo $value; ?></li>
    <?php endforeach; ?>
  </ul>
  </div>
  <ul class="authorize_options">
    <li>
      <form method="post">
        <button type="submit" class="button authorize">Authorize</button> 
        <input type="hidden" name="authorized" value="yes" />
      </form>
    </li>
    <li class="cancel">
      <form id="cancel" method="post">
        <a href="javascript:void(0)" onclick="document.getElementById('cancel').submit()">Cancel</a>
        <input type="hidden" name="authorized" value="no" />
      </form>
    </li>
  </ul>
</div>
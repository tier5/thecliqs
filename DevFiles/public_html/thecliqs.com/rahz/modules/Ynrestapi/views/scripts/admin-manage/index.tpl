<h2><?php echo $this->translate("YN SE API Plugin") ?></h2>

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
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("Manage Clients") ?> </h3>

          <?php if(count($this->clients)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Client Name") ?></th>
              <th><?php echo $this->translate("Client ID") ?></th>
              <th><?php echo $this->translate("Client Secret") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->clients as $client): ?>
                    <tr>
                      <td><?php echo $client->title?></td>
                      <td><?php echo $client->client_id?></td>
                      <td><?php echo $client->client_secret?></td>
                      <td>
                        <a href='<?php echo $this->url(array('action' => 'edit', 'client_id' => $client->client_id)) ?>'>
                          <?php echo $this->translate("edit") ?>
                        </a>
                        |
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'ynrestapi', 'controller' => 'admin-manage', 'action' => 'delete', 'id' =>$client->client_id),
																$this->translate('delete'),
																array('class' => 'smoothbox',
                        )) ?>

                      </td>
                    </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no clients.") ?></span>
      </div>
      <?php endif;?>
        <br/>

      <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Add Client'), array(
    'class' => 'buttonlink admin_fields_options_addquestion',
  )) ?>

    </div>
    </form>
    </div>
  </div>
     
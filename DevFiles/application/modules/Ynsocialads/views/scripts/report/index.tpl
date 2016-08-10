<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            //check start < end
            $('filter_form').submit();
        }
    });
    });
</script>
<script type="text/javascript">
    function export_data() {
        $('export').value = 1;
        $('filter_form').submit();
        $('export').value = 0;
    }
</script>

<p>
    <?php echo $this->translate("YNSOCIALADS_MY_REPORT_DESCRIPTION") ?>
</p>

    <div class="yn_filter">
        <?php echo $this->form->render($this);?>
    </div>
    <?php if( count($this->paginator) ): ?>
        <div class="fixed-scrolling">
            <table class="ynsocial_table frontend_table">
                <tr>
                    <th><?php echo $this->translate('Date') ?></th> 
                    <th><?php echo $this->translate('Ad') ?></th>
                    <th><?php echo $this->translate('Campaign') ?></th>
                    <th><?php echo $this->translate('Start Date') ?></th>
                    <th><?php echo $this->translate('End Date') ?></th>
                    <th><?php echo $this->translate('Running Date') ?></th>
                    <th><?php echo $this->translate('Reaches') ?></th>
                    <th><?php echo $this->translate('Impressions') ?></th>
                    <th><?php echo $this->translate('Clicks') ?></th>
                    <th><?php echo $this->translate('Unique Clicks') ?></th>
                </tr>
                <?php foreach ($this->paginator as $item): ?>
                    <?php $ad = Engine_Api::_()->getItem('ynsocialads_ad', $item['ad_id']);?>
                    <tr>
                        <td>
                        <?php
                            $date =  new Zend_Date(strtotime($item['date']));
                            $date->setTimezone($this->timezone);
                            echo $this->locale()->toDate($date);
                            ?>
                        </td>
                        <td><?php echo $ad->getTitle() ?></td>
                        <td><?php echo $ad->getCampaign()->getTitle() ?></td>
                        <td><?php if ($ad->start_date) echo $this->locale()->toDate($ad->getStartDate()); ?></td>
                        <td><?php if ($ad->end_date) echo $this->locale()->toDate($ad->getEndDate()); ?></td>
                        <td><?php echo $this->locale()->toDate($ad->getRunningDate()); ?></td>
                        <td><?php echo $item['reaches'] ?></td>
                        <td><?php echo $item['impressions'] ?></td>
                        <td><?php echo $item['clicks'] ?></td>
                        <td><?php echo $item['unique_clicks'] ?></td>
                    </tr>
                    <?php endforeach; ?>
            </table>
        </div>
        <?php 
        echo '<p class=result_count>';
        $total = $this->paginator->getTotalItemCount();
        echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
        echo '</p>';
        ?>
        <div>
            <?php echo $this->paginationControl($this->paginator, null, null, array(
                'pageAsQuery' => true,
                'query' => $this->formValues,
            )); ?>
        </div>
    <?php else:?>           
        <div class="tip">
            <span>
                <?php echo $this->translate("No results.") ?>
            </span>
        </div>
    <?php endif; ?>
<script type="text/javascript">
    $$('.core_main_ynsocialads').getParent().addClass('active');
</script>
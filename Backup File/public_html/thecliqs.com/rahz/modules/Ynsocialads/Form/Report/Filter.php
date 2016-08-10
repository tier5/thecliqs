<?php
class Ynsocialads_Form_Report_Filter extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'method'=>'GET',
            'id' => 'filter_form'
        ));
    
        //Feature Filter
         
        $this->addElement('Select', 'campaign_id', array(
            'label' => 'Campaign:',
            'multiOptions' => array(
                0   => 'All Campaigns',
            ),
            'value' => 0,
            'class' => 'filter_elem',
            'onchange' => 'submit()'
        ));
        
        $this->addElement('Select', 'ad_id', array(
            'label' => 'Ad:',
            'multiOptions' => array(
                0   => 'All Ads',
            ),
            'value' => 0,
            'class' => 'filter_elem',
            'onchange' => 'submit()'
        ));
        
        $this->addElement('Text', 'start_date', array(
          'label' => 'Start:',
          'class' => 'date_picker filter_elem',
        ));
        
        $this->addElement('Text', 'end_date', array(
            'label' => 'End:',
          'class' => 'date_picker filter_elem',
        ));
        
        // $this->addElement('Select', 'time_summary', array(
            // 'multiOptions' => array(
            // 'all_day' => 'All Days',
            // ),
            // 'value' => 0,
            // 'class' => 'filter_elem',
            // 'onchange' => 'submit()'
        // ));
        $this->addElement('Hidden', 'export', array(
            'value' => 0
        ));
        $this->addElement('Select', 'export_type', array(
            'multiOptions' => array(
                'xls' => 'Export Report(.xls)',
                'csv' => 'Export Report(.csv)',
            ),
            'class' => 'filter_elem',
        ));
        
        $this->addElement('Button', 'export_btn', array(
            'label' => 'Export',
            'class' => 'filter_elem',
            'onclick' => 'export_data()'
        ));
        
    }
}
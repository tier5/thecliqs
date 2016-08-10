<?php

class Ynvideochannel_Form_Search extends Engine_Form
{
    protected $_type;

    public function setType($value)
    {
        $this->_type = $value;
    }

    public function init()
    {
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form'))
            ->setMethod('GET');

        $this->addElement('Text', 'keyword', array(
            'label' => ''
        ));

        switch($this->_type)
        {
            case "videos":
                $this->addElement('Select', 'order', array(
                    'label' => 'Browse by',
                    'multiOptions' => array(
                        'creation_date' => 'Most Recent',
                        'most_viewed' => 'Most Viewed',
                        'most_liked' => 'Most Liked',
                        'most_commented' => 'Most Commented',
                        'most_favorited' => 'Most Favorited',
                        'featured' => 'Featured',
                        'rating' => 'Highest Rated'
                    ),
                    'value' => 'creation_date'
                ));
                $this->addElement('Hidden', 'tag', array(
                    'order' => 101
                ));
                break;
            case "channels":
                $this->addElement('Select', 'order', array(
                    'label' => 'Browse by',
                    'multiOptions' => array(
                        'creation_date' => 'Most Recent',
                        'most_liked' => 'Most Liked',
                        'most_commented' => 'Most Commented',
                        'most_subscribed' => 'Most Subscribed',
                        'featured' => 'Featured'
                    ),
                    'value' => 'creation_date'
                ));
                break;
            case "playlists":
                $this->addElement('Select', 'order', array(
                    'label' => 'Browse by',
                    'multiOptions' => array(
                        'creation_date' => 'Most Recent',
                        'most_liked' => 'Most Liked',
                        'most_commented' => 'Most Commented',
                    ),
                    'value' => 'creation_date'
                ));
                break;
        }

        $this->addElement('Select', 'category', array(
            'label' => 'Category',
            'multiOptions' => array(
                'all' => 'All Categories'
            ),
        ));

        // Button
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}
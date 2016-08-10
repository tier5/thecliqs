<?php

class Ynmobile_Helper_Poll extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('poll','ynmobile');
    }
    
    function field_id(){
        $this->data['iPollId']=  $this->entry->getIdentity();
    }
    
    function field_stats(){
        
        parent::field_stats();
        
        $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
        
        $hasVoted = $this->entry->viewerVoted();
        
        $this->data['bIsVoted']         = $hasVoted;
        $this->data['iTotalVote']       = $this->entry->vote_count;
        $this->data['bCanVote']         = $canChangeVote?1: ($hasVoted?0:1);
        $this->data['bIsClosed']        = $this->entry->closed?true:false;
        $this->field_voted();
    }
    
    
    /**
     * get limited 10 voters.
     */
    function field_voters(){
        
       $select = $this->getYnmobileApi()->get_voter_select($this->entry, 1, 10);
        
       $fields  = array('simple_array');
       
       return $this->data['aVoters'] = Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
       
    }
    
    function field_options(){
            
        $poll = $this->entry;
        $pollOptions = $poll->getOptions();
        $viewerId =  $this->getViewerId();
        
        $voted_option_id = 0;
        
        $vote = $this->getYnmobileApi()->get_user_vote($this->entry, $this->getViewerId());
        if($vote){
            $voted_option_id = $vote->poll_option_id;
        }
        
        
        $this->data['iVotedOptionId'] =  $voted_option_id;
        
        $options = array();
        foreach ($pollOptions as $option)
        {
            $options[] = array(
                'iOptionId' => $option->poll_option_id,
                'sOptionTitle' => $option->poll_option,
                'bViewerVoted' => ( $voted_option_id == $option->poll_option_id ) ? 1 : 0,
                'iTotalVote' => $option->votes,
                'iPercent' => ($poll->vote_count)
                     ? floor( 100 * ( $option->votes / $poll->vote_count ) )
                     : 0,
            );
        }
        
        $this->data['aOptions']=  $options;
    }

     function field_listing(){
        $this->field_id();
        $this->field_title();
        $this->field_desc();
        $this->field_user();
        $this->field_stats();
        
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_voters();
        $this->field_likes();
        $this->field_options();
    }
}

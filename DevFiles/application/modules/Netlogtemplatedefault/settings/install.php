<?php

class Netlogtemplatedefault_Installer extends Engine_Package_Installer_Module
{

  function _query() 
{

	$this->db->update('engine4_core_content', array('order'=>'2'), 'name="core.menu-footer"');

		// remove default widgets from home page
	$this->db->delete('engine4_core_content', array('parent_content_id = ?'=>$this->_getContainerId('left', 'core_index_index')));
	$this->db->delete('engine4_core_content', array('parent_content_id = ?'=>$this->_getContainerId('middle', 'core_index_index')));
	$this->db->delete('engine4_core_content', array('parent_content_id = ?'=>$this->_getContainerId('right', 'core_index_index')));
	if ( $this->_getContainerId('netlogtemplate.netlog-friends') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplate.netlog-friends')));
	if ( $this->_getContainerId('netlogtemplate.netlog-header-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplate.netlog-header-menu')));
	if ( $this->_getContainerId('netlogtemplate.netlog-languages') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplate.netlog-languages')));
	if ( $this->_getContainerId('netlogtemplate.netlog-network-statistic') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplate.netlog-network-statistic')));
	if ( $this->_getContainerId('netlogtemplate.netlog-main-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplate.netlog-main-menu')));
	if ( $this->_getContainerId('netlogtemplatedefault.netlog-friends') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatedefault.netlog-friends')));
	if ( $this->_getContainerId('netlogtemplatedefault.netlog-header-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatedefault.netlog-header-menu')));
	if ( $this->_getContainerId('netlogtemplatedefault.netlog-languages') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatedefault.netlog-languages')));
	if ( $this->_getContainerId('netlogtemplatedefault.netlog-network-statistic') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatedefault.netlog-network-statistic')));
	if ( $this->_getContainerId('netlogtemplatedefault.netlog-main-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatedefault.netlog-main-menu')));
	if ( $this->_getContainerId('netlogtemplatered.netlog-friends') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatered.netlog-friends')));
	if ( $this->_getContainerId('netlogtemplatered.netlog-header-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatered.netlog-header-menu')));
	if ( $this->_getContainerId('netlogtemplatered.netlog-languages') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatered.netlog-languages')));
	if ( $this->_getContainerId('netlogtemplatered.netlog-network-statistic') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatered.netlog-network-statistic')));
	if ( $this->_getContainerId('netlogtemplatered.netlog-main-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplatered.netlog-main-menu')));
	if ( $this->_getContainerId('netlogtemplateblue.netlog-friends') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplateblue.netlog-friends')));
	if ( $this->_getContainerId('netlogtemplateblue.netlog-header-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplateblue.netlog-header-menu')));
	if ( $this->_getContainerId('netlogtemplateblue.netlog-languages') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplateblue.netlog-languages')));
	if ( $this->_getContainerId('netlogtemplateblue.netlog-network-statistic') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplateblue.netlog-network-statistic')));
	if ( $this->_getContainerId('netlogtemplateblue.netlog-main-menu') ) $this->db->delete('engine4_core_content', array('content_id = ?'=>$this->_getContainerId('netlogtemplateblue.netlog-main-menu')));

  }

}
<?php
class Ynresume_Model_Publication extends Core_Model_Item_Abstract 
{
    protected $_type = 'ynresume_publication';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
    public function getAuthors()
    {
    	$userTbl = Engine_Api::_()->getItemTable('user');
    	$publicationAuthorTbl = Engine_Api::_()->getDbTable('Publicationauthors', 'ynresume');
    	$select = $publicationAuthorTbl 
    	-> select()
    	-> where ("publication_id = ?", $this -> getIdentity());
    	$publicationAuthors = $publicationAuthorTbl -> fetchAll($select);
    	if (!count($publicationAuthors))
    	{
    		return array();
    	}
    	$authors = array();
        foreach ( $publicationAuthors as $m )
        {
        	if ($m->user_id != 0)
        	{
        		$authors[] = Engine_Api::_()->getItem('user', $m->user_id);
        	}
        	else 
        	{
        		$author = $userTbl -> createRow();
        		$author -> displayname = $m -> name;
        		$authors[] = $author;
        	}
        }
        return $authors;
    }
    
	public function getAuthorObjects()
    {
    	$publicationAuthorTbl = Engine_Api::_()->getDbTable('Publicationauthors', 'ynresume');
    	$select = $publicationAuthorTbl 
    	-> select()
    	-> where ("publication_id = ?", $this -> getIdentity())
    	-> order ("order ASC")
    	;
    	$authors = $publicationAuthors = $publicationAuthorTbl -> fetchAll($select);
        return $authors;
    }
    
    public function getAuthor($user)
    {
    	$publicationAuthorTbl = Engine_Api::_()->getDbTable('Publicationauthors', 'ynresume');
    	$select = $publicationAuthorTbl 
    	-> select()
    	-> where ("publication_id = ?", $this -> getIdentity())
    	-> limit (1);
    	if (is_object($user))
    	{
    		$select -> where ("user_id = ?", $user -> getIdentity());
    	}
    	else if (is_string($user))
    	{
    		$select -> where ("name = ?", $user);
    	}
    	return $publicationAuthorTbl -> fetchRow($select);
    }
    
    public function hasAuthor($user)
    {
    	$author = $this->getAuthor($user);
    	return (!is_null($author));
    }
    
    public function addAuthor($user, $order = null)
    {
    	$publicationAuthorTbl = Engine_Api::_()->getDbTable('Publicationauthors', 'ynresume');
    	$author = $publicationAuthorTbl -> createRow();
    	$author -> publication_id = $this -> getIdentity();
    	if (!$this->hasAuthor($user))
		{
			if (is_object($user))
	    	{
	    		$author -> user_id = $user -> getIdentity();
	    		$author -> name = $user -> getTitle();
	    	}
	    	elseif (is_string($user))
	    	{
	    		$author -> user_id = 0;
	    		$author -> name = strip_tags($user);
	    	}
	    	if (!is_null($order))
	    	{
	    		$author -> order = $order;
	    	}
	    	$author -> save();
    	}
    	else 
    	{
    		throw new Exception("Already author!", 1);
    	}
    }
    
    public function saveAuthor($author, $user, $order = null)
    {
    	if (is_object($user))
    	{
    		$author -> user_id = $user -> getIdentity();
    		$author -> name = $user -> getTitle();
    	}
    	elseif (is_string($user))
    	{
    		$author -> user_id = 0;
    		$author -> name = $user;
    	}
    	if (!is_null($order))
    	{
    		$author -> order = $order;
    	}
    	$author -> save();
    	return $author;
    }
    
    public function removeAuthor($user)
    {
    	if ($this->hasAuthor($user))
		{
    		$author = $this -> getAuthor($user);
    		$author -> delete();
    	}
    	else 
    	{
    		throw new Exception("No author to remove!", 1);
    	}
    }
    
	public function removeAllAuthors()
    {
    	$authors = $this -> getAuthorObjects();
    	foreach ($authors as $author){
    		$author -> delete();
    	}
    }
    
	public function getAuthorAsString()
    {
    	$authors = $this -> getAuthorObjects();
    	$arr = array();
    	foreach ($authors as $author)
    	{
    		if ($author -> user_id > 0)
    		{
    			$arr[] = $author -> user_id;
    		}
    		else
    		{
				$arr[] = $author -> name;    			
    		}
    	}
    	if (count($arr) == 0)
    	{
    		return '';
    	}
    	return implode(',', $arr);
    }
    
	public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
    	$text  = "<h4>{$this->title}</h4>";
    	if ($this -> publisher)
    	{
    		$text .= "<div><b>{$translate->_("Publication/Publisher")}</b> {$this -> publisher}</div>";
    	}
    	
    	$pubDateObject = null;
		if (!is_null($this->publication_date) && !empty($this->publication_date) && $this->publication_date) 
		{
			if (strtotime($this->publication_date) != '')
			{
				$pubDateObject = new Zend_Date(strtotime($this->publication_date));
			}	
		}
    	if(!is_null($pubDateObject))
    	{
    		$date = date('M d Y', $pubDateObject -> getTimestamp());
    		$text .= "<div><b>{$translate->_("Publication Date")}</b> {$date}</div>";
    	}
    	
    	if ($this -> url)
    	{
    		$text .= "<div><b>{$translate->_("Publication URL")}</b> {$this -> url}</div>";
    	}
    	
    	$authors = $this -> getAuthors();
    	if (count($authors))
    	{
    		$text .= "<div>";
    		$i = 0;
    		foreach ($authors as $author)
    		{
    			if ($i > 0) $text .= ", ";
    			$text .= "<span>{$author -> getTitle()}</span>";
    			$i++;
    		}
    		$text .= "</div>";
    	}
    	
    	if ($this->description)
    	{
    		$text .= "<div>{$this->description}</div>";
    	}
    	
		return $text;
    }
}

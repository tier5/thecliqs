<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="en_US" />

<div id="print_view" style="text-align: center;">
    <div style="text-align: left;width: 500px; margin: 0 auto;;">  
       <div style="float: left; padding-right: 10px;">
            <?php echo $this->htmlLink($this->contest->getHref(), $this->itemPhoto($this->contest, 'thumb.normal')) ?>
              
       </div>
       <div>
            <h3 style="margin-bottom: 1px;"><?php echo $this->htmlLink($this->contest->getHref(), $this->contest->contest_name) ?> </h3>
                    
                   
            <p style="padding-left: 58px;"> 
           		<span><?php echo $this->translate("Created by:")?></span>
				<span><a href ="<?php echo $this->contest->getHref()?>"><?php echo $this->contest->getOwner();?></a></span>	
            </p>
            <p style="padding-left: 58px;"> 
           	 	<span><?php echo $this->translate("Contest Type:")?></span>
				<span><?php echo $this->arrPlugins[$this->contest->contest_type]?></span>
            </p>             
            <p style="padding-left: 58px;"> 
              	<span><?php echo $this->translate("Start Date:")?></span> 
				<span> <?php echo $this->locale()->toDate( $this->contest->start_date, array('size' => 'long'));?> </span>
            </p>
            <p style="padding-left: 58px;"> 
            	<span><?php echo $this->translate("End Date:")?></span> 
				<span> <?php echo $this->locale()->toDate( $this->contest->end_date, array('size' => 'long'));?> </span>
            </p>
             <p style="padding-left: 58px;"> 
            	<span><?php echo $this->translate("Participants:")?></span> <span><?php echo count($this->contest->membership() -> getMembers($this->contest->getIdentity(), true))?></span> 
            </p>
             <p style="padding-left: 58px;"> 
            	<span><?php echo $this->translate("Entries:")?></span> <span><?php echo count($this->contest->getEntriesByContest())?></span>
            </p>
            <p style="padding-left: 58px;"> 
           		 <span><?php echo $this->translate("View(s):")?></span> <span><?php echo $this->contest->view_count?></span>	
            </p>
             <p style="padding-left: 58px;"> 
           		 <span><?php echo $this->translate("Like(s):")?></span> <span><?php echo $this->contest->like_count?></span>  
            </p>
             <p style="padding-left: 58px;"> 
            
            </p>
             <p style="padding-left: 58px;"> 
            
            </p>
       </div>
       <br/>
       <div class="ynContest_content">  
       <?php 
         echo $this->contest->description;
       ?>
       </div>
        <div class="ynContest_content">  
       <?php 
         echo $this->contest->award;
       ?>
       </div>
      <?php 
       // $menu = $this->partial('_childs.tpl', array('contest'=>$this->contest));  
       // echo $menu;
        ?>
       <br/>
       <button onclick="window.print()"><?php echo $this->translate("Print")?></button>
    </div>
</div>   
<style type="">
.ynwiki_content p           
{ 
    margin: 0 0 0.7em; 
}
.ynwiki_content table       
{ 
    margin-bottom: 1.4em; 
    width:100%;
    border: 1px solid #C5C5C5;
    border-collapse: collapse;  
}
.ynwiki_content th          
{ 
    font-weight: bold; 
}
.ynwiki_content thead th    
{ 
    background: #c3d9ff; 
}
.ynwiki_content th,
.ynwiki_content td,
.ynwiki_content caption { padding: 4px 10px 4px 5px; }
.ynwiki_content tr
{
    background-color: #C5C5C5;
    border-bottom: 1px solid #EBEBEB;
    height: 28px;
    text-align: center;
}
.ynwiki_content tr + tr
{
    background-color: transparent;
}  
.ynwiki_content td 
{
   border-right : 1px solid #EBEBEB;
   text-align: center;    
}
ul.ynwiki_browse
{
  clear: both;
}
ul.ynwiki_browse span h3
{
  margin: 0;
}
ul.ynwiki_browse > li
{
  clear: both;
  padding: 0px 0px 15px 0px;
}
ul.ynwiki_browse > li + li
{
  border-top-width: 1px;
  padding-top: 15px;
}
ul.ynwiki_browse > li .ynwiki_browse_photo
{
  float: left;
  overflow: hidden;
  margin-right: 8px;
}
html[dir="rtl"] ul.ynwiki_browse > li .ynwiki_browse_photo
{
  float: right;
  margin-right: 0px;
  margin-left: 8px;
}
ul.ynwiki_browse > li .ynwiki_browse_options
{
  float: right;
  overflow: hidden;
  padding-left: 20px;
}
html[dir="rtl"] ul.ynwiki_browse > li .ynwiki_browse_options
{
  float: left;
  padding-left: 0px;
  padding-right: 20px;
}
ul.ynwiki_browse > li .ynwiki_browse_options > a
{
  clear: both;
  display: block;
  margin: 5px;
  font-size: .8em;
  padding-top: 2px;
  padding-bottom: 2px;
}
ul.ynwiki_browse > li .ynwiki_browse_info
{
  overflow: hidden;
}
ul.ynwiki_browse > li .ynwiki_browse_info_title
{
  font-weight: bold;
}
ul.ynwiki_browse > li .ynwiki_browse_info_date
{
  font-size: .8em;
  color: $theme_font_color_light;
}
ul.ynwiki_browse > li .ynwiki_browse_info_blurb
{
  margin-top: 5px;
}
</style>          
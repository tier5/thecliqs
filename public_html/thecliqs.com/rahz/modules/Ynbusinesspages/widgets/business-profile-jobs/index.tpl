<?php
    $this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_job').getParent();
        $('ynbusinesspages_job_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_job_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_job_previous').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                }
            }), {
                'element' : anchor
            })
        });

        $('ynbusinesspages_job_next').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                }
            }), {
                'element' : anchor
            })
        });
    });
</script>

<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
        <!-- Menu Bar -->
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'job',
                'action' => 'list',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
                 'tab' => $this->identity,
            ), '<i class="fa fa-list"></i>'.$this->translate('View all Jobs'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>
        <?php if ($this->canImport):?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'job',
                'action' => 'import',
                'business_id' => $this->business->getIdentity(),
                'tab' => $this->identity,
            ), '<i class="fa fa-plus-square"></i>'.$this->translate('Import Jobs'), array(
                'class' => 'buttonlink smoothbox'
            ))
            ?>
        <?php endif; ?>
    </div>

    <div class="ynbusinesspages-profile-header-content">
     <?php if( $this->paginator->getTotalItemCount() > 0 ): 
        $business = $this->business;?>
        <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
        <?php echo $this-> translate(array("ynbusiness_job", "Jobs", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?></p>
        <?php endif; ?>  
    </div>
</div>

<div class="ynjobposting-browse-listings ynjobposting-browse-job-viewmode-list" id="ynbusinesspages_job">
    <div class="ynjobposting-browse-job-viewmode-grid">        
        <!-- Content -->
        <?php if( $this->paginator->getTotalItemCount() > 0 ): 
            $business = $this->business;?>
        
        <ul class="ynbusinesspages_job ynjobposting-clearfix">           
            <?php foreach ($this->paginator as $job): ?>
            <li class="ynjobposting-browse-listings-item">
                <div class="ynjobposting-browse-listings-item-image">
                    <div class="ynjobposting-browse-listings-item-photo">
                        <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job); ?>
                    </div>
                    <?php if($job -> featured == 1) :?>
                        <span class="ynjobposting-item-featured"><?php echo $this -> translate('featured');?></span>
                    <?php endif;?>
                </div>
                <div class="ynjobposting-browse-listings-item-content">
                    <div class="ynjobposting-browse-listings-item-top">
                        <div class="ynjobposting-browse-listings-item-title <?php if($job -> featured == 1) :?>
                        ynjobposting-featured<?php endif;?>">
                            <a href="<?php echo $job -> getHref();?>"><?php echo $job->title;?></a>
                        </div>
                        <div class="ynjobposting-browse-listings-item-company">
                            <a href="<?php echo $job -> getCompany() -> getHref();?>"><?php echo $job -> getCompany() -> getTitle();?></a>
                        </div>
                        <div class="ynjobposting-browse-listings-item-industry">
                        <?php $industry = $job->getIndustry();
                        echo ($industry) ? $industry->getTitle() : $this->translate('Unknown industry.'); ?>                         
                        </div>
                    </div>
                    <div class="ynjobposting-browse-listings-item-main">
                    <div class="ynjobposting-browse-listings-item-working">
                        <?php if ($job->working_place) :?>
                        <i class="fa fa-map-marker"></i> <?php echo $job->working_place;?>
                        <?php endif;?>
                    </div>
                    <div class="ynjobposting-browse-listings-item-skill">
                        <i class="fa fa-briefcase"></i> <?php echo $job->getLevel(); ?>
                    </div>
                </div>
    
                <div class="ynjobposting-browse-listings-item-footer">
                    <div class="ynjobposting-browse-listings-item-salary">
                        <?php echo $job->getSalary();?>
                    </div>              
                    <div class="ynjobposting-browse-listings-item-type">
                        <span>
                            <?php 
                                $expirationDate = $job->expiration_date;
                                if (!is_null($expirationDate)) {
                                    $expirationDate = new Zend_Date(strtotime($expirationDate));
                                    $expirationDate->setTimezone($this->timezone);
                                    echo $this->locale()->toDate($expirationDate);
                                }
                            ?>  
                        </span>
                        <span class="ynjobposting-browse-listings-item-jobtype ynjobposting-type-<?php echo strtolower( $job->getJobType() );?>"><?php echo $job->getJobType();?></span>
                    </div>
                </div>
                </div>
            </li>       
            <?php endforeach; ?>             
        </ul>  
        
        <div class="ynbusinesspages-paginator">
            <div id="ynbusinesspages_job_previous" class="paginator_previous">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => '',
                  'class' => 'buttonlink icon_previous'
                )); ?>
            </div>
            <div id="ynbusinesspages_job_next" class="paginator_next">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                  'onclick' => '',
                  'class' => 'buttonlink_right icon_next'
                )); ?>
            </div>
        </div>
        
        <?php else: ?>
        <div class="tip">
            <span>
                 <?php echo $this->translate('No jobs have been added.');?>
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>
  
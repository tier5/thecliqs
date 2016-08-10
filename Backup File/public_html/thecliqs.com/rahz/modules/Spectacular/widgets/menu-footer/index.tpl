<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$tempFlag = $flag = 0;
$footerContent = $footertempCatContent = '';

foreach ($this->navigation as $navigation) :
    if ($navigation->uri == 'javascript:void(0)') :
        if (!empty($tempFlag)) :
            $footerContent .= '</ul></div>';
        endif;
        $tempFlag = 1;

        if (!empty($navigation->icon)) :
            $footertempCatContent = '<div class="spectacular_footer_block" style="background-image:url(\'' . $navigation->icon . '\'); background-repeat:no-repeat;"><ul>';
        else:
            $footertempCatContent = '<div class="spectacular_footer_block"><ul><li class="spectacular_footer_block_head">' . $this->translate($navigation->getLabel()) . '</li>';
        endif;
    else:
        if (!empty($footertempCatContent)) :
            $footerContent .= $footertempCatContent;
            $footertempCatContent = '';
        endif;
        if (!empty($navigation->icon)) :
            $tempContent = '<img src="' . $navigation->icon . '" title="' . $this->translate($navigation->getLabel()) . '" />' . ' ' . $this->translate($navigation->getLabel());
        else:
            $tempContent = $this->translate($navigation->getLabel());
        endif;

        if (isset($navigation->target)) {
            $footerContent .= '<li><a href="' . $navigation->getHref() . '" target="' . $navigation->target . '">' . $tempContent . '</a></li>';
        } else {
            $footerContent .= '<li><a href="' . $navigation->getHref() . '">' . $tempContent . '</a></li>';
        }
    endif;
//  endif;
endforeach;

if (!empty($flag) && empty($footertempCatContent)) :
    $footerContent .= '</ul></div>';
endif;

if (!empty($footerContent)) :
    echo '<div class="footerlinks">' . $footerContent . '</div>';
endif;
?>

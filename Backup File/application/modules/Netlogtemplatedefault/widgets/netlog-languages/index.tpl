<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplate
 * @copyright  Copyright 2010-2011 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     Vadim
 */
?>

<form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true)?>" style="display:inline-block">
<?php $selectedLanguage = $this->translate()->getLocale() ?>
<ul class="netlog-template-languages">
<?php $counter = 1; ?>
<?php foreach($this->languageNameList as $code=>$lang) { ?>
	<?php if ( $counter>5 ) { break; } ?>
	<?php if ( $counter!=1 ) print '<li>-</li>'; ?>
	<li<?php if($selectedLanguage==$code) { print ' class="active"'; } ?>><a href="javascript://" onclick="$('netlog-template-selected-lang').set('value','<?php echo $code?>'); $(this).getParent('form').submit();"><?php echo $lang;?></a></li>
	<?php $counter++; ?>
<?php } ?>
	<?php if ( count($this->languageNameList)>5 ) print '<li>-</li><li><a href="javascript://" onclick="$(\'netlog_langs\').setStyle(\'display\',\'block\');">' . $this->translate('More') . '</a></li>'; ?>
</ul>

<div id="netlog_langs">
	<ul>
<?php
	if ( count($this->languageNameList)>5 ) {
		foreach($this->languageNameList as $code=>$lang) {
			if( $selectedLanguage==$code ) { $active = ' class="active"'; } else { $active = ''; }
			print '<li' . $active . '><a href="javascript://" onclick="$(\'netlog-template-selected-lang\').set(\'value\',\'' . $code . '\'); $(this).getParent(\'form\').submit();">'.$lang.'</a></li>';
		}
	}
?>
	</ul>
	<a href="javascript://" class="btnClose" onclick="$('netlog_langs').setStyle('display','none');">X</a>
</div>

<?php echo $this->formHidden('language', $selectedLanguage, array('id'=>'netlog-template-selected-lang')) ?>
<?php echo $this->formHidden('return', $this->url()) ?>
</form>

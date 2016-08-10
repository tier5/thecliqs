<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php
$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();

if(version_compare($coreItem['version'], '4.2.2') < 0)
  echo $this->render('_jsLayout_old.tpl');
else
  echo $this->render('_jsLayout_new.tpl');
?>

<script type="text/javascript">
var currentPage = <?php echo $this->pageObject->getIdentity() ?>;
var newContentIndex = 1;
var currentParent;
var currentNextSibling;
var contentByName = <?php if ($this->contentByName) echo Zend_Json::encode($this->contentByName); else echo "{}" ?>;
var currentLayout = '<?php if ($this->pageObject) echo $this->pageObject->layout ?>';
var currentModifications = [];
var ContentSortables;
var ContentTooltips;

window.onbeforeunload = function(event) {
  if( currentModifications.length > 0 ) {
    return '<?php echo $this->string()->escapeJavascript($this->translate(' - All unsaved changes to pages or widgets will be lost - ')) ?>';
    // return 'I\'m sorry Dave, I can\'t do that.';
  }
}

/* modifications */
var pushModification = function(type) {
  if( !currentModifications.contains(type) ) {
    currentModifications.push(type);
  }
}

var eraseModification = function(type) {
  currentModifications.erase(type);
}

/* Attach javascript to existing elements */
window.addEvent('load', function()
{
  // Add info
  $$('li.pages_content_draggable').each(function(element) {
    var elClass = element.get('class');
    var matches = elClass.match(/pages_content_widget_([^ ]+)/i);
    if( !$type(matches) || !$type(matches[1])) return;
    var name = matches[1];
    var info = contentByName[name] || {};

    element.store('contentInfo', info);

    // Add info for tooltips
    element.store('tip:title', en4.core.language.translate(info.title) || 'Missing widget: ' + matches[1]);
    element.store('tip:text', en4.core.language.translate(info.description) || 'Missing widget: ' + matches[1]);
  });

  // Monitor form inputs for changes
  $$('#pages_layoutbox_menu_pageinfo input').addEvent('change', function(event) {
    if( event.target.get('tag') != 'input' ) return;
    pushModification('info');
  });

  // Add tooltips
  ContentTooltips = new Tips($$('ul#column_stock li.pages_content_draggable'), {

  });

  // Make sortable
  ContentSortables = new NestedSortables($$('ul.pages_content_sortable'), {
    constrain : false,
    clone: function(event, element, list) {
      var tmp = element.clone(true).setStyles({
        margin: '0px',
        position: 'absolute',
        visibility: 'hidden',
        zIndex: 9000,
        'width': element.getStyle('width')
      }).inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
      return tmp;
    },
    onStart : function(element, clone) {
      element.addClass('pages_content_dragging');
      currentParent = element.getParent();
      currentNextSibling = element.getNext();
    },
    onComplete : function(element, clone) {
      element.removeClass('pages_content_dragging');
      if( !currentParent ) {
        //alert('missing parent error');
        return;
      }

      // If it's coming from stock and going into stock, destroy and insert back into original location
      if( currentParent.hasClass('pages_content_stock_sortable') && element.getParent().hasClass('pages_content_stock_sortable') ) {
        if( currentNextSibling ) {
          element.inject(currentNextSibling, 'before');
        } else {
          element.inject(currentParent);
        }
      }

      // If it's not coming from stock, and going into stock, just destroy it
      else if( element.getParent().hasClass('pages_content_stock_sortable') ) {
        element.destroy();

        // Signal modification
        pushModification('main');
      }

      // If it's coming from stock, and not going into stock, put back into stock, clone, and insert
      else if( currentParent.hasClass('pages_content_stock_sortable') && !element.getParent().hasClass('pages_content_stock_sortable') ) {
        var elClone = element.clone();

        // Make it buildable, add info, and give it a temp id
        elClone.inject(element, 'after');
        elClone.addClass('pages_content_buildable');
        elClone.addClass('pages_content_cell');
        elClone.removeClass('pages_content_stock_draggable');
        elClone.getElement('span').setStyle('display', '');
        // @todo
        elClone.set('id', 'pages_content_new_' + (newContentIndex++));

        // Make it draggable
        ContentSortables.addItems(elClone);

        // Remove tips
        ContentTooltips.detach(elClone);

        // Put original back
        if( currentNextSibling ) {
          element.inject(currentNextSibling, 'before');
        } else {
          element.inject(currentParent);
        }

        // Try to expand special blocks
        expandSpecialBlock(elClone);

        // Check for autoEdit
        checkForAutoEdit(elClone);

        // Signal modification
        pushModification('main');
      }

      // It's coming from cms to cms
      else if( !currentParent.hasClass('pages_content_stock_sortable') && !element.getParent().hasClass('pages_content_stock_sortable') ) {
        // Signal modification
        pushModification('main');
      }

      // Something strange happened
      else {
        alert('error in widget placement');
      }

      currentParent = false;
      currentNextSibling = false;
    }
  });

  // Remove disabled stock items
  ContentSortables.removeItems($$('#column_stock li.disabled'));
});

var switchPageMenu = function(event, activator) {
  var element = activator.getParent('li');
  $$('.pages_layoutbox_menu_generic').each(function(otherElement) {
    var otherWrapper = otherElement.getElement('.pages_layoutbox_menu_wrapper_generic');
    if( otherElement.get('id') == element.get('id') && !otherElement.hasClass('active') ) {
      otherElement.addClass('active');
      otherWrapper.setStyle('display', 'block');
      var firstInput = otherElement.getElement('input');
      if( firstInput ) {
        firstInput.focus();
      }
    } else {
      otherElement.removeClass('active');
      if (otherWrapper) {
        otherWrapper.setStyle('display', 'none');
      }
    }
  });
};

var loadPage = function(url) {
  if( confirmPageChangeLoss() ) {
    // window.location.search = '?page=' + page_id;
    window.location.href = url;
  }
};

/* Lazy confirm box */
var confirmPageChangeLoss = function() {
  if( currentModifications.length == 0 ) return true; // Don't ask if nothing to lose
  // @todo check if there are any changes that would be lost
  return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Any unsaved changes will be lost. Are you sure you want to leave this page?")); ?>");
};

/* Remove widget */
var removeWidget = function(element) {
  if( !element.hasClass('pages_content_buildable') ) {
    element = element.getParent('.pages_content_buildable');
  }
  element.destroy();

  // Signal modification
  pushModification('main');
}

var pullWidgetParams = function() {
  if( currentEditingElement ) {
    var fullParams = currentEditingElement.retrieve('contentParams');
    if( $type(fullParams) && $type(fullParams.params) ) {
      return fullParams.params;
    }
  }
  return {};
}

var pullWidgetTypeInfo = function() {
  if( currentEditingElement ) {
    var info = currentEditingElement.retrieve('contentInfo');
    if( $type(info) ) {
      return info;
    }
  }
  return {};
}

/* Set the params in the widget */
var setWidgetParams = function(params) {
  if( !currentEditingElement ) return;
  var oldParams = currentEditingElement.retrieve('contentParams') || {};
  oldParams.params = params
  currentEditingElement.store('contentParams', oldParams);
  currentEditingElement = false;

  // Signal modification
  pushModification('main');
}

/* Save the page info */
var saveCurrentPageInfo = function(formElement) {
  var url = '<?php echo $this->url(array('module'=>'page', 'controller'=>'editor', 'action' => 'update', 'page' => $this->pageObject->getIdentity()), 'admin_default', true); ?>';
  var request = new Form.Request(formElement, formElement.getParent(), {
    requestOptions : {
      url : url
    },
    onComplete: function() {
      eraseModification('info');
    }
  });

  request.send();
}

/* Save current page changes */
var saveChanges = function()
{
  var data = [];
  $$('.pages_content_buildable').each(function(element) {
    var parent = element.getParent('.pages_content_buildable');

    var elData = {
      'element' : {},
      'parent' : {},
      'info' : {},
      'params' : {}
    };

    // Get element identity
    elData.element.id = element.get('id');
    if( elData.element.id.indexOf('pages_content_new_') === 0 ) {
      elData.tmp_identity = elData.element.id.replace('pages_content_new_', '');
    } else {
      elData.identity = elData.element.id.replace('pages_content_', '');
    }

    // Get element class
    elData.element.className = element.get('class');

    // Get element type and name
    if( element.hasClass('pages_content_cell') ) {
      var m = element.get('class').match(/pages_content_widget_([^ ]+)/i);
      if( $type(m) && $type(m[1]) ) {
        elData.type = 'widget';
        elData.name = m[1];
      }
    } else if( element.hasClass('pages_content_block') ) {
      var m = element.get('class').match(/pages_content_container_([^ ]+)/i);
      if( $type(m) && $type(m[1]) ) {
        elData.type = 'container';
        elData.name = m[1];
      }
    } else if( element.hasClass('pages_content_column') ) {
      var m = element.get('class').match(/pages_content_container_([^ ]+)/i);
      if( $type(m) && $type(m[1]) ) {
        elData.type = 'container';
        elData.name = m[1];
      }
    } else {

    }


    if( parent ) {
      // Get parent identity
      elData.parent.id = parent.get('id');
      if( elData.parent.id.indexOf('pages_content_new_') === 0 ) {
        elData.parent_tmp_identity = elData.parent.id.replace('pages_content_new_', '');
      } else {
        elData.parent_identity = elData.parent.id.replace('pages_content_', '');
      }
    }

    elData.info = element.retrieve('contentInfo');
    elData.params = (element.retrieve('contentParams') || {params:{}}).params;

    // Merge with defaults
    if( $type(contentByName[elData.name]) && $type(contentByName[elData.name].defaultParams) ) {
      elData.params = $merge(contentByName[elData.name].defaultParams, elData.params);
    }

    data.push(elData);
  });

  var url = '<?php echo $this->url(array('module'=>'page', 'controller'=>'editor', 'action' => 'update', 'page' => $this->pageObject->getIdentity()), 'admin_default', true); ?>';
  var request = new Request.JSON({
    'url' : url,
    'data' : {
      'format' : 'json',
      'page' : currentPage,
      'structure' : data,
      'layout' : currentLayout
    },
    onComplete : function(responseJSON) {
      $H(responseJSON.newIds).each(function(data, index) {
        var newContentEl = $('pages_content_new_' + index);
        if( !newContentEl ) throw "missing new content el";
        newContentEl.set('id', 'pages_content_' + data.identity);
        newContentEl.store('contentParams', data);
      });
      eraseModification('main');
      alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes to this page have been saved.")) ?>');
    }
  });

  request.send();
};

/* Change the layout */
var changeCurrentLayoutType = function(type) {
  var availableAreas = ['top', 'bottom', 'left', 'middle', 'right'];
  var types = type.split(',');


  // Build negative areas
  var negativeAreas = [];
  availableAreas.each(function(currentAvailableArea) {
    if( !types.contains(currentAvailableArea) ) {
      negativeAreas.push(currentAvailableArea);
    }
  });

  // Build positive areas
  var positiveAreas = [];
  types.each(function(currentType) {
    var el = document.getElement('.pages_content_container_'+currentType);
    if( !el ) {
      positiveAreas.push(currentType);
    }
  });

  // Check to see if any columns containing widgets are going to be destroyed
  var contentLossCount = 0;
  negativeAreas.each(function(currentType) {
    var el = document.getElement('.pages_content_container_'+currentType);
    if( el && el.getChildren().length > 0 ) {
      contentLossCount++;
    }
  });

  // Notify user of potential data loss
  if( contentLossCount > 0 ) {
  <?php $replace = $this->translate("Changing to this layout will cause %s area(s) containing widgets to be destroyed. Are you sure you want to continue?", "' + contentLossCount + '") ?>
    if( !confirm('<?php echo $this->string()->escapeJavascript($replace) ?>') ) {
      return false;
    }
  }

  // Destroy areas
  negativeAreas.each(function(currentType) {
    var el = document.getElement('.pages_content_container_'+currentType);
    if( el ) {
      el.destroy();
    }
  });

  // Create areas
  var levelOneReference = document.getElement('.pages_layoutbox table.pages_content_container_main');

  // Create level one areas
  $H({'top' : 'before', 'bottom' : 'after'}).each(function(placement, currentType) {
    if( !positiveAreas.contains(currentType) ) return;

    var newTable = new Element('table', {
      'id' : 'pages_content_new_' + (newContentIndex++),
      'class' : 'pages_content_block pages_content_buildable pages_content_container_' + currentType
    }).inject(levelOneReference, placement);

    var newTbody = new Element('tbody', {
    }).inject(newTable);

    var newTr = new Element('tr', {
    }).inject(newTbody);

    // L2
    var newTdContainer = new Element('td', {
      'id' : 'pages_content_new_' + (newContentIndex++),
      'class' : 'pages_content_column pages_content_buildable pages_content_container_middle'
    }).inject(newTr);

    // L3
    var newUlContainer = new Element('ul', {
      'class' : 'pages_content_sortable'
    }).inject(newTdContainer);

    ContentSortables.addLists(newUlContainer);
  });

  // Create level two areas
  var mainParent = document.getElement('.pages_layoutbox .pages_content_container_main tr');
  $H({'left' : 'top', 'right' : 'bottom'}).each(function(placement, currentType) {
    if( !positiveAreas.contains(currentType) ) return;

    // L2
    var newTdContainer = new Element('td', {
      'id' : 'pages_content_new_' + (newContentIndex++),
      'class' : 'pages_content_column pages_content_buildable pages_content_container_' + currentType
    }).inject(mainParent, placement);

    // L3
    var newUlContainer = new Element('ul', {
      'class' : 'pages_content_sortable'
    }).inject(newTdContainer);

    ContentSortables.addLists(newUlContainer);
  });

  // Signal modification
  pushModification('main');
}

/* Tab container and other special block handling */
var expandSpecialBlock = function(element)
{
  if( element.hasClass('pages_content_widget_core.container-tabs') ) {
    element.addClass('pages_layoutbox_widget_tabbed_wrapper');
    // Empty
    element.empty();
    // Title/edit
    new Element('span', {
      'class' : 'pages_layoutbox_widget_tabbed_top',
      'html' : '<?php echo $this->string()->escapeJavascript($this->translate("Tabbed Blocks")) ?><span class="open"> | <a href=\'javascript:void(0);\' onclick="openWidgetParamEdit(\'core.container-tabs\', $(this).getParent(\'li.pages_content_cell\')); (new Event(event).stop()); return false;"><?php echo $this->string()->escapeJavascript($this->translate("edit")) ?></a></span> <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>'
    }).inject(element);
    // Desc
    new Element('span', {
      'class' : 'pages_layoutbox_widget_tabbed_overtext',
      'html' : contentByName["core.container-tabs"].childAreaDescription
    }).inject(element);
    // Edit area
    var tmpDivContainer = new Element('div', {
      'class' : 'pages_layoutbox_widget_tabbed'
    }).inject(element);
    var list = new Element('ul', {
      'class' : 'sortablesForceInclude pages_content_sortable pages_layoutbox_widget_tabbed_contents'
    }).inject(tmpDivContainer);

    ContentSortables.addLists(list);
  }
}

/* Checks for autoEdit */
var checkForAutoEdit = function(element) {
  var m = element.get('class').match(/pages_content_widget_([^ ]+)/i);
  if( $type(m) && $type(m[1]) ) {
    if( $type(contentByName[m[1]].autoEdit) && contentByName[m[1]].autoEdit ) {
      openWidgetParamEdit(m[1], element);
    }
  }
}

/* This will hide (or show) the global layout for this page */
var toggleGlobalLayout = function(element) {
  pushModification('main');

  var headerContainer = $$('div.pages_layoutbox_header');
  var footerContainer = $$('div.pages_layoutbox_footer');

  // Hide
  if( currentLayout == 'default' || currentLayout == '' ) {
    headerContainer.addClass('pages_layoutbox_header_hidden');
    footerContainer.addClass('pages_layoutbox_footer_hidden');
    headerContainer.getElement('a').set('html', '(show on this page)');
    footerContainer.getElement('a').set('html', '(show on this page)');
    currentLayout = 'default-simple';
  }

  // Show
  else
  {
    headerContainer.removeClass('pages_layoutbox_header_hidden');
    footerContainer.removeClass('pages_layoutbox_footer_hidden');
    headerContainer.getElement('a').set('html', '(hide on this page)');
    footerContainer.getElement('a').set('html', '(hide on this page)');
    currentLayout = 'default';
  }
}

/* Delete the current page */
var deleteCurrentPage = function() {

  if( !confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this page?")) ?>') ) {
    return false;
  }

  var redirectUrl = '<?php echo $this->url(array()) ?>';
  var url = '<?php //echo $this->url(array('action' => 'delete', 'page' => $this->pageObject->getIdentity()), 'page_editor', true); ?>';
  var request = new Request.JSON({
    'url' : url,
    'data' : {
      'format' : 'json',
      'page' : currentPage
    },
    onComplete : function(responseJSON) {
      window.location.href = redirectUrl;
    }
  });

  request.send();
};

var currentEditingElement;
var openWidgetParamEdit = function(name, element) {
  //event.stop();

  currentEditingElement = $(element);
  var content_id;
  if( element.get('id').indexOf('pages_content_new_') !== 0 && element.get('id').indexOf('pages_content_') === 0 ) {
    content_id = element.get('id').replace('pages_content_', '');
  }

  var url = '<?php echo $this->url(array('action' => 'widget', 'page' => $this->pageObject->getIdentity()), 'admin_editor', true)?>';
  var urlObject = new URI(url);

  var fullParams = element.retrieve('contentParams');
  if( $type(fullParams) && $type(fullParams.params) ) {
    //urlObject.setData(fullParams.params);
  }

  urlObject.setData({'name' : name}, true);

  Smoothbox.open(urlObject.toString());
}

</script>



<h2><?php echo $this->translate("Default Layout Editor"); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<?php echo $this->translate("PAGE_ADMIN_default layout editor"); ?>

<div id='pages_cms_wrapper'>

<div class="pages_layoutbox_menu">
	<ul>
		<?php if ($this->pageObject) : ?>
		<li id="pages_layoutbox_menu_savechanges">
      <a href="javascript:void(0);" onClick="saveChanges()">
        <?php echo $this->translate("Save Changes") ?>
      </a>
    </li>
    <?php endif; ?>
	</ul>
</div>

<div class="pages_layoutbox_wrapper">

  <div class="pages_layoutbox_sub_menu">
    <h3><?php echo $this->translate("Page Block Placement"); ?></h3>
    <ul>
   		<?php if( $this->pageObject->name !== 'header' && $this->pageObject->name !== 'footer'): ?>
   		<li class="pages_layoutbox_menu_generic" id="pages_layoutbox_menu_editcolumns">
   			<div class="pages_layoutbox_menu_wrapper_generic pages_layoutbox_menu_columnchoices_wrapper" id="pages_layoutbox_menu_columnchoices_wrapper">
          <div class="pages_layoutbox_menu_columnchoices">
            <div class="pages_layoutbox_menu_columnchoices_instructions">
              <?php echo $this->translate("Select a new column layout for this page.") ?>
            </div>
            <ul class="pages_layoutbox_menu_columnchoices_thumbs">
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols1_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols1_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols1_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols1_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle');switchPageMenu(new Event(event), $(this));")) ?></li>
            </ul>
            <ul class="pages_layoutbox_menu_columnchoices_thumbs">
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols2_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('top,left,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols2_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('top,left,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols2_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('top,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols2_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('top,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
            </ul>
            <ul class="pages_layoutbox_menu_columnchoices_thumbs">
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols3_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols3_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols3_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
              <li><?php echo $this->htmlImage('application/modules/Core/externals/images/content/cols3_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
            </ul>
            <div class="pages_layoutbox_menu_columnchoices_cancel">
              Or, <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a> <?php echo $this->translate("and keep your current layout.") ?>
            </div>
          </div>
        </div>
        <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Columns") ?></a>
   		</li>
    	<?php endif ;?>
    </ul>
  </div>

  <?php if( $this->pageObject->name != 'header' && $this->pageObject->name != 'footer' ): ?>
  <div class='pages_layoutbox'>
  	<div class='pages_layoutbox_header<?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? '' : ' pages_layoutbox_header_hidden' ) ?>'>
      <span>
        <?php echo $this->translate("Global Header"); ?>
      </span>
    </div>

    <?php // LEVEL 0 - START (SANITY) ?>
    <?php
      ob_start();
      try {
    ?>

      <?php
        // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
        <table id="pages_content_<?php echo $structOne['identity'] ?>" class="pages_content_block pages_content_buildable pages_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
          <tbody>
            <tr>
              <script type="text/javascript">
                window.addEvent('domready', function() {
                  $("pages_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                });
              </script>
              <?php
                // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                <td id="pages_content_<?php echo $structTwo['identity'] ?>" class="pages_content_column pages_content_buildable pages_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("pages_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                    });
                  </script>
                  <ul class="pages_content_sortable">
                    <?php
                      // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("pages_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                        });
                      </script>
                      <?php if( empty($structThreeInfo) ): // Missing widget ?>
                        <li id="pages_content_<?php echo $structThree['identity'] ?>" class="disabled pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate('Missing widget: %s', $structThree['name']) ?>
                          <span class="open"></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                        <li id="pages_content_<?php echo $structThree['identity'] ?>" class="pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate($this->contentByName[$structThree['name']]['title']); ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit'); ?></a></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php else: ?>
                        <!-- tabbed widgets -->
                        <li id="pages_content_<?php echo $structThree['identity'] ?>" class="pages_content_cell pages_content_buildable pages_content_draggable pages_layoutbox_widget_tabbed_wrapper pages_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                          <span class="pages_layoutbox_widget_tabbed_top">
                            <?php echo $this->translate('Tabbed Blocks'); ?>
                            <span class="open">
                              <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit'); ?></a>
                            </span>
                            <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                          </span>
                          <span class="pages_layoutbox_widget_tabbed_overtext">
                            <?php echo $this->translate($structThreeInfo['childAreaDescription']); ?>
                          </span>
                          <div class="pages_layoutbox_widget_tabbed">
                            <ul class="sortablesForceInclude pages_content_sortable pages_layoutbox_widget_tabbed_contents">
                              <?php
                                // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                  <script type="text/javascript">
                                  window.addEvent('domready', function() {
                                    $("pages_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                  });
                                </script>
                                <?php if( empty($structFourInfo) ): ?>
                                  <li id="pages_content_<?php echo $structFour['identity'] ?>" class="disabled pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                    <?php echo $this->translate('Missing widget: %s', $structFour['name']) ?>
                                    <span></span>
                                  </li>
                                <?php else: ?>
                                  <li id="pages_content_<?php echo $structFour['identity'] ?>" class="pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                    <?php echo $this->translate($this->contentByName[$structFour['name']]['title']); ?>
                                    <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit'); ?></a></span>
                                    <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                                  </li>
                                <?php endif; ?>
                              <?php
                                endforeach;
                                    // LEVEL 4 - END
                                  ?>
                            </ul>
                          </div>
                        </li>
                        <!-- end tabbed widgets -->
                      <?php endif; ?>

                    <?php
                      endforeach;
                          // LEVEL 3 - END
                        ?>

                  </ul>
                </td>
              <?php
                endforeach;
                    // LEVEL 2 - END
                  ?>

            </tr>
          </tbody>
        </table>
      <?php
        endforeach;
            // LEVEL 1 - END
          ?>

    <?php // LEVEL 0 - END (SANITY) ?>
    <?php
      ob_end_flush();
      } catch( Exception $e ) {
        ob_end_clean();
        echo "An error has occurred.";
      }
    ?>

    <div class='pages_layoutbox_footer<?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? '' : ' pages_layoutbox_footer_hidden' ) ?>'>
      <span>
        <?php echo $this->translate("Global Footer"); ?>
      </span>
    </div>

  </div>

  <?php else: ?>

  <div class='pages_layoutbox'>
    <?php if( $this->pageObject->name == 'footer' ): ?>
      <div class='pages_layoutbox_header'>
        <span><?php echo $this->translate('Global Header'); ?></span>
      </div>
    <?php else: ?>
      <?php
        // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
        <table id="pages_content_<?php echo $structOne['identity'] ?>" class="pages_content_block pages_content_block_headerfooter pages_content_buildable pages_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
          <tbody>
            <tr>
              <td class="pages_content_column_headerfooter">
                <span class="pages_layoutbox_note">
                  <?php echo $this->translate("Drop things here to add them to the global header."); ?>
                </span>
                <script type="text/javascript">
                  window.addEvent('domready', function() {
                    $("pages_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                  });
                </script>
                <ul class="pages_content_sortable">
                  <?php
                    // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("pages_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                      });
                    </script>
                    <li id="pages_content_<?php echo $structThree['identity'] ?>" class="pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?> <?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                      <?php echo $this->translate($this->contentByName[$structThree['name']]['title']); ?>
                      <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit'); ?></a></span>
                      <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
            </tr>
          </tbody>
        </table>
      <?php
        endforeach;
            // LEVEL 1 - END
          ?>
    <?php endif; ?>

    <div class='pages_layoutbox_center_placeholder'>
      <span><?php echo $this->translate("Main Content Area") ?></span>
    </div>

    <?php if( $this->pageObject->name == 'header' ): ?>
    <div class='pages_layoutbox_footer'>
      <span><?php echo $this->translate("Global Footer") ?></span>
    </div>
    <?php else: ?>
      <?php
        // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
        <table id="pages_content_<?php echo $structOne['identity'] ?>" class="pages_content_block pages_content_block_headerfooter pages_content_buildable pages_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
          <tbody>
            <tr>
              <td class="pages_content_column_headerfooter">
                <span class="pages_layoutbox_note">
                  <?php echo $this->translate("Drop things here to add them to the global footer.") ?>
                </span>
                <script type="text/javascript">
                  window.addEvent('domready', function() {
                    $("pages_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                  });
                </script>
                <ul class="pages_content_sortable">
                  <?php
                    // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("pages_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                      });
                    </script>
                    <li id="pages_content_<?php echo $structThree['identity'] ?>" class="pages_content_cell pages_content_buildable pages_content_draggable pages_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                      <?php echo $this->translate($this->contentByName[$structThree['name']]['title']); ?>
                      <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                      <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
            </tr>
          </tbody>
        </table>
      <?php
        endforeach;
            // LEVEL 1 - END
          ?>
    <?php endif; ?>
  </div>

  <?php endif; ?>

</div>

<div class="pages_layoutbox_pool_wrapper">
  <h3><?php echo $this->translate("Available Blocks") ?></h3>
  <div class='pages_layoutbox_pool'>
    <div id='stock_div'></div>
    <ul id='column_stock'>
      <?php foreach( $this->contentAreas as $category => $categoryAreas ): ?>
        <li>
          <div class="pages_layoutbox_pool_category_wrapper" onclick="$(this); this.getParent('li').getElement('ul').style.display = ( this.getParent('li').getElement('ul').style.display == 'none' ? '' : 'none' );">
            <div class="pages_layoutbox_pool_category">
              <div class="pages_layoutbox_pool_category_hide">
                &nbsp;
              </div>
              <div class="pages_layoutbox_pool_category_label">
                <?php echo $this->translate($category); ?>
              </div>
            </div>
          </div>
          <ul class='pages_content_sortable pages_content_stock_sortable'>
            <?php foreach( $categoryAreas as $info ):
                $class = 'pages_content_widget_' . $info['name'];
                $class .= ' pages_content_draggable pages_content_stock_draggable';
                $onmousedown = false;
                if( !empty($info['disabled']) ) {
                  $class .= ' disabled';
                  if( !empty($info['requireItemType']) ) {
                    $onmousedown = 'alert(\'Disabled due to missing item type(s): '.join(', ', (array)$info['requireItemType']) . '\'); return false;';
                  } else {
                    $onmousedown = 'alert(\'Disabled due to missing dependency.\'); return false;';
                  }
                }
                if( !empty($info['special']) ) {
                  $class .= ' htmlblock special';
                }
                if( !empty($info['pagesCssClass']) ) {
                  $class .= ' ' . $info['pagesCssClass'];
                }

                ?>
              <?php //if( empty($info['canHaveChildren']) ): ?>
                <li class="<?php echo $class ?>" title="<?php echo $this->escape($this->translate($info['description'])) ?>"<?php if( $onmousedown ): ?> onmousedown="<?php echo $onmousedown ?>"<?php endif; ?>>
                    <?php echo $this->translate($info['title']); ?>
                  <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $info['name'] ?>', $(this).getParent('li.pages_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                  <span class="remove"><a href='javascript:void(0);' onclick="removeWidget($(this));">x</a></span>
                </li>
              <?php /* //else: ?>
                <li class="pages_layoutbox_widget_tabbed_wrapper">
                  <span class="pages_layoutbox_widget_tabbed_top">
                    <?php echo $this->translate("Tabbed Blocks"); ?> <a href="#">(<?php echo $this->translate("edit"); ?>)</a>
                  </span>
                  <div class="pages_layoutbox_widget_tabbed">
                    <ul class="pages_layoutbox_widget_tabbed_contents">
                      <?php echo $structThreeInfo['childAreaDescription'] ?>
                    </ul>
                  </div>
                </li>
              <?php //endif; */ ?>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
  
</div>

<br />
<br />
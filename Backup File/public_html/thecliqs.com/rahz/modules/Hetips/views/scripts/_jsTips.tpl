<script type="text/javascript">
  var tipsSort;
  en4.core.runonce.add(function(){

    $('subjectOption').removeEvents().addEvent('change', changeOption);

    $('addTips').removeEvents().addEvent('click', addTip);

    $$('.tip_delete > a').removeEvents().addEvent('click', deleteTip);

    $$('input[name*=_how_display]').addEvent('change', changeDisplayTips);

    changeDisplayTips();

    if(!tipsSort){
        if($$('.tips_list > li > span').length > 0){
            tipsSort = new Sortables($$('.tips_list'),{
            clone:true,
            constrain: true,
            opacity:0.2,
            onComplete:saveTip});
        }
    }
    else{
        tipsSort.removeLists(dragTip.lists);
        tipsSort.addLists($$('.tips_list'));
    }

  });

  var changeDisplayTips = function (){
    var element = $$('input[id*=_show_labels]')[0];
    if($$('input[name*=_how_display]')[0].checked){
      element.checked = false;
      element.disabled = true;
    } else {
      element.disabled = false;
      element.checked = true;
    }
  }

  var changeOption = function(event){
    var option_id = $(event.target).value;
    var url = new URI(window.location);
    url.setData({option_id:option_id});
    window.location = url;
  }

  var addTip = function(){
    var url = "<?php echo $this->url(array('action' => 'add-tip')) ?>";
    var option_id = $('subjectOption').value;
    var tip_id = $('subjectTips').value;
    var request = new Request.JSON({
      url:url,
      data:{
        format:'json',
        option_id:option_id,
        tip_id:tip_id
      },
      onComplete:function(newTip){
        onCreateTip(newTip.html);
      },
      onSuccess:saveTip
    });
    request.send();
  }

   var saveTip = function(){
     var url = '<?php echo $this->url(array('action' => 'order-tips')); ?>';
     var request = new Request.JSON({
         url:url,
         data:{
           format:'json',
           tips_ids:orderTips()
         }
     });
     request.send();
  }

  var deleteTip = function(){
    var tip_id = this.getParent('span').id;
    var url = "<?php echo $this->url(array('action' => 'delete-tip')) ?>";
    var request = new Request.JSON({
      url:url,
      data:{
        format:'json',
        tip_id:tip_id
      },
      onSuccess:this.getParent('li').destroy(),
      onComplete:function(){
        var tipsList = $$('ul.tips_list > li');
        var parentElement = $$('ul.tips_list')[0];
        var html = '<span>In this category are no tips! </span>';

        if (tipsList.length <= 0) {
            var el = new Element('div', {html:html, class:'tip'});
            el.inject(parentElement);
        }
      }
    });
    request.send();
  }

  var onCreateTip = function(htmlNewTip){
    var parentElement = $$('.tips_list')[0];
    var el = new Element('li', {html:htmlNewTip});

    el.getChildren("li").each(function(item){
        if (item.get('html') == '') return;
        if ($$('.tip')) $$('.tip').destroy();
        item.inject(parentElement, 'bottom');
    });

    if($$('.tips_list > li > span').length > 0){
        tipsSort = new Sortables($$('.tips_list'),{
        clone:true,
        constrain: true,
        opacity:0.2,
        onComplete:saveTip});
    }

    $('subjectOption').removeEvents().addEvent('change', changeOption);
    $$('.tip_delete > a').removeEvents().addEvent('click', deleteTip);
  }

  var orderTips = function(){
    var order = [];
    $$('.tips_list span').each(function(element){
      var ids = element.get('id');
      order.push(ids);
    });
    
    return order;
  }
</script>
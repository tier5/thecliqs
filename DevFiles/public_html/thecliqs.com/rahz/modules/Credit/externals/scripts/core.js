/* $Id: core.js 09.01.12 15:58 TeaJay $ */

var credit_manager = {
  started : false,
	init : function() {
		var self = this;
    self.started = true;
    if ($('send_credit_form') != undefined) {
      en4.core.runonce.add(function() {
        $('send').addEvent('click', function(e) {
          e.stop();
          self.sendCredits();
        });
      });
    }

    if ($('buy_credit_form') != undefined) {
      en4.core.runonce.add(function() {
        $('buy').addEvent('click', function(e) {
          e.stop();
          self.buyCredits();
        });
      });
    }

    if ($('buy_level_form') != undefined) {
      en4.core.runonce.add(function() {
        $('buy_level').addEvent('click', function(e) {
          e.stop();
          self.buyLevel();
        });
      });
    }
	},

  sendCredits: function() {
    var self = this;
    $('credit_loader').removeClass('hidden');
    new Request.JSON({
      url : self.action_url,
      data : {
        format: 'json',
        user_id: $('user_id').value,
        credit: $('credit').value
      },
      onComplete: function(data) {
        if (data.result) {
          he_show_message(data.message);
          self.resetData();
          setTimeout(function(){parent.location.href=parent.location.href;}, 3000);
        } else {
          he_show_message(data.message, 'error');
        }
        $('credit_loader').addClass('hidden');
      }
    }).send();
  },

  resetData : function() {
    $('user_id').value = 0;
    $('credit').value = '';
    $('username').value = '';
  },

  addLoader: function()
  {
    $('credit_loader_browse').removeClass('hidden');
  },

  removeLoader: function()
  {
    $('credit_loader_browse').addClass('hidden');
  },

  buyCredits: function() {
    var self = this;
    var url = self.buy_credits_url;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {mode: 'Request'});
  },

  buyLevel: function() {
    var already_paid = $('already_paid').getProperty('value');
    if (already_paid == 1) {
      return false;
    }
    var self = this;
    var package_id = 0;

    $$('input[type="radio"]').each(function(element) {
      if (element.checked == true) {
        package_id = element.value.toInt();
      }
    });

    var url = self.buy_level_url+'/package_id/'+package_id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {mode: 'Request'});
  }
}
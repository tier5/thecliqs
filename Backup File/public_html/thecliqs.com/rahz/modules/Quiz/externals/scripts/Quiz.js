
/* $Id: Quiz.js 2010-05-25 01:44 ermek $ */

var quiz =
{
  edit_result_url: '',
  delete_result_url: '',
  firstMatchCount: 0,
  secondMatchCount: 0,
  firstMax: 12,
  secondMax: 20,
  firstMatches: [],
  secondMatches: [],

  construct: function()
  {
  },
  
  manage_result: function()
  {
    var self = this;
        
    $$('.view_quiz_result .delete_result_btn').addEvent('click', function()
    {
      var $result_node = $(this).getParent('.view_quiz_result');
      var result_id = $result_node.getProperty('id').substr(7);

      self.delete_result_id = result_id;
      self.$delete_result_node = $result_node;

      var title = self.translate('Are you sure you want to delete this result?');
      var description = self.translate('<b>WARNING</b>: This will also delete all the answers associated with this result!');
      
      he_show_confirm(title, description, function(){
        self.delete_result();  
      });
    });
    
    $$('.view_quiz_result .edit_result_btn').addEvent('click', function()
    {
      var $result_node = $(this).getParent('.view_quiz_result');
      var result_id = $result_node.getProperty('id').substr(7);
      
      window.location.href = self.edit_result_url.replace('result_id', result_id);
    });
  },
  
  delete_result: function()
  {
    var self = this;
    var delete_url = this.delete_result_url.replace('result_id', this.delete_result_id);
    
    en4.core.request.send(new Request.JSON({
      url : delete_url,
      data: {format: 'json'},
      onSuccess : function(result){
        Smoothbox.close();
        
        if (result && result.status) {
          self.$delete_result_node.dispose();
          self.$delete_result_node = null;
          self.delete_result_id = 0;

          var result_count = $$('.view_quiz_results .view_quiz_result').length;

          if (result_count == 0) {
            $('tip_cont_tpl').removeClass('display_none');
            $('add_another_result_btn').addClass('display_none');
            $('add_result_btn').removeClass('display_none');
          }

          he_show_message(result.message);
        }
      }
    }));
  },
  
  manage_question: function()
  {
    var self = this;
    
    $$('.view_quiz_question .delete_question_btn').addEvent('click', function()
    {
      var $question_node = $(this).getParent('.view_quiz_question');
      var question_id = $question_node.getProperty('id').substr(9);
      
      self.delete_question_id = question_id;
      self.$delete_question_node = $question_node;

      var title = self.translate('quiz_Are you sure you want to delete this question?');
      var message = self.translate('<b>WARNING</b>: This will also delete all the answers associated with this question!');

      he_show_confirm(title, message, function(){
        self.delete_question();
      });
    });
    
    $$('.view_quiz_question .edit_question_btn').addEvent('click', function()
    {
      var $question_node = $(this).getParent('.view_quiz_question');
      var question_id = $question_node.getProperty('id').substr(9);
      
      window.location.href = self.edit_question_url.replace('question_id', question_id);
    });
    
  },
  
  delete_question: function()
  {
    var self = this;
    var delete_url = this.delete_question_url.replace('question_id', this.delete_question_id);
    
    en4.core.request.send(new Request.JSON({
      url : delete_url,
      data: {format: 'json'},
      onSuccess : function(question){
        Smoothbox.close();
        
        if (question && question.status) {
          self.$delete_question_node.dispose();
          self.$delete_question_node = null;
          self.delete_question_id = 0;

          var question_count = $$('.view_quiz_questions .view_quiz_question').length;

          if (question_count == 0) {
            $('tip_cont_tpl').removeClass('display_none');
            $('add_another_question_btn').addClass('display_none');
            $('add_question_btn').removeClass('display_none');
          }

          he_show_message(question.message);
        }
      }
    }));
  },
  
  manage_publish: function()
  {
    $('publish').addEvent('click', function()
    {
      $('published').value = 1;
      $('publish_quiz').submit();
    });
    
    $('unpublish').addEvent('click', function()
    {
      $('published').value = 0;
      $('publish_quiz').submit();
    });
  },

  manage_navigation: function(step_info)
  {
    if (step_info && step_info.next_error && $('quiz_next_btn')) {
      $('quiz_next_btn').addEvent('click', function() {
        he_show_message(step_info.message, 'error');
      });
    } else if (step_info && !step_info.next_error && step_info.next && $('quiz_next_btn')) {
      $('quiz_next_btn').addEvent('click', function() {
        window.location.href = step_info.next;
      });
    }

    if (step_info && step_info.error) {
      he_show_message(step_info.message, 'error');
      
      window.setTimeout(function() {
        window.location.href = step_info.redirect;
      }, 3000);
    }
    
    var $disabled_pages = $$('.quiz_tabs .disabled');
    if (!$disabled_pages || $disabled_pages.length == 0) {
      return false;
    }
    
    $disabled_pages.addEvent('click', function(event) {
      event.stop();
      
      he_show_message(step_info.message, 'error');
    });
  },
  
  take_quiz: function()
  {
    var self = this;
    this.left = 0;
    this.current = 1;
    this.progress_bar = 160;
    this.width = 600;
    
    this.$questions = $$('.take_quiz .quiz_question');
    this.count = this.$questions.length;
    this.$container = $$('.take_quiz .form-elements')[0];
    this.$question_number = $('answered_questions');
    this.$progress_status = $$('.quiz_progress_bar .progress_status');
    this.$progress_line = $$('.quiz_progress_bar .progress_line')[0];

    this.$container.set('tween', {'duration': 400});
    this.$progress_status.set('tween', {'duration': 400});
    this.$progress_line.set('tween', {'duration': 400});
    
    this.$container.getElements('.form-options-wrapper li').removeClass('checked');
    this.$container.getElements('.form-options-wrapper .quiz_answer').setProperty('checked', false);

    this.answer_checked = false;
    this.$container.getElements('.form-options-wrapper li').addEvent('click', function()
    {
      if (self.answer_checked) {
        return;
      }
      self.answer_checked = true;
      
      var $answers = this.getParent('.form-options-wrapper').getElements('li');
      $answers.removeClass('checked');
      this.addClass('checked');
      this.getElement('.quiz_answer').setProperty('checked', true);
      
      window.setTimeout(function(){
        self.update_progress_bar();
        
        if (self.check_take_result(true)) {
          
          he_show_message(self.translate('You successfully passed the quiz.'));
          
          $('quiz_take').submit();
          return;
        }
        
        self.move_question('right');
        self.answer_checked = false; 
      }, 100);
    });
    
    $('move_left').addEvent('click', function()
    {
      self.move_question('left');
      this.blur();
    });    
    
    $('move_right').addEvent('click', function()
    {
      self.move_question('right');
      this.blur();
    });    
  },
  
  move_question: function(direction)
  {
    switch (direction) {
    case 'right':
      var dir = 1;
      break;
    case 'left':
      var dir = -1;
      break;

    default:
      var dir = 0;
      break;
    }
    
    var left = this.left - this.width * dir;
    var start = this.left;
    
    var current = this.current + dir;
    
    if (current == 0) {
      current = this.count;
      left = this.width * (1 - this.count);
    } else if (current > this.count) {
      current = 1;
      left = 0;
    }

    var cont_left = left;
    try {
      if (this.$container.getStyle('direction') == 'rtl') {
        cont_left = (-1)*left;
      }
    } catch (e) {}

    this.$container.tween('left', [start, cont_left]);
    this.$question_number.set('html', current);
    
    this.left = left;
    this.current = current;
  },
  
  check_take_result: function(check_only)
  {
    var answers_count = this.$container.getElements('.form-options-wrapper li.checked').length;
    
    if (this.count == answers_count) {
      return true;
    } else if (check_only) {
      return false;
    }
    
    var error_question = 0;
    for ( var i = 0; i < this.count; i++) {
      var $question = this.$questions[i];
      
      if ($question.getElements('.form-options-wrapper li.checked').length == 0) {
        error_question = i + 1;
        break;
      }
    }
    
    if (this.current > error_question) {
      var count = this.current - error_question;
      for ( var i = 0; i < count; i++) {
        this.move_question('left');
      }
    } else if (this.current < error_question) {
      var count = error_question - this.current;
      for ( var i = 0; i < count; i++) {
        this.move_question('right');
      }
    }
    
    return false;
  },
  
  get_take_result: function()
  {
    if (!this.check_take_result()) {
      he_show_message(this.translate('quiz_Please answer for all questions!'), 'error');
      return false;
    }
    
    he_show_message(this.translate('You successfully passed the quiz.'));
    
    $('quiz_take').submit();
  },
  
  update_progress_bar: function()
  {
    var self = this;
    var answers_count = this.$container.getElements('.form-options-wrapper li.checked').length; 
    var percent = answers_count * this.progress_bar / this.count;
    
    if (this.progress_bar < percent) {
      return false;
    }
    
    if (percent > 0) {
      $$('.quiz_progress_bar .progress_invite_text').dispose();
    }
    
    this.$progress_status.tween('width', percent);
    this.$progress_line.tween('left', percent);
    
    if (this.progress_bar == percent) {
      window.setTimeout(function() {
        self.$progress_line.setStyle('width', 0);
      }, 400);
    }
  },

  view_quiz: function(quiz_id)
  {
    var self = this;

    this.prepare_matches();
    this.prepare_match_boxes();
    this.show_matches();

    this.$quiz_tabs = $$('.view_quiz_tabs .quiz_tab');
    this.$quiz_contents = $$('.quiz_content');

    this.$quiz_tabs.addEvent('click', function()
    {
      var $node = $(this);
      var tab = $node.getProperty('id').substr(5);
      var $content = $('content-' + tab);

      if (!$content) {
        return false;
      }

      $node.blur();

      $$('.view_quiz_tabs li.active').removeClass('active');
      self.$quiz_contents.addClass('display_none');

      $node.getParent('li').addClass('active');
      $content.removeClass('display_none');
    });
  },

  prepare_matches: function()
  {
    var matches = [];
    var page_items = [];
    var page = 0;
    for (var i = 0; i < this.firstMatchCount; i++) {

      if (i == this.firstMax * (page + 1)) {
        matches.push(page_items);
        page_items = [];
        page++;
      }
      page_items.push(this.firstMatches[i]);
    }
    matches.push(page_items);
    this.firstMatches = matches;

    var matches = [];
    var page_items = [];
    var page = 0;
    for (var i = 0; i < this.secondMatchCount; i++) {

      if (i == this.secondMax * (page + 1)) {
        matches.push(page_items);
        page_items = [];
        page++;
      }
      page_items.push(this.secondMatches[i]);
    }
    matches.push(page_items);
    this.secondMatches = matches;
  },
  
  prepare_match_boxes: function()
  {
    var $container = $('content-matches').getElement('.user_matches');

    if (!$container) {
      return;
    }

    var $firstMatches = $container.getElements('.main_first_row .user_match');
    var $secondMatches = $container.getElements('.first_row .user_match');

    this.$firstMatches = $firstMatches;
    this.$secondMatches = $secondMatches;

    var $overlay = new Element('div', {'class': 'user_match_overlay'});
    $overlay.set('tween', {'duration': 300});

    $container.getElements('.user_match').each(function(element, index){
      element.grab($overlay.clone());
    });

    $container.getElements('.main_side_column')[1].getElements('.user_match').each(function(element){
      $firstMatches.push(element);
    });

    $container.getElements('.main_third_row .user_match').reverse().each(function(element){
      $firstMatches.push(element);
    });

    $container.getElements('.main_side_column')[0].getElements('.user_match').reverse().each(function(element){
      $firstMatches.push(element);
    });

    $container.getElements('.side_column')[1].getElements('.user_match').each(function(element){
      $secondMatches.push(element);
    });

    $container.getElements('.third_row .user_match').reverse().each(function(element){
      $secondMatches.push(element);
    });

    $container.getElements('.side_column')[0].getElements('.user_match').reverse().each(function(element){
      $secondMatches.push(element);
    });
  },

  show_matches: function()
  {
    this.prepare_paging();

    if (this.firstMatchCount == 0 && this.secondMatchCount == 0) {
      return;
    }

    if (this.firstMatchCount < this.firstMax) {
      this.show_random(1);
    } else {
      this.show_list(1);
    }
    
    if (this.secondMatchCount < this.secondMax) {
      this.show_random(2);
    } else {
      this.show_list(2);
    }
  },

  show_list: function(level, direction)
  {
    direction = (direction == undefined) ? 1 : direction;
    var length = (level == 1) ? this.firstMax : this.secondMax;
    var items = (level == 1) ? this.firstMatches[this.first_page] : this.secondMatches[this.second_page];

    for (var i = 0; i < length; i++) {
      var index = (direction > 0) ? i : length - i - 1;
      var item_info = items[index];
      this.show_item(index, item_info, level, i*200);
    }
  },

  show_random: function(level)
  {
    var length = (level == 1) ? this.firstMax : this.secondMax;
    var queue_list = [];
    
    for (var i = 0; i < length; i++) {queue_list.push(i);}

    var items = (level == 1)
      ? this.shuffle_items(this.firstMatches[this.first_page])
      : this.shuffle_items(this.secondMatches[this.second_page]);
    
    var queue_list = this.shuffle_items(queue_list);

    for (var i = 0; i < items.length; i++) {
      var item_info = items[i];
      var box_index = queue_list[i];
      this.show_item(box_index, item_info, level, i*100);
    }
  },

  show_item: function(box_index, item_info, level, delay)
  {
    var self = this;
    window.setTimeout(function(){
      var $box = (level == 1) ? self.$firstMatches[box_index] : self.$secondMatches[box_index];

      //create item
      if (item_info) {
        var $item = new Element('a', {'href': item_info.url, 'title': item_info.username});

        if (item_info.photo) {
          var $photo = new Element('img', {'src': item_info.photo, 'class': 'thumb_icon match_photo'});
        } else {
          var $photo = $('user_match_nophoto_tpl').getElement('img').clone();
          $photo.setProperty('class', 'thumb_icon match_photo item_nophoto');
        }
        $item.grab($photo);
      }

      $box.getElement('.user_match_overlay').fade('in');
      window.setTimeout(function(){
        var $old_item = $box.getElement('a');
        if ($old_item) {$old_item.dispose();}
        if (item_info) {$box.grab($item);}
        
        $box.getElement('.user_match_overlay').fade('out');
      }, 300);
      
    }, delay);
  },

  prepare_paging: function()
  {
    var self = this;    
    this.first_page = 0;
    this.second_page = 0;
    
    var $container = $('content-matches').getElement('.user_matches');
    if (!$container) {
      return;
    }

    if (this.firstMatchCount <= this.firstMax) {
      $container.getElement('.first_matches_paging').addClass('display_none');
    } else {
      var $prev_btn = $container.getElement('.first_matches_paging .match_prev_btn');
      $prev_btn.addEvent('click', function(){        
        this.blur();
        self.change_page(1, -1, $(this));
      });

      var $next_btn = $container.getElement('.first_matches_paging .match_next_btn');
      $next_btn.addClass('next_active');
      $next_btn.addEvent('click', function(){
        this.blur();
        self.change_page(1, 1, $(this));
      });
    }

    if (this.secondMatchCount <= this.secondMax) {
      $container.getElement('.second_matches_paging').addClass('display_none');
    } else {
      $prev_btn = $container.getElement('.second_matches_paging .match_prev_btn');
      $prev_btn.addEvent('click', function(){
        this.blur();
        self.change_page(2, -1, $(this));
      });

      $next_btn = $container.getElement('.second_matches_paging .match_next_btn');
      $next_btn.addClass('next_active');
      $next_btn.addEvent('click', function(){
        this.blur();
        self.change_page(2, 1, $(this));
      });
    }
  },

  shuffle_items: function(items)
  {
    var new_items = [];
    var length = items.length;

    for (var i = 0; i < length; i++)
    {
        var item = items.getRandom();
        items.erase(item);

        new_items.push(item);
    }

    return new_items;
  },

  change_page: function(level, direction, $btn)
  {
    var page = (level == 1) ? this.first_page : this.second_page;
    var items = (level == 1) ? this.firstMatches[page + direction] : this.secondMatches[page + direction];
    var next_items = (level == 1) ? this.firstMatches[page + direction + 1] : this.secondMatches[page + direction + 1];
    var prev_items = (level == 1) ? this.firstMatches[page + direction - 1] : this.secondMatches[page + direction - 1];

    if ((direction < 0 && page == 0) || items == undefined) {
      return;
    }

    if (prev_items == undefined) {
      $btn.getParent().getElement('.match_prev_btn').removeClass('prev_active');
    } else {
      $btn.getParent().getElement('.match_prev_btn').addClass('prev_active');
    }

    if (next_items == undefined) {
      $btn.getParent().getElement('.match_next_btn').removeClass('next_active');
    } else {
      $btn.getParent().getElement('.match_next_btn').addClass('next_active');
    }

    if (level == 1) {
      this.first_page += direction;
    } else {
      this.second_page += direction;
    }

    this.show_list(level, direction);
  },

  translate: function(key) {
    if (typeof(en4.core.language.translate) == 'undefined') {
      return language.translate(key);
    } else {
      return en4.core.language.translate(key);
    }
  }
};
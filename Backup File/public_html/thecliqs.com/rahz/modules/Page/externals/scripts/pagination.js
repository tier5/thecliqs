var WidgetPagination = new Class({

  Implements: [Options],

  currentPage: '1',
  data: '',

  initialize: function(options)
  {
    this.setOptions(options);
    var self = this;

    if(self.options.totalPage == '1') {
      self.options.nextElement.addClass('hidden');
    }

    self.options.nextElement.addEvent('click', function(){
      if( self.currentPage < self.options.totalPage ) {
        self.currentPage++;
        self.data = {
          'page': self.currentPage
        };
        self.request(self.options.ajaxUrl, 'down');
      }
    });

    self.options.previousElement.addEvent('click', function(){
      if( self.currentPage > 1 ) {
        self.currentPage--;
        self.data = {
          'page': self.currentPage
        };
        self.request(self.options.ajaxUrl, 'previous');
      }
    });
  },

  request: function (url, dir)
  {
    var self = this;
    if( dir == 'previous' ) {
      self.options.previousElement.addClass('hidden');
      self.options.previousLoadingImg.removeClass('hidden');
    } else {
      self.options.nextElement.addClass('hidden');
      self.options.nextLoadingImg.removeClass('hidden');
    }

    var request = new Request.HTML({
      url: url,
      data: self.data,
      onRequest: function()
      {

      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        new Fx.Tween('page_team_content_id').start('opacity', 1, 0).chain(function(){
          var elem = $$(self.options.items);
          var tElement = new Element('div', {'html': responseHTML});
          var arr = tElement.getElements(self.options.items);
          var len = arr.length;

          for(var i = 0; i < len; i++) {
            elem[self.options.perPage - len + i].innerHTML = arr[i].innerHTML;
          }

          if( dir == 'previous' ) {
            self.options.previousElement.removeClass('hidden');
            self.options.previousLoadingImg.addClass('hidden');
          } else {
            self.options.nextElement.removeClass('hidden');
            self.options.nextLoadingImg.addClass('hidden');
          }

          if( self.options.totalPage == self.currentPage ) {
            self.options.nextElement.addClass('hidden');
          } else {
            self.options.nextElement.removeClass('hidden');
          }

          if( 1 == self.currentPage ) {
            self.options.previousElement.addClass('hidden');
          } else {
            self.options.previousElement.removeClass('hidden');
          }
          new Fx.Tween('page_team_content_id').start('opacity', 0, 1);
        });

      }
    }).post();
  }
});

Pagination = {
  ajaxUrl: '',
  data: '',
  content_element: '',
  loader_element: '',

  init: function(url, content, loader)
  {
    this.ajaxUrl = url;
    this.content_element = content;
    this.loader_element = $(loader);
  },

  getPage: function(page_num)
  {
    var self = this;
    self.data = {
      'page': page_num
    };

    if( self.loader_element )
      self.loader_element.removeClass('hidden');

    this.request(this.ajaxUrl);
  },

  request: function (url)
  {
    var self = this;
    var request = new Request.HTML({
      url: url,
      data: self.data,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        var tElement = new Element('div', {'html': responseHTML});
        $$(self.content_element)[0].innerHTML = tElement.getElement(self.content_element).innerHTML;

        if( self.loader_element )
          self.loader_element.addClass('hidden');
      }
    }).post();
  }
};

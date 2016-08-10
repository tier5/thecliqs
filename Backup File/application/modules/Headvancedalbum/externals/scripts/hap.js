var hap = {

  Implements: [Options],

  options:{
    id:'hapPhotos', // id="hapPhotos" of general container
    build_timeout:10000, // auto build in 10 sec
    max_width:200, // max width of an image

    // TODO deprecated, now uses auto calc of container width
    //container_width:820, // total container width (needed for calculate)

    container_padding: 45, // plus to width of the container (when we are adding padding to items)
    loading_on_scroll:true, // enable paging on scroll
    request_url: '', // request url of paging
    request_params: {},
    build_min_count: 2 // min count on a line in order to build the line,
  },
  is_loading:false,
  timer:null,
  image_sizes:[],
  success_loaded: 0,
  failed_loaded: 0,
  need_load_count: 0,
  page: 1,
  is_end: 0,
  container_width: 0,

  isDebug: function ()
  {
    // Debug mode in order to don't see images from sh.. sites
    return (window.location.hash.indexOf('#debug') !== -1);
  },

  initialize:function (options) {

    var self = this;



    this.setOptions(options);

    // Our container
    this.e = $(this.options.id);

    // Hide all to after to build images
    this.e.getChildren().setStyle('visibility', 'hidden');

    // Events
    this.setup();

    self.bindRebuildOnLoadImages();
    self.bindRebuildOnTimeout();

    // save instance to window object
    window[this.options.id] = this;

  },

  error:function (msg) {

  },

  setup:function () {

    var self = this;

    if (this.options.loading_on_scroll) {

      // loading on scroll down
      window.addEvent('scroll', function () {

        clearTimeout(self.timer);
        setTimeout(function () {

          // check bottom
          var is_bottom = (window.getScrollTop() >= window.getScrollSize().y - window.getSize().y);
          if (!is_bottom) {
            return;
          }

          if (!self.e.isVisible()){ // by example in a tab
            return ;
          }

          self.loadMore();

        }, 1000);

      });

    }

  },

  loadContent: function (url, data, onComplete)
  {
    var self = this;

    if (!data){
      data = {};
    }

    // send ajax request
    if (self.is_loading) {
      return;
    }

    // reset old vars
    self.is_end = false;
    self.page = 1;
    self.image_sizes = [];


    self.is_loading = true;
    $('hapLoader').addClass('active');

    (new Request.JSON({
      url: url,
      data: data,
      onSuccess:function (res)   {

        // clear old
        self.e.empty();

        self.is_loading = false;
        $('hapLoader').removeClass('active');

        var items = (new Element('div', {html:res.body})).getChildren();
        items.inject(self.e, 'bottom');

        // Hide all to after to build images
        items.setStyle('visible', 'hidden').addClass('with_effect');

        if (!res.is_next) {
          self.is_end = true;
        }

        self.bindRebuildOnLoadImages();
        self.bindRebuildOnTimeout();

        // Callback on complete
        if (onComplete){
          onComplete(res);
        }

      },
      onError:function () {
        self.error('Request error');
        self.is_loading = false;
        $('hapLoader').removeClass('active');
      }
    })).send();
  },

  loadMore:function (onComplete) {

    var self = this;

    // send ajax request
    if (self.is_loading) {
      return;
    }
    if (this.is_end){
      return ;
    }

    self.is_loading = true;
    $('hapLoader').addClass('active');

    // get a new page
    self.page++;

    var data = self.options.request_params;
    data.page = self.page;

    (new Request.JSON({
      url: self.options.request_url,
      data:data,
      onSuccess:function (res) {
        self.is_loading = false;
        $('hapLoader').removeClass('active');
        var items = (new Element('div', {html:res.body})).getChildren();
        items.inject(self.e, 'bottom');

        // Hide all to after to build images
        items.setStyle('visibility', 'hidden').addClass('with_effect');


        if (!res.is_next) {
          self.is_end = true;
        }

        if (onComplete){
          onComplete(res);
        }

        self.bindRebuildOnLoadImages();
        self.bindRebuildOnTimeout();

      },
      onError:function () {
        self.error('Request error');
        self.is_loading = false;
        $('hapLoader').removeClass('active');
      }
    })).send();
  },


  rebuild:function () {

    var self = this;

    $('hapBuildLoader').addClass('active');

    // Our container
    this.container_width = this.e.getParent().getWidth()-30; // -30px

    this.e
      .setStyle('width', this.container_width+this.options.container_padding)
      .setStyle('max-width', this.container_width+this.options.container_padding);


    // Step 1
    // get all images and get their sizes and save
    this.e.getElements('.img:not(.size_saved)').each(function (img) {

      var w = img.getWidth();
      var h = img.getHeight();

      if (!w || !h){
        return ;
      }
      img.addClass('size_saved');

      if (self.isDebug()){
        img
          .setStyle('visibility', 'hidden')
        ;
        img.getParent()
          .setStyle('border', '#000 1px solid')
          .setStyle('background-color', '#222')
        ;
      }

      self.image_sizes[self.image_sizes.length] = {
        width: w,
        height: h,
        ratio: w/h,
        element_id: img.get('id')
      };

    });

    // Step 2
    // align height


    var total_width = 0;
    var line_number = 0;
    var lines = [];

    self.image_sizes.each(function (img){

      var max_width = self.options.max_width*img.ratio;
      var $img = $(img.element_id);
      $img.setStyle('max-width', max_width);

      if (self.isDebug()){
        $img
          .setStyle('visibility', 'hidden')
        ;
        $img.getParent()
          .setStyle('border', '#000 1px solid')
          .setStyle('background-color', '#222')
        ;
      }


      total_width+=max_width;
      if (total_width > self.container_width){
        line_number++;
        total_width = 0;
      }
      if (!lines[line_number]){
        lines[line_number] = [];
      }
      lines[line_number][lines[line_number].length] = {
        img: $img,
        max_width: max_width
      };


    });


    // Step 3
    // align width


    // prepare lines width
    var lines_width = [];
    lines.each(function (line, key){

      var line_width = 0;
      line.each(function (item){
        line_width+=item.max_width;
      });
      lines_width[lines_width.length] = line_width;
    });


    // Global available
    self.lines = lines;
    self.lines_width = lines_width;


    // Resize
    lines.each(function (line, key){

      // if on the line only one item then nothing do
      if (line.length < self.options.build_min_count){
        line.each(function (item){ // but we set that is a line
          var $li = item.img.getParent('li');
          $li.addClass('li_line')

          if (self.isDebug()){
            item.img
              .setStyle('visibility', 'hidden')
            ;
            item.img.getParent()
              .setStyle('border', '#000 1px solid')
              .setStyle('background-color', '#222')
            ;
          }

        });
        return ;
      }


      var line_width = lines_width[key]
      line.each(function (item){

        // Super formula
        var ost = self.container_width-line_width;
        var new_max_width = item.max_width*(self.container_width / (self.container_width - ost));

        // Set sizes
        var $li = item.img.getParent('li');
        item.img.setStyle('max-width', new_max_width);
        item.img.addClass('line' + key);

        $li
          .addClass('li_line' + key)
          .addClass('li_line')
          .setStyle('width', new_max_width);

        if (self.isDebug()){
          item.img
            .setStyle('visibility', 'hidden')
          ;
          item.img.getParent()
            .setStyle('border', '#000 1px solid')
            .setStyle('background-color', '#222')
          ;
        }

      });

      var $images = $$('.li_line'+key + ' img');

      // get max height of the images
      var line_height = $images.getHeight().max();
      var $line = $$('.li_line'+key);

      // align all by height
      $line.setStyle('height', line_height);

      // center by vertical
      $$('.li_line'+key + ' .aimg').setStyle('line-height', line_height);
      $$('.li_line'+key + ' .img').setStyle('line-height', line_height);


    });

    // Display these items
    self.e.getChildren().setStyle('visibility', 'visible');
    $('hapBuildLoader').removeClass('active');

    // Fade in effect
    self.e.getChildren('.with_effect')
      .setStyle('opacity', 0)
      .fade('in')
      .removeClass('with_effect');

  },

  bindRebuildOnTimeout: function ()
  {
    var self = this;

    window.addEvent('domready', function (){
      clearTimeout(self.timeout_timer);
      self.timeout_timer = setTimeout(function (){
        self.rebuild();
      }, self.options.build_timeout);
    });

    clearTimeout(self.timeout_timer);
    self.timeout_timer = setTimeout(function (){
      self.rebuild();
    }, self.options.build_timeout);

  },

  bindRebuildOnLoadImages: function ()
  {
    var self = this;

    var imgs = this.e.getElements('.img:not(.photo_onload_binded)');
    imgs.addClass('photo_onload_binded');

    this.need_load_count = imgs.length;
    this.success_loaded = 0;
    this.failed_loaded = 0;

    // fix for IE
    if ([6,7,8,9,10,11].indexOf(this.getInternetExplorerVersion()) !== -1) // and on the future ))
    imgs.each(function (item){
      item.set('src', item.get('src') + '&nocache='+(new Date().getTime()));
    });

    imgs
      .removeEvent('load')
      .addEvent('load', function (){
        $(this).addClass('photo_loaded');
        self.success_loaded++; // one of the images has been loaded
        self.checkOnLoadImages();
      })
      .removeEvent('error')
      .addEvent('error', function (){
        $(this).addClass('photo_error');
        self.failed_loaded++;
        self.need_load_count--; // except the image from total count
      });
  },

  checkOnLoadImages: function ()
  {
    var self = this;
    // all of the images has been loaded
    // run build images
    if (self.success_loaded>=self.need_load_count){
      this.rebuild();
    }
  },

  /**
   * Detect version of Internet Explorer
   * if it is not IE return -1
   * @return {Number}
   */
  getInternetExplorerVersion:function () {
    var rv = -1;
    if (navigator.appName == 'Microsoft Internet Explorer') {
      var ua = navigator.userAgent;
      var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null)
        rv = parseFloat(RegExp.$1);
    }
    return rv;
  }

};


var HapInstance = new Class(hap);
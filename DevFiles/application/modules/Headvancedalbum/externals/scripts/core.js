var he_advanced_album = {

    initialize:function () {

    },

    resize_line:function (params, width, selector) {
        if (isNaN(width)) {
            // @todo timeline detection
            width = 935;
        }
        width -= 10 * params.length;
        var min_h = this.resize_to_min_height(params, selector);
        var flag = true;
        var e_width = 1;
        var e_height = 1;
        while (flag) {
            var w = 0;
            var i = 0;
            for (i = 0; i < params.length; i++) {
                e_width = $$('#' + selector + '_' + params[i].photo_id).getWidth();
                e_height = $$('#' + selector + '_' + params[i].photo_id).getHeight();
                var e_ratio = e_width / e_height;
                e_height = min_h;
                e_width = min_h * e_ratio;
                w += e_width;

                $$('#' + selector + '_' + params[i].photo_id).setStyle('width', e_width);
                $$('#' + selector + '_' + params[i].photo_id).setStyle('height', e_height);
            }
            if (w <= width) {

                flag = false;
            } else {
                min_h = min_h - 1;
            }
        }
//        if(params.length == 1) {
//            console.log(width);
//            $$('#' + selector + '_' + params[0].photo_id).setStyle('width', width);
//            $$('#' + selector + '_' + params[0].photo_id).setStyle('height', width / params[0].ratio);
//            params[0].height = width / params[0].ratio;
//            params[0].width = width;
//        }
    },

    resize_to_min_height:function (params, selector) {
        var min_h = $$('#' + selector + '_' + params[0].photo_id).getHeight();

        for (var i = 0; i < params.length; i++) {
            if (min_h >= $$('#' + selector + '_' + params[0].photo_id).getHeight())
                min_h = $$('#' + selector + '_' + params[0].photo_id).getHeight();
        }

        return min_h;
    },

    /*
        var widget - widget's class
        var loader = loader's div class
        var selector = main photos wrapper
        var photo_class = photo img tag class
        var params = photos params from server
     */
    resize_photos_on_load:function (widget, loader, selector, photo_class, photo_id, params) {
        var loaded = 0;
        $$('.'+widget).getElements('.'+loader)[0].setStyle('display', 'block');
        $$('.'+selector).setStyle('max-height', '50px');
        $$('.'+selector).setStyle('visibility', 'hidden');
        var album_count = $$('.'+widget).getElements('.'+photo_class)[0].length;

        $$('.'+widget).getElements('.'+photo_class)[0].addEvent('load', function () {
            loaded++;
            if (loaded >= album_count) {

                he_advanced_album.resize_line(params, parseInt($$('.'+selector).getStyle('width')), photo_id);

                $$('.'+widget).getElements('.'+loader)[0].setStyle('display', 'none');
                $$('.'+selector).setStyle('visibility', 'visible');
                $$('.'+selector).setStyle('max-height', 'none');
            }
        });
    }


};

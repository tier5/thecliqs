
window.addEvent('domready', function() {
    $$('.ynvideochannel-category-sub-category').set('styles', {
        display : 'none'
    });

    $$('.ynvideochannel-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynvideochannel-category-collapsed')) {

            var id = row.getAttribute('value');
            var rowSubCategories = row.getAllNext('li.child_'+id);

            this.removeClass('ynvideochannel-category-collapsed');
            this.addClass('ynvideochannel-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynvideochannel-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

            var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynvideochannel-category-no-collapsed');
            this.addClass('ynvideochannel-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynvideochannel-category-sub-category')) {
                    break;
                } else {
                    var collapsedDivs = rowSubCategories[i].getElements('.ynvideochannel-category-collapse-control');

                    if (collapsedDivs.length > 0) {
                        collapsedDivs[0].removeClass('ynvideochannel-category-no-collapsed');
                        collapsedDivs[0].addClass('ynvideochannel-category-collapsed');
                    }

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    });
});
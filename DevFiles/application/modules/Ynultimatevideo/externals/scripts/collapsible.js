
window.addEvent('domready', function() {
    $$('.ynultimatevideo-category-sub-category').set('styles', {
        display : 'none'
    });

    $$('.ynultimatevideo-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');
        var id = row.getAttribute('value');

        if (this.hasClass('ynultimatevideo-category-collapsed')) {

            var rowSubCategories = row.getAllNext('li.child_'+id);

            this.removeClass('ynultimatevideo-category-collapsed');
            this.addClass('ynultimatevideo-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynultimatevideo-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

            var rowSubCategories = row.getAllNext('li.child_'+id);

            this.removeClass('ynultimatevideo-category-no-collapsed');
            this.addClass('ynultimatevideo-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynultimatevideo-category-sub-category')) {
                    break;
                } else {
                    var collapsedDivs = rowSubCategories[i].getElements('.ynultimatevideo-category-collapse-control');

                    if (collapsedDivs.length > 0) {
                        collapsedDivs[0].removeClass('ynultimatevideo-category-no-collapsed');
                        collapsedDivs[0].addClass('ynultimatevideo-category-collapsed');
                    }

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    });
});
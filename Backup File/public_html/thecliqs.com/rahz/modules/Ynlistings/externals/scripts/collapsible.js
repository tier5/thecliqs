
window.addEvent('domready', function() {
   $$('.ynlistings-category-sub-category').set('styles', {
        display : 'none'
    });
    
     $$('.ynlistings-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynlistings-category-collapsed')) {

        	var id = row.getAttribute('value');
        	var rowSubCategories = row.getAllNext('li.child_'+id);  

            this.removeClass('ynlistings-category-collapsed');
            this.addClass('ynlistings-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynlistings-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

        	var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynlistings-category-no-collapsed');
            this.addClass('ynlistings-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynlistings-category-sub-category')) {
                    break;
                } else {
                	var collapsedDivs = rowSubCategories[i].getElements('.ynlistings-category-collapse-control');

                	if (collapsedDivs.length > 0) {
                		collapsedDivs[0].removeClass('ynlistings-category-no-collapsed');
                		collapsedDivs[0].addClass('ynlistings-category-collapsed');
                	}

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});
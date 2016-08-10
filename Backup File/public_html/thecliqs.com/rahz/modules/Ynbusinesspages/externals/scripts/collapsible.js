
window.addEvent('domready', function() {
   $$('.ynbusinesspages-category-sub-category').set('styles', {
        display : 'none'
    });
    
     $$('.ynbusinesspages-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynbusinesspages-category-collapsed')) {

        	var id = row.getAttribute('value');
        	var rowSubCategories = row.getAllNext('li.child_'+id);  

            this.removeClass('ynbusinesspages-category-collapsed');
            this.addClass('ynbusinesspages-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynbusinesspages-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

        	var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynbusinesspages-category-no-collapsed');
            this.addClass('ynbusinesspages-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynbusinesspages-category-sub-category')) {
                    break;
                } else {
                	var collapsedDivs = rowSubCategories[i].getElements('.ynbusinesspages-category-collapse-control');

                	if (collapsedDivs.length > 0) {
                		collapsedDivs[0].removeClass('ynbusinesspages-category-no-collapsed');
                		collapsedDivs[0].addClass('ynbusinesspages-category-collapsed');
                	}

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});
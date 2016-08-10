
window.addEvent('domready', function() {
   $$('.ynjobposting-industry-sub-industry').set('styles', {
        display : 'none'
    });
    
     $$('.ynjobposting-industry-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynjobposting-industry-collapsed')) {

        	var id = row.getAttribute('value');
        	var rowSubCategories = row.getAllNext('li.child_'+id);  

            this.removeClass('ynjobposting-industry-collapsed');
            this.addClass('ynjobposting-industry-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynjobposting-industry-sub-industry')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

        	var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynjobposting-industry-no-collapsed');
            this.addClass('ynjobposting-industry-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynjobposting-industry-sub-industry')) {
                    break;
                } else {
                	var collapsedDivs = rowSubCategories[i].getElements('.ynjobposting-industry-collapse-control');

                	if (collapsedDivs.length > 0) {
                		collapsedDivs[0].removeClass('ynjobposting-industry-no-collapsed');
                		collapsedDivs[0].addClass('ynjobposting-industry-collapsed');
                	}

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});
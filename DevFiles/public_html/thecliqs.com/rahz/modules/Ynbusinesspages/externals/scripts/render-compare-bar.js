window.addEvent('domready', function() {
    var $params = {};
    $params['format'] = 'html';
    var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/name/ynbusinesspages.compare-bar',
        data : $params,
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	$$('.layout_ynbusinesspages_compare_bar').destroy();
            var body = document.getElementsByTagName('body')[0];
            Elements.from(responseHTML).inject(body);
            eval(responseJavaScript);
        }
    });
    request.send();
});
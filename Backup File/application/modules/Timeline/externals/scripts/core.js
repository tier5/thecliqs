/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

if (document.getElementsByClassName == undefined) {
    document.getElementsByClassName = function (cl) {
        var retnode = [];
        var myclass = new RegExp('\\b' + cl + '\\b');
        var elem = this.getElementsByTagName('*');
        for (var i = 0; i < elem.length; i++) {
            var classes = elem[i].className;
            if (myclass.test(classes)) {
                retnode.push(elem[i]);
            }
        }
        return retnode;
    }
}
;


var tl_manager = new TimelineManager();
var tl_listener = new TimelineListener({'class_name':'click-listener'});

en4.core.runonce.add(function () {
    tl_manager.init();
    tl_listener.init();
});
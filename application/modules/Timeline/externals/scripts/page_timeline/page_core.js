/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */
var tl_manager = new PageTimelineManager();
var tl_listener = new TimelineListener({'class_name':'click-listener'});

en4.core.runonce.add(function () {
    tl_manager.init();
    tl_listener.init();
});
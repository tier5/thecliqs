<script type="text/javascript">
  var NestedDragMove = new Class({
    Extends : Drag.Move,
    checkDroppables: function() {
      //var overed = this.droppables.filter(this.checkAgainst, this).getLast();
      var overedMulti = this.droppables.filter(this.checkAgainst, this);

      // Pick the smallest one
      var overed;
      var smallestOvered = false;
      var overedSizes = [];
      overedMulti.each(function(currentOvered, index) {
        var overedSize = currentOvered.getSize().x * currentOvered.getSize().y;
        if( smallestOvered === false || overedSize < smallestOvered ) {
          overed = currentOvered;
          smallestOvered = overedSize;
        }
      });

      if (this.overed != overed){
        if (this.overed) {
          this.fireEvent('leave', [this.element, this.overed]);
        }
        if (overed) {
          this.fireEvent('enter', [this.element, overed]);
        }
        this.overed = overed;
      }
    }
  });

  var NestedSortables = new Class({
    Extends : Sortables,

    getDroppables: function(){
      var droppables = this.list.getChildren();
      if (!this.options.constrain) {
        droppables = this.lists.concat(droppables);
        if( !this.list.hasClass('sortablesForceInclude') ) droppables.erase(this.list);
      }
      return droppables.erase(this.clone).erase(this.element);
    },

    start: function(event, element){
      if (!this.idle) return;
      this.idle = false;
      this.element = element;
      this.opacity = element.get('opacity');
      this.list = element.getParent();
      this.clone = this.getClone(event, element);

      this.drag = new NestedDragMove(this.clone, {
        snap: this.options.snap,
        container: this.options.constrain && this.element.getParent(),
        droppables: this.getDroppables(),
        onSnap: function(){
          event.stop();
          this.clone.setStyle('visibility', 'visible');
          this.element.set('opacity', this.options.opacity || 0);
          this.fireEvent('start', [this.element, this.clone]);
        }.bind(this),
        onEnter: this.insert.bind(this),
        onCancel: this.reset.bind(this),
        onComplete: this.end.bind(this)
      });

      this.clone.inject(this.element, 'before');
      this.drag.start(event);
    },

    insert : function(dragging, element) {
      if( this.element.hasChild(element) ) return;
      //this.parent(dragging, element);

      //insert: function(dragging, element){
      var where = 'inside';
      if (this.lists.contains(element)){
        if( element.hasClass('sortablesForceInclude') && element == this.list ) return;
        this.list = element;
        this.drag.droppables = this.getDroppables();
      } else {
        where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
      }
      this.element.inject(element, where);
      this.fireEvent('sort', [this.element, this.clone]);
      //},
    }
  })
</script>


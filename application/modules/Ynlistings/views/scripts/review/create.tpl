<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>
<script type="application/javascript">
    var rated = 0;
    var new_rate = 0;
    
    var set_rating = window.set_rating = function() {
        var rating = new_rate;
        for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x).set('class', 'ynlistings_rating_star_big_generic ynlistings_rating_star_big');
        }

        for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x).set('class', 'ynlistings_rating_star_big_generic ynlistings_rating_star_big_disabled');
        }
    }

    var rate = window.rate = function(rating) {
        if (!rated) {
            rated = 1;
        }
        new_rate = rating;
        set_rating();
    }
    
    var rating_over = window.rating_over = function(rating) {
        for(var x=1; x<=5; x++) {
            if(x <= rating) {
                $('rate_'+x).set('class', 'ynlistings_rating_star_big_generic ynlistings_rating_star_big');
            } else {
                $('rate_'+x).set('class', 'ynlistings_rating_star_big_generic ynlistings_rating_star_big_disabled');
            }
        }
    }
    
    function check_validate() {
        var err = [];
        if (!rated) {
            err.push('Rate is required');
        }
        var review = $('review_body').value;
        if (review == '') {
            err.push('Review content is required');
        }
        if (err.length > 0) {
            var error_list = $('error_list');
            error_list.empty();
            for (var i = 0; i < err.length; i++) {
                var li = new Element('li', {
                    text: ''+err[i],
                });
                error_list.grab(li);
            }
            return false;
        }
        else {
            $('review_rating').set('value', new_rate);
            return true;
        }         
    }
    
</script>
<div id="add_review_form">
<form method="post" onsubmit="return check_validate()">
    <div>
        <h3><?php echo $this->translate('Add Review')?></h3>
    </div>
    <div>
    <ul id="error_list" class="form-errors">    
    </ul>
    </div>
    <div id="video_rating" class="rating" onmouseout="set_rating();">
        <span id="rate_1" class="rating_star_big_generic ynlistings_rating_star_big_generic" <?php if ($this->viewer): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
        <span id="rate_2" class="rating_star_big_generic ynlistings_rating_star_big_generic" <?php if ($this->viewer): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
        <span id="rate_3" class="rating_star_big_generic ynlistings_rating_star_big_generic" <?php if ($this->viewer): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
        <span id="rate_4" class="rating_star_big_generic ynlistings_rating_star_big_generic" <?php if ($this->viewer): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
        <span id="rate_5" class="rating_star_big_generic ynlistings_rating_star_big_generic" <?php if ($this->viewer): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
    </div>
    <div>
        <textarea id="review_body" name="review_body" rows="5" cols="50" placeholder="Write your review here."></textarea>
    </div>
    <input type="hidden" id="review_rating" name="review_rating" />
    <div>
        <button type='submit'><?php echo $this->translate("Save") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
    </div>
</form>
</div>
<script type="text/javascript">
    set_rating();
</script>
<?php endif; ?>
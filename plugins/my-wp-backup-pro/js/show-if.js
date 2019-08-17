(function($){
    'use strict';

    $('[data-show-only-if-checked]').each(function(){
        var self = $(this);
        var target = $(self.data('show-only-if-checked'));
        target.change(function(){
            if(target.is(':checked')) self.show();
            else self.hide();
        });
    });
})(jQuery);

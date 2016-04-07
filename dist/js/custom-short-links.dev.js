var CustomShortLinks = {};

CustomShortLinks = CustomShortLinks || {};
CustomShortLinks.Screen = CustomShortLinks.Screen || {};

CustomShortLinks.Screen.Edit = (function ($) {

    var typingTimer = false;
    var isTyping = false;

    function Edit() {
        $(function(){
            this.handleEvents();
        }.bind(this));
    }

    Edit.prototype.handleEvents = function () {
        $('#title').on('keyup', function (e) {
            var val = $(e.target).val();

            val = val.replace(/\s/g, '-');
            val = val.replace(/[^a-zA-Z0-9_-]/g, '');

            $(e.target).val(val);
        }.bind(this));
    };

    return new Edit();

})(jQuery);

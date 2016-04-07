CustomShortLinks = CustomShortLinks || {};
CustomShortLinks.Screen = CustomShortLinks.Screen || {};

CustomShortLinks.Screen.Edit = (function ($) {

    var typingTimer = false;
    var isTyping = false;

    function Edit() {
        $(function(){
            // this.handleEvents();
        }.bind(this));
    }

    Edit.prototype.handleEvents = function () {
        $('#title').on('input', function (e) {
            isTyping = true;

            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                $('#title').blur();
            }, 1000);
        }.bind(this));
    };

    return new Edit();

})(jQuery);

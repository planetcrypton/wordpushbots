// (function($) {

//     function activateTab( el ) {
//         $('section').hide();
//         $('section').eq(el.index()).show();
//         $('.nav-tab-wrapper a').removeClass('nav-tab-active');
//         $(el).addClass('nav-tab-active');
//     }

//     window.addEventListener("load", function(){
//         activateTab( $('.nav-tab-wrapper a:first-child') );
//     }, false);

//     $(document).on( 'click', '.nav-tab-wrapper a', function(evt) {
//         evt.preventDefault();
//         activateTab( $(this) );
//         this.blur();
//     });

// })( jQuery );

(function($) {

    // opens a tab by a hash
    // opens first tab when empty
    function activateTab() {
        var tabEl;
        if( location.hash === '' ) {
            tabEl = $('.nav-tab-wrapper a:first-child');
        }else{
            tabEl = $('a.nav-tab[href="'+location.hash+'"]');
        }
        if( tabEl.length < 1 ) return;

        $('section').hide();
        $('section').eq(tabEl.index()).show();
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $(tabEl).addClass('nav-tab-active');
    }

    // window.addEventListener('load', function(){
    //     // add tab's hash to form-action
    //     // so the tab opens when the page reloads
    //     $('#settings-form').on('submit', function(evt){
    //         var formEl = $(this);
    //         formEl.attr('action', location.href);
    //         return true;
    //     });

    //     // open 'requested' tab
    //     activateTab();
    // }, false);

    // window.addEventListener('hashchange', function(evt){
    //     activateTab();
    // }, false);

})( jQuery );

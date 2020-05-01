(function ( $ ) {
 
    $.fn.intlInput = function( options ) {
 
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
           /* color: "#556b2f",
            backgroundColor: "white"*/
            code_country : 'us'
        }, options );

        var filename = window.location.href;
        var url = filename.split('/');
        var path;

        if(url[2] == 'localhost')
        {
          path = 'https://localhost/'+url[3]+'/assets';
        }
        else
        {
          path = 'https://'+url[2]+'/'+url[3]+'/assets';
        }

        var input = document.querySelector("#phone");

        return window.intlTelInput(input, {
            customPlaceholder : function(selectedCountryPlaceholder, selectedCountryData){
               if(selectedCountryPlaceholder.substring(0,1) == '(')
               {
                  return selectedCountryPlaceholder.replace(/\-+/g,'');
               }
               else
               {
                  var placeholder = selectedCountryPlaceholder.replace(/^0| +|\-+/g,'');
                  return placeholder;
               }
            },
            dropdownContainer: document.body,
            pageHiddenInput : url[4],
            initialCountry: settings.code_country,  
            onlyCountries: ['us', 'gb', 'sg', 'au', 'id','my'],
            placeholderNumberType: "MOBILE",
            utilsScript: path+"/intl-tel-input/js/utils.js",
        });
    };
 
}( jQuery ));
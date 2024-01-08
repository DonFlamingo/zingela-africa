/* Dynamic Elements v1.0
 Show/hide, require/not require, and enable/disable fields based on checkboxes and select fields.

 Class names: "lock" will disable an element, "hide" will hide an element, "need" will require an element.

 Adding the ID of a checkbox as a class to an element will make it show/enabled/required:
 i.e. <input type="checkbox" name="getName" id="getName" /> <input type="text" name="yourName" class="getName lock hide require" />

 Adding the ID and value of a select field as a class to an element will make it show/enabled/required (format is 'ID-Value'):
 i.e. <select name="getTitle" id="getTitle"><option>Mr</option><option>Mrs</option><option>Other</option> <input type="text" name="yourTitle" class="getTitle-Other lock hide require" />

 You can use this like "$("*").dynamicElements();", or "$("select").dynamicElements();", or "$(":checkbox").dynamicElements();", etc.

 Dependencies:
 jQuery >= 1.6.2 | http://www.jquery.com
 Optional:
 jQuery Validation Plugin >= 1.9.0 | http://bassistance.de/jquery-plugins/jquery-plugin-validation/ (to require fields)
 */

(function( $ ){

    $.fn.dynamicElements = function() {

        // Function to hide/disable/not require elements based on hide/lock/need classes
        function disable(el) {
            // Hide elements with "hide" class
            if($(el).hasClass("hide")) { $(el).hide(); }
            // Disable elements with "lock" class
            if($(el).hasClass("lock")) { $(el).attr("disabled", true); }
            // Remove requirement on elements with the "need" class
            if($(el).hasClass("need")) { $(el).removeClass("required"); }
            // Reset to initial value (blank) if select field
            if($(el)[0].tagName == "SELECT") { $(el).val(""); }
            // Hide, Disable, and don't Require grandchildren elements on uncheck
            $("[class*='"+$(el).attr('id')+"']").each(function(index) {
                disable($(this));
            });
        }

        // Function to show/enable/require elements based on hide/lock/need classes
        function enable(el) {
            // Show elements with "hide" class
            if($(el).hasClass("hide")) { $(el).show(); }
            // Enable elements with "lock" class
            if($(el).hasClass("lock")) { $(el).removeAttr("disabled"); }
            // Require elements with "need" class
            if($(el).hasClass("need")) { $(el).addClass("required"); }
        }

        // Loop through every element
        this.each(function(index) {
            // Hide, Disable, and Require elements on Load
            if($(this).hasClass("hide")) { $(this).hide(); }
            if($(this).hasClass("lock")) { $(this).attr("disabled", true); }
            if($(this).hasClass("need")) { $(this).addClass("required"); }
            // Add change function if element is a select
            if($(this)[0].tagName == "SELECT") {
                $(this).change(function() {
                    // Get elements ID and create classname based on ID and selected value
                    var id = $(this).attr("id");
                    var classname = "."+id+"-"+$(this).val();
                    // Loop through and disable all elements containing the select ID in their classes
                    $("[class*='"+id+"']").each(function(index) {
                        disable($(this));
                    });
                    // Loop through ad enable all elements with the classname containing selects ID and current value
                    $(classname).each(function(index) {
                        enable($(this));
                    });
                });
                // Add change function if element is a checkbox
            } else if($(this)[0].tagName == "INPUT" && $(this).attr("type") == "checkbox") {
                $(this).change(function() {
                    // Get classname based on elements ID
                    var classname = "."+$(this).attr("id");
                    // Loop through and disable all elements with classname
                    $(classname).each(function(index) {
                        disable($(this));
                    });
                    // If checked, loop through and enable all elements with classname
                    if($(this).prop("checked")) {
                        $(classname).each(function(index) {
                            enable($(this));
                        });
                    }
                });
            }
        });
        $("select").change();
        $(":checkbox").change();
    };

})( jQuery );
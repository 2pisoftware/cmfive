// placeholder function to stop JS complaining 
function selectAutocompleteCallback(event, ui) {
}

function changeTab(hash) {
    if (hash.length > 0) {
        $(".tab-body > div").each(function() {
            $(this).hide();
            $(this).removeClass("active");
        });
        $('.tab-head > a').each(function() {
            $(this).removeClass("active");
        });
        
        if (hash[0] === "#"){
            hash = hash.substr(1);
        }
        
        if(history.replaceState) {
            history.replaceState(null, null, '#' + hash);
        } else {
            location.hash = '#' + hash;
        }
        
        $(".tab-body > div#" + hash).show().addClass("active");
        $('.tab-head > a[href$="' + hash + '"]').addClass("active");
        
        // Update codemirror instances
        $('.CodeMirror').each(function(){
           this.CodeMirror.refresh();
        }); 
    }
}

function toggleModalLoading() {
    if ($(".loading_overlay").is(":visible")) {
        $(".loading_overlay").fadeOut();
    } else {
        $(".loading_overlay").fadeIn();
    }
}

function bindCodeMirror() {
    var _codeMirror = [];
    //setup code-mirror
    var maxCodeInstances = $(".codemirror").length;
    var codeInstanceCount = 0;
    $('textarea.codemirror').each(function() {
//        $(this).click(function() {
            var textarea = $(this)[0];
            if (!_codeMirror[codeInstanceCount]) {
                _codeMirror[codeInstanceCount] = CodeMirror.fromTextArea(textarea, {
                    lineNumbers: true,
                    mode: 'text/html',
                    matchBrackets: true,
                    autoCloseTags: true,
                    wordWrap: true,
					viewportMargin: Infinity,
					
                });
                if (codeInstanceCount < (maxCodeInstances - 1)) {
                    codeInstanceCount++;
                }
            }
//        });
    });
}
    
/**
 * Clears all elements of a form
 */
function clearForm(ele) {
    $(ele).find(':input').each(function() {
        switch (this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'hidden':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
}

/* This function gets dates in dd/mm/yyyy format, 
 * splits them and rearranges it in mm/dd/yyy format
 * so JS can convert the date string into milliseconds
 * and then it is converted into days. The two day
 * strings are subtracted and that result will be returned.
 *
 * This means that it works just like a normal compare.
 * e.g. (05/08/2010 and 01/08/2010, result will be -4/
 */
function compareDates(from_date, to_date) {
    var d = from_date.split("/"); //To reuse this function, replace the variables 'from_date' here
    var temp = d[0];
    d[0] = d[1];
    d[1] = temp;
    var oStr = d.join('/');
    oStr = (Date.parse(oStr) / 86400000);
    var n = to_date.split("/"); // and 'to_date' here, with ones that need to be compared
    temp = n[0];
    n[0] = n[1];
    n[1] = temp;
    var ndStr = n.join('/');
    ndStr = (Date.parse(ndStr) / 86400000);//from milliseconds to days, divide by 86400000
    return ndStr - oStr;
}
<!-- @version $Id: translate.js,v 1.1 2009/04/30 17:50:27 lsces Exp $ -->
// initialize google translate API to version 1.0
google.load('language', '1');

var translatedItems = Array();

// add a remove function to the Array
Array.prototype.remove=function(s){
  for(i=0; i < this.length; i++){
    if (s==this[i]) {
        this.splice(i, 1);
        break;
    }
  }
}

/**
* Translate a sentence from english to another language, setting an element with the result
*/
function translate(lang1, lang2, ls01) {
	var untranslatedTextElement = document.getElementById('untr_' + ls01);
    var text = untranslatedTextElement.innerHTML;
    google.language.translate(text, lang1, lang2, function(result) {
        // get the translation and set the translated text element
        
        if (result.translation) {
        
            var r = result.translation;
            r = r.replace(/\r\n/g, "&lt;br /&gt;");
            r = r.replace(/\n/g, "&lt;br /&gt;");
            r = r.replace(/</g, "&lt;");
            r = r.replace(/>/g, "&gt;");
            r = r.replace(/\\/g, "");
            
            var translatedTextElement = document.getElementById("tr_" + ls01);
            translatedTextElement.innerHTML = r;
            
            // hide the text that used to represent the translated text
            var div1 = document.getElementById('tr_' + ls01 + '_pre');
            div1.style.display = 'none';
            
            // show translation and the cancel link
            var div2 = document.getElementById('tr_' + ls01 + '_post');
            div2.style.display = 'inline';
			
            translatedItems.push(ls01);
        }
    });
}


/**
* Revert an automated translation back to its original value
*/
function revertTranslation(ls01) {

    var untranslatedText = document.getElementById('untr_' + ls01);
    var translatedText = document.getElementById('tr_' + ls01);
    translatedText.innerHTML = untranslatedText.innerHTML;

    var div1 = document.getElementById('tr_' + ls01 + '_pre');
    div1.style.display = 'inline';

    var div2 = document.getElementById('tr_' + ls01 + '_post');
    div2.style.display = 'none';
    
    translatedItems.remove(parseInt(ls01));
}

/**
* Commit the changes one by one
*/
function commitTranslation(language2, file_type, ls01) {
    if (translatedItems.length > 0) {

        var div1 = document.getElementById('tr_' + ls01 + '_pre');
        div1.style.display = 'none';

        var div2 = document.getElementById('tr_' + ls01 + '_post');
        div2.style.display = 'inline';

        var translatedTextElement = document.getElementById('tr_' + ls01);
        
        var vars = "language2=" + language2 +
            "&ls01=" + ls01 +
            "&file_type=" + file_type +
            "&anchor=a1_" + ls01;
        var savecmd =  "&action=save";
        
        var message = translatedTextElement.innerHTML.replace(/&/g, "%26"); 
        var form =  vars + "&new_message=" + message;
        var url = "editlang_edit.php?" + vars + savecmd;

        div2.innerHTML = '<a href="javascript:;" onclick="return helpPopup00(\'' + vars + '\');">' + translatedTextElement.innerHTML + '</a>'
        
        translatedItems.remove(parseInt(ls01));

        postUrl(url, form);
    }
}


/**
* Commit the changes one by one
*/
function commitTranslations(language2, file_type, commitButtonId, commitPanelId) {
    if (translatedItems.length > 0) {
        var commitButton = document.getElementById(commitButtonId);
        commitButton.style.display = "none";
    
        var commitPanel = document.getElementById(commitPanelId);
        commitPanel.style.display = "block";
    
        var indicator = document.getElementById(commitPanelId + "_indicator");
    
        var total = translatedItems.length;
        while (translatedItems.length > 0) {
            if (translatedItems[0] != null) {
               
                commitTranslation(language2, file_type, translatedItems[0]);
               
                indicator.style.width = ((total - translatedItems.length) * 100.0 / total).toString() + '%';
            }
        }
    

        var commitButton = document.getElementById(commitButtonId);
        commitButton.style.display = "block";

        var commitPanel = document.getElementById(commitPanelId);
        commitPanel.style.display = "none";
    }
}

/**
* Get a url. Partially sourced from Wikipedia Ajax page
*/
function postUrl(url, form) {
    var xmlHttp=null;
    
    // Try to get the right object for different browser
    try {
        // Firefox, Opera 8.0+, Safari, IE7+
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        // Internet Explorer
        try {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4)
        try { 
            // In some instances, status cannot be retrieved and will produce an error (e.g. Port is not responsive)
            if (xmlHttp.status == 200) {
                return xmlHttp.responseText;
            }
        } catch (e) {
            return "Error: " + e.description;
        }
    }
        
    xmlHttp.open("POST", url, true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", form.length);
    xmlHttp.send(form); 
}

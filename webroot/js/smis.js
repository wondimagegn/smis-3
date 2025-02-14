function setCheckBoxByName(element, option) {
 var inputs = document.getElementsByName(element);

 for (var i = 0; i < inputs.length; i++) {
    
     if (inputs[i].type == 'checkbox') {
        inputs[i].click(); 
     }  
 }
}


function setCheckBoxByClassName(className, option) {
	 var inputs = document.getElementsByClassName(className);
	 for (var i = 0; i < inputs.length; i++) {
	     if(inputs[i].type == 'checkbox') {
	         if(inputs[i].checked == true) 
	            if (option == 'uncheck')
	                inputs[i].checked = false;
	         else
	        	if (option == 'check')
	        		inputs[i].checked = true;
	     }  
	 }  
}



function IsNumeric(sText)
{
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;
   for (i = 0; i < sText.length && IsNumber == true; i++) 
      { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) 
         {
         IsNumber = false;
         }
      }
   return IsNumber;
  
}

function toggle(e) {
   
    var e = document.getElementById(e);
   
    if(!e) return true;

    if (e.style.display == "none")
        e.style.display = "block";
    else
        e.style.display = "none";
}

// Use this function to switch between different divs. The selectTab is the ID
// of the one you one to activate. All other 'tabs' as listed in the string
// allTabsString will be hidden. That string is just a text string with
// comma-separated IDs.

// toggleId is the ID of the tab captions to be selected. It is used here just
// to store it in a window variable, so that other functions can lock and hide
// it. But it is irrelevant for the purpose of the switching, so you can still
// use this functions for any generic <div> switch, passing that as a dummy
// argument.

function switchDiv(selectedTab, toggleId, allTabsString) {

// TODO: Check isset tabsLocked or unlockTabs() in user add page.

    if (undefined === window.tabsLocked) {
        window.tabsLocked = false;
    }

    if (window.tabsLocked) {
        return;
    }

    var elementArray = allTabsString.split(',');

    for (var i = 0; i < elementArray.length; i++) {
        elementName = elementArray[i];
        var element = document.getElementById(elementName);
        if (elementName == selectedTab ) {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }
    }
    window.currentToggle = toggleId;
}

function updateLoading() {

    var status = window.loadingStatus;

    switch (status) {
        case 0:
            document.getElementById('loading').innerHTML = 'Loading...';
            break;
        case 1:
            document.getElementById('loading').innerHTML = 'Still loading...';
            break;
        case 2:
            document.getElementById('loading').innerHTML = 'Still busy...';
            break;
    }

    if (status == 2) {
        // Reset counter.
        status = -1;
    }
    window.loadingStatus = status + 1;

}

// Function that receives by parameter the id of a radio button
// that should be checked == true to show the content of a div.
// If the radio button id is not checked then displayDivId content is hidden.
function displayDivOnRadioChecked(radioId, displayDivId){
	var e = document.getElementById(radioId);
	if (e.checked == true){
		showBlockById(displayDivId);
	} else {
		hideById(displayDivId);
	}
}

// Hide a div when checking an option. Because this option is exclusive, also
// hide all other options in the same (given) class.
function hideByClassAndIdOnOptionChecked(optionID, divID, optionClass, hideDivId){
    var e = document.getElementById(optionID);

    if (e.checked == true){
        // Hide the given div (normally a button)
        hideById(hideDivId);
        // and also hide all other actions
        $$(optionClass).each(
            function (d) {
                if (d.id != divID) {
                    new Effect.Fade(d, {duration: 0.5});
                }
            }
        );
    } else {
        // Show everything.
        showBlockById(hideDivId);
        $$(optionClass).each(function (d) { showBlockById(d.id); });
    }
}


function hideById(elementID) {
    var e = document.getElementById(elementID);
    if(!e) return true;
    e.style.display = "none";
}

function showBlockById(elementID) {
    var e = document.getElementById(elementID);
    if(!e) return true;
    e.style.display = "block";
}
function showInlineById(elementID) {
    var e = document.getElementById(elementID);
    if(!e) return true;
    e.style.display = "inline";
}



function showIfNoError(elementID) {
    // See if there is not error message before showing an element.
    if (undefined === window.status_error || window.status_error == 0) {
        showBlockById(elementID)
    }
}


function lockTabs() {
    window.tabsLocked = true;
    hideById(window.currentToggle);
}

function unlockTabs() {
    if (undefined === window.status_error || window.status_error == 0) {
        window.tabsLocked = false;
        showBlockById(window.currentToggle);
    }
}

// Find if there is an error message after the last status_messages update.
// Some later actions (like unlockTabs or showIfNoError) will depend on this.
function checkErrors() {
    contents =  document.getElementById('status_messages').innerHTML;
    window.status_error = 0;
    if (contents.indexOf("error_message") != -1) {
        window.status_error = 1;
    } else {
    }
}

// simple java script to confirm user sending of mail
function confirmSubmit(){
    var msg="Are you sure you want to send this mail ?";
    var agree = confirm(msg);
    if(agree)
        return true;
    else
        return false;

}

function toggleRadioButton(radiobutton, divid)
{ 
	
    var e = document.getElementById(divid);
    
    if(radiobutton == 0){
            e.style.display = "none";
    } else {
            if(e.style.display == "none") {
                alert(divid + ' block');
                e.style.display = "block";
            } else {
                alert(divid + ' none');
                e.style.display = "none";
            }
    }
}


/**
 *
 *  Helper functions
 *
 **/


/**
 *
 *  Gets Max Value
 *
 **/

function getMaxVal(sel, elAttr) {
    var selElements = [];
    sel.each (function(){
        selElements.push(jQuery(this)[elAttr]());
    })
    return Math.max.apply(Math,selElements);
}

/**
 *
 *  Returns Two Digits for Number
 *  For ex: it will return 01 for 1 and 15 for 15
 *
 **/

function numberToTwoDigits(number) {
    var resNumber = number;
    if(resNumber<10)
        resNumber = "0" + resNumber.toString();
    return resNumber;
}

/**
 *
 *  Page Animation
 *
 **/

function bbPageAnimate(pos,speed) {
    jQuery('body,html').animate({
        scrollTop: pos
    }, speed);
}
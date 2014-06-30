function setLanguage(index) {
	$(".sigBlockJS").css("display", index == 0 ? "block" : "none");
	$(".sigBlockCS").css("display", index == 1 ? "block" : "none");
	$(".sigBlockBoo").css("display", index == 2 ? "block" : "none");
	
	$(".codeExampleJS").css("display", index == 0 ? "block" : "none");
	$(".codeExampleCS").css("display", index == 1 ? "block" : "none");
	$(".codeExampleBoo").css("display", index == 2 ? "block" : "none");

	setCookieState('exampleLang', index + "");
}


function getLanguage() {
    return getCookieState('exampleLang');
}


function setCategory(index) {
    $(".classRuntime").css("display", index == 0 ? "block" : "none");
    $(".enumRuntime").css("display", index == 1 ? "block" : "none");
    $(".attrRuntime").css("display", index == 2 ? "block" : "none");
    $(".classEditor").css("display", index == 3 ? "block" : "none");
    $(".enumEditor").css("display", index == 4 ? "block" : "none");
    $(".attrEditor").css("display", index == 5 ? "block" : "none");

    setCookieState('indexCategory', index + "");
}


function getCategory() {
    return getCookieState('indexCategory');
}


function setCookieState(cName, value) {
    var exDate = new Date();
    exDate.setDate(exDate.getDate() + 365);
    document.cookie = cName + "=" + escape(value) + ";expires=" + exDate.toGMTString();
}


function getCookieState(cName) {
    if (document.cookie.length > 0) {
        cStart = document.cookie.indexOf(cName + "=");

        if (cStart != -1) {
            cStart = cStart + cName.length + 1;
            cEnd = document.cookie.indexOf(";", cStart);

            if (cEnd == -1) {
                cEnd = document.cookie.length;
            }

            return unescape(document.cookie.substring(cStart, cEnd));
        }
    }

    return 0;
}


/*
$(function () {
setLanguage(getCookieState('exampleLang'));
setCategory(getCookieState('indexCategory'));
});
*/
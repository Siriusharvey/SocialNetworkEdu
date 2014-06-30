var talkTextGUI:GUIText;
var edgeMarginPercentage:int;

function start(){
	var edgeMargin:int = Screen.width/ edgeMarginPercentage * 100;
	
	talkTextGUI.pixelOffset.x = edgeMargin;
	talkTextGUI.pixelOffset.y = edgeMargin;


}
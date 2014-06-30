var Text:GUIText;
var TalkLines:String[];

private var talking:boolean;
private var currentLine:int;

function OnTriggerEnter(col:Collider){
	if(col.tag == "Player"){
	talking = true;
	currentLine = 0;
	Text.text = TalkLines[currentLine];
	}
}

function update () {
	if (talking){
				Text.text = TalkLines[currentLine];
		}
	}





#pragma strict

var hud: boolean ;
var checkKeyDown: boolean ;

hud = false;
checkKeyDown = false;

function Start () {

}

function Update () {
	keyHandle();
}


function OnTriggerEnter(col: Collider){
 if(col.tag == "Pandora") {
 Debug.Log("FUCKTHESYSTEM");
     hud = true;
}
}

function OnTriggerExit(col: Collider){
	hud = false;
}
//Key command
function keyHandle(){
	if (Input.GetKeyDown(KeyCode.E) == true){
		if (!checkKeyDown){
			checkKeyDown = true;
			}
			else { checkKeyDown = false; 
				}
			}
		}
		
		function OnGUI (){
	if (hud == true && checkKeyDown == true){
		GUI.Box(Rect(20,100,100,50),"Hello, there");
		}
}
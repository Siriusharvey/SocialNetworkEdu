using UnityEngine;
using System.Collections;

[ExecuteInEditMode]
public class MyDialogueSystem : MonoBehaviour 
{
	public bool debugMode = false;
	public bool ShowGUI = false; 
	public bool checkKeyDown = false;
    public GUIStyle style = null;
    string[] Q;
	int index = 0;
	
	
	void OnTriggerEnter (Collider other) //enter the trigger
	{			
		if ((other.gameObject.name == "Collider_player"))
		{
			ShowGUI = true;
			Debug.Log ("Collider - check");
		}
	}
	void OnTriggerExit (Collider other) //exit the trigger
	{
		if ((other.gameObject.name == "Collider_player"))
		{
			ShowGUI = false;
			Debug.Log ("ah, go away");
		}
	}
	
	void Update (){ //press enter to execute
		if (Input.GetKeyDown("return") && ShowGUI == true)
		{
            index++;

			if (!checkKeyDown){
				checkKeyDown = true;
				Debug.Log ("Key down - check");
			}
			else{
				checkKeyDown = false;
			}

		}
		if (ShowGUI == false) {
			checkKeyDown = false;		
		}
	}
	
	
	void OnGUI()//GUI box
	{
        Q = new string[4];
        Q[0] = "Hello";
        Q[1] = "You fucked my wife";
        Q[2] = "I AM your wife";
        Q[3] = "I am your wife and i fucked her";

		if (debugMode || Application.isPlaying){
			if (ShowGUI == true && checkKeyDown == true)
			{
				GUI.Label(new Rect(10,10,120,120), Q[index]);
				
			}
			
		}
	}
}
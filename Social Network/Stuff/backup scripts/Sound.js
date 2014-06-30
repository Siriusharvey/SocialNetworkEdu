#pragma strict
var soundToPlay: AudioClip;
function OnTriggerenter() {
	audio.PlayOneShot(soundToPlay);
}


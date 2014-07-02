<?php
//**************************************************************************
//
// ezffmpeg v1.0 (for use with PHP & FFmpeg) - 2008/02
// (c)2008 - Christophe Michau
//												
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//**************************************************************************

//Recuperation informations fichier
function ezffmpeg_vdofile_infos($src_filepath)
{
		 $commandline = EZFFMPEG_BIN_PATH." -i ".$src_filepath;

		 $exec_return          = ezffmpeg_exec($commandline);
		 $exec_return_content  = explode ("\n" , $exec_return);

		 //Traitement du retour
		 if( $error_line_id = ezffmpeg_array_search('error', $exec_return_content) )
		 {
		 		 //Erreur, retourne status = -1 et error_msg = message d'erreur
				 $error_line = trim($exec_return_content[$error_line_id]); 
				 
				 $return_array['status'] 		= -1;
				 $return_array['error_msg'] = $error_line;
		 }
		 else
		 {
		 		 //OK, decode le resultat et renvoie status = 1 + datas
				 $return_array['status'] 		= 1;

				 //Decodage des infos duree / bitrate
				 if($infos_line_id = ezffmpeg_array_search('Duration:', $exec_return_content))
				 {
				 	   $infos_line	   = trim($exec_return_content[$infos_line_id]);
				 	   $infos_cleaning = explode (': ', $infos_line);

						 //Duree						 
						 $infos_datas		 = explode (',', $infos_cleaning[1]);
						 $return_array['vdo_duration_format']  = trim($infos_datas[0]);
						 $return_array['vdo_duration_seconds'] = ezffmpeg_common_time_to_seconds($return_array['vdo_duration_format']);

						 //Bitrate						 
						 $return_array['vdo_bitrate']  = trim($infos_cleaning[3]);

				 }
				 
				 //Decodage des infos codec video
				 if($infos_line_id = ezffmpeg_array_search('Video:', $exec_return_content))
				 {
				 	   $infos_line	   = trim($exec_return_content[$infos_line_id]);
				 	   $infos_cleaning = explode (': ', $infos_line);
						 $infos_datas		 = explode (',', $infos_cleaning[2]);
						 
						 $return_array['vdo_format'] = trim($infos_datas[0]);
						 $return_array['vdo_codec']  = trim($infos_datas[1]);
						 $return_array['vdo_res'] 	 = trim($infos_datas[2]);
						 $return_array['vdo_fps'] 	 = trim($infos_datas[3]);
				 }
				 
				 //Decodage des infos codec video
				 if($infos_line_id = ezffmpeg_array_search('Audio:', $exec_return_content))
				 {
				 	   $infos_line	   = trim($exec_return_content[$infos_line_id]);
				 	   $infos_cleaning = explode (': ', $infos_line);
						 $infos_datas		 = explode (',', $infos_cleaning[2]);
						 
						 $return_array['aud_codec'] 		 = trim($infos_datas[0]);
						 $return_array['aud_frequency']  = trim($infos_datas[1]);
						 $return_array['aud_monostereo'] = trim($infos_datas[2]);
						 $return_array['aud_bitrate']		 = trim($infos_datas[3]);
				 }				 
		 }
				 
		 return($return_array);

}

//Creation d'une capture jpg d'une video
function ezffmpeg_vdofile_capture_jpg ($src_filepath, $output_filepath, $seconds_position, $jpg_resolution="320x240" )
{
		$commandline = EZFFMPEG_BIN_PATH." -i ".$src_filepath." -y -f mjpeg -t 0.001 -s ".$jpg_resolution." -ss ".$seconds_position." ".$output_filepath;
	  $exec_return          = ezffmpeg_exec($commandline);
		$exec_return_content  = explode ("\n" , $exec_return);
		
    if((!file_exists($output_filepath)) || (filesize($output_filepath) <= 0))
    {
 			    return(1);//Conversion OK (1)		
		}
		else
		{
 			    return(-1);//Echec, pas de conversion
		}

}

//Formatage d'une timestamp HH:MM:SS en secondes
function ezffmpeg_common_time_to_seconds($timestamp)
{
 				 $timestamp_datas = explode (':', $timestamp);
				 
				 $nb_seconds			= $timestamp_datas[2]; 
				 $nb_minutes			= $timestamp_datas[1]; 
				 $nb_hours				= $timestamp_datas[0]; 

				 $return_val			= ($nb_hours*3600)+($nb_minutes*60)+$nb_seconds;
				 
				 return($return_val);
}

//Execution propre de FFMpeg avec recuperation des datas
function ezffmpeg_exec($commandline)
{
 			$read = '';
      $handle = popen($commandline.' 2>&1', 'r');
      while(!feof($handle))
			{
			    $read .= fread($handle, 2096);
			}
      pclose($handle);
			return($read);
}

//Recherche data dans un array
function ezffmpeg_array_search($needle, $array_lines)
{
    $return_val = false;
    reset ($array_lines);
		foreach( $array_lines as $num_line => $line_content )
		{
		    if( strpos($line_content, $needle) !== false )
				{
				    return($num_line);
				}
				
		}
		return($return_val);
}
?>
<?php
/* 
	func_gallery.php
	Gallerian funktiot
	function
		
	Variables
	$function = tarkistaa onko funktio olemassa ja jatkaa sen perusteella, muuten antaa virheilmoituksen (jos muistan oikein) :D
	$variable1 = 1	
		image_information & create_resized_image = käyttäjän syöttämä tiedostonnimi ja -pääte
		maakansio = syötä maakoodi
	$variable2 = 2 	variaabeli 
		image_information & create_resized_image = Palvemille siirretyn tiedostonnimi ja -pääte
		galleriakansio = palvelimen hakemisto, tiedostonimi ja -pääte.
	$resized_width = normaalisti nolla, mikäli käytetään muuta kuin resize-functioita, muuten syötetään koko pikseleissä
	$resized_height = normaalisti nolla, mikäli käytetään muuta kuin resize-functioita, muuten syötetään koko pikseleissä
	
	Veikkaisin että check-functionissa on virhe, en suoralta käsin voi tarkistaa koodiani :D
*/

class image(check($function),$variable1=null,$variable2=null,$resized_width=null,$resized_height=null)
{
	// check of functions
	function check($function) {
		if(function_exists($function) {
			switch ($function) {
				case image_infromation: { 
					return($function);
				}
				case create_resized_image {
					return($function);
				}
				case luo_kansio {
					return($function);
				}
			}
		}
		else {
			echo "Error with "$function", Please, check your function and variables for it";
			exit();
		}
	}
	//	$orignal_file = alkuperäinen tiedosto, välitetään tiedostopäätteen perusteella palvemille
	function image_information($original_file) {
		$type = getimagesize($original_file);
		$filesize = filesize($original_file);
	   
		// Tarkistetaan tiedoston tyyppi
		if($type[2] == 1) {
				$file_extension = "gif";
			}
		elseif($type[2] == 2) {
				$file_extension = "jpg";
			}
		elseif($type[2] == 3) {
				$file_extension = "png";
			}
		// Tiedostomuoto ei ole tuettu, palauttaa FALSE
		else {
				$file_extension = FALSE;
		}
		// Funktio palauttaa arvot, jos ok
		if($file_extension) {
			// palauttaa type,tiedostopÃ¤Ã¤te,leveys,korkeus,tiedostokoko
			return array($type[2],$file_extension,$type[0],$type[1],$filesize);
		}
		else {
				// Tiedostotyyppi ei ole tuettu tai jotain hÃ¤iriÃ¶Ã¶
				return array(FALSE,FALSE,FALSE,FALSE);
			}
	}
	/*
		Ottaa syötteenä vastaan (alkuperäinen tiedosto
		$original file 		= alkuperäinen tiedosto
		$destination_file 	= uudeen kuvan
	*/
	function create_resized_image($original_file,$destination_file,$resized_width,$resized_height) {
		// SelvitetÃ¤Ã¤n kuvan koko ja tyyppi
		list($original_width, $original_height, $type) = getimagesize($original_file);
	   
		// Tarkistetaan tiedoston tyyppi
		// GIF
		if($type == 1) {
			$original_image = imagecreatefromgif($original_file);
			// LÃ¤pinÃ¤kyvyys -> valkoinen
			$white = imagecolorallocate($original_image, 255, 255, 255);
			$transparent = imagecolortransparent($original_image, $white);
		}
		// JPEG
		elseif($type == 2) {
			$original_image = imagecreatefromjpeg($original_file);
		}
		// PNG
		elseif($type == 3) {
			$original_image = imagecreatefrompng($original_file);
		}
		// Tiedostomuoto ei ole tuettu, palauttaa FALSE
		else {
			$type = FALSE;
		}
			
		if($type) {
			// Lasketaan kuvalle uusi koko siten, ettÃ¤ kuvasuhde sÃ¤ilyy
			$new_w = $original_width/$resized_width; // Kuvasuhde: leveys
			$new_h = $original_height/$resized_height; // Kuvasuhde: korkeus
			if($new_w > $new_h || $new_w == $new_h)	{
				if($new_w < 1) {
					// Jos alkuperÃ¤inen kuva on pienempi kuin luotava, luodaan alkuperÃ¤isen kokoinen kuva
					$new_w = 1;
				}
				// KÃ¤ytetÃ¤Ã¤n sitÃ¤ suhdetta, jolla tulee max. asetettu leveys, korkeus on alle max.
				$new_width = $original_width / $new_w;
				$new_height = $original_height / $new_w;
			}
			elseif($new_w < $new_h)	{
				if($new_h < 1) {
					// Jos alkuperÃ¤inen kuva on pienempi kuin luotava, luodaan alkuperÃ¤isen kokoinen kuva
					$new_h = 1;
				}
				// KÃ¤ytetÃ¤Ã¤n sitÃ¤ suhdetta, jolla tulee max. asetettu korkeus, leveys on alle max.
				$new_width = $original_width / $new_h;
				$new_height = $original_height / $new_h;
			}
			// Luodaan kuva, joka on mÃ¤Ã¤rÃ¤tyn kokoinen
			$image = imagecreatetruecolor($new_width, $new_height);
			// Resample, luo uuden kuvan tiedostoon
			imagecopyresampled($image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
		   
			// Tallennetaan uusi kuva mÃ¤Ã¤riteltyyn tiedostoon ja annetaan sopiva tiedostopÃ¤Ã¤te
			// GIF
			if($type == 1) {
				imagegif($image, $destination_file);
			}
			// JPEG
			elseif($type == 2) {
				imagejpeg($image, $destination_file);
			}
			// PNG
			elseif($type == 3) {
				imagepng($image, $destination_file);
			}
		}
		// Poistetaan kuva muistista, ei tuhoa alkuperÃ¤istÃ¤ tiedostoa!
		imagedestroy($image);
		// Palauttaa tiedostotyypin onnistuessaan, FALSE jos ei onnistu
		return $type;
	}

	/*
		$maakansio = Maa, esim. Suomi, Murica, jne.
		$galleriakansio = luo galleriakansion
	*/
	function luo_kansio($maakansio,$galleriakansio) {  
		$ok=FALSE; 

		/* luo galleriakansio ja pura suojaukset*/
		$polku="./sisaltokuvat/".$maakansio."/".$galleriakansio;
		if(mkdir($polku, 0777)) $ok=TRUE;

		/*luo alikansiot thumbs, upload ja kuvat*/
		$thumbpolku=$polku."/thumbs";	
		$kuvatpolku= $polku."/kuvat";
		$uploadpolku= $polku."/upload";
		
		if($ok) {
			if(mkdir($thumbpolku, 0755) && mkdir($kuvatpolku, 0755) && mkdir($uploadpolku, 0755)) $ok=TRUE;
		}
			suojaa_kansio($polku);
		return $ok;
	}

	function pura_suojaus($kansio) {
		chmod($kansio, 0777);
	}

	function suojaa_kansio($kansio) {
		chmod($kansio, 0755);
	}
}
?>
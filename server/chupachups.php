<?php
	header("Access-Control-Allow-Origin: *");

	function getUrl($url,$proxy=false,$params=NULL){
		$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

		if(isset($params)){
			$postData = '';
			//create name value pairs seperated by &
			foreach($params as $k => $v){
				$postData .= $k . '='.$v.'&'; 
			}
			$postData = rtrim($postData, '&');
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/x-www-form-urlencoded"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if(isset($params)){
			curl_setopt($ch, CURLOPT_POST, count($postData));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		
		if($proxy){
			// Proxies from : https://proxyrox.com/top-proxies/de
			$proxies = array(
				// Germany
				'52.29.160.137:8083',
				'5.189.129.137:8080',
				// France
				'51.255.38.187:3128',
				'5.135.160.149:3128',
				// Poland
				'83.23.6.181:80',
				'91.188.125.65:4444',
				// Romania
				'141.85.220.108:8080'
			);
			curl_setopt($ch, CURLOPT_PROXY, $proxies[array_rand($proxies)]); //your proxy url
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);//wait only 2 sec
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		
		return $result;
	}
	
	// Need IP:port and bf=3 or bf=4
	if( isset($_GET["ip"]) && ip2long($_GET["ip"]) && isset($_GET["port"]) && is_numeric($_GET["port"]) && isset($_GET["bf"]) &&  ($_GET["bf"]==3 || $_GET["bf"]==4 || $_GET["bf"]=='h') ){
		// Get parameters
		$serverIP = $_GET["ip"].":".$_GET["port"];
		$battlefield = $_GET["bf"]; // 3, 4 or h

		$refresh = false;
		if(isset($_GET["refresh"])){
			$refresh = true;
		}
		$noProxyPlease = false;
		if(isset($_GET["noProxyPlease"])){
			$noProxyPlease = true;
		}
		
		// Stream Services
		$StreamServices = array( "pbbans", "ggc", "aci" );
		
		// Load database files
		$database = array();
		foreach ($StreamServices as $Streamer){
			if ( file_exists("database/{$Streamer}_database_bf{$battlefield}.json") ) {
				$database[$Streamer] = json_decode(file_get_contents("database/{$Streamer}_database_bf{$battlefield}.json"), true);
			}
		}
		
		// Update or Create database files
		$servers = array();
		$connectionError = array();
		foreach ($StreamServices as $Streamer){
			$servers[$Streamer] = array();
			// Update GGC
			if( $Streamer=="ggc" ){
				// Get year's week
				$week = date("W");
				// Clear expired database (weekly)
				if( isset($database[$Streamer][1]) AND $database[$Streamer][1]!=$week ){
					$database[$Streamer][0] = array();
					$database[$Streamer][1] = $week;
					$database[$Streamer][2] = array();
					$database[$Streamer][3] = array();
					file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
				}
				
				// Search ip in database, if not...
				if( !isset($database[$Streamer]) ){
					$serversFlags[$Streamer] = array();
					$servers["not-ggc"] = array();
				}else{
					$servers[$Streamer] = $database[$Streamer][0];
					$servers["not-ggc"] = $database[$Streamer][3];
					$serversFlags[$Streamer] = $database[$Streamer][2];
				}
				if( (!in_array($serverIP, $servers[$Streamer]) && !in_array($serverIP, $servers["not-ggc"])) || $refresh == true ){
					// Get server page from GGC
						$params = array(
							"server_id" => "",
							"ip" => $_GET["ip"],
							"port" => $_GET["port"],
							"date" => date("Y-m-d"),
							"time" => date("H:i"),
							"interval" => "1",
							"submit" => "Send"
						);	
						$ServerBrowser = getUrl("https://www.ggc-stream.net/search/server/wwo", false, $params);
					if( !($ServerBrowser === FALSE) ){
						// Search if server is streaming
						$flags = array(/* LiveSecure */);
						if ( strpos($ServerBrowser,'Serverdata not available') == false ) {
							// Save Servers
							array_push($servers[$Streamer], $serverIP);
							
							// Search for the flags
								// Get server GGC ID ->   <a style="float: left;" href="/server/128703"><img src="//cdn.ggc-stream.net/icon/fugue_icon/monitor.png" class="icon" alt=""> Server View</a>
								array_push($flags, true );
							// Save the flags
								array_push($serversFlags[$Streamer], $flags);
						}else{
							array_push($servers["not-ggc"], $serverIP);
						}
						
						// Update database
						$database[$Streamer] = array( $servers[$Streamer],  $week, $serversFlags[$Streamer], $servers["not-ggc"]);
						file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
						
					}else{
						$connectionError[$Streamer]=true;
					}
				}
			// Update PBBANS
			}else if( $Streamer=="pbbans" ){
				// Get year's week
				$week = date("W");
				// Clear expired database (weekly)
				if( isset($database[$Streamer][1]) AND $database[$Streamer][1]!=$week ){
					$database[$Streamer][0] = array();
					$database[$Streamer][1] = $week;
					$database[$Streamer][2] = array();
					$database[$Streamer][3] = array();
					file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
				}
				
				// Search ip in database, if not...
				if( !isset($database[$Streamer]) ){
					$serversFlags[$Streamer] = array();
					$servers["not-pbbans"] = array();
				}else{
					$servers[$Streamer] = $database[$Streamer][0];
					$servers["not-pbbans"] = $database[$Streamer][3];
					$serversFlags[$Streamer] = $database[$Streamer][2];
				}
				if( (!in_array($serverIP, $servers[$Streamer]) && !in_array($serverIP, $servers["not-pbbans"])) || $refresh == true ){					
					// Get server page from PBBANS
						$ServerBrowser = getUrl("https://www.pbbans.com/msi.php?searchdata=".$serverIP."&action=1", false);
						
					if( !($ServerBrowser === FALSE) ){
						// Search if server is streaming
						$flags = array(/* Cross Game, MBi, UMBi */);
						if ( strpos($ServerBrowser,'Server is streaming to PBBans') !== false ) {
							// Save Servers
							array_push($servers[$Streamer], $serverIP);
							
							// Search for the flags
								// Enforce Cross Game Bans
								array_push($flags, ((strpos($ServerBrowser,'Enforce Cross Game Bans')!==false)?true:false) );
								// Enforce MBi Bans
								array_push($flags, ((strpos($ServerBrowser,'Enforce MBi Bans')!==false)?true:false) );
								// Enforce UMBi Bans
								array_push($flags, ((strpos($ServerBrowser,'Enforce UMBi Bans')!==false)?true:false) );
							// Save the flags
								array_push($serversFlags[$Streamer], $flags);
						}else{
							array_push($servers["not-pbbans"], $serverIP);
						}
						
						// Update database
						$database[$Streamer] = array( $servers[$Streamer],  $week, $serversFlags[$Streamer], $servers["not-pbbans"]);
						file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
						
					}else{
						$connectionError[$Streamer]=true;
					}
				}
			// Update ACI
			}else{				
				// Get year's week
				$week = date("W");
				// Clear expired database (weekly)
				if( isset($database[$Streamer][1]) AND $database[$Streamer][1]!=$week ){
					$database[$Streamer][0] = array();
					$database[$Streamer][1] = $week;
					$database[$Streamer][2] = array();
					$database[$Streamer][3] = array();
					file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
				}
				
				// Search ip in database, if not...
				if( !isset($database[$Streamer]) ){
					$serversFlags[$Streamer] = array();
					$servers["not-aci"] = array();
				}else{
					$servers[$Streamer] = $database[$Streamer][0];
					$servers["not-aci"] = $database[$Streamer][3];
					$serversFlags[$Streamer] = $database[$Streamer][2];
				}
				if( (!in_array($serverIP, $servers[$Streamer]) && !in_array($serverIP, $servers["not-aci"])) || $refresh == true ){
					// Get server page from ACI
					$ServerBrowser = getUrl("http://www.anticheatinc.net/forums/streaming.php?address=".$serverIP."&submit=Submit", ($noProxyPlease == false)?true:false);
					if( !($ServerBrowser === FALSE) ){
						// Search if server is streaming
						$flags = array(/* LiveSecure */);
						if ( strpos($ServerBrowser,' is streaming to ACI BF4 repository') !== false ) {
							// Save Servers
							array_push($servers[$Streamer], $serverIP);
							
							// Search for the flags
								// Enforce Cross Game Bans
								array_push($flags, ((strpos($ServerBrowser,' is not enrolled in LiveSecure')!==false)?false:true) );
							// Save the flags
								array_push($serversFlags[$Streamer], $flags);
						}else{
							array_push($servers["not-aci"], $serverIP);
						}
						
						// Update database
						$database[$Streamer] = array( $servers[$Streamer],  $week, $serversFlags[$Streamer], $servers["not-aci"]);
						file_put_contents( "database/{$Streamer}_database_bf{$battlefield}.json",json_encode($database[$Streamer]));
						
					}else{
						$connectionError[$Streamer]=true;
					}
				}
			}
		}
		
		// Search for the server in databases
		$activeStreamers = array();
		// Cross game and unof. ban
		$banned = false;
		foreach ($StreamServices as $Streamer){
			if( isset($database[$Streamer]) ){
				if( isset($database[$Streamer][0]) ){
					$servers[$Streamer] = $database[$Streamer][0];
				}
				
				if( $database[$Streamer][1]!="updating" ){
					$activeStreamers[$Streamer]=(in_array($serverIP, $servers[$Streamer]))?true:false;
					
					if( ($Streamer=="pbbans" || $Streamer=="ggc") && $activeStreamers[$Streamer] && isset($database[$Streamer][2])){
						$key = array_search($serverIP, $servers[$Streamer]);
						if($Streamer=="pbbans"){
							$flags = $database[$Streamer][2][$key];
						}else{
							$serverLink = $database[$Streamer][2][$key];
						}
					}
				}else{
					echo "{\"update\":true}";
					// Die so not to return an image (cashe reasons)
					die();
				}
			}else{
				$activeStreamers[$Streamer]=false;
			}
		}
		
		if(!isset($_GET["json"])){
		// Create image
			header("Content-type: image/png");
			$image = imagecreatefrompng("database/bg.png");
			$font = './database/times.ttf';
			$font_fail = imagecolorallocate($image, 255, 160, 125);
			$font_ban = imagecolorallocate($image, 255, 0, 0);
			$font_success = imagecolorallocate($image, 100, 190, 250);
			$font_black = imagecolorallocate($image, 0, 0, 0);
			
			$color=($activeStreamers["pbbans"])?$font_success:$font_fail;
			//imagefilledrectangle($image, 2, 21, 46, 24, $color);
			imagefilledellipse( $image, 10, 9, 10, 10, $font_black );
			imagefilledellipse( $image, 10, 9, 8, 8, $color );
			
			$color=($activeStreamers["ggc"])?$font_success:$font_fail;
			//imagefilledrectangle($image, 54, 21, 96, 24, $color);
			imagefilledellipse( $image, 80, 9, 10, 10, $font_black );
			imagefilledellipse( $image, 80, 9, 8, 8, $color );
			
			$color=($activeStreamers["aci"])?$font_success:$font_fail;
			//imagefilledrectangle($image, 106, 21, 146, 24, $color);
			imagefilledellipse( $image, 155, 9, 10, 10, $font_black );
			imagefilledellipse( $image, 155, 9, 8, 8, $color );
			
			// turning on alpha channel information saving (to ensure the full range of transparency is preserved)
			imagesavealpha($image, true);

			header("Content: image/png");
			imagepng($image);
			imagedestroy($image);
		}else{
			// PBBans Connection error
				if(isset($connectionError["pbbans"])){
					$activeStreamers["pbbans"]="Unknown";
				}
			// ACI Connection error
				if(isset($connectionError["aci"])){
					$activeStreamers["aci"]="Unknown";
				}
			// GGC Connection error
				if(isset($connectionError["ggc"])){
					$activeStreamers["ggc"]="Unknown";
				}
			// Add pbbans flags at the end, if pbbans is on
				if( $activeStreamers["pbbans"] && isset($flags)){
					$activeStreamers["pbbans"]=$flags;
				}
			// Add ggc server link id, if ggc is on
				if(isset($serverLink)){
					$activeStreamers["ggc"]=$serverLink;
				}
			// Add refreshed tag
				if($refresh == true){
					$activeStreamers["refresh"]=true;
				}
			// Add no proxy tag
				if($noProxyPlease == true){
					$activeStreamers["no_proxy"]="Ok... just for you.";
				}
			// Json Echo
			echo json_encode($activeStreamers);
		}
	}
?>

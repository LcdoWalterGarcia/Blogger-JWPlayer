<!DOCTYPE html>
<html>
<head>
	<title>Blogger</title>
	<meta name="robots" content="noindex">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
	<script type="text/javascript" src="https://ssl.p.jwpcdn.com/player/v/8.23.1/jwplayer.js"></script>
	<script type="text/javascript">jwplayer.key="XSuP4qMl+9tK17QNb+4+th2Pm9AWgMO/cYH8CI0HGGr7bdjo";</script>
	<style type="text/css" media="screen">html,body{padding:0;margin:0;height:100%}#apicodes-player{width:100%!important;height:100%!important;overflow:hidden;background-color:#000}</style>
</head>
<body>

<?php 
		error_reporting(0);
		
		$link = (isset($_GET['link'])) ? $_GET['link'] : '';

		$sub = (isset($_GET['sub'])) ? $_GET['sub'] : '';

		$poster = (isset($_GET['poster'])) ? $_GET['poster'] : '';
		
		if ($link != '') {
 			
 				include_once 'config.php';

				include_once 'curl.php';

				$curl = new cURL();

				$getVideoLink = $curl->get($link);

				$dom = new DOMDocument();

				@$dom->loadHTML($getVideoLink);

				$xpath = new DOMXPath($dom);

				$nlist = $xpath->query("//iframe");

				$fileurl = $nlist[0]->getAttribute("src");

				$getSource = $curl->get($fileurl);

				preg_match('/VIDEO_CONFIG\s*\=\s*\{(.*?)\]\}/', $getSource, $match);
				
				$deJson = json_decode('{' . $match[1] . ']}');
				
				foreach ($deJson->streams as $key => $value) {

					switch ($value->format_id) {
						case '37':
								$s[1080] = '{"file": "'.$value->play_url.'", "type": "video\/mp4", "label": "1080p"}';
							break;

						case '22':
								$s[720] = '{"file": "'.$value->play_url.'", "type": "video\/mp4", "label": "720p"}';
							break;
						
						case '18':
								$s[360] = '{"file": "'.$value->play_url.'", "type": "video\/mp4", "label": "360p"}';
							break;
					}

				}

				krsort($s);
				
				$enJson = implode(',', $s);
				
				$sources = '['.$enJson.']';
			
				$checkSource = preg_match('/\[\]/', $sources, $match);
				
				if($checkSource) {
					$sources = '[{"label":"undefined","type":"video\/mp4","file":"undefined"}]';
				}

				$result = '<div id="apicodes-player"></div>';

				$data = 'var player = jwplayer("apicodes-player");
							player.setup({
								sources: '.$sources.',
								aspectratio: "16:9",
								startparam: "start",
								primary: "html5",
								preload: "auto",
								image: "'.$poster.'",
							    captions: {
							        color: "#f3f368",
							        fontSize: 16,
							        backgroundOpacity: 0,
							        fontfamily: "Helvetica",
							        edgeStyle: "raised"
							    },
							    tracks: [{ 
							        file: "'.$sub.'", 
							        label: "English",
							        kind: "captions",
							        "default": true 
							    }]
							});
				            player.on("setupError", function() {
				              swal("Server Error!", "Please contact us to fix it asap. Thank you!", "error");
				            });
							player.on("error" , function(){
								swal("Server Error!", "Please contact us to fix it asap. Thank you!", "error");
							});';
							
				$packer = new Packer($data, 'Normal', true, false, true);

				$packed = $packer->pack();

				$result .= '<script type="text/javascript">' . $packed . '</script>';
			
				echo $result;

		} else echo 'Link not found!';
?>
</body>
</html>
<?php
include_once("Http.class.php");
include_once("Error.class.php");

class YoukuUploader
{
	const ACCESS_TOKEN_URL = "https://openapi.youku.com/v2/oauth2/token";
	const UPLOAD_TOKEN_URL = "https://openapi.youku.com/v2/uploads/create.json";
	const UPLOAD_COMMIT_URL = "https://openapi.youku.com/v2/uploads/commit.json";
	const VERSION_UPDATE_URL = "http://open.youku.com/sdk/version_update";
	const REFRESH_FILE = "refresh.txt";

	private $client_id;
	private $client_secret;
	private $access_token;
	private $upload_token;
	private $upload_server_ip;
	private $refresh_token;

	public function __construct($client_id, $client_secret) { 
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}
	
	private function getAccessToken($params) {
		$parameter = array(
			"client_id"     => $this->client_id,
			"client_secret" => $this->client_secret,
			"grant_type" => 'password',
			"username"       => $params['username'],
			"password"       => $params['password'],
		);
		try {
			$result = json_decode(Http::post(self::ACCESS_TOKEN_URL, $parameter));
			if (isset($result->error)) {
				$error = $result->error;
				throw new UploadException($error->description,$error->code);
			}		
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}

	private function getUploadToken($uploadInfo) {
		$basic = array(
			"client_id" => $this->client_id,
			"access_token" => $this->access_token,
		);

		$params = array_merge($basic,$uploadInfo);
		try {
			$result = json_decode(Http::get(self::UPLOAD_TOKEN_URL,$params));
			if (isset($result->error)) {
				if ($result->error->code != 1009) {
					$error = $result->error;
					throw new UploadException($error->description,$error->code);
				}
			}
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}
		
	private function uploadCreate($file_name) {
		$fileSize = fileSize($file_name);
		$url = 'http://'. $this->upload_server_ip .'/gupload/create_file';
		$param = array(
			'upload_token' => $this->upload_token,
            'file_size' => $fileSize,
            'slice_length' => 1024,
            'ext' => $this->getFileExt($file_name)
        );
		try {
			$result = json_decode(Http::post($url, $param));
			if (isset($result->error)) {
				$error = $result->error;
				throw new UploadException($error->description,$error->code);
			}
		}catch(UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}
		
	private function createSlice() {
		$url = 'http://'. $this->upload_server_ip .'/gupload/new_slice';
		$param = array(
			'upload_token' => $this->upload_token
		);
		try {
			$result = json_decode(Http::get($url, $param));
			if (isset($result->error)) {
				$error = $result->error;
				throw new UploadException($error->description,$error->code);
			}
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}
		
	private function uploadSlice($slice_task_id, $offset, $length, $file_name) {
		$url =  'http://'.$this->upload_server_ip .'/gupload/upload_slice';
		$data =$this->readVideoFile($file_name, $offset, $length);
		$param = array( 
			'upload_token' => $this->upload_token,
            'slice_task_id' => $slice_task_id,
            'offset' => $offset,
            'length' => $length,
            'crc' => dechex(crc32($data)),
            'hash' => bin2hex(md5($data, true))
        );
		try {
			$result = Http::do_post_request($url, $param, $data);
			if (isset($result->error)) {
				$error = $result->error;
				throw new UploadException($error->description,$error->code);
			}			
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;			
	}	

	private function getFileExt($file_name) {
		$path_parts = pathinfo($file_name);
		return $path_parts['extension'];
	}

	private function readVideoFile($filename, $offset, $length) {
		try {
			$handle = fopen($filename, "rb");
			if (!$handle) {
				throw new Exception("Could not open the file!");
			}
			$data = stream_get_contents($handle, $length, $offset); 
			fclose($handle);
			return $data;
		} catch (Exception $e) {
			echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()."\n";
		}
	}
	
	private function commit($uploadServerIp) {
		$param = array(
			'access_token' => $this->access_token,
			'client_id' => $this->client_id,
			'upload_token' => $this->upload_token,
			'upload_server_ip' => $uploadServerIp 
		);
		try {
			$result = json_decode(Http::get(self::UPLOAD_COMMIT_URL, $param));
			if (isset($result->error)) {
				$error = $result->error;			
				throw new UploadException($error->description,$error->code);
			}
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}

	private function versionUpdate($verlog) {
		$file = @fopen($verlog, "r");
		if (!$file) echo "Could not open " . $verlog . "!\n";
		else {
			$version = trim(fgets($file));
			echo "Your current sdk version is:" . $version . "\n";
			$param = array(
				'client_id' => $this->client_id,
				'version' => $version,
				'type' => 'php'
			);		
			Http::get(self::VERSION_UPDATE_URL, $param);		
			fclose($file);
		}		
	}

	private function check() {
		$url = 'http://'. $this->upload_server_ip .'/gupload/check';
		$param = array(
			'upload_token' => $this->upload_token
		);
		try {
			$result = json_decode(Http::get($url, $param));
			if (isset($result->error)) {
				$error = $result->error;
				throw new UploadException($error->description,$error->code);
			}
		}catch (UploadException $e) {
			echo $e->getError();
			exit;
		}
		return $result;
	}
	
	private function refreshToken() {
		$parameter = array(
            "client_id"     => $this->client_id,
            "client_secret" => $this->client_secret,
            "grant_type" 	=> 'refresh_token',
            "refresh_token" => $this->refresh_token
        );
        try {
            $result = json_decode(Http::post(self::ACCESS_TOKEN_URL, $parameter));
            if (isset($result->error)) {
                $error = $result->error;
                throw new UploadException($error->description,$error->code);
            }
        }catch (UploadException $e) {
            echo $e->getError();
            exit;
        }
        return $result;		
	}

	private function readRefreshFile($refresh_file) {
		$file = @fopen($refresh_file, "r");
		if ($file) {
			$refreshInfo = json_decode(trim(fgets($file)));
			$this->access_token = isset($refreshInfo->access_token) ? $refreshInfo->access_token : '';
			$this->refresh_token = isset($refreshInfo->refresh_token) ? $refreshInfo->refresh_token : '';
			fclose($file);
		}
	}
	
	private function writeRefreshFile($refresh_file,$refresh_json_result) {
		$file = @fopen($refresh_file, "w");
		if (!$file) echo "Could not open " . $refresh_file . "!\n";
		else {
            $refreshInfo = json_encode($refresh_json_result);
			$fw = @fwrite($file, $refreshInfo);
			if (!$fw) echo "Write refresh file fail!\n";
            fclose($file);
		}		
	}
	
	public function upload($upload_process = true, $params = array(),$uploadInfo = array()) { 
		if(isset($params['access_token']) && !empty($params['access_token'])) {
			$this->access_token = $params['access_token'];
			if (isset($params['refresh_token']) && !empty($params['refresh_token'])) $this->refresh_token = $params['refresh_token'];
			//$this->readRefreshFile(self::REFRESH_FILE);			
		}else {	
			echo "Only applys to the clients of partner level!\n";
			$result = $this->getAccessToken($params);
			if (isset($result->access_token)) $this->access_token = $result->access_token; 
		}
		$this->versionUpdate('verlog.txt');
		$uploadResult = $this->getUploadToken($uploadInfo);
		if (isset($uploadResult->error) && $uploadResult->error->code == 1009 && !empty($this->refresh_token)) {
			$refreshResult = $this->refreshToken();
			$this->access_token = $refreshResult->access_token;
			$this->refresh_token = $refreshInfo->refresh_token;
			$this->writeRefreshFile(self::REFRESH_FILE,$refreshResult);
			$uploadResult = $this->getUploadToken($uploadInfo);
		}
		if (!isset($uploadResult->upload_token)) exit;
		$this->upload_token = $uploadResult->upload_token;
		$file_name = $uploadInfo['file_name'];
		$this->upload_server_ip = gethostbyname($uploadResult->upload_server_uri);
		$uploadCreate = $this->uploadCreate($file_name);
		echo "Uploading start!\n";
		$finish = false;
		$transferred = 0;	

		$sliceResult = $this->createSlice();	
		$slice_id = $sliceResult->slice_task_id;
		$offset = $sliceResult->offset;
		$length = $sliceResult->length;
		$uploadServerIp = '';
		do {
			$uploadSlice = $this->uploadSlice($slice_id,$offset,$length,$file_name);
			$slice_id = $uploadSlice->slice_task_id;
            $offset = $uploadSlice->offset;
            $length = $uploadSlice->length;
			$transferred = (int)round($uploadSlice->transferred/$uploadInfo['file_size']*100);
			if ($slice_id == 0) {
				do {
					$checkResult = $this->check();
					if (isset($checkResult->status)) {
						$finish = $checkResult->finished;
						if ($checkResult->status == 1) {
							$uploadServerIp = $checkResult->upload_server_ip;
							$transferred = 100;
							break;
						}else if ($checkResult->status == 2 || $checkResult->status == 3) {
							$transferred = $checkResult->confirmed_percent;
						//	sleep(20);					
						}/*else if($checkResult->status == 4) {
						//	$transferred = $checkResult->confirmed_percent;
							break;
						}*/
					}
				}while(1);
			}	
			if ($upload_process) echo "Upload progress:{$transferred}%\n";
		}while(!$finish);
		if ($finish) {
			$commitResult = $this->commit($uploadServerIp);
			echo "Uploading success!\n";
			if (isset($commitResult->video_id)) echo "videoid: ". $commitResult->video_id."\n";
		}
	}
}
?>

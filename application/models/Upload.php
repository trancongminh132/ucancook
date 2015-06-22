<?php

class Upload {

    protected $max_filesize_upload;
    protected $upload_path;
    protected $thumb_folder;
    protected $media_folder;
    protected $extenion;
    protected $file_error_log_path;
    protected $log_path;
    protected $userName;
    protected $publicDomain;
    protected $allowExtensions;

    protected $error_message = array(
        0 => 'Upload Successful',
        1 => 'Tên file không hợp lệ',
        2 => 'Kích thước ảnh quá nhỏ hoặc quá lớn',
        3 => 'Ảnh quá dài hoặc quá ốm',
        4 => 'Kiểu file không hợp lệ',
        5 => 'Upload file thất bại',
        6 => 'Upload file thất bại'
    );

    function __construct() {
        require_once LIBS_PATH . '/My/phmagick/phmagick.php';

        $config = My_Zend_Globals::getConfiguration();
        $this->max_filesize_upload = $config->photo->upload->max_file_size * 1024 * 1024;
        $this->upload_path = $config->photo->upload->dir;
        $this->log_path = $config->photo->upload->log->path;
        $this->publicDomain = $config->photo->domain;
        $this->allowExtensions = explode(',', $config->photo->upload->extension_allow);
        $this->extenion = '.jpg';
    }

    function upload($upload, $destFolder = array(), $params=array()) 
    {
        $info = array();

        if ($upload && is_array($upload['tmp_name'])) 
        {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
                $fileData = array(
                    'tmp_name' => $upload['tmp_name'][$index],
                    'error' => $upload['error'][$index],
                    'name' => isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    'size' => isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    'type' => isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index]
                );

                $rs = $this->handleFileUpload($fileData, $destFolder);
                $info[] = $rs;

                $this->doResize($rs);
            }
        } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) 
        {
            // $_FILES is a one-dimensional array:
            $rs = $this->handleFileUpload($upload, $destFolder);
            $info[] = $rs;
			
            $this->doResize($rs);
        }

        return $info;
    }

    /**
     * Handle process upload file to server
     * @param array $fileData
     * @param array $rootFolder
     * @return multitype:number |multitype:number unknown |multitype:string unknown multitype:
     */
    function handleFileUpload($fileData, $destFolders = array()) {
        if (!$fileData) {
            return array('error_code' => 1);
        }

        try
        {
            // neu upload thanh cong
            if ($fileData['error'] === UPLOAD_ERR_OK) {
                $fileSize = $fileData['size'];

                $fileError = $fileData['error'];
                $fileName = $fileData['name'];
                $fileType = $fileData['type'];
                $arrExt = explode('.', trim($fileName));
                $ext = strtolower($arrExt[count($arrExt) - 1]);
                $this->extenion = '.'. $ext;
                array_pop($arrExt);
                $photoName = implode(' ', $arrExt);

                if ($fileName == '')
                    return array('error_code' => 1, 'name' => $fileName, 'size' => $fileSize);

                $imageSize = getimagesize($fileData['tmp_name']);

                if (!$imageSize)
                    return array('error_code' => 4, 'name' => $fileName, 'size' => $fileSize);

                list($width, $height, $type, $attr) = $imageSize;

                $systemName = time() . rand();
                $systemName = md5($systemName);

                $folders = array();

                // init dest folders
                if (is_array($destFolders) && !empty($destFolders)) {
                    $destFolders = array_values($destFolders);

                    foreach ($destFolders as $key => $folder) {
                        $folders[$key] = $folder;
                    }
                } else {
                    $folders[0] = date('Y');
                    $folders[1] = strtolower($systemName[0]);
                    $folders[2] = strtolower($systemName[1]);
                }

                // check folder exist
                if (!$this->checkSystemFolder($folders)) {
                    return array('error_code' => 5, 'name' => $fileName, 'size' => $fileSize);
                }

                $folder = '';

                foreach ($folders as $tmp) {
                    $folder .= $tmp . '/';
                }

                $folder = rtrim($folder, '/');

                $uploadTo = $this->upload_path . "/" . $folder . "/" . $systemName . $this->extenion;

                // upload to original file
                if (move_uploaded_file($fileData['tmp_name'], $uploadTo)) {
                    return array("name" => $photoName,
                        "url" => $this->publicDomain . '/uploads/' . $folder . '/' . $systemName . $this->extenion,
                        "path"  => $folder,
                        "sys_name" => $systemName,
                        "ext" => $this->extenion,
                        "w" => $width,
                        "h" => $height,
                        "size" => $fileSize

                    );
                } else {
                    return array('error_code' => 8, 'name' => $fileName, 'size' => $fileSize);
                }
            }

            return array('error_code' => 7);
        } catch (Exception $ex) {
            return array('error_code' => -7);
        }
    }

    private function checkSystemFolder($folderName) {
        try {
            $rs = false;

            if (is_array($folderName)) {
                $path = $this->upload_path;
                foreach ($folderName as $folder) {
                    $path .= '/' . $folder;
                    if (!is_dir($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }
                }

                $rs = true;
            } elseif (!is_dir($this->upload_path . '/' . $folderName)) {
                $oldmask = umask(0);
                $rs = mkdir($this->upload_path . '/' . $folderName, 0777, true);
                umask($oldmask);
            } else {
                $rs = true;
            }

            return $rs;
        } catch (Exception $ex) {
            My_Zend_Logger::log($ex->getMessage());
            return false;
        }
    }

    function createSystemFolder() {
        try {
            $year = date('Y', time());

            // create current year folder
            if (!is_dir($this->upload_path . '/' . $year)) {
                $oldmask = umask(0);
                mkdir($this->upload_path . '/' . $year, 0777, true);
                umask($oldmask);
            }

            if (!is_dir($this->upload_path . '/' . $year . '/' . $username)) {
                $oldmask = umask(0);
                mkdir($this->upload_path . '/' . $year . '/' . $username, 0777, true);
                umask($oldmask);
            }
        } catch (Exception $ex) {
            $this->writeErrorLog('Cannot create folder: ' . $ex->getMessage());
        }

        return false;
    }

    function handleError($message, $exit = false, $code = 500) {
        switch ($code) {
            case 500:
                header("HTTP/1.1 500 Internal Server Error");
                break;
            case 501:
                header("HTTP/1.1 501 Not Implemented");
                break;
            case 502:
                header("HTTP/1.1 502 Bad Gateway");
                break;
            case 503:
                header("HTTP/1.1 503 Service Unavailable");
                break;
        }
        echo $message;
        if ($exit)
            exit;
    }

    function resize($source, $width = 1024, $height = 1024, $exactDimentions = false, $background = '') {
        $phMagick = new phMagick($source, $source);
        return $phMagick->resize($width, $height, $exactDimentions, $background);
    }

    function uploadFile() {
        $fileData = isset($_FILES['Filedata']) ? $_FILES['Filedata'] : null;
        $signKey = isset($_POST['signkey']) ? $_POST['signkey'] : '';

        // validate
        if (!$this->verifySignKey($signKey)) {
            /* return array(
              'error_code' => 6,
              ); */
        }

        $rs = array();

        if ($fileData || isset($_SERVER['HTTP_X_FILE_NAME'])) {

            if ($fileData['error'] === UPLOAD_ERR_OK) {
                $fileSize = $fileData['size'];
                $fileError = $fileData['error'];
                $fileName = $fileData['name'];
                $fileType = $fileData['type'];
                $arrExt = explode('.', trim($fileName));
                $ext = strtolower($arrExt[count($arrExt) - 1]);
                array_pop($arrExt);
                $photoName = implode(' ', $arrExt);

                if ($fileName == '')
                    return array('error_code' => 1, 'name' => $fileName, 'size' => $fileSize);

                $systemName = time() . rand();
                $systemName = md5($systemName);

                // get first character of name
                $folders[0] = $this->media_folder;
                $folders[1] = 'files';
                $folders[2] = date('Y');

                // check folder exist
                if (!$this->checkSystemFolder($folders))
                    return array('error_code' => 5, 'name' => $fileName, 'size' => $fileSize);

                $folder = '';

                foreach ($folders as $tmp) {
                    $folder .= $tmp . '/';
                }

                $folder = rtrim($folder, '/');

                $uploadTo = $this->upload_path . '/' . $folder . "/" . $systemName . '.' . $ext;

                // upload to original file
                if (move_uploaded_file($fileData['tmp_name'], $uploadTo)) {
                    return array("name" => $photoName,
                        "url" => $this->publicDomain . '/' . $folder . '/' . $systemName . '.' . $ext
                    );
                } else {
                    return array('error_code' => 8, 'name' => $fileName, 'size' => $fileSize);
                }
            }
        }

        return $rs;
    }

    public function doResize($data) 
    {
        try 
        {
            $config = My_Zend_Globals::getConfiguration();

            $source = $config->photo->upload->dir . '/' . $data['path'] . '/' . $data['sys_name'] . $data['ext'];
			
            if (is_file($source)) 
            {
                $thumbnail = $config->photo->thumbnail->thumb;
                $thumbnail = explode(',', $thumbnail);
               	
                foreach ($thumbnail as $size)
	        	{
		        	if(strpos($size, 'x') !== false)
		        	{
		        		list($width, $height) = explode('x', $size);
		        	}
		        	else
		        	{
		        		$height = $width = intval($size);	        		
		        	}
					
                    $newName = $config->photo->upload->dir .'/'. $data['path'] .'/'. $data['sys_name'] .'_'. $width .'x'. $height . $data['ext'];
                    $phMagick = new phMagick($source, $newName);
					
                    $rs = $phMagick->resize($width, $height, false, 'transparent')->getLog();
                    
                }

                //$phMagick = new phMagick($source, $source, false, 'white');
                //$phMagick->resize($config->photo->upload->size, $config->photo->upload->size);
                return true;
            }

            return false;
        } catch (Exception $ex) {
            // log
            return false;
        }
    }
}

?>
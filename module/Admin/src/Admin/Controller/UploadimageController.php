<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Test\Data;
use Admin\Util\Util;

class UploadimageController extends AbstractController {
	
	private static $uploadsrc = array (
// 			"news",
// 			"job",
	        "upload"
	);
	
	// Just render the template  
	public function adminAction(){
		
	}
	
	public function indexAction() {
		try {
			
			$viewData = array ();
			$requestParams = $this->params ()->fromQuery ();
			$imagesrcarray = self::$uploadsrc;
			
			$viewData ['imagesrc'] = $imagesrcarray;
			
			if (isset ( $requestParams ['Continue'] ) && $requestParams ['Continue'] == 'Continue') {
				
				if (! in_array ( $requestParams ['basedir'], $imagesrcarray )) {
					die ( "error dir" );
				}
				try {
					$root = Util::getCmsWritableDir ( "images" );
				}catch (\Exception $e){
					die($e->getMessage().", Please add the image path  to Shopping\\config\\config.ENV.php as follow('writableDir'=>array(''images' => '/Library/WebServer/Documents/pradmin/public'')) ");
				}
				
				
				if (! is_dir ( $root . $requestParams ['basedir'] )) {
					@mkdir ( $root . $requestParams ['basedir'] . "/", 0777, true );
				}
				
				
				$dirend = trim ( $requestParams ['dirend'] );
				if ($dirend) {
					$imgroot = $root . $requestParams ['basedir'] . "/" . $dirend . "/";
					if (! is_dir ( $root . $requestParams ['basedir'] . "/" . $dirend )) {
						@mkdir ( $root . $requestParams ['basedir'] . "/" . $dirend, 0777, true );
					}
				} else {
					$imgroot = $root . $requestParams ['basedir'] . "/";
				}
				
				if(!is_writable($imgroot)){
					$this->_message($root."   is unwriteable!",self::MSG_ERROR);
				}
				
				$files = glob ( $imgroot . '*' );
				
				$newfileslist = array ();
				$subDirList = array ();
				
				foreach ( $files as $key => $value ) {
					
					if (is_dir ( $value )) {
						$dir ['dirname'] = str_replace ( $root, "", $value );
						// dir.
						$dir ['filetype'] = 1;
						$subDirList [] = $dir;
					} else {
					
						$subRoot = str_replace ( $root, "", $value );
						$valArr = explode ( "/", $value );
						// file.
						$array ['filetype'] = 2;
						
						$array ['imagename'] = array_pop ( $valArr );
						$array ['imagesize'] = $this->_byteFormat ( filesize ( $value ), 2 );
						$imageInfo = getimagesize ( $value );
						if ($imageInfo) {
							$array ['size'] = $imageInfo [0] . " X " . $imageInfo [1];
							// image.
							$array ['filetype'] = 3;
						}
						$subRoot = str_ireplace("//", "/", $subRoot);
						$array ['src'] = $subRoot;
						$newfileslist [] = $array;
					}
				}
				$newfileslist = array_merge ( $subDirList, $newfileslist );
				
				$nowsrc = str_replace ( $root, "", $imgroot );
				$viewData ['files'] = $newfileslist;
				
				$viewData ['rootsrc'] = $imgroot;
				// init crumb.
				$crumbs [] = array (
						'title' => $requestParams ['basedir'],
						'dirend' => '' 
				);
				$pathsrc = str_replace ( $requestParams ['basedir'], "", $nowsrc );
				$nowsrcArr = $pathsrc ? array_filter ( explode ( '/', $pathsrc ) ) : array ();
				if ($nowsrcArr) {
					foreach ( $nowsrcArr as $k => $src ) {
						$link ['title'] = $src;
						$direndArr = array_slice ( $nowsrcArr, 0, $k );
						$link ['dirend'] = implode ( '/', $direndArr );
						$crumbs [] = $link;
					}
				}
				$viewData ['crumbs'] = $crumbs;
				$viewData ['basedir'] = $requestParams ['basedir'];
				$viewData ['dirend'] = $dirend;
				
				$viewModel = new ViewModel ( $viewData );
				$viewModel->setTemplate ( "admin/uploadimage/list.phtml" );
				return $viewModel;
			}
		} catch ( \Exception $e ) {
			echo $e->getMessage ();
		}
		return new ViewModel ( $viewData );
	}
	private function _byteFormat($size, $dec = 2) {
		$unitArr = array (
				"B",
				"KB",
				"MB",
				"GB",
				"TB",
				"PB" 
		);
		$pos = 0;
		while ( $size >= 1024 ) {
			$size /= 1024;
			$pos ++;
		}
		return round ( $size, $dec ) . " " . $unitArr [$pos];
	}
	public function ajaxUploadAction() {
		if ($this->getRequest ()->isPost ()) {
			do {
				
				$uploadconfig = include (__DIR__ . "/../../../config/upload.config.php");
				$config = Data::getInstance ()->get ('config');
				$requestParams = $this->params ()->fromPost ();
				
				$error = "";
				// Compatible revo
				if ($_POST ['extension']) {
					$fileName = $_POST ['filename'];
					$ext = strtolower ( $_POST ['extension'] );
					$fileNameWithExt = $fileName . '.' . $ext;
					// $_POST ['cover'] = 'false';
					$isCover = ! empty ( $_POST ['override'] ) ? $_POST ['override'] : false;
				} else {
					$pathinfo = pathinfo ( $_POST ['filename'] );
					$fileName = $pathinfo ['filename'];
					$ext = strtolower ( $pathinfo ['extension'] );
					$fileNameWithExt = $fileName . '.' . $ext;
					// all the $_POST parameters are string type.
					$isCover = ('false' == $_POST ['cover']) ? false : true;
				}
				
				$allowExt = array_keys ( $uploadconfig ['extension'] );
				
				if ($_FILES ['file'] ['error'] > 0) {
					$error = "{$fileNameWithExt} 上传错误. 错误原因 : " . $this->getErr ( $_FILES ['file'] ['error'] );
					break;
				}
				
				if (! in_array ( $ext, $allowExt )) {
					$error = $ext . " 文件类型 {$fileNameWithExt} 错误";
					break;
				}
				
				$mime = $uploadconfig ['extension'] [$ext];
				$mimearray = array ();
				if (count ( $mime ['mimetyle'] ) > 1) {
					$mimearray = $mime ['mimetyle'];
				} else {
					$mimearray [] = $mime ['mimetyle'];
				}
				
				preg_match ( "/^([0-9a-zA-Z-_]+)$/i", $fileName, $s );
				if (empty ( $s )) {
					$error = "文件名 {$fileNameWithExt} 只能包括 [0-9a-zA-Z-_] ";
					break;
				}
				
				if (file_exists ( $_POST ['imgroot'] . $fileNameWithExt ) && ! $isCover) {
					$error = "文件 {$fileNameWithExt} 已存在 请选中替换已经存在的图片";
					break;
				}
				
				if (! is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
					$error = "文件上次错误 文件名称" . $_FILES ['file'] ['tmp_name'];
					break;
				}
				
//				$finfo = finfo_open ( 16 );
//				$filetype = finfo_file ( $finfo, $_FILES ['file'] ['tmp_name'] );
				
// 				if (! in_array ( $filetype, $mimearray )) {
// 					$error = $filetype . ".' 文件类型 {$fileNameWithExt} 错误'";
// 					break;
// 				}
				
//				finfo_close ( $finfo );
				$data = array();
				if (file_exists ( $_POST ['imgroot'] . $fileNameWithExt )) {
				    $title = "修改图片";
					
					
// 					if ($requestParams ['dirend']) {
// 						$akaimai_src = $config ['imageserver'] . $requestParams ['basedir'] . "/" . $requestParams ['dirend'] . "/" . $fileNameWithExt;
// 					} else {
// 						$akaimai_src = $config ['imageserver'] . $requestParams ['basedir'] . "/" . $fileNameWithExt;
// 					}
					
					//\Admin\Util\Util::clearAkamai ( $akaimai_src );
				} else {
				    $title = "上传图片";
					//move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $_POST ['imgroot'] . $fileNameWithExt );
				}
				move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $_POST ['imgroot'] . $fileNameWithExt );
				$data['path'] = $_POST ['imgroot'] . $fileNameWithExt;
				//$this->saveLog($title, $data);
			} while ( false );
			
			$result = array ();
			if (! $error) {
				$result = json_encode ( array (
						"status" => 1 
				) );
			} else {
				$result = json_encode ( array (
						"status" => 0,
						"error" => $error 
				) );
			}
			echo $result;
			exit ();
		}
	}
	private function getErr($intErr) {
		$errStr = "";
		switch ($intErr) {
			case 1 :
				$errStr = "UPLOAD_ERR_INI_SIZE  ";
				break;
			case 2 :
				$errStr = "UPLOAD_ERR_FORM_SIZE";
				break;
			case 3 :
				$errStr = "UPLOAD_ERR_PARTAL";
				break;
			case 4 :
				$errStr = "UPLOAD_ERR_NO_FILE";
				break;
		}
		return $errStr;
	}
	
	/**
	 * delete node action
	 */
	public function deleteAction() {
		$imagesrcarray = self::$uploadsrc;
		$fileName = $this->params ()->fromQuery ( 'filename' );
		$basedir = $this->params ()->fromQuery ( 'basedir' );
		$dirend = $this->params ()->fromQuery ( 'dirend' );
		if (in_array ( $basedir, $imagesrcarray )) {
			if ($dirend) {
				$filePath = $basedir . "/" . $dirend . "/" . $fileName;
			} else {
				$filePath = $basedir . "/" . $fileName;
			}
			$root = Util::getCmsWritableDir ( "images" );
			if (file_exists ( $root . $filePath )) {
				@unlink ( $root . $filePath );
				$data = array();
				$data['path'] = $filePath;
				//$this->saveLog("删除图片", $data);
				// clean Akamai cache
				//$config = Data::getInstance ()->get ( 'config' );
				//$akaimai_src = $config ['imageserver'] . $filePath;
				//\Admin\Util\Util::clearAkamai ( $akaimai_src );
			}
		}
		
		return $this->redirect ()->toRoute ( 'default', array (
				'controller' => 'uploadimage',
				"action" => "index" 
		), array (
				"query" => array (
						"basedir" => $basedir,
						"dirend" => $dirend,
						"Continue" => "Continue" 
				) 
		) );
	}
}
?>
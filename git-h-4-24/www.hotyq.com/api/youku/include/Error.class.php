<?php 
class UploadException extends Exception 
{
	protected $errorCode;
	protected $errorMessage;
	/**
	 *
	 * @param unknown $code
	 *        	code和message使用父类的属性
	 * @param unknown $message
	 *        	code和message使用父类的属性
	 * @param string $previous        	
	 */
	public function __construct($message, $code) {
		parent::__construct($message,$code);
		$this->errorCode = $code;
		$this->errorMessage = $message;
	}
    public function getError() {
		$error = array(
				'error' => array(
						'code' => $this->errorCode,
						'descrption' => $this->errorMessage
					)
				);
		return json_encode($error) . "\n";
    }
}
?>

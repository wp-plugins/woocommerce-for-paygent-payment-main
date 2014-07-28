<?php
/**
 * PAYGENT B2B MODULE
 * PaygentB2BModuleLogger.php
 * 
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/system/PaygentB2BModuleResources.php");

/**
 * �ڑ����W���[���p Logger �N���X
 * 
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */

class PaygentB2BModuleLogger {

	/** FileAppender �ێ� */
	var $filename = null;

	/**
	 * �R���X�g���N�^
	 */
	function PaygentB2BModuleLogger() {
		$inst = PaygentB2BModuleResources::getInstance();
		if (is_object($inst) && 
			!StringUtil::isEmpty($inst->getLogOutputPath())) {
			$this->filename = $inst->getLogOutputPath();
		}
	}
	
	/**
	 * PaygentB2BModuleLogger ���擾
	 * 
	 * @return PaygentB2BModuleLogger
	 */
	static function &getInstance() {
		static $logInstance = null;		
		if (isset($logInstance) == false
			|| $logInstance == null
			|| is_object($logInstance) != true) {

			$logInstance = new PaygentB2BModuleLogger();
		}
		return $logInstance;
	}

	/**
	 * �f�o�b�O���O���o��
	 * 
	 * @param className String ���O�̏o�͌��N���X�� �o�͌�������
	 * @param message Object ���O���b�Z�[�W
	 */
	function debug($className, $message) {
		if(is_null($this->filename) == false && $this->filename != "") {
			if(! $handle = fopen( $this->filename, 'a')) {
				// �t�@�C�����J���Ȃ�
				trigger_error(PaygentB2BModuleException__OTHER_ERROR. ":File doesn't open.(".$this->filename.").", E_USER_WARNING);
				return;
			}
			if(! fwrite($handle, $this->outputMsg($message, $className))) {
				// �t�@�C���ɏ������߂Ȃ�
				trigger_error(PaygentB2BModuleException__OTHER_ERROR. ":It is not possible to write it in the file(".$this->filename.").", E_USER_WARNING);
				return;
			}
			fclose($handle);
		}
	}
	
	/**
	 * �o�̓��b�Z�[�W�𐮌`����
	 * 
	 * @param message ���O���b�Z�[�W
	 * @param className �N���X��
	 * @return ���`��̃��b�Z�[�W
	 */
	function outputMsg($message, $className) {
		return date("Y/m/d H:i:s")." $className ".$message."\n";
	}
}

?>

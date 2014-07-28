<?php
/**
 * PAYGENT B2B MODULE
 * PaygentB2BModuleConnectException.php
 * 
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

/*
 * 接続モジュール　接続エラー用Exception
 *
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */


	define("PaygentB2BModuleConnectException__serialVersionUID", 1);

	/**
	 * モジュールパラメータエラー
	 */
	define("PaygentB2BModuleConnectException__MODULE_PARAM_REQUIRED_ERROR", "E02001");

	/**
	 * 電文要求パラメータエラー
	 */
	define("PaygentB2BModuleConnectException__TEREGRAM_PARAM_REQUIRED_ERROR", "E02002");

	/**
	 * 電文要求パラメータ固定値想定外エラー
	 */
	define("PaygentB2BModuleConnectException__TEREGRAM_PARAM_OUTSIDE_ERROR", "E02003");

	/**
	 * 証明書エラー
	 */
	define("PaygentB2BModuleConnectException__CERTIFICATE_ERROR", "E02004");

	/**
	 * 決済センター接続エラー
	 */
	define("PaygentB2BModuleConnectException__KS_CONNECT_ERROR", "E02005");

	/**
	 * 応答対応種別エラー
	 */
	define("PaygentB2BModuleConnectException__RESPONSE_TYPE_ERROR", "E02007");

 
 class PaygentB2BModuleConnectException {
 
	/** エラーコード */
	var $errorCode = "";

	/**
	 * コンストラクタ
	 * 
	 * @param errorCode String
	 * @param msg String
	 */
	function PaygentB2BModuleConnectException($errCode, $msg = null) {
		$this->errorCode = $errCode;
	}

	/**
	 * エラーコードを返す
	 * 
	 * @return String errorCode
	 */
	function getErrorCode() {
		return $this->errorCode;
	}
	
	/**
	 * メッセージを返す
	 * 
	 * @return String code=message
	 */
    function getLocalizedMessage() {
    }
 	
 }
  
?>

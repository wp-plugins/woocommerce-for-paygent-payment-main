<?php
/**
 * PAYGENT B2B MODULE
 * PaygentB2BModuleException.php
 *
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

/*
 * 接続モジュール　各種エラー用Exception
 *
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */

	define("PaygentB2BModuleException__serialVersionUID", 1);

	/**
	 * 設定ファイルなしエラー
	 */
	define("PaygentB2BModuleException__RESOURCE_FILE_NOT_FOUND_ERROR", "E01001");

	/**
	 * 設定ファイル不正エラー
	 */
	define("PaygentB2BModuleException__RESOURCE_FILE_REQUIRED_ERROR", "E01002");

	/**
	 * その他のエラー
	 */
	define("PaygentB2BModuleException__OTHER_ERROR", "E01901");

	/**
	 * CSV出力エラー
	 */
	define("PaygentB2BModuleException__CSV_OUTPUT_ERROR", "E01004");

	/**
	 * 取引ファイルエラー
	 */
	define("PaygentB2BModuleException__FILE_PAYMENT_ERROR", "E01005");


 class PaygentB2BModuleException {

	/** エラーコード */
	var $errorCode = "";

	/**
	 * コンストラクタ
	 *
	 * @param errorCode String
	 * @param msg String
	 */
	function PaygentB2BModuleException($errCode, $msg = null) {
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

<?php
/**
 * PAYGENT B2B MODULE
 * PaygentB2BModuleResources.php
 * 
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

/*
 * プロパティファイル読込、値保持クラス
 * 
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */

include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/StringUtil.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleConnectException.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleException.php");

	/**
	 * プロパティファイル名
	 */
//	define("PaygentB2BModuleResources__PROPERTIES_FILE_NAME", WC_PAYGENT_PLUGIN_PATH."/modenv_properties.php");

	/**
	 * 照会系電文種別の区切り文字
	 */
	define("PaygentB2BModuleResources__TELEGRAM_KIND_SEPARATOR", ",");
	
	/**
	 * 電文種別の先頭桁数（接続先URL取得）
	 */
	define("PaygentB2BModuleResources__TELEGRAM_KIND_FIRST_CHARS", 2);

	/**
	 * クライアント証明書ファイルパス
	 */
	define("PaygentB2BModuleResources__CLIENT_FILE_PATH", "paygentB2Bmodule.client_file_path");

	/**
	 * CA証明書ファイルパス
	 */
	define("PaygentB2BModuleResources__CA_FILE_PATH", "paygentB2Bmodule.ca_file_path");

	/**
	 * Proxyサーバ名
	 */
	define("PaygentB2BModuleResources__PROXY_SERVER_NAME", "paygentB2Bmodule.proxy_server_name");

	/**
	 * ProxyIPアドレス
	 */
	define("PaygentB2BModuleResources__PROXY_SERVER_IP", "paygentB2Bmodule.proxy_server_ip");

	/**
	 * Proxyポート番号
	 */
	define("PaygentB2BModuleResources__PROXY_SERVER_PORT", "0");

	/**
	 * デフォルトID
	 */
	define("PaygentB2BModuleResources__DEFAULT_ID", "paygentB2Bmodule.default_id");

	/**
	 * デフォルトパスワード
	 */
	define("PaygentB2BModuleResources__DEFAULT_PASSWORD", "paygentB2Bmodule.default_password");

	/**
	 * タイムアウト値
	 */
	define("PaygentB2BModuleResources__TIMEOUT_VALUE", "paygentB2Bmodule.timeout_value");

	/**
	 * ログ出力先
	 */
	define("PaygentB2BModuleResources__LOG_OUTPUT_PATH", "paygentB2Bmodule.log_output_path");

	/**
	 * 照会MAX件数
	 */
	define("PaygentB2BModuleResources__SELECT_MAX_CNT", "paygentB2Bmodule.select_max_cnt");

	/**
	 * 照会系電文種別ID
	 */
	define("PaygentB2BModuleResources__TELEGRAM_KIND_REFS", "paygentB2Bmodule.telegram_kind.ref");

	/**
	 * 接続先URL（共通）
	 */
	define("PaygentB2BModuleResources__URL_COMM", "paygentB2Bmodule.url.");

	/**
	 * デバッグオプション
	 */
	define("PaygentB2BModuleResources__DEBUG_FLG", "paygentB2Bmodule.debug_flg");

 class PaygentB2BModuleResources {
 	
	/** クライアント証明書ファイルパス */
	var $clientFilePath = "";

	/** CA証明書ファイルパス */
	var $caFilePath = "";

	/** Proxyサーバ名 */
	var $proxyServerName = "";

	/** ProxyIPアドレス */
	var $proxyServerIp = "";

	/** Proxyポート番号 */
	var $proxyServerPort = 0;

	/** デフォルトID */
	var $defaultId = "";

	/** デフォルトパスワード */
	var $defaultPassword = "";

	/** タイムアウト値 */
	var $timeout = 0;

	/** ログ出力先 */
	var $logOutputPath = "";

	/** 照会MAX件数 */
	var $selectMaxCnt = 0;
	
	/** 設定ファイル（プロパティ） */
	var $propConnect = null;

	/** 照会系電文種別リスト */
	var $telegramKindRefs = null;

	/** デバッグオプション */
	var $debugFlg = 0;

	/**
	 * コンストラクタ
	 */
	function PaygentB2BModuleResources() {
	}

	/**
	 * PaygentB2BModuleResources を取得
	 * 
	 * @return PaygentB2BModuleResources　失敗の場合、エラーコード
	 */
	static function &getInstance() {
		static $resourceInstance = null;
		
		if (isset($resourceInstance) == false 
			|| $resourceInstance == null
			|| is_object($resourceInstance) != true) {
			
			$resourceInstance = new PaygentB2BModuleResources();
			$rslt = $resourceInstance->readProperties();
			if ($rslt === true) {
			} else {
				$resourceInstance = $rslt;
			} 
		}

		return $resourceInstance;
	}

	/**
	 * クライアント証明書ファイルパスを取得。
	 * 
	 * @return clientFilePath
	 */
	function getClientFilePath() {
		return $this->clientFilePath;
	}

	/**
	 * CA証明書ファイルパスを取得。
	 * 
	 * @return caFilePath
	 */
	function getCaFilePath() {
		return $this->caFilePath;
	}

	/**
	 * Proxyサーバ名を取得。
	 * 
	 * @return proxyServerName
	 */
	function getProxyServerName() {
		return $this->proxyServerName;
	}

	/**
	 * ProxyIPアドレスを取得。
	 * 
	 * @return proxyServerIp
	 */
	function getProxyServerIp() {
		return $this->proxyServerIp;
	}

	/**
	 * Proxyポート番号を取得。
	 * 
	 * @return proxyServerPort
	 */
	function getProxyServerPort() {
		return $this->proxyServerPort;
	}

	/**
	 * デフォルトIDを取得。
	 * 
	 * @return defaultId
	 */
	function getDefaultId() {
		return $this->defaultId;
	}

	/**
	 * デフォルトパスワードを取得。
	 * 
	 * @return defaultPassword
	 */
	function getDefaultPassword() {
		return $this->defaultPassword;
	}

	/**
	 * タイムアウト値を取得。
	 * 
	 * @return timeout
	 */
	function getTimeout() {
		return $this->timeout;
	}

	/**
	 * ログ出力先を取得。
	 * 
	 * @return logOutputPath
	 */
	function getLogOutputPath() {
		return $this->logOutputPath;
	}

	/**
	 * 照会MAX件数を取得。
	 * 
	 * @return selectMaxCnt
	 */
	function getSelectMaxCnt() {
		return $this->selectMaxCnt;
	}

	/**
	 * 接続先URLを取得。
	 * 
	 * @param telegramKind
	 * @return FALSE: 失敗(PaygentB2BModuleConnectException::TEREGRAM_PARAM_OUTSIDE_ERROR)、成功:取得した URL
	 */
	function getUrl($telegramKind) {
		$rs = null;
		$sKey = null;

		// プロパティチェック
		if ($this->propConnect == null) {
			trigger_error(PaygentB2BModuleConnectException__TEREGRAM_PARAM_OUTSIDE_ERROR 
				. ": HTTP request contains unexpected value.", E_USER_WARNING);
			return false;
		}
		
		// 引数チェック
		if (StringUtil::isEmpty($telegramKind)) {
			trigger_error(PaygentB2BModuleConnectException__TEREGRAM_PARAM_OUTSIDE_ERROR 
				. ": HTTP request contains unexpected value.", E_USER_WARNING);
			return false;
		}

		// 全桁数でプロパティからURLを取得
		$sKey = PaygentB2BModuleResources__URL_COMM . $telegramKind;
		if (array_key_exists($sKey, $this->propConnect)) {
			$rs = $this->propConnect[$sKey];
		}
		
		// 全桁数で取得できた場合、その値を戻す
		if (!StringUtil::isEmpty($rs)) {
			return $rs;
		}
		
		// 先頭２桁でプロパティからURLを取得
		if (strlen($telegramKind) > PaygentB2BModuleResources__TELEGRAM_KIND_FIRST_CHARS) {
			$sKey = PaygentB2BModuleResources__URL_COMM 
				. substr($telegramKind, 0, PaygentB2BModuleResources__TELEGRAM_KIND_FIRST_CHARS);
		} else {
			// 全桁数となり、エラーとする
			trigger_error(PaygentB2BModuleConnectException__TEREGRAM_PARAM_OUTSIDE_ERROR 
				. ": HTTP request contains unexpected value.", E_USER_WARNING);
			return false;
		}
		if (array_key_exists($sKey, $this->propConnect)) {
			$rs = $this->propConnect[$sKey];
		}
		
		// 全桁数と先頭２桁で取得できなかった場合、エラーを戻す
		if (StringUtil::isEmpty($rs)) {
			trigger_error(PaygentB2BModuleConnectException__TEREGRAM_PARAM_OUTSIDE_ERROR 
				. ": HTTP request contains unexpected value.", E_USER_WARNING);
			return false;
		}
		
		return $rs;
	}

	/**
	 * デバッグオプションを取得。
	 * 
	 * @return debugFlg
	 */
	function getDebugFlg() {
		return $this->debugFlg;
	}

	/**
	 * PropertiesFile の値を取得し、設定。
	 *
	 * @return mixed 成功：TRUE、他：エラーコード 
	 */
	function readProperties() {

		// Properties File Read
		$prop = null;
		
//		$prop = PaygentB2BModuleResources::parseJavaProperty(PaygentB2BModuleResources__PROPERTIES_FILE_NAME);
		$prop = array(
			PaygentB2BModuleResources__LOG_OUTPUT_PATH => WP_CONTENT_DIR.'/uploads/wc-paygent/connectmodule.log',
			PaygentB2BModuleResources__CLIENT_FILE_PATH =>CLIENT_FILE_PATH,
			PaygentB2BModuleResources__CA_FILE_PATH => CA_FILE_PATH,
			PaygentB2BModuleResources__DEBUG_FLG => PAYGENT_DEBUG_FLG,
			PaygentB2BModuleResources__TIMEOUT_VALUE => PAYGENT_TIMEOUT_VALUE,
			PaygentB2BModuleResources__TELEGRAM_KIND_REFS =>PAYGENT_TELEGRAM_KIND_REF,
			PaygentB2BModuleResources__SELECT_MAX_CNT =>PAYGENT_SELCET_MAX_CNT,
			'paygentB2Bmodule.url.01'=>URL01,
			'paygentB2Bmodule.url.02'=>URL02,
			'paygentB2Bmodule.url.11'=>URL11,
			'paygentB2Bmodule.url.18'=>URL18,
			'paygentB2Bmodule.url.19'=>URL19,
			'paygentB2Bmodule.url.03'=>URL03,
			'paygentB2Bmodule.url.04'=>URL04,
			'paygentB2Bmodule.url.05'=>URL05,
			'paygentB2Bmodule.url.06'=>URL06,
			'paygentB2Bmodule.url.07'=>URL07,
			'paygentB2Bmodule.url.09'=>URL09,
			'paygentB2Bmodule.url.091'=>URL091,
			'paygentB2Bmodule.url.093'=>URL093,
			'paygentB2Bmodule.url.094'=>URL094,
			'paygentB2Bmodule.url.10'=>URL10,
			'paygentB2Bmodule.url.12'=>URL12,
			'paygentB2Bmodule.url.20'=>URL20,
			'paygentB2Bmodule.url.15'=>URL15,
			'paygentB2Bmodule.url.13'=>URL13,
		);// add 20141009 by Shohei Tanaka


		if ($prop === false) {
			// Properties File 読込エラー
			trigger_error(PaygentB2BModuleException__RESOURCE_FILE_NOT_FOUND_ERROR
				. ": Properties file doesn't exist.", E_USER_WARNING);
			return PaygentB2BModuleException__RESOURCE_FILE_NOT_FOUND_ERROR; 
		}

		// 必須項目エラーチェック
		if (!($this->isPropertiesIndispensableItem($prop) 
			&& $this->isPropertiesSetData($prop) 
			&& $this->isPropertieSetInt($prop))
			|| $this->isURLNull($prop)) {
			// 必須項目エラー
			$propConnect = null;
			trigger_error(PaygentB2BModuleException__RESOURCE_FILE_REQUIRED_ERROR
				. ": Properties file contains inappropriate value.", E_USER_WARNING);
			return PaygentB2BModuleException__RESOURCE_FILE_REQUIRED_ERROR; 
		}
		$this->propConnect = $prop;
		
		// クライアント証明書ファイルパス
		if (array_key_exists(PaygentB2BModuleResources__CLIENT_FILE_PATH, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__CLIENT_FILE_PATH]))) {
			$this->clientFilePath = $prop[PaygentB2BModuleResources__CLIENT_FILE_PATH];
		}

		// CA証明書ファイルパス
		if (array_key_exists(PaygentB2BModuleResources__CA_FILE_PATH, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__CA_FILE_PATH]))) {
			$this->caFilePath = $prop[PaygentB2BModuleResources__CA_FILE_PATH];
		}

		// Proxyサーバ名
		if (array_key_exists(PaygentB2BModuleResources__PROXY_SERVER_NAME, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__PROXY_SERVER_NAME]))) {
			$this->proxyServerName = $prop[PaygentB2BModuleResources__PROXY_SERVER_NAME];
		}

		// ProxyIPアドレス
		if (array_key_exists(PaygentB2BModuleResources__PROXY_SERVER_IP, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__PROXY_SERVER_IP]))) {
			$this->proxyServerIp = $prop[PaygentB2BModuleResources__PROXY_SERVER_IP];
		}

		// Proxyポート番号
		if (array_key_exists(PaygentB2BModuleResources__PROXY_SERVER_PORT, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__PROXY_SERVER_PORT]))) {
			if (StringUtil::isNumeric($prop[PaygentB2BModuleResources__PROXY_SERVER_PORT])) {
				$this->proxyServerPort = $prop[PaygentB2BModuleResources__PROXY_SERVER_PORT];
			} else {
				// 設定値エラー
				trigger_error(PaygentB2BModuleException__RESOURCE_FILE_REQUIRED_ERROR
					. ": Properties file contains inappropriate value.", E_USER_WARNING);
				return PaygentB2BModuleException__RESOURCE_FILE_REQUIRED_ERROR; 
			}
		}

		// デフォルトID
		if (array_key_exists(PaygentB2BModuleResources__DEFAULT_ID, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__DEFAULT_ID]))) {
			$this->defaultId = $prop[PaygentB2BModuleResources__DEFAULT_ID];
		}

		// デフォルトパスワード
		if (array_key_exists(PaygentB2BModuleResources__DEFAULT_PASSWORD, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__DEFAULT_PASSWORD]))) {
			$this->defaultPassword = $prop[PaygentB2BModuleResources__DEFAULT_PASSWORD];
		}

		// タイムアウト値
		if (array_key_exists(PaygentB2BModuleResources__TIMEOUT_VALUE, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__TIMEOUT_VALUE]))) {
			$this->timeout = $prop[PaygentB2BModuleResources__TIMEOUT_VALUE];
		}

		// ログ出力先
		if (array_key_exists(PaygentB2BModuleResources__LOG_OUTPUT_PATH, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__LOG_OUTPUT_PATH]))) {
			$this->logOutputPath = $prop[PaygentB2BModuleResources__LOG_OUTPUT_PATH];
		}

		// 照会MAX件数
		if (array_key_exists(PaygentB2BModuleResources__SELECT_MAX_CNT, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__SELECT_MAX_CNT]))) {
			$this->selectMaxCnt =$prop[PaygentB2BModuleResources__SELECT_MAX_CNT];
		}

		// 照会電文種別リスト
		if (array_key_exists(PaygentB2BModuleResources__TELEGRAM_KIND_REFS, $prop)
				&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__TELEGRAM_KIND_REFS]))) {
			$telegramKindRef = $prop[PaygentB2BModuleResources__TELEGRAM_KIND_REFS];
			$this->telegramKindRefs = $this->split($telegramKindRef, PaygentB2BModuleResources__TELEGRAM_KIND_SEPARATOR);
		}
		if ($this->telegramKindRefs == null) {
			$this->telegramKindRefs = array();
		}
				
		// デバッグオプション
		if (array_key_exists(PaygentB2BModuleResources__DEBUG_FLG, $prop)
			&& !(StringUtil::isEmpty($prop[PaygentB2BModuleResources__DEBUG_FLG]))) {
			$this->debugFlg = PAYGENT_DEBUG_FLG;
		}
		
		return true;
	}

	/**
	 * Properties 必須項目チェック
	 * 
	 * @param Properties
	 * @return boolean true=必須項目有り false=必須項目無し
	 */
	function isPropertiesIndispensableItem($prop) {
		$rb = false;

		if ((array_key_exists(PaygentB2BModuleResources__CLIENT_FILE_PATH, $prop)
				&& array_key_exists(PaygentB2BModuleResources__CA_FILE_PATH, $prop)
				&& array_key_exists(PaygentB2BModuleResources__TIMEOUT_VALUE, $prop)
				&& array_key_exists(PaygentB2BModuleResources__LOG_OUTPUT_PATH, $prop)
				&& array_key_exists(PaygentB2BModuleResources__SELECT_MAX_CNT, $prop)
				)) {
			// 必須項目有り
			$rb = true;
		}

		return $rb;
	}

	/**
	 * Properties データ設定チェック
	 * 
	 * @param prop Properties
	 * @return boolean true=データ未設定項目無し false=データ未設定項目有り
	 */
	function isPropertiesSetData($prop) {
		$rb = true;

		if (StringUtil::isEmpty($prop[PaygentB2BModuleResources__CLIENT_FILE_PATH])
				|| StringUtil::isEmpty($prop[PaygentB2BModuleResources__CA_FILE_PATH])
				|| StringUtil::isEmpty($prop[PaygentB2BModuleResources__TIMEOUT_VALUE])
				|| StringUtil::isEmpty($prop[PaygentB2BModuleResources__SELECT_MAX_CNT])) {
			// 必須項目未設定エラー
			$rb = false;
		}

		return $rb;
	}

	/**
	 * Properties 数値チェック
	 * 
	 * @param prop Properties
	 * @return boolean true=数値設定 false=数値未設定
	 */
	function isPropertieSetInt($prop) {
		$rb = false;

		if (StringUtil::isNumeric($prop[PaygentB2BModuleResources__TIMEOUT_VALUE])
				&& StringUtil::isNumeric($prop[PaygentB2BModuleResources__SELECT_MAX_CNT])) {
			// 数値設定
			$rb = true;
		}

		return $rb;
	}
	
	/**
	 * 接続先URLはヌルかどうかのチェック
	 * 
	 */
	function isURLNull($prop) {
		$rb = false;
		if (!is_array($prop)) {
			return true;
		}
		
		foreach($prop as $key => $value) {
			
			if (strpos($key, PaygentB2BModuleResources__URL_COMM) === 0) {
				if (isset($value) == false 
					|| strlen(trim($value)) == 0) {
					$rb = true;
					break;
				}
			}
		}
		return $rb;
	}
	
	/**
	 * 指定された区切り文字で文字列を分割し、トリムする
	 * 
	 * @param str 文字列
	 * @param separator 区切り文字
	 * @return リスト
	 */
	function split($str, $separator) {
		$list = array();
		
		if ($str == null) {
			return $list;
		}
		
		if ($separator == null || strlen($separator) == 0) {
			if (!StringUtil::isEmpty(trim($str))) {
				$list[] = trim($str);
			}
			return $list;
		}
		
		$arr = explode($separator, $str);
		for ($i=0; $arr && $i < sizeof($arr); $i++) {
			if (!StringUtil::isEmpty(trim($arr[$i]))) {
				$list[] = trim($arr[$i]);
			}
		}
		
		return $list;
	}
	
	/**
	 * 照会電文チェック
	 * @param telegramKind 電文種別
	 * @return true=照会電文 false=照会電文以外
	 */
	function isTelegramKindRef($telegramKind) {
		$bRet = false;
		
		if ($this->telegramKindRefs == null) {
			return $bRet;
		}
		$bRet = in_array($telegramKind, $this->telegramKindRefs);
		return $bRet;
	}
 	
 	/**
 	 * Javaフォーマットのプロパティファイルから値を取得して
 	 * 配列に入れて返す
 	 * 
 	 * @param fileName プロパティファイル名
 	 * @param commentChar コメント用文字
 	 * @return FALSE: 失敗、他:KEY=VALUE形式の配列,
 	 */
 	function parseJavaProperty($fileName, $commentChar = "#") {

		$properties = array();
		
		$lines = @file($fileName, FILE_USE_INCLUDE_PATH | FILE_IGNORE_NEW_LINES);
 		if ($lines === false) {
			// Properties File 読込エラー
			return $lines;
 		}
 		
 		foreach ($lines as $i => $line) {
 			$lineData = trim($line);
 			
 			$index = strpos($lineData, '\r');
 			if (!($index === false)) {
 				$lineData = trim(substr($lineData, 0, $index));
 			}
 			$index = strpos($lineData, '\n');
 			if (!($index === false)) {
 				$lineData = trim(substr($lineData, 0, $index));
 			}

 			if (strlen($lineData) <= 0) {
 				continue;
 			}
 			$firstChar = substr($lineData, 0, strlen($commentChar));
 			
 			if ($firstChar == $commentChar) {
 				continue;
 			}
 			
			$quotationIndex = strpos($lineData, '=');
			if ($quotationIndex <= 0) {
				continue;
			}
			
			$key = trim(substr($lineData, 0, $quotationIndex));
			$value = null;
			if (strlen($lineData) > $quotationIndex) {
				$value = trim(substr($lineData, $quotationIndex + 1));
			}
			$properties[$key] = $value;
 		}
 		
 		return $properties;
 	}
	
 }
?>

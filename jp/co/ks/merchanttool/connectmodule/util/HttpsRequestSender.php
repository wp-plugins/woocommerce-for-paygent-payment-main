<?php
/**
 * PAYGENT B2B MODULE
 * HttpsRequestSender.php
 *
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/StringUtil.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleConnectException.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleException.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/PaygentB2BModuleLogger.php");

/**
 * https要求をおこなうユーティリティクラス。
 *
 * @vesrion $Revision: 29962 $
 * @author $Author: ito $
 */

	// cURL エラーコード
	// http://curl.haxx.se/libcurl/c/libcurl-errors.html
	define("HttpsRequestSender__CURLE_COULDNT_CONNECT", 7);
	define("HttpsRequestSender__CURLE_SSL_CERTPROBLEM", 58);
	define("HttpsRequestSender__CURLE_SSL_CACERT", 60);
	define("HttpsRequestSender__CURLE_SSL_CACERT_BADFILE", 77);
	define("HttpsRequestSender__CURLE_HTTP_RETURNED_ERROR", 22);

	/**
	 * HTTP POST 通信用固定値
	 */
	define("HttpsRequestSender__POST", "POST");

	/**
	 * HTTPプロトコルを表す定数
	 */
	define("HttpsRequestSender__HTTP", "HTTP");

	/**
	 * HTTP/1.0を表す定数
	 */
	define("HttpsRequestSender__HTTP_1_0", "HTTP/1.0");

	/**
	 * HTTP通信の成功コード
	 */
	define("HttpsRequestSender__HTTP_1_0_200", "HTTP/1.0 200");

	/**
	 * HTTP通信の成功コード：200
	 */
	define("HttpsRequestSender__HTTP_SUCCESS", 200);

	/**
	 * HTTP通信の成功コード：206
	 */
	define("HttpsRequestSender__HTTP_PARTIAL_CONTENT", 206);

	/**
	 * 電文長
	 */
	define("HttpsRequestSender__TELEGRAM_LENGTH", 10240);

	/**
	 * HTTPS Default Port
	 */
	define("HttpsRequestSender__DEFAULT_PORT", 443);

	/**
	 * リクエスト・レスポンスの改行コード
	 */
	define("HttpsRequestSender__CRLF", "\r\n");

	/**
	 * デフォルトのエンコーディング
	 */
	define("HttpsRequestSender__DEFAULT_ENCODING", "SJIS-win");

	/**
	 * HTTPステータスコード変数の初期値
	 */
	define("HttpsRequestSender__HTTP_STATUS_INIT_VALUE", -1);

	/**
	 * ステータスコードの長さ
	 */
	define("HttpsRequestSender__REGEXPSTATUS_LEN", 3);

	/**
	 * Content-Length
	 */
	define("HttpsRequestSender__CONTENT_LENGTH", "Content-Length");

	/**
	 * User-Agent
	 */
	define("HttpsRequestSender__USER_AGENT", "User-Agent");

	/**
	 * Content-Type
	 */
	define("HttpsRequestSender__CONTENT_TYPE", "Content-Type=application/x-www-form-urlencoded");
	define("HttpsRequestSender__HTTP_ENCODING", "charset=Windows-31J");

	/**
	 * マスク文字列
	 */
	define("HttpsRequestSender__MASK_STRING", "XXXX");

class HttpsRequestSender {
	/**
	 * KeyStore Password
	 */
	var $KEYSTORE_PASSWORD = "changeit";

	/** レスポンスヘッダ */
	var $responseHeader;

	/** レスポンスボディ */
	var $responseBody;

	/** ステータスコード　*/
	var $statusCode;

	/** 接続先 URL */
	var $url;

	/** クライアント証明書パス */
	var $clientCertificatePath;

	/** 認証局証明書パス */
	var $caCertificatePath;

	/** SSL通信用ソケット */
	var $ch;

	/** トンネルソケット */
	//var $tunnelSocket;

	/** タイムアウト値 int */
	var $timeout;

	/** Proxyホスト名 */
	var $proxyHostName;

	/** Proxyポート番号 int */
	var $proxyPort;

	/** Proxy接続タイムアウト値 */
	var $proxyConnectTimeout;

	/** Proxy伝送タイムアウト値 */
	var $proxyCommunicateTimeout;

	/** Proxy使用判定 */
	var $isUsingProxy = false;

	/** デバッグログマスク対象項目 */
	var $MASK_COLUMNS = array("card_conf_number");

	/** 処理結果メッセージ */
	var $resultMessage = '';

	/**
	 * コンストラクタ<br>
	 * 接続先URLを設定
	 *
	 * @param url String
	 */
	function HttpsRequestSender($url) {
		$this->url = $url;
		$this->proxyHostName = "";
		$this->proxyPort = 0;

		$this->responseBody = null;
		$this->responseHeader = null;
	}

	/**
	 * クライアント証明書パスを設定
	 *
	 * @param fileName String
	 */
	function setClientCertificatePath($fileName) {
		$this->clientCertificatePath = $fileName;
	}

	/**
	 * 認証局証明書パスを設定
	 *
	 * @param fileName String
	 */
	function setCaCertificatePath($fileName) {
		$this->caCertificatePath = $fileName;
	}

	/**
	 * タイムアウトを設定
	 *
	 * @param timeout int
	 */
	function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	/**
	 * Proxy接続タイムアウトを設定
	 *
	 * @param proxyConnectTimeout int
	 */
	function setProxyConnectTimeout($proxyConnectTimeout) {
		$this->proxyConnectTimeout = $proxyConnectTimeout;
	}

	/**
	 * Proxy伝送タイムアウトを設定
	 *
	 * @param proxyCommunicateTimeout int
	 */
	function setProxyCommunicateTimeout($proxyCommunicateTimeout) {
		$this->proxyCommunicateTimeout = $proxyCommunicateTimeout;
	}

	/**
	 * ProxyHostName, ProxyPort を設定
	 *
	 * @param proxyHostName String
	 * @param proxyPort int
	 */
	function setProxyInfo($proxyHostName, $proxyPort) {
		$this->proxyHostName = $proxyHostName;
		$this->proxyPort = $proxyPort;
		$this->isUsingProxy = false;

		if (!StringUtil::isEmpty($this->proxyHostName) && 0 < $this->proxyPort) {
			// Proxy情報が設定された為、true を設定
			$this->isUsingProxy = true;
		}
	}

	/**
	 * 処理結果メッセージ
	 *
	 * @return resultMessage String
	 */
	function getResultMessage() {
		return $this->resultMessage;
	}

	/**
	 * Postを実施
	 *
	 * @param formData Map
	 * @param debugFlg
	 * @return mixed TRUE:成功、他:エラーコード
	 */
	function postRequestBody($formData, $debugFlg) {

		// 通信開始
		$this->initCurl();

		if ($this->isUsingProxy) {
			// プロキシ経由で通信先に接続
			$this->setProxy();
		}

		// リクエストを送信
		$retCode = $this->send($formData, $debugFlg);

		// レスポンスを受信
		$this->closeCurl();

		return $retCode;
	}

	/**
	 * 受信データを返す
	 *
	 * @return InputStream
	 */
	function getResponseBody() {
		return $this->responseBody;
	}

	/**
	 * 電文長取得
	 *
	 * @return telegramLength(byte)
	 */
	function getTelegramLength($formData) {
		if ($formData == null) {
			return 0;
		}

		$sb = $this->url;
		$sb .= "?";

		foreach($formData as $key => $value) {
			$sb .= $key;
			$sb .= "=";
			$sb .= $value;
			$sb .= "&";
		}

		$rs = "";

		if (0 < strlen($sb)) {
			$rs = substr($sb, 0, strlen($sb) - 1);
		}

		return strlen($rs);
	}

	/**
	 * 要求電文を作成
	 *
	 * @param formData Map 要求電文
	 * @param debugLogFlg デバッグログフラグ
	 * @return String 作成した要求電文（URL）
	 */
	function convertToUrlEncodedString($formData, $debugLogFlg) {
		$encodedString = "";
		if ($formData == null) {
			return "";
		}

		foreach($formData as $key => $value) {
//			$this->outputDebugLog("param: " . $key . " = \"" . $value . "\"");

			if ($debugLogFlg and in_array($key, $this->MASK_COLUMNS) and !StringUtil::isEmpty($value)) {
				// デバッグログ出力時、マスク対象項目の場合はマスク文字列を出力する
				$value = HttpsRequestSender__MASK_STRING;
			}

			$tmp = $key;
			$encodedString .= urlencode($tmp);
			$encodedString .= "=";
			$tmp = $value;
			$encodedString .= urlencode($tmp);
			$encodedString .= "&";
		}

		$rs = "";

		if (0 < strlen($encodedString)) {
			$rs = substr($encodedString, 0, strlen($encodedString) - 1);
		}

		return $rs;

	}

	/**
	 * デバッグログ出力メソッド
	 * ログ出力クラスのインスタンス生成に失敗したら標準出力にエラーメッセージを
	 * 出力する。
	 *
	 * @param msg String 出力メッセージ
	 */
	function outputDebugLog($msg) {
		if(StringUtil::isEmpty($msg)) return;

		$inst = PaygentB2BModuleLogger::getInstance();
		if (is_object($inst)) {
			$inst->debug(get_class($this), $msg);
		}
	}

	/**
	 * Proxy接続用
	 *
	 */
	function setProxy() {
		curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, true);
		curl_setopt($this->ch, CURLOPT_PROXY, "http://" . $this->proxyHostName . ":" . $this->proxyPort);

	}

	/**
	 * 接続のための初期化処理
	 *
	 */
	function initCurl() {
		$rslt = true;
		// 初期化
		$this->ch = curl_init($this->url);

		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_POST, true);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_HEADER, true);

		// 証明書
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_SSLCERT, $this->clientCertificatePath);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_SSLKEYPASSWD, $this->KEYSTORE_PASSWORD);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_CAINFO, $this->caCertificatePath);

		// タイムアウト
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		$rslt = $rslt && curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->proxyConnectTimeout);

		return $rslt;
	}

	/**
	 * リクエスト生成と送信
	 *
	 * @param formData Map 要求電文
	 * @param debugFlg デバッグフラグ
	 * @return mixed TRUE:成功、他:エラーコード
	 */
	function send($formData, $debugFlg) {
		// リクエストを Map から String に変換

		$query = $this->convertToUrlEncodedString($formData, false);

		$header = array();
		$header[] = HttpsRequestSender__CONTENT_TYPE;
		$header[] = HttpsRequestSender__HTTP_ENCODING;
		$header[] = HttpsRequestSender__CONTENT_LENGTH . ": "
			. (StringUtil::isEmpty($query)? "0" : strlen($query));
		$header[] = HttpsRequestSender__USER_AGENT . ": " . "curl_php";
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $query);

		// リクエスト内容 ログ出力
		if ($debugFlg) {
			$debugQuery = $this->convertToUrlEncodedString($formData, true);
			$this->outputDebugLog("request: " . $debugQuery);
		}

		$str = curl_exec($this->ch);

		if ($str === false && curl_errno($this->ch) != 0) {
			return $this->procError();
		}

		$data = $str;
		$retCode = $this->parseResponse($data);

		// レスポンス内容 ログ出力
		if ($debugFlg) {
			$this->outputDebugLog("response: \r\n" . $this->responseBody);
		}

		return $retCode;
	}

	/**
	 * Curlのエラー処理
	 * @return mixed True:問題なし、他：エラーコード
	 */
	function procError() {
		$errorNo = curl_errno($this->ch);
		$errorMsg = $errorNo . ": " . curl_error($this->ch);
		$retCode = true;

		if ($errorNo <= HttpsRequestSender__CURLE_COULDNT_CONNECT) { // 7
			// 接続問題
			$retCode = PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
			$this->outputDebugLog($errorMsg);
		} else if ($errorNo == HttpsRequestSender__CURLE_COULDNT_CONNECT) { // 7
			// 接続問題
			$retCode = PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
			$this->outputDebugLog($errorMsg);
		} else if ($errorNo == HttpsRequestSender__CURLE_SSL_CERTPROBLEM) {
			// 認証問題
			$retCode = PaygentB2BModuleConnectException__CERTIFICATE_ERROR;
			$this->outputDebugLog($errorMsg);
		} else if ($errorNo == HttpsRequestSender__CURLE_SSL_CACERT) {
			// 認証問題
			$retCode = PaygentB2BModuleConnectException__CERTIFICATE_ERROR;
			$this->outputDebugLog($errorMsg);
		} else if ($errorNo == HttpsRequestSender__CURLE_SSL_CACERT_BADFILE) {	// CURLE_SSL_CACERT_BADFILE
			// 認証問題
			$retCode = PaygentB2BModuleConnectException__CERTIFICATE_ERROR;
			$this->outputDebugLog($errorMsg);
		} else if ($errorNo == HttpsRequestSender__CURLE_HTTP_RETURNED_ERROR) {
			// HTTP Return code error
			$retCode = PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
			$this->outputDebugLog($errorMsg);
		} else {
			// その他のエラー
			$retCode = PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
			$this->outputDebugLog($errorMsg);
		}
		$this->resultMessage = "$retCode: $errorMsg";

		// 証明書ファイルの状態チェック
		foreach (array($this->clientCertificatePath, $this->caCertificatePath) as $path) {
			if (!file_exists($path)) {
				$this->resultMessage .= "(file is not exists: $path)";
			} elseif (!is_readable($path)) {
				$this->resultMessage .= "(file is not readable: $path)";
			}
		}

		trigger_error("$retCode: Http request ended with errors.", E_USER_WARNING);
		return $retCode;
	}

	/**
	 * レスポンスを受信。
	 *
	 * @param $data レスポンス文字列
	 * @return mixed TRUE:成功、他:エラーコード
	 */
	function parseResponse($data) {

		// レスポンス受信
		$line = null;
		$retCode = HttpsRequestSender__HTTP_STATUS_INIT_VALUE;
		$bHeaderOver = false;
		$resBodyStart = 0;

		$lines = mb_split(HttpsRequestSender__CRLF, $data);
		// ヘッダまでを読み込む
		foreach($lines as $i => $line) {

			if (StringUtil::isEmpty($line)) {
				 break;
			}
			$resBodyStart += strlen($line) + strlen(HttpsRequestSender__CRLF);

			if ($retCode === HttpsRequestSender__HTTP_STATUS_INIT_VALUE) {
				// ステータスの解析
				$retCode = $this->parseStatusLine($line);
				if ($retCode === true) {
					continue;
				}
				$this->outputDebugLog("Cannot get http return code.");
				return $retCode;
			}

			// ヘッダの解析
			if (!$this->parseResponseHeader($line)) {
				continue;
			}
		}
		$info = curl_getinfo($this->ch);
		// linuxサーバでheader_sizeに誤った値が設定される事象が見られたためsize_downloadでキャプチャしている
		$resBodyStart = -($info['size_download']);
		$this->responseBody = substr($data, $resBodyStart);

		return true;
	}

	/**
	 * ステータスラインを解析
	 * (HTTP-Version SP Status-Code SP Reason-Phrase CRLF)
	 *
	 * @param line String ステータスライン
	 * @return mixed TRUE:成功、他:エラーコード
	 */
	function parseStatusLine($line) {

		if (StringUtil::isEmpty($line)) {

			// 不正なステータスコードを受け取った
			return PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
		}

		$statusLine = StringUtil::split($line, " ", 3);

		if (StringUtil::isNumeric($statusLine[1])) {
			$this->statusCode = intVal($statusLine[1]);
		} else {

			// 不正なステータスコードを受け取った
			return PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
		}

		if (strpos($statusLine[0], HttpsRequestSender__HTTP . "/") != 0
				|| !StringUtil::isNumericLength($statusLine[1], HttpsRequestSender__REGEXPSTATUS_LEN)) {

			// 不正なステータスコードを受け取った
			return PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
		}

		if (!((HttpsRequestSender__HTTP_SUCCESS <= $this->statusCode)
			&& ($this->statusCode <= HttpsRequestSender__HTTP_PARTIAL_CONTENT))) {

			// HTTP Status が Success Code (200 - 206) でない場合
			return PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
		}

		return true;
	}

	/**
	 * レスポンスヘッダを一行解析して、内部に格納。<br>
	 * レスポンスヘッダの値が存在しない場合は、nullを設定。
	 *
	 * @param line String サーバから受け取ったレスポンス行
	 * @return boolean true=ヘッダ解析・格納完了, false=ヘッダではない（ヘッダ部終了）
	 */
	function parseResponseHeader($line) {
		if (StringUtil::isEmpty($line)) {
			// HEADER終了
			return false;
		}

		// HEADER
		$headerStr = StringUtil::split($line, ":", 2);
		if ($this->responseHeader == null) {
			$this->responseHeader = array();
		}

		if (count($headerStr) == 1 || strlen(trim($headerStr[1])) == 0) {
			// 値が存在しない or 値が空文字列
			$this->responseHeader[$headerStr[0]] = null;
		} else {
			$this->responseHeader[$headerStr[0]] = trim($headerStr[1]);
		}

		return true;
	}

	/**
	 * Close curl
	 *
	 */
	function closeCurl() {
		// プロキシソケットCLOSE
		if ($this->ch != null) {
			curl_close($this->ch);
			$this->ch = null;
		}
	}

}

?>

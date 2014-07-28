<?php
/**
 * PAYGENT B2B MODULE
 * FilePaymentResponseDataImpl.php
 *
 * Copyright (C) 2010 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleConnectException.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleException.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/CSVWriter.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/CSVTokenizer.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/HttpsRequestSender.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/util/StringUtil.php");
include_once(WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/entity/ResponseData.php");

/**
 * ファイル決済系応答電文処理クラス
 *
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */

	/**
	 * 行番号（ヘッダー部）= "1"
	 */
	define("FilePaymentResponseDataImpl__LINENO_HEADER", "1");

	/**
	 * レコード区分 位置", 0
	 */
	define("FilePaymentResponseDataImpl__LINE_RECORD_DIVISION", 0);

	/**
	 * ヘッダー部 処理結果 位置 6
	 */
	define("FilePaymentResponseDataImpl__LINE_HEADER_RESULT", 6);

	/**
	 * ヘッダー部 レスポンスコード 位置", 7
	 */
	define("FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_CODE", 7);

	/**
	 * ヘッダー部 レスポンス詳細 位置", 8
	 */
	define("FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_DETAIL", 8);

	/**
	 * 改行文字
	 */
	define("FilePaymentResponseDataImpl__LINE_SEPARATOR", "\r\n");

class FilePaymentResponseDataImpl extends ResponseData {

	/** 処理結果 */
	var $resultStatus;

	/** レスポンスコード */
	var $responseCode;

	/** レスポンス詳細 */
	var $responseDetail;

	/**
     * ファイル決済の場合は値を含むパースは不可。
     * 常にExceptionをthrowする。
	 *
	 * @param data
	 */
	function parse($body) {
		trigger_error(PaygentB2BModuleException__FILE_PAYMENT_ERROR
				. ": parse is not supported.", E_USER_WARNING);
		return PaygentB2BModuleException__FILE_PAYMENT_ERROR;
	}

	/**
     * data を分解 リザルト情報のみ、変数に設定。
	 *
	 * @param body
	 * @return mixed TRUE:成功、他：エラーコード
	 */
	function parseResultOnly($body) {

		$csvTknzr = new CSVTokenizer(CSVTokenizer__DEF_SEPARATOR,
			CSVTokenizer__NO_ITEM_ENVELOPE);
		$line = "";

		// リザルト情報の初期化
		$this->resultStatus = "";
		$this->responseCode = "";
		$this->responseDetail = "";

		$lines = explode(FilePaymentResponseDataImpl__LINE_SEPARATOR, $body);
		foreach($lines as $i => $line) {
			$lineItem = $csvTknzr->parseCSVData($line);

			if (0 < count($lineItem)) {
				if ($lineItem[FilePaymentResponseDataImpl__LINE_RECORD_DIVISION]
						== FilePaymentResponseDataImpl__LINENO_HEADER) {
					// ヘッダー部の行の場合
					if (FilePaymentResponseDataImpl__LINE_HEADER_RESULT < count($lineItem)) {
						// 処理結果を設定
						$this->resultStatus = $lineItem[FilePaymentResponseDataImpl__LINE_HEADER_RESULT];
					}
					if (FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_CODE < count($lineItem)) {
						// レスポンスコードを設定
						$this->responseCode = $lineItem[FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_CODE];
					}
					if (FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_DETAIL < count($lineItem)) {
						// レスポンス詳細を設定
						$this->responseDetail = $lineItem[FilePaymentResponseDataImpl__LINE_HEADER_RESPONSE_DETAIL];
					}

					// ヘッダーのみの解析で終了
					break;
				}
			}
		}

		if (StringUtil::isEmpty($this->resultStatus)) {
			// 処理結果が 空文字 もしくは null の場合
			trigger_error(PaygentB2BModuleConnectException__KS_CONNECT_ERROR
				. ": resultStatus is Nothing.", E_USER_WARNING);
			return PaygentB2BModuleConnectException__KS_CONNECT_ERROR;
		}

		return true;

	}

	/**
     * 次のデータを取得。
	 *
	 * @return Map
	 */
	function resNext() {
		return null;
	}

	/**
     * 次のデータが存在するか判定。
     *
	 * @return boolean true=存在する false=存在しない
	 */
	function hasResNext() {
		return false;
	}

	/**
	 * resultStatus を取得
	 *
	 * @return String
	 */
	function getResultStatus() {
		return $this->resultStatus;
	}

	/**
	 * responseCode を取得
	 *
	 * @return String
	 */
	function getResponseCode() {
		return $this->responseCode;
	}

	/**
	 * responseDetail を取得
	 *
	 * @return String
	 */
	function getResponseDetail() {
		return $this->responseDetail;
	}

	/**
	 * CSV を作成
	 *
	 * @param resBody
	 * @param resultCsv String
	 * @return boolean true：成功、他：エラーコード
	 */
	function writeCSV($body, $resultCsv) {
		$rb = false;

		// CSV を 1行ずつ出力
		$csvWriter = new CSVWriter($resultCsv);
		if ($csvWriter->open() === false) {
			// ファイルオープンエラー
			trigger_error(PaygentB2BModuleException__CSV_OUTPUT_ERROR
				. ": Failed to open CSV file.", E_USER_WARNING);
			return PaygentB2BModuleException__CSV_OUTPUT_ERROR;
		}

		$lines = explode(FilePaymentResponseDataImpl__LINE_SEPARATOR, $body);

		foreach($lines as $i => $line) {
			if(StringUtil::isEmpty($line)) {
				continue;
			}
			if (!$csvWriter->writeOneLine($line)) {
				// 書き込めなかった場合
				trigger_error(PaygentB2BModuleException__CSV_OUTPUT_ERROR
					. ": Failed to write to CSV file.", E_USER_WARNING);
				return PaygentB2BModuleException__CSV_OUTPUT_ERROR;
			}
		}

		$csvWriter->close();

		$rb = true;

		return $rb;
	}



}

?>
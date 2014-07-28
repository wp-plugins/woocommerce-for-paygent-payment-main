<?php
/**
 * PAYGENT B2B MODULE
 * ResponseDataFactory.php
 *
 * Copyright (C) 2007 by PAYGENT Co., Ltd.
 * All rights reserved.
 */

include_once( WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleException.php");
include_once( WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/system/PaygentB2BModuleResources.php");
include_once( WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/entity/ReferenceResponseDataImpl.php");
include_once( WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/entity/PaymentResponseDataImpl.php");
include_once( WC_PAYGENT_PLUGIN_PATH."/jp/co/ks/merchanttool/connectmodule/entity/FilePaymentResponseDataImpl.php");

/**
 * �����d�������p�I�u�W�F�N�g�쐬�N���X
 *
 * @version $Revision: 15878 $
 * @author $Author: orimoto $
 */
class ResponseDataFactory {

	/**
	 * ResponseData ���쐬
	 *
	 * @param kind
	 * @return ResponseData
	 */
	static function create($kind) {
		$resData = null;
		$masterFile = null;

		$masterFile = PaygentB2BModuleResources::getInstance();

		// Create ResponseData
		if (PaygentB2BModule__TELEGRAM_KIND_FILE_PAYMENT_RES == $kind) {
			// �t�@�C�����ό��ʏƉ�̏ꍇ
			$resData = new FilePaymentResponseDataImpl();
		} elseif ($masterFile->isTelegramKindRef($kind)) {
			// �Ɖ�̏ꍇ
			$resData = new ReferenceResponseDataImpl();
		} else {
			// �Ɖ�ȊO�̏ꍇ
			$resData = new PaymentResponseDataImpl();
		}

		return $resData;
	}

}

?>
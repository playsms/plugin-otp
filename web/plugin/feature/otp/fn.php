<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

function otp_generate($len = 4) {
	$len = ((int) $len > 0 ? (int) $len : 4);
	for ($i = 0; $i < $len; $i++) {
		$otp .= rand(1, 9);
	}
	
	return $otp;
}

function otp_send($msisdn, $template, $len = 4) {
	if ($otp = otp_generate($len)) {
		$message = str_replace('{OTP}', $otp, $template);
		$unicode = core_detect_unicode($message);
		list($ok, $to, $smslog_id, $queue) = sendsms_helper($u, $msisdn, $message, '', $unicode, '', TRUE);
		if ($ok[0] && $to[0] && $smslog_id[0] && $queue[0]) {
			
			return array(
				$otp,
				$to[0],
				$message,
				$smslog_id[0],
				$queue[0] 
			);
		}
	}
	
	return array(
		NULL,
		'',
		'',
		NULL,
		'' 
	);
}

function otp_hook_webservices_output($operation, $requests, $returns) {
	$returns = array();
	
	if (!($operation == 'otp')) {
		return FALSE;
	}
	
	$u = trim($requests['u']);
	$h = trim($requests['h']);
	
	if (!($h && $u && webservices_validate($h, $u))) {
		$returns['modified'] = TRUE;
		$returns['param']['content'] = json_encode(array(
			'status' => 'ERR',
			'error' => 100,
			'error_string' => 'authentication failed' 
		));
		$returns['param']['content-type'] = 'text/json';
		
		_log('OTP status:ERR error:100 u:[' . $u . '] h:[' . $h . ']', 2, 'otp_hook_webservices_output');
		
		return $returns;
	}
	
	$msisdn = trim($requests['msisdn']);
	$template = trim($requests['template']);
	
	if (!($msisdn && $template)) {
		$returns['modified'] = TRUE;
		$returns['param']['content'] = json_encode(array(
			'status' => 'ERR',
			'error' => 102,
			'error_string' => 'one or more field empty' 
		));
		$returns['param']['content-type'] = 'text/json';
		
		_log('OTP status:ERR error:102 msisdn:[' . $msisdn . '] template:[' . $template . ']', 2, 'otp_hook_webservices_output');
		
		return $returns;
	}
	
	$len = (trim($requests['len']) ? trim($requests['len']) : 4);
	
	_log('OTP start sending u:' . $u . ' msisdn:' . $msisdn . ' template:' . $template, 2, 'otp_hook_webservices_output');
	
	list($otp, $to, $message, $smslog_id, $queue) = otp_send($msisdn, $template, $len);
	
	if ($otp && $to && $message && $smslog_id && $queue) {
		$returns['modified'] = TRUE;
		$returns['param']['content'] = json_encode(array(
			'status' => 'OK',
			'error' => '0',
			'error_string' => '',
			'data' => array(
				'otp' => $otp,
				'msisdn' => $to,
				'message' => $message,
				'smslog_id' => $smslog_id,
				'queue' => $queue 
			) 
		));
		$returns['param']['content-type'] = 'text/json';
		
		_log('OTP status:OK error:0 otp:' . $otp . ' u:' . $u . ' msisdn:' . $to . ' smslog_id:' . $smslog_id, 2, 'otp_hook_webservices_output');
	} else {
		$returns['modified'] = TRUE;
		$returns['param']['content'] = json_encode(array(
			'status' => 'ERR',
			'error' => 200,
			'error_string' => 'send message failed' 
		));
		$returns['param']['content-type'] = 'text/json';
		
		_log('OTP status:ERR error:200 otp:' . $otp . ' u:' . $u . ' msisdn:' . $to . ' smslog_id:' . $smslog_id, 2, 'otp_hook_webservices_output');
	}
	
	return $returns;
}

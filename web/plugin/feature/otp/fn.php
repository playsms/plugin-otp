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

function _otp_gentoken($len = 4) {
	$len = ((int) $len > 0 ? (int) $len : 4);
	for ($i = 0; $i < $len; $i++) {
		$token .= rand(1, 9);
	}
	
	return $token;
}

function otp_hook_webservices_output($operation, $requests, $returns) {
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
		
		return $returns;
	}
	
	$len = (trim($requests['len']) ? trim($requests['len']) : 4);
	
	if ($token = _otp_gentoken($len)) {
		$returns['modified'] = TRUE;
		$returns['param']['content'] = json_encode(array(
			'status' => 'OK',
			'error' => '0',
			'error_string' => '',
			'data' => array(
				'token' => $token 
			) 
		));
		$returns['param']['content-type'] = 'text/json';
	}
	
	return $returns;
}

# About

One Time Password plugin for playSMS. This plugin provides webservices for sending OTP.

Info          | Data
--------------|-----------------------------------------
Author        | [Anton Raharja](http://antonraharja.com)
Created       | 160715
Last update   | 160716
Version       | 1
Compatibility | playSMS 1.1 and above
License       | GPLv3

# Changelog

## Version 1

   - changelog started

# Installation

Current version of this plugin should work with playSMS 1.1 and above.

Here is how to install it on a working playSMS:

- Just copy `web/plugin/feature/otp` to the playSMS `plugin/feature` folder
- No need to restart `playsmsd`

# Usage

User app must consume playSMS webservices with following parameters:

Parameters | Description
-----------|-------------------------------------------
`u`        | playSMS username 
`h`        | playSMS webservices token
`msisdn`   | Mobile phone number
`template` | Message template containing `{OTP}` phrase
`len`      | Length of OTP, default is 4

playSMS will returns:

Parameters     | Description
---------------|------------------------------------------------------------------
`status`       | Request status, `OK` or `ERR`
`error`        | Error number, `0` or other number
`error_string` | Error string
`data`         | Upon successful request the data will contain **OTP information**

OTP information:

Parameters  | Description
------------|--------------------------------
`otp`       | One Time Password, numeric only
`msisdn`    | Mobile phone number
`message`   | Translated message template
`smslog_id` | playSMS SMS Log ID 
`queue`     | playSMS queue code

Request example:
```
http://localhost/playsms/index.php?app=ws&u=admin&h=309655625e0dca1db8159c4429b310ef&op=otp&msisdn=0987654321&template=Your+verification+code+is+{OTP}&len=6
```

Returns example:
```
{"status":"OK","error":"0","error_string":"","data":{"otp":"211539","msisdn":"0987654321","message":"Your verification code is 211539","smslog_id":"7","queue":"6d127347ad3fc747833d5b71246090f8"}}
```

<?php
return array(
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	# This file should not be edited directly. If you want
	# to add custom patterns, create a new file named
	# config.html-cut-patterns.php in the same directory
	# as your config.php file.
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// Thunderbird
	'thunderbird_1'                      => 'div #^\s*On ([0-9]+)/([0-9]+)/([0-9]+) (.*?), ([\w\s]*?) wrote:\s*$# br /br /div',
	'thunderbird_str_rested'             => '#^\s*On ([0-9]+)/([0-9]+)/([0-9]+) (.*?), ([\w\s]*?) wrote:#m',

	'unknown_1'                          => 'p blockquote #^on\s#i a /a #\swrote:$#i br /br /blockquote /p',
	'unknown_2'                          => 'hr /hr font p b #from:#i /b #.*# br /br b #sent:#i /b #.*# br /br b #to:#i /b #.*# br /br /p /font',

	// mail from blackberry.net
	'blackberry_1'                       => 'hr /hr div b #from:#i /b /div div b #date:#i /b /div div b #to:#i /b /div',

	// mail from gmail.com
	'gmail_1'                            => 'div #^on\s.*\sat\s.*#i span /span #wrote:#i br /br blockquote /blockquote /div',
	'gmail_1_2'                          => 'div #^on\s+.+,\s+.+#i span a /a /span br /br blockquote /blockquote /div',
	'gmail_2'                            => 'div #^on\s.*$#i a /a #^.*wrote:$#i br /br blockquote /blockquote /div',
	'gmail_3'                            => 'br /br #^on\s.*\swrote:$#i br /br blockquote /blockquote',
	'gmail_4'                            => 'div #^on [a-z]+, [a-z]+ [0-9]+, [0-9]{4} at [a-zA-Z0-9: ]+, .*? wrote:#i br /br blockquote /blockquote /div',

	// Android
	'android_1'                          => 'div #^on\s+.+,\s+.+#i a /a br /br blockquote /blockquote /div',

	// mail from hotmail.com
	'hotmail_1'                          => 'hr /hr #^date:\s#i br /br #^subject:\s#i br /br #^from:\s#i br /br #^to:\s#i br /br',
	'hotmail_2'                          => '#<hr[^>]+>Date:\s+(.*?)<br( /)?>Subject:\s+(.*?)<br( /)?>From:\s+(.*?)<br( /)?>To:\s+(.*?)<br( /)?>#i',

	// blackberry wireless
	'blackberry_2'                       => 'br /br div font b #^from$#i /b #^:\s.*$# br /br b #^sent$#i /b #^:\s.*$# br /br b #^to$#i /b #^:\s.*$# br /br /font /div',

	// verizon wireless
	'verizon_wireless_1'                 => 'br /br div #-+\sreply\smessage\s-+#i br /br #^from:#i br /br #^to:#i /div',

	// X-Mailer: Apple Mail (2.1082)
	'apple_mail_1'                       => 'br /br div div #^on\s.*\sat\s.*\swrote:$#iU /div br /br blockquote /blockquote /div',
	'apple_mail_2'                       => '#On\s+.*?,\s+at\s+[0-9:]+\s+(AM|PM),\s+.*?\s+wrote:\s*<br[^>]*>#',

	// X-Mailer: Microsoft Windows Live Mail 15.4.3538.513
	'windows_live_mail_1'                => 'div b #from:#i /b /div div b #sent:#i /b /div div b #to:#i /b /div div b #subject:#i /b /div',

	// X-Mailer: Microsoft Windows Mail 6.0.6002.18197
	'windows_mail_1'                     => 'blockquote div #-+\soriginal\smessage\s-+#i /div div b #from:#i /b /div div b #to:#i /b /div div b #sent:#i /b /div /blockquote',

	// X-Mailer: Lotus Notes Release 8.5.1 September 28, 2009
	'lotus_notes_1'                      => 'br /br table tr td td table tr td font b /b /font /td /tr /table br /br table tr td font /font td font #to:#i /font /td /td /tr /table br /br table tr td font b #please\srespond\sto\s#i /b /font /td /tr /table',

	// X-Mailer: Lotus Notes Release 8.5.3FP1 March 08, 2012
	'lotus_notes_2'                      => 'font #From:.*?# /font font /font br /br font #To:.*?# /font font /font br /br font #Date:.*?# /font font /font br /br font #Subject:.*?# /font font /font',

	// X-Mailer: Microsoft Office Outlook 12.0
	'outlook_1'                          => 'lang:p b span #%From%:#i /span /b span #.*# br /br b #%Sent%:#i /b #.*# br /br b #%To%:#i /b #.*# br /br /span /p',

	// X-Mailer: Microsoft Outlook 14.0
	'outlook_2'                          => 'lang:p b span #%From%:#i /span /b span #.*# br /br b #%Sent%:#i /b #.*# br /br b #%To%:#i /b #.*# br /br b #%Subject%:#i /b /span /p',

	// Outlook
	'outlook_3'                          => 'lang:font b #%From%:# /b #.*?# br /br b #%Sent%:# /b #.*?# br /br b #%To%:# /b #.*?# br /br b #%Subject%:# /b #.*?# br /br',
	'outlook_4'                          => 'b span #From:# /span /b span #.*?# br /br b #Sent:# /b #.*?# br /br b #To:# /b #.*?# br /br b #Subject:# /b #.*?# /span',
	'outlook_5'                          => 'span #From:#i /span #.*# a /a #.*# br /br span #Date:#i /span #.*# br /br span #To:#i /span #.*# a /a #.*# br /br',
	'outlook_5_replyto'                  => 'span #From:#i /span #.*# a /a #.*# br /br span #Reply\-To:#i /span #.*# a /a #.*# br /br span #Date:#i /span #.*# br /br span #To:#i /span #.*# a /a #.*# br /br',
	'outlook_6'                          => 'lang:div p #%From%:\s+.*?\s+%Sent%:\s+.*?\s+%To%:\s+.*?\s+%Subject%:\s+.*?# /p /div',
	'outlook_mac'                        => 'span #From:#i /span #.*# a /a #.*# a /a #.*# br /br span #Reply\-To:#i /span #.*# a /a #.*# a /a #.*# br /br span #Date:#i /span #.*# br /br span #To:#i /span #.*# a /a #.*# br /br',

	// X-Mailer: iPhone Mail (9A405)
	'iphone_1'                           => 'div br /br #on\s.*\sat\s.*#i a /a #.*wrote:#i /div div /div blockquote /blockquote',

	// X-Mailer: iPhone Mail (8C148)
	'iphone_2'                           => 'div br /br br /br #on\s.*\sat\s.*#i a /a #.*wrote:#i /div div /div blockquote /blockquote',

	'iphone_3'                           => 'div #^on .*?, at .*? wrote:#i /div',

	// X-Mailer: Verizon Webmail
	'verizon_webmail_1'                  => 'span #^\s?on\s.*$#i span /span #^\s?wrote:$#i /span div /div',

	// X-Mailer: YahooMailWebService/0.8.116.338427
	'yahoo_1'                            => 'div font hr /hr b span #from:#i /span /b #.*# br /br b span #to:#i /span /b #.*# br /br b span #sent:#i /span /b #.*# br /br b span #subject:#i /span /b #.*# br /br /font /div',

	// X-Mailer: Motorola android mail 1.0
	'motorola_1'                         => 'br /br #^\s*-+\s?original\smessage\s?-+\s*$#im br /br blockquote /blockquote',

	// X-Mailer: Lotus Domino Web Server Release 8.5.3 September 15, 2011
	'lotus_domino_1'                     => 'br /br font #^-+.*\swrote:\s-+$#im /font div div #to:\s#i br /br #from:\s#i br /br #date:\s#i br /br /div /div',

	// X-MimeOLE: Produced By Microsoft Exchange V6.5
	'exchange_1'                         => 'p #^\s*-+\s?original\smessage\s?-+\s*$#im br /br #from:\s.*#i br /br #sent:\s.*#i br /br #to:\s.*#i br /br /p',

	// X-Mailer: YahooMailClassic/15.0.4 YahooMailWebService/0.8.116.338427
	'yahoo_2'                            => 'div /div b span #from:#i /span /b br /br b span #to:#i /span /b br /br b span #sent:#i /span /b br /br b span #subject:#i /span /b',

	// X-Mailer: Zimbra 7.1.1_GA_3196 (Zimbra Desktop/7.1.4_11299_Windows)
	'zimbra'                             => 'b #From: # /b br /br b #To: # /b br /br b #Sent: # /b br /br b #Subject: # /b',

	// Sparrow mac client
	'sparrow_1'                          => 'p #^On .*?,.*?wrote:#i /p blockquote /blockquote',
);
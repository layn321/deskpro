<?php return array(
	'admin.gateway.account_information'             => 'Account Information',
	'admin.gateway.add_account'                     => 'Add Account',
	'admin.gateway.add_email_gateway'               => 'Add Email Gateway',
	'admin.gateway.add_new_account'                 => 'add a new account',
	'admin.gateway.address_for_gateway'             => 'The address that this gateway account is for',
	'admin.gateway.addresses_matching_regex'        => 'Addresses that match a regular expression',
	'admin.gateway.all_email_is_being_sent_via'     => 'All email is currently being sent through the {{link}}.',
	'admin.gateway.ask_delete_gateway'              => 'Are you sure you want to delete this gateway? Email sent to this inbox will no longer be processed.',
	'admin.gateway.ask_delete_transport'            => 'Are you sure you want to delete this SMTP account? If email is still being sent by the system From any of the affected addresses, users may fail to receive them without this account.',
	'admin.gateway.ask_if_multiple_addresses'       => 'Will this account receive mail for more than one address?',
	'admin.gateway.ask_use_account_when'            => 'When should this account be used?',
	'admin.gateway.backup_account'                  => 'Backup Account',
	'admin.gateway.backup_mail_server'              => 'Backup Mail Server',
	'admin.gateway.backup_outgoing_mail_account'    => 'Backup Outgoing Mail Account',
	'admin.gateway.click_to_add_more'               => 'Click here to add more.',
	'admin.gateway.confirm_process_account'         => 'Yes, process email from this account',
	'admin.gateway.default_from_address'            => 'Default "From" Address',
	'admin.gateway.default_from_email'              => 'Default "From" email address',
	'admin.gateway.default_outgoing_account'        => 'default outgoing account',
	'admin.gateway.delete_transport'                => 'Delete Email Transport',
	'admin.gateway.domain'                          => 'Domain',
	'admin.gateway.edit_gateway'                    => 'Edit Gateway',
	'admin.gateway.edit_server'                     => 'Edit Server',
	'admin.gateway.enabled'                         => 'Enabled',
	'admin.gateway.error_while_connecting'          => 'There was an error while trying to connect to your email account',
	'admin.gateway.exact_address'                   => 'Exact address',
	'admin.gateway.explain_different_servers'       => 'You may {{link}} to send emails using different servers. For example, if your SMTP server requires authentication for each specific "From" address, you will need to create multiple outgoing accounts for each "From" address you plan to support.',
	'admin.gateway.explain_email_gateway'           => 'An email gateway reads email from an email account you control and converts those emails into tickets in DeskPRO for your agents to read and respond to. This allows you to communicate seamlessly with users via normal email.',
	'admin.gateway.from_domain'                     => 'From *@{{pattern}}',
	'admin.gateway.from_google'                     => 'Google Apps: {{username}}',
	'admin.gateway.from_pattern'                    => 'From {{pattern}}',
	'admin.gateway.mail_server'                     => 'Mail Server',
	'admin.gateway.match_domain'                    => 'Every address at a domain',
	'admin.gateway.new_gateway'                     => 'New Gateway',
	'admin.gateway.new_server'                      => 'New Server',
	'admin.gateway.no_errors_were_reported'         => 'No errors were reported.',
	'admin.gateway.notice_1'                        => 'This account is used by default when the system needs to email user. For example, registration confirmations and reset password links. You might also have email gateways and rules set up to send notifications from different email addresses.',
	'admin.gateway.notice_2'                        => 'If you have not set up other SMTP accounts to handle other email addresses, DeskPRO will attempt to use this account with a different "From" address. Depending on the service you are using, this might not work. For example, Gmail only allows the "From" address to be that of the account.',
	'admin.gateway.notice_mail_server_per_from'     => 'You need to define a mail server for every address DeskPRO might send email "From." Sometimes a single mail server might let you send email from any "From" address, but sometimes (such as with Google Apps), the server will only let you send email from a single address.',
	'admin.gateway.outgoing_email'                  => 'Outgoing Email',
	'admin.gateway.outgoing_mail_account'           => 'Outgoing Mail Account',
	'admin.gateway.pop3_account'                    => 'POP3 Account',
	'admin.gateway.revert_default_outgoing_account' => 'Go back to using the default outgoing account',
	'admin.gateway.send_through'                    => 'Send through',
	'admin.gateway.setup_default_mail_server'       => 'Setup Default Mail Server',
	'admin.gateway.test_email_connection'           => 'This will test the connection to your email account.',
	'admin.gateway.test_incoming_email_account'     => 'Test Incoming Email Account',
	'admin.gateway.use_criteria'                    => 'Use Criteria',
	'admin.gateway.use_different_outgoing_account'  => 'Use a different outgoing account',
	'admin.gateway.use_google_for_outgoing'         => 'Use the Google Apps account defined above for outgoing mail',
	'admin.gateway.using_the'                       => 'Using the {{link}}',
	'admin.gateway.when_no_rules_match'             => 'When none of the above rules match, email will be sent through the {{link}}.',
);
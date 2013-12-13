<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * Orb
 *
 * @package Orb
 * @subpackage Data
 */

namespace Orb\Data;

use Orb\Util\Arrays;

/**
 * Source: http://www.itu.int/dms_pub/itu-t/opb/sp/T-SP-E.164D-2009-PDF-E.pdf
 */
class CountryCallingCodes
{
	/**
	 * @var array
	 */
	protected static $codes = array(
		array('country_name' => 'Afghanistan', 'country_code' => 'AF', 'calling_code' => '93'),
		array('country_name' => 'Albania', 'country_code' => 'AL', 'calling_code' => '355'),
		array('country_name' => 'Algeria', 'country_code' => 'DZ', 'calling_code' => '213'),
		array('country_name' => 'American Samoa', 'country_code' => 'AS', 'calling_code' => '1684'),
		array('country_name' => 'Andorra', 'country_code' => 'AD', 'calling_code' => '376'),
		array('country_name' => 'Angola', 'country_code' => 'AO', 'calling_code' => '244'),
		array('country_name' => 'Anguilla', 'country_code' => 'AI', 'calling_code' => '1264'),
		array('country_name' => 'Antarctica', 'country_code' => 'AQ', 'calling_code' => '6721'),
		array('country_name' => 'Antigua and Barbuda', 'country_code' => 'AG', 'calling_code' => '1268'),
		array('country_name' => 'Argentina', 'country_code' => 'AR', 'calling_code' => '54'),
		array('country_name' => 'Armenia', 'country_code' => 'AM', 'calling_code' => '374'),
		array('country_name' => 'Aruba', 'country_code' => 'AW', 'calling_code' => '297'),
		array('country_name' => 'Ascension', 'country_code' => 'AC', 'calling_code' => '247'),
		array('country_name' => 'Australia', 'country_code' => 'AU', 'calling_code' => '61'),
		array('country_name' => 'Austria', 'country_code' => 'AT', 'calling_code' => '43'),
		array('country_name' => 'Azerbaijan', 'country_code' => 'AZ', 'calling_code' => '994'),
		array('country_name' => 'Bahamas', 'country_code' => 'BS', 'calling_code' => '1242'),
		array('country_name' => 'Bahrain', 'country_code' => 'BH', 'calling_code' => '973'),
		array('country_name' => 'Bangladesh', 'country_code' => 'BD', 'calling_code' => '880'),
		array('country_name' => 'Barbados', 'country_code' => 'BB', 'calling_code' => '1246'),
		array('country_name' => 'Belarus', 'country_code' => 'BY', 'calling_code' => '375'),
		array('country_name' => 'Belgium', 'country_code' => 'BE', 'calling_code' => '32'),
		array('country_name' => 'Belize', 'country_code' => 'BZ', 'calling_code' => '501'),
		array('country_name' => 'Benin', 'country_code' => 'BJ', 'calling_code' => '229'),
		array('country_name' => 'Bermuda', 'country_code' => 'BM', 'calling_code' => '1441'),
		array('country_name' => 'Bhutan', 'country_code' => 'BT', 'calling_code' => '975'),
		array('country_name' => 'Bolivia', 'country_code' => 'BO', 'calling_code' => '591'),
		array('country_name' => 'Bosnia and Herzegovina', 'country_code' => 'BA', 'calling_code' => '387'),
		array('country_name' => 'Botswana', 'country_code' => 'BW', 'calling_code' => '267'),
		array('country_name' => 'Brazil', 'country_code' => 'BR', 'calling_code' => '55'),
		array('country_name' => 'Brunei', 'country_code' => 'BN', 'calling_code' => '673'),
		array('country_name' => 'Bulgaria', 'country_code' => 'BG', 'calling_code' => '359'),
		array('country_name' => 'Burkina Faso', 'country_code' => 'BF', 'calling_code' => '226'),
		array('country_name' => 'Burundi', 'country_code' => 'BI', 'calling_code' => '257'),
		array('country_name' => 'Cambodia', 'country_code' => 'KH', 'calling_code' => '855'),
		array('country_name' => 'Cameroon', 'country_code' => 'CM', 'calling_code' => '237'),
		array('country_name' => 'Canada', 'country_code' => 'CA', 'calling_code' => '1'),
		array('country_name' => 'Cape Verde', 'country_code' => 'CV', 'calling_code' => '238'),
		array('country_name' => 'Cayman Islands', 'country_code' => 'KY', 'calling_code' => '1345'),
		array('country_name' => 'Central African Republic', 'country_code' => 'CF', 'calling_code' => '236'),
		array('country_name' => 'Chad', 'country_code' => 'TD', 'calling_code' => '235'),
		array('country_name' => 'Chile', 'country_code' => 'CL', 'calling_code' => '56'),
		array('country_name' => 'China', 'country_code' => 'CN', 'calling_code' => '86'),
		array('country_name' => 'Christmas Island', 'country_code' => 'CX', 'calling_code' => '618'),
		array('country_name' => 'Cocos (Keeling) Islands', 'country_code' => 'CC', 'calling_code' => '618'),
		array('country_name' => 'Colombia', 'country_code' => 'CO', 'calling_code' => '57'),
		array('country_name' => 'Comoros', 'country_code' => 'KM', 'calling_code' => '269'),
		array('country_name' => 'Congo', 'country_code' => 'CG', 'calling_code' => '243'),
		array('country_name' => 'Congo', 'country_code' => 'CG', 'calling_code' => '242'),
		array('country_name' => 'Cook Islands', 'country_code' => 'CK', 'calling_code' => '682'),
		array('country_name' => 'Costa Rica', 'country_code' => 'CR', 'calling_code' => '506'),
		array('country_name' => 'Cote d\'Ivoire', 'country_code' => 'CI', 'calling_code' => '225'),
		array('country_name' => 'Croatia', 'country_code' => 'HR', 'calling_code' => '385'),
		array('country_name' => 'Cuba', 'country_code' => 'CU', 'calling_code' => '53'),
		array('country_name' => 'Cyprus', 'country_code' => 'CY', 'calling_code' => '357'),
		array('country_name' => 'Czech Republic', 'country_code' => 'CZ', 'calling_code' => '420'),
		array('country_name' => 'Denmark', 'country_code' => 'DK', 'calling_code' => '45'),
		array('country_name' => 'Diego Garcia', 'country_code' => 'DG', 'calling_code' => '246'),
		array('country_name' => 'Djibouti', 'country_code' => 'DJ', 'calling_code' => '253'),
		array('country_name' => 'Dominica', 'country_code' => 'DM', 'calling_code' => '1767'),
		array('country_name' => 'Dominican Republic', 'country_code' => 'DO', 'calling_code' => '1809'),
		array('country_name' => 'Dominican Republic', 'country_code' => 'DO', 'calling_code' => '1829'),
		array('country_name' => 'East Timor', 'country_code' => 'TL', 'calling_code' => '670'),
		array('country_name' => 'Ecuador', 'country_code' => 'EC', 'calling_code' => '593'),
		array('country_name' => 'Egypt', 'country_code' => 'EG', 'calling_code' => '20'),
		array('country_name' => 'El Salvador', 'country_code' => 'SV', 'calling_code' => '503'),
		array('country_name' => 'Equatorial Guinea', 'country_code' => 'GQ', 'calling_code' => '240'),
		array('country_name' => 'Eritrea', 'country_code' => 'ER', 'calling_code' => '291'),
		array('country_name' => 'Estonia', 'country_code' => 'EE', 'calling_code' => '372'),
		array('country_name' => 'Ethiopia', 'country_code' => 'ET', 'calling_code' => '251'),
		array('country_name' => 'Faeroe Islands', 'country_code' => 'FO', 'calling_code' => '500'),
		array('country_name' => 'Falkland Islands', 'country_code' => 'FK', 'calling_code' => '298'),
		array('country_name' => 'Fiji', 'country_code' => 'FJ', 'calling_code' => '679'),
		array('country_name' => 'Finland', 'country_code' => 'FI', 'calling_code' => '358'),
		array('country_name' => 'France', 'country_code' => 'FR', 'calling_code' => '33'),
		array('country_name' => 'French Guiana', 'country_code' => 'GF', 'calling_code' => '594'),
		array('country_name' => 'French Polynesia', 'country_code' => 'PF', 'calling_code' => '689'),
		array('country_name' => 'Gabon', 'country_code' => 'GA', 'calling_code' => '241'),
		array('country_name' => 'Gambia', 'country_code' => 'GM', 'calling_code' => '220'),
		array('country_name' => 'Georgia', 'country_code' => 'GE', 'calling_code' => '995'),
		array('country_name' => 'Germany', 'country_code' => 'DE', 'calling_code' => '49'),
		array('country_name' => 'Ghana', 'country_code' => 'GH', 'calling_code' => '233'),
		array('country_name' => 'Gibraltar', 'country_code' => 'GI', 'calling_code' => '350'),
		array('country_name' => 'Greece', 'country_code' => 'GR', 'calling_code' => '30'),
		array('country_name' => 'Greenland', 'country_code' => 'GL', 'calling_code' => '299'),
		array('country_name' => 'Grenada', 'country_code' => 'GD', 'calling_code' => '1473'),
		array('country_name' => 'Guadeloupe', 'country_code' => 'GP', 'calling_code' => '590'),
		array('country_name' => 'Guam', 'country_code' => 'GU', 'calling_code' => '1671'),
		array('country_name' => 'Guatemala', 'country_code' => 'GT', 'calling_code' => '502'),
		array('country_name' => 'Guinea', 'country_code' => 'GN', 'calling_code' => '224'),
		array('country_name' => 'Guinea-Bissau', 'country_code' => 'GW', 'calling_code' => '245'),
		array('country_name' => 'Guyana', 'country_code' => 'GY', 'calling_code' => '592'),
		array('country_name' => 'Haiti', 'country_code' => 'HT', 'calling_code' => '509'),
		array('country_name' => 'Honduras', 'country_code' => 'HN', 'calling_code' => '504'),
		array('country_name' => 'Hong Kong', 'country_code' => 'HK', 'calling_code' => '852'),
		array('country_name' => 'Hungary', 'country_code' => 'HU', 'calling_code' => '36'),
		array('country_name' => 'Iceland', 'country_code' => 'IS', 'calling_code' => '354'),
		array('country_name' => 'India', 'country_code' => 'IN', 'calling_code' => '91'),
		array('country_name' => 'Indonesia', 'country_code' => 'ID', 'calling_code' => '62'),
		array('country_name' => 'Iran', 'country_code' => 'IR', 'calling_code' => '98'),
		array('country_name' => 'Iraq', 'country_code' => 'IQ', 'calling_code' => '964'),
		array('country_name' => 'Ireland', 'country_code' => 'IE', 'calling_code' => '353'),
		array('country_name' => 'Israel', 'country_code' => 'IL', 'calling_code' => '972'),
		array('country_name' => 'Italy', 'country_code' => 'IT', 'calling_code' => '39'),
		array('country_name' => 'Jamaica', 'country_code' => 'JM', 'calling_code' => '1876'),
		array('country_name' => 'Japan', 'country_code' => 'JP', 'calling_code' => '81'),
		array('country_name' => 'Jordan', 'country_code' => 'JO', 'calling_code' => '962'),
		array('country_name' => 'Kazakhstan', 'country_code' => 'KZ', 'calling_code' => '77'),
		array('country_name' => 'Kenya', 'country_code' => 'KE', 'calling_code' => '254'),
		array('country_name' => 'Kiribati', 'country_code' => 'KI', 'calling_code' => '686'),
		array('country_name' => 'Korea (North)', 'country_code' => 'KP', 'calling_code' => '850'),
		array('country_name' => 'Korea (South)', 'country_code' => 'KR', 'calling_code' => '82'),
		array('country_name' => 'Kuwait', 'country_code' => 'KW', 'calling_code' => '965'),
		array('country_name' => 'Kyrgyzstan', 'country_code' => 'KG', 'calling_code' => '996'),
		array('country_name' => 'Laos', 'country_code' => 'LA', 'calling_code' => '856'),
		array('country_name' => 'Latvia', 'country_code' => 'LV', 'calling_code' => '371'),
		array('country_name' => 'Lebanon', 'country_code' => 'LB', 'calling_code' => '961'),
		array('country_name' => 'Lesotho', 'country_code' => 'LS', 'calling_code' => '266'),
		array('country_name' => 'Liberia', 'country_code' => 'LR', 'calling_code' => '231'),
		array('country_name' => 'Libya', 'country_code' => 'LY', 'calling_code' => '218'),
		array('country_name' => 'Liechtenstein', 'country_code' => 'LI', 'calling_code' => '423'),
		array('country_name' => 'Lithuania', 'country_code' => 'LT', 'calling_code' => '370'),
		array('country_name' => 'Luxembourg', 'country_code' => 'LU', 'calling_code' => '352'),
		array('country_name' => 'Macau', 'country_code' => 'MO', 'calling_code' => '853'),
		array('country_name' => 'Macedonia', 'country_code' => 'MK', 'calling_code' => '389'),
		array('country_name' => 'Madagascar', 'country_code' => 'MG', 'calling_code' => '261'),
		array('country_name' => 'Malawi', 'country_code' => 'MW', 'calling_code' => '265'),
		array('country_name' => 'Malaysia', 'country_code' => 'MY', 'calling_code' => '60'),
		array('country_name' => 'Maldives', 'country_code' => 'MV', 'calling_code' => '960'),
		array('country_name' => 'Mali', 'country_code' => 'ML', 'calling_code' => '223'),
		array('country_name' => 'Malta', 'country_code' => 'MT', 'calling_code' => '356'),
		array('country_name' => 'Marshall Islands', 'country_code' => 'MH', 'calling_code' => '692'),
		array('country_name' => 'Martinique', 'country_code' => 'MQ', 'calling_code' => '596'),
		array('country_name' => 'Mauritania', 'country_code' => 'MR', 'calling_code' => '222'),
		array('country_name' => 'Mauritius', 'country_code' => 'MU', 'calling_code' => '230'),
		array('country_name' => 'Mayotte', 'country_code' => 'YT', 'calling_code' => '52'),
		array('country_name' => 'Mexico', 'country_code' => 'MX', 'calling_code' => '691'),
		array('country_name' => 'Micronesia', 'country_code' => 'FM', 'calling_code' => '373'),
		array('country_name' => 'Moldova', 'country_code' => 'MD', 'calling_code' => '377'),
		array('country_name' => 'Monaco', 'country_code' => 'MC', 'calling_code' => '976'),
		array('country_name' => 'Mongolia', 'country_code' => 'MN', 'calling_code' => '382'),
		array('country_name' => 'Montserrat', 'country_code' => 'MS', 'calling_code' => '1664'),
		array('country_name' => 'Morocco', 'country_code' => 'MA', 'calling_code' => '212'),
		array('country_name' => 'Mozambique', 'country_code' => 'MZ', 'calling_code' => '258'),
		array('country_name' => 'Myanmar', 'country_code' => 'MM', 'calling_code' => '95'),
		array('country_name' => 'Namibia', 'country_code' => 'NA', 'calling_code' => '264'),
		array('country_name' => 'Nauru', 'country_code' => 'NR', 'calling_code' => '674'),
		array('country_name' => 'Nepal', 'country_code' => 'NP', 'calling_code' => '977'),
		array('country_name' => 'Netherlands', 'country_code' => 'NL', 'calling_code' => '31'),
		array('country_name' => 'Netherlands Antilles', 'country_code' => 'AN', 'calling_code' => '599'),
		array('country_name' => 'New Caledonia', 'country_code' => 'NC', 'calling_code' => '687'),
		array('country_name' => 'New Zealand', 'country_code' => 'NZ', 'calling_code' => '64'),
		array('country_name' => 'Nicaragua', 'country_code' => 'NI', 'calling_code' => '505'),
		array('country_name' => 'Niger', 'country_code' => 'NE', 'calling_code' => '227'),
		array('country_name' => 'Nigeria', 'country_code' => 'NG', 'calling_code' => '234'),
		array('country_name' => 'Niue', 'country_code' => 'NU', 'calling_code' => '683'),
		array('country_name' => 'Norfolk Island', 'country_code' => 'NF', 'calling_code' => '6723'),
		array('country_name' => 'Northern Marianas', 'country_code' => 'MP', 'calling_code' => '1670'),
		array('country_name' => 'Norway', 'country_code' => 'NO', 'calling_code' => '47'),
		array('country_name' => 'Oman', 'country_code' => 'OM', 'calling_code' => '968'),
		array('country_name' => 'Pakistan', 'country_code' => 'PK', 'calling_code' => '92'),
		array('country_name' => 'Palau', 'country_code' => 'PW', 'calling_code' => '680'),
		array('country_name' => 'Palestinian Settlements', 'country_code' => 'PS', 'calling_code' => '970'),
		array('country_name' => 'Panama', 'country_code' => 'PA', 'calling_code' => '507'),
		array('country_name' => 'Papua New Guinea', 'country_code' => 'PG', 'calling_code' => '675'),
		array('country_name' => 'Paraguay', 'country_code' => 'PY', 'calling_code' => '595'),
		array('country_name' => 'Peru', 'country_code' => 'PE', 'calling_code' => '51'),
		array('country_name' => 'Philippines', 'country_code' => 'PH', 'calling_code' => '63'),
		array('country_name' => 'Poland', 'country_code' => 'PL', 'calling_code' => '48'),
		array('country_name' => 'Portugal', 'country_code' => 'PT', 'calling_code' => '351'),
		array('country_name' => 'Puerto Rico', 'country_code' => 'PR', 'calling_code' => '1787'),
		array('country_name' => 'Puerto Rico', 'country_code' => 'PR', 'calling_code' => '1939'),
		array('country_name' => 'Qatar', 'country_code' => 'QA', 'calling_code' => '974'),
		array('country_name' => 'Réunion', 'country_code' => 'RE', 'calling_code' => '262'),
		array('country_name' => 'Romania', 'country_code' => 'RO', 'calling_code' => '40'),
		array('country_name' => 'Russia', 'country_code' => 'RU', 'calling_code' => '7'),
		array('country_name' => 'Rwanda', 'country_code' => 'RW', 'calling_code' => '250'),
		array('country_name' => 'Saint Helena', 'country_code' => 'SH', 'calling_code' => '290'),
		array('country_name' => 'Saint Kitts and Nevis', 'country_code' => 'KN', 'calling_code' => '1869'),
		array('country_name' => 'Saint Lucia', 'country_code' => 'LC', 'calling_code' => '1758'),
		array('country_name' => 'Saint Pierre and Miquelon', 'country_code' => 'PM', 'calling_code' => '508'),
		array('country_name' => 'Saint Vincent and Grenadines', 'country_code' => 'VC', 'calling_code' => '1784'),
		array('country_name' => 'Samoa', 'country_code' => 'WS', 'calling_code' => '685'),
		array('country_name' => 'San Marino', 'country_code' => 'SM', 'calling_code' => '378'),
		array('country_name' => 'São Tomé and Príncipe', 'country_code' => 'ST', 'calling_code' => '239'),
		array('country_name' => 'Saudi Arabia', 'country_code' => 'SA', 'calling_code' => '966'),
		array('country_name' => 'Senegal', 'country_code' => 'SN', 'calling_code' => '221'),
		array('country_name' => 'Serbia', 'country_code' => 'RS', 'calling_code' => '381'),
		array('country_name' => 'Seychelles', 'country_code' => 'SC', 'calling_code' => '248'),
		array('country_name' => 'Sierra Leone', 'country_code' => 'SL', 'calling_code' => '232'),
		array('country_name' => 'Singapore', 'country_code' => 'SG', 'calling_code' => '65'),
		array('country_name' => 'Slovakia', 'country_code' => 'SK', 'calling_code' => '421'),
		array('country_name' => 'Slovenia', 'country_code' => 'SI', 'calling_code' => '386'),
		array('country_name' => 'Solomon Islands', 'country_code' => 'SB', 'calling_code' => '677'),
		array('country_name' => 'Somalia', 'country_code' => 'SO', 'calling_code' => '252'),
		array('country_name' => 'South Africa', 'country_code' => 'ZA', 'calling_code' => '27'),
		array('country_name' => 'Spain', 'country_code' => 'ES', 'calling_code' => '34'),
		array('country_name' => 'Sri Lanka', 'country_code' => 'LK', 'calling_code' => '94'),
		array('country_name' => 'Sudan', 'country_code' => 'SD', 'calling_code' => '249'),
		array('country_name' => 'Suriname', 'country_code' => 'SR', 'calling_code' => '597'),
		array('country_name' => 'Swaziland', 'country_code' => 'SZ', 'calling_code' => '268'),
		array('country_name' => 'Sweden', 'country_code' => 'SE', 'calling_code' => '46'),
		array('country_name' => 'Switzerland', 'country_code' => 'CH', 'calling_code' => '41'),
		array('country_name' => 'Syria', 'country_code' => 'SY', 'calling_code' => '963'),
		array('country_name' => 'Taiwan', 'country_code' => 'TW', 'calling_code' => '886'),
		array('country_name' => 'Tajikistan', 'country_code' => 'TJ', 'calling_code' => '992'),
		array('country_name' => 'Tanzania', 'country_code' => 'TZ', 'calling_code' => '255'),
		array('country_name' => 'Thailand', 'country_code' => 'TH', 'calling_code' => '66'),
		array('country_name' => 'Togo', 'country_code' => 'TG', 'calling_code' => '228'),
		array('country_name' => 'Tokelau', 'country_code' => 'TK', 'calling_code' => '690'),
		array('country_name' => 'Tonga', 'country_code' => 'TO', 'calling_code' => '676'),
		array('country_name' => 'Trinidad and Tobago', 'country_code' => 'TT', 'calling_code' => '1868'),
		array('country_name' => 'Tunisia', 'country_code' => 'TN', 'calling_code' => '216'),
		array('country_name' => 'Turkey', 'country_code' => 'TR', 'calling_code' => '90'),
		array('country_name' => 'Turkmenistan', 'country_code' => 'TM', 'calling_code' => '993'),
		array('country_name' => 'Turks and Caicos Islands', 'country_code' => 'TC', 'calling_code' => '1649'),
		array('country_name' => 'Tuvalu', 'country_code' => 'TV', 'calling_code' => '688'),
		array('country_name' => 'Uganda', 'country_code' => 'UG', 'calling_code' => '256'),
		array('country_name' => 'Ukraine', 'country_code' => 'UA', 'calling_code' => '380'),
		array('country_name' => 'United Arab Emirates', 'country_code' => 'AE', 'calling_code' => '971'),
		array('country_name' => 'United Kingdom', 'country_code' => 'GB', 'calling_code' => '44'),
		array('country_name' => 'United States', 'country_code' => 'US', 'calling_code' => '1'),
		array('country_name' => 'Uruguay', 'country_code' => 'UY', 'calling_code' => '598'),
		array('country_name' => 'US Virgin Islands', 'country_code' => 'VI', 'calling_code' => '1340'),
		array('country_name' => 'Uzbekistan', 'country_code' => 'UZ', 'calling_code' => '998'),
		array('country_name' => 'Vanuatu', 'country_code' => 'VU', 'calling_code' => '678'),
		array('country_name' => 'Venezuela', 'country_code' => 'VE', 'calling_code' => '58'),
		array('country_name' => 'Vietnam', 'country_code' => 'VN', 'calling_code' => '84'),
		array('country_name' => 'Virgin Islands', 'country_code' => 'VG', 'calling_code' => '1284'),
		array('country_name' => 'Wake Island', 'country_code' => 'WK', 'calling_code' => '808'),
		array('country_name' => 'Wallis and Futuna', 'country_code' => 'WF', 'calling_code' => '681'),
		array('country_name' => 'Yemen', 'country_code' => 'YE', 'calling_code' => '967'),
		array('country_name' => 'Zambia', 'country_code' => 'ZM', 'calling_code' => '260'),
		array('country_name' => 'Zimbabwe', 'country_code' => 'ZW', 'calling_code' => '263'),
	);


	/**
	 * Gets a full raw data array where each item is array(country name, country code, calling code)
	 *
	 * @return array
	 */
	public static function getData()
	{
		return self::$codes;
	}


	/**
	 * Gets a map of countrycode=>callingcode
	 *
	 * @return array
	 */
	public static function getCountryCodeToCallingCode()
	{
		static $map = null;

		if ($map === null) {
			foreach (self::$codes as $info) {
				$map[$info['country_code']] = $info['calling_code'];
			}
		}

		return $map;
	}
}

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

class Countries
{
	/**
	 * Array of country codes to their names.
	 * @var array
	 */
	protected static $code_to_name = array(
		'AF' => 'Afghanistan',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Columbia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivorie (Ivory Coast)',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'CD' => 'Democratic Republic Of Congo (Zaire)',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'TP' => 'East Timor',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'FX' => 'France, Metropolitan',
		'GF' => 'French Guinea',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard And McDonald Islands',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Laos',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macau',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'KP' => 'North Korea',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And The Grenadines',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And South Sandwich Islands',
		'KR' => 'South Korea',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syria',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'UK' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Minor Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VA' => 'Vatican City (Holy See)',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VG' => 'Virgin Islands (British)',
		'VI' => 'Virgin Islands (US)',
		'WF' => 'Wallis And Futuna Islands',
		'EH' => 'Western Sahara',
		'WS' => 'Western Samoa',
		'YE' => 'Yemen',
		'YU' => 'Yugoslavia',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe'
	);

	/**
	 * Maps country code to 3-letter continent code.
	 * @var array
	 */
	protected static $code_to_continent = array(
		'AD' => 'EUR',
		'AE' => 'ASI',
		'AF' => 'ASI',
		'AG' => 'AMS',
		'AI' => 'AMS',
		'AL' => 'EUR',
		'AM' => 'ASI',
		'AN' => 'AMS',
		'AO' => 'AFR',
		'AQ' => 'OCE',
		'AR' => 'AMS',
		'AS' => 'OCE',
		'AT' => 'EUR',
		'AU' => 'OCE',
		'AW' => 'AMS',
		'AX' => 'EUR',
		'AZ' => 'ASI',
		'BA' => 'EUR',
		'BB' => 'AMS',
		'BD' => 'ASI',
		'BE' => 'EUR',
		'BF' => 'AFR',
		'BG' => 'EUR',
		'BH' => 'ASI',
		'BI' => 'AFR',
		'BJ' => 'AFR',
		'BL' => 'AMS',
		'BM' => 'AMS',
		'BN' => 'ASI',
		'BO' => 'AMS',
		'BR' => 'AMS',
		'BS' => 'AMS',
		'BT' => 'ASI',
		'BV' => 'OCE',
		'BW' => 'AFR',
		'BY' => 'EUR',
		'BZ' => 'AMS',
		'CA' => 'AMN',
		'CC' => 'OCE',
		'CD' => 'AFR',
		'CF' => 'AFR',
		'CG' => 'AFR',
		'CH' => 'EUR',
		'CI' => 'AFR',
		'CK' => 'ASI',
		'CL' => 'AMS',
		'CM' => 'AFR',
		'CN' => 'ASI',
		'CO' => 'AMS',
		'CR' => 'AMS',
		'CU' => 'AMS',
		'CV' => 'AFR',
		'CX' => 'OCE',
		'CY' => 'EUR',
		'CZ' => 'EUR',
		'DE' => 'EUR',
		'DJ' => 'AFR',
		'DK' => 'EUR',
		'DM' => 'AMS',
		'DO' => 'AMS',
		'DZ' => 'AFR',
		'EC' => 'AMS',
		'EE' => 'EUR',
		'EG' => 'AFR',
		'EH' => 'AFR',
		'ER' => 'AFR',
		'ES' => 'EUR',
		'ET' => 'AFR',
		'FI' => 'EUR',
		'FJ' => 'OCE',
		'FK' => 'AMS',
		'FM' => 'OCE',
		'FO' => 'EUR',
		'FR' => 'EUR',
		'GA' => 'AFR',
		'GB' => 'EUR',
		'GD' => 'AMS',
		'GE' => 'ASI',
		'GF' => 'AMS',
		'GG' => 'EUR',
		'GH' => 'AFR',
		'GI' => 'AFR',
		'GL' => 'AMN',
		'GM' => 'AFR',
		'GN' => 'AFR',
		'GP' => 'AMS',
		'GQ' => 'AFR',
		'GR' => 'EUR',
		'GS' => 'EUR',
		'GT' => 'AMS',
		'GU' => 'ASI',
		'GW' => 'AFR',
		'GY' => 'AMS',
		'HK' => 'ASI',
		'HM' => 'OCE',
		'HN' => 'AMS',
		'HR' => 'EUR',
		'HT' => 'AMS',
		'HU' => 'EUR',
		'ID' => 'ASI',
		'IE' => 'EUR',
		'IL' => 'ASI',
		'IM' => 'EUR',
		'IN' => 'ASI',
		'IO' => 'ASI',
		'IQ' => 'ASI',
		'IR' => 'ASI',
		'IS' => 'EUR',
		'IT' => 'EUR',
		'JE' => 'EUR',
		'JM' => 'AMS',
		'JO' => 'ASI',
		'JP' => 'ASI',
		'KE' => 'AFR',
		'KG' => 'ASI',
		'KH' => 'ASI',
		'KI' => 'OCE',
		'KM' => 'AFR',
		'KN' => 'AMS',
		'KP' => 'ASI',
		'KR' => 'ASI',
		'KW' => 'ASI',
		'KY' => 'AMS',
		'KZ' => 'ASI',
		'LA' => 'ASI',
		'LB' => 'ASI',
		'LC' => 'AMS',
		'LI' => 'EUR',
		'LK' => 'ASI',
		'LR' => 'AFR',
		'LS' => 'AFR',
		'LT' => 'EUR',
		'LU' => 'EUR',
		'LV' => 'EUR',
		'LY' => 'AFR',
		'MA' => 'AFR',
		'MC' => 'EUR',
		'MD' => 'EUR',
		'ME' => 'EUR',
		'MF' => 'AMS',
		'MG' => 'AFR',
		'MH' => 'OCE',
		'MK' => 'EUR',
		'ML' => 'AFR',
		'MM' => 'ASI',
		'MN' => 'ASI',
		'MO' => 'ASI',
		'MP' => 'ASI',
		'MQ' => 'AMS',
		'MR' => 'AFR',
		'MS' => 'AMS',
		'MT' => 'EUR',
		'MU' => 'AFR',
		'MV' => 'ASI',
		'MW' => 'AFR',
		'MX' => 'AMS',
		'MY' => 'ASI',
		'MZ' => 'AFR',
		'NA' => 'AFR',
		'NC' => 'OCE',
		'NE' => 'AFR',
		'NF' => 'OCE',
		'NG' => 'AFR',
		'NI' => 'AMS',
		'NL' => 'EUR',
		'NO' => 'EUR',
		'NP' => 'ASI',
		'NR' => 'OCE',
		'NU' => 'OCE',
		'NZ' => 'OCE',
		'OM' => 'ASI',
		'PA' => 'AMS',
		'PE' => 'AMS',
		'PF' => 'OCE',
		'PG' => 'OCE',
		'PH' => 'ASI',
		'PK' => 'ASI',
		'PL' => 'EUR',
		'PM' => 'AMN',
		'PN' => 'OCE',
		'PR' => 'AMS',
		'PS' => 'ASI',
		'PT' => 'EUR',
		'PW' => 'OCE',
		'PY' => 'AMS',
		'QA' => 'ASI',
		'RE' => 'AFR',
		'RO' => 'EUR',
		'RS' => 'EUR',
		'RU' => 'EUR',
		'RW' => 'AFR',
		'SA' => 'ASI',
		'SB' => 'OCE',
		'SC' => 'AFR',
		'SD' => 'AFR',
		'SE' => 'EUR',
		'SG' => 'ASI',
		'SH' => 'AFR',
		'SI' => 'EUR',
		'SJ' => 'EUR',
		'SK' => 'EUR',
		'SL' => 'AFR',
		'SM' => 'EUR',
		'SN' => 'AFR',
		'SO' => 'AFR',
		'SR' => 'AMS',
		'ST' => 'AFR',
		'SV' => 'AMS',
		'SY' => 'ASI',
		'SZ' => 'AFR',
		'TC' => 'AMS',
		'TD' => 'AFR',
		'TF' => 'OCE',
		'TG' => 'AFR',
		'TH' => 'ASI',
		'TJ' => 'ASI',
		'TK' => 'OCE',
		'TL' => 'ASI',
		'TM' => 'ASI',
		'TN' => 'AFR',
		'TO' => 'OCE',
		'TR' => 'EUR',
		'TT' => 'AMS',
		'TV' => 'ASI',
		'TW' => 'ASI',
		'TZ' => 'AFR',
		'UA' => 'EUR',
		'UG' => 'AFR',
		'UM' => 'OCE',
		'US' => 'AMN',
		'UY' => 'AMS',
		'UZ' => 'ASI',
		'VA' => 'EUR',
		'VC' => 'AMS',
		'VE' => 'AMS',
		'VG' => 'AMS',
		'VI' => 'AMS',
		'VN' => 'ASI',
		'VU' => 'OCE',
		'WF' => 'OCE',
		'WS' => 'ASI',
		'YE' => 'ASI',
		'YT' => 'AFR',
		'ZA' => 'AFR',
		'ZM' => 'AFR',
		'ZW' => 'AFR',

		// exceptionally reserved
		'AC' => 'AFR', // .ac TLD
		'CP' => 'AMS',
		'DG' => 'ASI',
		'EA' => 'AFR',
		'EU' => 'EUR', // .eu TLD
		'FX' => 'EUR',
		'IC' => 'AFR',
		'SU' => 'EUR', // .su TLD
		'TA' => 'AFR',
		'UK' => 'EUR', // .uk TLD

		// transitionally reserved
		'BU' => 'ASI',
		'CS' => 'EUR', // former Serbia and Montenegro
		'NT' => 'ASI',
		'SF' => 'EUR',
		'TP' => 'OCE', // .tp TLD
		'YU' => 'EUR', // .yu TLD
		'ZR' => 'AFR',
	);

	/**
	 * A simple lookup array to try and reverse a country to name. Not very good.
	 * @var array
	 */
	protected static $name_to_code = array(
		'afghanistan' => 'AF',
		'albania' => 'AL',
		'algeria' => 'DZ',
		'americansamoa' => 'AS',
		'andorra' => 'AD',
		'angola' => 'AO',
		'anguilla' => 'AI',
		'antarctica' => 'AQ',
		'antiguaandbarbuda' => 'AG',
		'argentina' => 'AR',
		'armenia' => 'AM',
		'aruba' => 'AW',
		'australia' => 'AU',
		'austria' => 'AT',
		'azerbaijan' => 'AZ',
		'bahamas' => 'BS',
		'bahrain' => 'BH',
		'bangladesh' => 'BD',
		'barbados' => 'BB',
		'belarus' => 'BY',
		'belgium' => 'BE',
		'belize' => 'BZ',
		'benin' => 'BJ',
		'bermuda' => 'BM',
		'bhutan' => 'BT',
		'bolivia' => 'BO',
		'bosniaandherzegovina' => 'BA',
		'botswana' => 'BW',
		'bouvetisland' => 'BV',
		'brazil' => 'BR',
		'britishindianoceanterritory' => 'IO',
		'brunei' => 'BN',
		'bulgaria' => 'BG',
		'burkinafaso' => 'BF',
		'burundi' => 'BI',
		'cambodia' => 'KH',
		'cameroon' => 'CM',
		'canada' => 'CA',
		'capeverde' => 'CV',
		'caymanislands' => 'KY',
		'centralafricanrepublic' => 'CF',
		'chad' => 'TD',
		'chile' => 'CL',
		'china' => 'CN',
		'christmasisland' => 'CX',
		'cocos(keeling)islands' => 'CC',
		'columbia' => 'CO',
		'comoros' => 'KM',
		'congo' => 'CG',
		'cookislands' => 'CK',
		'costarica' => 'CR',
		'cotedivorieivorycoast' => 'CI',
		'croatiahrvatska' => 'HR',
		'cuba' => 'CU',
		'cyprus' => 'CY',
		'czechrepublic' => 'CZ',
		'democraticrepublicofcongozaire' => 'CD',
		'denmark' => 'DK',
		'djibouti' => 'DJ',
		'dominica' => 'DM',
		'dominicanrepublic' => 'DO',
		'easttimor' => 'TP',
		'ecuador' => 'EC',
		'egypt' => 'EG',
		'elsalvador' => 'SV',
		'equatorialguinea' => 'GQ',
		'eritrea' => 'ER',
		'estonia' => 'EE',
		'ethiopia' => 'ET',
		'falklandislandsmalvinas' => 'FK',
		'faroeislands' => 'FO',
		'fiji' => 'FJ',
		'finland' => 'FI',
		'france' => 'FR',
		'france,metropolitan' => 'FX',
		'frenchguinea' => 'GF',
		'frenchpolynesia' => 'PF',
		'frenchsouthernterritories' => 'TF',
		'gabon' => 'GA',
		'gambia' => 'GM',
		'georgia' => 'GE',
		'germany' => 'DE',
		'ghana' => 'GH',
		'gibraltar' => 'GI',
		'greece' => 'GR',
		'greenland' => 'GL',
		'grenada' => 'GD',
		'guadeloupe' => 'GP',
		'guam' => 'GU',
		'guatemala' => 'GT',
		'guinea' => 'GN',
		'guinea-bissau' => 'GW',
		'guyana' => 'GY',
		'haiti' => 'HT',
		'heardandmcdonaldislands' => 'HM',
		'honduras' => 'HN',
		'hongkong' => 'HK',
		'hungary' => 'HU',
		'iceland' => 'IS',
		'india' => 'IN',
		'indonesia' => 'ID',
		'iran' => 'IR',
		'iraq' => 'IQ',
		'ireland' => 'IE',
		'israel' => 'IL',
		'italy' => 'IT',
		'jamaica' => 'JM',
		'japan' => 'JP',
		'jordan' => 'JO',
		'kazakhstan' => 'KZ',
		'kenya' => 'KE',
		'kiribati' => 'KI',
		'kuwait' => 'KW',
		'kyrgyzstan' => 'KG',
		'laos' => 'LA',
		'latvia' => 'LV',
		'lebanon' => 'LB',
		'lesotho' => 'LS',
		'liberia' => 'LR',
		'libya' => 'LY',
		'liechtenstein' => 'LI',
		'lithuania' => 'LT',
		'luxembourg' => 'LU',
		'macau' => 'MO',
		'macedonia' => 'MK',
		'madagascar' => 'MG',
		'malawi' => 'MW',
		'malaysia' => 'MY',
		'maldives' => 'MV',
		'mali' => 'ML',
		'malta' => 'MT',
		'marshallislands' => 'MH',
		'martinique' => 'MQ',
		'mauritania' => 'MR',
		'mauritius' => 'MU',
		'mayotte' => 'YT',
		'mexico' => 'MX',
		'micronesia' => 'FM',
		'moldova' => 'MD',
		'monaco' => 'MC',
		'mongolia' => 'MN',
		'montserrat' => 'MS',
		'morocco' => 'MA',
		'mozambique' => 'MZ',
		'myanmar(burma)' => 'MM',
		'namibia' => 'NA',
		'nauru' => 'NR',
		'nepal' => 'NP',
		'netherlands' => 'NL',
		'netherlandsantilles' => 'AN',
		'newcaledonia' => 'NC',
		'newzealand' => 'NZ',
		'nicaragua' => 'NI',
		'niger' => 'NE',
		'nigeria' => 'NG',
		'niue' => 'NU',
		'norfolkisland' => 'NF',
		'northkorea' => 'KP',
		'northernmarianaislands' => 'MP',
		'norway' => 'NO',
		'oman' => 'OM',
		'pakistan' => 'PK',
		'palau' => 'PW',
		'panama' => 'PA',
		'papuanewguinea' => 'PG',
		'paraguay' => 'PY',
		'peru' => 'PE',
		'philippines' => 'PH',
		'pitcairn' => 'PN',
		'poland' => 'PL',
		'portugal' => 'PT',
		'puertorico' => 'PR',
		'qatar' => 'QA',
		'reunion' => 'RE',
		'romania' => 'RO',
		'russia' => 'RU',
		'rwanda' => 'RW',
		'sainthelena' => 'SH',
		'saintkittsandnevis' => 'KN',
		'saintlucia' => 'LC',
		'saintpierreandmiquelon' => 'PM',
		'saintvincentandthegrenadines' => 'VC',
		'sanmarino' => 'SM',
		'saotomeandprincipe' => 'ST',
		'saudiarabia' => 'SA',
		'senegal' => 'SN',
		'seychelles' => 'SC',
		'sierraleone' => 'SL',
		'singapore' => 'SG',
		'alovakia' => 'SK',
		'slovenia' => 'SI',
		'solomonislands' => 'SB',
		'somalia' => 'SO',
		'southafrica' => 'ZA',
		'southgeorgiaandsouthsandwichislands' => 'GS',
		'southkorea' => 'KR',
		'spain' => 'ES',
		'srilanka' => 'LK',
		'sudan' => 'SD',
		'suriname' => 'SR',
		'svalbardandjanmayen' => 'SJ',
		'swaziland' => 'SZ',
		'sweden' => 'SE',
		'switzerland' => 'CH',
		'syria' => 'SY',
		'taiwan' => 'TW',
		'tajikistan' => 'TJ',
		'tanzania' => 'TZ',
		'thailand' => 'TH',
		'togo' => 'TG',
		'tokelau' => 'TK',
		'tonga' => 'TO',
		'trinidadandtobago' => 'TT',
		'tunisia' => 'TN',
		'turkey' => 'TR',
		'turkmenistan' => 'TM',
		'turksandcaicosislands' => 'TC',
		'tuvalu' => 'TV',
		'uganda' => 'UG',
		'ukraine' => 'UA',
		'unitedarabemirates' => 'AE',
		'unitedkingdom' => 'UK',
		'unitedstates' => 'US',
		'unitedstatesminoroutlyingislands' => 'UM',
		'uruguay' => 'UY',
		'uzbekistan' => 'UZ',
		'vanuatu' => 'VU',
		'vaticancityholysee' => 'VA',
		'venezuela' => 'VE',
		'vietnam' => 'VN',
		'virginislandsbritish' => 'VG',
		'virginislandsus' => 'VI',
		'wallisandfutunaislands' => 'WF',
		'westernsahara' => 'EH',
		'westernsamoa' => 'WS',
		'yemen' => 'YE',
		'yugoslavia' => 'YU',
		'zambia' => 'ZM',
		'zimbabwe' => 'ZW'
	);

	protected static $calling_codes = array(
		array('country' => 'Abkhazia',                                        'country_code' => null,  'calling_code' => '7 840'   ),
		array('country' => 'Abkhazia',                                        'country_code' => null,  'calling_code' => '7 940'   ),
		array('country' => 'Afghanistan',                                     'country_code' => 'AF',  'calling_code' => '93'      ),
		array('country' => 'Albania',                                         'country_code' => 'AL',  'calling_code' => '355'     ),
		array('country' => 'Algeria',                                         'country_code' => 'DZ',  'calling_code' => '213'     ),
		array('country' => 'American Samoa',                                  'country_code' => 'AS',  'calling_code' => '1 684'   ),
		array('country' => 'Andorra',                                         'country_code' => 'AD',  'calling_code' => '376'     ),
		array('country' => 'Angola',                                          'country_code' => 'AO',  'calling_code' => '244'     ),
		array('country' => 'Anguilla',                                        'country_code' => 'AI',  'calling_code' => '1 264'   ),
		array('country' => 'Antigua and Barbuda',                             'country_code' => 'AG',  'calling_code' => '1 268'   ),
		array('country' => 'Argentina',                                       'country_code' => 'AR',  'calling_code' => '54'      ),
		array('country' => 'Armenia',                                         'country_code' => 'AM',  'calling_code' => '374'     ),
		array('country' => 'Aruba',                                           'country_code' => 'AW',  'calling_code' => '297'     ),
		array('country' => 'Ascension',                                       'country_code' => null,  'calling_code' => '247'     ),
		array('country' => 'Australia',                                       'country_code' => 'AU',  'calling_code' => '61'      ),
		array('country' => 'Australian External Territories',                 'country_code' => null,  'calling_code' => '672'     ),
		array('country' => 'Austria',                                         'country_code' => 'AT',  'calling_code' => '43'      ),
		array('country' => 'Azerbaijan',                                      'country_code' => 'AZ',  'calling_code' => '994'     ),
		array('country' => 'Bahamas',                                         'country_code' => 'BS',  'calling_code' => '1 242'   ),
		array('country' => 'Bahrain',                                         'country_code' => 'BH',  'calling_code' => '973'     ),
		array('country' => 'Bangladesh',                                      'country_code' => 'BD',  'calling_code' => '880'     ),
		array('country' => 'Barbados',                                        'country_code' => 'BB',  'calling_code' => '1 246'   ),
		array('country' => 'Barbuda',                                         'country_code' => null,  'calling_code' => '1 268'   ),
		array('country' => 'Belarus',                                         'country_code' => 'BY',  'calling_code' => '375'     ),
		array('country' => 'Belgium',                                         'country_code' => 'BE',  'calling_code' => '32'      ),
		array('country' => 'Belize',                                          'country_code' => 'BZ',  'calling_code' => '501'     ),
		array('country' => 'Benin',                                           'country_code' => 'BJ',  'calling_code' => '229'     ),
		array('country' => 'Bermuda',                                         'country_code' => 'BM',  'calling_code' => '1 441'   ),
		array('country' => 'Bhutan',                                          'country_code' => 'BT',  'calling_code' => '975'     ),
		array('country' => 'Bolivia',                                         'country_code' => 'BO',  'calling_code' => '591'     ),
		array('country' => 'Bosnia and Herzegovina',                          'country_code' => 'BA',  'calling_code' => '387'     ),
		array('country' => 'Botswana',                                        'country_code' => 'BW',  'calling_code' => '267'     ),
		array('country' => 'Brazil',                                          'country_code' => 'BR',  'calling_code' => '55'      ),
		array('country' => 'British Indian Ocean Territory',                  'country_code' => 'IO',  'calling_code' => '246'     ),
		array('country' => 'British Virgin Islands',                          'country_code' => null,  'calling_code' => '1 284'   ),
		array('country' => 'Brunei',                                          'country_code' => 'BN',  'calling_code' => '673'     ),
		array('country' => 'Bulgaria',                                        'country_code' => 'BG',  'calling_code' => '359'     ),
		array('country' => 'Burkina Faso',                                    'country_code' => 'BF',  'calling_code' => '226'     ),
		array('country' => 'Burundi',                                         'country_code' => 'BI',  'calling_code' => '257'     ),
		array('country' => 'Cambodia',                                        'country_code' => 'KH',  'calling_code' => '855'     ),
		array('country' => 'Cameroon',                                        'country_code' => 'CM',  'calling_code' => '237'     ),
		array('country' => 'Canada',                                          'country_code' => 'CA',  'calling_code' => '1'       ),
		array('country' => 'Cape Verde',                                      'country_code' => 'CV',  'calling_code' => '238'     ),
		array('country' => 'Cayman Islands',                                  'country_code' => 'KY',  'calling_code' => '345'     ),
		array('country' => 'Central African Republic',                        'country_code' => 'CF',  'calling_code' => '236'     ),
		array('country' => 'Chad',                                            'country_code' => 'TD',  'calling_code' => '235'     ),
		array('country' => 'Chile',                                           'country_code' => 'CL',  'calling_code' => '56'      ),
		array('country' => 'China',                                           'country_code' => 'CN',  'calling_code' => '86'      ),
		array('country' => 'Christmas Island',                                'country_code' => 'CX',  'calling_code' => '61'      ),
		array('country' => 'Cocos-Keeling Islands',                           'country_code' => null,  'calling_code' => '61'      ),
		array('country' => 'Colombia',                                        'country_code' => null,  'calling_code' => '57'      ),
		array('country' => 'Comoros',                                         'country_code' => 'KM',  'calling_code' => '269'     ),
		array('country' => 'Congo',                                           'country_code' => 'CG',  'calling_code' => '242'     ),
		array('country' => 'Congo, Dem. Rep. of (Zaire)',                     'country_code' => null,  'calling_code' => '243'     ),
		array('country' => 'Cook Islands',                                    'country_code' => 'CK',  'calling_code' => '682'     ),
		array('country' => 'Costa Rica',                                      'country_code' => 'CR',  'calling_code' => '506'     ),
		array('country' => 'Ivory Coast',                                     'country_code' => null,  'calling_code' => '225'     ),
		array('country' => 'Croatia',                                         'country_code' => null,  'calling_code' => '385'     ),
		array('country' => 'Cuba',                                            'country_code' => 'CU',  'calling_code' => '53'      ),
		array('country' => 'Curacao',                                         'country_code' => null,  'calling_code' => '599'     ),
		array('country' => 'Cyprus',                                          'country_code' => 'CY',  'calling_code' => '537'     ),
		array('country' => 'Czech Republic',                                  'country_code' => 'CZ',  'calling_code' => '420'     ),
		array('country' => 'Denmark',                                         'country_code' => 'DK',  'calling_code' => '45'      ),
		array('country' => 'Diego Garcia',                                    'country_code' => null,  'calling_code' => '246'     ),
		array('country' => 'Djibouti',                                        'country_code' => 'DJ',  'calling_code' => '253'     ),
		array('country' => 'Dominica',                                        'country_code' => 'DM',  'calling_code' => '1 767'   ),
		array('country' => 'Dominican Republic',                              'country_code' => 'DO',  'calling_code' => '1 809'   ),
		array('country' => 'Dominican Republic',                              'country_code' => 'DO',  'calling_code' => '1 829'   ),
		array('country' => 'Dominican Republic',                              'country_code' => 'DO',  'calling_code' => '1 849'   ),
		array('country' => 'East Timor',                                      'country_code' => 'TP',  'calling_code' => '670'     ),
		array('country' => 'Easter Island',                                   'country_code' => null,  'calling_code' => '56'      ),
		array('country' => 'Ecuador',                                         'country_code' => 'EC',  'calling_code' => '593'     ),
		array('country' => 'Egypt',                                           'country_code' => 'EG',  'calling_code' => '20'      ),
		array('country' => 'El Salvador',                                     'country_code' => 'SV',  'calling_code' => '503'     ),
		array('country' => 'Equatorial Guinea',                               'country_code' => 'GQ',  'calling_code' => '240'     ),
		array('country' => 'Eritrea',                                         'country_code' => 'ER',  'calling_code' => '291'     ),
		array('country' => 'Estonia',                                         'country_code' => 'EE',  'calling_code' => '372'     ),
		array('country' => 'Ethiopia',                                        'country_code' => 'ET',  'calling_code' => '251'     ),
		array('country' => 'Falkland Islands',                                'country_code' => null,  'calling_code' => '500'     ),
		array('country' => 'Faroe Islands',                                   'country_code' => 'FO',  'calling_code' => '298'     ),
		array('country' => 'Fiji',                                            'country_code' => 'FJ',  'calling_code' => '679'     ),
		array('country' => 'Finland',                                         'country_code' => 'FI',  'calling_code' => '358'     ),
		array('country' => 'France',                                          'country_code' => 'FR',  'calling_code' => '33'      ),
		array('country' => 'French Antilles',                                 'country_code' => null,  'calling_code' => '596'     ),
		array('country' => 'French Guiana',                                   'country_code' => null,  'calling_code' => '594'     ),
		array('country' => 'French Polynesia',                                'country_code' => 'PF',  'calling_code' => '689'     ),
		array('country' => 'Gabon',                                           'country_code' => 'GA',  'calling_code' => '241'     ),
		array('country' => 'Gambia',                                          'country_code' => 'GM',  'calling_code' => '220'     ),
		array('country' => 'Georgia',                                         'country_code' => 'GE',  'calling_code' => '995'     ),
		array('country' => 'Germany',                                         'country_code' => 'DE',  'calling_code' => '49'      ),
		array('country' => 'Ghana',                                           'country_code' => 'GH',  'calling_code' => '233'     ),
		array('country' => 'Gibraltar',                                       'country_code' => 'GI',  'calling_code' => '350'     ),
		array('country' => 'Greece',                                          'country_code' => 'GR',  'calling_code' => '30'      ),
		array('country' => 'Greenland',                                       'country_code' => 'GL',  'calling_code' => '299'     ),
		array('country' => 'Grenada',                                         'country_code' => 'GD',  'calling_code' => '1 473'   ),
		array('country' => 'Guadeloupe',                                      'country_code' => 'GP',  'calling_code' => '590'     ),
		array('country' => 'Guam',                                            'country_code' => 'GU',  'calling_code' => '1 671'   ),
		array('country' => 'Guatemala',                                       'country_code' => 'GT',  'calling_code' => '502'     ),
		array('country' => 'Guinea',                                          'country_code' => 'GN',  'calling_code' => '224'     ),
		array('country' => 'Guinea-Bissau',                                   'country_code' => null,  'calling_code' => '245'     ),
		array('country' => 'Guyana',                                          'country_code' => 'GY',  'calling_code' => '595'     ),
		array('country' => 'Haiti',                                           'country_code' => 'HT',  'calling_code' => '509'     ),
		array('country' => 'Honduras',                                        'country_code' => 'HN',  'calling_code' => '504'     ),
		array('country' => 'Hong Kong SAR China',                             'country_code' => null,  'calling_code' => '852'     ),
		array('country' => 'Hungary',                                         'country_code' => 'HU',  'calling_code' => '36'      ),
		array('country' => 'Iceland',                                         'country_code' => 'IS',  'calling_code' => '354'     ),
		array('country' => 'India',                                           'country_code' => 'IN',  'calling_code' => '91'      ),
		array('country' => 'Indonesia',                                       'country_code' => 'ID',  'calling_code' => '62'      ),
		array('country' => 'Iran',                                            'country_code' => 'IR',  'calling_code' => '98'      ),
		array('country' => 'Iraq',                                            'country_code' => 'IQ',  'calling_code' => '964'     ),
		array('country' => 'Ireland',                                         'country_code' => 'IE',  'calling_code' => '353'     ),
		array('country' => 'Israel',                                          'country_code' => 'IL',  'calling_code' => '972'     ),
		array('country' => 'Italy',                                           'country_code' => 'IT',  'calling_code' => '39'      ),
		array('country' => 'Jamaica',                                         'country_code' => 'JM',  'calling_code' => '1 876'   ),
		array('country' => 'Japan',                                           'country_code' => 'JP',  'calling_code' => '81'      ),
		array('country' => 'Jordan',                                          'country_code' => 'JO',  'calling_code' => '962'     ),
		array('country' => 'Kazakhstan',                                      'country_code' => 'KZ',  'calling_code' => '7 7'     ),
		array('country' => 'Kenya',                                           'country_code' => 'KE',  'calling_code' => '254'     ),
		array('country' => 'Kiribati',                                        'country_code' => 'KI',  'calling_code' => '686'     ),
		array('country' => 'North Korea',                                     'country_code' => 'KP',  'calling_code' => '850'     ),
		array('country' => 'South Korea',                                     'country_code' => 'KR',  'calling_code' => '82'      ),
		array('country' => 'Kuwait',                                          'country_code' => 'KW',  'calling_code' => '965'     ),
		array('country' => 'Kyrgyzstan',                                      'country_code' => 'KG',  'calling_code' => '996'     ),
		array('country' => 'Laos',                                            'country_code' => 'LA',  'calling_code' => '856'     ),
		array('country' => 'Latvia',                                          'country_code' => 'LV',  'calling_code' => '371'     ),
		array('country' => 'Lebanon',                                         'country_code' => 'LB',  'calling_code' => '961'     ),
		array('country' => 'Lesotho',                                         'country_code' => 'LS',  'calling_code' => '266'     ),
		array('country' => 'Liberia',                                         'country_code' => 'LR',  'calling_code' => '231'     ),
		array('country' => 'Libya',                                           'country_code' => 'LY',  'calling_code' => '218'     ),
		array('country' => 'Liechtenstein',                                   'country_code' => 'LI',  'calling_code' => '423'     ),
		array('country' => 'Lithuania',                                       'country_code' => 'LT',  'calling_code' => '370'     ),
		array('country' => 'Luxembourg',                                      'country_code' => 'LU',  'calling_code' => '352'     ),
		array('country' => 'Macau SAR China',                                 'country_code' => null,  'calling_code' => '853'     ),
		array('country' => 'Macedonia',                                       'country_code' => 'MK',  'calling_code' => '389'     ),
		array('country' => 'Madagascar',                                      'country_code' => 'MG',  'calling_code' => '261'     ),
		array('country' => 'Malawi',                                          'country_code' => 'MW',  'calling_code' => '265'     ),
		array('country' => 'Malaysia',                                        'country_code' => 'MY',  'calling_code' => '60'      ),
		array('country' => 'Maldives',                                        'country_code' => 'MV',  'calling_code' => '960'     ),
		array('country' => 'Mali',                                            'country_code' => 'ML',  'calling_code' => '223'     ),
		array('country' => 'Malta',                                           'country_code' => 'MT',  'calling_code' => '356'     ),
		array('country' => 'Marshall Islands',                                'country_code' => 'MH',  'calling_code' => '692'     ),
		array('country' => 'Martinique',                                      'country_code' => 'MQ',  'calling_code' => '596'     ),
		array('country' => 'Mauritania',                                      'country_code' => 'MR',  'calling_code' => '222'     ),
		array('country' => 'Mauritius',                                       'country_code' => 'MU',  'calling_code' => '230'     ),
		array('country' => 'Mayotte',                                         'country_code' => 'YT',  'calling_code' => '262'     ),
		array('country' => 'Mexico',                                          'country_code' => 'MX',  'calling_code' => '52'      ),
		array('country' => 'Micronesia',                                      'country_code' => 'FM',  'calling_code' => '691'     ),
		array('country' => 'Midway Island',                                   'country_code' => null,  'calling_code' => '1 808'   ),
		array('country' => 'Micronesia',                                      'country_code' => 'FM',  'calling_code' => '691'     ),
		array('country' => 'Moldova',                                         'country_code' => 'MD',  'calling_code' => '373'     ),
		array('country' => 'Monaco',                                          'country_code' => 'MC',  'calling_code' => '377'     ),
		array('country' => 'Mongolia',                                        'country_code' => 'MN',  'calling_code' => '976'     ),
		array('country' => 'Montenegro',                                      'country_code' => null,  'calling_code' => '382'     ),
		array('country' => 'Montserrat',                                      'country_code' => 'MS',  'calling_code' => '1664'    ),
		array('country' => 'Morocco',                                         'country_code' => 'MA',  'calling_code' => '212'     ),
		array('country' => 'Myanmar',                                         'country_code' => null,  'calling_code' => '95'      ),
		array('country' => 'Namibia',                                         'country_code' => 'NA',  'calling_code' => '264'     ),
		array('country' => 'Nauru',                                           'country_code' => 'NR',  'calling_code' => '674'     ),
		array('country' => 'Nepal',                                           'country_code' => 'NP',  'calling_code' => '977'     ),
		array('country' => 'Netherlands',                                     'country_code' => 'NL',  'calling_code' => '31'      ),
		array('country' => 'Netherlands Antilles',                            'country_code' => 'AN',  'calling_code' => '599'     ),
		array('country' => 'Nevis',                                           'country_code' => null,  'calling_code' => '1 869'   ),
		array('country' => 'New Caledonia',                                   'country_code' => 'NC',  'calling_code' => '687'     ),
		array('country' => 'New Zealand',                                     'country_code' => 'NZ',  'calling_code' => '64'      ),
		array('country' => 'Nicaragua',                                       'country_code' => 'NI',  'calling_code' => '505'     ),
		array('country' => 'Niger',                                           'country_code' => 'NE',  'calling_code' => '227'     ),
		array('country' => 'Nigeria',                                         'country_code' => 'NG',  'calling_code' => '234'     ),
		array('country' => 'Niue',                                            'country_code' => 'NU',  'calling_code' => '683'     ),
		array('country' => 'Norfolk Island',                                  'country_code' => 'NF',  'calling_code' => '672'     ),
		array('country' => 'Northern Mariana Islands',                        'country_code' => 'MP',  'calling_code' => '1 670'   ),
		array('country' => 'Norway',                                          'country_code' => 'NO',  'calling_code' => '47'      ),
		array('country' => 'Oman',                                            'country_code' => 'OM',  'calling_code' => '968'     ),
		array('country' => 'Pakistan',                                        'country_code' => 'PK',  'calling_code' => '92'      ),
		array('country' => 'Palau',                                           'country_code' => 'PW',  'calling_code' => '680'     ),
		array('country' => 'Palestinian Territory',                           'country_code' => null,  'calling_code' => '970'     ),
		array('country' => 'Panama',                                          'country_code' => 'PA',  'calling_code' => '507'     ),
		array('country' => 'Papua New Guinea',                                'country_code' => 'PG',  'calling_code' => '675'     ),
		array('country' => 'Paraguay',                                        'country_code' => 'PY',  'calling_code' => '595'     ),
		array('country' => 'Peru',                                            'country_code' => 'PE',  'calling_code' => '51'      ),
		array('country' => 'Philippines',                                     'country_code' => 'PH',  'calling_code' => '63'      ),
		array('country' => 'Poland',                                          'country_code' => 'PL',  'calling_code' => '48'      ),
		array('country' => 'Portugal',                                        'country_code' => 'PT',  'calling_code' => '351'     ),
		array('country' => 'Puerto Rico',                                     'country_code' => 'PR',  'calling_code' => '1 787'   ),
		array('country' => 'Puerto Rico',                                     'country_code' => 'PR',  'calling_code' => '1 939'   ),
		array('country' => 'Qatar',                                           'country_code' => 'QA',  'calling_code' => '974'     ),
		array('country' => 'Reunion',                                         'country_code' => 'RE',  'calling_code' => '262'     ),
		array('country' => 'Romania',                                         'country_code' => 'RO',  'calling_code' => '40'      ),
		array('country' => 'Russia',                                          'country_code' => 'RU',  'calling_code' => '7'       ),
		array('country' => 'Rwanda',                                          'country_code' => 'RW',  'calling_code' => '250'     ),
		array('country' => 'Samoa',                                           'country_code' => null,  'calling_code' => '685'     ),
		array('country' => 'San Marino',                                      'country_code' => 'SM',  'calling_code' => '378'     ),
		array('country' => 'Saudi Arabia',                                    'country_code' => 'SA',  'calling_code' => '966'     ),
		array('country' => 'Senegal',                                         'country_code' => 'SN',  'calling_code' => '221'     ),
		array('country' => 'Serbia',                                          'country_code' => null,  'calling_code' => '381'     ),
		array('country' => 'Seychelles',                                      'country_code' => 'SC',  'calling_code' => '248'     ),
		array('country' => 'Sierra Leone',                                    'country_code' => 'SL',  'calling_code' => '232'     ),
		array('country' => 'Singapore',                                       'country_code' => 'SG',  'calling_code' => '65'      ),
		array('country' => 'Slovakia',                                        'country_code' => null,  'calling_code' => '421'     ),
		array('country' => 'Slovenia',                                        'country_code' => 'SI',  'calling_code' => '386'     ),
		array('country' => 'Solomon Islands',                                 'country_code' => 'SB',  'calling_code' => '677'     ),
		array('country' => 'South Africa',                                    'country_code' => 'ZA',  'calling_code' => '27'      ),
		array('country' => 'South Georgia and the South Sandwich Islands',    'country_code' => null,  'calling_code' => '500'     ),
		array('country' => 'Spain',                                           'country_code' => 'ES',  'calling_code' => '34'      ),
		array('country' => 'Sri Lanka',                                       'country_code' => 'LK',  'calling_code' => '94'      ),
		array('country' => 'Sudan',                                           'country_code' => 'SD',  'calling_code' => '249'     ),
		array('country' => 'Suriname',                                        'country_code' => 'SR',  'calling_code' => '597'     ),
		array('country' => 'Swaziland',                                       'country_code' => 'SZ',  'calling_code' => '268'     ),
		array('country' => 'Sweden',                                          'country_code' => 'SE',  'calling_code' => '46'      ),
		array('country' => 'Switzerland',                                     'country_code' => 'CH',  'calling_code' => '41'      ),
		array('country' => 'Syria',                                           'country_code' => 'SY',  'calling_code' => '963'     ),
		array('country' => 'Taiwan',                                          'country_code' => 'TW',  'calling_code' => '886'     ),
		array('country' => 'Tajikistan',                                      'country_code' => 'TJ',  'calling_code' => '992'     ),
		array('country' => 'Tanzania',                                        'country_code' => 'TZ',  'calling_code' => '255'     ),
		array('country' => 'Thailand',                                        'country_code' => 'TH',  'calling_code' => '66'      ),
		array('country' => 'Timor Leste',                                     'country_code' => null,  'calling_code' => '670'     ),
		array('country' => 'Togo',                                            'country_code' => 'TG',  'calling_code' => '228'     ),
		array('country' => 'Tokelau',                                         'country_code' => 'TK',  'calling_code' => '690'     ),
		array('country' => 'Tonga',                                           'country_code' => 'TO',  'calling_code' => '676'     ),
		array('country' => 'Trinidad and Tobago',                             'country_code' => 'TT',  'calling_code' => '1 868'   ),
		array('country' => 'Tunisia',                                         'country_code' => 'TN',  'calling_code' => '216'     ),
		array('country' => 'Turkey',                                          'country_code' => 'TR',  'calling_code' => '90'      ),
		array('country' => 'Turkmenistan',                                    'country_code' => 'TM',  'calling_code' => '993'     ),
		array('country' => 'Turks and Caicos Islands',                        'country_code' => 'TC',  'calling_code' => '1 649'   ),
		array('country' => 'Tuvalu',                                          'country_code' => 'TV',  'calling_code' => '688'     ),
		array('country' => 'Uganda',                                          'country_code' => 'UG',  'calling_code' => '256'     ),
		array('country' => 'Ukraine',                                         'country_code' => 'UA',  'calling_code' => '380'     ),
		array('country' => 'United Arab Emirates',                            'country_code' => 'AE',  'calling_code' => '971'     ),
		array('country' => 'United Kingdom',                                  'country_code' => 'UK',  'calling_code' => '44'      ),
		array('country' => 'United States',                                   'country_code' => 'US',  'calling_code' => '1'       ),
		array('country' => 'Uruguay',                                         'country_code' => 'UY',  'calling_code' => '598'     ),
		array('country' => 'U.S. Virgin Islands',                             'country_code' => null,  'calling_code' => '1 340'   ),
		array('country' => 'Uzbekistan',                                      'country_code' => 'UZ',  'calling_code' => '998'     ),
		array('country' => 'Vanuatu',                                         'country_code' => 'VU',  'calling_code' => '678'     ),
		array('country' => 'Venezuela',                                       'country_code' => 'VE',  'calling_code' => '58'      ),
		array('country' => 'Vietnam',                                         'country_code' => 'VN',  'calling_code' => '84'      ),
		array('country' => 'Wake Island',                                     'country_code' => null,  'calling_code' => '1 808'   ),
		array('country' => 'Wallis and Futuna',                               'country_code' => null,  'calling_code' => '681'     ),
		array('country' => 'Yemen',                                           'country_code' => 'YE',  'calling_code' => '967'     ),
		array('country' => 'Zambia',                                          'country_code' => 'ZM',  'calling_code' => '260'     ),
		array('country' => 'Zanzibar',                                        'country_code' => null,  'calling_code' => '255'     ),
		array('country' => 'Zimbabwe',                                        'country_code' => 'ZW',  'calling_code' => '263'     ),
	);



	/**
	 * Check if a country code is in the list.
	 *
	 * @param  string  $code  The code to check
	 * @return bool
	 */
	public static function isCountry($code)
	{
		return isset(self::$code_to_name[$code]);
	}


	/**
	 * Get an array of simple country codes.
	 *
	 * @return array
	 */
	public static function getCountryCodes()
	{
		return array_keys(self::$code_to_name);
	}


	/**
	 * Get an array of code=>name of countries.
	 *
	 * @return array
	 */
	public static function getCountryArray()
	{
		return self::$code_to_name;
	}


	/*
	 * Get an array of country names
	 *
	 * @return string[]
	 */
	public static function getCountryNames()
	{
		return array_values(self::$code_to_name);
	}


	/**
	 * Get an array of code=>continent of countries.
	 *
	 * @return array
	 */
	public static function getContientArray()
	{
		return self::$code_to_continent;
	}


	/**
	 * Ge the 3-letter continent code for a specified country.
	 *
	 * @param   string  $code  The two letter country code
	 * @return  string
	 */
	public static function getContinentFromCode($code)
	{
		if (!isset(self::$code_to_continent[$code])) {
			return null;
		}

		return self::$code_to_continent[$code];
	}


	/**
	 * Get the country name from a code. Returns null if no country exists.
	 *
	 * @param   string  $code  The two letter country code
	 * @return  string
	 */
	public static function getCountryFromCode($code)
	{
		if (strlen($code) != 2) {
			return $code;
		}

		$code = strtoupper($code);

		if (!isset(self::$code_to_name[$code])) {
			return null;
		}

		return self::$code_to_name[$code];
	}


	/**
	 * Tries to get a country code from a country name. This isn't very
	 * reliable since some countries can have different ways of expressing
	 * their name.
	 *
	 * Returns false when a country couldnt be found.
	 *
	 * @param   string  $country  The country name
	 * @return  string
	 */
	public static function getCodeFromCountry($country)
	{
		$country = preg_replace('#[^a-z]#', '', strtolower($country));

		if (!isset(self::$name_to_code[$country])) {
			return null;
		}

		return self::$name_to_code[$country];
	}


	/**
	 * Get an array of country codes that use the Euro.
	 *
	 * @return array
	 */
	public static function getEuroCountries()
	{
		return array(
			'AT', // Austria
			'BE', // Belgium
			'CY', // Cyprus
			'FI', // Finland
			'FR', // France
			'DE', // Germany
			'GR', // Greece
			'IE', // Ireland
			'IT', // Italy
			'LU', // Luxembourg
			'MT', // Malta
			'NL', // Netherlands
			'PT', // Portugal
			'SK', // Slovakia
			'SI', // Slovenia
			'ES', // Spain
		);
	}


	/**
	 * Get country calling code options suitable for a select box.
	 *
	 * @return array
	 */
	public static function getCallingCodeOptions($add_common = true)
	{
		$options = array();

		// Add common countries to the top
		if ($add_common) {
			if ($add_common === true) {
				// Default common options
				$common = array(
					'Canada', 'China', 'France',
					'Germany', 'India', 'Japan',
					'Russia', 'United Kingdom', 'United States'
				);
			} else {
				$common = (array)$add_common;
			}

			foreach ($common as $name) {
				foreach (self::$calling_codes as $info) {
					if ($info['country'] == $name) {
						$options[] = "{$info['country']} (+{$info['calling_code']})";
						break;
					}
				}
			}

			$options[] = '---';
		}

		foreach (self::$calling_codes as $info) {
			$options[] = "{$info['country']} (+{$info['calling_code']})";
		}

		return $options;
	}


	/**
	 * Check if a country uses the euro.
	 *
	 * @param   string  $country_code  The coutry to check
	 * @return  bool
	 */
	public static function isEuroCountry($country_code)
	{
		return in_array(strtoupper($country_code), self::getEuroCountries());
	}
}

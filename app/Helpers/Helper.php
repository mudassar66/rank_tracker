<?php

namespace App\Helpers;

class Helper
{

    public static function getStopWords(){
        return ["a", "about", "above", "after", "again", "against", "ain", "all", "am", "an", "and", "any", "are", "aren", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can", "couldn", "couldn't", "d", "did", "didn", "didn't", "do", "does", "doesn", "doesn't", "doing", "don", "don't", "down", "during", "each", "few", "for", "from", "further", "had", "hadn", "hadn't", "has", "hasn", "hasn't", "have", "haven", "haven't", "having", "he", "her", "here", "hers", "herself", "him", "himself", "his", "how", "i", "if", "in", "into", "is", "isn", "isn't", "it", "it's", "its", "itself", "just", "ll", "m", "ma", "me", "mightn", "mightn't", "more", "most", "mustn", "mustn't", "my", "myself", "needn", "needn't", "no", "nor", "not", "now", "o", "of", "off", "on", "once", "only", "or", "other", "our", "ours", "ourselves", "out", "over", "own", "re", "s", "same", "shan", "shan't", "she", "she's", "should", "should've", "shouldn", "shouldn't", "so", "some", "such", "t", "than", "that", "that'll", "the", "their", "theirs", "them", "themselves", "then", "there", "these", "they", "this", "those", "through", "to", "too", "under", "until", "up", "ve", "very", "was", "wasn", "wasn't", "we", "were", "weren", "weren't", "what", "when", "where", "which", "while", "who", "whom", "why", "will", "with", "won", "won't", "wouldn", "wouldn't", "y", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "could", "he'd", "he'll", "he's", "here's", "how's", "i'd", "i'll", "i'm", "i've", "let's", "ought", "she'd", "she'll", "that's", "there's", "they'd", "they'll", "they're", "they've", "we'd", "we'll", "we're", "we've", "what's", "when's", "where's", "who's", "why's", "would"];
    }
    public static function getCountries(){
        return array(
            'AF'=>'AFGHANISTAN',
            'AL'=>'ALBANIA',
            'DZ'=>'ALGERIA',
            'AS'=>'AMERICAN SAMOA',
            'AD'=>'ANDORRA',
            'AO'=>'ANGOLA',
            'AI'=>'ANGUILLA',
            'AQ'=>'ANTARCTICA',
            'AG'=>'ANTIGUA AND BARBUDA',
            'AR'=>'ARGENTINA',
            'AM'=>'ARMENIA',
            'AW'=>'ARUBA',
            'AU'=>'AUSTRALIA',
            'AT'=>'AUSTRIA',
            'AZ'=>'AZERBAIJAN',
            'BS'=>'BAHAMAS',
            'BH'=>'BAHRAIN',
            'BD'=>'BANGLADESH',
            'BB'=>'BARBADOS',
            'BY'=>'BELARUS',
            'BE'=>'BELGIUM',
            'BZ'=>'BELIZE',
            'BJ'=>'BENIN',
            'BM'=>'BERMUDA',
            'BT'=>'BHUTAN',
            'BO'=>'BOLIVIA',
            'BA'=>'BOSNIA AND HERZEGOVINA',
            'BW'=>'BOTSWANA',
            'BV'=>'BOUVET ISLAND',
            'BR'=>'BRAZIL',
            'IO'=>'BRITISH INDIAN OCEAN TERRITORY',
            'BN'=>'BRUNEI DARUSSALAM',
            'BG'=>'BULGARIA',
            'BF'=>'BURKINA FASO',
            'BI'=>'BURUNDI',
            'KH'=>'CAMBODIA',
            'CM'=>'CAMEROON',
            'CA'=>'CANADA',
            'CV'=>'CAPE VERDE',
            'KY'=>'CAYMAN ISLANDS',
            'CF'=>'CENTRAL AFRICAN REPUBLIC',
            'TD'=>'CHAD',
            'CL'=>'CHILE',
            'CN'=>'CHINA',
            'CX'=>'CHRISTMAS ISLAND',
            'CC'=>'COCOS (KEELING) ISLANDS',
            'CO'=>'COLOMBIA',
            'KM'=>'COMOROS',
            'CG'=>'CONGO',
            'CD'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
            'CK'=>'COOK ISLANDS',
            'CR'=>'COSTA RICA',
            'CI'=>'COTE D IVOIRE',
            'HR'=>'CROATIA',
            'CU'=>'CUBA',
            'CY'=>'CYPRUS',
            'CZ'=>'CZECH REPUBLIC',
            'DK'=>'DENMARK',
            'DJ'=>'DJIBOUTI',
            'DM'=>'DOMINICA',
            'DO'=>'DOMINICAN REPUBLIC',
            'TP'=>'EAST TIMOR',
            'EC'=>'ECUADOR',
            'EG'=>'EGYPT',
            'SV'=>'EL SALVADOR',
            'GQ'=>'EQUATORIAL GUINEA',
            'ER'=>'ERITREA',
            'EE'=>'ESTONIA',
            'ET'=>'ETHIOPIA',
            'FK'=>'FALKLAND ISLANDS (MALVINAS)',
            'FO'=>'FAROE ISLANDS',
            'FJ'=>'FIJI',
            'FI'=>'FINLAND',
            'FR'=>'FRANCE',
            'GF'=>'FRENCH GUIANA',
            'PF'=>'FRENCH POLYNESIA',
            'TF'=>'FRENCH SOUTHERN TERRITORIES',
            'GA'=>'GABON',
            'GM'=>'GAMBIA',
            'GE'=>'GEORGIA',
            'DE'=>'GERMANY',
            'GH'=>'GHANA',
            'GI'=>'GIBRALTAR',
            'GR'=>'GREECE',
            'GL'=>'GREENLAND',
            'GD'=>'GRENADA',
            'GP'=>'GUADELOUPE',
            'GU'=>'GUAM',
            'GT'=>'GUATEMALA',
            'GN'=>'GUINEA',
            'GW'=>'GUINEA-BISSAU',
            'GY'=>'GUYANA',
            'HT'=>'HAITI',
            'HM'=>'HEARD ISLAND AND MCDONALD ISLANDS',
            'VA'=>'HOLY SEE (VATICAN CITY STATE)',
            'HN'=>'HONDURAS',
            'HK'=>'HONG KONG',
            'HU'=>'HUNGARY',
            'IS'=>'ICELAND',
            'IN'=>'INDIA',
            'ID'=>'INDONESIA',
            'IR'=>'IRAN, ISLAMIC REPUBLIC OF',
            'IQ'=>'IRAQ',
            'IE'=>'IRELAND',
            'IL'=>'ISRAEL',
            'IT'=>'ITALY',
            'JM'=>'JAMAICA',
            'JP'=>'JAPAN',
            'JO'=>'JORDAN',
            'KZ'=>'KAZAKSTAN',
            'KE'=>'KENYA',
            'KI'=>'KIRIBATI',
            'KP'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
            'KR'=>'KOREA REPUBLIC OF',
            'KW'=>'KUWAIT',
            'KG'=>'KYRGYZSTAN',
            'LA'=>'LAO PEOPLES DEMOCRATIC REPUBLIC',
            'LV'=>'LATVIA',
            'LB'=>'LEBANON',
            'LS'=>'LESOTHO',
            'LR'=>'LIBERIA',
            'LY'=>'LIBYAN ARAB JAMAHIRIYA',
            'LI'=>'LIECHTENSTEIN',
            'LT'=>'LITHUANIA',
            'LU'=>'LUXEMBOURG',
            'MO'=>'MACAU',
            'MK'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
            'MG'=>'MADAGASCAR',
            'MW'=>'MALAWI',
            'MY'=>'MALAYSIA',
            'MV'=>'MALDIVES',
            'ML'=>'MALI',
            'MT'=>'MALTA',
            'MH'=>'MARSHALL ISLANDS',
            'MQ'=>'MARTINIQUE',
            'MR'=>'MAURITANIA',
            'MU'=>'MAURITIUS',
            'YT'=>'MAYOTTE',
            'MX'=>'MEXICO',
            'FM'=>'MICRONESIA, FEDERATED STATES OF',
            'MD'=>'MOLDOVA, REPUBLIC OF',
            'MC'=>'MONACO',
            'MN'=>'MONGOLIA',
            'MS'=>'MONTSERRAT',
            'MA'=>'MOROCCO',
            'MZ'=>'MOZAMBIQUE',
            'MM'=>'MYANMAR',
            'NA'=>'NAMIBIA',
            'NR'=>'NAURU',
            'NP'=>'NEPAL',
            'NL'=>'NETHERLANDS',
            'AN'=>'NETHERLANDS ANTILLES',
            'NC'=>'NEW CALEDONIA',
            'NZ'=>'NEW ZEALAND',
            'NI'=>'NICARAGUA',
            'NE'=>'NIGER',
            'NG'=>'NIGERIA',
            'NU'=>'NIUE',
            'NF'=>'NORFOLK ISLAND',
            'MP'=>'NORTHERN MARIANA ISLANDS',
            'NO'=>'NORWAY',
            'OM'=>'OMAN',
            'PK'=>'PAKISTAN',
            'PW'=>'PALAU',
            'PS'=>'PALESTINIAN TERRITORY, OCCUPIED',
            'PA'=>'PANAMA',
            'PG'=>'PAPUA NEW GUINEA',
            'PY'=>'PARAGUAY',
            'PE'=>'PERU',
            'PH'=>'PHILIPPINES',
            'PN'=>'PITCAIRN',
            'PL'=>'POLAND',
            'PT'=>'PORTUGAL',
            'PR'=>'PUERTO RICO',
            'QA'=>'QATAR',
            'RE'=>'REUNION',
            'RO'=>'ROMANIA',
            'RU'=>'RUSSIAN FEDERATION',
            'RW'=>'RWANDA',
            'SH'=>'SAINT HELENA',
            'KN'=>'SAINT KITTS AND NEVIS',
            'LC'=>'SAINT LUCIA',
            'PM'=>'SAINT PIERRE AND MIQUELON',
            'VC'=>'SAINT VINCENT AND THE GRENADINES',
            'WS'=>'SAMOA',
            'SM'=>'SAN MARINO',
            'ST'=>'SAO TOME AND PRINCIPE',
            'SA'=>'SAUDI ARABIA',
            'SN'=>'SENEGAL',
            'SC'=>'SEYCHELLES',
            'SL'=>'SIERRA LEONE',
            'SG'=>'SINGAPORE',
            'SK'=>'SLOVAKIA',
            'SI'=>'SLOVENIA',
            'SB'=>'SOLOMON ISLANDS',
            'SO'=>'SOMALIA',
            'ZA'=>'SOUTH AFRICA',
            'GS'=>'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
            'ES'=>'SPAIN',
            'LK'=>'SRI LANKA',
            'SD'=>'SUDAN',
            'SR'=>'SURINAME',
            'SJ'=>'SVALBARD AND JAN MAYEN',
            'SZ'=>'SWAZILAND',
            'SE'=>'SWEDEN',
            'CH'=>'SWITZERLAND',
            'SY'=>'SYRIAN ARAB REPUBLIC',
            'TW'=>'TAIWAN, PROVINCE OF CHINA',
            'TJ'=>'TAJIKISTAN',
            'TZ'=>'TANZANIA, UNITED REPUBLIC OF',
            'TH'=>'THAILAND',
            'TG'=>'TOGO',
            'TK'=>'TOKELAU',
            'TO'=>'TONGA',
            'TT'=>'TRINIDAD AND TOBAGO',
            'TN'=>'TUNISIA',
            'TR'=>'TURKEY',
            'TM'=>'TURKMENISTAN',
            'TC'=>'TURKS AND CAICOS ISLANDS',
            'TV'=>'TUVALU',
            'UG'=>'UGANDA',
            'UA'=>'UKRAINE',
            'AE'=>'UNITED ARAB EMIRATES',
            'GB'=>'UNITED KINGDOM',
            'US'=>'UNITED STATES',
            'UM'=>'UNITED STATES MINOR OUTLYING ISLANDS',
            'UY'=>'URUGUAY',
            'UZ'=>'UZBEKISTAN',
            'VU'=>'VANUATU',
            'VE'=>'VENEZUELA',
            'VN'=>'VIET NAM',
            'VG'=>'VIRGIN ISLANDS, BRITISH',
            'VI'=>'VIRGIN ISLANDS, U.S.',
            'WF'=>'WALLIS AND FUTUNA',
            'EH'=>'WESTERN SAHARA',
            'YE'=>'YEMEN',
            'YU'=>'YUGOSLAVIA',
            'ZM'=>'ZAMBIA',
            'ZW'=>'ZIMBABWE',
        );
    }

    public static function getDevices(){
        return [
            \App\Models\Search::DESKTOP => 'Desktop',
            \App\Models\Search::MOBILE => 'Mobile',
        ];

    }


    /**
     * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
     * array containing the HTTP server response header fields and content.
     */
    public static function get_web_page( $url )
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

}

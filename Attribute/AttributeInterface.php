<?php


namespace SkyDiablo\SkyRadius\Attribute;

interface AttributeInterface
{

    // Password
    const ATTR_USER_PASSWORD = 2; // https://tools.ietf.org/html/rfc2865#section-5.2
    const ATTR_CHAP_PASSWORD = 3; // https://tools.ietf.org/html/rfc2865#section-5.3

    // IP-Address
    const ATTR_NAS_IP_ADDRESS = 4; // https://tools.ietf.org/html/rfc2865#section-5.4
    const ATTR_FRAMED_IP_ADDRESS = 8; // https://tools.ietf.org/html/rfc2865#section-5.8
    const ATTR_FRAMED_IP_NETMASK = 9; // https://tools.ietf.org/html/rfc2865#section-5.9
    const ATTR_LOGIN_IP_HOST = 14; // https://tools.ietf.org/html/rfc2865#section-5.14

    // Unsigned 32bit Integer/Long
    const ATTR_NAS_PORT = 5; // https://tools.ietf.org/html/rfc2865#section-5.5
    const ATTR_SERVICE_TYPE = 6; // https://tools.ietf.org/html/rfc2865#section-5.6
    const ATTR_FRAMED_PROTOCOL = 7; // https://tools.ietf.org/html/rfc2865#section-5.7
    const ATTR_FRAMED_ROUTING = 10; // https://tools.ietf.org/html/rfc2865#section-5.10
    const ATTR_FRAMED_MTU = 12; // https://tools.ietf.org/html/rfc2865#section-5.12
    const ATTR_FRAMED_COMPRESSION = 13; // https://tools.ietf.org/html/rfc2865#section-5.13
    const ATTR_LOGIN_SERVICE = 15; // https://tools.ietf.org/html/rfc2865#section-5.15
    const ATTR_LOGIN_TCP_PORT = 16; // https://tools.ietf.org/html/rfc2865#section-5.16
    const ATTR_FRAMED_IPX_NETWORK = 23; // https://tools.ietf.org/html/rfc2865#section-5.23
    const ATTR_SESSION_TIMEOUT = 27; // https://tools.ietf.org/html/rfc2865#section-5.27
    const ATTR_IDLE_TIMEOUT = 28; // https://tools.ietf.org/html/rfc2865#section-5.28
    const ATTR_TERMINATION_ACTION = 29; // https://tools.ietf.org/html/rfc2865#section-5.29
    const ATTR_FRAMED_APPLETALK_LINK = 37; // https://tools.ietf.org/html/rfc2865#section-5.37
    const ATTR_FRAMED_APPLETALK_NETWORK = 38; // https://tools.ietf.org/html/rfc2865#section-5.38
    const ATTR_NAS_PORT_TYPE = 41; // https://tools.ietf.org/html/rfc2865#section-5.41
    const ATTR_PORT_LIMIT = 42; // https://tools.ietf.org/html/rfc2865#section-5.42

    // String
    const ATTR_USER_NAME = 1; // https://tools.ietf.org/html/rfc2865#section-5.1
    const ATTR_FILTER_ID = 11; // https://tools.ietf.org/html/rfc2865#section-5.11
    const ATTR_REPLY_MESSAGE = 18; // https://tools.ietf.org/html/rfc2865#section-5.18
    const ATTR_CALLBACK_NUMBER = 19; // https://tools.ietf.org/html/rfc2865#section-5.19
    const ATTR_CALLBACK_ID = 20; // https://tools.ietf.org/html/rfc2865#section-5.20
    const ATTR_FRAMED_ROUTE = 22; // https://tools.ietf.org/html/rfc2865#section-5.22
    const ATTR_STATE = 24; // https://tools.ietf.org/html/rfc2865#section-5.24
    const ATTR_CLASS = 25; // https://tools.ietf.org/html/rfc2865#section-5.25
    const ATTR_VENDOR_SPECIFIC = 26; // https://tools.ietf.org/html/rfc2865#section-5.26
    const ATTR_CALLED_STATION_ID = 30; // https://tools.ietf.org/html/rfc2865#section-5.30
    const ATTR_CALLING_STATION_ID = 31; // https://tools.ietf.org/html/rfc2865#section-5.31
    const ATTR_NAS_IDENTIFIER = 32; // https://tools.ietf.org/html/rfc2865#section-5.32
    const ATTR_PROXY_STATE = 33; // https://tools.ietf.org/html/rfc2865#section-5.33
    const ATTR_LOGIN_LAT_SERVICE = 34; // https://tools.ietf.org/html/rfc2865#section-5.34
    const ATTR_LOGIN_LAT_NODE = 35; // https://tools.ietf.org/html/rfc2865#section-5.35
    const ATTR_LOGIN_LAT_GROUP = 36; // https://tools.ietf.org/html/rfc2865#section-5.36
    const ATTR_FRAMED_APPLETALK_ZONE = 39; // https://tools.ietf.org/html/rfc2865#section-5.39
    const ATTR_CHAP_CHALLENGE = 40; // https://tools.ietf.org/html/rfc2865#section-5.40
    const ATTR_LOGIN_LAT_PORT = 43; // https://tools.ietf.org/html/rfc2865#section-5.43

    const ATTR_TYPE_ALIAS = [
        self::ATTR_USER_NAME => 'User-Name',
        self::ATTR_USER_PASSWORD => 'User-Password',
        self::ATTR_CHAP_PASSWORD => 'CHAP-Password',
        self::ATTR_NAS_IP_ADDRESS => 'NAS-IP-Address',
        self::ATTR_NAS_PORT => 'NAS-Port',
        self::ATTR_SERVICE_TYPE => 'Service-Type',
        self::ATTR_FRAMED_PROTOCOL => 'Framed-Protocol',
        self::ATTR_FRAMED_IP_ADDRESS => 'Framed-IP-Address',
        self::ATTR_FRAMED_IP_NETMASK => 'Framed-IP-Netmask',
        self::ATTR_FRAMED_ROUTING => 'Framed-Routing',
        self::ATTR_FILTER_ID => 'Filter-Id',
        self::ATTR_FRAMED_MTU => 'Framed-MTU',
        self::ATTR_FRAMED_COMPRESSION => 'Framed-Compression',
        self::ATTR_LOGIN_IP_HOST => 'Login-IP-Host',
        self::ATTR_LOGIN_SERVICE => 'Login-Service',
        self::ATTR_LOGIN_TCP_PORT => 'Login-TCP-Port',
        self::ATTR_REPLY_MESSAGE => 'Reply-Message',
        self::ATTR_CALLBACK_NUMBER => 'Callback-Number',
        self::ATTR_CALLBACK_ID => 'Callback-Id',
        self::ATTR_FRAMED_ROUTE => 'Framed-Route',
        self::ATTR_FRAMED_IPX_NETWORK => 'Framed-IPX-Network',
        self::ATTR_STATE => 'State',
        self::ATTR_CLASS => 'Class',
        self::ATTR_VENDOR_SPECIFIC => 'Vendor-Specific',
        self::ATTR_SESSION_TIMEOUT => 'Session-Timeout',
        self::ATTR_IDLE_TIMEOUT => 'Idle-Timeout',
        self::ATTR_TERMINATION_ACTION => 'Termination-Action',
        self::ATTR_CALLED_STATION_ID => 'Called-Station-Id',
        self:: ATTR_CALLING_STATION_ID => 'Calling-Station-Id',
        self::ATTR_NAS_IDENTIFIER => 'NAS-Identifier',
        self::ATTR_PROXY_STATE => 'Proxy-State',
        self::ATTR_LOGIN_LAT_SERVICE => 'Login-LAT-Service',
        self::ATTR_LOGIN_LAT_NODE => 'Login-LAT-Node',
        self::ATTR_LOGIN_LAT_GROUP => 'Login-LAT-Group',
        self::ATTR_FRAMED_APPLETALK_LINK => 'Framed-AppleTalk-Link',
        self::ATTR_FRAMED_APPLETALK_NETWORK => 'Framed-AppleTalk-Network',
        self::ATTR_FRAMED_APPLETALK_ZONE => 'Framed-AppleTalk-Zone',
        self::ATTR_CHAP_CHALLENGE => 'CHAP-Challenge',
        self::ATTR_NAS_PORT_TYPE => 'NAS-Port-Type',
        self::ATTR_PORT_LIMIT => 'Port-Limit',
        self::ATTR_LOGIN_LAT_PORT => 'Login-LAT-Port',
    ];

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param string $alias
     * @return AttributeInterface
     */
    public function setTypeAlias(string $alias);

    /**
     * @param string $alias
     * @return AttributeInterface
     */
    public function setValueAlias(string $alias);

    /**
     * @return string
     */
    public function getTypeAlias();

    /**
     * @return string
     */
    public function getValueAlias();

}
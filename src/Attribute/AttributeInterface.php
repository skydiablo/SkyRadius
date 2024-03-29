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
    const ATTR_NAS_PORT_TYPE = 61; // https://tools.ietf.org/html/rfc2865#section-5.41
    const ATTR_PORT_LIMIT = 62; // https://tools.ietf.org/html/rfc2865#section-5.42

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
    const ATTR_CHAP_CHALLENGE = 60; // https://tools.ietf.org/html/rfc2865#section-5.40
    const ATTR_LOGIN_LAT_PORT = 63; // https://tools.ietf.org/html/rfc2865#section-5.43

    // Tunnel-Type
    const ATTR_TUNNEL_TYPE = 64; //https://tools.ietf.org/html/rfc2868#section-3.1
    const ATTR_TUNNEL_MEDIUM_TYPE = 65; //https://tools.ietf.org/html/rfc2868#section-3.2
    const ATTR_TUNNEL_CLIENT_ENDPOINT = 66; //https://tools.ietf.org/html/rfc2868#section-3.3
    const ATTR_TUNNEL_SERVER_ENDPOINT = 67; //https://tools.ietf.org/html/rfc2868#section-3.4
    const ATTR_TUNNEL_PASSWORD = 69; //https://tools.ietf.org/html/rfc2868#section-3.5
    const ATTR_TUNNEL_PRIVATE_GROUP_ID = 81; //https://tools.ietf.org/html/rfc2868#section-3.6
    const ATTR_TUNNEL_ASSIGNMENT_ID = 82; //https://tools.ietf.org/html/rfc2868#section-3.7
    const ATTR_TUNNEL_PREFERENCE = 83; //https://tools.ietf.org/html/rfc2868#section-3.8
    const ATTR_TUNNEL_CLIENT_AUTH_ID = 90; //https://tools.ietf.org/html/rfc2868#section-3.9
    const ATTR_TUNNEL_SERVER_AUTH_ID = 91; //https://tools.ietf.org/html/rfc2868#section-3.10

    const ATTR_ACCT_STATUS_TYPE = 40;
    const ATTR_ACCT_DELAY_TIME = 41;
    const ATTR_ACCT_INPUT_OCTETS = 42;
    const ATTR_ACCT_OUTPUT_OCTETS = 43;
    const ATTR_ACCT_SESSION_ID = 44;
    const ATTR_ACCT_AUTHENTIC = 45;
    const ATTR_ACCT_SESSION_TIME = 46;
    const ATTR_ACCT_INPUT_PACKETS = 47;
    const ATTR_ACCT_OUTPUT_PACKETS = 48;
    const ATTR_ACCT_TERMINATE_CAUSE = 49;
    const ATTR_ACCT_MULTI_SESSION_ID = 50;
    const ATTR_ACCT_LINK_COUNT = 51;

    //rfc2869
    const ATTR_ACCT_INPUT_GIGAWORDS = 52;
    const ATTR_ACCT_OUTPUT_GIGAWORDS = 53;
    const ATTR_EVENT_TIMESTAMP = 55;
    const ATTR_ARAP_PASSWORD = 70; //todo: not implemented yet
    const ATTR_ARAP_FEATURES = 71; //todo: not implemented yet
    const ATTR_ARAP_ZONE_ACCESS = 72; //todo: not implemented yet
    const ATTR_ARAP_SECURITY = 73; //todo: not implemented yet
    const ATTR_ARAP_SECURITY_DATA = 74; //todo: not implemented yet
    const ATTR_PASSWORD_RETRY = 75;
    const ATTR_PROMPT = 76;
    const ATTR_CONNECT_INFO = 77;
    const ATTR_CONFIGURATION_TOKEN = 78;
    const ATTR_EAP_MESSAGE = 79; //todo: may not implemented yet
    const ATTR_MESSAGE_AUTHENTICATOR = 80; //todo: may not implemented yet
    const ATTR_ARAP_CHALLENGE_RESPONSE = 84; //todo: not implemented yet
    const ATTR_ACCT_INTERIM_INTERVAL = 85;
    const ATTR_NAS_PORT_ID = 87;
    const ATTR_FRAMED_POOL = 88;


    // checkout RFC 2866 (https://tools.ietf.org/html/rfc2866)
    const ATTR_ACCT_STATUS_VALUE_START = 1;
    const ATTR_ACCT_STATUS_VALUE_STOP = 2;
    const ATTR_ACCT_STATUS_VALUE_INTERIM_UPDATE = 3;
    const ATTR_ACCT_STATUS_VALUE_ACCOUNTING_ON = 7;
    const ATTR_ACCT_STATUS_VALUE_ACCOUNTING_OFF = 8;
    const ATTR_ACCT_STATUS_VALUE_FAILED = 15;

    const ATTR_ACCT_AUTHENTIC_RADIUS = 1;
    const ATTR_ACCT_AUTHENTIC_LOCAL = 2;
    const ATTR_ACCT_AUTHENTIC_REMOTE = 3;

    const ATTR_ACCT_TERMINATE_CAUSE_USER_REQUEST = 1;
    const ATTR_ACCT_TERMINATE_CAUSE_LOST_CARRIER = 2;
    const ATTR_ACCT_TERMINATE_CAUSE_LOST_SERVICE = 3;
    const ATTR_ACCT_TERMINATE_CAUSE_IDLE_TIMEOUT = 4;
    const ATTR_ACCT_TERMINATE_CAUSE_SESSION_TIMEOUT = 5;
    const ATTR_ACCT_TERMINATE_CAUSE_ADMIN_RESET = 6;
    const ATTR_ACCT_TERMINATE_CAUSE_ADMIN_REBOOT = 7;
    const ATTR_ACCT_TERMINATE_CAUSE_PORT_ERROR = 8;
    const ATTR_ACCT_TERMINATE_CAUSE_NAS_ERROR = 9;
    const ATTR_ACCT_TERMINATE_CAUSE_NAS_REQUEST = 10;
    const ATTR_ACCT_TERMINATE_CAUSE_NAS_REBOOT = 11;
    const ATTR_ACCT_TERMINATE_CAUSE_PORT_UNNEEDED = 12;
    const ATTR_ACCT_TERMINATE_CAUSE_PORT_PREEMPTED = 13;
    const ATTR_ACCT_TERMINATE_CAUSE_PORT_SUSPENDED = 14;
    const ATTR_ACCT_TERMINATE_CAUSE_SERVICE_UNAVAILABLE = 15;
    const ATTR_ACCT_TERMINATE_CAUSE_CALLBACK = 16;
    const ATTR_ACCT_TERMINATE_CAUSE_USER_ERROR = 17;
    const ATTR_ACCT_TERMINATE_CAUSE_HOST_REQUEST = 18;

    const ATTR_PROMPT_NO_ECHO = 0;
    const ATTR_PROMPT_ECHO = 1;

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
        self::ATTR_TUNNEL_TYPE => 'Tunnel-Type',
        self::ATTR_TUNNEL_MEDIUM_TYPE => 'Tunnel-Medium-Type',
        self::ATTR_TUNNEL_CLIENT_ENDPOINT => 'Tunnel-Client-Endpoint',
        self::ATTR_TUNNEL_SERVER_ENDPOINT => 'Tunnel-Server-Endpoint',
        self::ATTR_TUNNEL_PASSWORD => 'Tunnel-Password',
        self::ATTR_TUNNEL_PRIVATE_GROUP_ID => 'Tunnel-Private-Group-ID',
        self::ATTR_TUNNEL_ASSIGNMENT_ID => 'Tunnel-Assignment-ID',
        self::ATTR_TUNNEL_PREFERENCE => 'Tunnel-Preference',
        self::ATTR_TUNNEL_CLIENT_AUTH_ID => 'Tunnel-Client-Auth-ID',
        self::ATTR_TUNNEL_SERVER_AUTH_ID => 'Tunnel-Server-Auth-ID',

        self::ATTR_ACCT_STATUS_TYPE => 'Acct-Status-Type',
        self::ATTR_ACCT_DELAY_TIME => 'Acct-Delay-Time',
        self::ATTR_ACCT_INPUT_OCTETS => 'Acct-Input-Octets',
        self::ATTR_ACCT_OUTPUT_OCTETS => 'Acct-Output-Octets',
        self::ATTR_ACCT_SESSION_ID => 'Acct-Session-Id',
        self::ATTR_ACCT_AUTHENTIC => 'Acct-Authentic',
        self::ATTR_ACCT_SESSION_TIME => 'Acct-Session-Time',
        self::ATTR_ACCT_INPUT_PACKETS => 'Acct-Input-Packets',
        self::ATTR_ACCT_OUTPUT_PACKETS => 'Acct-Output-Packets',
        self::ATTR_ACCT_TERMINATE_CAUSE => 'Acct-Terminate-Cause',
        self::ATTR_ACCT_MULTI_SESSION_ID => 'Acct-Multi-Session-Id',
        self::ATTR_ACCT_LINK_COUNT => 'Acct-Link-Count',
        self::ATTR_ACCT_INPUT_GIGAWORDS => 'Acct-Input-Gigawords',
        self::ATTR_ACCT_OUTPUT_GIGAWORDS => 'Acct-Output-Gigawords',
        self::ATTR_ACCT_INTERIM_INTERVAL => 'Acct-Interim-Interval',

        //rfc2869
        self::ATTR_EVENT_TIMESTAMP => 'Event-Timestamp',
        self::ATTR_ARAP_PASSWORD => 'ARAP-Password',
        self::ATTR_ARAP_FEATURES => 'ARAP-Features',
        self::ATTR_ARAP_ZONE_ACCESS => 'ARAP-Zone-Access',
        self::ATTR_ARAP_SECURITY => 'ARAP-Security',
        self::ATTR_ARAP_SECURITY_DATA => 'ARAP-Security-Data',
        self::ATTR_PASSWORD_RETRY => 'Password-Retry',
        self::ATTR_PROMPT => 'Prompt',
        self::ATTR_CONNECT_INFO => 'Connect-Info',
        self::ATTR_CONFIGURATION_TOKEN => 'Configuration-Token',
        self::ATTR_EAP_MESSAGE => 'EAP-Message',
        self::ATTR_MESSAGE_AUTHENTICATOR => 'Message-Authenticator',
        self::ATTR_ARAP_CHALLENGE_RESPONSE => 'ARAP-Challenge-Response',
        self::ATTR_NAS_PORT_ID => 'NAS-Port-Id',
        self::ATTR_FRAMED_POOL => 'Framed-Pool',
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
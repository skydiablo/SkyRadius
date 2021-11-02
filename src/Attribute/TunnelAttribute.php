<?php

declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute;

/**
 * Class TunnelTypeAttribute
 * @package SkyDiablo\SkyRadius\src\Attribute
 */
class TunnelAttribute extends AbstractAttribute
{

    const TUNNEL_TYPE_PPTP = 1; // Point-to-Point Tunneling Protocol (PPTP)
    const TUNNEL_TYPE_L2F = 2; // Layer Two Forwarding (L2F)
    const TUNNEL_TYPE_L2TP = 3; // Layer Two Tunneling Protocol (L2TP)
    const TUNNEL_TYPE_ATMP = 4; // Ascend Tunnel Management Protocol (ATMP)
    const TUNNEL_TYPE_VTP = 5; // Virtual Tunneling Protocol (VTP)
    const TUNNEL_TYPE_AH = 6; // IP Authentication Header in the Tunnel-mode (AH)
    const TUNNEL_TYPE_IPIP = 7; // IP-in-IP Encapsulation (IP-IP)
    const TUNNEL_TYPE_MIN_IPIP = 8; // Minimal IP-in-IP Encapsulation (MIN-IP-IP)
    const TUNNEL_TYPE_ESP = 9; // IP Encapsulating Security Payload in the Tunnel-mode (ESP)
    const TUNNEL_TYPE_GRE = 10; // Generic Route Encapsulation (GRE)
    const TUNNEL_TYPE_DVS = 11; // Bay Dial Virtual Services (DVS)
    const TUNNEL_TYPE_IP_IN_IP = 12; // IP-in-IP Tunneling
    const TUNNEL_TYPE_VLAN = 13; // VLAN


    const TUNNEL_MEDIUM_TYPE_IPV4 = 1; // IPv4 (IP version 4)
    const TUNNEL_MEDIUM_TYPE_IPV6 = 2; // IPv6 (IP version 6)
    const TUNNEL_MEDIUM_TYPE_NSAP = 3; // NSAP
    const TUNNEL_MEDIUM_TYPE_HDLC = 4; // HDLC (8-bit multidrop)
    const TUNNEL_MEDIUM_TYPE_BBN = 5; // BBN 1822
    const TUNNEL_MEDIUM_TYPE_IEEE_802 = 6; // 802 (includes all 802 media plus Ethernet "canonical format")
    const TUNNEL_MEDIUM_TYPE_E163 = 7; // E.163 (POTS)
    const TUNNEL_MEDIUM_TYPE_E164 = 8; // E.164 (SMDS, Frame Relay, ATM)
    const TUNNEL_MEDIUM_TYPE_F69 = 9; // F.69 (Telex)
    const TUNNEL_MEDIUM_TYPE_X121 = 10; // X.121 (X.25, Frame Relay)
    const TUNNEL_MEDIUM_TYPE_IPX = 11; // IPX
    const TUNNEL_MEDIUM_TYPE_APPLETALK = 12; // Appletalk
    const TUNNEL_MEDIUM_TYPE_DECNET_IV = 13; // Decnet IV
    const TUNNEL_MEDIUM_TYPE_BV = 14; // Banyan Vines
    const TUNNEL_MEDIUM_TYPE_E164_NSAP = 15; // E.164 with NSAP format subaddress


    /**
     * Valid values are 1 - 31 (0x01 - 0x1F), otherwise this value should ignored
     * @var int
     */
    private int $tag;

    public function __construct(int $type, int $tag, $value)
    {
        parent::__construct($type, $value);
        $this->tag = $tag;
    }

    /**
     * @return int
     */
    public function getTag(): int
    {
        return $this->tag;
    }

}
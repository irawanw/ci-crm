<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_t_rbl_liste extends CI_Migration 
{
    public function up() 
    {
        $sql = <<<'EOT'
CREATE TABLE IF NOT EXISTS `t_rbl_liste` (
  `rbl_id` int(11) NOT NULL AUTO_INCREMENT,
  `rbl_nom` varchar(50) NOT NULL,
  `rbl_inactive` datetime DEFAULT NULL,
  `rbl_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`rbl_id`),
  KEY `rbl_nom` (`rbl_nom`),
  KEY `rbl_id` (`rbl_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103 ;
EOT;
      echo $this->db->query($sql);

      $sqlInsert = <<<'EOT'
INSERT IGNORE INTO `t_rbl_liste` (`rbl_id`, `rbl_nom`, `rbl_inactive`, `rbl_deleted`) VALUES
(1, 'Abuse.ro', NULL, NULL),
(2, 'Anonmails DNSBL1', NULL, NULL),
(3, 'ASPEWS', NULL, NULL),
(4, 'BACKSCATTERER', NULL, NULL),
(5, 'BARRACUDA', NULL, NULL),
(6, 'BBFHL1', NULL, NULL),
(7, 'BBFHL2', NULL, NULL),
(8, 'BLOCKLIST.DE', NULL, NULL),
(9, 'BSB', NULL, NULL),
(10, 'BSB Domain', NULL, NULL),
(11, 'CALIVENT', NULL, NULL),
(12, 'CASA CBL', NULL, NULL),
(13, 'CBL', NULL, NULL),
(14, 'CYMRU BOGONS', NULL, NULL),
(15, 'DAN TOR', NULL, NULL),
(16, 'DAN TOREXIT', NULL, NULL),
(17, 'DNS RealtimeBlackhole List', NULL, NULL),
(18, 'DNS SERVICIOS', NULL, NULL),
(19, 'DRMX', NULL, NULL),
(20, 'DRONE BL', NULL, NULL),
(21, 'DULRU', NULL, NULL),
(22, 'EMAILBASURA', NULL, NULL),
(23, 'FABELSOURCES', NULL, NULL),
(24, 'HIL', NULL, NULL),
(25, 'HIL2', NULL, NULL),
(26, 'IBM DNS Blacklist', NULL, NULL),
(27, 'ICMFORBIDDEN', NULL, NULL),
(28, 'IMP SPAM', NULL, NULL),
(29, 'IMP WORM', NULL, NULL),
(30, 'INPS_DE', NULL, NULL),
(31, 'INTERSERVER', NULL, NULL),
(32, 'IPrange RBL Project', NULL, NULL),
(33, 'ivmSIP', NULL, NULL),
(34, 'ivmSIP24', NULL, NULL),
(35, 'ivmURI', NULL, NULL),
(36, 'JIPPG', NULL, NULL),
(37, 'KEMPTBL', NULL, NULL),
(38, 'KISA', NULL, NULL),
(39, 'Konstant', NULL, NULL),
(40, 'LASHBACK', NULL, NULL),
(41, 'LNSGBLOCK', NULL, NULL),
(42, 'LNSGBULK', NULL, NULL),
(43, 'LNSGMULTI', NULL, NULL),
(44, 'LNSGOR', NULL, NULL),
(45, 'LNSGSRC', NULL, NULL),
(46, 'MADAVI', NULL, NULL),
(47, 'MailBlacklist', NULL, NULL),
(48, 'MAILSPIKE BL', NULL, NULL),
(49, 'MAILSPIKE Z', NULL, NULL),
(50, 'MSRBL Phishing', NULL, NULL),
(51, 'MSRBL Spam', NULL, NULL),
(52, 'NETHERRELAYS', NULL, NULL),
(53, 'NETHERUNSURE', NULL, NULL),
(54, 'NIXSPAM', NULL, NULL),
(55, 'NoSolicitado', NULL, NULL),
(56, 'ORVEDB', NULL, NULL),
(57, 'OSPAM', NULL, NULL),
(58, 'ProtectedSky', NULL, NULL),
(59, 'PSBL', NULL, NULL),
(60, 'RATS Dyna', NULL, NULL),
(61, 'RATS NoPtr', NULL, NULL),
(62, 'RATS Spam', NULL, NULL),
(63, 'RBL JP', NULL, NULL),
(64, 'RSBL', NULL, NULL),
(65, 'SCHULTE', NULL, NULL),
(66, 'SECTOOR EXITNODES', NULL, NULL),
(67, 'SEM BACKSCATTER', NULL, NULL),
(68, 'SEM BLACK', NULL, NULL),
(69, 'SEM FRESH', NULL, NULL),
(70, 'SEM URI', NULL, NULL),
(71, 'SEM URIRED', NULL, NULL),
(72, 'Sender Score Reputation Network', NULL, NULL),
(73, 'SERVICESNET', NULL, NULL),
(74, 'SORBS BLOCK', NULL, NULL),
(75, 'SORBS DUHL', NULL, NULL),
(76, 'SORBS HTTP', NULL, NULL),
(77, 'SORBS MISC', NULL, NULL),
(78, 'SORBS NEW', NULL, NULL),
(79, 'SORBS RHSBL BADCONF', NULL, NULL),
(80, 'SORBS RHSBL NOMAIL', NULL, NULL),
(81, 'SORBS SMTP', NULL, NULL),
(82, 'SORBS SOCKS', NULL, NULL),
(83, 'SORBS SPAM', NULL, NULL),
(84, 'SORBS WEB', NULL, NULL),
(85, 'SORBS ZOMBIE', NULL, NULL),
(86, 'SPAMCANNIBAL', NULL, NULL),
(87, 'SPAMCOP', NULL, NULL),
(88, 'Spamhaus DBL', NULL, NULL),
(89, 'Spamhaus ZEN', NULL, NULL),
(90, 'SPEWS1', NULL, NULL),
(91, 'SPEWS2', NULL, NULL),
(92, 'SuomispamReputation', NULL, NULL),
(93, 'SURBL multi', NULL, NULL),
(94, 'SWINOG', NULL, NULL),
(95, 'TRIUMF', NULL, NULL),
(96, 'TRUNCATE', NULL, NULL),
(97, 'UCEPROTECTL1', NULL, NULL),
(98, 'UCEPROTECTL2', NULL, NULL),
(99, 'UCEPROTECTL3', NULL, NULL),
(100, 'VIRBL', NULL, NULL),
(101, 'WPBL', NULL, NULL),
(102, 'ZapBL', NULL, NULL);
EOT;

      $this->db->query($sqlInsert);
    }

    public function down() {
        $sql = <<<'EOT'
DROP TABLE IF EXISTS t_rbl_liste;
EOT;
        $this->db->query($sql);
    }
}

// EOF
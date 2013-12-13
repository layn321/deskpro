<?php

/* DeskPRO:pdf_user:view_ticket.html.twig */
class __TwigTemplate_2d1f46bfe1d4c670beea2dfd8f5865f9 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->env->loadTemplate("DeskPRO:pdf_user:header.html.twig")->display($context);
        // line 2
        echo "
<sethtmlpageheader name=\"deskproHeader\" page=\"ALL\" value=\"on\" show-this-page=\"1\" />

<style>
.ticket_attribute {
    list-style: none;
    border-style: solid;
    border-color: #000000;
    border-width: 1px;
    margin: 8px;
    float: left;
}

body {
    font-family: DejaVuSans;
}

.ticket_list_table {
    page-break-inside: avoid;
}
</style>

<h1>";
        // line 24
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "subject"), "html", null, true);
        echo "</h1>

<table class=\"ticket_attribute\">
    <tr><th>";
        // line 27
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_ref");
        echo "</th></tr>
    <tr><td>";
        // line 28
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_ticket_, "public_id"), "html", null, true);
        echo "</td></tr>
</table>
<table class=\"ticket_attribute\">
    <tr><th>";
        // line 31
        echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.department");
        echo "</th></tr>
    <tr><td>";
        // line 32
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "department"), "full"), "html", null, true);
        echo "</td></tr>
</table>

";
        // line 35
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "category")) {
            // line 36
            echo "    <table class=\"ticket_attribute\">
        <tr><th>";
            // line 37
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo "</th></tr>
        <tr><td>";
            // line 38
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "category"), "full"), "html", null, true);
            echo "</td></tr>
    </table>
";
        }
        // line 41
        echo "
";
        // line 42
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "product")) {
            // line 43
            echo "    <table class=\"ticket_attribute\">
        <tr><th>";
            // line 44
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo "</th></tr>
        <tr><td>";
            // line 45
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "product"), "full"), "html", null, true);
            echo "<td></tr>
    </table>
";
        }
        // line 48
        echo "
";
        // line 49
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if ($this->getAttribute($_ticket_, "priority")) {
            // line 50
            echo "    <table class=\"ticket_attribute\">
        <tr><th>";
            // line 51
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo "</th></tr>
        <tr><td>";
            // line 52
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "priority")), "html", null, true);
            echo "</td></tr>
    </table>
";
        }
        // line 55
        echo "
";
        // line 56
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if (($this->getAttribute($this->getAttribute($_ticket_, "person"), "id") != $this->getAttribute($this->getAttribute($_app_, "user"), "id"))) {
            // line 57
            echo "    <table class=\"ticket_attribute\">
        <tr><th>";
            // line 58
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.ticket_starter");
            echo "</th></tr>
        <tr><td>";
            // line 59
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "display_name_user"), "html", null, true);
            echo " &lt;";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "person"), "email_address"), "html", null, true);
            echo "&gt;</td></tr>
    </table>
";
        }
        // line 62
        echo "
";
        // line 63
        if (isset($context["user_participants"])) { $_user_participants_ = $context["user_participants"]; } else { $_user_participants_ = null; }
        if (twig_length_filter($this->env, $_user_participants_)) {
            // line 64
            echo "    <table class=\"ticket_attribute\">
        <tr><th>";
            // line 65
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.tickets.label_cced");
            echo "</th></tr>
        <tr>
            <td>
                <ul>
                    ";
            // line 69
            if (isset($context["user_participants"])) { $_user_participants_ = $context["user_participants"]; } else { $_user_participants_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($_user_participants_);
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["part"]) {
                // line 70
                echo "                        ";
                if (isset($context["part"])) { $_part_ = $context["part"]; } else { $_part_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_part_, "person"), "display_name_user"), "html", null, true);
                echo " &lt;";
                if (isset($context["part"])) { $_part_ = $context["part"]; } else { $_part_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_part_, "person"), "email_address"), "html", null, true);
                echo "&gt;
                        ";
                // line 71
                if (isset($context["loop"])) { $_loop_ = $context["loop"]; } else { $_loop_ = null; }
                if ((!$this->getAttribute($_loop_, "last"))) {
                    echo ", ";
                }
                // line 72
                echo "                    ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['part'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 73
            echo "                </ul>
            </td>
        </tr>
    </table>
";
        }
        // line 78
        echo "
";
        // line 79
        if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
            // line 80
            echo "    ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            if ($this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($this->getAttribute($_f_, "field_def"), "id")), "method")) {
                // line 81
                echo "        <table class=\"ticket_attribute\">
            <tr><th>";
                // line 82
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                echo "</th></tr>
            <tr><td>";
                // line 83
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                echo "</td></tr>
        </table>
    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 87
        echo "
<div style=\"clear:both;\"></div>

";
        // line 90
        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_, "displayable_messages"));
        foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
            // line 91
            echo "<table width=\"100%\" class=\"ticket_list_table\" border=\"0\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><hr /></td></tr>
    <tr>
        <th>";
            // line 94
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "user.tickets.from_x_at_y", array("name" => $this->getAttribute($this->getAttribute($_message_, "person"), "display_name_user"), "date" => $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($_message_, "date_created"), "fulltime")));
            echo "</th>
    </tr>
    <tr><td colspan=\"2\"><hr /></td></tr>

    <tr><td>";
            // line 98
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            echo $this->getAttribute($_message_, "message_html");
            echo "</td></tr>


    ";
            // line 101
            if (isset($context["message_to_attach"])) { $_message_to_attach_ = $context["message_to_attach"]; } else { $_message_to_attach_ = null; }
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            if ($this->getAttribute($_message_to_attach_, $this->getAttribute($_message_, "id"), array(), "array")) {
                // line 102
                echo "        <tr>
            <td>
                <ul class=\"attachment-list\">
                    ";
                // line 105
                if (isset($context["message_to_attach"])) { $_message_to_attach_ = $context["message_to_attach"]; } else { $_message_to_attach_ = null; }
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($_message_to_attach_, $this->getAttribute($_message_, "id"), array(), "array"));
                foreach ($context['_seq'] as $context["_key"] => $context["attach_id"]) {
                    // line 106
                    echo "                    ";
                    if (isset($context["attachments"])) { $_attachments_ = $context["attachments"]; } else { $_attachments_ = null; }
                    if (isset($context["attach_id"])) { $_attach_id_ = $context["attach_id"]; } else { $_attach_id_ = null; }
                    $context["ticket_attach"] = $this->getAttribute($_attachments_, $_attach_id_, array(), "array");
                    // line 107
                    echo "                    <li>
                        ";
                    // line 108
                    if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                    if ($this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "isImage", array(), "method")) {
                        // line 109
                        echo "                            <a href=\"";
                        if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "download_url"), "html", null, true);
                        echo "\" target=\"_blank\">
                                <img src=\"";
                        // line 110
                        if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("serve_blob", array("blob_auth_id" => $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "auth_id"), "filename" => $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "filename"), "s" => 50, "size-fit" => "1")), "html", null, true);
                        echo "\" />
                            </a>
                        ";
                    }
                    // line 113
                    echo "                            <a href=\"";
                    if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "download_url"), "html", null, true);
                    echo "\" target=\"_blank\">";
                    if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "filename"), "html", null, true);
                    echo "</a>
                        <span class=\"size\">(";
                    // line 114
                    if (isset($context["ticket_attach"])) { $_ticket_attach_ = $context["ticket_attach"]; } else { $_ticket_attach_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_attach_, "blob"), "readable_filesize"), "html", null, true);
                    echo ")</span>
                    </li>
                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attach_id'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 117
                echo "                </ul>
            </td>
        </tr>
    ";
            }
            // line 121
            echo "</table>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
    }

    public function getTemplateName()
    {
        return "DeskPRO:pdf_user:view_ticket.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  344 => 117,  318 => 110,  306 => 107,  295 => 105,  357 => 141,  300 => 130,  286 => 101,  276 => 122,  269 => 119,  254 => 112,  128 => 70,  237 => 81,  165 => 57,  122 => 53,  798 => 119,  770 => 113,  759 => 112,  748 => 110,  731 => 108,  721 => 107,  718 => 106,  708 => 104,  696 => 102,  617 => 94,  590 => 91,  553 => 87,  550 => 86,  540 => 84,  533 => 82,  500 => 74,  493 => 72,  489 => 71,  482 => 69,  467 => 67,  464 => 66,  458 => 64,  452 => 62,  449 => 61,  415 => 55,  382 => 52,  372 => 51,  361 => 49,  356 => 48,  339 => 46,  302 => 42,  285 => 40,  258 => 37,  123 => 68,  108 => 63,  424 => 57,  394 => 86,  380 => 80,  338 => 67,  319 => 66,  316 => 65,  312 => 109,  290 => 102,  267 => 57,  206 => 43,  110 => 25,  240 => 82,  224 => 78,  219 => 51,  217 => 73,  202 => 44,  186 => 57,  170 => 82,  100 => 44,  67 => 32,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 276,  1013 => 275,  1007 => 274,  1002 => 272,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 251,  940 => 249,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 228,  887 => 227,  884 => 226,  876 => 222,  874 => 221,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 208,  843 => 206,  840 => 205,  815 => 201,  812 => 200,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 188,  775 => 184,  771 => 183,  754 => 176,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 163,  709 => 162,  706 => 161,  698 => 157,  694 => 156,  692 => 155,  689 => 154,  681 => 150,  677 => 149,  675 => 148,  663 => 143,  661 => 99,  650 => 137,  646 => 136,  629 => 129,  627 => 128,  625 => 127,  622 => 126,  598 => 119,  592 => 117,  586 => 115,  575 => 108,  566 => 103,  556 => 100,  554 => 99,  541 => 92,  536 => 91,  515 => 86,  511 => 85,  509 => 84,  488 => 79,  486 => 78,  483 => 77,  465 => 73,  463 => 72,  450 => 65,  432 => 60,  419 => 57,  371 => 46,  362 => 43,  353 => 139,  337 => 37,  333 => 35,  309 => 108,  303 => 31,  299 => 30,  291 => 28,  272 => 21,  261 => 90,  253 => 14,  239 => 73,  235 => 6,  213 => 98,  200 => 50,  198 => 71,  159 => 78,  149 => 187,  146 => 55,  131 => 55,  116 => 26,  79 => 35,  74 => 35,  71 => 19,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 115,  779 => 306,  776 => 305,  773 => 301,  761 => 296,  751 => 175,  747 => 293,  742 => 292,  739 => 291,  736 => 287,  724 => 282,  705 => 278,  702 => 277,  688 => 265,  680 => 263,  667 => 261,  662 => 260,  656 => 259,  649 => 258,  644 => 97,  641 => 134,  624 => 240,  613 => 231,  607 => 93,  597 => 225,  591 => 222,  584 => 218,  579 => 216,  563 => 88,  559 => 208,  551 => 98,  547 => 205,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 182,  498 => 178,  485 => 176,  480 => 175,  472 => 171,  466 => 169,  460 => 71,  447 => 163,  442 => 162,  434 => 58,  428 => 156,  422 => 152,  404 => 149,  368 => 136,  364 => 134,  340 => 131,  334 => 114,  330 => 129,  325 => 113,  292 => 116,  287 => 115,  282 => 124,  279 => 98,  273 => 107,  266 => 91,  256 => 87,  252 => 102,  228 => 32,  218 => 287,  201 => 91,  64 => 13,  51 => 5,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 793,  2042 => 790,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 774,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 745,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 481,  1257 => 480,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 458,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 452,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 445,  1146 => 444,  1137 => 437,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 421,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 412,  1076 => 411,  1070 => 407,  1057 => 405,  1052 => 404,  1045 => 399,  1040 => 397,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 381,  979 => 379,  974 => 261,  967 => 373,  962 => 371,  958 => 370,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 242,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 341,  868 => 328,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 310,  826 => 309,  822 => 308,  818 => 307,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 311,  789 => 298,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 182,  763 => 287,  760 => 178,  756 => 111,  752 => 284,  745 => 281,  741 => 280,  738 => 279,  732 => 171,  719 => 273,  714 => 280,  710 => 279,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 262,  683 => 261,  679 => 101,  672 => 147,  668 => 256,  665 => 255,  658 => 141,  645 => 248,  640 => 247,  634 => 96,  628 => 240,  623 => 238,  619 => 237,  611 => 235,  606 => 234,  603 => 120,  599 => 232,  595 => 231,  583 => 114,  580 => 90,  573 => 221,  560 => 101,  543 => 204,  538 => 209,  534 => 208,  530 => 81,  526 => 89,  521 => 80,  518 => 204,  514 => 78,  510 => 202,  503 => 75,  496 => 197,  490 => 193,  484 => 192,  474 => 190,  470 => 188,  446 => 64,  440 => 184,  436 => 61,  431 => 157,  425 => 178,  416 => 175,  412 => 55,  408 => 173,  403 => 172,  400 => 53,  396 => 51,  392 => 169,  385 => 166,  381 => 48,  367 => 45,  363 => 155,  359 => 154,  355 => 153,  350 => 121,  346 => 149,  343 => 148,  328 => 139,  324 => 138,  313 => 134,  307 => 132,  301 => 106,  288 => 27,  283 => 125,  271 => 94,  257 => 114,  251 => 13,  238 => 34,  233 => 72,  195 => 42,  191 => 45,  187 => 42,  183 => 87,  130 => 37,  88 => 18,  76 => 37,  115 => 82,  95 => 42,  655 => 233,  651 => 232,  648 => 231,  637 => 223,  633 => 130,  621 => 219,  618 => 218,  615 => 121,  604 => 209,  600 => 226,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 210,  555 => 207,  552 => 190,  549 => 215,  544 => 186,  542 => 185,  535 => 181,  531 => 90,  519 => 189,  516 => 176,  513 => 175,  508 => 172,  506 => 83,  499 => 198,  495 => 166,  491 => 165,  481 => 162,  478 => 68,  475 => 160,  469 => 170,  456 => 154,  451 => 186,  443 => 60,  439 => 147,  427 => 89,  423 => 58,  420 => 176,  409 => 54,  405 => 54,  401 => 132,  391 => 129,  387 => 49,  384 => 139,  378 => 138,  365 => 78,  360 => 120,  348 => 136,  336 => 111,  332 => 140,  329 => 134,  323 => 133,  310 => 133,  305 => 132,  277 => 23,  274 => 91,  263 => 54,  259 => 82,  247 => 110,  244 => 53,  241 => 77,  222 => 67,  210 => 67,  207 => 96,  204 => 94,  184 => 26,  181 => 64,  167 => 212,  157 => 36,  96 => 26,  421 => 153,  417 => 152,  414 => 151,  406 => 148,  398 => 144,  393 => 144,  390 => 143,  376 => 79,  369 => 137,  366 => 136,  352 => 115,  345 => 132,  342 => 72,  331 => 129,  326 => 128,  320 => 137,  317 => 124,  314 => 33,  311 => 122,  308 => 123,  297 => 129,  293 => 128,  281 => 93,  278 => 110,  275 => 39,  264 => 117,  260 => 115,  248 => 75,  245 => 83,  242 => 74,  231 => 87,  227 => 79,  215 => 83,  212 => 82,  209 => 81,  197 => 61,  177 => 84,  171 => 69,  161 => 64,  132 => 71,  121 => 48,  105 => 24,  99 => 34,  81 => 15,  77 => 36,  180 => 38,  176 => 53,  156 => 77,  143 => 30,  139 => 175,  118 => 25,  189 => 70,  185 => 236,  173 => 35,  166 => 68,  152 => 76,  174 => 83,  164 => 65,  154 => 35,  150 => 55,  137 => 33,  133 => 31,  127 => 29,  107 => 30,  102 => 22,  83 => 37,  78 => 20,  53 => 9,  23 => 3,  42 => 12,  138 => 57,  134 => 56,  109 => 25,  103 => 79,  97 => 43,  94 => 42,  84 => 38,  75 => 16,  69 => 11,  66 => 31,  54 => 10,  44 => 7,  230 => 72,  226 => 68,  203 => 72,  193 => 242,  188 => 88,  182 => 235,  178 => 71,  168 => 64,  163 => 79,  160 => 77,  155 => 62,  148 => 43,  145 => 59,  140 => 54,  136 => 60,  125 => 24,  120 => 51,  113 => 17,  101 => 22,  92 => 20,  89 => 17,  85 => 42,  73 => 13,  62 => 30,  59 => 28,  56 => 28,  41 => 4,  126 => 29,  119 => 27,  111 => 48,  106 => 53,  98 => 43,  93 => 33,  86 => 25,  70 => 34,  60 => 16,  28 => 6,  36 => 4,  114 => 49,  104 => 45,  91 => 41,  80 => 37,  63 => 31,  58 => 9,  40 => 11,  34 => 4,  45 => 24,  61 => 13,  55 => 27,  48 => 6,  39 => 4,  35 => 6,  31 => 48,  26 => 5,  21 => 2,  46 => 7,  29 => 5,  57 => 12,  50 => 8,  47 => 23,  38 => 3,  33 => 3,  49 => 8,  32 => 7,  246 => 90,  236 => 59,  232 => 80,  225 => 3,  221 => 102,  216 => 65,  214 => 98,  211 => 272,  208 => 96,  205 => 66,  199 => 65,  196 => 71,  190 => 58,  179 => 66,  175 => 61,  172 => 60,  169 => 59,  162 => 48,  158 => 63,  153 => 45,  151 => 56,  147 => 73,  144 => 42,  141 => 58,  135 => 51,  129 => 25,  124 => 52,  117 => 50,  112 => 26,  90 => 41,  87 => 18,  82 => 12,  72 => 14,  68 => 10,  65 => 9,  52 => 27,  43 => 6,  37 => 10,  30 => 6,  27 => 2,  25 => 3,  24 => 4,  22 => 2,  19 => 1,);
    }
}

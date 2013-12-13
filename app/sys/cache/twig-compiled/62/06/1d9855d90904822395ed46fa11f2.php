<?php

/* DeskPRO:Common:admin-login.css.twig */
class __TwigTemplate_62061d9855d90904822395ed46fa11f2 extends \Application\DeskPRO\Twig\Template
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
        echo "#dp_logo {
\tposition: absolute;
\tbottom: 0;
\tright: 0;
\tbottom: 0;
\tleft: 0;
\tz-index: 99900;
\tdisplay: block;

\theight: 59px;
\ttext-indent: -10000px;
\toverflow: hidden;

\tbackground: url(";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/dp-logo-faded.png"), "html", null, true);
        echo ") 50% 50% no-repeat;

\tpadding: 15px 0 15px 0;
}

#dp_logo:hover {
\ttext-decoration: none;
\tbackground-image: url(";
        // line 21
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("images/dp-logo-color.png"), "html", null, true);
        echo ");
}

#dp_login {
\tposition: absolute;
\ttop: 0;
\tright: 0;
\tbottom: 0;
\tleft: 0;
\tz-index: 9999;

\tbackground-color: #DFE5EB;
\tbackground: rgb(158,166,175); /* Old browsers */
\tbackground: -moz-linear-gradient(top, rgba(158,166,175,1) 0%, rgba(213,218,224,1) 15%, rgba(223,229,235,1) 100%); /* FF3.6+ */
\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(158,166,175,1)), color-stop(15%,rgba(213,218,224,1)), color-stop(100%,rgba(223,229,235,1))); /* Chrome,Safari4+ */
\tbackground: -webkit-linear-gradient(top, rgba(158,166,175,1) 0%,rgba(213,218,224,1) 15%,rgba(223,229,235,1) 100%); /* Chrome10+,Safari5.1+ */
\tbackground: -o-linear-gradient(top, rgba(158,166,175,1) 0%,rgba(213,218,224,1) 15%,rgba(223,229,235,1) 100%); /* Opera11.10+ */
\tbackground: -ms-linear-gradient(top, rgba(158,166,175,1) 0%,rgba(213,218,224,1) 15%,rgba(223,229,235,1) 100%); /* IE10+ */
\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9ea6af', endColorstr='#dfe5eb',GradientType=0 ); /* IE6-9 */
\tbackground: linear-gradient(top, rgba(158,166,175,1) 0%,rgba(213,218,224,1) 15%,rgba(223,229,235,1) 100%); /* W3C */
\ttext-align: center;
}

#dp_login .msg-outer {
\t-webkit-border-radius: 4px;
\t-moz-border-radius: 4px;
\tborder-radius: 4px;

\twidth: 550px;

\tposition: absolute;
\ttop: 50%;
\tleft: 50%;
\tmargin-top: -120px;
\tmargin-left: -275px;

\t-webkit-box-shadow: 1px 1px 6px 1px rgba(50, 50, 50, 0.6);
\t-moz-box-shadow: 1px 1px 6px 1px rgba(50, 50, 50, 0.6);
\tbox-shadow: 1px 1px 6px 1px rgba(50, 50, 50, 0.6);

\tborder: 1px solid #C3CEE1;
}

#dp_login .msg {
\t-webkit-border-radius: 3px;
\t-moz-border-radius: 3px;
\tborder-radius: 3px;
\tbackground-color: #fff;

\tborder: 1px solid #6B7A9F;

\t-webkit-box-shadow: inset 0px 0px 4px 1px rgba(50, 50, 50, 0.3);
\t-moz-box-shadow: inset 0px 0px 4px 1px rgba(50, 50, 50, 0.3);
\tbox-shadow: inset 0px 0px 4px 1px rgba(50, 50, 50, 0.3);

\tpadding:  20px;
\tz-index: 99999;
\tposition: relative;
}

#dp_login h1 {
\tfont-family: 'Myriad Pro', 'Myriad', helvetica, arial, sans-serif;
\tfont-size: 17px;
\ttext-align: left;
\tcolor: #000;
\tpadding: 0;
\tmargin: 0;
\tmargin-bottom: 15px;
}

#dp_login dl {
\tmargin: 0;
\tpadding: 0;
}
#dp_login dt {
\tclear: left;
\tmargin: 0;
\tpadding: 0;
\tmargin-top: 8px;

\tfont-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;
\tfont-size: 13px;
\tfont-weight: bold;

\tfloat: left;
\twidth: 120px;
\tline-height: 33px;
\ttext-align: right;
}
#dp_login dd {
\tmargin: 0;
\tpadding: 0;
\tmargin-top: 8px;
\tmargin-left: 15px;

\tfont-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;
\tfont-size: 13px;
\tfont-weight: bold;

\tfloat: left;
\tline-height: 33px;

\twidth: 330px;
\ttext-align: left;
}

#dp_login dd input.text {
\tpadding: 5px;
\tborder: solid 1px #B6BABF;
\toutline: 0;
\tfont: normal 13px/100% Verdana, Tahoma, sans-serif;
\tbackground: #FFFFFF;
\tbackground: -webkit-gradient(linear, left top, left 25, from(#FFFFFF), color-stop(4%, #EEEEEE), to(#FFFFFF));
\tbackground: -moz-linear-gradient(top, #FFFFFF, #EEEEEE 1px, #FFFFFF 25px);
\tbox-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;
\t-moz-box-shadow: 0px 0px 8px rgba(0,0,0, 0.1);
\t-webkit-box-shadow: 0px 0px 8px rgba(0,0,0, 0.1);
\tbox-shadow: 0px 0px 8px rgba(0,0,0, 0.1);
\twidth: 330px;

\t-webkit-border-top-left-radius: 3px;
\t-webkit-border-top-right-radius: 3px;
\t-webkit-border-bottom-right-radius: 3px;
\t-webkit-border-bottom-left-radius: 3px;
\t-moz-border-radius-topleft: 3px;
\t-moz-border-radius-topright: 3px;
\t-moz-border-radius-bottomright: 3px;
\t-moz-border-radius-bottomleft: 3px;
\tborder-top-left-radius: 3px;
\tborder-top-right-radius: 3px;
\tborder-bottom-right-radius: 3px;
\tborder-bottom-left-radius: 3px;
}

#dp_login dd.btn {
\ttext-align: left;
}

#dp_login dd.btn.lost a {
\tmargin-left: 8px;
\tfont-size: 11px;
\tcolor: #B6BABF;
\ttext-decoration: none;
}

#dp_login dd.btn.lost a:hover {
\tcolor: #000;
\ttext-decoration: underline;
}

#dp_login dd cite {
\tbackground-repeat: no-repeat;
\tbackground-position: 0 50%;
\tpadding-left: 36px;
\tfont: normal 13px/33px Verdana, Tahoma, sans-serif;
\tdisplay: block;
}

#dp_login button {
\tposition: relative;
\tbackground: #f0f2f3;
\tbackgroundt: repeat-x;
\tbackground: -khtml-gradient(linear, left top, left bottom, from(#fafcfd), to(#f0f2f3));
\tbackground: -moz-linear-gradient(#fafcfd, #f0f2f3);
\tbackground: -ms-linear-gradient(#fafcfd, #f0f2f3);
\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fafcfd), color-stop(100%, #f0f2f3));
\tbackground: -webkit-linear-gradient(#fafcfd, #f0f2f3);
\tbackground: -o-linear-gradient(#fafcfd, #f0f2f3);
\tfilter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fafcfd', endColorstr='#f0f2f3', GradientType=0);
\t-ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorstr='#fafcfd', endColorstr='#f0f2f3', GradientType=0)\";
\tbackground: linear-gradient(#fafcfd, #f0f2f3);
\tbox-shadow: 0 1px 0 rgba(255, 255, 255, 0.6), 0 1px 0 rgba(255, 255, 255, 0.5) inset, 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
\tborder: 1px solid #b6babf;
\t-webkit-border-top-left-radius: 3px;
\t-webkit-border-top-right-radius: 3px;
\t-webkit-border-bottom-right-radius: 3px;
\t-webkit-border-bottom-left-radius: 3px;
\t-moz-border-radius-topleft: 3px;
\t-moz-border-radius-topright: 3px;
\t-moz-border-radius-bottomright: 3px;
\t-moz-border-radius-bottomleft: 3px;
\tborder-top-left-radius: 3px;
\tborder-top-right-radius: 3px;
\tborder-bottom-right-radius: 3px;
\tborder-bottom-left-radius: 3px;
\t-moz-background-clip: padding;
\t-webkit-background-clip: padding-box;
\tbackground-clip: padding-box;
\tcolor: #626A73;
\tpadding: 3px 6px 3px 8px;
\tcursor: pointer;
\tfont-size: 10px;
\tfont: normal 13px/15px Arial, Helvetica, sans-serif;
\tposition: relative;
\twhite-space: nowrap;
}

#dp_login dd input.text:focus {
\t-moz-box-shadow: 0px 0px 8px rgba(0,0,0, 0.2);
\t-webkit-box-shadow: 0px 0px 8px rgba(0,0,0, 0.2);
\tbox-shadow: 0px 0px 8px rgba(0,0,0, 0.2);
\tborder-color: #009CD9;
}

#dp_login dd.password {
\tposition: relative;
}
#dp_login dd.password input.text {
\tpadding-right: 35px;
\twidth: 300px;
}
#dp_login dd.password a {
\tposition: absolute;
\ttop: 0px;
\tright: -5px;
\tfont-size: 11px;
\tcolor: #B6BABF;
\ttext-decoration: none;
}
#dp_login dd.password a:hover {
\tcolor: #000;
\ttext-decoration: underline;
}

#dp_login p.error {
\tcolor: #8B170C;
\tfont: normal 9pt/15px Arial, Helvetica, sans-serif;
\tmargin: 0px 25px 10px 25px;
\ttext-align: left;

\tbackground-color: #F8D1D7;
\tborder: 1px solid #B17E81;
\tpadding: 5px;
}

#dp_login p.okay {
\tcolor: #227434;
\tfont: normal 9pt/15px Arial, Helvetica, sans-serif;
\tmargin: 0px 25px 10px 25px;
\ttext-align: left;

\tbackground-color: #CBF9C9;
\tborder: 1px solid #6AA66E;
\tpadding: 5px;
}

#dp_login p.note {
\tcolor: #37341A;
\tfont: normal 9pt/15px Arial, Helvetica, sans-serif;
\tmargin: 0px 25px 10px 25px;
\ttext-align: left;

\tbackground-color: #FFF692;
\tborder: 1px solid #B3AC64;
\tpadding: 5px;
}

br.clear {
\tclear: both;
\theight: 1px;
\twidth: 1px;
\toverflow: hidden;
}

#lost_explain {
\tline-height: 120%;
\tfont-weight: normal;
\tfont-size: 11px;
\tpadding-top: 4px;
}

#lost_explain .code {
\tdisplay: block;
\tfont-family: 'Monaco', 'Courier New', monospace;
\tmargin-top: 4px;
\tpadding: 3px;
\toverflow: scroll;
\twidth: 340px;
\twhite-space: nowrap;
}";
    }

    public function getTemplateName()
    {
        return "DeskPRO:Common:admin-login.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 6,  138 => 41,  134 => 39,  109 => 34,  103 => 33,  97 => 29,  94 => 28,  84 => 25,  75 => 20,  69 => 17,  66 => 16,  54 => 11,  44 => 21,  230 => 84,  226 => 82,  203 => 76,  193 => 70,  188 => 67,  182 => 63,  178 => 61,  168 => 58,  163 => 57,  160 => 56,  155 => 55,  148 => 44,  145 => 49,  140 => 46,  136 => 44,  125 => 40,  120 => 39,  113 => 36,  101 => 33,  92 => 28,  89 => 27,  85 => 25,  73 => 19,  62 => 15,  59 => 14,  56 => 13,  41 => 8,  126 => 24,  119 => 36,  111 => 20,  106 => 34,  98 => 32,  93 => 17,  86 => 14,  70 => 9,  60 => 8,  28 => 8,  36 => 5,  114 => 21,  104 => 19,  91 => 16,  80 => 15,  63 => 15,  58 => 12,  40 => 8,  34 => 14,  45 => 7,  61 => 12,  55 => 6,  48 => 7,  39 => 16,  35 => 4,  31 => 6,  26 => 4,  21 => 1,  46 => 6,  29 => 3,  57 => 9,  50 => 10,  47 => 6,  38 => 5,  33 => 4,  49 => 10,  32 => 4,  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 79,  214 => 73,  211 => 78,  208 => 77,  205 => 70,  199 => 66,  196 => 71,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 16,  87 => 26,  82 => 24,  72 => 22,  68 => 18,  65 => 14,  52 => 11,  43 => 13,  37 => 5,  30 => 3,  27 => 3,  25 => 3,  24 => 2,  22 => 2,  19 => 1,);
    }
}

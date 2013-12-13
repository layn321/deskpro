<?php

/* UserBundle:Twitter:view-long.html.twig */
class __TwigTemplate_8373e08b207f034cc4f9d14e83d17032 extends \Application\DeskPRO\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("UserBundle::layout.html.twig");

        $this->blocks = array(
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'page_title' => array($this, 'block_page_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        ob_start();
        // line 3
        if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
        if (isset($context["can_view"])) { $_can_view_ = $context["can_view"]; } else { $_can_view_ = null; }
        if ($this->getAttribute($_long_, "is_public")) {
            // line 4
            echo "\t\tLong tweet by @";
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "
\t";
        } elseif (($this->getAttribute($this->getAttribute($_long_, "status"), "recipient") && (!$_can_view_))) {
            // line 6
            echo "\t\tPrivate tweet
\t";
        } else {
            // line 8
            echo "\t\tPrivate tweet by @";
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "
\t";
        }
        $context["title"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 11
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 12
        echo "\t<li><span class=\"dp-divider\">";
        echo $this->env->getExtension('deskpro_templating')->getLanguageArrow("right");
        echo "</span> <a href=\"";
        if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_long_tweet_view", array("long_id" => $this->getAttribute($_long_, "id"))), "html", null, true);
        echo "\">";
        if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
        echo twig_escape_filter($this->env, $_title_, "html", null, true);
        echo "</a></li>
";
    }

    // line 14
    public function block_page_title($context, array $blocks = array())
    {
        if (isset($context["title"])) { $_title_ = $context["title"]; } else { $_title_ = null; }
        echo twig_escape_filter($this->env, $_title_, "html", null, true);
    }

    // line 15
    public function block_content($context, array $blocks = array())
    {
        // line 16
        echo "
<section class=\"dp-tweet dp-content-page dp-content-post\">
\t<article style=\"border-top: none\">
\t\t";
        // line 19
        if (isset($context["can_view"])) { $_can_view_ = $context["can_view"]; } else { $_can_view_ = null; }
        if ($_can_view_) {
            // line 20
            echo "\t\t\t<div class=\"dp-status-body\">
\t\t\t\t<div class=\"dp-photo\">
\t\t\t\t\t<a href=\"https://twitter.com/";
            // line 22
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "\" target=\"_blank\"><img src=\"";
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "profile_image_url"), "html", null, true);
            echo "\" width=\"48\" height=\"48\" border=\"0\" /></a>
\t\t\t\t</div>
\t\t\t\t<div class=\"dp-main-status-body\">
\t\t\t\t\t<h4>
\t\t\t\t\t\t<a href=\"https://twitter.com/";
            // line 26
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "\" target=\"_blank\" class=\"dp-name\">";
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "name"), "html", null, true);
            echo "</a>
\t\t\t\t\t\t<a href=\"https://twitter.com/";
            // line 27
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "\" target=\"_blank\" class=\"dp-screen-name\">@";
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
            echo "</a>
\t\t\t\t\t</h4>

\t\t\t\t\t<div class=\"dp-status-text\">";
            // line 30
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            echo $this->getAttribute($_long_, "parsed_text");
            echo "</div>

\t\t\t\t\t";
            // line 32
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            if ($this->getAttribute($this->getAttribute($_long_, "status"), "isMessage", array(), "method")) {
                // line 33
                echo "\t\t\t\t\t\t<time class=\"timeago\" title=\"";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_long_, "status"), "date_created"), "c", "UTC"), "html", null, true);
                echo "\">";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_long_, "status"), "date_created"), "day"), "html", null, true);
                echo "</time>
\t\t\t\t\t";
            } else {
                // line 35
                echo "\t\t\t\t\t\t<a href=\"https://twitter.com/";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($_long_, "status"), "user"), "screen_name"), "html", null, true);
                echo "/status/";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_long_, "status"), "id"), "html", null, true);
                echo "\" target=\"_blank\" class=\"timeago\" title=\"";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_long_, "status"), "date_created"), "c", "UTC"), "html", null, true);
                echo "\">";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->userDate($context, $this->getAttribute($this->getAttribute($_long_, "status"), "date_created"), "day"), "html", null, true);
                echo "</a>
\t\t\t\t\t";
            }
            // line 37
            echo "
\t\t\t\t\t";
            // line 38
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            if (($this->getAttribute($this->getAttribute($_long_, "status"), "in_reply_to_status") && (!$this->getAttribute($this->getAttribute($_long_, "status"), "isMessage", array(), "method")))) {
                // line 39
                echo "\t\t\t\t\t\t<div class=\"dp-extra-container\">
\t\t\t\t\t\t\t";
                // line 40
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                $context["reply"] = $this->getAttribute($this->getAttribute($_long_, "status"), "in_reply_to_status");
                // line 41
                echo "\t\t\t\t\t\t\t<div class=\"dp-in-reply-to\">
\t\t\t\t\t\t\t\tIn reply to
\t\t\t\t\t\t\t\t<a href=\"https://twitter.com/";
                // line 43
                if (isset($context["reply"])) { $_reply_ = $context["reply"]; } else { $_reply_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_reply_, "user"), "screen_name"), "html", null, true);
                echo "/status/";
                if (isset($context["reply"])) { $_reply_ = $context["reply"]; } else { $_reply_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_reply_, "id"), "html", null, true);
                echo "\"><span class=\"dp-user\"><strong>@";
                if (isset($context["reply"])) { $_reply_ = $context["reply"]; } else { $_reply_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_reply_, "user"), "screen_name"), "html", null, true);
                echo "</strong></span>
\t\t\t\t\t\t\t\t\"";
                // line 44
                if (isset($context["reply"])) { $_reply_ = $context["reply"]; } else { $_reply_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_reply_, "getClippedText", array(0 => 70), "method"), "html", null, true);
                echo "\"</a>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t";
            }
            // line 48
            echo "\t\t\t\t</div>

\t\t\t\t";
            // line 50
            if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_long_, "status"), "isMessage", array(), "method"))) {
                // line 51
                echo "\t\t\t\t\t<ul class=\"dp-status-buttons\">
\t\t\t\t\t\t<li class=\"dp-reply\"><a href=\"https://twitter.com/intent/tweet?in_reply_to=";
                // line 52
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_long_, "status"), "id"), "html", null, true);
                echo "\"><em></em>Reply</a></li>
\t\t\t\t\t\t<li class=\"dp-retweet\"><a href=\"https://twitter.com/intent/retweet?tweet_id=";
                // line 53
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_long_, "status"), "id"), "html", null, true);
                echo "\"><em></em>Retweet</a></li>
\t\t\t\t\t\t<li class=\"dp-favorite\"><a href=\"https://twitter.com/intent/favorite?tweet_id=";
                // line 54
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_long_, "status"), "id"), "html", null, true);
                echo "\"><em></em>Favorite</a></li>
\t\t\t\t\t</ul>
\t\t\t\t";
            }
            // line 57
            echo "\t\t\t</div>
\t\t";
        } else {
            // line 59
            echo "\t\t\t";
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ($this->getAttribute($this->getAttribute($_app_, "session"), "get", array(0 => "twitter_user_id"), "method")) {
                // line 60
                echo "\t\t\t\t<p>This is a private message that is not directed at you. You do not have permission to view it.</p>
\t\t\t";
            } else {
                // line 62
                echo "\t\t\t\t";
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                if ($this->getAttribute($this->getAttribute($_long_, "status"), "recipient")) {
                    // line 63
                    echo "\t\t\t\t\t<p>This is a private message. You must sign in with Twitter to view this message.</p>
\t\t\t\t";
                } else {
                    // line 65
                    echo "\t\t\t\t\t<p>This is a private message for @";
                    if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_long_, "for_user"), "screen_name"), "html", null, true);
                    echo ". You must sign in with Twitter to view this message.</p>
\t\t\t\t";
                }
                // line 67
                echo "\t\t\t\t<div style=\"text-align: center; margin-top: 10px;\">
\t\t\t\t\t<a href=\"";
                // line 68
                if (isset($context["long"])) { $_long_ = $context["long"]; } else { $_long_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_long_tweet_view", array("long_id" => $this->getAttribute($_long_, "id"), "start" => 1)), "html", null, true);
                echo "\" class=\"dp-dp-btn dp-btn-primary\">Sign in with Twitter</a>
\t\t\t\t</div>
\t\t\t";
            }
            // line 71
            echo "\t\t";
        }
        // line 72
        echo "\t</article>
</section>

<script type=\"text/javascript\">
(function() {
  if (window.__twitterIntentHandler) return;
  var intentRegex = /twitter\\.com(\\:\\d{2,4})?\\/intent\\/(\\w+)/,
      windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
      width = 550,
      height = 420,
      winHeight = screen.height,
      winWidth = screen.width;

  function handleIntent(e) {
    e = e || window.event;
    var target = e.target || e.srcElement,
        m, left, top;

    while (target && target.nodeName.toLowerCase() !== 'a') {
      target = target.parentNode;
    }

    if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
      m = target.href.match(intentRegex);
      if (m) {
        left = Math.round((winWidth / 2) - (width / 2));
        top = 0;

        if (winHeight > height) {
          top = Math.round((winHeight / 2) - (height / 2));
        }

        window.open(target.href, 'intent', windowOptions + ',width=' + width +
                                           ',height=' + height + ',left=' + left + ',top=' + top);
        e.returnValue = false;
        e.preventDefault && e.preventDefault();
      }
    }
  }

  if (document.addEventListener) {
    document.addEventListener('click', handleIntent, false);
  } else if (document.attachEvent) {
    document.attachEvent('onclick', handleIntent);
  }
  window.__twitterIntentHandler = true;
}());
</script>

";
    }

    public function getTemplateName()
    {
        return "UserBundle:Twitter:view-long.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  722 => 269,  697 => 256,  674 => 249,  671 => 248,  577 => 220,  569 => 216,  557 => 209,  502 => 195,  497 => 194,  445 => 173,  729 => 286,  684 => 261,  676 => 256,  669 => 254,  660 => 250,  647 => 243,  643 => 244,  601 => 231,  570 => 211,  522 => 200,  501 => 179,  296 => 149,  374 => 137,  631 => 239,  616 => 19,  608 => 17,  605 => 16,  596 => 15,  574 => 13,  561 => 209,  527 => 147,  433 => 160,  388 => 142,  426 => 177,  383 => 146,  461 => 167,  370 => 113,  395 => 144,  294 => 76,  223 => 59,  220 => 59,  492 => 127,  468 => 121,  444 => 33,  410 => 105,  397 => 101,  377 => 144,  262 => 95,  250 => 129,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 2108,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 1383,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 1303,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 1173,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 1041,  1301 => 1037,  1252 => 999,  1243 => 994,  1223 => 978,  1212 => 970,  1167 => 936,  1161 => 933,  1145 => 923,  1140 => 922,  1134 => 920,  1126 => 916,  1106 => 900,  1100 => 897,  1026 => 841,  997 => 822,  983 => 816,  975 => 812,  956 => 800,  939 => 786,  902 => 753,  894 => 749,  879 => 746,  757 => 631,  727 => 608,  716 => 605,  670 => 582,  528 => 447,  476 => 123,  435 => 31,  354 => 110,  341 => 97,  192 => 48,  321 => 163,  243 => 92,  793 => 351,  780 => 348,  758 => 341,  700 => 257,  686 => 292,  652 => 274,  638 => 266,  620 => 254,  545 => 218,  523 => 203,  494 => 183,  459 => 163,  438 => 172,  351 => 119,  347 => 109,  402 => 157,  268 => 75,  430 => 120,  411 => 120,  379 => 96,  322 => 92,  315 => 110,  289 => 38,  284 => 93,  255 => 24,  234 => 89,  1133 => 64,  1124 => 57,  1121 => 56,  1116 => 55,  1113 => 54,  1108 => 51,  1103 => 43,  1098 => 26,  1081 => 361,  1073 => 358,  1067 => 356,  1064 => 355,  1061 => 354,  1053 => 350,  1049 => 348,  1042 => 346,  1039 => 345,  1025 => 335,  1021 => 333,  1015 => 331,  1008 => 329,  996 => 320,  989 => 316,  985 => 315,  981 => 314,  977 => 313,  970 => 311,  966 => 310,  955 => 303,  952 => 302,  943 => 299,  936 => 296,  930 => 293,  919 => 290,  917 => 289,  908 => 287,  905 => 286,  896 => 280,  891 => 278,  877 => 270,  862 => 267,  857 => 265,  837 => 253,  832 => 250,  827 => 248,  821 => 247,  803 => 243,  778 => 235,  769 => 233,  765 => 232,  753 => 229,  746 => 225,  743 => 224,  735 => 613,  730 => 218,  720 => 213,  717 => 212,  712 => 209,  691 => 204,  678 => 586,  654 => 196,  587 => 14,  576 => 179,  539 => 171,  517 => 197,  471 => 155,  441 => 131,  437 => 142,  418 => 165,  386 => 160,  373 => 95,  304 => 151,  270 => 33,  265 => 83,  229 => 88,  477 => 138,  455 => 177,  448 => 164,  429 => 159,  407 => 95,  399 => 156,  389 => 99,  375 => 123,  358 => 103,  349 => 118,  335 => 84,  327 => 93,  298 => 84,  280 => 85,  249 => 68,  194 => 65,  142 => 68,  344 => 83,  318 => 162,  306 => 87,  295 => 83,  357 => 119,  300 => 150,  286 => 145,  276 => 87,  269 => 66,  254 => 94,  128 => 32,  237 => 69,  165 => 45,  122 => 30,  798 => 242,  770 => 113,  759 => 112,  748 => 337,  731 => 108,  721 => 606,  718 => 106,  708 => 271,  696 => 102,  617 => 234,  590 => 226,  553 => 87,  550 => 466,  540 => 84,  533 => 82,  500 => 129,  493 => 72,  489 => 181,  482 => 69,  467 => 67,  464 => 120,  458 => 166,  452 => 117,  449 => 174,  415 => 163,  382 => 124,  372 => 107,  361 => 104,  356 => 122,  339 => 124,  302 => 85,  285 => 77,  258 => 64,  123 => 28,  108 => 23,  424 => 156,  394 => 86,  380 => 80,  338 => 107,  319 => 101,  316 => 91,  312 => 109,  290 => 146,  267 => 81,  206 => 51,  110 => 31,  240 => 122,  224 => 60,  219 => 57,  217 => 58,  202 => 52,  186 => 69,  170 => 82,  100 => 28,  67 => 13,  14 => 1,  1096 => 291,  1090 => 290,  1088 => 289,  1085 => 288,  1066 => 284,  1034 => 282,  1031 => 281,  1018 => 836,  1013 => 275,  1007 => 274,  1002 => 823,  993 => 266,  986 => 264,  982 => 263,  976 => 262,  971 => 260,  964 => 255,  949 => 252,  946 => 300,  940 => 298,  937 => 248,  928 => 244,  926 => 243,  915 => 237,  912 => 236,  903 => 231,  898 => 230,  892 => 229,  889 => 748,  887 => 227,  884 => 747,  876 => 222,  874 => 269,  871 => 220,  863 => 214,  861 => 213,  858 => 212,  850 => 263,  843 => 206,  840 => 205,  815 => 245,  812 => 244,  808 => 199,  804 => 120,  799 => 196,  791 => 190,  785 => 238,  775 => 184,  771 => 183,  754 => 340,  728 => 170,  726 => 169,  723 => 168,  715 => 105,  711 => 263,  709 => 162,  706 => 260,  698 => 208,  694 => 255,  692 => 155,  689 => 254,  681 => 150,  677 => 149,  675 => 148,  663 => 251,  661 => 277,  650 => 246,  646 => 136,  629 => 129,  627 => 21,  625 => 236,  622 => 126,  598 => 230,  592 => 117,  586 => 115,  575 => 214,  566 => 210,  556 => 100,  554 => 177,  541 => 216,  536 => 205,  515 => 86,  511 => 85,  509 => 196,  488 => 126,  486 => 78,  483 => 189,  465 => 73,  463 => 179,  450 => 65,  432 => 179,  419 => 155,  371 => 141,  362 => 151,  353 => 129,  337 => 18,  333 => 122,  309 => 94,  303 => 76,  299 => 105,  291 => 111,  272 => 82,  261 => 78,  253 => 58,  239 => 65,  235 => 63,  213 => 84,  200 => 43,  198 => 66,  159 => 57,  149 => 61,  146 => 47,  131 => 33,  116 => 25,  79 => 22,  74 => 15,  71 => 14,  836 => 320,  817 => 319,  814 => 318,  811 => 317,  805 => 313,  787 => 657,  779 => 306,  776 => 305,  773 => 347,  761 => 296,  751 => 175,  747 => 293,  742 => 336,  739 => 291,  736 => 287,  724 => 214,  705 => 278,  702 => 601,  688 => 262,  680 => 263,  667 => 261,  662 => 246,  656 => 249,  649 => 258,  644 => 97,  641 => 241,  624 => 236,  613 => 233,  607 => 232,  597 => 221,  591 => 220,  584 => 223,  579 => 234,  563 => 213,  559 => 208,  551 => 202,  547 => 200,  537 => 201,  524 => 191,  512 => 185,  507 => 76,  504 => 143,  498 => 142,  485 => 176,  480 => 124,  472 => 171,  466 => 153,  460 => 119,  447 => 388,  442 => 162,  434 => 110,  428 => 29,  422 => 166,  404 => 149,  368 => 136,  364 => 127,  340 => 19,  334 => 130,  330 => 94,  325 => 104,  292 => 102,  287 => 109,  282 => 119,  279 => 98,  273 => 103,  266 => 98,  256 => 71,  252 => 67,  228 => 85,  218 => 81,  201 => 100,  64 => 14,  51 => 14,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 805,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 1705,  2037 => 789,  2031 => 788,  2028 => 787,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 775,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 740,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 726,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 707,  1829 => 706,  1827 => 705,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 696,  1801 => 695,  1798 => 694,  1795 => 693,  1789 => 689,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 676,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 666,  1739 => 665,  1736 => 664,  1730 => 660,  1720 => 658,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 645,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 638,  1657 => 637,  1651 => 633,  1641 => 631,  1637 => 630,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 617,  1592 => 614,  1587 => 613,  1584 => 612,  1581 => 611,  1576 => 608,  1574 => 607,  1567 => 603,  1563 => 602,  1559 => 601,  1552 => 598,  1548 => 597,  1542 => 593,  1529 => 591,  1525 => 590,  1516 => 584,  1512 => 583,  1503 => 576,  1490 => 574,  1486 => 573,  1477 => 567,  1473 => 566,  1467 => 562,  1460 => 557,  1447 => 555,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 530,  1388 => 529,  1380 => 524,  1376 => 523,  1371 => 520,  1367 => 519,  1364 => 518,  1361 => 517,  1355 => 513,  1342 => 511,  1337 => 510,  1328 => 504,  1324 => 503,  1317 => 500,  1313 => 499,  1310 => 498,  1304 => 494,  1291 => 492,  1286 => 491,  1279 => 486,  1274 => 484,  1270 => 483,  1266 => 482,  1262 => 1001,  1257 => 1000,  1254 => 479,  1250 => 478,  1246 => 477,  1239 => 474,  1235 => 473,  1232 => 472,  1226 => 468,  1213 => 466,  1208 => 465,  1201 => 460,  1196 => 958,  1192 => 457,  1188 => 456,  1184 => 455,  1179 => 454,  1176 => 453,  1172 => 938,  1168 => 451,  1164 => 450,  1160 => 449,  1153 => 446,  1149 => 924,  1146 => 444,  1137 => 249,  1123 => 426,  1119 => 425,  1115 => 424,  1111 => 423,  1107 => 422,  1102 => 898,  1099 => 420,  1095 => 419,  1091 => 418,  1082 => 413,  1079 => 880,  1076 => 359,  1070 => 875,  1057 => 352,  1052 => 404,  1045 => 854,  1040 => 852,  1036 => 283,  1032 => 395,  1028 => 394,  1023 => 393,  1020 => 392,  1016 => 391,  1012 => 390,  1005 => 273,  1001 => 386,  998 => 385,  992 => 821,  979 => 379,  974 => 312,  967 => 373,  962 => 803,  958 => 801,  954 => 253,  950 => 368,  945 => 367,  942 => 250,  938 => 365,  934 => 364,  927 => 361,  923 => 292,  920 => 359,  910 => 353,  901 => 347,  897 => 346,  890 => 343,  886 => 342,  883 => 272,  868 => 268,  856 => 323,  853 => 319,  849 => 318,  845 => 207,  841 => 321,  835 => 312,  830 => 249,  826 => 309,  822 => 308,  818 => 246,  813 => 306,  810 => 305,  806 => 304,  802 => 197,  795 => 118,  792 => 239,  789 => 350,  784 => 295,  782 => 187,  777 => 291,  772 => 289,  768 => 345,  763 => 343,  760 => 231,  756 => 230,  752 => 284,  745 => 281,  741 => 280,  738 => 335,  732 => 171,  719 => 279,  714 => 264,  710 => 310,  704 => 267,  699 => 103,  695 => 264,  690 => 263,  687 => 203,  683 => 251,  679 => 288,  672 => 255,  668 => 247,  665 => 201,  658 => 244,  645 => 270,  640 => 247,  634 => 238,  628 => 193,  623 => 238,  619 => 237,  611 => 18,  606 => 234,  603 => 467,  599 => 242,  595 => 231,  583 => 216,  580 => 221,  573 => 221,  560 => 101,  543 => 172,  538 => 209,  534 => 208,  530 => 202,  526 => 187,  521 => 146,  518 => 185,  514 => 183,  510 => 202,  503 => 75,  496 => 128,  490 => 193,  484 => 125,  474 => 137,  470 => 168,  446 => 133,  440 => 114,  436 => 113,  431 => 146,  425 => 126,  416 => 104,  412 => 98,  408 => 149,  403 => 170,  400 => 169,  396 => 133,  392 => 152,  385 => 24,  381 => 139,  367 => 134,  363 => 139,  359 => 92,  355 => 88,  350 => 128,  346 => 127,  343 => 115,  328 => 17,  324 => 164,  313 => 122,  307 => 88,  301 => 40,  288 => 88,  283 => 86,  271 => 76,  257 => 76,  251 => 76,  238 => 19,  233 => 68,  195 => 75,  191 => 60,  187 => 54,  183 => 64,  130 => 26,  88 => 18,  76 => 18,  115 => 27,  95 => 35,  655 => 275,  651 => 232,  648 => 231,  637 => 241,  633 => 130,  621 => 234,  618 => 233,  615 => 232,  604 => 186,  600 => 516,  588 => 116,  585 => 204,  582 => 203,  571 => 104,  567 => 178,  555 => 207,  552 => 190,  549 => 208,  544 => 199,  542 => 207,  535 => 212,  531 => 189,  519 => 189,  516 => 199,  513 => 168,  508 => 145,  506 => 83,  499 => 198,  495 => 141,  491 => 191,  481 => 162,  478 => 172,  475 => 184,  469 => 182,  456 => 136,  451 => 186,  443 => 132,  439 => 162,  427 => 169,  423 => 109,  420 => 176,  409 => 159,  405 => 148,  401 => 147,  391 => 129,  387 => 129,  384 => 132,  378 => 138,  365 => 96,  360 => 132,  348 => 21,  336 => 123,  332 => 107,  329 => 127,  323 => 81,  310 => 116,  305 => 94,  277 => 84,  274 => 91,  263 => 70,  259 => 72,  247 => 21,  244 => 91,  241 => 20,  222 => 17,  210 => 55,  207 => 53,  204 => 57,  184 => 44,  181 => 86,  167 => 80,  157 => 37,  96 => 32,  421 => 124,  417 => 137,  414 => 152,  406 => 171,  398 => 129,  393 => 100,  390 => 162,  376 => 110,  369 => 94,  366 => 91,  352 => 134,  345 => 91,  342 => 109,  331 => 106,  326 => 102,  320 => 129,  317 => 90,  314 => 117,  311 => 78,  308 => 60,  297 => 89,  293 => 89,  281 => 36,  278 => 140,  275 => 34,  264 => 31,  260 => 73,  248 => 75,  245 => 90,  242 => 67,  231 => 62,  227 => 60,  215 => 64,  212 => 54,  209 => 63,  197 => 43,  177 => 34,  171 => 66,  161 => 79,  132 => 41,  121 => 29,  105 => 29,  99 => 27,  81 => 16,  77 => 21,  180 => 35,  176 => 85,  156 => 51,  143 => 47,  139 => 46,  118 => 33,  189 => 93,  185 => 59,  173 => 43,  166 => 40,  152 => 40,  174 => 39,  164 => 80,  154 => 76,  150 => 33,  137 => 43,  133 => 36,  127 => 42,  107 => 21,  102 => 23,  83 => 22,  78 => 15,  53 => 10,  23 => 6,  42 => 8,  138 => 27,  134 => 31,  109 => 57,  103 => 55,  97 => 52,  94 => 25,  84 => 28,  75 => 17,  69 => 48,  66 => 16,  54 => 10,  44 => 8,  230 => 18,  226 => 85,  203 => 12,  193 => 97,  188 => 68,  182 => 47,  178 => 49,  168 => 46,  163 => 39,  160 => 38,  155 => 51,  148 => 39,  145 => 36,  140 => 46,  136 => 34,  125 => 39,  120 => 27,  113 => 32,  101 => 31,  92 => 25,  89 => 20,  85 => 31,  73 => 23,  62 => 13,  59 => 12,  56 => 11,  41 => 8,  126 => 35,  119 => 26,  111 => 34,  106 => 29,  98 => 36,  93 => 22,  86 => 19,  70 => 14,  60 => 18,  28 => 3,  36 => 11,  114 => 38,  104 => 26,  91 => 26,  80 => 21,  63 => 19,  58 => 12,  40 => 6,  34 => 7,  45 => 10,  61 => 45,  55 => 11,  48 => 10,  39 => 5,  35 => 7,  31 => 4,  26 => 9,  21 => 2,  46 => 13,  29 => 3,  57 => 11,  50 => 15,  47 => 10,  38 => 8,  33 => 4,  49 => 14,  32 => 7,  246 => 67,  236 => 49,  232 => 72,  225 => 87,  221 => 104,  216 => 13,  214 => 102,  211 => 79,  208 => 76,  205 => 64,  199 => 51,  196 => 50,  190 => 84,  179 => 68,  175 => 34,  172 => 48,  169 => 41,  162 => 44,  158 => 52,  153 => 37,  151 => 75,  147 => 28,  144 => 37,  141 => 35,  135 => 32,  129 => 40,  124 => 37,  117 => 35,  112 => 27,  90 => 26,  87 => 21,  82 => 20,  72 => 20,  68 => 20,  65 => 21,  52 => 15,  43 => 9,  37 => 6,  30 => 6,  27 => 2,  25 => 6,  24 => 6,  22 => 5,  19 => 1,);
    }
}

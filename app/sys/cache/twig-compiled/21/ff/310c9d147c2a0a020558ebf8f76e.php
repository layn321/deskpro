<?php

/* TwigBundle:Exception:exception.html.twig */
class __TwigTemplate_21ff310c9d147c2a0a020558ebf8f76e extends \Application\DeskPRO\Twig\Template
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
        echo "<div class=\"sf-exceptionreset\">

    <div class=\"block_exception\">
    \t";
        // line 4
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "getLastException", array(), "method"), "_dp_sn")) {
            // line 5
            echo "\t\t\t<div style=\"float: right;\">
\t\t\t\t<strong>[SN";
            // line 6
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_app_, "getLastException", array(), "method"), "_dp_sn"), "html", null, true);
            echo "]</strong>
\t\t\t</div>
\t\t";
        }
        // line 9
        echo "        <div class=\"block_exception_detected clear_fix\">
            <div class=\"text_exception\">

                <h1>
                    ";
        // line 13
        if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
        echo $this->env->getExtension('code')->formatFileFromText(strtr(twig_escape_filter($this->env, $this->getAttribute($_exception_, "message")), array("
" => "<br />")));
        echo "
                </h1>

                <div>
                    <strong>";
        // line 17
        if (isset($context["status_code"])) { $_status_code_ = $context["status_code"]; } else { $_status_code_ = null; }
        echo twig_escape_filter($this->env, $_status_code_, "html", null, true);
        echo "</strong> ";
        if (isset($context["status_text"])) { $_status_text_ = $context["status_text"]; } else { $_status_text_ = null; }
        echo twig_escape_filter($this->env, $_status_text_, "html", null, true);
        echo " - ";
        if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
        echo $this->env->getExtension('code')->abbrClass($this->getAttribute($_exception_, "class"));
        echo "
                </div>

                ";
        // line 20
        if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
        $context["previous_count"] = twig_length_filter($this->env, $this->getAttribute($_exception_, "allPrevious"));
        // line 21
        echo "                ";
        if (isset($context["previous_count"])) { $_previous_count_ = $context["previous_count"]; } else { $_previous_count_ = null; }
        if ($_previous_count_) {
            // line 22
            echo "                    <div class=\"linked\"><span><strong>";
            if (isset($context["previous_count"])) { $_previous_count_ = $context["previous_count"]; } else { $_previous_count_ = null; }
            echo twig_escape_filter($this->env, $_previous_count_, "html", null, true);
            echo "</strong> linked Exception";
            if (isset($context["previous_count"])) { $_previous_count_ = $context["previous_count"]; } else { $_previous_count_ = null; }
            echo ((($_previous_count_ > 1)) ? ("s") : (""));
            echo ":</span>
                        <ul>
                            ";
            // line 24
            if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_exception_, "allPrevious"));
            foreach ($context['_seq'] as $context["i"] => $context["previous"]) {
                // line 25
                echo "                                <li>
                                    ";
                // line 26
                if (isset($context["previous"])) { $_previous_ = $context["previous"]; } else { $_previous_ = null; }
                echo $this->env->getExtension('code')->abbrClass($this->getAttribute($_previous_, "class"));
                echo " <a href=\"#traces_link_";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, ($_i_ + 1), "html", null, true);
                echo "\" onclick=\"toggle('traces_";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, ($_i_ + 1), "html", null, true);
                echo "', 'traces'); switchIcons('icon_traces_";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, ($_i_ + 1), "html", null, true);
                echo "_open', 'icon_traces_";
                if (isset($context["i"])) { $_i_ = $context["i"]; } else { $_i_ = null; }
                echo twig_escape_filter($this->env, ($_i_ + 1), "html", null, true);
                echo "_close');\">&raquo;</a>
                                </li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['i'], $context['previous'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 29
            echo "                        </ul>
                    </div>
                ";
        }
        // line 32
        echo "
            </div>
        </div>
    </div>

    ";
        // line 37
        if (isset($context["exception"])) { $_exception_ = $context["exception"]; } else { $_exception_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_exception_, "toarray"));
        foreach ($context['_seq'] as $context["position"] => $context["e"]) {
            // line 38
            echo "        ";
            if (isset($context["e"])) { $_e_ = $context["e"]; } else { $_e_ = null; }
            if (isset($context["position"])) { $_position_ = $context["position"]; } else { $_position_ = null; }
            if (isset($context["previous_count"])) { $_previous_count_ = $context["previous_count"]; } else { $_previous_count_ = null; }
            $this->env->loadTemplate("TwigBundle:Exception:traces.html.twig")->display(array("exception" => $_e_, "position" => $_position_, "count" => $_previous_count_));
            // line 39
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['position'], $context['e'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 40
        echo "
    ";
        // line 41
        if (isset($context["logger"])) { $_logger_ = $context["logger"]; } else { $_logger_ = null; }
        if ($_logger_) {
            // line 42
            echo "        <div class=\"block\">
            <div class=\"logs clear_fix\">
                ";
            // line 44
            ob_start();
            // line 45
            echo "                <h2>
                    Logs&nbsp;
                    <a href=\"#\" onclick=\"toggle('logs'); switchIcons('icon_logs_open', 'icon_logs_close'); return false;\">
                        <img class=\"toggle\" id=\"icon_logs_open\" alt=\"+\" src=\"";
            // line 48
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/framework/images/blue_picto_more.gif"), "html", null, true);
            echo "\" style=\"visibility: hidden\" />
                        <img class=\"toggle\" id=\"icon_logs_close\" alt=\"-\" src=\"";
            // line 49
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/framework/images/blue_picto_less.gif"), "html", null, true);
            echo "\" style=\"visibility: visible; margin-left: -18px\" />
                    </a>
                </h2>
                ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
            // line 53
            echo "
                ";
            // line 54
            if (isset($context["logger"])) { $_logger_ = $context["logger"]; } else { $_logger_ = null; }
            if ($this->getAttribute($_logger_, "counterrors")) {
                // line 55
                echo "                    <div class=\"error_count\">
                        <span>
                            ";
                // line 57
                if (isset($context["logger"])) { $_logger_ = $context["logger"]; } else { $_logger_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_logger_, "counterrors"), "html", null, true);
                echo " error";
                if (isset($context["logger"])) { $_logger_ = $context["logger"]; } else { $_logger_ = null; }
                echo ((($this->getAttribute($_logger_, "counterrors") > 1)) ? ("s") : (""));
                echo "
                        </span>
                    </div>
                ";
            }
            // line 61
            echo "
            </div>

            <div id=\"logs\">
                ";
            // line 65
            if (isset($context["logger"])) { $_logger_ = $context["logger"]; } else { $_logger_ = null; }
            $this->env->loadTemplate("TwigBundle:Exception:logs.html.twig")->display(array("logs" => $this->getAttribute($_logger_, "logs")));
            // line 66
            echo "            </div>

        </div>
    ";
        }
        // line 70
        echo "
    ";
        // line 71
        if (isset($context["currentContent"])) { $_currentContent_ = $context["currentContent"]; } else { $_currentContent_ = null; }
        if ($_currentContent_) {
            // line 72
            echo "        <div class=\"block\">
            ";
            // line 73
            ob_start();
            // line 74
            echo "            <h2>
                Content of the Output&nbsp;
                <a href=\"#\" onclick=\"toggle('output_content'); switchIcons('icon_content_open', 'icon_content_close'); return false;\">
                    <img class=\"toggle\" id=\"icon_content_close\" alt=\"-\" src=\"";
            // line 77
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/framework/images/blue_picto_less.gif"), "html", null, true);
            echo "\" style=\"visibility: hidden\" />
                    <img class=\"toggle\" id=\"icon_content_open\" alt=\"+\" src=\"";
            // line 78
            echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/framework/images/blue_picto_more.gif"), "html", null, true);
            echo "\" style=\"visibility: visible; margin-left: -18px\" />
                </a>
            </h2>
            ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
            // line 82
            echo "
            <div id=\"output_content\" style=\"display: none\">
                ";
            // line 84
            if (isset($context["currentContent"])) { $_currentContent_ = $context["currentContent"]; } else { $_currentContent_ = null; }
            echo twig_escape_filter($this->env, $_currentContent_, "html", null, true);
            echo "
            </div>

            <div style=\"clear: both\"></div>
        </div>
    ";
        }
        // line 90
        echo "
</div>

<script type=\"text/javascript\">//<![CDATA[
    function toggle(id, clazz) {
        var el = document.getElementById(id),
            current = el.style.display,
            i;

        if (clazz) {
            var tags = document.getElementsByTagName('*');
            for (i = tags.length - 1; i >= 0 ; i--) {
                if (tags[i].className === clazz) {
                    tags[i].style.display = 'none';
                }
            }
        }

        el.style.display = current === 'none' ? 'block' : 'none';
    }

    function switchIcons(id1, id2) {
        var icon1, icon2, visibility1, visibility2;

        icon1 = document.getElementById(id1);
        icon2 = document.getElementById(id2);

        visibility1 = icon1.style.visibility;
        visibility2 = icon2.style.visibility;

        icon1.style.visibility = visibility2;
        icon2.style.visibility = visibility1;
    }
//]]></script>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  246 => 90,  236 => 84,  232 => 82,  225 => 78,  221 => 77,  216 => 74,  214 => 73,  211 => 72,  208 => 71,  205 => 70,  199 => 66,  196 => 65,  190 => 61,  179 => 57,  175 => 55,  172 => 54,  169 => 53,  162 => 49,  158 => 48,  153 => 45,  151 => 44,  147 => 42,  144 => 41,  141 => 40,  135 => 39,  129 => 38,  124 => 37,  117 => 32,  112 => 29,  90 => 26,  87 => 25,  82 => 24,  72 => 22,  68 => 21,  65 => 20,  52 => 17,  43 => 13,  37 => 9,  30 => 6,  27 => 5,  25 => 3,  24 => 4,  22 => 2,  19 => 1,);
    }
}

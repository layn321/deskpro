<?php

/* AgentBundle:Ticket:view-page-display-holders.html.twig */
class __TwigTemplate_3f68c624c6c6d4a34685c4d51fc31d5b extends \Application\DeskPRO\Twig\Template
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
        if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
        if ((!$_baseId_)) {
            $context["baseId"] = $this->env->getExtension('deskpro_templating')->elUid();
        }
        // line 2
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($this->getAttribute($_app_, "languages"), "isMultiLang", array(), "method")) {
            // line 3
            echo "\t<tbody class=\"ticket_language language_id standard-field item-on ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($this->getAttribute($_ticket_, "language"), "title"))) {
                echo "no-value";
            }
            echo "\" data-item-type=\"ticket_language\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"language_id label\">";
            // line 6
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<span class=\"prop-val language_id\">";
            // line 9
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ($this->getAttribute($_ticket_, "language")) {
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->getPhraseObject($this->getAttribute($_ticket_, "language")), "html", null, true);
            } else {
                echo "None";
            }
            echo "</span>
\t\t\t</td>
\t\t</tr>
\t\t<tr class=\"mode-edit\">
\t\t\t<th width=\"80\">";
            // line 13
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.language");
            echo ":</th>
\t\t\t<td>
\t\t\t\t";
            // line 15
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            $this->env->loadTemplate("AgentBundle:Common:select-language.html.twig")->display(array_merge($context, array("name" => "ticket[language_id]", "id" => ($_baseId_ . "_ticket_language_id"), "selected" => $this->getAttribute($this->getAttribute($_ticket_, "language"), "id"), "languages" => $this->getAttribute($this->getAttribute($_app_, "languages"), "getAll", array(), "method"), "add_classname" => "prop-input-language_id", "with_blank" => true, "add_attr" => "data-prop-id=\"language_id\"")));
            // line 24
            echo "\t\t\t</td>
\t\t</tr>
\t</tbody>
";
        }
        // line 28
        echo "
";
        // line 30
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_category"), "method")) {
            // line 31
            echo "\t<tbody class=\"ticket_category category_id standard-field item ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($_ticket_, "category"))) {
                echo "no-value";
            }
            echo "\" data-item-type=\"ticket_category\" style=\"display:none\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"category_id label\">";
            // line 34
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<span class=\"prop-val category_id\">";
            // line 37
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "full_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "category", array(), "any", false, true), "full_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t\t\t</td>
\t\t</tr>
\t\t<tr class=\"mode-edit\">
\t\t\t<th width=\"80\">";
            // line 41
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.category");
            echo ":</th>
\t\t\t<td>
\t\t\t\t<select name=\"ticket[category_id]\" id=\"";
            // line 43
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ticket_category_id\" data-prop-id=\"category_id\" class=\"prop-input-category_id\">
\t\t\t\t\t<option value=\"0\">";
            // line 44
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
            echo "</option>
\t\t\t\t\t";
            // line 45
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_options_, "ticket_categories_hierarchy"));
            foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
                // line 46
                echo "\t\t\t\t\t\t";
                if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_cat_, "children"))) {
                    // line 47
                    echo "\t\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t";
                    // line 48
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_cat_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subcat"]) {
                        // line 49
                        echo "\t\t\t\t\t\t\t\t\t<option ";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        if (($this->getAttribute($this->getAttribute($_ticket_, "category"), "id") == $this->getAttribute($_subcat_, "id"))) {
                            echo "selected=\"selected\"";
                        }
                        echo " value=\"";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "id"), "html", null, true);
                        echo "\" data-full-title=\"";
                        if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                        echo " &gt; ";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                        echo "\">";
                        if (isset($context["subcat"])) { $_subcat_ = $context["subcat"]; } else { $_subcat_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subcat_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcat'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 51
                    echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                } else {
                    // line 53
                    echo "\t\t\t\t\t\t\t<option ";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    if (($this->getAttribute($this->getAttribute($_ticket_, "category"), "id") == $this->getAttribute($_cat_, "id"))) {
                        echo "selected=\"selected\"";
                    }
                    echo " value=\"";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->env->getExtension('deskpro_templating')->strRepeat("--", $this->getAttribute($_cat_, "depth")), "html", null, true);
                    echo " ";
                    if (isset($context["cat"])) { $_cat_ = $context["cat"]; } else { $_cat_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_cat_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t";
                }
                // line 55
                echo "\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 56
            echo "\t\t\t\t</select>
\t\t\t</td>
\t\t</tr>
\t</tbody>
";
        }
        // line 61
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_product"), "method")) {
            // line 62
            echo "\t<tbody class=\"ticket_product standard-field item ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($_ticket_, "product"))) {
                echo "no-value";
            }
            echo "\" data-item-type=\"product\" style=\"display:none\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"product label\">";
            // line 65
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<span class=\"prop-val product\">";
            // line 68
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "full_title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "product", array(), "any", false, true), "full_title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t\t\t</td>
\t\t</tr>
\t\t";
            // line 71
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
            if ((($this->getAttribute($_ticket_, "product") && $this->getAttribute($_app_, "getSetting", array(0 => "core.use_product_fields"), "method")) && twig_length_filter($this->env, $this->getAttribute($this->getAttribute($_ticket_, "product"), "custom_data")))) {
                // line 72
                echo "\t\t\t";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                $context["product_field_data"] = $this->getAttribute($this->getAttribute($_ticket_, "product"), "getFieldDisplayArray", array(), "method");
                // line 73
                echo "\t\t\t";
                if (isset($context["product_field_data"])) { $_product_field_data_ = $context["product_field_data"]; } else { $_product_field_data_ = null; }
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($_product_field_data_);
                foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    if ($this->getAttribute($this->getAttribute($_ticket_, "product"), "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method")) {
                        // line 74
                        echo "\t\t\t\t<tr class=\"mode-display\">
\t\t\t\t\t<th width=\"80\">";
                        // line 75
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                        echo "</th>
\t\t\t\t\t<td>";
                        // line 76
                        if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                        echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                        echo "</td>
\t\t\t\t</tr>
\t\t\t";
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 79
                echo "\t\t";
            }
            // line 80
            echo "\t\t<tr class=\"mode-edit\">
\t\t\t<th width=\"80\">";
            // line 81
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.product");
            echo ":</th>
\t\t\t<td>
\t\t\t\t<select name=\"ticket[product_id]\" id=\"";
            // line 83
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ticket_product_id\" data-prop-id=\"product\" class=\"prop-input-product\">
\t\t\t\t\t<option value=\"0\">";
            // line 84
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
            echo "</option>
\t\t\t\t\t";
            // line 85
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_options_, "products_hierarchy"));
            foreach ($context['_seq'] as $context["_key"] => $context["prod"]) {
                // line 86
                echo "\t\t\t\t\t\t";
                if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                if (twig_length_filter($this->env, $this->getAttribute($_prod_, "children"))) {
                    // line 87
                    echo "\t\t\t\t\t\t\t<optgroup label=\"";
                    if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_prod_, "title"), "html", null, true);
                    echo "\">
\t\t\t\t\t\t\t\t";
                    // line 88
                    if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($_prod_, "children"));
                    foreach ($context['_seq'] as $context["_key"] => $context["subprod"]) {
                        // line 89
                        echo "\t\t\t\t\t\t\t\t\t<option ";
                        if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                        if (isset($context["subprod"])) { $_subprod_ = $context["subprod"]; } else { $_subprod_ = null; }
                        if (($this->getAttribute($this->getAttribute($_ticket_, "product"), "id") == $this->getAttribute($_subprod_, "id"))) {
                            echo "selected=\"selected\"";
                        }
                        echo " data-full-title=\"";
                        if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_prod_, "title"), "html", null, true);
                        echo " &gt; ";
                        if (isset($context["subprod"])) { $_subprod_ = $context["subprod"]; } else { $_subprod_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subprod_, "title"), "html", null, true);
                        echo "\" value=\"";
                        if (isset($context["subprod"])) { $_subprod_ = $context["subprod"]; } else { $_subprod_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subprod_, "id"), "html", null, true);
                        echo "\">";
                        if (isset($context["subprod"])) { $_subprod_ = $context["subprod"]; } else { $_subprod_ = null; }
                        echo twig_escape_filter($this->env, $this->getAttribute($_subprod_, "title"), "html", null, true);
                        echo "</option>
\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subprod'], $context['_parent'], $context['loop']);
                    $context = array_merge($_parent, array_intersect_key($context, $_parent));
                    // line 91
                    echo "\t\t\t\t\t\t\t</optgroup>
\t\t\t\t\t\t";
                } else {
                    // line 93
                    echo "\t\t\t\t\t\t\t<option ";
                    if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                    if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                    if (($this->getAttribute($this->getAttribute($_ticket_, "product"), "id") == $this->getAttribute($_prod_, "id"))) {
                        echo "selected=\"selected\"";
                    }
                    echo " value=\"";
                    if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_prod_, "id"), "html", null, true);
                    echo "\">";
                    if (isset($context["prod"])) { $_prod_ = $context["prod"]; } else { $_prod_ = null; }
                    echo twig_escape_filter($this->env, $this->getAttribute($_prod_, "title"), "html", null, true);
                    echo "</option>
\t\t\t\t\t\t";
                }
                // line 95
                echo "\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['prod'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 96
            echo "\t\t\t\t</select>
\t\t\t</td>
\t\t</tr>
\t</tbody>
";
        }
        // line 101
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_priority"), "method")) {
            // line 102
            echo "\t<tbody class=\"ticket_priority priority_id standard-field item ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($_ticket_, "priority"))) {
                echo "no-value";
            }
            echo "\" data-item-type=\"ticket_priority\" style=\"display:none\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"priority_id label\">";
            // line 105
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<span class=\"prop-val priority_id\">";
            // line 108
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "priority", array(), "any", false, true), "title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t\t\t</td>
\t\t</tr>
\t\t<tr class=\"mode-edit\">
\t\t\t<th width=\"80\">";
            // line 112
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.priority");
            echo ":</th>
\t\t\t<td>
\t\t\t\t<select name=\"ticket[priority_id]\" id=\"";
            // line 114
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ticket_priority_id\" data-prop-id=\"priority_id\" class=\"prop-input-priority_id\">
\t\t\t\t\t<option value=\"0\">";
            // line 115
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
            echo "</option>
\t\t\t\t\t";
            // line 116
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_options_, "priorities"));
            foreach ($context['_seq'] as $context["id"] => $context["pri"]) {
                // line 117
                echo "\t\t\t\t\t\t<option ";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if (($this->getAttribute($this->getAttribute($_ticket_, "priority"), "id") == $_id_)) {
                    echo "selected=\"selected\"";
                }
                echo " value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["pri"])) { $_pri_ = $context["pri"]; } else { $_pri_ = null; }
                echo twig_escape_filter($this->env, $_pri_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['pri'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 119
            echo "\t\t\t\t</select>
\t\t\t</td>
\t\t</tr>
\t</tbody>
";
        }
        // line 124
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        if ($this->getAttribute($_app_, "getSetting", array(0 => "core.use_ticket_workflow"), "method")) {
            // line 125
            echo "\t<tbody class=\"ticket_workflow workflow_id standard-field item ";
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if ((!$this->getAttribute($_ticket_, "workflow"))) {
                echo "no-value";
            }
            echo "\" data-item-type=\"ticket_workflow\" style=\"display:none\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"workflow_id label\">";
            // line 128
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<span class=\"prop-val workflow_id\">";
            // line 131
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($_ticket_, "workflow", array(), "any", false, true), "title"), $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))) : ($this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none"))), "html", null, true);
            echo "</span>
\t\t\t</td>
\t\t</tr>
\t\t<tr class=\"mode-edit\">
\t\t\t<th width=\"80\">";
            // line 135
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.workflow");
            echo ":</th>
\t\t\t<td>
\t\t\t\t<select name=\"ticket[workflow_id]\" id=\"";
            // line 137
            if (isset($context["baseId"])) { $_baseId_ = $context["baseId"]; } else { $_baseId_ = null; }
            echo twig_escape_filter($this->env, $_baseId_, "html", null, true);
            echo "_ticket_workflow_id\" data-prop-id=\"workflow_id\" class=\"prop-input-workflow_id\">
\t\t\t\t\t<option value=\"0\">";
            // line 138
            echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
            echo "</option>
\t\t\t\t\t";
            // line 139
            if (isset($context["ticket_options"])) { $_ticket_options_ = $context["ticket_options"]; } else { $_ticket_options_ = null; }
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($_ticket_options_, "ticket_workflows"));
            foreach ($context['_seq'] as $context["id"] => $context["work"]) {
                // line 140
                echo "\t\t\t\t\t\t<option ";
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                if (($this->getAttribute($this->getAttribute($_ticket_, "workflow"), "id") == $_id_)) {
                    echo "selected=\"selected\"";
                }
                echo " value=\"";
                if (isset($context["id"])) { $_id_ = $context["id"]; } else { $_id_ = null; }
                echo twig_escape_filter($this->env, $_id_, "html", null, true);
                echo "\">";
                if (isset($context["work"])) { $_work_ = $context["work"]; } else { $_work_ = null; }
                echo twig_escape_filter($this->env, $_work_, "html", null, true);
                echo "</option>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['id'], $context['work'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 142
            echo "\t\t\t\t</select>
\t\t\t</td>
\t\t</tr>
\t</tbody>
";
        }
        // line 147
        echo "
";
        // line 149
        if (isset($context["custom_fields"])) { $_custom_fields_ = $context["custom_fields"]; } else { $_custom_fields_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_custom_fields_);
        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
            // line 150
            echo "\t<tbody class=\"ticket_field_";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo " custom-field item ";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (($this->getAttribute($this->getAttribute($_f_, "field_def"), "isFormField", array(), "method") && (!$this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method")))) {
                echo "no-value";
            }
            echo "\" data-field-type=\"ticket_field\" data-item-id=\"";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo "\" data-custom-field-handler=\"";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "field_handler"), "html", null, true);
            echo "\" style=\"display:none\">
\t\t<tr class=\"mode-display\">
\t\t\t<th width=\"80\">
\t\t\t\t<span class=\"custom-field edit-trigger ticket_field_";
            // line 153
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo "\" data-item-id=\"";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo "\">";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
            echo ":</span>
\t\t\t</th>
\t\t\t<td>
\t\t\t\t<div class=\"custom-field content ticket_field_";
            // line 156
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo "\" data-item-id=\"";
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
            echo "\">
\t\t\t\t\t";
            // line 157
            if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            if ((!$this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method"))) {
                // line 158
                echo "\t\t\t\t\t\t";
                echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                echo "
\t\t\t\t\t";
            } else {
                // line 160
                echo "\t\t\t\t\t\t";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                echo "
\t\t\t\t\t";
            }
            // line 162
            echo "\t\t\t\t</div>
\t\t\t</td>
\t\t</tr>
\t\t<tr class=\"mode-edit\">
\t\t\t";
            // line 166
            if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
            if ($this->getAttribute($this->getAttribute($_f_, "field_def"), "isFormField", array(), "method")) {
                // line 167
                echo "\t\t\t\t<th width=\"80\">";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                echo ":</th>
\t\t\t\t<td>
\t\t\t\t\t<div class=\"custom-field field-input ticket_field_";
                // line 169
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\" data-item-id=\"";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\">
\t\t\t\t\t\t";
                // line 170
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo $this->env->getExtension('deskpro_templating')->renderCustomFieldForm($_f_);
                echo "
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t";
            } else {
                // line 174
                echo "\t\t\t\t<td>
\t\t\t\t\t<span class=\"custom-field edit-trigger ticket_field_";
                // line 175
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\" data-item-id=\"";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\">";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "title"), "html", null, true);
                echo ":</span>
\t\t\t\t</td>
\t\t\t\t<td>
\t\t\t\t\t<div class=\"custom-field content ticket_field_";
                // line 178
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\" data-item-id=\"";
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($_f_, "id"), "html", null, true);
                echo "\">
\t\t\t\t\t\t";
                // line 179
                if (isset($context["ticket"])) { $_ticket_ = $context["ticket"]; } else { $_ticket_ = null; }
                if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                if ((!$this->getAttribute($_ticket_, "hasCustomField", array(0 => $this->getAttribute($_f_, "id")), "method"))) {
                    // line 180
                    echo "\t\t\t\t\t\t\t";
                    echo $this->env->getExtension('deskpro_templating')->getPhrase($context, "agent.general.none");
                    echo "
\t\t\t\t\t\t";
                } else {
                    // line 182
                    echo "\t\t\t\t\t\t\t";
                    if (isset($context["f"])) { $_f_ = $context["f"]; } else { $_f_ = null; }
                    echo $this->env->getExtension('deskpro_templating')->renderCustomField($_f_);
                    echo "
\t\t\t\t\t\t";
                }
                // line 184
                echo "\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t";
            }
            // line 187
            echo "\t\t</tr>
\t</tbody>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
    }

    public function getTemplateName()
    {
        return "AgentBundle:Ticket:view-page-display-holders.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1330 => 4,  1327 => 3,  1322 => 2,  1290 => 297,  1283 => 295,  1278 => 292,  1271 => 290,  1230 => 277,  1190 => 265,  1166 => 258,  1138 => 251,  642 => 147,  1264 => 464,  1259 => 462,  1227 => 448,  1211 => 446,  1399 => 568,  1387 => 560,  1358 => 555,  1334 => 553,  1316 => 529,  1298 => 520,  1195 => 478,  1148 => 253,  1120 => 453,  1117 => 245,  1093 => 440,  788 => 316,  612 => 203,  1394 => 446,  1385 => 442,  1382 => 441,  1369 => 433,  1363 => 432,  1360 => 431,  1350 => 429,  1305 => 523,  1300 => 409,  1281 => 404,  1277 => 403,  1265 => 398,  1255 => 396,  1247 => 393,  1234 => 387,  1202 => 270,  1199 => 374,  1187 => 264,  1162 => 365,  1136 => 461,  1128 => 352,  1122 => 248,  1069 => 332,  968 => 387,  846 => 188,  1183 => 449,  1132 => 460,  1097 => 341,  957 => 356,  907 => 278,  875 => 263,  653 => 176,  1329 => 405,  1309 => 401,  1303 => 399,  1297 => 398,  1292 => 397,  1280 => 394,  1249 => 458,  1237 => 279,  1205 => 409,  1200 => 408,  1194 => 440,  1178 => 430,  1175 => 447,  1170 => 375,  1135 => 362,  1105 => 428,  1078 => 335,  1068 => 388,  1048 => 417,  961 => 207,  922 => 280,  750 => 245,  842 => 263,  1038 => 319,  904 => 198,  882 => 194,  831 => 267,  860 => 314,  790 => 284,  733 => 230,  707 => 283,  744 => 220,  873 => 349,  824 => 267,  762 => 243,  713 => 225,  578 => 158,  2739 => 1153,  2735 => 1152,  2732 => 1151,  2725 => 1146,  2712 => 1144,  2707 => 1143,  2692 => 1131,  2688 => 1130,  2683 => 1127,  2680 => 1126,  2677 => 1125,  2671 => 1121,  2658 => 1119,  2653 => 1118,  2645 => 1113,  2641 => 1112,  2636 => 1109,  2632 => 1108,  2629 => 1107,  2626 => 1106,  2606 => 1089,  2602 => 1088,  2598 => 1087,  2594 => 1086,  2590 => 1085,  2586 => 1084,  2572 => 1073,  2568 => 1072,  2564 => 1071,  2560 => 1070,  2556 => 1069,  2552 => 1068,  2537 => 1056,  2533 => 1055,  2529 => 1054,  2525 => 1053,  2521 => 1052,  2503 => 1040,  2499 => 1039,  2495 => 1038,  2491 => 1037,  2487 => 1036,  2483 => 1035,  2469 => 1024,  2465 => 1023,  2461 => 1022,  2457 => 1021,  2453 => 1020,  2449 => 1019,  2426 => 999,  2422 => 998,  2418 => 997,  2414 => 996,  2410 => 995,  2406 => 994,  2392 => 983,  2388 => 982,  2384 => 981,  2380 => 980,  2376 => 979,  2372 => 978,  2358 => 967,  2354 => 966,  2350 => 965,  2346 => 964,  2342 => 963,  2338 => 962,  2324 => 951,  2320 => 950,  2316 => 949,  2312 => 948,  2308 => 947,  2304 => 946,  2295 => 939,  2282 => 937,  2277 => 936,  2269 => 931,  2265 => 930,  2260 => 927,  2256 => 926,  2253 => 925,  2230 => 901,  2220 => 899,  2216 => 898,  2211 => 896,  2208 => 895,  2198 => 893,  2194 => 892,  2189 => 890,  2184 => 889,  2177 => 885,  2173 => 884,  2164 => 877,  2151 => 875,  2146 => 874,  2143 => 873,  2141 => 864,  2134 => 860,  2130 => 859,  2122 => 853,  2120 => 852,  2113 => 848,  2105 => 846,  2096 => 839,  2083 => 837,  2078 => 836,  2073 => 834,  2067 => 830,  2062 => 828,  2058 => 827,  2054 => 826,  2050 => 825,  2045 => 824,  2038 => 822,  2034 => 821,  2022 => 814,  2009 => 812,  1999 => 809,  1993 => 805,  1988 => 803,  1984 => 802,  1980 => 801,  1976 => 800,  1971 => 799,  1968 => 798,  1964 => 797,  1960 => 796,  1954 => 793,  1946 => 788,  1942 => 787,  1938 => 786,  1932 => 782,  1927 => 780,  1919 => 778,  1915 => 777,  1911 => 776,  1906 => 775,  1903 => 774,  1899 => 773,  1895 => 772,  1886 => 768,  1883 => 764,  1871 => 759,  1866 => 756,  1863 => 755,  1842 => 752,  1825 => 749,  1818 => 748,  1815 => 747,  1800 => 743,  1793 => 741,  1772 => 729,  1761 => 721,  1757 => 720,  1751 => 717,  1740 => 709,  1719 => 697,  1715 => 696,  1686 => 673,  1676 => 669,  1665 => 661,  1661 => 660,  1645 => 651,  1638 => 649,  1630 => 645,  1625 => 642,  1622 => 641,  1598 => 637,  1577 => 634,  1558 => 629,  1549 => 627,  1546 => 626,  1535 => 618,  1531 => 617,  1514 => 606,  1510 => 605,  1504 => 602,  1493 => 594,  1483 => 590,  1464 => 580,  1458 => 577,  1443 => 568,  1437 => 565,  1426 => 557,  1405 => 545,  1401 => 544,  1395 => 541,  1390 => 538,  1381 => 533,  1373 => 531,  1348 => 521,  1343 => 520,  1335 => 515,  1321 => 510,  1318 => 549,  1299 => 503,  1294 => 298,  1282 => 496,  1269 => 466,  1260 => 397,  1240 => 478,  1221 => 484,  1216 => 378,  1210 => 272,  1206 => 445,  1193 => 467,  1189 => 466,  1155 => 468,  1150 => 419,  1022 => 312,  1006 => 299,  988 => 398,  969 => 392,  965 => 294,  921 => 286,  878 => 275,  866 => 349,  854 => 254,  819 => 322,  796 => 286,  1713 => 683,  1700 => 681,  1685 => 674,  1662 => 670,  1643 => 669,  1640 => 668,  1631 => 663,  1618 => 661,  1613 => 660,  1608 => 639,  1605 => 656,  1602 => 655,  1589 => 645,  1586 => 644,  1583 => 643,  1554 => 613,  1547 => 608,  1521 => 602,  1508 => 600,  1499 => 598,  1491 => 593,  1482 => 587,  1472 => 582,  1468 => 581,  1465 => 579,  1457 => 573,  1445 => 566,  1422 => 556,  1416 => 553,  1410 => 548,  1397 => 546,  1392 => 545,  1386 => 541,  1377 => 532,  1370 => 530,  1357 => 528,  1352 => 554,  1341 => 428,  1314 => 510,  1311 => 1,  1275 => 493,  1248 => 284,  1238 => 488,  1225 => 476,  1220 => 275,  1209 => 466,  1185 => 385,  1182 => 431,  1159 => 421,  1154 => 254,  1130 => 438,  1125 => 407,  1101 => 240,  1074 => 432,  1056 => 326,  1046 => 323,  1043 => 225,  1030 => 397,  1027 => 412,  947 => 361,  925 => 352,  913 => 324,  893 => 196,  881 => 267,  847 => 343,  829 => 336,  825 => 259,  1083 => 237,  995 => 399,  984 => 350,  963 => 292,  941 => 354,  851 => 367,  682 => 217,  1365 => 556,  1359 => 545,  1349 => 539,  1336 => 427,  1331 => 424,  1323 => 420,  1319 => 530,  1312 => 402,  1284 => 475,  1272 => 401,  1268 => 509,  1261 => 288,  1251 => 285,  1245 => 483,  1231 => 496,  1207 => 271,  1197 => 267,  1180 => 484,  1173 => 457,  1169 => 259,  1157 => 363,  1147 => 438,  1109 => 330,  1065 => 440,  1059 => 423,  1047 => 385,  1044 => 424,  1033 => 381,  1009 => 357,  991 => 363,  987 => 404,  973 => 294,  931 => 202,  924 => 287,  911 => 347,  906 => 81,  885 => 337,  872 => 335,  855 => 332,  749 => 240,  701 => 221,  594 => 180,  1163 => 257,  1143 => 252,  1087 => 420,  1077 => 433,  1051 => 325,  1037 => 223,  1010 => 301,  999 => 407,  932 => 352,  899 => 306,  895 => 404,  933 => 387,  914 => 133,  909 => 323,  833 => 284,  783 => 315,  755 => 303,  666 => 214,  453 => 168,  639 => 209,  568 => 176,  520 => 232,  657 => 216,  572 => 201,  609 => 202,  20 => 1,  659 => 217,  562 => 164,  548 => 180,  558 => 197,  479 => 157,  589 => 154,  457 => 199,  413 => 174,  953 => 206,  948 => 379,  935 => 394,  929 => 283,  916 => 382,  864 => 365,  844 => 338,  816 => 342,  807 => 318,  801 => 268,  774 => 257,  766 => 312,  737 => 297,  685 => 218,  664 => 225,  635 => 249,  593 => 199,  546 => 201,  532 => 236,  865 => 191,  852 => 241,  838 => 285,  820 => 182,  781 => 327,  764 => 173,  725 => 250,  632 => 268,  602 => 175,  565 => 183,  529 => 153,  505 => 147,  487 => 101,  473 => 212,  1853 => 842,  1849 => 753,  1839 => 751,  1810 => 820,  1803 => 819,  1799 => 818,  1782 => 733,  1778 => 732,  1770 => 807,  1749 => 795,  1738 => 793,  1728 => 789,  1721 => 788,  1717 => 787,  1709 => 693,  1705 => 781,  1688 => 675,  1681 => 769,  1677 => 768,  1667 => 671,  1656 => 762,  1648 => 655,  1644 => 756,  1636 => 750,  1624 => 744,  1617 => 743,  1606 => 738,  1588 => 732,  1570 => 726,  1545 => 719,  1534 => 606,  1527 => 713,  1509 => 707,  1500 => 701,  1496 => 700,  1489 => 593,  1474 => 688,  1456 => 682,  1449 => 681,  1438 => 676,  1431 => 675,  1420 => 670,  1413 => 669,  1404 => 663,  1400 => 662,  1383 => 540,  1379 => 557,  1356 => 632,  1351 => 631,  1338 => 621,  1332 => 617,  1315 => 613,  1302 => 302,  1289 => 406,  1276 => 604,  1263 => 601,  1244 => 456,  1241 => 595,  1236 => 594,  1218 => 578,  1215 => 577,  1191 => 373,  1181 => 561,  1171 => 446,  1158 => 550,  1114 => 428,  1104 => 465,  1072 => 333,  1062 => 7,  1024 => 395,  1014 => 218,  1000 => 368,  990 => 303,  980 => 393,  960 => 466,  918 => 285,  888 => 80,  834 => 325,  673 => 178,  636 => 145,  462 => 142,  454 => 120,  1144 => 463,  1139 => 356,  1131 => 250,  1127 => 434,  1110 => 243,  1092 => 459,  1089 => 239,  1086 => 238,  1084 => 337,  1063 => 232,  1060 => 231,  1055 => 230,  1050 => 227,  1035 => 372,  1019 => 330,  1003 => 401,  959 => 387,  900 => 366,  880 => 276,  870 => 277,  867 => 334,  859 => 294,  848 => 271,  839 => 376,  828 => 302,  823 => 183,  809 => 179,  800 => 241,  797 => 267,  794 => 177,  786 => 283,  740 => 194,  734 => 192,  703 => 228,  693 => 297,  630 => 166,  626 => 142,  614 => 139,  610 => 236,  581 => 206,  564 => 149,  525 => 138,  722 => 226,  697 => 282,  674 => 270,  671 => 285,  577 => 180,  569 => 187,  557 => 179,  502 => 187,  497 => 76,  445 => 163,  729 => 306,  684 => 237,  676 => 179,  669 => 268,  660 => 203,  647 => 175,  643 => 229,  601 => 195,  570 => 129,  522 => 156,  501 => 147,  296 => 108,  374 => 115,  631 => 207,  616 => 198,  608 => 194,  605 => 193,  596 => 134,  574 => 180,  561 => 126,  527 => 165,  433 => 183,  388 => 98,  426 => 172,  383 => 105,  461 => 137,  370 => 105,  395 => 166,  294 => 88,  223 => 71,  220 => 67,  492 => 129,  468 => 144,  444 => 149,  410 => 104,  397 => 136,  377 => 121,  262 => 80,  250 => 47,  5659 => 53,  5653 => 52,  5642 => 51,  5628 => 48,  5623 => 47,  5617 => 46,  5606 => 45,  5588 => 42,  5579 => 41,  5570 => 40,  5561 => 39,  5552 => 38,  5543 => 37,  5534 => 36,  5525 => 35,  5516 => 34,  5507 => 33,  5498 => 32,  5489 => 31,  5486 => 30,  5482 => 29,  5478 => 28,  5467 => 27,  5450 => 24,  5442 => 23,  5434 => 22,  5428 => 21,  5421 => 20,  5407 => 19,  5403 => 4677,  5358 => 4636,  5231 => 4513,  5166 => 4452,  5063 => 4352,  5024 => 4316,  5014 => 4309,  4973 => 4271,  4941 => 4242,  4892 => 4196,  4827 => 4135,  4812 => 4124,  4774 => 4090,  4759 => 4078,  4748 => 4070,  4734 => 4060,  4679 => 4009,  4629 => 3963,  4615 => 3953,  4573 => 3915,  4561 => 3906,  4475 => 3824,  4410 => 3763,  4382 => 3739,  4368 => 3729,  4361 => 3726,  4353 => 3722,  4346 => 3719,  4338 => 3715,  4331 => 3712,  4302 => 3687,  4280 => 3669,  4257 => 3650,  4248 => 3645,  4223 => 3624,  4211 => 3616,  4175 => 3584,  4066 => 3478,  4062 => 3477,  4040 => 3458,  4034 => 3455,  4021 => 3445,  4015 => 3442,  4009 => 3439,  3967 => 3400,  3960 => 3396,  3866 => 3305,  3810 => 3252,  3709 => 3154,  3696 => 3145,  3688 => 3140,  3659 => 3115,  3650 => 3110,  3553 => 3017,  3548 => 3016,  3543 => 3015,  3538 => 3014,  3533 => 3013,  3528 => 3012,  3523 => 3011,  3518 => 3010,  3513 => 3009,  3508 => 3008,  3503 => 3007,  3498 => 3006,  3493 => 3005,  3488 => 3004,  3483 => 3003,  3478 => 3002,  3470 => 2998,  3447 => 2978,  3423 => 2958,  3370 => 2909,  3234 => 2776,  3148 => 2694,  3130 => 2679,  3124 => 2676,  3040 => 2596,  2963 => 2522,  2861 => 2424,  2853 => 2420,  2820 => 2391,  2803 => 2377,  2799 => 2376,  2794 => 2375,  2649 => 2234,  2625 => 2213,  2517 => 1051,  2498 => 2092,  2482 => 2079,  2472 => 2072,  2464 => 2067,  2450 => 2056,  2446 => 2055,  2436 => 2049,  2428 => 2045,  2378 => 1998,  2303 => 1926,  2293 => 1919,  2252 => 1881,  2224 => 1856,  2176 => 1811,  2114 => 1753,  2107 => 1750,  2102 => 1749,  2086 => 1737,  2079 => 1734,  1967 => 1637,  1826 => 1502,  1819 => 1498,  1732 => 1414,  1723 => 1408,  1695 => 680,  1672 => 1363,  1664 => 1359,  1611 => 1310,  1601 => 638,  1533 => 1238,  1526 => 1235,  1518 => 1230,  1511 => 1227,  1454 => 572,  1439 => 1161,  1417 => 1143,  1409 => 1139,  1308 => 526,  1301 => 1037,  1252 => 392,  1243 => 283,  1223 => 978,  1212 => 494,  1167 => 366,  1161 => 933,  1145 => 923,  1140 => 413,  1134 => 409,  1126 => 443,  1106 => 528,  1100 => 897,  1026 => 220,  997 => 367,  983 => 298,  975 => 361,  956 => 318,  939 => 285,  902 => 274,  894 => 364,  879 => 76,  757 => 309,  727 => 293,  716 => 226,  670 => 204,  528 => 187,  476 => 213,  435 => 112,  354 => 89,  341 => 86,  192 => 17,  321 => 89,  243 => 75,  793 => 266,  780 => 247,  758 => 229,  700 => 193,  686 => 294,  652 => 185,  638 => 269,  620 => 216,  545 => 243,  523 => 82,  494 => 142,  459 => 156,  438 => 113,  351 => 84,  347 => 83,  402 => 99,  268 => 71,  430 => 136,  411 => 101,  379 => 95,  322 => 41,  315 => 38,  289 => 33,  284 => 86,  255 => 66,  234 => 48,  1133 => 444,  1124 => 455,  1121 => 430,  1116 => 405,  1113 => 429,  1108 => 376,  1103 => 444,  1098 => 401,  1081 => 320,  1073 => 235,  1067 => 314,  1064 => 330,  1061 => 421,  1053 => 318,  1049 => 307,  1042 => 313,  1039 => 384,  1025 => 304,  1021 => 219,  1015 => 308,  1008 => 461,  996 => 406,  989 => 454,  985 => 395,  981 => 296,  977 => 321,  970 => 360,  966 => 359,  955 => 293,  952 => 464,  943 => 299,  936 => 353,  930 => 289,  919 => 314,  917 => 348,  908 => 346,  905 => 363,  896 => 275,  891 => 338,  877 => 334,  862 => 333,  857 => 271,  837 => 261,  832 => 260,  827 => 184,  821 => 266,  803 => 179,  778 => 175,  769 => 253,  765 => 201,  753 => 171,  746 => 196,  743 => 297,  735 => 168,  730 => 251,  720 => 305,  717 => 165,  712 => 187,  691 => 219,  678 => 275,  654 => 199,  587 => 191,  576 => 167,  539 => 200,  517 => 210,  471 => 125,  441 => 114,  437 => 138,  418 => 106,  386 => 152,  373 => 120,  304 => 108,  270 => 83,  265 => 81,  229 => 42,  477 => 167,  455 => 125,  448 => 131,  429 => 124,  407 => 120,  399 => 163,  389 => 114,  375 => 148,  358 => 87,  349 => 137,  335 => 41,  327 => 106,  298 => 98,  280 => 53,  249 => 76,  194 => 81,  142 => 32,  344 => 95,  318 => 39,  306 => 102,  295 => 78,  357 => 101,  300 => 118,  286 => 88,  276 => 100,  269 => 97,  254 => 61,  128 => 27,  237 => 72,  165 => 53,  122 => 14,  798 => 256,  770 => 309,  759 => 278,  748 => 298,  731 => 191,  721 => 227,  718 => 188,  708 => 185,  696 => 236,  617 => 140,  590 => 259,  553 => 145,  550 => 157,  540 => 161,  533 => 182,  500 => 135,  493 => 160,  489 => 202,  482 => 198,  467 => 70,  464 => 170,  458 => 121,  452 => 197,  449 => 196,  415 => 152,  382 => 132,  372 => 92,  361 => 100,  356 => 124,  339 => 120,  302 => 36,  285 => 104,  258 => 40,  123 => 34,  108 => 27,  424 => 108,  394 => 115,  380 => 2,  338 => 42,  319 => 79,  316 => 113,  312 => 39,  290 => 106,  267 => 70,  206 => 36,  110 => 31,  240 => 74,  224 => 33,  219 => 39,  217 => 80,  202 => 82,  186 => 42,  170 => 47,  100 => 11,  67 => 12,  14 => 1,  1096 => 345,  1090 => 339,  1088 => 397,  1085 => 456,  1066 => 233,  1034 => 282,  1031 => 221,  1018 => 303,  1013 => 302,  1007 => 408,  1002 => 403,  993 => 213,  986 => 212,  982 => 211,  976 => 399,  971 => 209,  964 => 208,  949 => 289,  946 => 288,  940 => 388,  937 => 374,  928 => 371,  926 => 318,  915 => 284,  912 => 82,  903 => 231,  898 => 440,  892 => 303,  889 => 277,  887 => 302,  884 => 79,  876 => 274,  874 => 193,  871 => 331,  863 => 345,  861 => 270,  858 => 272,  850 => 189,  843 => 270,  840 => 186,  815 => 264,  812 => 263,  808 => 323,  804 => 258,  799 => 312,  791 => 176,  785 => 262,  775 => 313,  771 => 245,  754 => 267,  728 => 167,  726 => 190,  723 => 189,  715 => 105,  711 => 220,  709 => 222,  706 => 232,  698 => 182,  694 => 199,  692 => 161,  689 => 181,  681 => 224,  677 => 288,  675 => 234,  663 => 213,  661 => 263,  650 => 213,  646 => 231,  629 => 266,  627 => 180,  625 => 266,  622 => 202,  598 => 157,  592 => 155,  586 => 175,  575 => 189,  566 => 251,  556 => 146,  554 => 158,  541 => 144,  536 => 142,  515 => 79,  511 => 208,  509 => 150,  488 => 200,  486 => 145,  483 => 127,  465 => 110,  463 => 153,  450 => 182,  432 => 125,  419 => 178,  371 => 154,  362 => 144,  353 => 98,  337 => 124,  333 => 44,  309 => 38,  303 => 81,  299 => 89,  291 => 36,  272 => 99,  261 => 49,  253 => 30,  239 => 29,  235 => 65,  213 => 14,  200 => 62,  198 => 54,  159 => 46,  149 => 35,  146 => 34,  131 => 48,  116 => 45,  79 => 16,  74 => 45,  71 => 11,  836 => 262,  817 => 243,  814 => 321,  811 => 320,  805 => 244,  787 => 257,  779 => 169,  776 => 232,  773 => 322,  761 => 199,  751 => 302,  747 => 298,  742 => 237,  739 => 296,  736 => 215,  724 => 259,  705 => 69,  702 => 216,  688 => 226,  680 => 205,  667 => 273,  662 => 282,  656 => 215,  649 => 285,  644 => 174,  641 => 220,  624 => 162,  613 => 166,  607 => 137,  597 => 260,  591 => 170,  584 => 236,  579 => 132,  563 => 212,  559 => 183,  551 => 190,  547 => 188,  537 => 160,  524 => 164,  512 => 137,  507 => 237,  504 => 149,  498 => 162,  485 => 158,  480 => 126,  472 => 111,  466 => 138,  460 => 152,  447 => 107,  442 => 128,  434 => 133,  428 => 181,  422 => 119,  404 => 102,  368 => 136,  364 => 144,  340 => 80,  334 => 123,  330 => 48,  325 => 115,  292 => 92,  287 => 101,  282 => 103,  279 => 85,  273 => 33,  266 => 68,  256 => 31,  252 => 87,  228 => 90,  218 => 55,  201 => 51,  64 => 6,  51 => 10,  2159 => 821,  2155 => 820,  2152 => 819,  2149 => 818,  2145 => 817,  2142 => 816,  2139 => 815,  2136 => 814,  2132 => 813,  2129 => 812,  2126 => 811,  2123 => 810,  2117 => 809,  2109 => 847,  2104 => 802,  2101 => 801,  2087 => 799,  2080 => 798,  2077 => 797,  2063 => 795,  2056 => 794,  2053 => 1712,  2042 => 823,  2037 => 789,  2031 => 788,  2028 => 818,  2024 => 786,  2021 => 785,  2008 => 776,  2004 => 811,  2001 => 1668,  1996 => 771,  1994 => 770,  1987 => 766,  1983 => 765,  1979 => 764,  1972 => 761,  1969 => 760,  1966 => 759,  1961 => 756,  1959 => 755,  1952 => 751,  1948 => 750,  1944 => 749,  1937 => 746,  1933 => 1606,  1930 => 744,  1925 => 741,  1923 => 779,  1916 => 736,  1912 => 735,  1908 => 734,  1901 => 731,  1897 => 730,  1894 => 729,  1889 => 769,  1887 => 725,  1880 => 721,  1876 => 720,  1872 => 719,  1865 => 716,  1861 => 715,  1858 => 714,  1855 => 713,  1850 => 710,  1841 => 708,  1835 => 836,  1829 => 706,  1827 => 831,  1820 => 701,  1816 => 700,  1809 => 697,  1804 => 744,  1801 => 695,  1798 => 694,  1795 => 742,  1789 => 814,  1776 => 687,  1771 => 686,  1768 => 685,  1766 => 806,  1759 => 672,  1755 => 671,  1750 => 668,  1745 => 667,  1742 => 794,  1739 => 665,  1736 => 708,  1730 => 705,  1720 => 688,  1716 => 657,  1711 => 655,  1708 => 654,  1698 => 652,  1694 => 651,  1689 => 649,  1682 => 672,  1678 => 644,  1671 => 641,  1666 => 640,  1663 => 639,  1660 => 763,  1657 => 659,  1651 => 656,  1641 => 631,  1637 => 667,  1632 => 628,  1629 => 627,  1619 => 625,  1615 => 624,  1610 => 622,  1603 => 618,  1599 => 651,  1592 => 614,  1587 => 613,  1584 => 635,  1581 => 731,  1576 => 608,  1574 => 633,  1567 => 603,  1563 => 630,  1559 => 601,  1552 => 628,  1548 => 597,  1542 => 593,  1529 => 605,  1525 => 614,  1516 => 708,  1512 => 583,  1503 => 599,  1490 => 574,  1486 => 694,  1477 => 567,  1473 => 566,  1467 => 687,  1460 => 557,  1447 => 569,  1442 => 554,  1427 => 542,  1423 => 541,  1418 => 538,  1415 => 537,  1412 => 536,  1406 => 532,  1393 => 564,  1388 => 537,  1380 => 539,  1376 => 436,  1371 => 645,  1367 => 527,  1364 => 518,  1361 => 523,  1355 => 543,  1342 => 624,  1337 => 510,  1328 => 552,  1324 => 550,  1317 => 511,  1313 => 499,  1310 => 526,  1304 => 504,  1291 => 479,  1286 => 476,  1279 => 486,  1274 => 468,  1270 => 483,  1266 => 487,  1262 => 490,  1257 => 500,  1254 => 286,  1250 => 394,  1246 => 477,  1239 => 389,  1235 => 498,  1232 => 416,  1226 => 383,  1213 => 273,  1208 => 481,  1201 => 443,  1196 => 464,  1192 => 490,  1188 => 456,  1184 => 263,  1179 => 448,  1176 => 261,  1172 => 428,  1168 => 451,  1164 => 450,  1160 => 256,  1153 => 561,  1149 => 359,  1146 => 444,  1137 => 249,  1123 => 442,  1119 => 354,  1115 => 424,  1111 => 377,  1107 => 242,  1102 => 344,  1099 => 347,  1095 => 400,  1091 => 321,  1082 => 455,  1079 => 524,  1076 => 393,  1070 => 431,  1057 => 313,  1052 => 406,  1045 => 484,  1040 => 224,  1036 => 283,  1032 => 415,  1028 => 312,  1023 => 374,  1020 => 311,  1016 => 266,  1012 => 390,  1005 => 216,  1001 => 304,  998 => 308,  992 => 406,  979 => 297,  974 => 400,  967 => 399,  962 => 397,  958 => 383,  954 => 293,  950 => 292,  945 => 391,  942 => 290,  938 => 375,  934 => 203,  927 => 288,  923 => 201,  920 => 369,  910 => 365,  901 => 197,  897 => 273,  890 => 271,  886 => 270,  883 => 353,  868 => 273,  856 => 293,  853 => 341,  849 => 264,  845 => 329,  841 => 249,  835 => 268,  830 => 333,  826 => 282,  822 => 281,  818 => 265,  813 => 181,  810 => 290,  806 => 261,  802 => 178,  795 => 241,  792 => 335,  789 => 249,  784 => 286,  782 => 237,  777 => 255,  772 => 289,  768 => 202,  763 => 327,  760 => 305,  756 => 248,  752 => 198,  745 => 245,  741 => 218,  738 => 241,  732 => 171,  719 => 288,  714 => 251,  710 => 164,  704 => 222,  699 => 162,  695 => 66,  690 => 226,  687 => 210,  683 => 156,  679 => 155,  672 => 153,  668 => 264,  665 => 151,  658 => 177,  645 => 187,  640 => 184,  634 => 206,  628 => 174,  623 => 179,  619 => 78,  611 => 158,  606 => 234,  603 => 136,  599 => 174,  595 => 156,  583 => 169,  580 => 256,  573 => 166,  560 => 160,  543 => 175,  538 => 174,  534 => 189,  530 => 213,  526 => 170,  521 => 287,  518 => 194,  514 => 230,  510 => 154,  503 => 133,  496 => 202,  490 => 129,  484 => 128,  474 => 71,  470 => 139,  446 => 116,  440 => 130,  436 => 62,  431 => 110,  425 => 135,  416 => 168,  412 => 117,  408 => 167,  403 => 117,  400 => 119,  396 => 100,  392 => 143,  385 => 97,  381 => 150,  367 => 51,  363 => 89,  359 => 125,  355 => 76,  350 => 96,  346 => 73,  343 => 140,  328 => 93,  324 => 91,  313 => 81,  307 => 75,  301 => 119,  288 => 87,  283 => 88,  271 => 86,  257 => 48,  251 => 69,  238 => 93,  233 => 69,  195 => 47,  191 => 23,  187 => 20,  183 => 21,  130 => 28,  88 => 34,  76 => 23,  115 => 34,  95 => 30,  655 => 148,  651 => 275,  648 => 215,  637 => 210,  633 => 182,  621 => 462,  618 => 241,  615 => 178,  604 => 201,  600 => 233,  588 => 206,  585 => 222,  582 => 153,  571 => 187,  567 => 162,  555 => 125,  552 => 171,  549 => 123,  544 => 179,  542 => 156,  535 => 237,  531 => 139,  519 => 80,  516 => 218,  513 => 154,  508 => 117,  506 => 188,  499 => 209,  495 => 132,  491 => 146,  481 => 215,  478 => 72,  475 => 140,  469 => 182,  456 => 135,  451 => 139,  443 => 64,  439 => 178,  427 => 60,  423 => 59,  420 => 141,  409 => 140,  405 => 218,  401 => 56,  391 => 159,  387 => 133,  384 => 112,  378 => 94,  365 => 93,  360 => 102,  348 => 50,  336 => 94,  332 => 40,  329 => 119,  323 => 116,  310 => 37,  305 => 111,  277 => 34,  274 => 94,  263 => 105,  259 => 79,  247 => 38,  244 => 76,  241 => 59,  222 => 60,  210 => 65,  207 => 23,  204 => 22,  184 => 55,  181 => 77,  167 => 41,  157 => 32,  96 => 22,  421 => 143,  417 => 150,  414 => 145,  406 => 139,  398 => 116,  393 => 53,  390 => 98,  376 => 108,  369 => 148,  366 => 90,  352 => 128,  345 => 49,  342 => 126,  331 => 108,  326 => 78,  320 => 77,  317 => 114,  314 => 86,  311 => 105,  308 => 111,  297 => 93,  293 => 34,  281 => 35,  278 => 71,  275 => 84,  264 => 92,  260 => 80,  248 => 76,  245 => 63,  242 => 72,  231 => 73,  227 => 72,  215 => 60,  212 => 26,  209 => 73,  197 => 61,  177 => 51,  171 => 49,  161 => 51,  132 => 26,  121 => 46,  105 => 26,  99 => 23,  81 => 43,  77 => 16,  180 => 20,  176 => 16,  156 => 30,  143 => 24,  139 => 16,  118 => 30,  189 => 80,  185 => 48,  173 => 43,  166 => 19,  152 => 36,  174 => 37,  164 => 33,  154 => 28,  150 => 31,  137 => 33,  133 => 28,  127 => 15,  107 => 43,  102 => 41,  83 => 20,  78 => 31,  53 => 10,  23 => 3,  42 => 11,  138 => 36,  134 => 30,  109 => 24,  103 => 26,  97 => 18,  94 => 37,  84 => 18,  75 => 30,  69 => 7,  66 => 24,  54 => 16,  44 => 11,  230 => 80,  226 => 56,  203 => 12,  193 => 72,  188 => 33,  182 => 41,  178 => 44,  168 => 49,  163 => 18,  160 => 38,  155 => 37,  148 => 45,  145 => 52,  140 => 43,  136 => 49,  125 => 47,  120 => 23,  113 => 20,  101 => 37,  92 => 11,  89 => 10,  85 => 21,  73 => 15,  62 => 14,  59 => 47,  56 => 13,  41 => 11,  126 => 32,  119 => 30,  111 => 28,  106 => 41,  98 => 63,  93 => 21,  86 => 13,  70 => 22,  60 => 9,  28 => 3,  36 => 5,  114 => 36,  104 => 22,  91 => 16,  80 => 10,  63 => 10,  58 => 17,  40 => 6,  34 => 10,  45 => 5,  61 => 15,  55 => 11,  48 => 6,  39 => 8,  35 => 4,  31 => 4,  26 => 4,  21 => 2,  46 => 9,  29 => 6,  57 => 8,  50 => 10,  47 => 9,  38 => 10,  33 => 6,  49 => 8,  32 => 4,  246 => 94,  236 => 28,  232 => 91,  225 => 63,  221 => 78,  216 => 68,  214 => 53,  211 => 64,  208 => 58,  205 => 48,  199 => 48,  196 => 50,  190 => 56,  179 => 58,  175 => 9,  172 => 70,  169 => 8,  162 => 48,  158 => 17,  153 => 69,  151 => 44,  147 => 3,  144 => 36,  141 => 35,  135 => 27,  129 => 34,  124 => 35,  117 => 32,  112 => 44,  90 => 31,  87 => 19,  82 => 17,  72 => 28,  68 => 13,  65 => 49,  52 => 16,  43 => 9,  37 => 6,  30 => 3,  27 => 3,  25 => 4,  24 => 2,  22 => 2,  19 => 1,);
    }
}

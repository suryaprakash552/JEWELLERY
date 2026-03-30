<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* admin/view/template/common/column_left.twig */
class __TwigTemplate_8724377a7068942882fda735de121e9b extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<nav id=\"top-nav\" class=\"navbar navbar-expand-lg navbar-dark\"
     style=\"background-color: #000; border-bottom: 1px solid #333;\">
  <div class=\"container-fluid\">
    <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">
      ";
        // line 5
        $context["i"] = 0;
        // line 6
        yield "      ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["menus"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["menu"]) {
            // line 7
            yield "        ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "children", [], "any", false, false, false, 7)) {
                // line 8
                yield "          <li class=\"nav-item dropdown ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "active", [], "any", false, false, false, 8)) {
                    yield "active";
                }
                yield "\">
            <a href=\"#\" class=\"nav-link px-3 dropdown-toggle ";
                // line 9
                if (CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "active", [], "any", false, false, false, 9)) {
                    yield "active";
                }
                yield "\" data-bs-toggle=\"dropdown\">
              <i class=\"";
                // line 10
                yield CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "icon", [], "any", false, false, false, 10);
                yield "\"></i> ";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "name", [], "any", false, false, false, 10);
                yield "
            </a>
            <ul class=\"dropdown-menu border-0 shadow-sm mt-0\" style=\"background-color: #1e293b;\">
              ";
                // line 13
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "children", [], "any", false, false, false, 13));
                foreach ($context['_seq'] as $context["_key"] => $context["children_1"]) {
                    // line 14
                    yield "                ";
                    if (CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "children", [], "any", false, false, false, 14)) {
                        // line 15
                        yield "                  <li class=\"dropdown-submenu dropend ";
                        if (CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "active", [], "any", false, false, false, 15)) {
                            yield "active";
                        }
                        yield "\">
                    <a href=\"#\" class=\"dropdown-item dropdown-toggle text-light ";
                        // line 16
                        if (CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "active", [], "any", false, false, false, 16)) {
                            yield "active";
                        }
                        yield "\" data-bs-toggle=\"dropdown\">
                      ";
                        // line 17
                        yield CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "name", [], "any", false, false, false, 17);
                        yield "
                    </a>
                    <ul class=\"dropdown-menu border-0 shadow-sm\" style=\"background-color: #1e293b;\">
                      ";
                        // line 20
                        $context['_parent'] = $context;
                        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "children", [], "any", false, false, false, 20));
                        foreach ($context['_seq'] as $context["_key"] => $context["children_2"]) {
                            // line 21
                            yield "                        <li";
                            if (CoreExtension::getAttribute($this->env, $this->source, $context["children_2"], "active", [], "any", false, false, false, 21)) {
                                yield " class=\"active\"";
                            }
                            yield ">
                          <a class=\"dropdown-item text-light ";
                            // line 22
                            if (CoreExtension::getAttribute($this->env, $this->source, $context["children_2"], "active", [], "any", false, false, false, 22)) {
                                yield "active";
                            }
                            yield "\" href=\"";
                            yield CoreExtension::getAttribute($this->env, $this->source, $context["children_2"], "href", [], "any", false, false, false, 22);
                            yield "\">";
                            yield CoreExtension::getAttribute($this->env, $this->source, $context["children_2"], "name", [], "any", false, false, false, 22);
                            yield "</a>
                        </li>
                      ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_key'], $context['children_2'], $context['_parent']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 25
                        yield "                    </ul>
                  </li>
                ";
                    } else {
                        // line 28
                        yield "                  <li";
                        if (CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "active", [], "any", false, false, false, 28)) {
                            yield " class=\"active\"";
                        }
                        yield ">
                    <a class=\"dropdown-item text-light ";
                        // line 29
                        if (CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "active", [], "any", false, false, false, 29)) {
                            yield "active";
                        }
                        yield "\" href=\"";
                        yield CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "href", [], "any", false, false, false, 29);
                        yield "\">
                      ";
                        // line 30
                        yield CoreExtension::getAttribute($this->env, $this->source, $context["children_1"], "name", [], "any", false, false, false, 30);
                        yield "
                    </a>
                  </li>
                ";
                    }
                    // line 34
                    yield "              ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['children_1'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 35
                yield "            </ul>
          </li>
        ";
            } else {
                // line 38
                yield "          <li class=\"nav-item ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "active", [], "any", false, false, false, 38)) {
                    yield "active";
                }
                yield "\">
            <a href=\"";
                // line 39
                yield CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "href", [], "any", false, false, false, 39);
                yield "\" class=\"nav-link px-3 ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "active", [], "any", false, false, false, 39)) {
                    yield "active";
                }
                yield "\">
              <i class=\"";
                // line 40
                yield CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "icon", [], "any", false, false, false, 40);
                yield "\"></i> ";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["menu"], "name", [], "any", false, false, false, 40);
                yield "
            </a>
          </li>
        ";
            }
            // line 44
            yield "        ";
            $context["i"] = (($context["i"] ?? null) + 1);
            // line 45
            yield "      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['menu'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "    </ul>
  </div>
</nav>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/common/column_left.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  202 => 46,  196 => 45,  193 => 44,  184 => 40,  176 => 39,  169 => 38,  164 => 35,  158 => 34,  151 => 30,  143 => 29,  136 => 28,  131 => 25,  116 => 22,  109 => 21,  105 => 20,  99 => 17,  93 => 16,  86 => 15,  83 => 14,  79 => 13,  71 => 10,  65 => 9,  58 => 8,  55 => 7,  50 => 6,  48 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<nav id=\"top-nav\" class=\"navbar navbar-expand-lg navbar-dark\"
     style=\"background-color: #000; border-bottom: 1px solid #333;\">
  <div class=\"container-fluid\">
    <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">
      {% set i = 0 %}
      {% for menu in menus %}
        {% if menu.children %}
          <li class=\"nav-item dropdown {% if menu.active %}active{% endif %}\">
            <a href=\"#\" class=\"nav-link px-3 dropdown-toggle {% if menu.active %}active{% endif %}\" data-bs-toggle=\"dropdown\">
              <i class=\"{{ menu.icon }}\"></i> {{ menu.name }}
            </a>
            <ul class=\"dropdown-menu border-0 shadow-sm mt-0\" style=\"background-color: #1e293b;\">
              {% for children_1 in menu.children %}
                {% if children_1.children %}
                  <li class=\"dropdown-submenu dropend {% if children_1.active %}active{% endif %}\">
                    <a href=\"#\" class=\"dropdown-item dropdown-toggle text-light {% if children_1.active %}active{% endif %}\" data-bs-toggle=\"dropdown\">
                      {{ children_1.name }}
                    </a>
                    <ul class=\"dropdown-menu border-0 shadow-sm\" style=\"background-color: #1e293b;\">
                      {% for children_2 in children_1.children %}
                        <li{% if children_2.active %} class=\"active\"{% endif %}>
                          <a class=\"dropdown-item text-light {% if children_2.active %}active{% endif %}\" href=\"{{ children_2.href }}\">{{ children_2.name }}</a>
                        </li>
                      {% endfor %}
                    </ul>
                  </li>
                {% else %}
                  <li{% if children_1.active %} class=\"active\"{% endif %}>
                    <a class=\"dropdown-item text-light {% if children_1.active %}active{% endif %}\" href=\"{{ children_1.href }}\">
                      {{ children_1.name }}
                    </a>
                  </li>
                {% endif %}
              {% endfor %}
            </ul>
          </li>
        {% else %}
          <li class=\"nav-item {% if menu.active %}active{% endif %}\">
            <a href=\"{{ menu.href }}\" class=\"nav-link px-3 {% if menu.active %}active{% endif %}\">
              <i class=\"{{ menu.icon }}\"></i> {{ menu.name }}
            </a>
          </li>
        {% endif %}
        {% set i = i + 1 %}
      {% endfor %}
    </ul>
  </div>
</nav>
", "admin/view/template/common/column_left.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/admin/view/template/common/column_left.twig");
    }
}

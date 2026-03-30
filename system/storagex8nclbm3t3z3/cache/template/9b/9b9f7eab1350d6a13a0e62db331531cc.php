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

/* extension/opencart/admin/view/template/dashboard/recent_info.twig */
class __TwigTemplate_4fc0b95b1b764ce55f600e44b32b22cc extends Template
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
        yield "<div class=\"card mb-3\">
  <div class=\"card-header\"><i class=\"fa-solid fa-shopping-cart\"></i> ";
        // line 2
        yield ($context["heading_title"] ?? null);
        yield "</div>
  <div class=\"table-responsive\">
    <table class=\"table\">
      <thead>
        <tr>
          <td class=\"text-end\">";
        // line 7
        yield ($context["column_order_id"] ?? null);
        yield "</td>
          <td>";
        // line 8
        yield ($context["column_customer"] ?? null);
        yield "</td>
          <td>";
        // line 9
        yield ($context["column_status"] ?? null);
        yield "</td>
          <td>";
        // line 10
        yield ($context["column_date_added"] ?? null);
        yield "</td>
          <td class=\"text-end\">";
        // line 11
        yield ($context["column_total"] ?? null);
        yield "</td>
          <td class=\"text-end\">";
        // line 12
        yield ($context["column_action"] ?? null);
        yield "</td>
        </tr>
      </thead>
      <tbody>
        ";
        // line 16
        if (($context["orders"] ?? null)) {
            // line 17
            yield "          ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["orders"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["order"]) {
                // line 18
                yield "            <tr>
              <td class=\"text-end\">";
                // line 19
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "order_id", [], "any", false, false, false, 19);
                yield "</td>
              <td>";
                // line 20
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "customer", [], "any", false, false, false, 20);
                yield "</td>
              <td>";
                // line 21
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "status", [], "any", false, false, false, 21);
                yield "</td>
              <td>";
                // line 22
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "date_added", [], "any", false, false, false, 22);
                yield "</td>
              <td class=\"text-end\">";
                // line 23
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "total", [], "any", false, false, false, 23);
                yield "</td>
              <td class=\"text-end\"><a href=\"";
                // line 24
                yield CoreExtension::getAttribute($this->env, $this->source, $context["order"], "view", [], "any", false, false, false, 24);
                yield "\" data-bs-toggle=\"tooltip\" title=\"";
                yield ($context["button_view"] ?? null);
                yield "\" class=\"btn btn-info\"><i class=\"fa-solid fa-eye\"></i></a></td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['order'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 27
            yield "        ";
        } else {
            // line 28
            yield "          <tr>
            <td class=\"text-center\" colspan=\"6\">";
            // line 29
            yield ($context["text_no_results"] ?? null);
            yield "</td>
          </tr>
        ";
        }
        // line 32
        yield "      </tbody>
    </table>
  </div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/opencart/admin/view/template/dashboard/recent_info.twig";
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
        return array (  133 => 32,  127 => 29,  124 => 28,  121 => 27,  110 => 24,  106 => 23,  102 => 22,  98 => 21,  94 => 20,  90 => 19,  87 => 18,  82 => 17,  80 => 16,  73 => 12,  69 => 11,  65 => 10,  61 => 9,  57 => 8,  53 => 7,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"card mb-3\">
  <div class=\"card-header\"><i class=\"fa-solid fa-shopping-cart\"></i> {{ heading_title }}</div>
  <div class=\"table-responsive\">
    <table class=\"table\">
      <thead>
        <tr>
          <td class=\"text-end\">{{ column_order_id }}</td>
          <td>{{ column_customer }}</td>
          <td>{{ column_status }}</td>
          <td>{{ column_date_added }}</td>
          <td class=\"text-end\">{{ column_total }}</td>
          <td class=\"text-end\">{{ column_action }}</td>
        </tr>
      </thead>
      <tbody>
        {% if orders %}
          {% for order in orders %}
            <tr>
              <td class=\"text-end\">{{ order.order_id }}</td>
              <td>{{ order.customer }}</td>
              <td>{{ order.status }}</td>
              <td>{{ order.date_added }}</td>
              <td class=\"text-end\">{{ order.total }}</td>
              <td class=\"text-end\"><a href=\"{{ order.view }}\" data-bs-toggle=\"tooltip\" title=\"{{ button_view }}\" class=\"btn btn-info\"><i class=\"fa-solid fa-eye\"></i></a></td>
            </tr>
          {% endfor %}
        {% else %}
          <tr>
            <td class=\"text-center\" colspan=\"6\">{{ text_no_results }}</td>
          </tr>
        {% endif %}
      </tbody>
    </table>
  </div>
</div>
", "extension/opencart/admin/view/template/dashboard/recent_info.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/opencart/admin/view/template/dashboard/recent_info.twig");
    }
}

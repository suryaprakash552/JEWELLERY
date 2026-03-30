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

/* catalog/view/template/mail/order_alert.twig */
class __TwigTemplate_324aca219e9fe0eebf871e80a56eac5d extends Template
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
        yield ($context["text_received"] ?? null);
        yield "<br/>
<br/>
";
        // line 3
        yield ($context["text_order_id"] ?? null);
        yield " ";
        yield ($context["order_id"] ?? null);
        yield "<br/>
";
        // line 4
        yield ($context["text_date_added"] ?? null);
        yield " ";
        yield ($context["date_added"] ?? null);
        yield "<br/>
";
        // line 5
        yield ($context["text_order_status"] ?? null);
        yield " ";
        yield ($context["order_status"] ?? null);
        yield "<br/>
<br/>
";
        // line 7
        yield ($context["text_product"] ?? null);
        yield "<br/>
<br/>
";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["products"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 10
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "quantity", [], "any", false, false, false, 10);
            yield "x ";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, false, 10);
            yield " (";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "model", [], "any", false, false, false, 10);
            yield ") ";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "total", [], "any", false, false, false, 10);
            yield "<br/>
";
            // line 11
            if (CoreExtension::getAttribute($this->env, $this->source, $context["product"], "option", [], "any", false, false, false, 11)) {
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["product"], "option", [], "any", false, false, false, 12));
                foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                    // line 13
                    yield "\t- ";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["option"], "name", [], "any", false, false, false, 13);
                    yield " ";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["option"], "value", [], "any", false, false, false, 13);
                    yield "<br/>
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['option'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['product'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        yield "<br/>
";
        // line 18
        yield ($context["text_total"] ?? null);
        yield "<br/>
<br/>
";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["totals"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["total"]) {
            // line 21
            yield CoreExtension::getAttribute($this->env, $this->source, $context["total"], "title", [], "any", false, false, false, 21);
            yield ": ";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["total"], "value", [], "any", false, false, false, 21);
            yield "<br/>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['total'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        yield "<br/>
";
        // line 24
        if (($context["comment"] ?? null)) {
            // line 25
            yield ($context["text_comment"] ?? null);
            yield "<br/>
<br/>
";
            // line 27
            yield ($context["comment"] ?? null);
            yield "<br/>
";
        }
        // line 29
        yield "<br/>
";
        // line 30
        yield ($context["store"] ?? null);
        yield "<br/>
";
        // line 31
        yield ($context["store_url"] ?? null);
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "catalog/view/template/mail/order_alert.twig";
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
        return array (  151 => 31,  147 => 30,  144 => 29,  139 => 27,  134 => 25,  132 => 24,  129 => 23,  119 => 21,  115 => 20,  110 => 18,  107 => 17,  91 => 13,  87 => 12,  85 => 11,  75 => 10,  71 => 9,  66 => 7,  59 => 5,  53 => 4,  47 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{{ text_received }}<br/>
<br/>
{{ text_order_id }} {{ order_id }}<br/>
{{ text_date_added }} {{ date_added }}<br/>
{{ text_order_status }} {{ order_status }}<br/>
<br/>
{{ text_product }}<br/>
<br/>
{% for product in products %}
{{ product.quantity }}x {{ product.name }} ({{ product.model }}) {{ product.total }}<br/>
{% if product.option %}
{% for option in product.option %}
\t- {{ option.name }} {{ option.value }}<br/>
{% endfor %}
{% endif %}
{% endfor %}
<br/>
{{ text_total }}<br/>
<br/>
{% for total in totals %}
{{ total.title }}: {{ total.value }}<br/>
{% endfor %}
<br/>
{% if comment %}
{{ text_comment }}<br/>
<br/>
{{ comment }}<br/>
{% endif %}
<br/>
{{ store }}<br/>
{{ store_url }}", "catalog/view/template/mail/order_alert.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/catalog/view/template/mail/order_alert.twig");
    }
}

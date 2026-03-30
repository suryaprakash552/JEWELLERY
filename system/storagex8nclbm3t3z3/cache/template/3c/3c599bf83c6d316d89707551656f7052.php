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

/* extension/opencart/admin/view/template/dashboard/activity_info.twig */
class __TwigTemplate_5e20f0e455f50fda0dd47aae1409038b extends Template
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
  <div class=\"card-header\"><i class=\"fa-regular fa-calendar\"></i> ";
        // line 2
        yield ($context["heading_title"] ?? null);
        yield "</div>
  <ul class=\"list-group list-group-flush\">
    ";
        // line 4
        if (($context["activities"] ?? null)) {
            // line 5
            yield "      ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["activities"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["activity"]) {
                // line 6
                yield "        <li class=\"list-group-item\">";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "comment", [], "any", false, false, false, 6);
                yield "
          <br/>
          <small class=\"text-muted\"><i class=\"fa-solid fa-clock\"></i> ";
                // line 8
                yield CoreExtension::getAttribute($this->env, $this->source, $context["activity"], "date_added", [], "any", false, false, false, 8);
                yield "</small>
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['activity'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            yield "    ";
        } else {
            // line 12
            yield "      <li class=\"list-group-item text-center\">";
            yield ($context["text_no_results"] ?? null);
            yield "</li>
    ";
        }
        // line 14
        yield "  </ul>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/opencart/admin/view/template/dashboard/activity_info.twig";
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
        return array (  81 => 14,  75 => 12,  72 => 11,  63 => 8,  57 => 6,  52 => 5,  50 => 4,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"card mb-3\">
  <div class=\"card-header\"><i class=\"fa-regular fa-calendar\"></i> {{ heading_title }}</div>
  <ul class=\"list-group list-group-flush\">
    {% if activities %}
      {% for activity in activities %}
        <li class=\"list-group-item\">{{ activity.comment }}
          <br/>
          <small class=\"text-muted\"><i class=\"fa-solid fa-clock\"></i> {{ activity.date_added }}</small>
        </li>
      {% endfor %}
    {% else %}
      <li class=\"list-group-item text-center\">{{ text_no_results }}</li>
    {% endif %}
  </ul>
</div>
", "extension/opencart/admin/view/template/dashboard/activity_info.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/opencart/admin/view/template/dashboard/activity_info.twig");
    }
}

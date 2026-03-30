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

/* extension/opencart/admin/view/template/dashboard/sale_info.twig */
class __TwigTemplate_d3e9c78f1da4720d0ff4e2343e6db7ed extends Template
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
        yield "<div class=\"tile tile-primary\">
  <div class=\"tile-heading\">";
        // line 2
        yield ($context["heading_title"] ?? null);
        yield " <span class=\"float-end\">
    ";
        // line 3
        if ((($context["percentage"] ?? null) > 0)) {
            // line 4
            yield "      <i class=\"fa-solid fa-caret-up\"></i>
    ";
        } elseif ((        // line 5
($context["percentage"] ?? null) < 0)) {
            // line 6
            yield "      <i class=\"fa-solid fa-caret-down\"></i>
    ";
        }
        // line 8
        yield "    ";
        yield ($context["percentage"] ?? null);
        yield "%</span>
  </div>
  <div class=\"tile-body\"><i class=\"fa-solid fa-credit-card\"></i>
    <h2 class=\"float-end\">";
        // line 11
        yield ($context["total"] ?? null);
        yield "</h2>
  </div>
  <div class=\"tile-footer\"><a href=\"";
        // line 13
        yield ($context["sale"] ?? null);
        yield "\">";
        yield ($context["text_view"] ?? null);
        yield "</a></div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/opencart/admin/view/template/dashboard/sale_info.twig";
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
        return array (  72 => 13,  67 => 11,  60 => 8,  56 => 6,  54 => 5,  51 => 4,  49 => 3,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"tile tile-primary\">
  <div class=\"tile-heading\">{{ heading_title }} <span class=\"float-end\">
    {% if percentage > 0 %}
      <i class=\"fa-solid fa-caret-up\"></i>
    {% elseif percentage < 0 %}
      <i class=\"fa-solid fa-caret-down\"></i>
    {% endif %}
    {{ percentage }}%</span>
  </div>
  <div class=\"tile-body\"><i class=\"fa-solid fa-credit-card\"></i>
    <h2 class=\"float-end\">{{ total }}</h2>
  </div>
  <div class=\"tile-footer\"><a href=\"{{ sale }}\">{{ text_view }}</a></div>
</div>
", "extension/opencart/admin/view/template/dashboard/sale_info.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/opencart/admin/view/template/dashboard/sale_info.twig");
    }
}

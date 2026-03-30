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

/* extension/purpletree_pos/catalog/view/template/pos/header.twig */
class __TwigTemplate_8979623db175dd3cea282399132db628 extends Template
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
        yield "<!DOCTYPE html>
<html dir=\"";
        // line 2
        yield ($context["direction"] ?? null);
        yield "\" lang=\"";
        yield ($context["lang"] ?? null);
        yield "\">
<head>

<meta charset=\"UTF-8\" />
<title> ";
        // line 6
        yield ($context["heading_title1"] ?? null);
        yield "</title>
<base href=\"";
        // line 7
        yield ($context["base"] ?? null);
        yield "\" />
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0\" />
";
        // line 9
        if (($context["description"] ?? null)) {
            // line 10
            yield "<meta name=\"description\" content=\"";
            yield ($context["description"] ?? null);
            yield "\" />
";
        }
        // line 12
        if (($context["keywords"] ?? null)) {
            // line 13
            yield "<meta name=\"keywords\" content=\"";
            yield ($context["keywords"] ?? null);
            yield "\" />
";
        }
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["stylespts"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["stylepts"]) {
            // line 16
            yield "<link href=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["stylepts"], "href", [], "any", false, false, false, 16);
            yield "\" type=\"text/css\" rel=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["stylepts"], "rel", [], "any", false, false, false, 16);
            yield "\" media=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["stylepts"], "media", [], "any", false, false, false, 16);
            yield "\" />
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['stylepts'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        yield "<script type=\"text/javascript\" src=\"";
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/jquery-2.1.1.min.js\"></script>
<script type=\"text/javascript\" src=\"";
        // line 19
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/js/bootstrap.min.js\"></script>
<link href=\"";
        // line 20
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap1/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"";
        // line 21
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/font-awesome/css/font-awesome.min.css\" type=\"text/css\" rel=\"stylesheet\" />
<script src=\"";
        // line 22
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js\" type=\"text/javascript\"></script>
<script src=\"";
        // line 23
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js\" type=\"text/javascript\"></script>
<script src=\"";
        // line 24
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js\" type=\"text/javascript\"></script>
<link href=\"";
        // line 25
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css\" type=\"text/css\" rel=\"stylesheet\" media=\"screen\" />

<link href=\"";
        // line 27
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"";
        // line 28
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/theme/default/stylesheet/purpletree/custom-a.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"";
        // line 29
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/adminstylesheet-a.css\" type=\"text/css\" rel=\"stylesheet\" />

<link href=\"";
        // line 31
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"";
        // line 32
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/theme/default/stylesheet/purpletree/custom.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"";
        // line 33
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/adminstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />

<link href=\"";
        // line 35
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/commonstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />
";
        // line 36
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["scriptspts"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["scriptpts"]) {
            // line 37
            yield "<script src=\"";
            yield $context["scriptpts"];
            yield "\" type=\"text/javascript\"></script>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['scriptpts'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        yield "\t\t\t<script src=\"";
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/common.js\" type=\"text/javascript\"></script>
";
        // line 40
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["links"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 41
            yield "<link href=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["link"], "href", [], "any", false, false, false, 41);
            yield "\" rel=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["link"], "rel", [], "any", false, false, false, 41);
            yield "\" />
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['link'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["analytics"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["analytic"]) {
            // line 44
            yield $context["analytic"];
            yield "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['analytic'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "</head>
<body> 
<body> 
<div id=\"container\">
<header id=\"header\" class=\"navbar navbar-static-top\">
  <div class=\"container-fluid\">
    <div id=\"header-logo\" class=\"pts-navbar-header\">";
        // line 52
        if (($context["logo"] ?? null)) {
            yield "<a class=\"pts-pull-left navbar-brand ptsc-header-padding\" href=\"";
            yield ($context["home"] ?? null);
            yield "\"  /><img src=\"";
            yield ($context["logo"] ?? null);
            yield "\" title=\"";
            yield ($context["name"] ?? null);
            yield "\" alt=\"";
            yield ($context["name"] ?? null);
            yield "\" class=\"img-responsive ptsc-header-height\" /></a>";
        } else {
            // line 53
            yield "          <h1><a  class=\"pts-pull-left navbar-brand\" href=\"";
            yield ($context["home"] ?? null);
            yield "\">";
            yield ($context["name"] ?? null);
            yield "</a></h1>
          ";
        }
        // line 54
        yield "</div>

    <a href=\"#\" id=\"button-menu\" class=\"hidden-md hidden-lg\"><span class=\"fa fa-bars\"></span></a>
\t\t
\t<div class=\"ptssellertop pts-pull-right\">
\t\t";
        // line 59
        yield ($context["currency"] ?? null);
        yield "
\t\t";
        // line 60
        yield ($context["language"] ?? null);
        yield "
\t</div>
  </div>
</header>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/purpletree_pos/catalog/view/template/pos/header.twig";
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
        return array (  244 => 60,  240 => 59,  233 => 54,  225 => 53,  213 => 52,  205 => 46,  197 => 44,  193 => 43,  182 => 41,  178 => 40,  173 => 39,  164 => 37,  160 => 36,  156 => 35,  151 => 33,  147 => 32,  143 => 31,  138 => 29,  134 => 28,  130 => 27,  125 => 25,  121 => 24,  117 => 23,  113 => 22,  109 => 21,  105 => 20,  101 => 19,  96 => 18,  83 => 16,  79 => 15,  73 => 13,  71 => 12,  65 => 10,  63 => 9,  58 => 7,  54 => 6,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html dir=\"{{ direction }}\" lang=\"{{ lang }}\">
<head>

<meta charset=\"UTF-8\" />
<title> {{ heading_title1 }}</title>
<base href=\"{{ base }}\" />
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0\" />
{% if description %}
<meta name=\"description\" content=\"{{ description }}\" />
{% endif %}
{% if keywords %}
<meta name=\"keywords\" content=\"{{ keywords }}\" />
{% endif %}
{% for stylepts in stylespts %}
<link href=\"{{ stylepts.href }}\" type=\"text/css\" rel=\"{{ stylepts.rel }}\" media=\"{{ stylepts.media }}\" />
{% endfor %}
<script type=\"text/javascript\" src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/jquery-2.1.1.min.js\"></script>
<script type=\"text/javascript\" src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/js/bootstrap.min.js\"></script>
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap1/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/font-awesome/css/font-awesome.min.css\" type=\"text/css\" rel=\"stylesheet\" />
<script src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js\" type=\"text/javascript\"></script>
<script src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js\" type=\"text/javascript\"></script>
<script src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js\" type=\"text/javascript\"></script>
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css\" type=\"text/css\" rel=\"stylesheet\" media=\"screen\" />

<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/theme/default/stylesheet/purpletree/custom-a.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/adminstylesheet-a.css\" type=\"text/css\" rel=\"stylesheet\" />

<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/theme/default/stylesheet/purpletree/custom.css\" type=\"text/css\" rel=\"stylesheet\" />
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/adminstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />

<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/commonstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />
{% for scriptpts in scriptspts %}
<script src=\"{{ scriptpts }}\" type=\"text/javascript\"></script>
{% endfor %}
\t\t\t<script src=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/common.js\" type=\"text/javascript\"></script>
{% for link in links %}
<link href=\"{{ link.href }}\" rel=\"{{ link.rel }}\" />
{% endfor %}
{% for analytic in analytics %}
{{ analytic }}
{% endfor %}
</head>
<body> 
<body> 
<div id=\"container\">
<header id=\"header\" class=\"navbar navbar-static-top\">
  <div class=\"container-fluid\">
    <div id=\"header-logo\" class=\"pts-navbar-header\">{% if logo %}<a class=\"pts-pull-left navbar-brand ptsc-header-padding\" href=\"{{ home }}\"  /><img src=\"{{ logo }}\" title=\"{{ name }}\" alt=\"{{ name }}\" class=\"img-responsive ptsc-header-height\" /></a>{% else %}
          <h1><a  class=\"pts-pull-left navbar-brand\" href=\"{{ home }}\">{{ name }}</a></h1>
          {% endif %}</div>

    <a href=\"#\" id=\"button-menu\" class=\"hidden-md hidden-lg\"><span class=\"fa fa-bars\"></span></a>
\t\t
\t<div class=\"ptssellertop pts-pull-right\">
\t\t{{ currency }}
\t\t{{ language }}
\t</div>
  </div>
</header>", "extension/purpletree_pos/catalog/view/template/pos/header.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/purpletree_pos/catalog/view/template/pos/header.twig");
    }
}

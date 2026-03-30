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

/* extension/purpletree_pos/catalog/view/template/pos/agentlogin.twig */
class __TwigTemplate_8b3caeba6a18078435aa46bed1fd223c extends Template
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
        yield ($context["header"] ?? null);
        yield "
<div id=\"content\">
  <div class=\"container-fluid\">
  <br>
    <br>
    <div class=\"row\">
\t\t\t\t<div class=\"col-sm-offset-4 col-sm-4\">
\t\t\t\t  ";
        // line 8
        if (($context["success"] ?? null)) {
            // line 9
            yield "\t<div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> ";
            yield ($context["success"] ?? null);
            yield "</div>
\t";
        }
        // line 11
        yield "\t";
        if (($context["error_warning"] ?? null)) {
            // line 12
            yield "\t<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            yield ($context["error_warning"] ?? null);
            yield "</div>
\t";
        }
        // line 13
        yield " 
\t\t\t\t\t<div class=\"pts-well pos-login-form\">
\t\t\t\t\t\t<h2 class=\"text-center\">";
        // line 15
        yield ($context["text_agent_login"] ?? null);
        yield "</h2>
\t\t\t\t\t\t<form action=\"";
        // line 16
        yield ($context["action"] ?? null);
        yield "\" method=\"post\" id=\"regForm\" enctype=\"multipart/form-data\">
\t\t\t\t\t\t\t<div class=\"pts-form-group\">
\t\t\t\t\t\t\t\t<label class=\"pts-control-label col-form-label\" for=\"agent-email\">";
        // line 18
        yield ($context["entry_email"] ?? null);
        yield "</label>
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"email\" value=\"";
        // line 19
        yield ($context["email"] ?? null);
        yield "\" placeholder=\"";
        yield ($context["entry_email"] ?? null);
        yield "\" id=\"agent-email\" class=\"pts-form-control\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"pts-form-group \">
\t\t\t\t\t\t\t\t<label class=\"pts-control-label col-form-label\" for=\"agent-password\">";
        // line 22
        yield ($context["entry_password"] ?? null);
        yield "</label>
\t\t\t\t\t\t\t\t<input type=\"password\" name=\"password\" value=\"";
        // line 23
        yield ($context["password"] ?? null);
        yield "\" placeholder=\"";
        yield ($context["entry_password"] ?? null);
        yield "\" id=\"agent-password\" class=\"pts-form-control\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<a href=\"";
        // line 25
        yield ($context["forgotten"] ?? null);
        yield "\">";
        yield ($context["text_forgotten"] ?? null);
        yield "</a>
\t\t\t\t\t\t\t<div class=\"pts-row\">
\t\t\t\t\t\t\t\t<div class=\"pts-col-sm-5 pts-pull-left-1  pts-mt-3\">
\t\t\t\t\t\t\t\t\t<input id=\"agent-login-button\" type=\"submit\" value=\"";
        // line 28
        yield ($context["button_login"] ?? null);
        yield "\" class=\"pts-btn pts-btn-primary\" />
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"pts-pr-3 text-center pts-pull-right-1 pts-mt-3\">
\t\t\t\t\t\t\t\t\t<!--<p class=\"new-agent-register-here\"><span class=\"login-agent\" ><i class=\"fa fa-user-o fa-users\"></i> ";
        // line 31
        yield ($context["text_new_agent"] ?? null);
        yield "  </span><a href=\"";
        yield ($context["agentregister"] ?? null);
        yield "\" class=\"ptsc-sregister-sellog\" id=\"pts-reg-agent\"> ";
        yield ($context["text_register_new"] ?? null);
        yield " </a></p>-->
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t";
        // line 34
        if (($context["redirect"] ?? null)) {
            // line 35
            yield "\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"redirect\" value=\"";
            yield ($context["redirect"] ?? null);
            yield "\" />
\t\t\t\t\t\t\t\t";
        }
        // line 37
        yield "\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t</div>
</div>
<link href=\"";
        // line 43
        yield ($context["baseurl"] ?? null);
        yield "extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/commonstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />
<style>
#button-menu {
\tdisplay: none;
}
.pos-login-form {
    margin-top: 20px;
    background: #ffffff;
    border: 1px solid #e3e3e3;
}
</style>
";
        // line 54
        yield ($context["footer"] ?? null);
        yield "      ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/purpletree_pos/catalog/view/template/pos/agentlogin.twig";
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
        return array (  159 => 54,  145 => 43,  137 => 37,  131 => 35,  129 => 34,  119 => 31,  113 => 28,  105 => 25,  98 => 23,  94 => 22,  86 => 19,  82 => 18,  77 => 16,  73 => 15,  69 => 13,  63 => 12,  60 => 11,  54 => 9,  52 => 8,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{{ header }}
<div id=\"content\">
  <div class=\"container-fluid\">
  <br>
    <br>
    <div class=\"row\">
\t\t\t\t<div class=\"col-sm-offset-4 col-sm-4\">
\t\t\t\t  {% if success %}
\t<div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> {{ success }}</div>
\t{% endif %}
\t{% if error_warning %}
\t<div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> {{ error_warning }}</div>
\t{% endif %} 
\t\t\t\t\t<div class=\"pts-well pos-login-form\">
\t\t\t\t\t\t<h2 class=\"text-center\">{{ text_agent_login }}</h2>
\t\t\t\t\t\t<form action=\"{{ action }}\" method=\"post\" id=\"regForm\" enctype=\"multipart/form-data\">
\t\t\t\t\t\t\t<div class=\"pts-form-group\">
\t\t\t\t\t\t\t\t<label class=\"pts-control-label col-form-label\" for=\"agent-email\">{{ entry_email }}</label>
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"email\" value=\"{{ email }}\" placeholder=\"{{ entry_email }}\" id=\"agent-email\" class=\"pts-form-control\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"pts-form-group \">
\t\t\t\t\t\t\t\t<label class=\"pts-control-label col-form-label\" for=\"agent-password\">{{ entry_password }}</label>
\t\t\t\t\t\t\t\t<input type=\"password\" name=\"password\" value=\"{{ password }}\" placeholder=\"{{ entry_password }}\" id=\"agent-password\" class=\"pts-form-control\" />
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<a href=\"{{ forgotten }}\">{{ text_forgotten }}</a>
\t\t\t\t\t\t\t<div class=\"pts-row\">
\t\t\t\t\t\t\t\t<div class=\"pts-col-sm-5 pts-pull-left-1  pts-mt-3\">
\t\t\t\t\t\t\t\t\t<input id=\"agent-login-button\" type=\"submit\" value=\"{{ button_login }}\" class=\"pts-btn pts-btn-primary\" />
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"pts-pr-3 text-center pts-pull-right-1 pts-mt-3\">
\t\t\t\t\t\t\t\t\t<!--<p class=\"new-agent-register-here\"><span class=\"login-agent\" ><i class=\"fa fa-user-o fa-users\"></i> {{ text_new_agent }}  </span><a href=\"{{ agentregister }}\" class=\"ptsc-sregister-sellog\" id=\"pts-reg-agent\"> {{ text_register_new }} </a></p>-->
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t{% if redirect %}
\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"redirect\" value=\"{{ redirect }}\" />
\t\t\t\t\t\t\t\t{% endif %}
\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t</div>
</div>
<link href=\"{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/css/stylesheet/commonstylesheet.css\" type=\"text/css\" rel=\"stylesheet\" />
<style>
#button-menu {
\tdisplay: none;
}
.pos-login-form {
    margin-top: 20px;
    background: #ffffff;
    border: 1px solid #e3e3e3;
}
</style>
{{ footer }}      ", "extension/purpletree_pos/catalog/view/template/pos/agentlogin.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/purpletree_pos/catalog/view/template/pos/agentlogin.twig");
    }
}

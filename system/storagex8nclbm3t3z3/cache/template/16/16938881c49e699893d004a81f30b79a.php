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

/* admin/view/template/common/login.twig */
class __TwigTemplate_085eda92fdc8b0df135457ff262a9723 extends Template
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
    <br/>
    <br/>
    <div class=\"row justify-content-sm-center\">
      <div class=\"col-sm-10 col-md-8 col-lg-5\">
        <div class=\"card\">
          <div class=\"card-header\" style=\"color:white\"><i class=\"fa-solid fa-lock\"></i> ";
        // line 9
        yield ($context["text_login"] ?? null);
        yield "</div>
          <div class=\"card-body\">
            <form id=\"form-login\" action=\"";
        // line 11
        yield ($context["login"] ?? null);
        yield "\" method=\"post\" data-oc-toggle=\"ajax\">
              ";
        // line 12
        if (($context["error_warning"] ?? null)) {
            // line 13
            yield "                <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> ";
            yield ($context["error_warning"] ?? null);
            yield " <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>
              ";
        }
        // line 15
        yield "              ";
        if (($context["success"] ?? null)) {
            // line 16
            yield "                <div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> ";
            yield ($context["success"] ?? null);
            yield " <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>
              ";
        }
        // line 18
        yield "              <div class=\"mb-3\">
                <label for=\"input-username\" class=\"form-label\" style=\"color:white\">";
        // line 19
        yield ($context["entry_username"] ?? null);
        yield "</label>
                <div class=\"input-group\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-user\"></i></div>
                  <input type=\"text\" name=\"username\" value=\"\" placeholder=\"";
        // line 22
        yield ($context["entry_username"] ?? null);
        yield "\" id=\"input-username\" class=\"form-control\"/>
                </div>
              </div>
              <div class=\"mb-3\">
                <label for=\"input-password\" class=\"form-label\" style=\"color:white\">";
        // line 26
        yield ($context["entry_password"] ?? null);
        yield "</label>
                <div class=\"input-group mb-2\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-lock\"></i></div>
                  <input type=\"password\" name=\"password\" value=\"\" placeholder=\"";
        // line 29
        yield ($context["entry_password"] ?? null);
        yield "\" id=\"input-password\" class=\"form-control\"/>
                </div>
              </div>
              <div class=\"mb-3\">
                <label for=\"input-token\" class=\"form-label\" style=\"color:white\">";
        // line 33
        yield ($context["entry_token"] ?? null);
        yield "</label>
                <div class=\"input-group mb-2\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-lock\"></i></div>
                  <input type=\"password\" name=\"token\" value=\"\" placeholder=\"";
        // line 36
        yield ($context["entry_token"] ?? null);
        yield "\" id=\"input-token\" class=\"form-control\"/>
                </div>
                
             <!-- UPDATED Forgotten Password link -->
            <div style=\"display: flex; justify-content: space-between; align-items: center; margin-top: 7px; padding:7px;\">
              <a href=\"#\" style=\"color: white; text-decoration: underline;\">
              Forgotten Password
              </a> 
            <button style=\"background-color: #007bff; color: white; border: none; padding:5px 13px; border-radius: 7px; cursor: pointer; position: relative; top: 5px;\">
              Login
            </button>
           </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--UPDATED JavaScript to handle click -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const forgotLink = document.getElementById('forgotten-password');
    if (forgotLink) {
      forgotLink.addEventListener('click', function (e) {
        e.preventDefault();
        alert('Forgot password clicked! Please validate your email before resetting the password.');
      });
    }
  });
</script>
";
        // line 68
        yield ($context["footer"] ?? null);
        yield "
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/common/login.twig";
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
        return array (  149 => 68,  114 => 36,  108 => 33,  101 => 29,  95 => 26,  88 => 22,  82 => 19,  79 => 18,  73 => 16,  70 => 15,  64 => 13,  62 => 12,  58 => 11,  53 => 9,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{{ header }}
<div id=\"content\">
  <div class=\"container-fluid\">
    <br/>
    <br/>
    <div class=\"row justify-content-sm-center\">
      <div class=\"col-sm-10 col-md-8 col-lg-5\">
        <div class=\"card\">
          <div class=\"card-header\" style=\"color:white\"><i class=\"fa-solid fa-lock\"></i> {{ text_login }}</div>
          <div class=\"card-body\">
            <form id=\"form-login\" action=\"{{ login }}\" method=\"post\" data-oc-toggle=\"ajax\">
              {% if error_warning %}
                <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa-solid fa-circle-exclamation\"></i> {{ error_warning }} <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>
              {% endif %}
              {% if success %}
                <div class=\"alert alert-success alert-dismissible\"><i class=\"fa-solid fa-check-circle\"></i> {{ success }} <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button></div>
              {% endif %}
              <div class=\"mb-3\">
                <label for=\"input-username\" class=\"form-label\" style=\"color:white\">{{ entry_username }}</label>
                <div class=\"input-group\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-user\"></i></div>
                  <input type=\"text\" name=\"username\" value=\"\" placeholder=\"{{ entry_username }}\" id=\"input-username\" class=\"form-control\"/>
                </div>
              </div>
              <div class=\"mb-3\">
                <label for=\"input-password\" class=\"form-label\" style=\"color:white\">{{ entry_password }}</label>
                <div class=\"input-group mb-2\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-lock\"></i></div>
                  <input type=\"password\" name=\"password\" value=\"\" placeholder=\"{{ entry_password }}\" id=\"input-password\" class=\"form-control\"/>
                </div>
              </div>
              <div class=\"mb-3\">
                <label for=\"input-token\" class=\"form-label\" style=\"color:white\">{{ entry_token }}</label>
                <div class=\"input-group mb-2\">
                  <div class=\"input-group-text\"><i class=\"fa-solid fa-lock\"></i></div>
                  <input type=\"password\" name=\"token\" value=\"\" placeholder=\"{{ entry_token }}\" id=\"input-token\" class=\"form-control\"/>
                </div>
                
             <!-- UPDATED Forgotten Password link -->
            <div style=\"display: flex; justify-content: space-between; align-items: center; margin-top: 7px; padding:7px;\">
              <a href=\"#\" style=\"color: white; text-decoration: underline;\">
              Forgotten Password
              </a> 
            <button style=\"background-color: #007bff; color: white; border: none; padding:5px 13px; border-radius: 7px; cursor: pointer; position: relative; top: 5px;\">
              Login
            </button>
           </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--UPDATED JavaScript to handle click -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const forgotLink = document.getElementById('forgotten-password');
    if (forgotLink) {
      forgotLink.addEventListener('click', function (e) {
        e.preventDefault();
        alert('Forgot password clicked! Please validate your email before resetting the password.');
      });
    }
  });
</script>
{{ footer }}
", "admin/view/template/common/login.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/admin/view/template/common/login.twig");
    }
}

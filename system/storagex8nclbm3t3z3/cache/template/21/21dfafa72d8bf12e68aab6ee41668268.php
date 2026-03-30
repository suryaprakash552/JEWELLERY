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

/* catalog/view/template/mail/order_add.twig */
class __TwigTemplate_cbd7aff0c9f1bf08b3eb25398f48fc92 extends Template
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
        yield "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd\">
<html>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
  <title>";
        // line 5
        yield ($context["title"] ?? null);
        yield "</title>
</head>
<body style=\"font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;\">
<div style=\"width: 680px;\">
  ";
        // line 9
        if (($context["logo"] ?? null)) {
            // line 10
            yield "    <a href=\"";
            yield ($context["store_url"] ?? null);
            yield "\" title=\"";
            yield ($context["store_name"] ?? null);
            yield "\"><img src=\"";
            yield ($context["logo"] ?? null);
            yield "\" alt=\"";
            yield ($context["store_name"] ?? null);
            yield "\" style=\"margin-bottom: 20px; border: none;\"/></a>
  ";
        } else {
            // line 12
            yield "    <h2><a href=\"";
            yield ($context["store_url"] ?? null);
            yield "\" title=\"";
            yield ($context["store_name"] ?? null);
            yield "\">";
            yield ($context["store_name"] ?? null);
            yield "</a></h2>
  ";
        }
        // line 14
        yield "  <p style=\"margin-top: 0px; margin-bottom: 20px;\">";
        yield ($context["text_greeting"] ?? null);
        yield "</p>
  ";
        // line 15
        if (($context["customer_id"] ?? null)) {
            // line 16
            yield "    <p style=\"margin-top: 0px; margin-bottom: 20px;\">";
            yield ($context["text_link"] ?? null);
            yield "</p>
    <p style=\"margin-top: 0px; margin-bottom: 20px;\"><a href=\"";
            // line 17
            yield ($context["link"] ?? null);
            yield "\">";
            yield ($context["link"] ?? null);
            yield "</a></p>
  ";
        }
        // line 19
        yield "  ";
        if (($context["download"] ?? null)) {
            // line 20
            yield "    <p style=\"margin-top: 0px; margin-bottom: 20px;\">";
            yield ($context["text_download"] ?? null);
            yield "</p>
    <p style=\"margin-top: 0px; margin-bottom: 20px;\"><a href=\"";
            // line 21
            yield ($context["download"] ?? null);
            yield "\">";
            yield ($context["download"] ?? null);
            yield "</a></p>
    ";
        }
        // line 23
        yield "  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\" colspan=\"2\">";
        // line 26
        yield ($context["text_order_detail"] ?? null);
        yield "</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\"><b>";
        // line 31
        yield ($context["text_order_id"] ?? null);
        yield "</b> ";
        yield ($context["order_id"] ?? null);
        yield "
          <br/>
          <b>";
        // line 33
        yield ($context["text_date_added"] ?? null);
        yield "</b> ";
        yield ($context["date_added"] ?? null);
        yield "
          <br/>
          <b>";
        // line 35
        yield ($context["text_payment_method"] ?? null);
        yield "</b> ";
        yield ($context["payment_method"] ?? null);
        yield "
          <br/>
          ";
        // line 37
        if (($context["shipping_method"] ?? null)) {
            yield " <b>";
            yield ($context["text_shipping_method"] ?? null);
            yield "</b> ";
            yield ($context["shipping_method"] ?? null);
            yield "
          ";
        }
        // line 38
        yield "</td>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\"><b>";
        // line 39
        yield ($context["text_email"] ?? null);
        yield "</b> ";
        yield ($context["email"] ?? null);
        yield "
          <br/>
          <b>";
        // line 41
        yield ($context["text_telephone"] ?? null);
        yield "</b> ";
        yield ($context["telephone"] ?? null);
        yield "
          <br/>
          <b>";
        // line 43
        yield ($context["text_ip"] ?? null);
        yield "</b> ";
        yield ($context["ip"] ?? null);
        yield "
          <br/>
          <b>";
        // line 45
        yield ($context["text_order_status"] ?? null);
        yield "</b> ";
        yield ($context["order_status"] ?? null);
        yield "
          <br/></td>
      </tr>
    </tbody>
  </table>
  ";
        // line 50
        if (($context["comment"] ?? null)) {
            // line 51
            yield "    <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
      <thead>
        <tr>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">";
            // line 54
            yield ($context["text_instruction"] ?? null);
            yield "</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">";
            // line 59
            yield ($context["comment"] ?? null);
            yield "</td>
        </tr>
      </tbody>
    </table>
  ";
        }
        // line 64
        yield "  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">";
        // line 67
        yield ($context["text_payment_address"] ?? null);
        yield "</td>
        ";
        // line 68
        if (($context["shipping_address"] ?? null)) {
            // line 69
            yield "          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">";
            yield ($context["text_shipping_address"] ?? null);
            yield "</td>
        ";
        }
        // line 71
        yield "      </tr>
    </thead>
    <tbody>
      <tr>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">";
        // line 75
        yield ($context["payment_address"] ?? null);
        yield "</td>
        ";
        // line 76
        if (($context["shipping_address"] ?? null)) {
            // line 77
            yield "          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">";
            yield ($context["shipping_address"] ?? null);
            yield "</td>
        ";
        }
        // line 79
        yield "      </tr>
    </tbody>
  </table>
  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">";
        // line 85
        yield ($context["text_product"] ?? null);
        yield "</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">";
        // line 86
        yield ($context["text_model"] ?? null);
        yield "</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">";
        // line 87
        yield ($context["text_quantity"] ?? null);
        yield "</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">";
        // line 88
        yield ($context["text_price"] ?? null);
        yield "</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">";
        // line 89
        yield ($context["text_total"] ?? null);
        yield "</td>
      </tr>
    </thead>
    <tbody>
      ";
        // line 93
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["products"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 94
            yield "        <tr>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">";
            // line 95
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, false, 95);
            yield "
            ";
            // line 96
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["product"], "option", [], "any", false, false, false, 96));
            foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                // line 97
                yield "              <br/>
              <small> - ";
                // line 98
                yield CoreExtension::getAttribute($this->env, $this->source, $context["option"], "name", [], "any", false, false, false, 98);
                yield ": ";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["option"], "value", [], "any", false, false, false, 98);
                yield "</small>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['option'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 99
            yield "</td>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">";
            // line 100
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "model", [], "any", false, false, false, 100);
            yield "</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">";
            // line 101
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "quantity", [], "any", false, false, false, 101);
            yield "</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">";
            // line 102
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "price", [], "any", false, false, false, 102);
            yield "</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">";
            // line 103
            yield CoreExtension::getAttribute($this->env, $this->source, $context["product"], "total", [], "any", false, false, false, 103);
            yield "</td>
        </tr>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['product'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 106
        yield "    </tbody>
    <tfoot>
      ";
        // line 108
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["totals"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["total"]) {
            // line 109
            yield "        <tr>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\" colspan=\"4\"><b>";
            // line 110
            yield CoreExtension::getAttribute($this->env, $this->source, $context["total"], "title", [], "any", false, false, false, 110);
            yield ":</b></td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">";
            // line 111
            yield CoreExtension::getAttribute($this->env, $this->source, $context["total"], "text", [], "any", false, false, false, 111);
            yield "</td>
        </tr>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['total'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 114
        yield "    </tfoot>
  </table>
  <p style=\"margin-top: 0px; margin-bottom: 20px;\">";
        // line 116
        yield ($context["text_footer"] ?? null);
        yield "</p>
</div>
</body>
</html>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "catalog/view/template/mail/order_add.twig";
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
        return array (  359 => 116,  355 => 114,  346 => 111,  342 => 110,  339 => 109,  335 => 108,  331 => 106,  322 => 103,  318 => 102,  314 => 101,  310 => 100,  307 => 99,  297 => 98,  294 => 97,  290 => 96,  286 => 95,  283 => 94,  279 => 93,  272 => 89,  268 => 88,  264 => 87,  260 => 86,  256 => 85,  248 => 79,  242 => 77,  240 => 76,  236 => 75,  230 => 71,  224 => 69,  222 => 68,  218 => 67,  213 => 64,  205 => 59,  197 => 54,  192 => 51,  190 => 50,  180 => 45,  173 => 43,  166 => 41,  159 => 39,  156 => 38,  147 => 37,  140 => 35,  133 => 33,  126 => 31,  118 => 26,  113 => 23,  106 => 21,  101 => 20,  98 => 19,  91 => 17,  86 => 16,  84 => 15,  79 => 14,  69 => 12,  57 => 10,  55 => 9,  48 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd\">
<html>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
  <title>{{ title }}</title>
</head>
<body style=\"font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;\">
<div style=\"width: 680px;\">
  {% if logo %}
    <a href=\"{{ store_url }}\" title=\"{{ store_name }}\"><img src=\"{{ logo }}\" alt=\"{{ store_name }}\" style=\"margin-bottom: 20px; border: none;\"/></a>
  {% else %}
    <h2><a href=\"{{ store_url }}\" title=\"{{ store_name }}\">{{ store_name }}</a></h2>
  {% endif %}
  <p style=\"margin-top: 0px; margin-bottom: 20px;\">{{ text_greeting }}</p>
  {% if customer_id %}
    <p style=\"margin-top: 0px; margin-bottom: 20px;\">{{ text_link }}</p>
    <p style=\"margin-top: 0px; margin-bottom: 20px;\"><a href=\"{{ link }}\">{{ link }}</a></p>
  {% endif %}
  {% if download %}
    <p style=\"margin-top: 0px; margin-bottom: 20px;\">{{ text_download }}</p>
    <p style=\"margin-top: 0px; margin-bottom: 20px;\"><a href=\"{{ download }}\">{{ download }}</a></p>
    {% endif %}
  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\" colspan=\"2\">{{ text_order_detail }}</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\"><b>{{ text_order_id }}</b> {{ order_id }}
          <br/>
          <b>{{ text_date_added }}</b> {{ date_added }}
          <br/>
          <b>{{ text_payment_method }}</b> {{ payment_method }}
          <br/>
          {% if shipping_method %} <b>{{ text_shipping_method }}</b> {{ shipping_method }}
          {% endif %}</td>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\"><b>{{ text_email }}</b> {{ email }}
          <br/>
          <b>{{ text_telephone }}</b> {{ telephone }}
          <br/>
          <b>{{ text_ip }}</b> {{ ip }}
          <br/>
          <b>{{ text_order_status }}</b> {{ order_status }}
          <br/></td>
      </tr>
    </tbody>
  </table>
  {% if comment %}
    <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
      <thead>
        <tr>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">{{ text_instruction }}</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">{{ comment }}</td>
        </tr>
      </tbody>
    </table>
  {% endif %}
  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">{{ text_payment_address }}</td>
        {% if shipping_address %}
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">{{ text_shipping_address }}</td>
        {% endif %}
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">{{ payment_address }}</td>
        {% if shipping_address %}
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">{{ shipping_address }}</td>
        {% endif %}
      </tr>
    </tbody>
  </table>
  <table style=\"border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;\">
    <thead>
      <tr>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">{{ text_product }}</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;\">{{ text_model }}</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">{{ text_quantity }}</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">{{ text_price }}</td>
        <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;\">{{ text_total }}</td>
      </tr>
    </thead>
    <tbody>
      {% for product in products %}
        <tr>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">{{ product.name }}
            {% for option in product.option %}
              <br/>
              <small> - {{ option.name }}: {{ option.value }}</small>
            {% endfor %}</td>
          <td style=\"font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;\">{{ product.model }}</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">{{ product.quantity }}</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">{{ product.price }}</td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">{{ product.total }}</td>
        </tr>
      {% endfor %}
    </tbody>
    <tfoot>
      {% for total in totals %}
        <tr>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\" colspan=\"4\"><b>{{ total.title }}:</b></td>
          <td style=\"font-size: 12px;\tborder-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;\">{{ total.text }}</td>
        </tr>
      {% endfor %}
    </tfoot>
  </table>
  <p style=\"margin-top: 0px; margin-bottom: 20px;\">{{ text_footer }}</p>
</div>
</body>
</html>
", "catalog/view/template/mail/order_add.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/catalog/view/template/mail/order_add.twig");
    }
}

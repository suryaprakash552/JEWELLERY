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

/* extension/opencart/admin/view/template/dashboard/chart_info.twig */
class __TwigTemplate_36da33a8f33d48d84963aba20a9c9ec3 extends Template
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
  <div class=\"card-header\">
    <div class=\"float-end\"><a href=\"#\" class=\"dropdown-toggle\" data-bs-toggle=\"dropdown\"><i class=\"fa-regular fa-calendar\"></i> <i class=\"fa-solid fa-caret-down\"></i></a>
      <div id=\"range\" class=\"dropdown-menu dropdown-menu-right\">
        <a href=\"day\" class=\"dropdown-item\">";
        // line 5
        yield ($context["text_day"] ?? null);
        yield "</a> <a href=\"week\" class=\"dropdown-item\">";
        yield ($context["text_week"] ?? null);
        yield "</a> <a href=\"month\" class=\"dropdown-item active\">";
        yield ($context["text_month"] ?? null);
        yield "</a> <a href=\"year\" class=\"dropdown-item\">";
        yield ($context["text_year"] ?? null);
        yield "</a>
      </div>
    </div>
    <i class=\"fa-solid fa-chart-bar\"></i> ";
        // line 8
        yield ($context["heading_title"] ?? null);
        yield "
  </div>
  <div class=\"card-body\">
    <div id=\"chart-sale\" style=\"width: 100%; height: 260px;\"></div>
  </div>
</div>
<script type=\"text/javascript\" src=\"view/javascript/jquery/flot/jquery.flot.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/jquery/flot/jquery.flot.resize.min.js\"></script>
<script type=\"text/javascript\"><!--
\$('#range a').on('click', function(e) {
    e.preventDefault();

    \$(this).parent().find('a').removeClass('active');

    \$(this).addClass('active');

    \$.ajax({
        type: 'get',
        url: 'index.php?route=extension/opencart/dashboard/chart.chart&user_token=";
        // line 26
        yield ($context["user_token"] ?? null);
        yield "&range=' + \$(this).attr('href'),
        dataType: 'json',
        success: function(json) {
            if (typeof json['order'] == 'undefined') {
                return false;
            }

            var option = {
                shadowSize: 0,
                colors: ['#9FD5F1', '#1065D2'],
                bars: {
                    show: true,
                    fill: true,
                    lineWidth: 1
                },
                grid: {
                    backgroundColor: '#FFFFFF',
                    hoverable: true
                },
                points: {
                    show: false
                },
                xaxis: {
                    show: true,
                    ticks: json['xaxis']
                }
            }

            \$.plot('#chart-sale', [json['order'], json['customer']], option);

            \$('#chart-sale').bind('plothover', function(event, pos, item) {
                \$('.tooltip').remove();

                if (item) {
                    var element = document.querySelector(\"#chart-sale\");
                    var rect = element.getBoundingClientRect();
                    var elementTop = rect.top + window.scrollY;
                    var elementLeft = rect.left + window.scrollX;

                    \$('<div id=\"tooltip\" class=\"tooltip top show\"><div class=\"tooltip-arrow\"></div><div class=\"tooltip-inner\">' + item.datapoint[1].toFixed(2) + '</div></div>').prependTo('#chart-sale');

                    \$('#tooltip').css({
                        position: 'absolute',
                        top: (elementTop - item.pageY) + 50,
                        left: (item.pageX - elementLeft),
                        pointer: 'cursor'
                    }).fadeIn('slow');

                    \$('#chart-sale').css('cursor', 'pointer');
                } else {
                    \$('#chart-sale').css('cursor', 'auto');
                }
            });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
        }
    });
});

\$('#range a.active').trigger('click');
//--></script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "extension/opencart/admin/view/template/dashboard/chart_info.twig";
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
        return array (  81 => 26,  60 => 8,  48 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"card mb-3\">
  <div class=\"card-header\">
    <div class=\"float-end\"><a href=\"#\" class=\"dropdown-toggle\" data-bs-toggle=\"dropdown\"><i class=\"fa-regular fa-calendar\"></i> <i class=\"fa-solid fa-caret-down\"></i></a>
      <div id=\"range\" class=\"dropdown-menu dropdown-menu-right\">
        <a href=\"day\" class=\"dropdown-item\">{{ text_day }}</a> <a href=\"week\" class=\"dropdown-item\">{{ text_week }}</a> <a href=\"month\" class=\"dropdown-item active\">{{ text_month }}</a> <a href=\"year\" class=\"dropdown-item\">{{ text_year }}</a>
      </div>
    </div>
    <i class=\"fa-solid fa-chart-bar\"></i> {{ heading_title }}
  </div>
  <div class=\"card-body\">
    <div id=\"chart-sale\" style=\"width: 100%; height: 260px;\"></div>
  </div>
</div>
<script type=\"text/javascript\" src=\"view/javascript/jquery/flot/jquery.flot.js\"></script>
<script type=\"text/javascript\" src=\"view/javascript/jquery/flot/jquery.flot.resize.min.js\"></script>
<script type=\"text/javascript\"><!--
\$('#range a').on('click', function(e) {
    e.preventDefault();

    \$(this).parent().find('a').removeClass('active');

    \$(this).addClass('active');

    \$.ajax({
        type: 'get',
        url: 'index.php?route=extension/opencart/dashboard/chart.chart&user_token={{ user_token }}&range=' + \$(this).attr('href'),
        dataType: 'json',
        success: function(json) {
            if (typeof json['order'] == 'undefined') {
                return false;
            }

            var option = {
                shadowSize: 0,
                colors: ['#9FD5F1', '#1065D2'],
                bars: {
                    show: true,
                    fill: true,
                    lineWidth: 1
                },
                grid: {
                    backgroundColor: '#FFFFFF',
                    hoverable: true
                },
                points: {
                    show: false
                },
                xaxis: {
                    show: true,
                    ticks: json['xaxis']
                }
            }

            \$.plot('#chart-sale', [json['order'], json['customer']], option);

            \$('#chart-sale').bind('plothover', function(event, pos, item) {
                \$('.tooltip').remove();

                if (item) {
                    var element = document.querySelector(\"#chart-sale\");
                    var rect = element.getBoundingClientRect();
                    var elementTop = rect.top + window.scrollY;
                    var elementLeft = rect.left + window.scrollX;

                    \$('<div id=\"tooltip\" class=\"tooltip top show\"><div class=\"tooltip-arrow\"></div><div class=\"tooltip-inner\">' + item.datapoint[1].toFixed(2) + '</div></div>').prependTo('#chart-sale');

                    \$('#tooltip').css({
                        position: 'absolute',
                        top: (elementTop - item.pageY) + 50,
                        left: (item.pageX - elementLeft),
                        pointer: 'cursor'
                    }).fadeIn('slow');

                    \$('#chart-sale').css('cursor', 'pointer');
                } else {
                    \$('#chart-sale').css('cursor', 'auto');
                }
            });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(thrownError + \"\\r\\n\" + xhr.statusText + \"\\r\\n\" + xhr.responseText);
        }
    });
});

\$('#range a.active').trigger('click');
//--></script>
", "extension/opencart/admin/view/template/dashboard/chart_info.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/extension/opencart/admin/view/template/dashboard/chart_info.twig");
    }
}

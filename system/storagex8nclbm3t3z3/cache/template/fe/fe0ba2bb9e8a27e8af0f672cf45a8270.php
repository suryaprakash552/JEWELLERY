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

/* admin/view/template/common/header.twig */
class __TwigTemplate_0ef3bc8312244d746e0b864348d8ca8a extends Template
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
  <meta charset=\"UTF-8\"/>
  <title>";
        // line 5
        yield ($context["title"] ?? null);
        yield "</title>
  <base href=\"";
        // line 6
        yield ($context["base"] ?? null);
        yield "\"/>
  ";
        // line 7
        if (($context["description"] ?? null)) {
            // line 8
            yield "    <meta name=\"description\" content=\"";
            yield ($context["description"] ?? null);
            yield "\"/>
  ";
        }
        // line 10
        yield "  ";
        if (($context["keywords"] ?? null)) {
            // line 11
            yield "    <meta name=\"keywords\" content=\"";
            yield ($context["keywords"] ?? null);
            yield "\"/>
  ";
        }
        // line 13
        yield "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\"/>
  <meta http-equiv=\"cache-control\" content=\"no-cache\">
  <meta http-equiv=\"expires\" content=\"0\">
  <link href=\"";
        // line 16
        yield ($context["bootstrap"] ?? null);
        yield "\" rel=\"stylesheet\" media=\"screen\"/>
  <link href=\"";
        // line 17
        yield ($context["icons"] ?? null);
        yield "\" rel=\"stylesheet\" type=\"text/css\"/>
  <link href=\"";
        // line 18
        yield ($context["stylesheet"] ?? null);
        yield "\" rel=\"stylesheet\" type=\"text/css\"/>
  <script src=\"";
        // line 19
        yield ($context["jquery"] ?? null);
        yield "\" type=\"text/javascript\"></script>
  <script type=\"text/javascript\" src=\"view/javascript/common.js\"></script>
  ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["styles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["style"]) {
            // line 22
            yield "    <link type=\"text/css\" href=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["style"], "href", [], "any", false, false, false, 22);
            yield "\" rel=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["style"], "rel", [], "any", false, false, false, 22);
            yield "\" media=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["style"], "media", [], "any", false, false, false, 22);
            yield "\"/>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['style'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        yield "  ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["links"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 25
            yield "    <link href=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["link"], "href", [], "any", false, false, false, 25);
            yield "\" rel=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["link"], "rel", [], "any", false, false, false, 25);
            yield "\"/>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['link'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        yield "  ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["scripts"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 28
            yield "    <script type=\"text/javascript\" src=\"";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["script"], "href", [], "any", false, false, false, 28);
            yield "\"></script>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['script'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 30
        yield "</head>
<body>
    <div id=\"sidebar-overlay\"></div>
<div id=\"alert\"></div>
<div id=\"container\">
  <header id=\"header\" class=\"navbar navbar-expand navbar-light bg-light\">
    <div class=\"container-fluid\">
      <a href=\"";
        // line 37
        yield ($context["home"] ?? null);
        yield "\" class=\"navbar-brand d-none d-lg-block\"><img src=\"view/image/logo.png\" alt=\"";
        yield ($context["heading_title"] ?? null);
        yield "\" title=\"";
        yield ($context["heading_title"] ?? null);
        yield "\"/>
      </a>
            <h3 style=\"
    font-size:33px; margin:0; font-weight:bold; color:#C9A24D; font-family:initial;text-align:left;\">
SALEEM GOLD COVERING</h3>

      ";
        // line 43
        if (($context["logged"] ?? null)) {
            // line 44
            yield "        <button type=\"button\" id=\"button-menu\" class=\"btn btn-link d-inline-block d-lg-none\"><i class=\"fa-solid fa-bars\"></i></button>
        <ul class=\"nav navbar-nav\">
          <li id=\"nav-notification\" class=\"nav-item dropdown\">
            <a href=\"#\" data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle\"><i class=\"fa-regular fa-bell\"></i>";
            // line 47
            if (($context["notification_total"] ?? null)) {
                yield " <span class=\"badge bg-danger\">";
                yield ($context["notification_total"] ?? null);
                yield "</span>";
            }
            yield "</a>
            <div class=\"dropdown-menu dropdown-menu-end\">
              ";
            // line 49
            if (($context["notifications"] ?? null)) {
                // line 50
                yield "                ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(($context["notifications"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["notification"]) {
                    // line 51
                    yield "                  <a href=\"";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "href", [], "any", false, false, false, 51);
                    yield "\" data-bs-toggle=\"modal\" class=\"dropdown-item\">";
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "title", [], "any", false, false, false, 51);
                    yield "</a>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['notification'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 53
                yield "                <a href=\"";
                yield ($context["notification_all"] ?? null);
                yield "\" class=\"dropdown-item text-center text-primary\">";
                yield ($context["text_notification_all"] ?? null);
                yield "</a>
              ";
            } else {
                // line 55
                yield "                <span class=\"dropdown-item text-center\">";
                yield ($context["text_no_results"] ?? null);
                yield "</span>
              ";
            }
            // line 57
            yield "            </div>
          </li>
          <li class=\"nav-item\">
  <a href=\"";
            // line 60
            yield ($context["setting_store"] ?? null);
            yield "\" class=\"nav-link\" title=\"Settings\"style=\"font-size:23px;margin-top:6px;\">
    <i class=\"fa-solid fa-gear\"></i>
  </a>
</li>


          <li id=\"nav-language\" class=\"nav-item dropdown\">";
            // line 66
            yield ($context["language"] ?? null);
            yield "</li>
          <li id=\"nav-profile\" class=\"nav-item dropdown\">
            <a href=\"#\" data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle\"><img src=\"";
            // line 68
            yield ($context["logoimage"] ?? null);
            yield "\" alt=\"";
            yield ($context["firstname"] ?? null);
            yield " ";
            yield ($context["lastname"] ?? null);
            yield "\" title=\"";
            yield ($context["firstname"] ?? null);
            yield " ";
            yield ($context["lastname"] ?? null);
            yield "\" class=\"rounded-circle\"/><span class=\"d-none d-md-inline d-lg-inline\">&nbsp;&nbsp;&nbsp;";
            yield ($context["firstname"] ?? null);
            yield " ";
            yield ($context["lastname"] ?? null);
            yield " <i class=\"fa-solid fa-caret-down fa-fw\"></i></span></a>
            ";
            // line 70
            yield "            ";
            // line 71
            yield "            ";
            // line 72
            yield "            ";
            // line 73
            yield "            ";
            // line 74
            yield "            ";
            // line 75
            yield "            ";
            // line 76
            yield "            ";
            // line 77
            yield "            ";
            // line 78
            yield "            ";
            // line 79
            yield "            ";
            // line 80
            yield "            ";
            // line 81
            yield "            ";
            // line 82
            yield "          </li>
          <li id=\"nav-logout\" class=\"nav-item\"><a href=\"";
            // line 83
            yield ($context["logout"] ?? null);
            yield "\" class=\"nav-link\"><i class=\"fa-solid fa-sign-out\"></i> <span class=\"d-none d-md-inline\">";
            yield ($context["text_logout"] ?? null);
            yield "</span></a></li>
        </ul>
      ";
        }
        // line 86
        yield "    </div>
  </header>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Get current URL (without query parameters)
  const currentURL = window.location.href.split('&')[0];

  // Remove 'active' from all
  document.querySelectorAll('#top-nav .nav-link, #top-nav .dropdown-item').forEach(el => {
    el.classList.remove('active');
  });

  // Find and highlight the matching dropdown item
  let activeItem = null;
  document.querySelectorAll('#top-nav .dropdown-item').forEach(item => {
    const href = item.getAttribute('href');
    if (href && currentURL.includes(href.split('&')[0])) {
      activeItem = item;
      item.classList.add('active'); // highlight the exact page link
    }
  });

  // Highlight the parent dropdown button (if submenu clicked)
  if (activeItem) {
    let parentDropdown = activeItem.closest('.dropdown');
    if (parentDropdown) {
      let parentLink = parentDropdown.querySelector('.nav-link');
      if (parentLink) {
        parentLink.classList.add('active');
      }
    }

    // If it's a sub-dropdown, also highlight the higher parent
    let subParent = activeItem.closest('.dropdown-submenu');
    if (subParent) {
      let subParentLink = subParent.querySelector('.dropdown-toggle');
      if (subParentLink) {
        subParentLink.classList.add('active');
      }
      let higherParent = subParent.closest('.dropdown');
      if (higherParent) {
        let higherLink = higherParent.querySelector('.nav-link');
        if (higherLink) {
          higherLink.classList.add('active');
        }
      }
    }
  }

  // Also handle top-level nav items
  document.querySelectorAll('#top-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && currentURL.includes(href.split('&')[0])) {
      link.classList.add('active');
    }
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const MOBILE_W = 992;

  function isMobileWidth() {
    return window.innerWidth <= MOBILE_W;
  }

  // Prevent bootstrap native toggles interfering on mobile
  function setupMobileToggles() {
    // Parent dropdown toggles and submenu toggles
    const toggles = document.querySelectorAll('#top-nav .nav-item.dropdown > .nav-link, #top-nav .dropdown-submenu > a');

    toggles.forEach(toggle => {
      // Remove any existing handler we might have added earlier
      toggle.onclick = null;

      toggle.addEventListener('click', function (e) {
        if (!isMobileWidth()) return; // keep desktop default behavior

        e.preventDefault();
        e.stopPropagation();

        // find closest dropdown or dropdown-submenu parent
        let parent = this.closest('.dropdown-submenu') || this.closest('.dropdown');

        if (!parent) return;

        // close siblings on same level
        const siblings = Array.from(parent.parentElement ? parent.parentElement.children : []);
        siblings.forEach(sib => {
          if (sib !== parent) {
            sib.classList.remove('open', 'show');
          }
        });

        // toggle this parent
        parent.classList.toggle('open');
        parent.classList.toggle('show');
      });
    });
  }

  // close all when tapping outside
  function setupDocumentClose() {
    document.addEventListener('click', function (e) {
      if (!isMobileWidth()) return;
      if (!e.target.closest('#top-nav')) {
        document.querySelectorAll('#top-nav .dropdown.open, #top-nav .dropdown.show, #top-nav .dropdown-submenu.open, #top-nav .dropdown-submenu.show').forEach(el => {
          el.classList.remove('open', 'show');
        });
      }
    });
  }

  // reset on resize
  let rtimer;
  window.addEventListener('resize', function () {
    clearTimeout(rtimer);
    rtimer = setTimeout(function () {
      // remove mobile-only classes on desktop
      if (!isMobileWidth()) {
        document.querySelectorAll('#top-nav .dropdown.open, #top-nav .dropdown.show, #top-nav .dropdown-submenu.open, #top-nav .dropdown-submenu.show').forEach(el => {
          el.classList.remove('open', 'show');
        });
      }
      // re-setup toggles (in case DOM changed)
      setupMobileToggles();
    }, 120);
  });

  // initial setup
  setupMobileToggles();
  setupDocumentClose();
});
</script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/view/template/common/header.twig";
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
        return array (  287 => 86,  279 => 83,  276 => 82,  274 => 81,  272 => 80,  270 => 79,  268 => 78,  266 => 77,  264 => 76,  262 => 75,  260 => 74,  258 => 73,  256 => 72,  254 => 71,  252 => 70,  236 => 68,  231 => 66,  222 => 60,  217 => 57,  211 => 55,  203 => 53,  192 => 51,  187 => 50,  185 => 49,  176 => 47,  171 => 44,  169 => 43,  156 => 37,  147 => 30,  138 => 28,  133 => 27,  122 => 25,  117 => 24,  104 => 22,  100 => 21,  95 => 19,  91 => 18,  87 => 17,  83 => 16,  78 => 13,  72 => 11,  69 => 10,  63 => 8,  61 => 7,  57 => 6,  53 => 5,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html dir=\"{{ direction }}\" lang=\"{{ lang }}\">
<head>
  <meta charset=\"UTF-8\"/>
  <title>{{ title }}</title>
  <base href=\"{{ base }}\"/>
  {% if description %}
    <meta name=\"description\" content=\"{{ description }}\"/>
  {% endif %}
  {% if keywords %}
    <meta name=\"keywords\" content=\"{{ keywords }}\"/>
  {% endif %}
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\"/>
  <meta http-equiv=\"cache-control\" content=\"no-cache\">
  <meta http-equiv=\"expires\" content=\"0\">
  <link href=\"{{ bootstrap }}\" rel=\"stylesheet\" media=\"screen\"/>
  <link href=\"{{ icons }}\" rel=\"stylesheet\" type=\"text/css\"/>
  <link href=\"{{ stylesheet }}\" rel=\"stylesheet\" type=\"text/css\"/>
  <script src=\"{{ jquery }}\" type=\"text/javascript\"></script>
  <script type=\"text/javascript\" src=\"view/javascript/common.js\"></script>
  {% for style in styles %}
    <link type=\"text/css\" href=\"{{ style.href }}\" rel=\"{{ style.rel }}\" media=\"{{ style.media }}\"/>
  {% endfor %}
  {% for link in links %}
    <link href=\"{{ link.href }}\" rel=\"{{ link.rel }}\"/>
  {% endfor %}
  {% for script in scripts %}
    <script type=\"text/javascript\" src=\"{{ script.href }}\"></script>
  {% endfor %}
</head>
<body>
    <div id=\"sidebar-overlay\"></div>
<div id=\"alert\"></div>
<div id=\"container\">
  <header id=\"header\" class=\"navbar navbar-expand navbar-light bg-light\">
    <div class=\"container-fluid\">
      <a href=\"{{ home }}\" class=\"navbar-brand d-none d-lg-block\"><img src=\"view/image/logo.png\" alt=\"{{ heading_title }}\" title=\"{{ heading_title }}\"/>
      </a>
            <h3 style=\"
    font-size:33px; margin:0; font-weight:bold; color:#C9A24D; font-family:initial;text-align:left;\">
SALEEM GOLD COVERING</h3>

      {% if logged %}
        <button type=\"button\" id=\"button-menu\" class=\"btn btn-link d-inline-block d-lg-none\"><i class=\"fa-solid fa-bars\"></i></button>
        <ul class=\"nav navbar-nav\">
          <li id=\"nav-notification\" class=\"nav-item dropdown\">
            <a href=\"#\" data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle\"><i class=\"fa-regular fa-bell\"></i>{% if notification_total %} <span class=\"badge bg-danger\">{{ notification_total }}</span>{% endif %}</a>
            <div class=\"dropdown-menu dropdown-menu-end\">
              {% if notifications %}
                {% for notification in notifications %}
                  <a href=\"{{ notification.href }}\" data-bs-toggle=\"modal\" class=\"dropdown-item\">{{ notification.title }}</a>
                {% endfor %}
                <a href=\"{{ notification_all }}\" class=\"dropdown-item text-center text-primary\">{{ text_notification_all }}</a>
              {% else %}
                <span class=\"dropdown-item text-center\">{{ text_no_results }}</span>
              {% endif %}
            </div>
          </li>
          <li class=\"nav-item\">
  <a href=\"{{ setting_store }}\" class=\"nav-link\" title=\"Settings\"style=\"font-size:23px;margin-top:6px;\">
    <i class=\"fa-solid fa-gear\"></i>
  </a>
</li>


          <li id=\"nav-language\" class=\"nav-item dropdown\">{{ language }}</li>
          <li id=\"nav-profile\" class=\"nav-item dropdown\">
            <a href=\"#\" data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle\"><img src=\"{{ logoimage }}\" alt=\"{{ firstname }} {{ lastname }}\" title=\"{{ firstname }} {{ lastname }}\" class=\"rounded-circle\"/><span class=\"d-none d-md-inline d-lg-inline\">&nbsp;&nbsp;&nbsp;{{ firstname }} {{ lastname }} <i class=\"fa-solid fa-caret-down fa-fw\"></i></span></a>
            {#<ul class=\"dropdown-menu dropdown-menu-end\">#}
            {#  <li><a href=\"{{ profile }}\" class=\"dropdown-item\"><i class=\"fa-solid fa-user-circle fa-fw\"></i> {{ text_profile }}</a></li>#}
            {#  <li><hr class=\"dropdown-divider\"></li>#}
            {#  <li><h6 class=\"dropdown-header\">{{ text_store }}</h6></li>#}
            {#  {% for store in stores %}#}
            {#    <a href=\"{{ store.href }}\" target=\"_blank\" class=\"dropdown-item\">{{ store.name }}</a>#}
            {#  {% endfor %}#}
            {#  <li><hr class=\"dropdown-divider\"></li>#}
            {#  <li><h6 class=\"dropdown-header\">{{ text_help }}</h6></li>#}
            {#  <li><a href=\"https://www.opencart.com\" target=\"_blank\" class=\"dropdown-item\"><i class=\"fa-brands fa-opencart fa-fw\"></i> {{ text_homepage }}</a></li>#}
            {#  <li><a href=\"https://docs.opencart.com\" target=\"_blank\" class=\"dropdown-item\"><i class=\"fa-solid fa-file fa-fw\"></i> {{ text_documentation }}</a></li>#}
            {#  <li><a href=\"https://forum.opencart.com\" target=\"_blank\" class=\"dropdown-item\"><i class=\"fa-solid fa-comments fa-fw\"></i> {{ text_support }}</a></li>#}
            {#</ul>#}
          </li>
          <li id=\"nav-logout\" class=\"nav-item\"><a href=\"{{ logout }}\" class=\"nav-link\"><i class=\"fa-solid fa-sign-out\"></i> <span class=\"d-none d-md-inline\">{{ text_logout }}</span></a></li>
        </ul>
      {% endif %}
    </div>
  </header>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Get current URL (without query parameters)
  const currentURL = window.location.href.split('&')[0];

  // Remove 'active' from all
  document.querySelectorAll('#top-nav .nav-link, #top-nav .dropdown-item').forEach(el => {
    el.classList.remove('active');
  });

  // Find and highlight the matching dropdown item
  let activeItem = null;
  document.querySelectorAll('#top-nav .dropdown-item').forEach(item => {
    const href = item.getAttribute('href');
    if (href && currentURL.includes(href.split('&')[0])) {
      activeItem = item;
      item.classList.add('active'); // highlight the exact page link
    }
  });

  // Highlight the parent dropdown button (if submenu clicked)
  if (activeItem) {
    let parentDropdown = activeItem.closest('.dropdown');
    if (parentDropdown) {
      let parentLink = parentDropdown.querySelector('.nav-link');
      if (parentLink) {
        parentLink.classList.add('active');
      }
    }

    // If it's a sub-dropdown, also highlight the higher parent
    let subParent = activeItem.closest('.dropdown-submenu');
    if (subParent) {
      let subParentLink = subParent.querySelector('.dropdown-toggle');
      if (subParentLink) {
        subParentLink.classList.add('active');
      }
      let higherParent = subParent.closest('.dropdown');
      if (higherParent) {
        let higherLink = higherParent.querySelector('.nav-link');
        if (higherLink) {
          higherLink.classList.add('active');
        }
      }
    }
  }

  // Also handle top-level nav items
  document.querySelectorAll('#top-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && currentURL.includes(href.split('&')[0])) {
      link.classList.add('active');
    }
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const MOBILE_W = 992;

  function isMobileWidth() {
    return window.innerWidth <= MOBILE_W;
  }

  // Prevent bootstrap native toggles interfering on mobile
  function setupMobileToggles() {
    // Parent dropdown toggles and submenu toggles
    const toggles = document.querySelectorAll('#top-nav .nav-item.dropdown > .nav-link, #top-nav .dropdown-submenu > a');

    toggles.forEach(toggle => {
      // Remove any existing handler we might have added earlier
      toggle.onclick = null;

      toggle.addEventListener('click', function (e) {
        if (!isMobileWidth()) return; // keep desktop default behavior

        e.preventDefault();
        e.stopPropagation();

        // find closest dropdown or dropdown-submenu parent
        let parent = this.closest('.dropdown-submenu') || this.closest('.dropdown');

        if (!parent) return;

        // close siblings on same level
        const siblings = Array.from(parent.parentElement ? parent.parentElement.children : []);
        siblings.forEach(sib => {
          if (sib !== parent) {
            sib.classList.remove('open', 'show');
          }
        });

        // toggle this parent
        parent.classList.toggle('open');
        parent.classList.toggle('show');
      });
    });
  }

  // close all when tapping outside
  function setupDocumentClose() {
    document.addEventListener('click', function (e) {
      if (!isMobileWidth()) return;
      if (!e.target.closest('#top-nav')) {
        document.querySelectorAll('#top-nav .dropdown.open, #top-nav .dropdown.show, #top-nav .dropdown-submenu.open, #top-nav .dropdown-submenu.show').forEach(el => {
          el.classList.remove('open', 'show');
        });
      }
    });
  }

  // reset on resize
  let rtimer;
  window.addEventListener('resize', function () {
    clearTimeout(rtimer);
    rtimer = setTimeout(function () {
      // remove mobile-only classes on desktop
      if (!isMobileWidth()) {
        document.querySelectorAll('#top-nav .dropdown.open, #top-nav .dropdown.show, #top-nav .dropdown-submenu.open, #top-nav .dropdown-submenu.show').forEach(el => {
          el.classList.remove('open', 'show');
        });
      }
      // re-setup toggles (in case DOM changed)
      setupMobileToggles();
    }, 120);
  });

  // initial setup
  setupMobileToggles();
  setupDocumentClose();
});
</script>
", "admin/view/template/common/header.twig", "/home/k5ahkheh1fv2/public_html/JEWELLERY/admin/view/template/common/header.twig");
    }
}

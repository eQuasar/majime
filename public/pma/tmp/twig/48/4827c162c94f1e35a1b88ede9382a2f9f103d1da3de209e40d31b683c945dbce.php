<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* table/maintenance/checksum.twig */
class __TwigTemplate_c571eb26f2f0ff5a47fb46e23df6306992bb79fff38067583c7b0bf1e6c9529c extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div class=\"container-fluid\">
  <h2>
    ";
        // line 3
        echo _gettext("Checksum table");
        // line 4
        echo "    ";
        echo \PhpMyAdmin\Html\MySQLDocumentation::show("CHECKSUM_TABLE");
        echo "
  </h2>

  ";
        // line 7
        echo ($context["message"] ?? null);
        echo "

  <table class=\"pma-table my-3\">
    <thead>
      <tr>
        <th>";
        // line 12
        echo _gettext("Table");
        echo "</th>
        <th>";
        // line 13
        echo _gettext("Checksum");
        echo "</th>
      </tr>
    </thead>
    <tbody>
      ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["rows"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 18
            echo "        <tr>
          <td>";
            // line 19
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "Table", [], "any", false, false, false, 19), "html", null, true);
            echo "</td>
          <td class=\"text-right\">
            ";
            // line 21
            if ( !(null === twig_get_attribute($this->env, $this->source, $context["row"], "Checksum", [], "any", false, false, false, 21))) {
                // line 22
                echo "              ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "Checksum", [], "any", false, false, false, 22), "html", null, true);
                echo "
            ";
            } else {
                // line 24
                echo "              <em class=\"text-muted\">NULL</em>
            ";
            }
            // line 26
            echo "          </td>
        </tr>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "    </tbody>
  </table>

  ";
        // line 32
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["warnings"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["warning"]) {
            // line 33
            echo "    ";
            ob_start(function () { return ''; });
            // line 34
            echo "      ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["warning"], "Level", [], "any", false, false, false, 34), "html", null, true);
            echo ": #";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["warning"], "Code", [], "any", false, false, false, 34), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["warning"], "Message", [], "any", false, false, false, 34), "html", null, true);
            echo "
    ";
            $___internal_0cde42e54267e4e0093b9d32f51b1ff090c4e013bf0f0fa9d9c595b5407c5c08_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 33
            echo call_user_func_array($this->env->getFilter('notice')->getCallable(), [$___internal_0cde42e54267e4e0093b9d32f51b1ff090c4e013bf0f0fa9d9c595b5407c5c08_]);
            // line 36
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['warning'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "table/maintenance/checksum.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  131 => 37,  125 => 36,  123 => 33,  113 => 34,  110 => 33,  106 => 32,  101 => 29,  93 => 26,  89 => 24,  83 => 22,  81 => 21,  76 => 19,  73 => 18,  69 => 17,  62 => 13,  58 => 12,  50 => 7,  43 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/maintenance/checksum.twig", "/var/www/html/public/pma/templates/table/maintenance/checksum.twig");
    }
}

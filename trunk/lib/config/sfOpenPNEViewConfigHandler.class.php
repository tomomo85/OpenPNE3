<?php

/**
 * sfOpenPNEViewConfigHandler allows you to configure views for OpenPNE.
 *
 * @package    OpenPNE
 * @subpackage config
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class sfOpenPNEViewConfigHandler extends sfViewConfigHandler
{
  public function execute($configFiles)
  {
    $result = parent::execute($configFiles);
    $data = array();

    $first = true;
    foreach ($this->yamlConfig as $viewName => $values)
    {
      if ($viewName == 'all')
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$templateName.\$this->viewName == '$viewName')\n".
                "{\n";

      $data[] = $this->addCustomizes($viewName);

      $data[] = "}\n";

      $first = false;
    }

    $data[] = ($first ? '' : "else\n{")."\n";

    $data[] = $this->addCustomizes();

    $data[] = ($first ? '' : "}")."\n";

    $result .= sprintf("// auto-generated by sfOpenPNEViewConfigHandler\n".
                      "// date: %s\n%s\n",
                      date('Y/m/d H:i:s'), implode('', $data));

    return $result;
  }

  protected function addCustomizes($viewName = '')
  {
    $data = '';

    $customizes = $this->mergeConfigValue('customize', $viewName);
    foreach ($customizes as $name => $customize)
    {
      if (!is_array($customize) || empty($customize))
      {
        continue;
      }

      $template = array(null, $name);
      if (!empty($customize['template']) && is_array($customize['template']))
      {
        $template = $customize['template'];
      }

      $category = 'array(';
      if (!empty($customize['category']) && is_array($customize['category']))
      {
        foreach ($customize['category'] as $value) {
          $category .= '\'' . $value . '\',';
        }
      }
      $category .= ')';

      $parts = 'array(';
      if (!empty($customize['parts']) && is_array($customize['parts']))
      {
        foreach ($customize['parts'] as $value) {
          $parts .= '\'' . $value . '\',';
        }
      }
      $parts .= ')';

      $target = 'array(';
      if (!empty($customize['target']) && is_array($customize['target']))
      {
        foreach ($customize['target'] as $value) {
          $target .= '\'' . $value . '\',';
        }
      }
      $target .= ')';

      $isComponent = 'false';
      if (!empty($customize['is_component']))
      {
        $isComponent = 'true';
      }

      $data .= "  \$this->setCustomize('$name', '{$template[0]}', '{$template[1]}', $category, $parts, $target, $isComponent);\n";
      $data .= "  if (sfConfig::get('sf_logging_enabled')) \$this->context->getEventDispatcher()->notify(new sfEvent(\$this, 'application.log', array(sprintf('Set customize \"%s\" (%s/%s)', '$name', '{$template[0]}', '{$template[1]}'))));\n";
    }

    return $data;
  }
}

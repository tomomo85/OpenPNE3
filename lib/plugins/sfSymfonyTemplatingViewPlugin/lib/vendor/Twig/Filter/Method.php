<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a method template filter.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class Twig_Filter_Method extends Twig_Filter
{
  protected $extension, $method;

  public function __construct(Twig_ExtensionInterface $extension, $method, array $options = array())
  {
    parent::__construct($options);

    $this->extension = $extension;
    $this->method = $method;
  }

  public function compile()
  {
    return sprintf('$this->getEnvironment()->getExtension(\'%s\')->%s', $this->extension->getName(), $this->method);
  }
}
